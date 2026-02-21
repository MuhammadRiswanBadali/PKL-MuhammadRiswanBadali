<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";

function getRekomendasiSpesifik($parameter, $nilai, $baku_mutu) {
    $kelebihan = $nilai - $baku_mutu;
    
    switch($parameter) {
        case 'NO₂':
            if ($kelebihan <= 5) { 
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'rentang' => '66 - 70 µg/m³',
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
                    'rentang' => '71 - 75 µg/m³',
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
                    'rentang' => '76 - 80 µg/m³',
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
                    'rentang' => '> 80 µg/m³',
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
            
        case 'SO₂':
            if ($kelebihan <= 5) { 
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'rentang' => '61 - 65 µg/m³',
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
                    'rentang' => '66 - 70 µg/m³',
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
                    'rentang' => '71 - 75 µg/m³',
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
                    'rentang' => '> 75 µg/m³',
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
            
        case 'PM₂.₅':
            if ($kelebihan <= 5) {
                return [
                    'level' => 'RINGAN',
                    'warna' => '#ffc107',
                    'rentang' => '56 - 60 µg/m³',
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
                    'rentang' => '61 - 65 µg/m³',
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
                    'rentang' => '66 - 70 µg/m³',
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
                    'rentang' => '> 70 µg/m³',
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
                'rentang' => '-',
                'rekomendasi' => ['Perlu evaluasi lebih lanjut oleh petugas'],
                'tindakan' => 'Koordinasi dengan laboratorium lingkungan'
            ];
    }
}


?>
<style>
.container-fluid {
    width: 100%;
    margin: 0;
    padding: 10px 20px;
}
.table th {
    background-color: #dc3545;
    color: white;
    text-align: center;
    vertical-align: middle;
}
.table td {
    text-align: center;
    vertical-align: middle;
}
.info-box {
    background: #e9ecef;
    padding: 15px;
    border-left: 5px solid #007bff;
    margin-bottom: 20px;
}
.filter-info-box {
    background: #e9ecef;
    padding: 10px 15px;
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
    border-radius: 4px;
}
.filter-label {
    font-weight: bold;
    color: #495057;
}
.filter-value {
    font-weight: bold;
}
.filter-separator {
    margin: 0 10px;
    color: #6c757d;
}

.rekomendasi-list {
    margin: 0;
    padding-left: 20px;
    text-align: left;
}
.rekomendasi-list li {
    margin-bottom: 3px;
    font-size: 0.9em;
}
.tindakan-box {
    background-color: #e6f3ff;
    padding: 5px;
    border-radius: 3px;
    font-weight: 500;
    font-size: 0.9em;
}
.level-badge {
    padding: 5px 8px;
    border-radius: 4px;
    color: white;
    font-weight: bold;
    display: inline-block;
    min-width: 100px;
}
.table-responsive {
    overflow-x: auto;
}
</style>

<div class="container-fluid">
    <h2 align="center" style="font-weight:bold;">
        Laporan Pelanggaran Baku Mutu Udara
    </h2>
    <hr>

    <div class="info-box">
        <b>Acuan Baku Mutu Harian (24 jam) PP No. 22 Tahun 2021:</b><br>
        NO₂ = 65 µg/m³ &nbsp;|&nbsp;
        SO₂ = 60 µg/m³ &nbsp;|&nbsp;
        PM₂.₅ = 55 µg/m³
    </div>

    <div class="info-box">
    <b>Keterangan Level Pelanggaran (Kelipatan 5 dari Baku Mutu):</b><br>
    <div style="display: flex; flex-wrap: wrap; margin-top: 8px; gap: 15px;">
        
        <div style="min-width: 150px;">
            <span style="background-color: #ffc107; color: black; padding: 2px 8px; border-radius: 3px; font-weight: bold;">RINGAN</span><br>
            Kelebihan 1-5 µg/m³
        </div>
        <div style="min-width: 150px;">
            <span style="background-color: #fd7e14; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold;">SEDANG</span><br>
            Kelebihan 6-10 µg/m³
        </div>
        <div style="min-width: 150px;">
            <span style="background-color: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold;">BERAT</span><br>
            Kelebihan 11-15 µg/m³
        </div>
        <div style="min-width: 150px;">
            <span style="background-color: #8b0000; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold;">SGT BERAT</span><br>
            Kelebihan >15 µg/m³
        </div>
    </div>
</div>

    <form method="GET">
        <div class="row">
            <div class="col-sm-4">
                <label>Periode (Bulan & Tahun)</label>
                <select name="periode" class="form-control">
                    <option value="">-- Semua Periode --</option>
                    <?php
                    $periodeQ = mysqli_query($koneksi,"
                        SELECT DISTINCT 
                            DATE_FORMAT(tanggal_pemantauan,'%m-%Y') AS periode,
                            DATE_FORMAT(tanggal_pemantauan,'%m') AS bulan,
                            DATE_FORMAT(tanggal_pemantauan,'%Y') AS tahun
                        FROM pemantauan_udara
                        ORDER BY tahun DESC, bulan DESC
                    ");

                    $bulanNama = [
                        "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
                        "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
                        "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
                    ];

                    while ($p = mysqli_fetch_assoc($periodeQ)) {
                        $val = $p['periode'];
                        $text = $bulanNama[$p['bulan']] . " " . $p['tahun'];
                        $sel = (@$_GET['periode']==$val) ? "selected" : "";
                        echo "<option value='$val' $sel>$text</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-sm-4">
                <label>Peruntukan</label>
                <select name="peruntukan" class="form-control">
                    <option value="">-- Semua Peruntukan --</option>
                    <?php
                    $perQ = mysqli_query($koneksi,"
                        SELECT DISTINCT peruntukan 
                        FROM lokasi_pemantauan 
                        ORDER BY peruntukan ASC
                    ");
                    while ($p = mysqli_fetch_assoc($perQ)) {
                        $sel = (@$_GET['peruntukan']==$p['peruntukan']) ? "selected" : "";
                        echo "<option value='{$p['peruntukan']}' $sel>{$p['peruntukan']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="glyphicon glyphicon-search"></span> Tampilkan
                </button>
            </div>

            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <a href="laporan_pelanggaran_baku_mutu.php" class="btn btn-default btn-block">
                    <span class="glyphicon glyphicon-refresh"></span> Reset
                </a>
            </div>
        </div>
    </form>

    <hr>

<?php
$periode    = $_GET['periode'] ?? '';
$peruntukan = $_GET['peruntukan'] ?? '';


$filter_terpakai = [];
$filter_display = "";

if (!empty($periode)) {
    list($bulan,$tahun) = explode('-', $periode);
    $bulanNama = [
        "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
        "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
        "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
    ];
    $filter_terpakai[] = "Periode: <span class='filter-value'>" . $bulanNama[$bulan] . " " . $tahun . "</span>";
}

if (!empty($peruntukan)) {
    $filter_terpakai[] = "Peruntukan: <span class='filter-value'>" . htmlspecialchars($peruntukan) . "</span>";
}

if (!empty($filter_terpakai)) {
    $filter_display = implode("<span class='filter-separator'> | </span>", $filter_terpakai);
}

$where = "WHERE 1=1 ";

if (!empty($periode)) {
    list($bulan,$tahun) = explode('-', $periode);
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
        'NO₂' AS parameter,
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
        'SO₂',
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
        'PM₂.₅',
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

$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query)==0) {
    echo "<div class='alert alert-info'>
            Tidak ditemukan pelanggaran baku mutu pada filter yang dipilih.
          </div>";
} else {
    if (!empty($filter_display)) {
        echo "<div class='filter-info-box'>
                <span class='filter-label'>Filter yang digunakan:</span><br>
                " . $filter_display . "
              </div>";
    }
    
    echo "<div class='table-responsive'>
    <table class='table table-bordered table-striped'>
        <thead>
            <tr>
                <th rowspan='2'>No</th>
                <th rowspan='2'>Kode Lokasi</th>
                <th rowspan='2'>Peruntukan</th>
                <th rowspan='2'>Alamat</th>
                <th rowspan='2'>Kab/Kota</th>
                <th rowspan='2'>Parameter</th>
                <th colspan='3'>Hasil Pengukuran</th>
                <th rowspan='2'>Tanggal</th>
                <th rowspan='2'>Level</th>
                <th colspan='2'>Rekomendasi Penanganan</th>
            </tr>
            <tr>
                <th>Nilai (µg/m³)</th>
                <th>Baku Mutu (µg/m³)</th>
                <th>Kelebihan</th>
                <th>Rekomendasi Teknis</th>
                <th>Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>";

    $no=1;
    while($d=mysqli_fetch_assoc($query)){
       
        $rekom = getRekomendasiSpesifik($d['parameter'], $d['nilai'], $d['baku_mutu']);
        
        $rekomendasi_list = "<ul class='rekomendasi-list'>";
        foreach($rekom['rekomendasi'] as $r) {
            $rekomendasi_list .= "<li>$r</li>";
        }
        $rekomendasi_list .= "</ul>";
        
        echo "<tr>
            <td>$no</td>
            <td>{$d['kode_lokasi']}</td>
            <td>{$d['peruntukan']}</td>
            <td>{$d['alamat_lokasi']}</td>
            <td>{$d['kabupaten_kota']}</td>
            <td><b>{$d['parameter']}</b><br><small style='color:#666;'>({$rekom['rentang']})</small></td>
            <td><b>" . number_format($d['nilai'], 2) . "</b></td>
            <td>{$d['baku_mutu']}</td>
            <td style='color:red;font-weight:bold;'>+" . number_format($d['kelebihan'], 2) . "</td>
            <td>" . date('d-m-Y', strtotime($d['tanggal_pemantauan'])) . "</td>
            <td style='background-color: {$rekom['warna']}; color: white; font-weight:bold; text-align:center;'>
                {$rekom['level']}
            </td>
            <td style='text-align:left; background-color: #f0f8ff;'>
                $rekomendasi_list
            </td>
            <td style='text-align:left; background-color: #e6f3ff;'>
                <div class='tindakan-box'>
                    <strong>{$rekom['tindakan']}</strong>
                </div>
            </td>
        </tr>";
        $no++;
    }

    echo "</tbody></table></div>";

    echo "<div class='text-right' style='margin-top:15px;'>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=word' class='btn btn-primary btn-sm'>Export Word</a>
        </div>";
}
?>

</div>

<?php include "footer.php"; ?>