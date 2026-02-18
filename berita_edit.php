<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";


if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Administrator.'); window.location='dashboard.php';</script>";
    exit;
}


if (!isset($_GET['id_berita'])) {
    echo '<div class="alert alert-danger">❌ ID berita tidak ditemukan!</div>';
    exit;
}

$id_berita = mysqli_real_escape_string($koneksi, $_GET['id_berita']);


$sql = mysqli_query($koneksi, "SELECT * FROM berita WHERE id_berita='$id_berita'") or die(mysqli_error($koneksi));
if (mysqli_num_rows($sql) == 0) {
    echo '<div class="alert alert-danger">⚠️ Data berita tidak ditemukan!</div>';
    exit;
}
$row = mysqli_fetch_assoc($sql);


if (isset($_POST['save'])) {
    $judul_berita = mysqli_real_escape_string($koneksi, $_POST['judul_berita']);
    $isi_berita = mysqli_real_escape_string($koneksi, $_POST['isi_berita']);
    $tanggal_posting = date('Y-m-d H:i:s');
    $gambar_baru = $row['gambar']; 

 
    if (!empty($_FILES['gambar']['name'])) {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $folder = "uploads/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

    
        if (!empty($row['gambar']) && file_exists($folder . $row['gambar'])) {
            unlink($folder . $row['gambar']);
        }

      
        $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_file);
        $path = $folder . $nama_baru;

        if (move_uploaded_file($tmp_file, $path)) {
            $gambar_baru = $nama_baru;
        } else {
            echo '<div class="alert alert-danger">❌ Gagal mengupload gambar baru!</div>';
        }
    }

   
    $update = mysqli_query($koneksi, "
        UPDATE berita SET 
            judul_berita='$judul_berita',
            isi_berita='$isi_berita',
            tanggal_posting='$tanggal_posting',
            gambar='$gambar_baru'
        WHERE id_berita='$id_berita'
    ") or die(mysqli_error($koneksi));

    if ($update) {
        echo '<div class="alert alert-success alert-dismissable">
                ✅ Berita berhasil diperbarui!
              </div>
              <meta http-equiv="refresh" content="1; url=berita_data.php">';
    } else {
        echo '<div class="alert alert-danger alert-dismissable">
                ❌ Gagal memperbarui berita!
              </div>';
    }
}
?>

<h2>Edit Berita</h2>
<hr />

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">


    <div class="form-group">
        <label class="col-sm-3 control-label">Judul Berita</label>
        <div class="col-sm-6">
            <input type="text" name="judul_berita" value="<?php echo htmlspecialchars($row['judul_berita']); ?>" class="form-control" required>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">Isi Berita</label>
        <div class="col-sm-8">
            <textarea name="isi_berita" rows="8" class="form-control" required><?php echo htmlspecialchars($row['isi_berita']); ?></textarea>
        </div>
    </div>

 
    <div class="form-group">
        <label class="col-sm-3 control-label">Gambar Saat Ini</label>
        <div class="col-sm-5">
            <?php if (!empty($row['gambar'])) { ?>
                <img src="uploads/<?php echo $row['gambar']; ?>" alt="Gambar Berita" style="max-width: 200px; border-radius: 5px;">
            <?php } else { ?>
                <p><em>Tidak ada gambar</em></p>
            <?php } ?>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">Ganti Gambar</label>
        <div class="col-sm-5">
            <input type="file" name="gambar" accept="image/*" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="save" class="btn btn-sm btn-primary">Simpan</button>
            <button type="reset" class="btn btn-sm btn-warning">Reset</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="history.back()">Kembali</button>
        </div>
    </div>

</form>

<?php include "footer.php"; ?>
