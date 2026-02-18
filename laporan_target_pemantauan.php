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
</style>

<div class="container-fluid">
    <h2 align="center" style=font-weight:bold>Laporan Target Pemantauan Kualitas Udara</h2>
    <hr>

    <div class="info-box">
        <b>Keterangan Target:</b><br>
        <b>TERCAPAI</b> = ≥ 8 kali pemantauan / tahun <br>
        <b>BELUM TERCAPAI</b> = < 8 kali pemantauan / tahun
    </div>

    <!-- FILTER TAHUN -->
    <form method="GET">
        <div class="row">
            <div class="col-sm-3">
                <label>Pilih Tahun Pemantauan</label>
                <select name="tahun" class="form-control" required>
                    <option value="">-- Pilih Tahun --</option>
                    <?php
                    $tahunQ = mysqli_query($koneksi, "
                        SELECT DISTINCT YEAR(tanggal_pemantauan) AS tahun 
                        FROM pemantauan_udara 
                        ORDER BY tahun DESC
                    ");

                    while ($row = mysqli_fetch_assoc($tahunQ)) {
                        $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $row['tahun']) ? 'selected' : '';
                        echo "<option value='$row[tahun]' $selected>$row[tahun]</option>";
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
                <a href="laporan_target_pemantauan.php" class="btn btn-default btn-block">
                    <span class="glyphicon glyphicon-refresh"></span> Hapus
                </a>
            </div>
        </div>
    </form>

    <hr>

    <?php
    if (!isset($_GET['tahun']) || $_GET['tahun'] == '') {

        echo "<div class='alert alert-info'>
                Silakan pilih <b>tahun</b> terlebih dahulu untuk menampilkan laporan.
              </div>";

    } else {

        $tahun = $_GET['tahun'];

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


        $query = mysqli_query($koneksi, $sql);

        if (mysqli_num_rows($query) == 0) {

            echo "<div class='alert alert-warning'>
                    Tidak ada data pemantauan pada tahun <b>$tahun</b>.
                  </div>";

        } else {

            echo "<h4>Tahun Pemantauan : <b>$tahun</b></h4>";

            echo "<div class='table-responsive'>
            <table class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kabupaten / Kota</th>
                        <th>Total Pemantauan</th>
                        <th>Target</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

            $no = 1;

            while ($data = mysqli_fetch_assoc($query)) {

                $total = $data['total_pemantauan'];

                if ($total >= 8) {
                    $status = "<span style='color:green;font-weight:bold;'>TERCAPAI</span>";
                } else {
                    $status = "<span style='color:red;font-weight:bold;'>BELUM TERCAPAI</span>";
                }

                echo "<tr>
                        <td>$no</td>
                        <td>{$data['kabupaten_kota']}</td>
                        <td>$total</td>
                        <td>8 Kali / Tahun</td>
                        <td>$status</td>
                      </tr>";

                $no++;
            }

            echo "</tbody></table></div>";

            echo "<div class='text-right' style='margin-top:15px;'>
                        <a href='export_target_pemantauan.php?tahun=$tahun&type=pdf'class='btn btn-danger btn-sm'>Export PDF</a>
                        <a href='export_target_pemantauan.php?tahun=$tahun&type=excel'class='btn btn-success btn-sm'>Export Excel</a>
                        <a href='export_target_pemantauan.php?tahun=$tahun&type=word' class='btn btn-primary btn-sm'>Export Word</a>
                    </div>";
        }
    }
    ?>

</div>

<?php include "footer.php"; ?>
