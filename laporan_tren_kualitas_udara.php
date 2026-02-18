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
    vertical-align: middle;
    text-align: center;
}
.filter-row {
    margin-bottom: 10px;
}
/* TAMBAHAN UNTUK TAMPILAN FILTER */
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
    <h2 align="center" style=font-weight:bold>Laporan Tren Kualitas Udara</h2>
    <hr/>

    <form method="get" action="">
        <div class="row filter-row">

            <div class="col-sm-4">
                <label>Kabupaten / Kota</label>
                <select name="kabupaten" class="form-control">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    <?php
                    $kabQuery = mysqli_query($koneksi, "SELECT DISTINCT kabupaten_kota FROM lokasi_pemantauan ORDER BY kabupaten_kota ASC");
                    while ($k = mysqli_fetch_assoc($kabQuery)) {
                        $selected = (@$_GET['kabupaten'] == $k['kabupaten_kota']) ? 'selected' : '';
                        echo "<option value='{$k['kabupaten_kota']}' $selected>{$k['kabupaten_kota']}</option>";
                    }
                    ?>
                </select>
            </div>

         
            <div class="col-sm-4">
                <label>Peruntukan</label>
                <select name="peruntukan" class="form-control">
                    <option value="">-- Semua Peruntukan --</option>
                    <option value="PERKANTORAN" <?= (@$_GET['peruntukan']=='PERKANTORAN') ? 'selected' : '' ?>>Perkantoran</option>
                    <option value="PEMUKIMAN" <?= (@$_GET['peruntukan']=='PEMUKIMAN') ? 'selected' : '' ?>>Pemukiman</option>
                    <option value="INDUSTRI" <?= (@$_GET['peruntukan']=='INDUSTRI') ? 'selected' : '' ?>>Industri</option>
                    <option value="TRANSPORTASI" <?= (@$_GET['peruntukan']=='TRANSPORTASI') ? 'selected' : '' ?>>Transportasi</option>
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
                <a href="laporan_tren_kualitas_udara.php" class="btn btn-default btn-block">
                    <span class="glyphicon glyphicon-refresh"></span> Hapus
                </a>
            </div>

        </div>
    </form>

    <hr/>

<?php

if (isset($_GET['kabupaten']) || isset($_GET['peruntukan'])) {

    $kabupaten  = $_GET['kabupaten'] ?? '';
    $peruntukan = $_GET['peruntukan'] ?? '';

    // ========= TAMPILKAN FILTER YANG DIGUNAKAN =========
    $filter_terpakai = [];
    $filter_display = "";

    if (!empty($kabupaten)) {
        $filter_terpakai[] = "Kabupaten/Kota: <span class='filter-value'>" . htmlspecialchars($kabupaten) . "</span>";
    }

    if (!empty($peruntukan)) {
        $filter_terpakai[] = "Peruntukan: <span class='filter-value'>" . htmlspecialchars($peruntukan) . "</span>";
    }

    // Gabungkan filter dengan separator " | "
    if (!empty($filter_terpakai)) {
        $filter_display = implode("<span class='filter-separator'> | </span>", $filter_terpakai);
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
            l.provinsi,
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

    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) == 0) {
        echo "<div class='alert alert-warning'>⚠️ Tidak ada data ditemukan.</div>";
        
        // Tampilkan filter yang digunakan meski data kosong
        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }
    } else {

        // Tampilkan filter yang digunakan di atas tabel
        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }

        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Lokasi</th>
                        <th>Nama Lokasi</th>
                        <th>Alamat Lokasi</th>
                        <th>Kab/Kota</th>
                        <th>Peruntukan</th>
                        <th>NO₂ (µg/m³)</th>
                        <th>SO₂ (µg/m³)</th>
                        <th>PM₂.₅ (µg/m³)</th>
                    </tr>
                </thead>
                <tbody>";

        $no = 1;
        while ($data = mysqli_fetch_assoc($query)) {
            echo "<tr>
                    <td>$no</td>
                    <td>".date('d-m-Y', strtotime($data['tanggal_pemantauan']))."</td>
                    <td>{$data['kode_lokasi']}</td>
                    <td>{$data['nama_lokasi']}</td>
                    <td>{$data['alamat_lokasi']}</td>
                    <td>{$data['kabupaten_kota']}</td>
                    <td>{$data['peruntukan']}</td>
                    <td>{$data['no2']}</td>
                    <td>{$data['so2']}</td>
                    <td>{$data['pm25']}</td>
                </tr>";
            $no++;
        }

        echo "</tbody></table></div>";

        echo "<div class='text-right' style='margin-top:15px;'>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=word' class='btn btn-primary btn-sm'>Export Word</a>
                <a href='grafik_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan' target='_blank' class='btn btn-info btn-sm'>Lihat Grafik Tren</a>
              </div>";
    }

} else {
    echo "<div class='alert alert-info'>
            Silakan pilih filter Kabupaten/Kota atau Peruntukan, lalu klik <b>Tampilkan</b>.
          </div>";
}
?>

</div>

<?php include "footer.php"; ?>