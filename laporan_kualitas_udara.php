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
}
.table td, .table th {
    text-align: center;
    vertical-align: middle;
}
.filter-row {
    margin-bottom: 10px;
}
.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
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
    <h2 align="center" style=font-weight:bold>Laporan Rata-Rata Polutan Udara per Kabupaten/Kota</h2>
    <hr/>
   
    <form method="get" action="">
        <div class="row filter-row">
            
         
            <div class="col-sm-3">
                <label>Filter Periode</label>
                <select name="tahun" class="form-control">
                    <option value="">-- Semua Tahun --</option>
                    <?php
                    $tahunQuery = mysqli_query($koneksi, "
                        SELECT DISTINCT YEAR(tanggal_pemantauan) AS tahun 
                        FROM pemantauan_udara 
                        ORDER BY tahun DESC
                    ");
                    while ($row = mysqli_fetch_assoc($tahunQuery)) {
                        $selected = (@$_GET['tahun'] == $row['tahun']) ? 'selected' : '';
                        echo "<option value='{$row['tahun']}' $selected>{$row['tahun']}</option>";
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
                    <a href="laporan_kualitas_udara.php" class="btn btn-default btn-block">
                        <span class="glyphicon glyphicon-refresh"></span> Hapus
                    </a>
                </div>

        </div>
    </form>
    <hr/>

    <?php

    if (isset($_GET['tahun']) || isset($_GET['kabupaten'])) {

        $tahun = $_GET['tahun'] ?? '';
        $kabupaten = $_GET['kabupaten'] ?? '';

        // ========= TAMPILKAN FILTER YANG DIGUNAKAN =========
        $filter_terpakai = [];
        $filter_display = "";

        if (!empty($tahun)) {
            $filter_terpakai[] = "Tahun: <span class='filter-value'>" . htmlspecialchars($tahun) . "</span>";
        }

        if (!empty($kabupaten)) {
            $filter_terpakai[] = "Kabupaten/Kota: <span class='filter-value'>" . htmlspecialchars($kabupaten) . "</span>";
        }

        // Gabungkan filter dengan separator " | "
        if (!empty($filter_terpakai)) {
            $filter_display = implode("<span class='filter-separator'> | </span>", $filter_terpakai);
        }
       
        $filter = "WHERE 1=1 ";
        if (!empty($tahun)) $filter .= "AND YEAR(p.tanggal_pemantauan) = '$tahun' ";
        if (!empty($kabupaten)) $filter .= "AND l.kabupaten_kota = '$kabupaten' ";

       
        $sql = "
            SELECT 
                l.kabupaten_kota,
                COUNT(h.id_pemantauan) AS jumlah_data,
                ROUND(AVG(h.no2),2) AS rata_no2,
                ROUND(AVG(h.so2),2) AS rata_so2,
                ROUND(AVG(h.pm25),2) AS rata_pm25
            FROM hasil_pemantauan h
            JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
            JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
            $filter
            GROUP BY l.kabupaten_kota
            ORDER BY l.kabupaten_kota ASC
        ";

        $query = mysqli_query($koneksi, $sql);

        if (mysqli_num_rows($query) == 0) {
            echo "<div class='alert alert-warning'>⚠️ Tidak ada data untuk filter yang dipilih.</div>";
            
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
                            <th>Kabupaten / Kota</th>
                            <th>Jumlah Data</th>
                            <th>Rata-rata NO₂ (µg/m³)</th>
                            <th>Rata-rata SO₂ (µg/m³)</th>
                            <th>Rata-rata PM₂.₅ (µg/m³)</th>
                          
                        </tr>
                    </thead>
                    <tbody>";

            $no = 1;
            while ($data = mysqli_fetch_assoc($query)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$data['kabupaten_kota']}</td>
                        <td>{$data['jumlah_data']}</td>
                        <td>{$data['rata_no2']}</td>
                        <td>{$data['rata_so2']}</td>
                        <td>{$data['rata_pm25']}</td>
                    </tr>";
                $no++;
            }

            echo "</tbody></table></div>";

            echo "<div class='text-right' style='margin-top:15px;'>
                    <a href='export_laporan_kualitas_udara.php?tahun=$tahun&kabupaten=$kabupaten&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
                    <a href='export_laporan_kualitas_udara.php?tahun=$tahun&kabupaten=$kabupaten&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
                    <a href='export_laporan_kualitas_udara.php?tahun=$tahun&kabupaten=$kabupaten&type=word' class='btn btn-primary btn-sm'>Export Word</a>
                  </div>";
        }

    } else {
        echo "<div class='alert alert-info'>
                <b>Pilih Tahun</b> pada Filter Periode, atau pilih <b>Semua Tahun</b> jika ingin melihat semua data, kemudian klik tombol <b>Tampilkan</b>.
              </div>";
    }
    ?>
</div>

<?php include "footer.php"; ?>