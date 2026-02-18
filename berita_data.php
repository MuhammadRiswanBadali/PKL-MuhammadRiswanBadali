<?php
include "header-admin.php"; 
include "sessionlogin.php"; 
include "koneksi.php";


if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini khusus Administrator.'); window.location='dashboard.php';</script>";
    exit;
}
?>

<style>
body {
    background-color: #f5f5f5;
}
.container, .table-responsive {
    width: 100%;
    max-width: 100%;
}
.table {
    width: 98%;
    margin: auto;
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
</style>

<h2>Manajemen Berita</h2>
<hr/>

<div class="form-group">
    <a href="berita_add.php" class="btn btn-success btn-sm">
        <span class="glyphicon glyphicon-plus"></span> Tambah Berita
    </a>
</div>

<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Judul Berita</th>
            <th>Isi Singkat</th>
            <th>Tanggal Posting</th>
            <th>Penulis</th>
            <th>Gambar</th>
            <th>Tools</th>
        </tr>

        <?php
      
        $sql = "
            SELECT 
                b.id_berita,
                b.judul_berita,
                LEFT(b.isi_berita, 100) AS isi_singkat,
                b.tanggal_posting,
                b.gambar,
                u.nama_lengkap AS penulis
            FROM berita b
            LEFT JOIN users u ON b.id_user = u.id_user
            ORDER BY b.tanggal_posting DESC
        ";

        $query = mysqli_query($koneksi, $sql) or die("Query salah: " . mysqli_error($koneksi));
        $no = 1;

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($data['judul_berita']); ?></td>
            <td><?php echo htmlspecialchars($data['isi_singkat']) . '...'; ?></td>
            <td><?php echo htmlspecialchars($data['tanggal_posting']); ?></td>
            <td><?php echo htmlspecialchars($data['penulis']); ?></td>
            <td>
                <?php if (!empty($data['gambar'])) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($data['gambar']); ?>" alt="gambar berita" width="80">
                <?php } else { ?>
                    <span class="text-muted">Tidak ada</span>
                <?php } ?>
            </td>
            <td>
                <a href="berita_edit.php?id_berita=<?php echo $data['id_berita']; ?>" 
                   title="Edit Data" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>

                <a href="berita_delete.php?id_berita=<?php echo $data['id_berita']; ?>" 
                   title="Hapus Data" 
                   onclick="return confirm('Yakin ingin menghapus berita ini?')" 
                   class="btn btn-danger btn-sm">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo '<tr><td colspan="7" class="text-center">Belum ada berita yang diposting.</td></tr>';
        }
        ?>
    </table>
</div>

<?php include "footer.php"; ?>
