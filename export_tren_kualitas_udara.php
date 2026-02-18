<?php
include "koneksi.php";
require_once __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$kabupaten  = $_GET['kabupaten'] ?? '';
$peruntukan = $_GET['peruntukan'] ?? '';
$type       = $_GET['type'] ?? 'pdf';

// Bangun informasi filter
$filterInfo = "";
if (!empty($kabupaten) || !empty($peruntukan)) {
    $filterParts = [];
    if (!empty($kabupaten)) {
        $filterParts[] = "Kabupaten/Kota: " . htmlspecialchars($kabupaten);
    }
    if (!empty($peruntukan)) {
        $filterParts[] = "Peruntukan: " . htmlspecialchars($peruntukan);
    }
    $filterInfo = implode(" | ", $filterParts);
}

$filter = "WHERE 1=1 ";
if (!empty($kabupaten))  $filter .= "AND l.kabupaten_kota = '$kabupaten' ";
if (!empty($peruntukan)) $filter .= "AND l.peruntukan = '$peruntukan' ";

$sql = "
    SELECT 
        p.tanggal_pemantauan,
        l.kode_lokasi,
        l.nama_lokasi,
        l.alamat_lokasi,
        l.kabupaten_kota,
        l.peruntukan,
        h.no2,
        h.so2,
        h.pm25
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    $filter
    ORDER BY 
        l.kabupaten_kota ASC,
        l.alamat_lokasi ASC,
        p.tanggal_pemantauan ASC
";

$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Tidak ada data untuk diexport.");
}


$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$judul = "Laporan Tren Kualitas Udara";
$tanggalCetak = date("d M Y");


$logoPath = __DIR__ . '/img/logo_kalsel.png';
$logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : '';



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
        th { background-color: #f2f2f2; text-align: center; padding: 6px; }
        td { text-align: center; padding: 5px; }
        .kop td { border: none; }
        .filter-info { font-size: 11px; color: #666; margin-bottom: 10px; text-align: center; }
    </style>
    </head>
    <body>

        <table width="100%" class="kop" cellpadding="4">
            <tr>
                <td width="15%">
                    <img src="' . $logoBase64 . '" width="65"> 
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

        <hr><br>

        <h2 style="text-align:center;">' . $judul . '</h2>';

    // Tampilkan informasi filter jika ada
    if (!empty($filterInfo)) {
        $html .= '
        <div class="filter-info" style="text-align:center;">
            ' . $filterInfo . '
        </div>';
    }

    $html .= '
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode Lokasi</th>
                    <th>Nama Lokasi</th>
                    <th>Alamat Lokasi</th>
                    <th>Kab / Kota</th>
                    <th>Peruntukan</th>
                    <th>NO2<br>(µg/m³)</th>
                    <th>SO2<br>(µg/m³)</th>
                    <th>PM2.5<br>(µg/m³)</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $d) {
        $html .= "
            <tr>
                <td>{$no}</td>
                <td>" . date('d-m-Y', strtotime($d['tanggal_pemantauan'])) . "</td>
                <td>{$d['kode_lokasi']}</td>
                <td>{$d['nama_lokasi']}</td>
                <td>{$d['alamat_lokasi']}</td>
                <td>{$d['kabupaten_kota']}</td>
                <td>{$d['peruntukan']}</td>
                <td>{$d['no2']}</td>
                <td>{$d['so2']}</td>
                <td>{$d['pm25']}</td>
            </tr>";
        $no++;
    }

    $html .= '
            </tbody>
        </table>

        <br><br>
        <p style="text-align:right; font-size:11px; font-style:italic;">
            Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel<br>
            Pada: ' . $tanggalCetak . '
        </p>

    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // Generate filename berdasarkan filter
    $filename = "Laporan_Tren_Kualitas_Udara";
    if (!empty($kabupaten)) {
        $filename .= "_" . str_replace(' ', '_', $kabupaten);
    }
    if (!empty($peruntukan)) {
        $filename .= "_" . str_replace(' ', '_', $peruntukan);
    }
    $filename .= ".pdf";
    
    $dompdf->stream($filename, ["Attachment" => true]);
    exit;
}




// EXCEL

