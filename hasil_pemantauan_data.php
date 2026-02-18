<?php
include "koneksi.php";
session_start();

if (!isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'petugas')) {

    echo "<script>alert('Akses ditolak!'); window.location='login.php';</script>";
    exit;
}

if ($_SESSION['role'] === 'admin') {
    include "header-admin.php";
} else {
    include "header-petugas.php";
}

// FILTER
// ===========================
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";

$data_per_halaman = 20;

// PAGINATION
// ===========================
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_aktif < 1) $halaman_aktif = 1;

$offset = ($halaman_aktif - 1) * $data_per_halaman;

// Hitung total data berdasarkan filter
$total_data_query = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    " . ($cari != "" ? " WHERE l.kode_lokasi LIKE '%$cari%'" : "")
);

$total_data = (int) mysqli_fetch_assoc($total_data_query)['total'];
$total_halaman = max(1, ceil($total_data / $data_per_halaman));

if ($halaman_aktif > $total_halaman) {
    $halaman_aktif = $total_halaman;
}

// ============================
// QUERY UTAMA DENGAN ROW_NUMBER
// ============================

$query = mysqli_query($koneksi, "
    SELECT *
    FROM (
        SELECT 
            ROW_NUMBER() OVER (ORDER BY p.tanggal_pemantauan DESC) AS nomor_asli,
            h.id_hasil,
            l.kode_lokasi,
            p.tanggal_pemantauan,
            h.no2,
            h.so2,
            h.pm25
        FROM hasil_pemantauan h
        JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
        JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    ) AS x
    WHERE 1=1
    " . ($cari != "" ? " AND x.kode_lokasi LIKE '%$cari%'" : "") . "
    ORDER BY nomor_asli ASC
    LIMIT $offset, $data_per_halaman
");

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
.pagination-container {
    text-align: center;
    margin: 20px 0;
}
</style>

<h2>Data Hasil Pemantauan Udara</h2>
<hr/>

<div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">

    <!-- Bagian kiri: Button Tambah Data -->
    <div>
        <a href="hasil_pemantauan_add.php" class="btn btn-success btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Tambah Data
        </a>
    </div>

    <!-- Bagian kanan: Form Search -->
    <div>
        <form method="GET" action="" style="display:flex; gap:5px;">
            <input type="text" name="cari" 
                   class="form-control input-sm" 
                   placeholder="Cari kode lokasi" 
                   value="<?= $cari; ?>"
                   style="width:250px;">

            <button type="submit" class="btn btn-primary btn-sm">
                <span class="glyphicon glyphicon-search"></span> Cari
            </button>

            <?php if ($cari != "") { ?>
                <a href="hasil_pemantauan_data.php" class="btn btn-default btn-sm">
                    Clear
                </a>
            <?php } ?>

            <input type="hidden" name="halaman" value="1">
        </form>
    </div>

</div>



<br/>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>No</th>
            <th>Kode Lokasi</th>
            <th>Tanggal Pemantauan</th>
            <th>Kadar NO₂ (µg/m³)</th>
            <th>Kadar SO₂ (µg/m³)</th>
            <th>Kadar PM₂.₅ (µg/m³)</th>
            <th>Tools</th>
        </tr>

        <?php
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $row['nomor_asli']; ?></td>
            <td><?= htmlspecialchars($row['kode_lokasi']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_pemantauan']); ?></td>
            <td><?= htmlspecialchars($row['no2']); ?></td>
            <td><?= htmlspecialchars($row['so2']); ?></td>
            <td><?= htmlspecialchars($row['pm25']); ?></td>
            <td>
                <a href="hasil_pemantauan_edit.php?id_hasil=<?= $row['id_hasil']; ?>&halaman=<?= $halaman_aktif; ?>" 
                   class="btn btn-primary btn-sm" title="Edit Data">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>

                <a href="hasil_pemantauan_delete.php?id_hasil=<?= $row['id_hasil']; ?>&halaman=<?= $halaman_aktif; ?>" 
                   onclick="return confirm('Yakin ingin menghapus data ini?')" 
                   class="btn btn-danger btn-sm" title="Hapus Data">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='7' class='text-center'>Tidak ada data.</td></tr>";
        }
        ?>
    </table>
</div>

<div class="pagination-container">
    <nav>
        <ul class="pagination">
            <li class="<?= ($halaman_aktif <= 1) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= max(1, $halaman_aktif - 1); ?>&cari=<?= $cari; ?>">&laquo; Previous</a>
            </li>

            <li class="active">
                <a href="#">
                    <?= $halaman_aktif; ?> dari <?= $total_halaman; ?>
                </a>
            </li>

            <li class="<?= ($halaman_aktif >= $total_halaman) ? 'disabled' : ''; ?>">
                <a href="?halaman=<?= min($total_halaman, $halaman_aktif + 1); ?>&cari=<?= $cari; ?>">Next &raquo;</a>
            </li>
        </ul>
    </nav>

    <p>Total Data: <b><?= $total_data; ?></b></p>
</div>

<?php include('footer.php'); ?>
