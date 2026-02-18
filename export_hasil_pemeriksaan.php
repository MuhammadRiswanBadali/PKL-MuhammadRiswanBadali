<?php
include "koneksi.php";
require_once __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$periode_val = $_GET['periode'] ?? '';
$kabupaten   = $_GET['kabupaten'] ?? '';
$type        = $_GET['type'] ?? 'pdf';

$bulan = '';
$tahun = '';
if (!empty($periode_val)) {
    list($bulan, $tahun) = explode("-", $periode_val);
}

$filter = "WHERE 1=1 ";
if (!empty($periode_val)) {
    $filter .= "AND MONTH(p.tanggal_pemantauan) = '$bulan' ";
    $filter .= "AND YEAR(p.tanggal_pemantauan) = '$tahun' ";
}
if (!empty($kabupaten)) {
    $filter .= "AND l.kabupaten_kota = '$kabupaten' ";
}

$sql = "
    SELECT
        p.id_pemantauan,
        p.tanggal_pemantauan,
        p.durasi_pemantauan,
        p.metode_pemantauan,
        p.shu,
        l.kode_lokasi,
        l.alamat_lokasi,
        l.kabupaten_kota,
        l.latitude,
        l.longitude,
        h.no2, h.so2, h.pm25
    FROM pemantauan_udara p
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    JOIN hasil_pemantauan h ON p.id_pemantauan = h.id_pemantauan
    $filter
    ORDER BY p.tanggal_pemantauan ASC
";

$query = mysqli_query($koneksi, $sql);

if (!$query || mysqli_num_rows($query) == 0) {
    die("Tidak ada data untuk diexport dengan filter yang dipilih.");
}

$judul = "Laporan Hasil Pemeriksaan Kualitas Udara";
$tanggalCetak = date("d M Y");

$bulanNama = [
    "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
    "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
    "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
];

$filterInfo = "";
if (!empty($periode_val) || !empty($kabupaten)) {
    $filterParts = [];
    if (!empty($periode_val)) {
        $filterParts[] = "Periode: " . $bulanNama[$bulan] . " " . $tahun;
    }
    if (!empty($kabupaten)) {
        $filterParts[] = "Kabupaten/Kota: " . htmlspecialchars($kabupaten);
    }
    $filterInfo = implode(" | ", $filterParts);
}

$logoPath = __DIR__ . '/img/logo_kalsel.png';
$logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : '';

$no = 1;
$dataList = [];

while ($data = mysqli_fetch_assoc($query)) {
    $dataList[] = $data;
    $no++;
}
$no = 1;

//  EXPORT PDF
if ($type == "pdf") {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new Dompdf($options);
    
    $html = '
    <html>
    <head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; text-align:center; padding:6px; }
        td { text-align:center; padding:6px; }
        .kop td { border: none; }
        .filter-info { font-size: 11px; color: #666; margin-bottom: 10px; text-align: center; }
    </style>
    </head>
    <body>
    
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
    <hr><br>
    
    <h2 style="text-align:center;">' . $judul . '</h2>';
    
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
                <th>Alamat</th>
                <th>Kabupaten/Kota</th>
                <th>Durasi</th>
                <th>Metode</th>
                <th>SHU</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>NO2<br>(µg/m³)</th>
                <th>SO2<br>(µg/m³)</th>
                <th>PM2.5<br>(µg/m³)</th>
            </tr>
        </thead>
        <tbody>';
  
    foreach ($dataList as $data) {
        $html .= '
        <tr>
            <td>' . $no . '</td>
            <td>' . $data['tanggal_pemantauan'] . '</td>
            <td>' . $data['kode_lokasi'] . '</td>
            <td>' . $data['alamat_lokasi'] . '</td>
            <td>' . $data['kabupaten_kota'] . '</td>
            <td>' . $data['durasi_pemantauan'] . '</td>
            <td>' . $data['metode_pemantauan'] . '</td>
            <td>' . $data['shu'] . '</td>
            <td>' . $data['latitude'] . '</td>
            <td>' . $data['longitude'] . '</td>
            <td>' . $data['no2'] . '</td>
            <td>' . $data['so2'] . '</td>
            <td>' . $data['pm25'] . '</td>
        </tr>';
        $no++;
    }
    
    $html .= '
        </tbody>
    </table>
    
    <br><br>
    <p style="text-align:right; font-size:11px; font-style:italic;">
        Dicetak pada: ' . $tanggalCetak . '
    </p>
    
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    $filename = "Laporan_Hasil_Pemeriksaan_Udara";
    if (!empty($periode_val)) {
        $filename .= "_" . $bulanNama[$bulan] . "_" . $tahun;
    }
    if (!empty($kabupaten)) {
        $filename .= "_" . str_replace(' ', '_', $kabupaten);
    }
    $filename .= ".pdf";

    $dompdf->stream($filename, ["Attachment" => true]);
    exit;
}

