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
    background-color: #007bff;
    color: white;
    text-align: center;
}

.table td {
    text-align: center;
    vertical-align: middle;
}

.info-box {
    background: #f8f9fa;
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
    <h2 align="center" style="font-weight:bold">Laporan Hasil Pemeriksaan Kualitas Udara</h2>
    <hr>

    <form method="GET">
        <div class="row">

            <div class="col-sm-4">
                <label>Pilih Periode</label>
                <select name="bulan_tahun" class="form-control">
                    <option value="">-- Silahkan Pilih Periode --</option>
                    <?php
                    $periodeQ = mysqli_query($koneksi,"
                        SELECT DISTINCT 
                            DATE_FORMAT(tanggal_pemantauan, '%m') AS bulan,
                            DATE_FORMAT(tanggal_pemantauan, '%Y') AS tahun,
                            DATE_FORMAT(tanggal_pemantauan, '%m-%Y') AS periode
                        FROM pemantauan_udara
                        ORDER BY tahun DESC, bulan DESC
                    ");

                    $bulanNama = [
                        "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April",
                        "05"=>"Mei","06"=>"Juni","07"=>"Juli","08"=>"Agustus",
                        "09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
                    ];

                    while ($p = mysqli_fetch_assoc($periodeQ)) {
                        $periodeVal = $p['periode'];
                        $periodeText = $bulanNama[$p['bulan']] . " " . $p['tahun'];

                        $sel = (isset($_GET['bulan_tahun']) && $_GET['bulan_tahun']==$periodeVal) 
                            ? "selected" : "";

                        echo "<option value='$periodeVal' $sel>$periodeText</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="col-sm-4">
                <label>Kabupaten/Kota</label>
                <select name="kabupaten" class="form-control">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    <?php
                    $kabQ = mysqli_query($koneksi,"
                        SELECT DISTINCT kabupaten_kota FROM lokasi_pemantauan ORDER BY kabupaten_kota ASC
                    ");
                    while ($kab = mysqli_fetch_assoc($kabQ)) {
                        $sel = (isset($_GET['kabupaten']) && $_GET['kabupaten']==$kab['kabupaten_kota']) 
                            ? "selected" : "";
                        echo "<option value='{$kab['kabupaten_kota']}' $sel>{$kab['kabupaten_kota']}</option>";
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
                <a href="laporan_hasil_pemeriksaan_udara.php" class="btn btn-default btn-block">
                    <span class="glyphicon glyphicon-refresh"></span> Reset
                </a>
            </div>

        </div>
    </form>

    <hr>

<?php
//CEK FILTER
$periode = $_GET['bulan_tahun'] ?? "";
$kab     = $_GET['kabupaten'] ?? "";

if ($periode == "") {

    echo "<div class='alert alert-warning'>
            Silakan pilih periode <b>bulan & tahun</b> terlebih dahulu untuk menampilkan data.
            <br>Filter kabupaten bersifat opsional dan hanya dapat digunakan <b>jika periode sudah dipilih</b>.
          </div>";

} else {

    $filter_terpakai = [];
    $filter_display = "";

    if ($periode != "") {
        list($bl, $th) = explode("-", $periode);
        $filter_terpakai[] = "Periode: <span class='filter-value'>" . $bulanNama[$bl] . " " . $th . "</span>";
    }

    if ($kab != "") {
        $filter_terpakai[] = "Kabupaten/Kota: <span class='filter-value'>" . htmlspecialchars($kab) . "</span>";
    }

    if (!empty($filter_terpakai)) {
        $filter_display = implode("<span class='filter-separator'> | </span>", $filter_terpakai);
    }

    $where = [];

    if ($periode != "") {
        list($bulan, $tahun) = explode("-", $periode);
        $where[] = "MONTH(p.tanggal_pemantauan) = '$bulan'";
        $where[] = "YEAR(p.tanggal_pemantauan) = '$tahun'";
    }

    if ($kab != "") {
        $where[] = "l.kabupaten_kota = '$kab'";
    }

    $whereSQL = "";
    if (!empty($where)) {
        $whereSQL = "WHERE " . implode(" AND ", $where);
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
        $whereSQL
        ORDER BY p.tanggal_pemantauan ASC
    ";

    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) == 0) {

        echo "<div class='alert alert-warning'>
                Tidak ada data pemantauan sesuai filter.
              </div>";

        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }

    } else {

        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }

        echo "
        <div class='table-responsive' style='margin-top:15px;'>
            <table class='table table-bordered table-striped'>
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
                        <th>NO₂<br>(µg/m³)</th>
                        <th>SO₂<br>(µg/m³)</th>
                        <th>PM₂.₅<br>(µg/m³)</th>
                    </tr>
                </thead>
                <tbody>
        ";

        $no = 1;
        while ($row = mysqli_fetch_assoc($query)) {
            echo "
            <tr>
                <td>$no</td>
                <td>{$row['tanggal_pemantauan']}</td>
                <td>{$row['kode_lokasi']}</td>
                <td>{$row['alamat_lokasi']}</td>
                <td>{$row['kabupaten_kota']}</td>
                <td>{$row['durasi_pemantauan']}</td>
                <td>{$row['metode_pemantauan']}</td>
                <td>{$row['shu']}</td>
                <td>{$row['latitude']}</td>
                <td>{$row['longitude']}</td>
                <td>{$row['no2']}</td>
                <td>{$row['so2']}</td>
                <td>{$row['pm25']}</td>
            </tr>";
            $no++;
        }

        echo "</tbody></table></div>";

        echo "<div class='text-right' style='margin-top:15px;'>
                <a href='export_hasil_pemeriksaan.php?periode=$periode&kabupaten=$kab&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
                <a href='export_hasil_pemeriksaan.php?periode=$periode&kabupaten=$kab&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
                <a href='export_hasil_pemeriksaan.php?periode=$periode&kabupaten=$kab&type=word' class='btn btn-primary btn-sm'>Export Word</a>
            </div>";

    }
}
?>

</div>

<?php include "footer.php"; ?>