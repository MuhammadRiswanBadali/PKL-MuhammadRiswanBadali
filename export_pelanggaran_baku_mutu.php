<?php
include "koneksi.php";
require_once __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$periode    = $_GET['periode'] ?? '';
$peruntukan = $_GET['peruntukan'] ?? '';
$type       = $_GET['type'] ?? 'pdf';

date_default_timezone_set('Asia/Jakarta');

$filterInfo = "";
if (!empty($periode)) {
    list($bulan, $tahun) = explode('-', $periode);
    $bulanNama = [
        "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
        "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
        "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
    ];
    $filterInfo .= "<b>Periode:</b> " . $bulanNama[$bulan] . " " . $tahun;
}

if (!empty($peruntukan)) {
    if (!empty($filterInfo)) {
        $filterInfo .= " | ";
    }
    $filterInfo .= "<b>Peruntukan:</b> " . htmlspecialchars($peruntukan);
}

$where = "WHERE 1=1 ";

if (!empty($periode)) {
    list($bulan, $tahun) = explode('-', $periode);
    $where .= "AND MONTH(p.tanggal_pemantauan)='$bulan' 
               AND YEAR(p.tanggal_pemantauan)='$tahun' ";
}

if (!empty($peruntukan)) {
    $where .= "AND l.peruntukan='$peruntukan' ";
}

$sql = "
    SELECT 
        l.kode_lokasi,
        l.peruntukan,
        l.alamat_lokasi,
        l.kabupaten_kota,
        'NO2' AS parameter_display, -- Diganti dari 'NO₂' menjadi 'NO2'
        'NO2' AS parameter,
        h.no2 AS nilai,
        50 AS baku_mutu,
        (h.no2 - 50) AS kelebihan,
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.no2 > 50

    UNION ALL

    SELECT 
        l.kode_lokasi,
        l.peruntukan,
        l.alamat_lokasi,
        l.kabupaten_kota,
        'SO2', -- Diganti dari 'SO₂' menjadi 'SO2'
        'SO2',
        h.so2,
        60,
        (h.so2 - 60),
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.so2 > 60

    UNION ALL

    SELECT 
        l.kode_lokasi,
        l.peruntukan,
        l.alamat_lokasi,
        l.kabupaten_kota,
        'PM2.5', -- Diganti dari 'PM₂.₅' menjadi 'PM2.5'
        'PM2.5',
        h.pm25,
        15,
        (h.pm25 - 15),
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.pm25 > 15

    ORDER BY kabupaten_kota ASC, tanggal_pemantauan ASC
";

$result = mysqli_query($koneksi, $sql);
if (mysqli_num_rows($result) == 0) die("Tidak ada data.");

$data = [];
while ($row = mysqli_fetch_assoc($result)) $data[] = $row;

$judul = "Laporan Pelanggaran Baku Mutu Udara";
$tanggalCetak = date("d M Y");
$totalData = count($data);

// Tambahkan total data ke informasi filter
$filterInfoWithTotal = $filterInfo;
if (!empty($filterInfoWithTotal)) {
    $filterInfoWithTotal .= " ";
}

$logoPath = __DIR__ . '/img/logo_kalsel.png';
$logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : '';

