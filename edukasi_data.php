<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";
?>

<style>
body {
    background-color: #f5f5f5;
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

<h2>Manajemen Edukasi</h2>
<hr/>

<div class="form-group">
    <a href="edukasi_add.php" class="btn btn-success btn-sm">
        <span class="glyphicon glyphicon-plus"></span> Tambah Edukasi
    </a>
</div>

<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Judul Edukasi</th>
            <th>Isi Singkat</th>
            <th>Jenis Konten</th>
            <th>Tanggal Posting</th>
            <th>Penulis</th>
            <th>Konten</th>
            <th>Tools</th>
        </tr>

        <?php
     
        $sql = "
            SELECT 
                e.id_edukasi,
                e.judul_edukasi,
                LEFT(e.isi_edukasi, 120) AS isi_singkat,
                e.tipe_konten,
                e.file_path,
                e.link_video,
                e.tanggal_posting,
                u.nama_lengkap AS penulis
            FROM edukasi e
            LEFT JOIN users u ON e.id_user = u.id_user
            ORDER BY e.tanggal_posting DESC
        ";

        $query = mysqli_query($koneksi, $sql) or die("Query error: " . mysqli_error($koneksi));
        $no = 1;

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $no++; ?></td>

            <td><?= htmlspecialchars($data['judul_edukasi']); ?></td>

            <td><?= htmlspecialchars($data['isi_singkat']) . '...'; ?></td>

            <td><?= ucfirst(htmlspecialchars($data['tipe_konten'])); ?></td>

            <td><?= htmlspecialchars($data['tanggal_posting']); ?></td>

            <td><?= htmlspecialchars($data['penulis'] ?: '-'); ?></td>

            <td>
                <?php 
                if ($data['tipe_konten'] === 'file' && !empty($data['file_path'])) {
                    echo '<a href="uploads/edukasi/' . htmlspecialchars($data['file_path']) . '" 
                           target="_blank" class="btn btn-info btn-sm">📄 Lihat File</a>';
                } 
                elseif ($data['tipe_konten'] === 'video' && !empty($data['link_video'])) {
                    echo '<a href="' . htmlspecialchars($data['link_video']) . '" 
                           target="_blank" class="btn btn-danger btn-sm">▶️ Tonton Video</a>';
                } 
                else {
                    echo '<span class="text-muted">Tidak ada konten</span>';
                }
                ?>
            </td>

            <td>
                <a href="edukasi_edit.php?id_edukasi=<?= $data['id_edukasi']; ?>" 
                   class="btn btn-primary btn-sm" title="Edit">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>

                <a href="edukasi_delete.php?id_edukasi=<?= $data['id_edukasi']; ?>" 
                   onclick="return confirm('Yakin ingin menghapus data edukasi ini?')"
                   class="btn btn-danger btn-sm" title="Hapus">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo '<tr><td colspan="8" class="text-center">Belum ada konten edukasi yang ditambahkan.</td></tr>';
        }
        ?>
    </table>
</div>

<?php include "footer.php"; ?>
