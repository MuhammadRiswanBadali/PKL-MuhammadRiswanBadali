<?php
include "header-admin.php"; 
include "koneksi.php";


if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Session tidak ditemukan. Silakan login kembali!'); window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];


if (isset($_POST['add'])) {
    $judul_berita = mysqli_real_escape_string($koneksi, $_POST['judul_berita']);
    $isi_berita   = mysqli_real_escape_string($koneksi, $_POST['isi_berita']);
    $tanggal_posting = date('Y-m-d H:i:s');
    $gambar = "";


    if (!empty($_FILES['gambar']['name'])) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file  = $_FILES['gambar']['tmp_name'];
        $folder    = "uploads/";

     
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

      
        $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "_", $nama_file);
        $path = $folder . $nama_baru;

       
        if ($_FILES['gambar']['size'] > 2097152) {
            echo '<div class="alert alert-danger">❌ Ukuran gambar maksimal 2MB!</div>';
        } else {
            if (move_uploaded_file($tmp_file, $path)) {
                $gambar = $nama_baru;
            } else {
                echo '<div class="alert alert-danger">❌ Gagal mengupload gambar!</div>';
            }
        }
    }


    $insert = mysqli_query($koneksi,
        "INSERT INTO berita (judul_berita, isi_berita, tanggal_posting, id_user, gambar)
         VALUES ('$judul_berita', '$isi_berita', '$tanggal_posting', '$id_user', '$gambar')"
    ) or die(mysqli_error($koneksi));

    if ($insert) {
        echo '
        <div class="alert alert-success alert-dismissable">
            ✅ Berita berhasil ditambahkan!
        </div>
        <meta http-equiv="refresh" content="1; url=berita_data.php">';
    } else {
        echo '
        <div class="alert alert-danger alert-dismissable">
            ❌ Gagal menyimpan berita!
        </div>';
    }
}
?>

<h2>Tambah Berita Baru</h2>
<hr />

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">


    <div class="form-group">
        <label class="col-sm-3 control-label">Judul Berita</label>
        <div class="col-sm-6">
            <input type="text" name="judul_berita" class="form-control" placeholder="Masukkan judul berita" required>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">Isi Berita</label>
        <div class="col-sm-8">
            <textarea name="isi_berita" rows="8" class="form-control" placeholder="Tulis isi berita..." required></textarea>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">Upload Gambar</label>
        <div class="col-sm-5">
            <input type="file" name="gambar" accept="image/*" class="form-control">
            <small class="text-muted">Format: JPG, PNG, GIF (maks 2MB)</small>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="add" class="btn btn-sm btn-primary">Simpan</button>
            <button type="reset" class="btn btn-sm btn-warning">Reset</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="history.back()">Kembali</button>
        </div>
    </div>

</form>

<?php include "footer.php"; ?>
