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

if (!isset($_GET['id_pemantauan']) || $_GET['id_pemantauan'] == "") {
    echo "<div class='alert alert-danger alert-dismissable'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
            ⚠️ <b>Data yang akan dihapus tidak ditemukan.</b>
          </div>";
    echo "<meta http-equiv='refresh' content='1; url=pemantauan_udara_data.php?halaman={$current_halaman}'>";
    exit;
}

$id_pemantauan = $_GET['id_pemantauan'];

$deleteQuery = mysqli_query($koneksi, "DELETE FROM pemantauan_udara WHERE id_pemantauan='$id_pemantauan'")
    or die("<div class='alert alert-danger'>Error: " . mysqli_error($koneksi) . "</div>");

if ($deleteQuery) {
    echo "<div class='alert alert-success alert-dismissable'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
            ✅ <b>Data pemantauan udara berhasil dihapus.</b>
          </div>";
    echo "<meta http-equiv='refresh' content='1; url=pemantauan_udara_data.php?halaman={$current_halaman}'>";
} else {
    echo "<div class='alert alert-danger alert-dismissable'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
            ❌ <b>Gagal menghapus data. Silakan coba lagi.</b>
          </div>";
}
?>
