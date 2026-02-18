<style>
    .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-top: 0;
    }

    .main-image-container {
        max-height: 450px;
        overflow: hidden;
        margin-bottom: 20px;
        border-radius: 8px;
    }

    .main-image-container img {
        width: 100%;
        height: auto;
        object-fit: cover;
        display: block;
    }

    .news-content p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }
</style>

<?php
include ('header.php');
include ('koneksi.php');


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_berita = mysqli_real_escape_string($koneksi, $_GET['id']);
} else {
    header("Location: index.php");
    exit;
}


$qDetail = mysqli_query($koneksi, "
    SELECT b.*, u.nama_lengkap AS penulis 
    FROM berita b
    LEFT JOIN users u ON b.id_user = u.id_user
    WHERE b.id_berita = '$id_berita'
");

if (mysqli_num_rows($qDetail) == 0) {
    echo "<div class='container-main' style='padding-top: 50px;'>
            <h3>Berita tidak ditemukan.</h3>
            <p><a href='index.php'>&laquo; Kembali ke Beranda</a></p>
          </div>";
} else {

    $berita = mysqli_fetch_assoc($qDetail);

    
    $pathGambar = "uploads/" . $berita['gambar'];
    $gambar = (!empty($berita['gambar']) && file_exists($pathGambar))
                ? $pathGambar
                : "no-image.png";
?>

<div class="container-main" style="padding-top: 20px;">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <p><a href="index.php">&laquo; Kembali ke Beranda</a></p>

            <h1 class="page-title"><?= htmlspecialchars($berita['judul_berita']); ?></h1>

            <p class="text-muted">
                <small>
                    📅 Diposting: <?= date('d M Y', strtotime($berita['tanggal_posting'])); ?><br>
                    ✍️ Penulis: <?= htmlspecialchars($berita['penulis'] ?? 'Tidak diketahui'); ?>
                </small>
            </p>

            <hr>

            <div class="main-image-container">
                <img src="<?= htmlspecialchars($gambar); ?>" 
                     class="img-responsive center-block" 
                     alt="<?= htmlspecialchars($berita['judul_berita']); ?>">
            </div>

            <div class="news-content" style="margin-top: 30px; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($berita['isi_berita'])); ?>
            </div>

            <div style="margin-top: 50px;">
                <p><a href="index.php" class="btn btn-default">&laquo; Kembali</a></p>
            </div>

        </div>
    </div>
</div>

<?php
}

include ('footer.php');
?>
