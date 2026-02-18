<?php
include('koneksi.php');
include('library.php');
include('sessionlogin.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard_petugas.php");
    exit;
}

// Cek apakah user yang login adalah protected admin
$current_user_id = $_SESSION['id_user'];
$user_query = mysqli_query($koneksi, 
    "SELECT is_protected FROM users WHERE id_user='$current_user_id'");
$current_user = mysqli_fetch_assoc($user_query);
$is_protected_admin = (isset($current_user['is_protected']) && $current_user['is_protected'] == 1);
?>

<html lang="en">
<head>
    <title>Admin | Sistem Pemantauan Kualitas Udara DLH</title>
    <link href="css/site.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <style>
        body, html {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .navbar {
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .navbar-inverse .navbar-nav > li > a {
            color: #fff;
        }

        .navbar-inverse .navbar-nav > li > a:hover {
            background-color: #337ab7;
            color: #fff;
        }

        .navbar-nav > li:hover > .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            background-color: #222;
            border-radius: 0;
            border: none;
        }

        .dropdown-menu > li > a {
            color: #fff;
            padding: 8px 20px;
        }

        .dropdown-menu > li > a:hover {
            background-color: #337ab7;
            color: #fff;
        }

        .navbar-btn-small {
            background-color: #5cb85c;
            color: #fff !important;
            padding: 6px 12px;
            border-radius: 4px;
            margin-top: 2px;
            margin-right: 5px;
            font-size: 13px;
            transition: background-color 0.3s ease;
        }

        .navbar-btn-small:hover {
            background-color: #4cae4c;
            text-decoration: none;
        }
        
        .navbar-btn-small.disabled {
            background-color: #6c757d;
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .navbar-btn-small.disabled:hover {
            background-color: #6c757d;
            text-decoration: none;
        }

        .logout-btn {
            background-color: #d9534f;
        }

        .logout-btn:hover {
            background-color: #c9302c;
        }

        .navbar-right .btn-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            margin-right: 15px;
        }

        .logout-info {
            color: #fff;
            font-size: 14px;
            margin-right: 8px;
        }
        
        .super-admin-badge {
            background-color: #007bff;
            color: white;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
            vertical-align: middle;
        }

        .container-fluid, .content-fluid {
            width: 100%;
            margin: 0;
            padding: 10px 20px;
        }

        .table-responsive {
            width: 100%;
        }

        h2 {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="content-fluid">
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">DLH Admin Panel</a>
                    </div>

                    <ul class="nav navbar-nav">
                        <li><a href="dashboard.php">Dashboard</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Kelola Data <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="lokasi_pemantauan_data.php">Data Lokasi Pemantauan</a></li>
                                <li><a href="pemantauan_udara_data.php">Data Pemantauan Udara</a></li>
                                <li><a href="hasil_pemantauan_data.php">Data Hasil Pemantauan</a></li>
                                <li><a href="daftar_user.php">Daftar User</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Report <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="laporan_hasil_pemeriksaan_udara.php">Laporan hasil Pemeriksaan Udara</a></li>
                                <li><a href="laporan_tren_kualitas_udara.php">Laporan Tren Kualitas Udara</a></li>
                                <li><a href="laporan_pelanggaran_baku_mutu.php">Laporan Pelanggaran Baku Mutu</a></li>
                                <li><a href="laporan_target_pemantauan.php">Laporan Target Pemantauan</a></li>
                                <li><a href="laporan_kualitas_udara.php">Laporan Rata-Rata Kualitas Udara</a></li>
                            </ul>
                        </li>

                        <li><a href="berita_data.php">Berita</a></li>
                        <li><a href="edukasi_data.php">Edukasi</a></li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <div class="btn-group">
                            <span class="logout-info">
                                👤 <?php echo htmlspecialchars($_SESSION['nama']); ?> (Admin)
                                <?php if ($is_protected_admin): ?>
                                    <span class="super-admin-badge">Super</span>
                                <?php endif; ?>
                            </span>
                            
                            <?php if ($is_protected_admin): ?>
                                <a href="register_admin.php" class="navbar-btn-small" title="Tambah User Baru">
                                    <span class="glyphicon glyphicon-user"></span> Tambah User
                                </a>
                            <?php else: ?>
                            
                                <span class="navbar-btn-small disabled" title="Hanya Super Admin yang bisa menambah user">
                                    <span class="glyphicon glyphicon-user"></span> Tambah User
                                </span>
                            <?php endif; ?>
                            
                            <a href="logout.php" class="navbar-btn-small logout-btn">
                                <span class="glyphicon glyphicon-log-out"></span> Logout
                            </a>
                        </div>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div class="container-fluid">
        <div class="content-fluid">