<?php
include "header.php";
include "koneksi.php";
?>

<style>

body {
    background-color: #f7f7f7;
    font-family: Arial, sans-serif;
}
.container-main {
    width: 95%;
    margin: 0 auto;
    padding: 10px 0;
}


.section-title {
    font-size: 22px;
    font-weight: bold;
    margin-top: 30px;
    color: #2c3e50;
    border-left: 5px solid #28a745;
    padding-left: 10px;
}


.scroll-horizontal {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    padding: 10px 0;
    scroll-behavior: smooth;
}
.scroll-horizontal::-webkit-scrollbar {
    height: 8px;
}
.scroll-horizontal::-webkit-scrollbar-thumb {
    background: #bbb;
    border-radius: 4px;
}


.card {
    background: #fff;
    border-radius: 8px;
    min-width: 300px;
    max-width: 300px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    padding: 12px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.card:hover {
    transform: scale(1.02);
}
.card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 6px;
}
.card h4 {
    font-size: 16px;
    margin-top: 10px;
    color: #333;
}
.card p {
    font-size: 14px;
    color: #666;
}


.saran-box {
    background: #fff;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.saran-box small {
    color: #777;
}
</style>

<div class="container-main">

 
    <h3 class="section-title">Berita Terbaru</h3>
    <div class="scroll-horizontal">
        <?php
        $qBerita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_posting DESC LIMIT 10");
        if (mysqli_num_rows($qBerita) == 0) {
            echo "<p style='padding:10px;'>Belum ada berita.</p>";
        } else {
            while ($b = mysqli_fetch_assoc($qBerita)) {
                $gambar = !empty($b['gambar']) && file_exists("uploads/".$b['gambar']) ? "uploads/".$b['gambar'] : "no-image.png";
                echo "
                <div class='card'>
                    <img src='$gambar'>
                    <h4>".htmlspecialchars($b['judul_berita'])."</h4>
                    <p>".substr(strip_tags($b['isi_berita']), 0, 100)."...</p>
                    <small>".date('d M Y', strtotime($b['tanggal_posting']))."</small>
                </div>";
            }
        }
        ?>
    </div>


    <h3 class="section-title">Edukasi Lingkungan</h3>
    <div class="scroll-horizontal">
        <?php
        $qEdukasi = mysqli_query($koneksi, "SELECT * FROM edukasi ORDER BY tanggal_posting DESC LIMIT 10");
        if (mysqli_num_rows($qEdukasi) == 0) {
            echo "<p style='padding:10px;'>Belum ada materi edukasi.</p>";
        } else {
            while ($e = mysqli_fetch_assoc($qEdukasi)) {
                $gambar = !empty($e['gambar']) && file_exists("uploads/".$e['gambar']) ? "uploads/".$e['gambar'] : "no-image.png";
                echo "
                <div class='card'>
                    <img src='$gambar'>
                    <h4>".htmlspecialchars($e['judul_edukasi'])."</h4>
                    <p>".substr(strip_tags($e['isi_edukasi']), 0, 100)."...</p>
                    <small>".date('d M Y', strtotime($e['tanggal_posting']))."</small>
                </div>";
            }
        }
        ?>
    </div>


    <h3 class="section-title">Saran dari Pengunjung</h3>
    <?php
    $qSaran = mysqli_query($koneksi, "SELECT * FROM saran ORDER BY tanggal_saran DESC");
    if (mysqli_num_rows($qSaran) == 0) {
        echo "<p style='padding:10px;'>Belum ada saran yang dikirim.</p>";
    } else {
        while ($s = mysqli_fetch_assoc($qSaran)) {
            echo "
            <div class='saran-box'>
                <p>".nl2br(htmlspecialchars($s['isi_saran']))."</p>
                <small>🧍 ".htmlspecialchars($s['nama_pengirim'])." • 📅 ".date('d M Y', strtotime($s['tanggal_saran']))."</small>
            </div>";
        }
    }
    ?>

 
    <div class="saran-box">
        <h4>Kirim Saran Anda</h4>
        <form action="" method="post">
            <input type="text" name="nama_pengirim" class="form-control" placeholder="Nama Anda" required><br>
            <textarea name="isi_saran" rows="4" class="form-control" placeholder="Tulis saran Anda di sini..." required></textarea><br>
            <button type="submit" name="kirimSaran" class="btn btn-success btn-sm">Kirim Saran</button>
        </form>
    </div>

    <?php
    if (isset($_POST['kirimSaran'])) {
        $nama_pengirim = mysqli_real_escape_string($koneksi, $_POST['nama_pengirim']);
        $isi_saran = mysqli_real_escape_string($koneksi, $_POST['isi_saran']);
        $tanggal_saran = date('Y-m-d H:i:s');

        mysqli_query($koneksi, "INSERT INTO saran (nama_pengirim, isi_saran, tanggal_saran) 
                                VALUES ('$nama_pengirim', '$isi_saran', '$tanggal_saran')");

        echo "<meta http-equiv='refresh' content='0; url=beranda.php'>";
    }
    ?>

</div>

<?php include "footer.php"; ?>
