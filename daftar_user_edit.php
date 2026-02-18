<?php
include('header-admin.php');
include('koneksi.php');
include('sessionlogin.php');

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Admin.'); window.location='dashboard.php';</script>";
    exit;
}

if (!isset($_GET['id_user'])) {
    echo "<div class='alert alert-danger'>⚠️ ID user tidak ditemukan!</div>";
    exit;
}

$id_user = $_GET['id_user'];

$sql = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
if (mysqli_num_rows($sql) == 0) {
    echo "<div class='alert alert-danger'>⚠️ Data user tidak ditemukan!</div>";
    exit;
}
$row = mysqli_fetch_assoc($sql);

// TAMBAHKAN: Cek apakah user yang login adalah protected admin
$current_user_id = $_SESSION['id_user'];
$current_user_query = mysqli_query($koneksi, 
    "SELECT is_protected FROM users WHERE id_user='$current_user_id'");
$current_user = mysqli_fetch_assoc($current_user_query);
$is_protected_admin = (isset($current_user['is_protected']) && $current_user['is_protected'] == 1);

// TAMBAHKAN: Cek apakah user yang diedit adalah protected
$target_is_protected = (isset($row['is_protected']) && $row['is_protected'] == 1);

// TAMBAHKAN: Hitung total admin untuk proteksi
$count_query = mysqli_query($koneksi, 
    "SELECT COUNT(*) as total FROM users WHERE role='admin'");
$count_data = mysqli_fetch_assoc($count_query);
$total_admin = $count_data['total'];

// TAMBAHKAN: Cek jika ini satu-satunya admin
$is_last_admin = ($row['role'] == 'admin' && $total_admin <= 1);

if (isset($_POST['save'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role         = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password_baru = trim($_POST['password']);
    
    // TAMBAHKAN: Validasi sebelum update
    $validation_error = "";
    
    // Validasi 1: Jika mengubah role admin menjadi petugas
    if ($row['role'] == 'admin' && $role == 'petugas') {
        // Cek apakah ini satu-satunya admin
        if ($is_last_admin) {
            $validation_error = "Tidak dapat mengubah satu-satunya admin menjadi petugas!";
        }
        // Cek apakah yang mengedit adalah protected admin
        elseif (!$is_protected_admin) {
            $validation_error = "Hanya Super Admin yang bisa mengubah role Admin!";
        }
    }
    
    // Validasi 2: Jika user yang diedit adalah protected
    if ($target_is_protected) {
        // Super Admin tidak bisa diubah rolenya
        if ($row['role'] != $role) {
            $validation_error = "Role Super Admin tidak dapat diubah!";
        }
    }
    
    // TAMBAHKAN: Jika ada error validasi, tampilkan dan stop proses
    if (!empty($validation_error)) {
        echo "<div class='alert alert-danger alert-dismissable'>
                ❌ $validation_error
              </div>";
    } else {
        // Lanjutkan dengan update jika validasi lolos
        if (!empty($password_baru)) {
            $password = password_hash($password_baru, PASSWORD_DEFAULT);
            $update = mysqli_query($koneksi, "
                UPDATE users 
                SET nama_lengkap='$nama_lengkap',
                    username='$username',
                    password='$password',
                    role='$role'
                WHERE id_user='$id_user'
            ");
        } else {
            $update = mysqli_query($koneksi, "
                UPDATE users 
                SET nama_lengkap='$nama_lengkap',
                    username='$username',
                    role='$role'
                WHERE id_user='$id_user'
            ");
        }

        if ($update) {
            echo "<div class='alert alert-success alert-dismissable'>
                    ✅ Data user berhasil diperbarui.
                  </div>
                  <meta http-equiv='refresh' content='1; url=daftar_user.php'>";
        } else {
            echo "<div class='alert alert-danger alert-dismissable'>
                    ❌ Gagal memperbarui data user.
                  </div>";
        }
    }
}
?>

<style>
body {
    background-color: #f5f5f5;
}
.container {
    background: #fff;
    padding: 20px;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    width: 80%;
    margin: 30px auto;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
/* TAMBAHKAN: Style untuk warning box */
.warning-box {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 15px;
    font-size: 14px;
}
.info-text {
    font-size: 12px;
    color: #666;
    font-style: italic;
    margin-top: 5px;
}
</style>

<div class="container">
    <h2>Edit Data User</h2>
    <hr />
    
    <!-- TAMBAHKAN: Warning jika ini satu-satunya admin -->
    <?php if ($is_last_admin): ?>
    <div class="warning-box">
        ⚠️ <strong>PERINGATAN:</strong> Ini adalah satu-satunya admin di sistem!<br>
        Jika Anda mengubah role menjadi petugas, sistem tidak akan memiliki admin.
    </div>
    <?php endif; ?>
    
    <!-- TAMBAHKAN: Info jika user adalah Super Admin -->
    <?php if ($target_is_protected): ?>
    <div class="alert alert-info">
        <strong>ℹ️ INFO:</strong> User ini adalah <strong>Super Admin</strong>.<br>
        Role Super Admin tidak dapat diubah.
    </div>
    <?php endif; ?>

    <form class="form-horizontal" action="" method="post">

        <div class="form-group">
            <label class="col-sm-3 control-label">Nama Lengkap</label>
            <div class="col-sm-6">
                <input type="text" name="nama_lengkap" class="form-control" 
                       value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">Username</label>
            <div class="col-sm-6">
                <input type="text" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($row['username']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">Password Baru</label>
            <div class="col-sm-6">
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">Role</label>
            <div class="col-sm-4">
                <?php if ($target_is_protected): ?>
                    <!-- TAMBAHKAN: Super Admin - role tidak bisa diubah -->
                    <input type="text" class="form-control" 
                           value="<?php echo htmlspecialchars($row['role']); ?>" readonly>
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['role']); ?>">
                    <span class="info-text">Role Super Admin tidak dapat diubah</span>
                <?php elseif ($row['role'] == 'admin' && !$is_protected_admin): ?>
                    <!-- TAMBAHKAN: Admin biasa - hanya Super Admin yang bisa ubah role -->
                    <input type="text" class="form-control" 
                           value="<?php echo htmlspecialchars($row['role']); ?>" readonly>
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($row['role']); ?>">
                    <span class="info-text">Hanya Super Admin yang bisa mengubah role Admin</span>
                <?php else: ?>
                    <!-- TAMBAHKAN: Kasus lain (petugas atau admin diedit oleh Super Admin) -->
                    <select name="role" class="form-control" required>
                        <option value="admin" <?php echo ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="petugas" <?php echo ($row['role'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                    </select>
                    <?php if ($row['role'] == 'admin'): ?>
                        <span class="info-text text-warning">⚠️ Hati-hati mengubah role admin!</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-3 control-label">&nbsp;</label>
            <div class="col-sm-6">
                <button type="submit" name="save" class="btn btn-sm btn-primary">Simpan</button>
                <button type="reset" class="btn btn-sm btn-warning">Reset</button>
                <a href="daftar_user.php" class="btn btn-sm btn-danger">Kembali</a>
            </div>
        </div>
    </form>
</div>

<?php include('footer.php'); ?>