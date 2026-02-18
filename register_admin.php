<?php
include 'koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $pass     = $_POST['password'];
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']); // AMBIL role dari select

    // enkripsi password pakai bcrypt
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // cek apakah username sudah digunakan
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "⚠️ Username sudah terpakai!";
    } else {
        $sql = "INSERT INTO users (username, password, nama_lengkap, role, created_at)
                VALUES ('$username', '$hash', '$nama', '$role', NOW())";

        if (mysqli_query($koneksi, $sql)) {

            // pesan sukses
            $success = "✅ Akun berhasil dibuat sebagai $role!";

            // 🔥 redirect otomatis ke daftar_user.php (dalam 1.5 detik)
            echo "<meta http-equiv='refresh' content='1.5; url=daftar_user.php'>";
        } else {
            $error = "❌ Gagal membuat user: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi User Baru</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container {
            margin-top: 100px;
            max-width: 400px;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Buat Akun User</h2>
        <form method="post">

            <div class="form-group">
                <label>Username</label>
                <input name="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input name="nama_lengkap" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Buat User</button>
        </form>

        <br>

        <?php
        if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if (isset($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>

    </div>
</body>
</html>
