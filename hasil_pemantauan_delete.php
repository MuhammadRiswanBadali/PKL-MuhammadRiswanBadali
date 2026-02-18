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

$current_halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($current_halaman < 1) $current_halaman = 1;


if (!isset($_GET['id_hasil']) || !is_numeric($_GET['id_hasil'])) {
    echo "<div class='alert alert-danger'>
            ⚠️ ID hasil pemantauan tidak valid!
          </div>";
    exit;
}

$id_hasil = (int) $_GET['id_hasil'];


$cek = mysqli_query($koneksi, "SELECT id_hasil FROM hasil_pemantauan WHERE id_hasil = $id_hasil");

if (mysqli_num_rows($cek) == 0) {
    echo "<div class='alert alert-warning'>
            ⚠️ Data yang ingin dihapus tidak ditemukan atau sudah dihapus sebelumnya.
          </div>
          <meta http-equiv='refresh' content='1; url=hasil_pemantauan_data.php?halaman=$current_halaman'>";
    exit;
}

$delete = mysqli_query($koneksi, "DELETE FROM hasil_pemantauan WHERE id_hasil = $id_hasil");

if ($delete) {
    echo "<div class='alert alert-success alert-dismissable'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            ✅ Data hasil pemantauan berhasil dihapus.
          </div>
          <meta http-equiv='refresh' content='1; url=hasil_pemantauan_data.php?halaman=$current_halaman'>";
} else {
    echo "<div class='alert alert-danger alert-dismissable'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            ❌ Gagal menghapus data hasil pemantauan.
          </div>";
}
?>
