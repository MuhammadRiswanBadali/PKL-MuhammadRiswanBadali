<?php
include('header-admin.php');
include('koneksi.php');
include('sessionlogin.php');

if ($_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger'>Anda tidak memiliki izin untuk menghapus user!</div>";
    exit;
}

if (!isset($_GET['id_user']) || empty($_GET['id_user'])) {
    echo "<div class='alert alert-danger'>
            ID user tidak ditemukan!
          </div>";
    exit;
}

$id_user = $_GET['id_user'];
$login_user = $_SESSION['id_user'];  

// TAMBAHKAN: Cek apakah user memiliki data terkait di tabel lain
$has_related_data = false;
$related_tables = [];

// Cek di tabel hasil_pemantauan
$cek_hasil = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM hasil_pemantauan WHERE id_user='$id_user'");
$hasil_data = mysqli_fetch_assoc($cek_hasil);
if ($hasil_data['total'] > 0) {
    $has_related_data = true;
    $related_tables[] = 'Hasil Pemantauan';
}

// Cek di tabel lokasi_pemantauan
$cek_lokasi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM lokasi_pemantauan WHERE id_user='$id_user'");
$lokasi_data = mysqli_fetch_assoc($cek_lokasi);
if ($lokasi_data['total'] > 0) {
    $has_related_data = true;
    $related_tables[] = 'Lokasi Pemantauan';
}

// Cek di tabel pemantauan_udara
$cek_pemantauan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pemantauan_udara WHERE id_user='$id_user'");
$pemantauan_data = mysqli_fetch_assoc($cek_pemantauan);
if ($pemantauan_data['total'] > 0) {
    $has_related_data = true;
    $related_tables[] = 'Data Pemantauan Udara';
}

// Cek di tabel berita
$cek_berita = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM berita WHERE id_user='$id_user'");
$berita_data = mysqli_fetch_assoc($cek_berita);
if ($berita_data['total'] > 0) {
    $has_related_data = true;
    $related_tables[] = 'Berita';
}

// Cek di tabel edukasi
$cek_edukasi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM edukasi WHERE id_user='$id_user'");
$edukasi_data = mysqli_fetch_assoc($cek_edukasi);
if ($edukasi_data['total'] > 0) {
    $has_related_data = true;
    $related_tables[] = 'Edukasi';
}

// TAMPILKAN PESAN ERROR JIKA ADA DATA TERKAIT
if ($has_related_data) {
    echo "<div class='container' style='max-width: 1000px; margin: 100px auto; border-radius: 15px; background-color: #ffffffff; border: 1px solid #ddd; padding: 20px;'>";
    echo "<div class='alert alert-danger'>";
    echo "<h3 style='text-align: center; font-weight: bold;'>User Tidak Dapat Dihapus</h3>";
    echo "<div class='well' style='text-align: center; margin: 20px 0; padding: 15px;'>";
    echo "<p style='font-size: 16px;'>User ini masih memiliki data terkait di database:</p>";
    echo "<ul style='text-align: left; display: inline-block;'>";
    foreach ($related_tables as $table) {
        echo "<li><strong>$table</strong></li>";
    }
    echo "</ul>";
    echo "</div>";
    echo "<div class='text-center'>";
    echo "<a href='daftar_user.php' class='btn btn-primary btn-lg'>Kembali ke Daftar User</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    include('footer.php');
    exit;
}

// TAMBAHKAN: Cek apakah user yang login adalah protected admin
$current_user_query = mysqli_query($koneksi, 
    "SELECT is_protected FROM users WHERE id_user='$login_user'");
$current_user = mysqli_fetch_assoc($current_user_query);
$is_protected_admin = (isset($current_user['is_protected']) && $current_user['is_protected'] == 1);

$cek = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user='$id_user'");
if (mysqli_num_rows($cek) == 0) {
    echo "<div class='alert alert-warning'>
            Data user tidak ditemukan di database.
          </div>";
    exit;
}

$row = mysqli_fetch_assoc($cek);

// TAMBAHKAN: Cek apakah user yang akan dihapus adalah protected
$target_is_protected = (isset($row['is_protected']) && $row['is_protected'] == 1);

// MODIFIKASI: Proteksi tidak bisa hapus diri sendiri
if ($id_user == $login_user) {
    echo "<div class='alert alert-danger'>
                Anda tidak dapat menghapus akun Anda sendiri!<br>
            <small>Jika perlu menghapus akun Anda, minta Super Admin lain untuk melakukannya.</small>
          </div>";
    echo "<meta http-equiv='refresh' content='2; url=daftar_user.php'>";
    exit;
}

