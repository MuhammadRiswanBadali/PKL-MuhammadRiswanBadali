<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";


if (!isset($_GET['id_edukasi']) || empty($_GET['id_edukasi'])) {
    echo "<div class='alert alert-danger'>⚠️ ID edukasi tidak ditemukan!</div>";
    exit;
}

$id_edukasi = mysqli_real_escape_string($koneksi, $_GET['id_edukasi']);


$sql = mysqli_query($koneksi, "
    SELECT e.*, u.nama_lengkap AS penulis
    FROM edukasi e
    LEFT JOIN users u ON e.id_user = u.id_user
    WHERE e.id_edukasi = '$id_edukasi'
") or die(mysqli_error($koneksi));

if (mysqli_num_rows($sql) == 0) {
    echo '<div class="alert alert-danger">⚠️ Data edukasi tidak ditemukan!</div>';
    exit;
}

$row = mysqli_fetch_assoc($sql);


if (isset($_POST['save'])) {
    $judul_edukasi = mysqli_real_escape_string($koneksi, $_POST['judul_edukasi']);
    $isi_edukasi = mysqli_real_escape_string($koneksi, $_POST['isi_edukasi']);
    $tipe_konten = $_POST['tipe_konten'];


    $file_path    = $row['file_path'];
    $link_video   = $row['link_video'];
    $cover_gambar = $row['gambar'];

    
    if ($tipe_konten == "file") {

       
        if (!empty($_FILES['file_edukasi']['name'])) {

            $nama_file = $_FILES['file_edukasi']['name'];
            $tmp_file = $_FILES['file_edukasi']['tmp_name'];
            $folder = "uploads/edukasi/";

            if (!is_dir($folder)) mkdir($folder, 0777, true);

          
            $nama_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_file);
            $path = $folder . $nama_baru;

            if (move_uploaded_file($tmp_file, $path)) {
                
                if (!empty($row['file_path']) && file_exists("uploads/edukasi/" . $row['file_path'])) {
                    unlink("uploads/edukasi/" . $row['file_path']);
                }
                $file_path = $nama_baru;
            } else {
                echo "<div class='alert alert-danger'>❌ Gagal mengupload file edukasi!</div>";
            }
        }

        $link_video = "";
    }

    
    if (!empty($_FILES['cover_gambar']['name'])) {

        $nama_cover = $_FILES['cover_gambar']['name'];
        $tmp_cover = $_FILES['cover_gambar']['tmp_name'];
        $folder_cover = "uploads/cover_edukasi/";

        if (!is_dir($folder_cover)) mkdir($folder_cover, 0777, true);

        $nama_cover_baru = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_cover);
        $path_cover = $folder_cover . $nama_cover_baru;

        if (move_uploaded_file($tmp_cover, $path_cover)) {

           
            if (!empty($row['gambar']) && file_exists("uploads/cover_edukasi/" . $row['gambar'])) {
                unlink("uploads/cover_edukasi/" . $row['gambar']);
            }

            $cover_gambar = $nama_cover_baru;
        } else {
            echo "<div class='alert alert-danger'>❌ Gagal mengupload gambar cover!</div>";
        }
    }

   
    if ($tipe_konten == "video") {
        $link_video = mysqli_real_escape_string($koneksi, $_POST['link_video']);

       
        if (!empty($row['file_path']) && file_exists("uploads/edukasi/" . $row['file_path'])) {
            unlink("uploads/edukasi/" . $row['file_path']);
        }

        $file_path = "";
        
    }

    
    $update = mysqli_query($koneksi, "
        UPDATE edukasi SET
            judul_edukasi  = '$judul_edukasi',
            isi_edukasi    = '$isi_edukasi',
            tipe_konten    = '$tipe_konten',
            file_path      = '$file_path',
            link_video     = '$link_video',
            gambar         = '$cover_gambar'
        WHERE id_edukasi = '$id_edukasi'
    ") or die(mysqli_error($koneksi));

    if ($update) {
        echo "<div class='alert alert-success alert-dismissable'>
                ✅ Data edukasi berhasil diperbarui!
              </div>
              <meta http-equiv='refresh' content='1; url=edukasi_data.php'>";
    } else {
        echo "<div class='alert alert-danger alert-dismissable'>
                ❌ Gagal memperbarui data edukasi!
              </div>";
    }
}
?>

<h2>Edit Edukasi</h2>
<hr/>

<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

 
    <div class="form-group">
        <label class="col-sm-3 control-label">Judul Edukasi</label>
        <div class="col-sm-6">
            <input type="text" name="judul_edukasi" value="<?= htmlspecialchars($row['judul_edukasi']); ?>" class="form-control" required>
        </div>
    </div>

   
    <div class="form-group">
        <label class="col-sm-3 control-label">Deskripsi Edukasi</label>
        <div class="col-sm-8">
            <textarea name="isi_edukasi" rows="6" class="form-control" required><?= htmlspecialchars($row['isi_edukasi']); ?></textarea>
        </div>
    </div>

  
    <div class="form-group">
        <label class="col-sm-3 control-label">Jenis Konten</label>
        <div class="col-sm-3">
            <select name="tipe_konten" id="tipe_konten" class="form-control" onchange="toggleContentType()" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="file"  <?= ($row['tipe_konten']=='file') ? 'selected' : '' ?>>File</option>
                <option value="video" <?= ($row['tipe_konten']=='video') ? 'selected' : '' ?>>Video</option>
            </select>
        </div>
    </div>

 
    <div class="form-group" id="fileSection" style="display:none;">
        <label class="col-sm-3 control-label">File Edukasi</label>
        <div class="col-sm-5">
            <input type="file" name="file_edukasi" accept=".pdf,.doc,.docx,.ppt,.pptx" class="form-control">
            <small class="text-muted">Kosongkan jika tidak ingin mengganti.</small>

            <?php if (!empty($row['file_path'])) { ?>
                <p><a href="uploads/edukasi/<?= $row['file_path']; ?>" target="_blank">📄 Lihat File Lama</a></p>
            <?php } ?>
        </div>
    </div>

   
    <div class="form-group" id="coverSection" style="display:none;">
        <label class="col-sm-3 control-label">Gambar Cover</label>
        <div class="col-sm-5">
            <input type="file" name="cover_gambar" accept=".jpg,.jpeg,.png" class="form-control">
            <small class="text-muted">Opsional.</small>

            <?php if (!empty($row['gambar'])) { ?>
                <p><img src="uploads/cover_edukasi/<?= $row['gambar']; ?>" width="120" class="img-thumbnail"></p>
            <?php } ?>
        </div>
    </div>

   
    <div class="form-group" id="videoSection" style="display:none;">
        <label class="col-sm-3 control-label">Link Video YouTube</label>
        <div class="col-sm-6">
            <input type="url" name="link_video" value="<?= htmlspecialchars($row['link_video']); ?>" class="form-control" placeholder="https://www.youtube.com/...">
        </div>
    </div>

   
    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="save" class="btn btn-sm btn-primary">Simpan</button>
            <button type="button" class="btn btn-sm btn-danger" onclick="history.back()">Kembali</button>
        </div>
    </div>
</form>

<script>
function toggleContentType() {
    var tipe = document.getElementById("tipe_konten").value;
    document.getElementById("fileSection").style.display = (tipe === "file") ? "block" : "none";
    document.getElementById("coverSection").style.display = (tipe === "file") ? "block" : "none";
    document.getElementById("videoSection").style.display = (tipe === "video") ? "block" : "none";
}
window.onload = toggleContentType;
</script>

<?php include "footer.php"; ?>