if ($type == 'excel') {

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Tren Udara');

    // LOGO
    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kalsel');
        $drawing->setPath($logoPath);
        $drawing->setHeight(80);
        $drawing->setCoordinates('A2');
        $drawing->setWorksheet($sheet);
    }

    // KOP
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
    $sheet->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B4:J4');
    $sheet->setCellValue('B4', "Jl. Bangun Praja Banjarbaru Kode Pos 70732");
    $sheet->getStyle('B4')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B5:J5');
    $sheet->setCellValue('B5', "Telp/Fax: (0815)-6749241 | Email: blhdkalsel@gmail.com");
    $sheet->getStyle('B5')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
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
        $sheet->setCellValue('A' . $row, $filterInfo);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Hapus border untuk baris filter
        $sheet->getStyle('A' . $row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
        
        $row++;
    }
    $row++;

    // HEADER TABEL
    $headers = ['No','Tanggal','Kode Lokasi','Nama Lokasi','Alamat','Kab/Kota','Peruntukan','NO₂(µg/m³)','SO₂(µg/m³)','PM₂.₅(µg/m³)'];
    $sheet->fromArray($headers, NULL, 'A'.$row);
    $sheet->getStyle('A'.$row.':J'.$row)->getFont()->setBold(true);
    $sheet->getStyle('A'.$row.':J'.$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
    $sheet->getStyle('A'.$row.':J'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
    $no = 1;

    foreach ($data as $d) {
        $sheet->fromArray([
            $no,
            date('d-m-Y', strtotime($d['tanggal_pemantauan'])),
            $d['kode_lokasi'],
            $d['nama_lokasi'],
            $d['alamat_lokasi'],
            $d['kabupaten_kota'],
            $d['peruntukan'],
            $d['no2'],
            $d['so2'],
            $d['pm25']
        ], NULL, 'A'.$row);
        $row++;
        $no++;
    }

    // Border untuk data - HANYA untuk tabel (dimulai dari header tabel)
    $headerTableRow = $row - $no; // Row header tabel
    $lastRow = $row - 1;
    $sheet->getStyle('A' . $headerTableRow . ':J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    foreach (range('A','J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // FOOTER
    $footerRow = $row + 2;
    $sheet->mergeCells('A' . $footerRow . ':J' . $footerRow);
    $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $tanggalCetak);
    $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(10);
    
    // Hapus border untuk footer
    $sheet->getStyle('A' . $footerRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);

    // Generate filename berdasarkan filter
    $filename = "Laporan_Tren_Kualitas_Udara";
    if (!empty($kabupaten)) {
        $filename .= "_" . str_replace(' ', '_', $kabupaten);
    }
    if (!empty($peruntukan)) {
        $filename .= "_" . str_replace(' ', '_', $peruntukan);
    }
    $filename .= ".xlsx";

    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
}


// WORD
if ($type == 'word') {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=Laporan_Tren_Kualitas_Udara_" . date('YmdHis') . ".doc");

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
            background-color: #f2f2f2; 
            color: black; 
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
                if (!empty($filterInfo)) {
                    echo '
                    <div class="filter-info" style="text-align:center;">
                    <br>
                        ' . $filterInfo . '
                    </div>';
                }

                echo '
            <table>
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="10%">Kode Lokasi</th>
                        <th width="12%">Nama Lokasi</th>
                        <th width="18%">Alamat Lokasi</th>
                        <th width="10%">Kab/Kota</th>
                        <th width="10%">Peruntukan</th>
                        <th width="10%">NO₂<br>(µg/m³)</th>
                        <th width="10%">SO₂<br>(µg/m³)</th>
                        <th width="10%">PM₂.₅<br>(µg/m³)</th>
                    </tr>
                </thead>
                <tbody>
                ';

                    $no = 1;
                    foreach ($data as $d) {
                        echo "
                        <tr>
                        <td>$no</td>
                        <td>".date('d-m-Y', strtotime($d['tanggal_pemantauan']))."</td>
                        <td>{$d['kode_lokasi']}</td>
                        <td>{$d['nama_lokasi']}</td>
                        <td>{$d['alamat_lokasi']}</td>
                        <td>{$d['kabupaten_kota']}</td>
                        <td>{$d['peruntukan']}</td>
                        <td>{$d['no2']}</td>
                        <td>{$d['so2']}</td>
                        <td>{$d['pm25']}</td>
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