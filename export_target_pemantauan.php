<?php
include "koneksi.php";
require_once __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$tahun  = $_GET['tahun'] ?? '';
$type   = $_GET['type'] ?? 'pdf';

if (empty($tahun)) {
    die("Tahun pemantauan wajib diisi.");
}

$sql = "
    SELECT 
        l.kabupaten_kota,
        COUNT(h.id_hasil) AS total_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    WHERE YEAR(p.tanggal_pemantauan) = '$tahun'
    GROUP BY l.kabupaten_kota
    ORDER BY l.kabupaten_kota ASC
";

$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Tidak ada data pemantauan yang memiliki hasil pada tahun $tahun.");
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$judul = "Laporan Target Pemantauan Kualitas Udara Tahun $tahun";
$tanggalCetak = date("d M Y");

$logoPath = __DIR__ . '/img/logo_kalsel.png';
$logoBase64 = file_exists($logoPath)
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
    : '';

// EXPORT PDF
if ($type == 'pdf') {

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    $html = '
    <html>
    <head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; text-align:center; padding:6px; }
        td { text-align:center; padding:6px; }
        .kop td { border: none; }
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

    <h2 style="text-align:center;">' . $judul . '</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kabupaten / Kota</th>
                <th>Total Pemantauan</th>
                <th>Target</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

    $no = 1;
    foreach ($data as $d) {

        $total = $d['total_pemantauan'];

        if ($total >= 8) {
            $status = "TERCAPAI";
        } else {
            $status = "BELUM TERCAPAI";
        }

        $html .= "
        <tr>
            <td>{$no}</td>
            <td>{$d['kabupaten_kota']}</td>
            <td>{$total}</td>
            <td>8 Kali / Tahun</td>
            <td>{$status}</td>
        </tr>";
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
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Target_Pemantauan_$tahun.pdf", ["Attachment" => true]);
    exit;
}


// EXPORT EXCEL
if ($type == 'excel') {

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Target Pemantauan');

    // LOGO 
    $logoPath = __DIR__ . '/img/logo_kalsel.png';
    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kalsel');
        $drawing->setPath($logoPath);
        $drawing->setHeight(80);
        $drawing->setCoordinates('A2');
        $drawing->setWorksheet($sheet);
    }

    $sheet->mergeCells('B1:E1');
    $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B2:E2');
    $sheet->setCellValue('B2', 'DINAS LINGKUNGAN HIDUP');
    $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B3:E3');
    $sheet->setCellValue('B3', "Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan");
    $sheet->getStyle('B3')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B4:E4');
    $sheet->setCellValue('B4', "Jl. Bangun Praja Banjarbaru Kode Pos 70732");
    $sheet->getStyle('B4')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B5:E5');
    $sheet->setCellValue('B5', "Telp/Fax: (0815)-6749241 | Email: blhdkalsel@gmail.com");
    $sheet->getStyle('B5')->getAlignment()->setWrapText(true)->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // JUDUL LAPORAN
    $row = 7;
    $sheet->mergeCells('A' . $row . ':E' . $row);
    $sheet->setCellValue('A' . $row, $judul);
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $row++;
    $row++;

    // HEADER TABEL
    $headers = [
        'No', 
        'Kabupaten/Kota', 
        'Total Pemantauan', 
        'Target', 
        'Status'
    ];
    
    $sheet->fromArray($headers, NULL, 'A' . $row);
    $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
    $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');
    $sheet->getStyle('A' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
    $no = 1;

    // DATA
    foreach ($data as $d) {

        $total = $d['total_pemantauan'];

        if ($total >= 8) {
            $status = "TERCAPAI";
        } else {
            $status = "BELUM TERCAPAI";
        }

        $sheet->fromArray([
            $no,
            $d['kabupaten_kota'],
            $total,
            "8 Kali / Tahun",
            $status
        ], NULL, 'A' . $row);

        $row++;
        $no++;
    }

    // Border untuk data (termasuk header tabel di baris 9)
    $lastRow = $row - 1;
    $headerTableRow = $row - $no; // Baris header tabel (baris 9)
    $sheet->getStyle('A' . $headerTableRow . ':E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

    // Autosize columns
    foreach (range('A', 'E') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // FOOTER
    $footerRow = $row + 2;
    $sheet->mergeCells('A' . $footerRow . ':E' . $footerRow);
    $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $tanggalCetak);
    $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(10);

    // OUTPUT
    $writer = new Xlsx($spreadsheet);
    
    // Generate filename berdasarkan filter
    $filename = "Target_Pemantauan_$tahun.xlsx";
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

// EXPORT WORD
if ($type == 'word') {

    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=Target_Pemantauan_$tahun.doc");

    echo "<html>
    <head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid #555; }
        th { background-color: #f2f2f2; text-align: center; }
        td { text-align: center; padding: 5px; }
        .kop td { border: none; }
    </style>
    </head>
    <body>
        <table width='100%' class='kop' cellpadding='4'>
            <tr>
                <td width='15%' align='center' valign='middle'>
                    <img src='" . $logoBase64 . "' 
                        width='80' 
                        height='120'
                        style='max-width: 80px; max-height: 120px;'
                        alt='Logo DLH Kalsel'>
                </td>
                <td width='85%' align='center' style='font-family: Times New Roman; line-height: 1.1;'>
                    <div style='font-size:13pt; margin: 0; padding: 0;'>PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div style='font-size:15pt; font-weight:bold; margin: 1px 0 2px 0; padding: 0;'>DINAS LINGKUNGAN HIDUP</div>
                    <div style='font-size:10pt; margin: 0; padding: 0;'>Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan</div>
                    <div style='font-size:10pt; margin: 0; padding: 0;'>Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241</div>
                    <div style='font-size:10pt; margin: 0; padding: 0;'>Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id</div>
                </td>
            </tr>
        </table>
    <hr>
    <br>
   <h2 style='text-align:center; font-size: 14pt;'>" . $judul . "</h2>";


    echo "<table>
            <thead>
                <tr style='background:#ccc;font-weight:bold'>
                    <th>No</th>
                    <th>Kabupaten/Kota</th>
                    <th>Total Pemantauan</th>
                    <th>Target</th>
                    <th>Status</th>
                </tr>
             </thead>

        <tbody>";
        $no = 1;
        foreach ($data as $d) {

            $total = $d['total_pemantauan'];

            if ($total >= 8) {
                $status = "TERCAPAI";
            } else {
                $status = "BELUM TERCAPAI";
            }

        echo "<tr>
                <td>$no</td>
                <td>{$d['kabupaten_kota']}</td>
                <td>$total</td>
                <td>8 Kali / Tahun</td>
                <td>$status</td>
            </tr>";
        $no++;
    }

    echo "</tbody>
        </table>
        <br><br>
        <p style='text-align:right; font-size:11px; font-style:italic;'>
            Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel<br>
            Pada: " . $tanggalCetak . "
        </p>
    </body>
    </html>";
    exit;
}
?>