// MODIFIKASI: Proteksi untuk user protected (Super Admin)
if ($target_is_protected) {
    echo "<div class='alert alert-danger'>
                Super Admin tidak dapat dihapus!<br>
            <small>User ini ditandai sebagai Super Admin dan dilindungi dari penghapusan.</small>
          </div>";
    echo "<meta http-equiv='refresh' content='2; url=daftar_user.php'>";
    exit;
}

// MODIFIKASI: Proteksi untuk admin (hanya Super Admin yang bisa hapus admin)
if ($row['role'] === 'admin' && !$is_protected_admin) {
    echo "<div class='alert alert-danger'>
                Akses ditolak! Hanya Super Admin yang bisa menghapus Admin.<br>
            <small>Anda login sebagai Admin biasa. Hubungi Super Admin untuk tindakan ini.</small>
          </div>";
    echo "<meta http-equiv='refresh' content='2; url=daftar_user.php'>";
    exit;
}

// TAMBAHKAN: Proteksi untuk satu-satunya admin
if ($row['role'] === 'admin') {
    // Hitung total admin SELAIN yang akan dihapus
    $count_query = mysqli_query($koneksi, 
        "SELECT COUNT(*) as total FROM users WHERE role='admin' AND is_protected=0 AND id_user != '$id_user'");
    $count_data = mysqli_fetch_assoc($count_query);
    $remaining_normal_admins = $count_data['total'];
    
    // Hitung protected admin yang tersisa
    $protected_query = mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM users WHERE role='admin' AND is_protected=1 AND id_user != '$id_user'");
    $protected_data = mysqli_fetch_assoc($protected_query);
    $remaining_protected_admins = $protected_data['total'];
    
    // Jika tidak ada admin normal lain dan tidak ada protected admin lain
    if ($remaining_normal_admins < 1 && $remaining_protected_admins < 1) {
        echo "<div class='alert alert-danger'>
                    Tidak dapat menghapus satu-satunya admin!<br>
                <small>Sistem harus memiliki minimal 1 admin. Buat admin baru terlebih dahulu.</small>
              </div>";
        echo "<meta http-equiv='refresh' content='2; url=daftar_user.php'>";
        exit;
    }
}

// TAMBAHKAN: Konfirmasi ekstra untuk hapus admin

if ($row['role'] === 'admin') {
    ?>
    <div class="container" style="max-width: 1000px; margin: 100px auto; border-radius: 15px; background-color: #ffffffff; border: 1px solid #ddd; padding: 20px;">
        <div class="alert alert-danger">
          <h3 style="text-align: center; font-weight: bold;">Anda Yakin Ingin Menghapus Admin?</h3>
            <div class="well" style="text-align: center;">
                <strong>Nama :</strong> <?php echo htmlspecialchars($row['nama_lengkap']); ?><br>
                <strong>Username :</strong> <?php echo htmlspecialchars($row['username']); ?>
            </div>
            
            <form method="post">
                <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
                
                <div class="text-center" style="margin-top: 20px;">
                    <button type="submit" name="confirm_delete_admin" class="btn btn-danger btn-lg">
                        <span class="glyphicon glyphicon-trash"></span> Ya, Hapus Admin
                    </button>
                    <a href="daftar_user.php" class="btn btn-default btn-lg">Batal</a>
                </div>
            </form>
        </div>
    </div>
    
    <?php
    if (isset($_POST['confirm_delete_admin'])) {
        $delete = mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id_user'") 
                  or die(mysqli_error($koneksi));
        
        if ($delete) {
            echo "<meta http-equiv='refresh' content='1; url=daftar_user.php'>"; 
        } else {
        }
    }
    
    include('footer.php');
    exit;
}

$delete = mysqli_query($koneksi, "DELETE FROM users WHERE id_user='$id_user'") 
          or die(mysqli_error($koneksi));

if ($delete) {
    echo "<div class='alert alert-success alert-dismissable'>
            ✅ Petugas <strong>" . htmlspecialchars($row['nama_lengkap']) . "</strong> berhasil dihapus.
          </div>";
    echo "<meta http-equiv='refresh' content='1; url=daftar_user.php'>"; 
} else {
    echo "<div class='alert alert-danger alert-dismissable'>
            ❌ Gagal menghapus data user.
          </div>";
}
?>

<?php include('footer.php'); ?>