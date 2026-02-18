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

$current_halaman = isset($_GET['halaman']) ? $_GET['halaman'] : 1;

if (!isset($_GET['id_hasil']) || !is_numeric($_GET['id_hasil'])) {
    echo "<div class='alert alert-danger'>⚠️ ID hasil tidak valid!</div>";
    exit;
}
$id_hasil = $_GET['id_hasil'];

$sql = mysqli_query($koneksi, "
    SELECT h.*, p.tanggal_pemantauan, p.id_pemantauan, 
           l.kode_lokasi, l.nama_lokasi, l.alamat_lokasi
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    WHERE h.id_hasil='$id_hasil'
");

if (mysqli_num_rows($sql) == 0) {
?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ⚠️ Data hasil pemantauan tidak ditemukan!
    </div>
<?php
    exit;
}

$row = mysqli_fetch_assoc($sql);

if (isset($_POST['save'])) {
    $id_pemantauan = $_POST['id_pemantauan'];
    $no2  = $_POST['no2'];
    $so2  = $_POST['so2'];
    $pm25 = $_POST['pm25'];

    $current_halaman = $_POST['current_halaman'];

    $update = mysqli_query($koneksi, "
        UPDATE hasil_pemantauan SET 
            id_pemantauan='$id_pemantauan',
            no2='$no2',
            so2='$so2',
            pm25='$pm25'
        WHERE id_hasil='$id_hasil'
    ") or die(mysqli_error($koneksi));

    if ($update) {
        echo "
            <div class='alert alert-success alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                ✅ Data hasil pemantauan berhasil diperbarui.
            </div>
            <meta http-equiv='refresh' content='1; url=hasil_pemantauan_data.php?halaman=$current_halaman'>
        ";
    } else {
        echo "
            <div class='alert alert-danger alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                ❌ Gagal memperbarui data, silakan coba lagi.
            </div>
        ";
    }
}
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
.select2-container--default .select2-selection--single {
    height: 34px;
    padding: 3px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 32px;
}
</style>

<div class="container-fluid">
    <div class="content-fluid">
        <h2>Data Hasil Pemantauan &raquo; Edit Data</h2>
        <hr/>

        <form class="form-horizontal" action="" method="post">

            <input type="hidden" name="current_halaman" value="<?php echo $current_halaman; ?>">

            <div class="form-group">
                <label class="col-sm-3 control-label">Pemantauan Udara</label>
                <div class="col-sm-7">
                    <select name="id_pemantauan" id="selectPemantauan" class="form-control" required>
                        <option value="">-- Pilih Pemantauan Udara --</option>

                        <?php
                        $qry = mysqli_query($koneksi, "
                            SELECT p.id_pemantauan, l.kode_lokasi, p.tanggal_pemantauan, 
                                   l.nama_lokasi, l.alamat_lokasi
                            FROM pemantauan_udara p
                            JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
                            ORDER BY p.tanggal_pemantauan DESC
                        ");

                        while ($data = mysqli_fetch_array($qry)) {
                            $selected = ($data['id_pemantauan'] == $row['id_pemantauan']) ? 'selected' : '';
                            echo "
                                <option value='{$data['id_pemantauan']}' $selected>
                                    {$data['kode_lokasi']} | {$data['tanggal_pemantauan']} | {$data['nama_lokasi']} | {$data['alamat_lokasi']}
                                </option>
                            ";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Kadar NO₂ (µg/m³)</label>
                <div class="col-sm-3">
                    <input type="number" step="0.01" name="no2" value="<?php echo $row['no2']; ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Kadar SO₂ (µg/m³)</label>
                <div class="col-sm-3">
                    <input type="number" step="0.01" name="so2" value="<?php echo $row['so2']; ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Kadar PM₂.₅ (µg/m³)</label>
                <div class="col-sm-3">
                    <input type="number" step="0.01" name="pm25" value="<?php echo $row['pm25']; ?>" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <button type="submit" name="save" class="btn btn-sm btn-primary">Simpan</button>
                    <button type="reset" class="btn btn-sm btn-warning">Reset</button>
                    <a href="hasil_pemantauan_data.php?halaman=<?php echo $current_halaman; ?>" 
                       class="btn btn-sm btn-danger">Kembali</a>
                </div>
            </div>

        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $("#selectPemantauan").select2({
        placeholder: "Ketik untuk mencari pemantauan udara...",
        allowClear: true,
        width: "100%",
        language: {
            noResults: () => "Data tidak ditemukan",
            searching: () => "Mencari..."
        }
    });
});
</script>

<?php include "footer.php"; ?>
