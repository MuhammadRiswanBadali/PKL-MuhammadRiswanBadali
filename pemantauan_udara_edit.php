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

if (!isset($_GET['id_pemantauan']) || $_GET['id_pemantauan'] == "") {
    echo "<div class='alert alert-danger'>⚠️ ID pemantauan tidak ditemukan.</div>";
    echo "<meta http-equiv='refresh' content='1; url=pemantauan_udara_data.php?halaman={$current_halaman}'>";
    exit;
}

$id_pemantauan = $_GET['id_pemantauan'];

$sql = mysqli_query($koneksi, "
    SELECT p.*, l.nama_lokasi, l.kode_lokasi, l.alamat_lokasi
    FROM pemantauan_udara p
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    WHERE p.id_pemantauan='$id_pemantauan'
");

if (mysqli_num_rows($sql) == 0) {
    echo "<div class='alert alert-danger'>⚠️ Data pemantauan tidak ditemukan!</div>";
    echo "<meta http-equiv='refresh' content='1; url=pemantauan_udara_data.php?halaman={$current_halaman}'>";
    exit;
}

$row = mysqli_fetch_assoc($sql);

if (isset($_POST['save'])) {

    $id_lokasi          = $_POST['id_lokasi'];
    $periode_pemantauan = $_POST['periode_pemantauan'];
    $tanggal_pemantauan = $_POST['tanggal_pemantauan'];
    $metode_pemantauan  = $_POST['metode_pemantauan'];
    $shu                = $_POST['shu'];
    $durasi_pemantauan  = $_POST['durasi_pemantauan'];
    $level              = $_POST['level'];

    $current_halaman = $_POST['current_halaman'];

    $cek = mysqli_query($koneksi, "
        SELECT id_pemantauan 
        FROM pemantauan_udara 
        WHERE id_lokasi='$id_lokasi'
        AND tanggal_pemantauan='$tanggal_pemantauan'
        AND id_pemantauan != '$id_pemantauan'
    ");

    if (mysqli_num_rows($cek) > 0) {
        echo "<div class='alert alert-danger alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                ❌ <b>Gagal memperbarui!</b><br>
                Tanggal <b>" . date('d-m-Y', strtotime($tanggal_pemantauan)) . "</b> sudah digunakan oleh lokasi ini.
              </div>";
    } else {

        $update = mysqli_query($koneksi, "
            UPDATE pemantauan_udara SET 
                id_lokasi='$id_lokasi',
                periode_pemantauan='$periode_pemantauan',
                tanggal_pemantauan='$tanggal_pemantauan',
                metode_pemantauan='$metode_pemantauan',
                shu='$shu',
                durasi_pemantauan='$durasi_pemantauan',
                level='$level'
            WHERE id_pemantauan='$id_pemantauan'
        ");

        if ($update) {
            echo "<div class='alert alert-success alert-dismissable'>
                    <button type='button' class='close' data-dismiss='alert'>&times;</button>
                    ✅ Data berhasil diperbarui.
                  </div>";
            echo "<meta http-equiv='refresh' content='1; url=pemantauan_udara_data.php?halaman={$current_halaman}'>";
        } else {
            echo "<div class='alert alert-danger alert-dismissable'>
                    <button type='button' class='close' data-dismiss='alert'>&times;</button>
                    ❌ Terjadi kesalahan. Tidak dapat menyimpan data.
                  </div>";
        }
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
        <h2>Data Pemantauan Udara &raquo; Edit Data</h2>
        <hr/>

        <form class="form-horizontal" action="" method="post">
            <input type="hidden" name="current_halaman" value="<?php echo $current_halaman; ?>">

            <div class="form-group">
                <label class="col-sm-3 control-label">Lokasi Pemantauan</label>
                <div class="col-sm-6">
                    <select name="id_lokasi" id="selectLokasi" class="form-control" required>
                        <option value="">-- Pilih Lokasi --</option>
                        <?php
                        $lokasi = mysqli_query($koneksi, "
                            SELECT id_lokasi, kode_lokasi, nama_lokasi, alamat_lokasi
                            FROM lokasi_pemantauan
                            ORDER BY nama_lokasi ASC
                        ");
                        while ($l = mysqli_fetch_array($lokasi)) {
                            $sel = ($l['id_lokasi'] == $row['id_lokasi']) ? "selected" : "";
                            echo "<option value='{$l['id_lokasi']}' $sel>
                                    {$l['kode_lokasi']} | {$l['nama_lokasi']} | {$l['alamat_lokasi']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Level Pemantauan</label>
                <div class="col-sm-3">
                    <select name="level" class="form-control" required>
                        <option value="">-- Pilih Level --</option>
                        <option value="Pusat" <?= ($row['level'] == 'Pusat') ? 'selected' : ''; ?>>Pusat</option>
                        <!-- <option value="Provinsi" <?= ($row['level'] == 'Provinsi') ? 'selected' : ''; ?>>Provinsi</option>
                        <option value="Kabupaten/Kota" <?= ($row['level'] == 'Kabupaten/Kota') ? 'selected' : ''; ?>>Kabupaten/Kota</option> -->
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Tanggal Pemantauan</label>
                <div class="col-sm-3">
                    <input type="date" name="tanggal_pemantauan" 
                           class="form-control"
                           value="<?php echo $row['tanggal_pemantauan']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Periode</label>
                <div class="col-sm-3">
                    <select name="periode_pemantauan" class="form-control" required>
                        <option value="">-- Pilih Periode --</option>
                        <option value="1" <?= ($row['periode_pemantauan'] == '1') ? 'selected' : ''; ?>>1</option>
                        <option value="2" <?= ($row['periode_pemantauan'] == '2') ? 'selected' : ''; ?>>2</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Durasi</label>
                <div class="col-sm-3">
                    <select name="durasi_pemantauan" class="form-control" required>
                        <option value="14 Hari" <?= ($row['durasi_pemantauan'] == '14 Hari') ? 'selected' : ''; ?>>14 Hari</option>
                        <option value="30 Hari" <?= ($row['durasi_pemantauan'] == '30 Hari') ? 'selected' : ''; ?>>30 Hari</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Metode</label>
                <div class="col-sm-4">
                    <select name="metode_pemantauan" class="form-control" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="Manual Passive" <?= ($row['metode_pemantauan'] == 'Manual Passive') ? 'selected' : ''; ?>>Manual Passive</option>
                        <option value="Manual Active" <?= ($row['metode_pemantauan'] == 'Manual Active') ? 'selected' : ''; ?>>Manual Active</option>
                        <option value="Automated" <?= ($row['metode_pemantauan'] == 'Automated') ? 'selected' : ''; ?>>Automated</option>
                        <option value="Continuous Monitoring" <?= ($row['metode_pemantauan'] == 'Continuous Monitoring') ? 'selected' : ''; ?>>Continuous Monitoring</option>
                        <option value="Hybrid" <?= ($row['metode_pemantauan'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Status SHU</label>
                <div class="col-sm-3">
                    <select name="shu" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="ADA SHU" <?= ($row['shu'] == 'ADA SHU') ? 'selected' : ''; ?>>ADA SHU</option>
                        <option value="TANPA SHU" <?= ($row['shu'] == 'TANPA SHU') ? 'selected' : ''; ?>>TANPA SHU</option>
                        <option value="SEDANG PROSES" <?= ($row['shu'] == 'SEDANG PROSES') ? 'selected' : ''; ?>>SEDANG PROSES</option>
                        <option value="BELUM ADA" <?= ($row['shu'] == 'BELUM ADA') ? 'selected' : ''; ?>>BELUM ADA</option>
                    </select>
                    <small class="help-block" style="color: #666; font-size: 12px;">
                        • Surat Hasil Uji
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <button type="submit" name="save" class="btn btn-primary btn-sm">Simpan</button>
                    <button type="reset" class="btn btn-warning btn-sm" onclick="resetForm()">Reset</button>
                    <a href="pemantauan_udara_data.php?halaman=<?php echo $current_halaman; ?>" 
                       class="btn btn-danger btn-sm">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#selectLokasi').select2({
        placeholder: "Cari lokasi...",
        allowClear: true,
        width: '100%'
    });

    // Reset form dengan benar
    function resetForm() {
        $('#selectLokasi').val('<?php echo $row['id_lokasi']; ?>').trigger('change');
        // Reset semua select ke nilai awal
        $('select').each(function() {
            var originalValue = $(this).find('option[selected]').val() || $(this).find('option:first').val();
            $(this).val(originalValue);
        });
        // Reset date ke nilai awal
        $('input[type="date"]').val('<?php echo $row['tanggal_pemantauan']; ?>');
    }
});
</script>

<?php include "footer.php"; ?>