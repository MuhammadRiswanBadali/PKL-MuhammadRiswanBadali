<?php
include "koneksi.php";
require_once __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$periode    = $_GET['periode'] ?? '';
$peruntukan = $_GET['peruntukan'] ?? '';
$type       = $_GET['type'] ?? 'pdf';

date_default_timezone_set('Asia/Jakarta');

function getRekomendasiSpesifik($parameter, $nilai, $baku_mutu) {
    $kelebihan = $nilai - $baku_mutu;
    
    switch($parameter) {
        case 'NO2':
            if ($kelebihan <= 5) { 
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'warna_rgb' => '255, 193, 7',
                    'warna_excel' => 'FFC107',
                    'rentang' => '66-70',
                    'rekomendasi' => [
                        'Lakukan uji emisi kendaraan di sekitar lokasi pemantauan',
                        'Sosialisasikan pengurangan penggunaan kendaraan pribadi',
                        'Optimalkan waktu tunggu di lampu merah (matikan mesin)',
                        'Tambah jalur sepeda dan perbaiki transportasi publik'
                    ],
                    'tindakan' => 'Peringatan lisan + himbauan tertulis ke pengelola parkir/terminal'
                ];
            } elseif ($kelebihan <= 10) {
                return [
                    'level' => 'SEDANG',
                    'warna' => '#fd7e14',
                    'warna_rgb' => '253, 126, 20',
                    'warna_excel' => 'FD7E14',
                    'rentang' => '71-75',
                    'rekomendasi' => [
                        'Berlakukan ganjil-genap atau pembatasan kendaraan berat',
                        'Inspeksi rutin cerobong industri di radius 2 km',
                        'Tambah ruang terbuka hijau penyerap polutan',
                        'Audit emisi kendaraan umum secara berkala'
                    ],
                    'tindakan' => 'Peringatan tertulis tahap 1 + koordinasi Dinas Perhubungan'
                ];
            } elseif ($kelebihan <= 15) { 
                return [
                    'level' => 'BERAT',
                    'warna' => '#dc3545',
                    'warna_rgb' => '220, 53, 69',
                    'warna_excel' => 'DC3545',
                    'rentang' => '76-80',
                    'rekomendasi' => [
                        'Evaluasi tata ruang dan relokasi sumber emisi',
                        'Audit energi pada industri penghasil NO₂',
                        'Terapkan teknologi denitrifikasi (SCR/SNCR)',
                        'Pengetatan ambang batas emisi kendaraan bermotor'
                    ],
                    'tindakan' => 'Peringatan tertulis tahap 2 + inspeksi lapangan bersama DLH'
                ];
            } else { 
                return [
                    'level' => 'SANGAT BERAT',
                    'warna' => '#8b0000',
                    'warna_rgb' => '139, 0, 0',
                    'warna_excel' => '8B0000',
                    'rentang' => '>80',
                    'rekomendasi' => [
                        'Rekomendasi penghentian sementara operasional sumber emisi',
                        'Penegakan hukum dan sanksi administratif',
                        'Kajian dampak lingkungan menyeluruh (AMDAL)',
                        'Rekomendasi moratorium izin baru di kawasan tersebut'
                    ],
                    'tindakan' => 'Rekomendasi sanksi pidana + pencekalan operasional'
                ];
            }
            break;
            
        case 'SO2':
            if ($kelebihan <= 5) { 
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'warna_rgb' => '255, 193, 7',
                    'warna_excel' => 'FFC107',
                    'rentang' => '61-65',
                    'rekomendasi' => [
                        'Awasi penggunaan bahan bakar berkadar sulfur rendah (<1%)',
                        'Sosialisasi penggunaan bahan bakar ramah lingkungan',
                        'Optimalkan perawatan rutin boiler/insinerator',
                        'Pantau kualitas bahan bakar industri sekitar'
                    ],
                    'tindakan' => 'Peringatan lisan + edukasi penggunaan bahan bakar bersih'
                ];
            } elseif ($kelebihan <= 10) { 
                return [
                    'level' => 'SEDANG',
                    'warna' => '#fd7e14',
                    'warna_rgb' => '253, 126, 20',
                    'warna_excel' => 'FD7E14',
                    'rentang' => '66-70',
                    'rekomendasi' => [
                        'Wajibkan penggunaan bahan bakar sulfur <0.5%',
                        'Inspeksi sistem desulfurisasi (FGD) di industri',
                        'Pantau kualitas bahan bakar secara berkala',
                        'Uji petik kadar sulfur bahan bakar industri'
                    ],
                    'tindakan' => 'Peringatan tertulis tahap 1 + inspeksi mendadak'
                ];
            } elseif ($kelebihan <= 15) { 
                return [
                    'level' => 'BERAT',
                    'warna' => '#dc3545',
                    'warna_rgb' => '220, 53, 69',
                    'warna_excel' => 'DC3545',
                    'rentang' => '71-75',
                    'rekomendasi' => [
                        'Evaluasi teknis sistem pengendali emisi (scrubber)',
                        'Rekomendasi perbaikan atau penggantian teknologi',
                        'Pengetatan izin lingkungan (UKL-UPL)',
                        'Audit menyeluruh proses produksi penghasil SO₂'
                    ],
                    'tindakan' => 'Peringatan tertulis tahap 2 + evaluasi teknis 14 hari'
                ];
            } else {
                return [
                    'level' => 'SANGAT BERAT',
                    'warna' => '#8b0000',
                    'warna_rgb' => '139, 0, 0',
                    'warna_excel' => '8B0000',
                    'rentang' => '>75',
                    'rekomendasi' => [
                        'Rekomendasi moratorium sementara kegiatan',
                        'Investigasi kepatuhan hukum lingkungan',
                        'Ganti bahan bakar ke energi alternatif bersih (gas/listrik)',
                        'Rekomendasi pencabutan izin sementara'
                    ],
                    'tindakan' => 'Rekomendasi sanksi administratif/pidana + penghentian operasi'
                ];
            }
            break;
            
        case 'PM2.5':
            if ($kelebihan <= 5) { 
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'warna_rgb' => '255, 193, 7',
                    'warna_excel' => 'FFC107',
                    'rentang' => '56-60',
                    'rekomendasi' => [
                        'Siram jalan raya secara berkala (minimal 2x/hari)',
                        'Tutup rapat material konstruksi berpotensi debu',
                        'Edukasi warga untuk tidak bakar sampah terbuka',
                        'Tanam pohon berdaun lebar di sepanjang jalan'
                    ],
                    'tindakan' => 'Edukasi masyarakat + koordinasi Dinas PU untuk penyiraman jalan'
                ];
            } elseif ($kelebihan <= 10) { 
                return [
                    'level' => 'SEDANG',
                    'warna' => '#fd7e14',
                    'warna_rgb' => '253, 126, 20',
                    'warna_excel' => 'FD7E14',
                    'rentang' => '61-65',
                    'rekomendasi' => [
                        'Gunakan water spray di area konstruksi dan jalan',
                        'Pasang filter udara (baghouse/electrostatic precipitator) di industri',
                        'Larang pembakaran terbuka dengan pengawasan',
                        'Wajibkan penggunaan masker bagi pekerja luar ruangan'
                    ],
                    'tindakan' => 'Peringatan tertulis ke kontraktor/industri + sosialisasi masker'
                ];
            } elseif ($kelebihan <= 15) { 
                return [
                    'level' => 'BERAT',
                    'warna' => '#dc3545',
                    'warna_rgb' => '220, 53, 69',
                    'warna_excel' => 'DC3545',
                    'rentang' => '66-70',
                    'rekomendasi' => [
                        'Evaluasi teknis cerobong dan sistem ventilasi industri',
                        'Rekomendasi work from home bagi rentan (lansia, anak)',
                        'Inspeksi kepatuhan penggunaan masker industri',
                        'Pengetatan pengawasan pembakaran lahan'
                    ],
                    'tindakan' => 'Peringatan tertulis tahap 2 + inspeksi lapangan + rekomendasi WFH'
                ];
            } else { 
                return [
                    'level' => 'SANGAT BERAT',
                    'warna' => '#8b0000',
                    'warna_rgb' => '139, 0, 0',
                    'warna_excel' => '8B0000',
                    'rentang' => '>70',
                    'rekomendasi' => [
                        'Hentikan sementara aktivitas konstruksi/pembakaran',
                        'Evakuasi sementara warga rentan jika diperlukan',
                        'Tindakan hukum tegas pada pelaku pembakaran lahan',
                        'Operasi modifikasi cuaca jika diperlukan (hujan buatan)'
                    ],
                    'tindakan' => 'Rekomendasi penghentian aktivitas + koordinasi BPBD + penegakan hukum'
                ];
            }
            break;
            
        default:
            return [
                'level' => 'TIDAK DIKETAHUI',
                'warna' => '#6c757d',
                'warna_rgb' => '108, 117, 125',
                'warna_excel' => '6C757D',
                'rentang' => '-',
                'rekomendasi' => ['Perlu evaluasi lebih lanjut oleh petugas'],
                'tindakan' => 'Koordinasi dengan laboratorium lingkungan'
            ];
    }
}

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
        'NO2' AS parameter_display,
        'NO2' AS parameter,
        h.no2 AS nilai,
        65 AS baku_mutu,
        (h.no2 - 65) AS kelebihan,
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.no2 > 65

    UNION ALL

    SELECT 
        l.kode_lokasi,
        l.peruntukan,
        l.alamat_lokasi,
        l.kabupaten_kota,
        'SO2',
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
        'PM2.5',
        'PM2.5',
        h.pm25,
        55,
        (h.pm25 - 55),
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.pm25 > 55

    ORDER BY kabupaten_kota ASC, tanggal_pemantauan ASC