// PDF EXPORT
if ($type == 'pdf') {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->setBasePath(__DIR__);

    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            table { border-collapse: collapse; width: 100%; margin-top: 10px; }
            table, th, td { border: 1px solid #555; }
            th { background-color: #dc3545; color: white; text-align: center; font-weight: bold; padding: 8px; }
            td { text-align: center; padding: 6px; }
            .kop td { border: none; padding: 2px 0; }
            .kelebihan { color: #dc3545; font-weight: bold; }
            .footer { text-align: right; font-size: 9px; font-style: italic; margin-top: 20px; }
            .filter-info { font-size: 11px; color: #666; margin-bottom: 10px; text-align: center; }
            .parameter { font-weight: bold; }
        </style>
    </head>
    <body>

        <!-- KOP SURAT -->
        <table width="100%" class="kop" cellpadding="4">
            <tr>
                <td width="15%">
                    <img src="' . $logoBase64 . '" width="70"> 
                </td>
                <td width="85%" align="center" style="font-family: Times New Roman;">
                    <div style="font-size:14pt;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div style="font-size:16pt; font-weight:bold;">DINAS LINGKUNGAN HIDUP</div>
                    <div style="font-size:11pt;">Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan</div>
                    <div style="font-size:11pt;">Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241</div>
                    <div style="font-size:11pt;">Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id</div>
                </td>
            </tr>
        </table>

        <hr style="height:2px; border:none; color:#333; background-color:#333;">

        <!-- JUDUL -->
        <h3 style="text-align:center; font-size:14pt; margin:10px 0;">' . $judul . '</h3>';

    // Tampilkan informasi filter jika ada
    if (!empty($filterInfoWithTotal)) {
        $html .= '
        <div class="filter-info" style="text-align:center;">
            <br>
            ' . str_replace(['<b>', '</b>'], ['<strong>', '</strong>'], $filterInfoWithTotal) . '
        </div>';
    }

    $html .= '
        <!-- TABEL DATA -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode Lokasi</th>
                    <th width="12%">Peruntukan</th>
                    <th width="18%">Alamat Lokasi</th>
                    <th width="12%">Kab/Kota</th>
                    <th width="8%">Parameter</th>
                    <th width="10%">Nilai (µg/m³)</th>
                    <th width="10%">Baku Mutu (µg/m³)</th>
                    <th width="10%">Kelebihan</th>
                    <th width="12%">Tanggal Pemantauan</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $d) {
        $html .= "
            <tr>
                <td>{$no}</td>
                <td>{$d['kode_lokasi']}</td>
                <td>{$d['peruntukan']}</td>
                <td align=\"left\">{$d['alamat_lokasi']}</td>
                <td>{$d['kabupaten_kota']}</td>
                <td class=\"parameter\">{$d['parameter_display']}</td>
                <td>{$d['nilai']}</td>
                <td>{$d['baku_mutu']}</td>
                <td class=\"kelebihan\">+" . number_format($d['kelebihan'], 2) . "</td>
                <td>" . date('d-m-Y', strtotime($d['tanggal_pemantauan'])) . "</td>
            </tr>";
        $no++;
    }

    $html .= '
            </tbody>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel<br>
            Pada: ' . $tanggalCetak . '
        </div>

    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("Laporan_Pelanggaran_Baku_Mutu.pdf", ["Attachment" => true]);
    exit;
}

// EXCEL EXPORT 
if ($type == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Pelanggaran Baku Mutu');

    // LOGO KOP SURAT - Menggunakan struktur sebelumnya
    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kalsel');
        $drawing->setPath($logoPath);
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    }

    // KOP SURAT - Menggunakan struktur sebelumnya
    $sheet->mergeCells('B1:J1');
    $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B2:J2');
    $sheet->setCellValue('B2', 'DINAS LINGKUNGAN HIDUP');
    $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B3:J3');
    $sheet->setCellValue('B3', "Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan");
    $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B4:J4');
    $sheet->setCellValue('B4', "Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241");
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B5:J5');
    $sheet->setCellValue('B5', "Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id");
    $sheet->getStyle('B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // JUDUL LAPORAN
    $row = 7;
    $sheet->mergeCells('A' . $row . ':J' . $row);
    $sheet->setCellValue('A' . $row, $judul);
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $row++;

    // INFORMASI FILTER
    if (!empty($filterInfo)) {
        $sheet->mergeCells('A' . $row . ':J' . $row);
        
        // Hapus tag HTML untuk Excel
        $excelFilterInfo = str_replace(['<b>', '</b>'], '', $filterInfo);
        
        $sheet->setCellValue('A' . $row, $excelFilterInfo);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A' . $row)->getFill()->getStartColor()->setARGB('F8F9FA');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getAlignment()->setWrapText(true);
        
        $row++;
    }
    $row++;

    // HEADER TABEL - Disesuaikan dengan struktur laporan pelanggaran
    $headers = [
        'No', 
        'Kode Lokasi', 
        'Peruntukan', 
        'Alamat Lokasi', 
        'Kab/Kota',
        'Parameter', 
        'Nilai (µg/m³)', 
        'Baku Mutu (µg/m³)', 
        'Kelebihan', 
        'Tanggal Pemantauan'
    ];
    
    $sheet->fromArray($headers, NULL, 'A' . $row);
    
    // STYLING HEADER - Menggunakan gaya dari referensi
    $headerStyle = $sheet->getStyle('A' . $row . ':J' . $row);
    $headerStyle->getFont()->setBold(true);
    $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('DC3545');
    $headerStyle->getFont()->getColor()->setARGB('FFFFFF');
    $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
    $no = 1;
    
    // DATA
    foreach ($data as $d) {
        $sheet->fromArray([
            $no,
            $d['kode_lokasi'],
            $d['peruntukan'],
            $d['alamat_lokasi'],
            $d['kabupaten_kota'],
            $d['parameter_display'],  // Menggunakan format NO2, SO2, PM2.5
            $d['nilai'],
            $d['baku_mutu'],
            $d['kelebihan'],
            date('d-m-Y', strtotime($d['tanggal_pemantauan']))
        ], NULL, 'A' . $row);
        
        // Warna merah untuk kolom kelebihan
        $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FF0000');
        
        $row++;
        $no++;
    }

    // Border untuk data - Menggunakan gaya dari referensi
    $lastRow = $row - 1;
    $startDataRow = $row - $no + 1;
    $sheet->getStyle('A' . $startDataRow . ':J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    // Autosize columns - Menggunakan gaya dari referensi
    foreach (range('A', 'J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // FOOTER - Menggunakan gaya dari referensi
    $footerRow = $row + 2;
    $sheet->mergeCells('A' . $footerRow . ':J' . $footerRow);
    $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel | Pada: ' . $tanggalCetak);
    $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(10);

    // Generate filename berdasarkan filter - Menggunakan gaya dari referensi
    $filename = "Laporan_Pelanggaran_Baku_Mutu";
    if (!empty($periode)) {
        list($bulan, $tahun) = explode('-', $periode);
        $bulanNama = [
            "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
            "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
            "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
        ];
        $filename .= "_" . $bulanNama[$bulan] . "_" . $tahun;
    }
    if (!empty($peruntukan)) {
        $filename .= "_" . str_replace(' ', '_', $peruntukan);
    }
    $filename .= ".xlsx";
    
    // OUTPUT
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
// WORD EXPORT 
if ($type == 'word') {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=Laporan_Pelanggaran_Baku_Mutu_" . date('YmdHis') . ".doc");

    echo '
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">
    <head>
    <meta charset="UTF-8">

    <!-- Pengaturan halaman landscape -->
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->

    <style>
        @page Section1 {
            size: 841.7pt 595.45pt; /* Landscape A4 */
            margin: 20mm;
            mso-page-orientation: landscape;
        }
        div.Section1 { page: Section1; }

        body { 
            font-family: Arial, sans-serif; 
            font-size: 10pt; 
            margin: 0;
            padding: 0;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 10px;
        }
        table, th, td { 
            border: 1px solid #555; 
        }
        th { 
            background-color: #dc3545; 
            color: white; 
            padding: 8px; 
            text-align: center;
            font-weight: bold;
        }
        td { 
            padding: 6px; 
            text-align: center;
            vertical-align: middle;
        }
        .kop td { 
            border: none; 
            padding: 2px 0;
        }
        .kelebihan { 
            color: #dc3545; 
            font-weight: bold; 
        }
        .footer { 
            text-align: right; 
            font-size: 9pt; 
            font-style: italic; 
            margin-top: 20px;
        }
        .filter-info { 
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }
        .parameter { font-weight: bold; }
    </style>

    </head>
        <body>
            <div class="Section1">

            <!-- KOP SURAT -->
            <table class="kop" width="100%" cellpadding="4">
                <tr>
                    <td width="15%" align="center" valign="middle">
                        <img src="'.$logoBase64.'" 
                            width="80" 
                            height="120"
                            style="max-width: 80px; max-height: 120px;"
                            alt="Logo DLH Kalsel">
                    </td>
                    <td width="85%" align="center" style="font-family: Times New Roman;">
                        <div style="font-size:14pt;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                        <div style="font-size:16pt; font-weight:bold;">DINAS LINGKUNGAN HIDUP</div>
                        <div style="font-size:11pt;">Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan</div>
                        <div style="font-size:11pt;">Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241</div>
                        <div style="font-size:11pt;">Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id</div>
                    </td>
                </tr>
            </table>

            <hr style="height:2px; border:none; color:#333; background-color:#333;">

            <h2 style="text-align:center; margin:10px 0; font-size: 14pt; ">'.$judul.'</h2>';

                // Tampilkan informasi filter jika ada
                if (!empty($filterInfoWithTotal)) {
                    echo '
                    <div class="filter-info" style="text-align:center;">
                    <br>
                        ' . $filterInfoWithTotal . '
                    </div>';
                }

                echo '
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Kode Lokasi</th>
                        <th width="12%">Peruntukan</th>
                        <th width="18%">Alamat Lokasi</th>
                        <th width="12%">Kab/Kota</th>
                        <th width="8%">Parameter</th>
                        <th width="10%">Nilai<br>(µg/m³)</th>
                        <th width="10%">Baku Mutu<br>(µg/m³)</th>
                        <th width="10%">Kelebihan</th>
                        <th width="12%">Tanggal<br>Pemantauan</th>
                    </tr>
                </thead>
                <tbody>
                ';

                    $no = 1;
                    foreach ($data as $d) {
                        echo "
                        <tr>
                        <td>$no</td>
                        <td>{$d['kode_lokasi']}</td>
                        <td>{$d['peruntukan']}</td>
                        <td align=\"left\">{$d['alamat_lokasi']}</td>
                        <td>{$d['kabupaten_kota']}</td>
                        <td class=\"parameter\">{$d['parameter_display']}</td>
                        <td>{$d['nilai']}</td>
                        <td>{$d['baku_mutu']}</td>
                        <td class=\"kelebihan\">+".number_format($d['kelebihan'],2)."</td>
                        <td>".date('d-m-Y',strtotime($d['tanggal_pemantauan']))."</td>
                        </tr>";
                        $no++;
                    }

                    echo '
                </tbody>
            </table>

                <div class="footer">
                Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel<br>
                Pada: '.$tanggalCetak.'
                </div>

            </div>
        </body>
    </html>';

        exit;
    }
?>