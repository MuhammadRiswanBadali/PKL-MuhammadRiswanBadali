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


$current_halaman = isset($_GET['halaman']) ? $_GET['halaman'] : 1;

if (!isset($_GET['id_lokasi']) || empty($_GET['id_lokasi'])) {
?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ⚠️ Data yang akan dihapus tidak ditemukan!
    </div>
<?php
    exit;
}

$id_lokasi = $_GET['id_lokasi'];

$cek = mysqli_query($koneksi, "SELECT * FROM lokasi_pemantauan WHERE id_lokasi='$id_lokasi'");
if (mysqli_num_rows($cek) == 0) {
?>
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ⚠️ Data lokasi tidak ditemukan di database.
    </div>
<?php
    exit;
}


$delete = mysqli_query($koneksi, "DELETE FROM lokasi_pemantauan WHERE id_lokasi='$id_lokasi'") 
          or die("Error hapus data: " . mysqli_error($koneksi));

if ($delete) {
?>
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ✅ Data lokasi berhasil dihapus.
    </div>

    <meta http-equiv='refresh' content='1; url=lokasi_pemantauan_data.php?halaman=<?= $current_halaman; ?>'>
<?php
} else {
?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ❌ Gagal menghapus data lokasi. Silakan coba lagi.
    </div>
<?php
}
?>
