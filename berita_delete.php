<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";


if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini khusus Administrator.'); window.location='dashboard.php';</script>";
    exit;
}


if (!isset($_GET['id_berita'])) {
    echo '<div class="alert alert-danger">❌ ID berita tidak ditemukan!</div>';
    exit;
}

$id_berita = mysqli_real_escape_string($koneksi, $_GET['id_berita']);


$sql = mysqli_query($koneksi, "SELECT * FROM berita WHERE id_berita='$id_berita'") 
       or die(mysqli_error($koneksi));

if (mysqli_num_rows($sql) == 0) {
    echo '<div class="alert alert-danger">⚠️ Data berita tidak ditemukan!</div>';
    exit;
} else {
    $row = mysqli_fetch_assoc($sql);

 
    if (!empty($row['gambar'])) {
        $filePath = "uploads/" . $row['gambar'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

  
    $delete = mysqli_query($koneksi, "DELETE FROM berita WHERE id_berita='$id_berita'")
              or die(mysqli_error($koneksi));

    if ($delete) {
        echo '<div class="alert alert-success alert-dismissable">
                ✅ Berita berhasil dihapus!
              </div>
              <meta http-equiv="refresh" content="1; url=berita_data.php">';
    } else {
        echo '<div class="alert alert-danger alert-dismissable">
                ❌ Gagal menghapus berita!
              </div>';
    }
}
?>

<?php include "footer.php"; ?>
