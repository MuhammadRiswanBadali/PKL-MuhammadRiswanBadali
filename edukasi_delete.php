<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";


if (!isset($_GET['id_edukasi']) || empty($_GET['id_edukasi'])) {
    echo '<div class="alert alert-danger">⚠️ ID edukasi tidak ditemukan!</div>';
    exit;
}

$id_edukasi = mysqli_real_escape_string($koneksi, $_GET['id_edukasi']);


$q = mysqli_query($koneksi, "
    SELECT file_path, gambar, tipe_konten 
    FROM edukasi 
    WHERE id_edukasi='$id_edukasi'
") or die(mysqli_error($koneksi));

if (mysqli_num_rows($q) == 0) {
    echo '<div class="alert alert-warning">⚠️ Data edukasi tidak ditemukan.</div>';
    exit;
}

$data = mysqli_fetch_assoc($q);


if ($data['tipe_konten'] === 'file' && !empty($data['file_path'])) {
    $file_path = "uploads/edukasi/" . $data['file_path'];

    if (file_exists($file_path)) {
        unlink($file_path);
    }
}


if (!empty($data['gambar'])) {
    $cover_path = "uploads/cover_edukasi/" . $data['gambar'];

    if (file_exists($cover_path)) {
        unlink($cover_path);
    }
}


$delete = mysqli_query($koneksi, "
    DELETE FROM edukasi 
    WHERE id_edukasi='$id_edukasi'
") or die(mysqli_error($koneksi));

if ($delete) {
    echo '<div class="alert alert-success alert-dismissable">
            ✅ Data edukasi berhasil dihapus.
          </div>
          <meta http-equiv="refresh" content="1; url=edukasi_data.php">';
} else {
    echo '<div class="alert alert-danger alert-dismissable">
            ❌ Gagal menghapus data edukasi.
          </div>';
}
?>

<?php include "footer.php"; ?>
