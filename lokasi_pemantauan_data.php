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


// ========== SEARCH ==========
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";
$where = ($cari != "") ? 
         "WHERE kode_lokasi LIKE '%$cari%' 
          OR nama_lokasi LIKE '%$cari%' 
          OR alamat_lokasi LIKE '%$cari%'" 
          : "";


// ========== PAGINATION ==========
$data_per_halaman = 20;

$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_aktif < 1) $halaman_aktif = 1;

$offset = ($halaman_aktif - 1) * $data_per_halaman;


// ========== TOTAL DATA ==========
$total_data_result = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM lokasi_pemantauan
    $where
");
$total_data = mysqli_fetch_assoc($total_data_result)['total'];

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
    align-items: center;
    margin-left: 30px;
    margin-right: 30px;
}
.pagination-container {
    margin: 20px 30px;
    text-align: center;
}
</style>

<h2>Data Lokasi Pemantauan</h2>
<hr/>

<!-- ============================= -->
<!-- AREA TOMBOL TAMBAH + SEARCH -->
<!-- ============================= -->
<div class="form-toolbar">

    <!-- TOMBOL TAMBAH DATA -->
    <a href="lokasi_pemantauan_add.php?halaman=<?= $halaman_aktif; ?>" 
       class="btn btn-success btn-sm">
        <span class="glyphicon glyphicon-plus"></span> Tambah Data
    </a>

    <!-- FORM SEARCH -->
    <form method="GET" action="" style="display:flex; gap:5px;">
        <input type="text" name="cari" 
               class="form-control input-sm" 
               placeholder="Cari kode lokasi / alamat..." 
               value="<?= $cari; ?>"
               style="width:250px;">

        <button type="submit" class="btn btn-primary btn-sm">
            <span class="glyphicon glyphicon-search"></span> Cari
        </button>

        <?php if ($cari != "") { ?>
            <a href="lokasi_pemantauan_data.php" class="btn btn-default btn-sm">
                Clear
            </a>
        <?php } ?>

        <input type="hidden" name="halaman" value="1">
    </form>

</div>

<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Kode Lokasi</th>
            <th>Nama Lokasi</th>
            <th>Alamat</th>
            <th>Kabupaten/Kota</th>
            <th>Provinsi</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Peruntukan</th>
            <th>Tools</th>
        </tr>

        <?php

        // ======== QUERY UTAMA MENGGUNAKAN ROW_NUMBER() =========
        $query = mysqli_query($koneksi, "
            SELECT * FROM (
                SELECT 
                    ROW_NUMBER() OVER (ORDER BY id_lokasi ASC) AS nomor_asli,
                    lokasi_pemantauan.*
                FROM lokasi_pemantauan
            ) AS x
            $where
            ORDER BY nomor_asli ASC
            LIMIT $offset, $data_per_halaman
        ");

        while ($data = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $data['nomor_asli']; ?></td>
            <td><?= $data['kode_lokasi']; ?></td>
            <td><?= $data['nama_lokasi']; ?></td>
            <td><?= $data['alamat_lokasi']; ?></td>
            <td><?= $data['kabupaten_kota']; ?></td>
            <td><?= $data['provinsi']; ?></td>
            <td><?= $data['latitude']; ?></td>
            <td><?= $data['longitude']; ?></td>
            <td><?= $data['peruntukan']; ?></td>
            <td>
                <a href="lokasi_pemantauan_edit.php?id_lokasi=<?= $data['id_lokasi']; ?>&halaman=<?= $halaman_aktif; ?>"
                    class="btn btn-primary btn-sm"
                    title="Edit">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>

                <a href="lokasi_pemantauan_delete.php?id_lokasi=<?= $data['id_lokasi']; ?>&halaman=<?= $halaman_aktif; ?>" 
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Yakin ingin menghapus data ini?');"
                    title="Hapus">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>

        <?php } ?>

    </table>
</div>

<div class="pagination-container">
    <nav>
        <ul class="pagination">

            <li class="<?= ($halaman_aktif <= 1) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= ($halaman_aktif > 1) ? ($halaman_aktif - 1) : 1; ?>&cari=<?= $cari; ?>">
                    &laquo; Previous
                </a>
            </li>

            <li class="active">
                <a href="#">
                    <?= $halaman_aktif; ?> dari <?= $total_halaman; ?>
                </a>
            </li>

            <li class="<?= ($halaman_aktif >= $total_halaman) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= ($halaman_aktif < $total_halaman) ? ($halaman_aktif + 1) : $total_halaman; ?>&cari=<?= $cari; ?>">
                    Next &raquo;
                </a>
            </li>
        </ul>
    </nav>

    <p>Total Data: <?= $total_data; ?></p>
</div>

<?php include "footer.php"; ?>
