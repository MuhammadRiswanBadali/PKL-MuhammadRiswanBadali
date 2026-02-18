<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";
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
</style>

<div class="container-fluid">
    <h2 align="center" style="font-weight:bold;">
        Laporan Pelanggaran Baku Mutu Udara
    </h2>
    <hr>

    <div class="info-box">
        <b>Acuan Baku Mutu Tahunan PP No. 22 Tahun 2021:</b><br>
        NO₂ = 50 µg/m³ &nbsp;|&nbsp;
        SO₂ = 60 µg/m³ &nbsp;|&nbsp;
        PM₂.₅ = 15 µg/m³
    </div>

    <!-- FILTER -->
    <form method="GET">
        <div class="row">

            <!-- PERIODE -->
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

            <!-- PERUNTUKAN -->
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

// Tampilkan filter yang sedang digunakan
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

// Gabungkan filter dengan separator " | "
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
        15,
        (h.pm25 - 15),
        p.tanggal_pemantauan
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan=p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi=l.id_lokasi
    $where AND h.pm25 > 15

    ORDER BY kabupaten_kota ASC, tanggal_pemantauan ASC
";

$query = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($query)==0) {
    echo "<div class='alert alert-info'>
            Tidak ditemukan pelanggaran baku mutu pada filter yang dipilih.
          </div>";
} else {
    // Tampilkan filter yang sedang digunakan di atas tabel
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
                <th>No</th>
                <th>Kode Lokasi</th>
                <th>Peruntukan</th>
                <th>Alamat</th>
                <th>Kab/Kota</th>
                <th>Parameter</th>
                <th>Nilai (µg/m³)</th>
                <th>Baku Mutu (µg/m³)</th>
                <th>Kelebihan</th>
                <th>Tanggal Pemantauan</th>
            </tr>
        </thead>
        <tbody>";

    $no=1;
    while($d=mysqli_fetch_assoc($query)){
        echo "<tr>
            <td>$no</td>
            <td>{$d['kode_lokasi']}</td>
            <td>{$d['peruntukan']}</td>
            <td>{$d['alamat_lokasi']}</td>
            <td>{$d['kabupaten_kota']}</td>
            <td><b>{$d['parameter']}</b></td>
            <td>{$d['nilai']}</td>
            <td>{$d['baku_mutu']}</td>
            <td style='color:red;font-weight:bold;'>+".number_format($d['kelebihan'],2)."</td>
            <td>".date('d-m-Y',strtotime($d['tanggal_pemantauan']))."</td>
        </tr>";
        $no++;
    }

    echo "</tbody></table></div>";

    // TOMBOL EXPORT
    echo "<div class='text-right' style='margin-top:15px;'>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
            <a href='export_pelanggaran_baku_mutu.php?periode=$periode&peruntukan=$peruntukan&type=word' class='btn btn-primary btn-sm'>Export Word</a>
        </div>";
}
?>

</div>

<?php include "footer.php"; ?>