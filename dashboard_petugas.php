<?php
include "header-petugas.php";
include "sessionlogin_petugas.php";
include "koneksi.php";


if ($_SESSION['role'] !== 'petugas') {
    header("Location: dashboard.php");
    exit;
}

$id_user = $_SESSION['id_user']; 


$total_lokasi = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM lokasi_pemantauan"))['total'];

$total_pemantauan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pemantauan_udara"))['total'];

$total_hasil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM hasil_pemantauan"))['total'];

?>

<style>
body {
    background-color: #f5f5f5;
}

.container-fluid {
    width: 100%;
    margin: 0;
    padding: 10px 20px;
}

h2 {
    text-align: center;
    font-weight: 600;
    margin-top: 25px;
}

hr {
    width: 95%;
    border: 1px solid #ccc;
}

.dashboard-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    margin-top: 40px;
}

.card-box {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    text-align: center;
    width: 270px;
    padding: 25px 15px;
    transition: transform 0.25s ease;
}
.card-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.25);
}
.card-icon {
    font-size: 42px;
    margin-bottom: 12px;
}
.card-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}
.card-count {
    font-size: 32px;
    color: #007bff;
    font-weight: bold;
}

footer {
    margin-top: 20px;
    background-color: #222;
    color: #ddd;
    padding: 15px 0;
    text-align: center;
}


.edukasi-preview {
    margin-top: 60px;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

.edukasi-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.edukasi-card {
    width: 280px;
    background: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    text-align: center;
}

.edukasi-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.edukasi-card h4 {
    padding: 10px;
    font-size: 16px;
    font-weight: 600;
}
</style>

<div class="container-fluid">
    <h2>Dashboard Petugas</h2>
    <hr/>

    <div class="dashboard-cards">

       
        <div class="card-box">
            <div class="card-icon text-info">📍</div>
            <div class="card-title">Lokasi Pemantauan</div>
            <div class="card-count"><?php echo $total_lokasi; ?></div>
            <a href="lokasi_pemantauan_data.php" class="btn btn-sm btn-info" style="margin-top:10px;">Lihat Lokasi</a>
        </div>

       
        <div class="card-box">
            <div class="card-icon text-info">🌫️</div>
            <div class="card-title">Pemantauan Udara</div>
            <div class="card-count"><?php echo $total_pemantauan; ?></div>
            <a href="pemantauan_udara_data.php" class="btn btn-sm btn-info" style="margin-top:10px;">Lihat Data</a>
        </div>

      
        <div class="card-box">
            <div class="card-icon text-info">📈</div>
            <div class="card-title">Hasil Pemantauan</div>
            <div class="card-count"><?php echo $total_hasil; ?></div>
            <a href="hasil_pemantauan_data.php" class="btn btn-sm btn-info" style="margin-top:10px;">Lihat Hasil</a>
        </div>

    </div>

</div>

<?php include "footer.php"; ?>
