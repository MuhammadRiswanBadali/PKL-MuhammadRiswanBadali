<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";


if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Session tidak ditemukan. Silakan login kembali!'); window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];


if (isset($_POST['add'])) {
    $judul_edukasi   = mysqli_real_escape_string($koneksi, $_POST['judul_edukasi']);
    $isi_edukasi     = mysqli_real_escape_string($koneksi, $_POST['isi_edukasi']);
    $tanggal_posting = date('Y-m-d H:i:s');
    $tipe_konten     = $_POST['tipe_konten'];

    $file_path    = "";
    $cover_gambar = "";
    $link_video   = "";

   
    if ($tipe_konten === "file") {

        
        if (!empty($_FILES['file_edukasi']['name'])) {

            $folder = "uploads/edukasi/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $nama_file = $_FILES['file_edukasi']['name'];
            $tmp_file  = $_FILES['file_edukasi']['tmp_name'];

            $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_file);
            $path = $folder . $nama_baru;

            if (move_uploaded_file($tmp_file, $path)) {
                $file_path = $nama_baru;
            } else {
                echo '<div class="alert alert-danger">❌ Gagal mengupload file edukasi!</div>';
            }
        }

        
        if (!empty($_FILES['cover_gambar']['name'])) {

            $folder_cover = "uploads/cover_edukasi/";
            if (!is_dir($folder_cover)) mkdir($folder_cover, 0777, true);

            $nama_cover = $_FILES['cover_gambar']['name'];
            $tmp_cover  = $_FILES['cover_gambar']['tmp_name'];

            $nama_cover_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_cover);
            $path_cover = $folder_cover . $nama_cover_baru;

            if (move_uploaded_file($tmp_cover, $path_cover)) {
                $cover_gambar = $nama_cover_baru;
            } else {
                echo '<div class="alert alert-danger">❌ Gagal mengupload gambar cover!</div>';
            }
        }
    }

    
    if ($tipe_konten === "video") {
        $link_video = mysqli_real_escape_string($koneksi, $_POST['link_video']);
    }

    
    $insert = mysqli_query($koneksi,
        "INSERT INTO edukasi 
         (judul_edukasi, isi_edukasi, tanggal_posting, id_user, tipe_konten, file_path, link_video, gambar)
         VALUES 
         ('$judul_edukasi', '$isi_edukasi', '$tanggal_posting', '$id_user', '$tipe_konten', '$file_path', '$link_video', '$cover_gambar')"
    );

    if ($insert) {
        echo '<div class="alert alert-success alert-dismissable">
                ✅ Edukasi berhasil ditambahkan!
              </div>
              <meta http-equiv="refresh" content="1; url=edukasi_data.php">';
    } else {
        echo '<div class="alert alert-danger alert-dismissable">
                ❌ Gagal menyimpan edukasi!
              </div>';
    }
}
?>

<h2>Tambah Edukasi Baru</h2>
<hr />

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">


    <div class="form-group">
        <label class="col-sm-3 control-label">Judul Edukasi</label>
        <div class="col-sm-6">
            <input type="text" name="judul_edukasi" class="form-control" required>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-3 control-label">Deskripsi Edukasi</label>
        <div class="col-sm-8">
            <textarea name="isi_edukasi" rows="6" class="form-control" required></textarea>
        </div>
    </div>

   
    <div class="form-group">
        <label class="col-sm-3 control-label">Jenis Konten</label>
        <div class="col-sm-3">
            <select name="tipe_konten" id="tipe_konten" class="form-control" onchange="toggleContentType()" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="file">File (PDF/DOC/PPT)</option>
                <option value="video">Video (YouTube)</option>
            </select>
        </div>
    </div>

   
    <div class="form-group" id="fileSection" style="display:none;">
        <label class="col-sm-3 control-label">Upload File</label>
        <div class="col-sm-5">
            <input type="file" name="file_edukasi" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx">
        </div>
    </div>

  
    <div class="form-group" id="coverSection" style="display:none;">
        <label class="col-sm-3 control-label">Gambar Cover</label>
        <div class="col-sm-5">
            <input type="file" name="cover_gambar" class="form-control" accept="image/*">
            <small class="text-muted">Opsional, tampil di halaman utama</small>
        </div>
    </div>


    <div class="form-group" id="videoSection" style="display:none;">
        <label class="col-sm-3 control-label">Link Video</label>
        <div class="col-sm-6">
            <input type="url" name="link_video" class="form-control" placeholder="https://youtube.com/...">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-6">
            <button type="submit" name="add" class="btn btn-primary btn-sm">Simpan</button>
            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
            <button type="button" class="btn btn-danger btn-sm" onclick="history.back()">Kembali</button>
        </div>
    </div>
</form>

<script>
function toggleContentType() {
    var tipe = document.getElementById("tipe_konten").value;

    document.getElementById("fileSection").style.display = tipe === "file" ? "block" : "none";
    document.getElementById("coverSection").style.display = tipe === "file" ? "block" : "none";
    document.getElementById("videoSection").style.display = tipe === "video" ? "block" : "none";
}
</script>

<?php include "footer.php"; ?>
