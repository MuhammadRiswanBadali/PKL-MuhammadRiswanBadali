<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {

    echo "<script>alert('Akses ditolak!'); window.location='login.php';</script>";
    exit;
}

if ($_SESSION['role'] === 'admin') {
    include "header-admin.php";
} else {
    include "header-petugas.php";
}

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";

$where = ($cari != "") ? "
    WHERE 
        l.nama_lokasi LIKE '%$cari%' OR 
        l.alamat_lokasi LIKE '%$cari%' OR
        p.level LIKE '%$cari%' OR
        p.periode_pemantauan LIKE '%$cari%' OR
        p.metode_pemantauan LIKE '%$cari%'
" : "";

$data_per_halaman = 20;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_aktif < 1) $halaman_aktif = 1;

$offset = ($halaman_aktif - 1) * $data_per_halaman;

$total_data_query = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM pemantauan_udara p
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    $where
");
$total_data = mysqli_fetch_assoc($total_data_query)['total'];

$total_halaman = ceil($total_data / $data_per_halaman);
?>

<style>
.container-fluid {
    width: 100%;
    margin: 0;
    padding: 10px 20px;
}
.table {
    width: 98%;
    margin: auto;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-top: 30px;
    font-weight: 600;
}
hr {
    border: 1px solid #ddd;
    width: 95%;
}
.form-toolbar {
    display: flex;
    justify-content: space-between;
    margin-left: 30px;
    margin-right: 30px;
    align-items: center;
}
.pagination-container {
    text-align: center;
    margin: 20px 0;
}
</style>

<h2>Data Pemantauan Udara</h2>
<hr/>


<div class="form-toolbar">

    <a href="pemantauan_udara_add.php?halaman=<?= $halaman_aktif; ?>" class="btn btn-success btn-sm">
        <span class="glyphicon glyphicon-plus"></span> Tambah Data
    </a>

    <!-- FORM SEARCH -->
    <form method="GET" action="" style="display:flex; gap:5px;">
        <input type="text" name="cari" class="form-control input-sm"
               placeholder="Cari lokasi / alamat ..."
               value="<?= $cari; ?>" style="width:250px;">

        <button type="submit" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-search"></span> Cari
        </button>

        <?php if ($cari != "") { ?>
            <a href="pemantauan_udara_data.php" class="btn btn-default btn-sm">Clear</a>
        <?php } ?>

        <input type="hidden" name="halaman" value="1">
    </form>

</div>

<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Nama Lokasi</th>
            <th>Alamat</th>
            <th>Level</th>
            <th>Periode</th>
            <th>Tanggal</th>
            <th>Durasi</th>
            <th>Metode</th>
            <th>SHU</th>
            <th>Tools</th>
        </tr>

        <?php

        $mySql = "
            SELECT *
            FROM (
                SELECT 
                    ROW_NUMBER() OVER (ORDER BY p.id_pemantauan ASC) AS nomor_asli,
                    p.id_pemantauan,
                    l.nama_lokasi,
                    l.alamat_lokasi,
                    p.level,
                    p.periode_pemantauan,
                    p.tanggal_pemantauan,
                    p.durasi_pemantauan,
                    p.metode_pemantauan,
                    p.shu
                FROM pemantauan_udara p
                JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
            ) AS x
            WHERE 
                1=1
                " . ($cari != "" ? "
                    AND (
                        x.nama_lokasi LIKE '%$cari%' OR
                        x.alamat_lokasi LIKE '%$cari%' OR
                        x.level LIKE '%$cari%' OR
                        x.periode_pemantauan LIKE '%$cari%' OR
                        x.metode_pemantauan LIKE '%$cari%'
                    )
                " : "") . "
            ORDER BY nomor_asli ASC
            LIMIT $offset, $data_per_halaman
        ";

        $myQry = mysqli_query($koneksi, $mySql);

        if (mysqli_num_rows($myQry) == 0) {
        ?>
            <tr>
                <td colspan="10" align="center"><b>Tidak ada data</b></td>
            </tr>
        <?php
        } else {
            while ($kolom = mysqli_fetch_assoc($myQry)) {
        ?>

        <tr>
            <td><?= $kolom['nomor_asli']; ?></td>
            <td><?= $kolom['nama_lokasi']; ?></td>
            <td><?= $kolom['alamat_lokasi']; ?></td>
            <td><?= $kolom['level']; ?></td>
            <td><?= $kolom['periode_pemantauan']; ?></td>
            <td><?= date('d-m-Y', strtotime($kolom['tanggal_pemantauan'])); ?></td>
            <td><?= $kolom['durasi_pemantauan']; ?></td>
            <td><?= $kolom['metode_pemantauan']; ?></td>
            <td><?= $kolom['shu']; ?></td>

            <td>

                <a href="pemantauan_udara_edit.php?id_pemantauan=<?= $kolom['id_pemantauan']; ?>&halaman=<?= $halaman_aktif; ?>"
                    class="btn btn-primary btn-sm" title="Edit">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>

                <a href="pemantauan_udara_delete.php?id_pemantauan=<?= $kolom['id_pemantauan']; ?>&halaman=<?= $halaman_aktif; ?>"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Yakin ingin menghapus data ini?');"
                    title="Hapus">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>

        <?php
            }
        }
        ?>
    </table>
</div>

<div class="pagination-container">
    <nav>
        <ul class="pagination">

            <li class="<?= ($halaman_aktif <= 1) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= $halaman_aktif - 1; ?>&cari=<?= $cari; ?>">&laquo; Previous</a>
            </li>

            <li class="active">
                <a href="#">
                    <?= $halaman_aktif; ?> dari <?= $total_halaman; ?>
                </a>
            </li>

            <li class="<?= ($halaman_aktif >= $total_halaman) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= $halaman_aktif + 1; ?>&cari=<?= $cari; ?>">Next &raquo;</a>
            </li>

        </ul>
    </nav>

    <p>Total Data: <b><?= $total_data; ?></b></p>
</div>

<?php include "footer.php"; ?>