";

$result = mysqli_query($koneksi, $sql);
if (mysqli_num_rows($result) == 0) die("Tidak ada data.");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rekom = getRekomendasiSpesifik($row['parameter'], $row['nilai'], $row['baku_mutu']);
    $row['level'] = $rekom['level'];
    $row['level_warna'] = $rekom['warna'];
    $row['rentang'] = $rekom['rentang'];
    
    $rekomendasi_text = "";
    foreach($rekom['rekomendasi'] as $r) {
        $rekomendasi_text .= "• " . $r . "\n";
    }
    $row['rekomendasi_list'] = $rekomendasi_text;
    
    $row['rekomendasi_array'] = $rekom['rekomendasi'];
    
    $row['tindakan'] = $rekom['tindakan'];
    $data[] = $row;
}

$judul = "Laporan Pelanggaran Baku Mutu Udara";
$tanggalCetak = date("d M Y");
$totalData = count($data);


$filterInfoWithTotal = $filterInfo;
if (!empty($filterInfoWithTotal)) {
    $filterInfoWithTotal .= " ";
}

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
            body { font-family: Arial, sans-serif; font-size: 11px; }
            @page { 
                size: A4 landscape;
                margin: 1.5cm 1cm 1cm 1cm;
            }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                margin-top: 10px;
                font-size: 10px;
            }
            table, th, td { 
                border: 1px solid #555; 
            }
            th { 
                background-color: #dc3545; 
                color: white; 
                text-align: center; 
                font-weight: bold; 
                padding: 8px 3px;
                vertical-align: middle;
            }
            td { 
                text-align: center; 
                padding: 5px 3px;
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
                font-size: 8px; 
                font-style: italic; 
                margin-top: 20px; 
            }
            .filter-info { 
                font-size: 10px; 
                color: #666; 
                margin-bottom: 10px; 
                text-align: center; 
            }
            .level-ringan { background-color: #ffc107; color: black; font-weight: bold; text-align: center; }
            .level-sedang { background-color: #fd7e14; color: white; font-weight: bold; text-align: center; }
            .level-berat { background-color: #dc3545; color: white; font-weight: bold; text-align: center; }
            .level-sgt-berat { background-color: #8b0000; color: white; font-weight: bold; text-align: center; }
            .rekomendasi-box { 
                text-align: left; 
                padding: 3px 5px; 
                font-size: 9px;
                background-color: #f0f8ff;
                white-space: normal;
                word-wrap: break-word;
            }
            .tindakan-box { 
                text-align: left; 
                padding: 3px 5px; 
                font-size: 9px;
                background-color: #e6f3ff;
                font-weight: 500;
            }
            .parameter-info { font-size: 9px; color: #666; }
            .bullet-list {
                margin: 0;
                padding-left: 15px;
            }
            .bullet-list li {
                margin-bottom: 2px;
            }
        </style>
    </head>
    <body>';

    $html .= '
        <table width="100%" class="kop" cellpadding="4">
            <tr>
                <td width="15%">
                    <img src="' . $logoBase64 . '" width="70"> 
                </td>
                <td width="85%" align="center" style="font-family: Times New Roman;">
                    <div style="font-size:12pt;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                    <div style="font-size:14pt; font-weight:bold;">DINAS LINGKUNGAN HIDUP</div>
                    <div style="font-size:9pt;">Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan</div>
                    <div style="font-size:9pt;">Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241</div>
                    <div style="font-size:9pt;">Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id</div>
                </td>
            </tr>
        </table>

        <hr style="height:2px; border:none; color:#333; background-color:#333;">';

    $html .= '<h3 style="text-align:center; font-size:12pt; margin:8px 0;">' . $judul . '</h3>';

    if (!empty($filterInfoWithTotal)) {
        $html .= '
        <div class="filter-info" style="text-align:center;">
            ' . str_replace(['<b>', '</b>'], ['<strong>', '</strong>'], $filterInfoWithTotal) . '
        </div>';
    }

    $html .= '
        <table>
            <thead>
                <tr>
                    <th rowspan="2" width="3%">No</th>
                    <th rowspan="2" width="7%">Kode Lokasi</th>
                    <th rowspan="2" width="8%">Peruntukan</th>
                    <th rowspan="2" width="10%">Alamat Lokasi</th>
                    <th rowspan="2" width="8%">Kab/Kota</th>
                    <th rowspan="2" width="5%">Parameter</th>
                    <th colspan="3">Hasil Pengukuran</th>
                    <th rowspan="2" width="7%">Tanggal</th>
                    <th rowspan="2" width="7%">Level</th>
                    <th colspan="2">Rekomendasi Penanganan</th>
                </tr>
                <tr>
                    <th width="5%">Nilai</th>
                    <th width="5%">BM</th>
                    <th width="5%">Kelebihan</th>
                    <th width="15%">Rekomendasi Teknis</th>
                    <th width="10%">Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>';

    $no = 1;
    foreach ($data as $d) {
        
        $levelClass = '';
        switch($d['level']) {
            case 'RINGAN': $levelClass = 'level-ringan'; break;
            case 'SEDANG': $levelClass = 'level-sedang'; break;
            case 'BERAT': $levelClass = 'level-berat'; break;
            case 'SANGAT BERAT': $levelClass = 'level-sgt-berat'; break;
        }
        

        $rekomendasi_html = "<ul class='bullet-list' style='margin:0; padding-left:15px;'>";
        foreach($d['rekomendasi_array'] as $r) {
            $rekomendasi_html .= "<li>" . $r . "</li>";
        }
        $rekomendasi_html .= "</ul>";
        
        $html .= "
            <tr>
                <td>{$no}</td>
                <td>{$d['kode_lokasi']}</td>
                <td>{$d['peruntukan']}</td>
                <td align=\"left\">" . substr($d['alamat_lokasi'], 0, 30) . (strlen($d['alamat_lokasi']) > 30 ? '...' : '') . "</td>
                <td>{$d['kabupaten_kota']}</td>
                <td><b>{$d['parameter_display']}</b><br><span class='parameter-info'>({$d['rentang']})</span></td>
                <td>" . number_format($d['nilai'], 2) . "</td>
                <td>{$d['baku_mutu']}</td>
                <td class='kelebihan'>+" . number_format($d['kelebihan'], 2) . "</td>
                <td>" . date('d-m-Y', strtotime($d['tanggal_pemantauan'])) . "</td>
                <td class='{$levelClass}'>{$d['level']}</td>
                <td class='rekomendasi-box'>{$rekomendasi_html}</td>
                <td class='tindakan-box'>{$d['tindakan']}</td>
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


if ($type == 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Pelanggaran Baku Mutu');


    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Kalsel');
        $drawing->setPath($logoPath);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    }


    $sheet->mergeCells('B1:O1');
    $sheet->setCellValue('B1', 'PEMERINTAH PROVINSI KALIMANTAN SELATAN');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B2:O2');
    $sheet->setCellValue('B2', 'DINAS LINGKUNGAN HIDUP');
    $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B3:O3');
    $sheet->setCellValue('B3', "Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan");
    $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B4:O4');
    $sheet->setCellValue('B4', "Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241");
    $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->mergeCells('B5:O5');
    $sheet->setCellValue('B5', "Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id");
    $sheet->getStyle('B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);


    $row = 7;
    $sheet->mergeCells('A' . $row . ':O' . $row);
    $sheet->setCellValue('A' . $row, $judul);
    $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $row++;


    if (!empty($filterInfo)) {
        $sheet->mergeCells('A' . $row . ':O' . $row);
        $excelFilterInfo = str_replace(['<b>', '</b>'], '', $filterInfo);
        $sheet->setCellValue('A' . $row, $excelFilterInfo);
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F8F9FA');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
    }
    $row++;

   
    $sheet->setCellValue('A' . $row, 'No');
    $sheet->setCellValue('B' . $row, 'Kode Lokasi');
    $sheet->setCellValue('C' . $row, 'Peruntukan');
    $sheet->setCellValue('D' . $row, 'Alamat Lokasi');
    $sheet->setCellValue('E' . $row, 'Kab/Kota');
    $sheet->setCellValue('F' . $row, 'Parameter');
    $sheet->setCellValue('G' . $row, 'Hasil Pengukuran');
    $sheet->setCellValue('J' . $row, 'Tanggal');
    $sheet->setCellValue('K' . $row, 'Level');
    $sheet->setCellValue('L' . $row, 'Rekomendasi Penanganan');
    
    $sheet->mergeCells('G' . $row . ':I' . $row);
    $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $sheet->mergeCells('L' . $row . ':M' . $row);
    $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
    
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, '');
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, '');
    $sheet->setCellValue('F' . $row, '');
    $sheet->setCellValue('G' . $row, 'Nilai');
    $sheet->setCellValue('H' . $row, 'BM');
    $sheet->setCellValue('I' . $row, 'Kelebihan');
    $sheet->setCellValue('J' . $row, '');
    $sheet->setCellValue('K' . $row, '');
    $sheet->setCellValue('L' . $row, 'Rekomendasi Teknis');
    $sheet->setCellValue('M' . $row, 'Tindak Lanjut');
    
    $headerStyle = $sheet->getStyle('A' . ($row-1) . ':M' . $row);
    $headerStyle->getFont()->setBold(true);
    $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DC3545');
    $headerStyle->getFont()->getColor()->setARGB('FFFFFF');
    $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $headerStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    $row++;
    $startDataRow = $row;
    $no = 1;
    
    foreach ($data as $d) {
        $sheet->setCellValue('A' . $row, $no);
        $sheet->setCellValue('B' . $row, $d['kode_lokasi']);
        $sheet->setCellValue('C' . $row, $d['peruntukan']);
        $sheet->setCellValue('D' . $row, $d['alamat_lokasi']);
        $sheet->setCellValue('E' . $row, $d['kabupaten_kota']);
        $sheet->setCellValue('F' . $row, $d['parameter_display'] . ' (' . $d['rentang'] . ')');
        $sheet->setCellValue('G' . $row, $d['nilai']);
        $sheet->setCellValue('H' . $row, $d['baku_mutu']);
        $sheet->setCellValue('I' . $row, $d['kelebihan']);
        $sheet->setCellValue('J' . $row, date('d-m-Y', strtotime($d['tanggal_pemantauan'])));
        $sheet->setCellValue('K' . $row, $d['level']);
        
        $rekomendasi_excel = "";
        foreach($d['rekomendasi_array'] as $r) {
            $rekomendasi_excel .= chr(149) . " " . $r . "\n";
        }
        $sheet->setCellValue('L' . $row, trim($rekomendasi_excel));
        $sheet->setCellValue('M' . $row, $d['tindakan']);
        
        $sheet->getStyle('L' . $row)->getAlignment()->setWrapText(true);
        
        $sheet->getStyle('I' . $row)->getFont()->getColor()->setARGB('FF0000');
        
        $sheet->getStyle('K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($d['level_warna']);
        
        $row++;
        $no++;
    }
    
    $lastRow = $row - 1;
    
    $sheet->getStyle('A' . $startDataRow . ':M' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    foreach (range('A', 'M') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    $footerRow = $lastRow + 3;
    $sheet->mergeCells('A' . $footerRow . ':M' . $footerRow);
    $sheet->setCellValue('A' . $footerRow, 'Dicetak oleh Sistem Informasi Pemantauan Udara - DLH Prov. KalSel | Pada: ' . $tanggalCetak);
    $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('A' . $footerRow)->getFont()->setItalic(true)->setSize(10);
    
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
    
    $writer = new Xlsx($spreadsheet);
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

if ($type == 'word') {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=Laporan_Pelanggaran_Baku_Mutu_" . date('YmdHis') . ".doc");

    echo '
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta charset="UTF-8">
    <title>Laporan Pelanggaran Baku Mutu Udara</title>

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
            size: 841.7pt 595.45pt;
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
            padding: 5px; 
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        td { 
            padding: 4px; 
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
            font-size: 8pt; 
            font-style: italic; 
            margin-top: 20px;
        }
        .filter-info { 
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }
        .level-ringan { background-color: #ffc107; color: black; font-weight: bold; text-align: center; }
        .level-sedang { background-color: #fd7e14; color: white; font-weight: bold; text-align: center; }
        .level-berat { background-color: #dc3545; color: white; font-weight: bold; text-align: center; }
        .level-sgt-berat { background-color: #8b0000; color: white; font-weight: bold; text-align: center; }
        .rekomendasi-box { 
            text-align: left; 
            padding: 3px; 
            font-size: 9px;
            background-color: #f0f8ff;
        }
        .tindakan-box { 
            text-align: left; 
            padding: 3px; 
            font-size: 9px;
            background-color: #e6f3ff;
        }
        .parameter-info { font-size: 8px; color: #666; }
        .bullet-list {
            margin: 0;
            padding-left: 15px;
        }
        .bullet-list li {
            margin-bottom: 2px;
        }
    </style>

    </head>
        <body>
            <div class="Section1">';

    echo '
            <table class="kop" width="100%" cellpadding="4">
                <tr>
                    <td width="15%" align="center" valign="middle">
                        <img src="'.$logoBase64.'" 
                            width="70" 
                            style="max-width: 70px;"
                            alt="Logo DLH Kalsel">
                    </td>
                    <td width="85%" align="center" style="font-family: Times New Roman;">
                        <div style="font-size:12pt;">PEMERINTAH PROVINSI KALIMANTAN SELATAN</div>
                        <div style="font-size:14pt; font-weight:bold;">DINAS LINGKUNGAN HIDUP</div>
                        <div style="font-size:9pt;">Kawasan Perkantoran Pemerintah Provinsi Kalimantan Selatan</div>
                        <div style="font-size:9pt;">Jl. Bangun Praja Banjarbaru Kode Pos 70732, Telp/Fax: (0815)-6749241</div>
                        <div style="font-size:9pt;">Email: blhdkalsel@gmail.com | Website: dlh.kalselprov.go.id</div>
                    </td>
                </tr>
            </table>

            <hr style="height:2px; border:none; color:#333; background-color:#333;">

            <h3 style="text-align:center; margin:8px 0; font-size: 12pt;">'.$judul.'</h3>';

    if (!empty($filterInfoWithTotal)) {
        echo '<div class="filter-info" style="text-align:center;">' . $filterInfoWithTotal . '</div>';
    }

    echo '
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" width="3%">No</th>
                        <th rowspan="2" width="7%">Kode Lokasi</th>
                        <th rowspan="2" width="8%">Peruntukan</th>
                        <th rowspan="2" width="10%">Alamat Lokasi</th>
                        <th rowspan="2" width="8%">Kab/Kota</th>
                        <th rowspan="2" width="5%">Parameter</th>
                        <th colspan="3">Hasil Pengukuran</th>
                        <th rowspan="2" width="7%">Tanggal</th>
                        <th rowspan="2" width="7%">Level</th>
                        <th colspan="2">Rekomendasi Penanganan</th>
                    </tr>
                    <tr>
                        <th width="5%">Nilai</th>
                        <th width="5%">BM</th>
                        <th width="5%">Kelebihan</th>
                        <th width="15%">Rekomendasi Teknis</th>
                        <th width="10%">Tindak Lanjut</th>
                    </tr>
                </thead>
                <tbody>';

    $no = 1;
    foreach ($data as $d) {
        $levelClass = '';
        switch($d['level']) {
            case 'RINGAN': $levelClass = 'level-ringan'; break;
            case 'SEDANG': $levelClass = 'level-sedang'; break;
            case 'BERAT': $levelClass = 'level-berat'; break;
            case 'SANGAT BERAT': $levelClass = 'level-sgt-berat'; break;
        }
        
        $rekomendasi_html = "<ul class='bullet-list' style='margin:0; padding-left:15px;'>";
        foreach($d['rekomendasi_array'] as $r) {
            $rekomendasi_html .= "<li>" . $r . "</li>";
        }
        $rekomendasi_html .= "</ul>";
        
        echo '
                <tr>
                    <td>'.$no.'</td>
                    <td>'.$d['kode_lokasi'].'</td>
                    <td>'.$d['peruntukan'].'</td>
                    <td align="left">'.$d['alamat_lokasi'].'</td>
                    <td>'.$d['kabupaten_kota'].'</td>
                    <td><b>'.$d['parameter_display'].'</b><br><span class="parameter-info">('.$d['rentang'].')</span></td>
                    <td>'.number_format($d['nilai'], 2).'</td>
                    <td>'.$d['baku_mutu'].'</td>
                    <td class="kelebihan">+'.number_format($d['kelebihan'], 2).'</td>
                    <td>'.date('d-m-Y', strtotime($d['tanggal_pemantauan'])).'</td>
                    <td class="'.$levelClass.'">'.$d['level'].'</td>
                    <td class="rekomendasi-box">'.$rekomendasi_html.'</td>
                    <td class="tindakan-box">'.$d['tindakan'].'</td>
                </tr>';
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