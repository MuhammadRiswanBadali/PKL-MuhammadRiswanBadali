<?php
include('header-admin.php');
include('koneksi.php');
include('sessionlogin.php');

if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk Administrator.'); window.location='dashboard.php';</script>";
    exit;
}

// Cek apakah user yang login adalah protected admin
$current_user_id = $_SESSION['id_user'];
$user_query = mysqli_query($koneksi, 
    "SELECT is_protected FROM users WHERE id_user='$current_user_id'");
$current_user = mysqli_fetch_assoc($user_query);
$is_protected_admin = (isset($current_user['is_protected']) && $current_user['is_protected'] == 1);
?>

<style>
body {
    background-color: #f5f5f5;
}
.table {
    width: 98%;
    margin: 20px auto;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    margin-top: 30px;
    font-weight: 600;
}
hr {
    border: 1px solid #ddd;
    width: 95%;
}
.form-group {
    margin-left: 30px;
}
.btn-disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.label-protected {
    font-size: 10px;
    padding: 2px 5px;
    margin-left: 5px;
    border-radius: 3px;
}
</style>

<h2>Data User</h2>
<hr/>

<?php if (!$is_protected_admin): ?>
<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px 15px; margin: 0 30px 20px 30px; font-size: 14px;">
    <strong>Hanya dapat mengedit profil sendiri</strong>
</div>
<?php endif; ?>

<div class="form-group">
    <?php if ($is_protected_admin): ?>
        <a href="register_admin.php" class="btn btn-success btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Tambah User
        </a>
    <?php else: ?>
        <button class="btn btn-success btn-sm" style="opacity: 0.5; cursor: not-allowed;" disabled>
            <span class="glyphicon glyphicon-plus"></span> Tambah User
        </button>
        <small style="color: #6c757d; margin-left: 10px;">Hanya Super Admin</small>
    <?php endif; ?>
</div>

<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Username</th>
            <th>Role</th>
            <th>Tanggal Dibuat</th>
            <th>Tools</th>
        </tr>

        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id_user ASC")
                 or die("Query error: " . mysqli_error($koneksi));

        $no = 1;
        while ($data = mysqli_fetch_assoc($query)) {
            $show_edit = true;
            $edit_title = "Edit";
            
            if (!$is_protected_admin && $_SESSION['id_user'] != $data['id_user']) {
                $show_edit = false;
            }
            
            $can_delete = true;
            $delete_reason = "";
            
            if ($_SESSION['id_user'] == $data['id_user']) {
                $can_delete = false;
                $delete_reason = "Akun sendiri";
            }
            elseif (isset($data['role']) && $data['role'] == 'admin' && !$is_protected_admin) {
                $can_delete = false;
                $delete_reason = "Hanya Super Admin yang bisa hapus Admin";
            }
            elseif (isset($data['is_protected']) && $data['is_protected'] == 1) {
                $can_delete = false;
                $delete_reason = "Super Admin tidak dapat dihapus";
            }
            elseif (!$is_protected_admin && isset($data['role']) && $data['role'] == 'petugas') {
                $can_delete = false;
                $delete_reason = "Hanya Super Admin yang bisa hapus Petugas";
            }
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
            <td><?php echo htmlspecialchars($data['username']); ?></td>
            <td>
                <?php echo ucfirst($data['role']); ?>
                <?php if (isset($data['is_protected']) && $data['is_protected'] == 1): ?>
                    <span class="label label-primary label-protected">Super</span>
                <?php endif; ?>
                <?php if ($_SESSION['id_user'] == $data['id_user']): ?>
                    <span class="label label-success label-protected">Anda</span>
                <?php endif; ?>
            </td>
            <td><?php echo $data['created_at']; ?></td>
            <td>
                <?php if ($show_edit): ?>
                    <a href="daftar_user_edit.php?id_user=<?php echo $data['id_user']; ?>" 
                       class="btn btn-primary btn-sm" title="<?php echo $edit_title; ?>">
                       <span class="glyphicon glyphicon-edit"></span>
                    </a>
                <?php else: ?>
                    <button class="btn btn-primary btn-sm" 
                            style="opacity: 0.5; cursor: not-allowed;" 
                            title="Hanya bisa edit profil sendiri" disabled>
                        <span class="glyphicon glyphicon-edit"></span>
                    </button>
                <?php endif; ?>

                <?php if ($_SESSION['id_user'] != $data['id_user']) { 
                    if ($can_delete) { 
                ?>
                        <a href="daftar_user_delete.php?id_user=<?php echo $data['id_user']; ?>" 
                           class="btn btn-danger btn-sm" 
                           title="Hapus" 
                           onclick="return confirm('Yakin ingin menghapus <?php echo htmlspecialchars($data['nama_lengkap']); ?>?')">
                           <span class="glyphicon glyphicon-trash"></span>
                        </a>
                <?php 
                    } else { 
                ?>
                        <button class="btn btn-danger btn-sm" 
                                style="opacity: 0.5; cursor: not-allowed;" 
                                title="Tidak dapat dihapus: <?php echo $delete_reason; ?>" 
                                disabled>
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                <?php 
                    }
                } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include('footer.php'); ?>