//  EXPORT EXCEL
if ($type == "excel") {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Hasil Pemantauan Udara');

    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kalsel');
        $drawing->setPath($logoPath);
        $drawing->setHeight(80);
        $drawing->setCoordinates('A2');
        $drawing->setWorksheet($sheet);
    }

    $sheet->mergeCells('B1:M1');
    $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B2:M2');
    $sheet->setCellValue('B2', 'DINAS LINGKUNGAN HIDUP');
    $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B3:M3');
    $sheet->setCellValue('B3', "Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan");
    $sheet->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B4:M4');
    $sheet->setCellValue('B4', "Jl. Bangun Praja Banjarbaru Kode Pos 70732");
    $sheet->getStyle('B4')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B5:M5');
    $sheet->setCellValue('B5', "Telp/Fax: (0815)-6749241 | Email: blhdkalsel@gmail.com");
    $sheet->getStyle('B5')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // JUDUL LAPORAN
    $row = 7;
    $sheet->mergeCells('A' . $row . ':M' . $row);
    $sheet->setCellValue('A' . $row, $judul);
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $row++;

    // INFORMASI FILTER
    if (!empty($filterInfo)) {
        $sheet->mergeCells('A' . $row . ':M' . $row);
        $sheet->setCellValue('A' . $row, $filterInfo);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
    }
    $row++;

    // HEADER TABEL
    $headers = [
        'No', 
        'Tanggal',
        'Kode Lokasi', 
        'Alamat', 
        'Kabupaten/Kota', 
        'Durasi', 
        'Metode', 
        'SHU', 
        'Latitude',
        'Longitude',
        'NO₂(µg/m³)', 
        'SO₂(µg/m³)', 
        'PM₂.₅(µg/m³)'
    ];
    
    $sheet->fromArray($headers, NULL, 'A' . $row);
    $sheet->getStyle('A' . $row . ':M' . $row)->getFont()->setBold(true);
    $sheet->getStyle('A' . $row . ':M' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
    $sheet->getStyle('A' . $row . ':M' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
    $no = 1;

    // DATA
    foreach ($dataList as $d) {
        $sheet->fromArray([
            $no,
            $d['tanggal_pemantauan'],
            $d['kode_lokasi'],
            $d['alamat_lokasi'],
            $d['kabupaten_kota'],
            $d['durasi_pemantauan'],
            $d['metode_pemantauan'],
            $d['shu'],
            $d['latitude'],
            $d['longitude'],
            $d['no2'],
            $d['so2'],
            $d['pm25']
        ], NULL, 'A' . $row);

        $row++;
        $no++;
    }

    // Border untuk data
    $lastRow = $row - 1;
    $sheet->getStyle('A' . ($row - $no) . ':M' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    // Autosize columns
    foreach (range('A', 'M') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // FOOTER
    $footerRow = $row + 2;
    $sheet->mergeCells('A' . $footerRow . ':M' . $footerRow);
    $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $tanggalCetak);
    $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(10);

    // OUTPUT
    $writer = new Xlsx($spreadsheet);
    
    // Generate filename berdasarkan filter
    $filename = "Laporan_Hasil_Pemeriksaan_Udara";
    if (!empty($periode_val)) {
        $filename .= "_" . $bulanNama[$bulan] . "_" . $tahun;
    }
    if (!empty($kabupaten)) {
        $filename .= "_" . str_replace(' ', '_', $kabupaten);
    }
    $filename .= ".xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

//  EXPORT WORD
if ($type == "word") {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=Laporan_Hasil_Pemeriksaan_Udara_" . date('YmdHis') . ".doc");

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
                        <img src="' . $logoBase64 . '" 
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

            <h2 style="text-align:center; margin:10px 0; font-size: 14pt; ">' . $judul . '</h2>';

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
                        <th width="8%">Tanggal</th>
                        <th width="8%">Kode Lokasi</th>
                        <th width="15%">Alamat</th>
                        <th width="10%">Kabupaten/Kota</th>
                        <th width="6%">Durasi</th>
                        <th width="6%">Metode</th>
                        <th width="6%">SHU</th>
                        <th width="8%">Latitude</th>
                        <th width="8%">Longitude</th>
                        <th width="8%">NO₂<br>(µg/m³)</th>
                        <th width="8%">SO₂<br>(µg/m³)</th>
                        <th width="8%">PM₂.₅<br>(µg/m³)</th>
                    </tr>
                </thead>
                <tbody>
                ';

                    $no = 1;
                    foreach ($dataList as $data) {
                        echo "
                        <tr>
                        <td>$no</td>
                        <td>{$data['tanggal_pemantauan']}</td>
                        <td>{$data['kode_lokasi']}</td>
                        <td>{$data['alamat_lokasi']}</td>
                        <td>{$data['kabupaten_kota']}</td>
                        <td>{$data['durasi_pemantauan']}</td>
                        <td>{$data['metode_pemantauan']}</td>
                        <td>{$data['shu']}</td>
                        <td>{$data['latitude']}</td>
                        <td>{$data['longitude']}</td>
                        <td>{$data['no2']}</td>
                        <td>{$data['so2']}</td>
                        <td>{$data['pm25']}</td>
                        </tr>";
                        $no++;
                    }

                    echo '
                </tbody>
            </table>

                <div class="footer">
                Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel<br>
                Pada: ' . $tanggalCetak . '
                </div>

            </div>
        </body>
    </html>';

        exit;
}

?>