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

if (isset($_POST['add'])) {

    $id_lokasi          = $_POST['id_lokasi'];
    $periode_pemantauan = $_POST['periode_pemantauan'];
    $tanggal_pemantauan = $_POST['tanggal_pemantauan'];
    $metode_pemantauan  = $_POST['metode_pemantauan'];
    $shu                = $_POST['shu'];
    $durasi_pemantauan  = $_POST['durasi_pemantauan'];
    $level              = $_POST['level'];

    $current_halaman = $_POST['current_halaman'];

    $cek = mysqli_query($koneksi,
        "SELECT id_pemantauan 
         FROM pemantauan_udara 
         WHERE id_lokasi = '$id_lokasi'
         AND tanggal_pemantauan = '$tanggal_pemantauan'"
    );

    if (mysqli_num_rows($cek) > 0) {
?>
        <div class="alert alert-danger alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            ❌ <b>Gagal menyimpan!</b><br>
            Tanggal <b><?= date('d-m-Y', strtotime($tanggal_pemantauan)); ?></b>
            sudah terdaftar pada lokasi ini. Silakan gunakan tanggal lain.
        </div>
<?php
    } else {

        $id_user = $_SESSION['id_user']; // Ambil ID user dari session

        $insert = mysqli_query($koneksi, 
            "INSERT INTO pemantauan_udara 
                (id_user, id_lokasi, periode_pemantauan, tanggal_pemantauan, metode_pemantauan, shu, durasi_pemantauan, level)
             VALUES 
                ('$id_user', '$id_lokasi', '$periode_pemantauan', '$tanggal_pemantauan', '$metode_pemantauan', '$shu', '$durasi_pemantauan', '$level')"
        );


        if ($insert) {
?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                ✅ Data pemantauan udara berhasil disimpan!
            </div>

            <meta http-equiv="refresh" content="1; url=pemantauan_udara_data.php?halaman=<?= $current_halaman; ?>" />
<?php
        } else {
?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                ❌ Terjadi kesalahan, data gagal disimpan.
            </div>
<?php
        }
    }
}
?>

<h2>Data Pemantauan Udara &raquo; Tambah Data</h2> 
<hr />

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

<form class="form-horizontal" action="" method="post">

    <input type="hidden" name="current_halaman" value="<?= $current_halaman; ?>">

    <div class="form-group">
        <label class="col-sm-3 control-label">Lokasi Pemantauan</label>
        <div class="col-sm-6">
            <select name="id_lokasi" id="selectLokasi" class="form-control" required>
                <option value="">-- Pilih Lokasi Pemantauan --</option>
                <?php
                $lokasiQuery = mysqli_query($koneksi, 
                    "SELECT id_lokasi, kode_lokasi, nama_lokasi, alamat_lokasi 
                     FROM lokasi_pemantauan 
                     ORDER BY nama_lokasi ASC");
                while ($row = mysqli_fetch_assoc($lokasiQuery)) {
                    echo "<option value='{$row['id_lokasi']}'>
                            {$row['kode_lokasi']} | {$row['nama_lokasi']} | {$row['alamat_lokasi']}
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
                <option value="Pusat">Pusat</option>
                <!-- <option value="Provinsi">Provinsi</option>
                <option value="Kabupaten/Kota">Kabupaten/Kota</option> -->
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Tanggal Pemantauan</label>
        <div class="col-sm-3">
            <input type="date" name="tanggal_pemantauan" class="form-control" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Periode</label>
        <div class="col-sm-3">
            <select name="periode_pemantauan" class="form-control" required>
                <option value="">-- Pilih Periode --</option>
                <option value="1">1</option>
                <option value="2">2</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Durasi</label>
        <div class="col-sm-3">
            <select name="durasi_pemantauan" class="form-control" required>
                <option value="">-- Pilih Durasi --</option>
                <option value="14 Hari">14 Hari</option>
                <option value="30 Hari">30 Hari</option>
            </select>
            <!-- <small class="help-block" style="color: #666; font-size: 12px;">
                • Lama waktu pemantauan
            </small> -->
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Metode</label>
        <div class="col-sm-4">
            <select name="metode_pemantauan" class="form-control" required>
                <option value="">-- Pilih Metode --</option>
                <option value="Manual Passive">Manual Passive</option>
                <option value="Manual Active">Manual Active</option>
                <option value="Automated">Automated</option>
                <option value="Continuous Monitoring">Continuous Monitoring</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Status SHU</label>
        <div class="col-sm-3">
            <select name="shu" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="ADA SHU">ADA SHU</option>
                <option value="TANPA SHU">TANPA SHU</option>
                <option value="SEDANG PROSES">SEDANG PROSES</option>
                <option value="BELUM ADA">BELUM ADA</option>
            </select>
            <small class="help-block" style="color: #666; font-size: 12px;">
                • Surat Hasil Uji
            </small>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="add" class="btn btn-sm btn-primary">Simpan</button>
            <button type="reset" class="btn btn-sm btn-warning" onclick="resetForm()">Reset</button>
            <a href="pemantauan_udara_data.php?halaman=<?= $current_halaman; ?>" class="btn btn-sm btn-danger">Kembali</a>
        </div>
    </div>

</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {

    $('#selectLokasi').select2({
        placeholder: "Ketik untuk mencari lokasi...",
        allowClear: true,
        width: '100%',
        language: {
            noResults: () => "Lokasi tidak ditemukan",
            searching: () => "Mencari..."
        }
    });

    // Reset form dengan benar
    function resetForm() {
        $('#selectLokasi').val('').trigger('change');
        // Reset semua select ke nilai default
        $('select').each(function() {
            $(this).val('');
        });
        // Reset date
        $('input[type="date"]').val('');
    }

    $('button[type="reset"]').on('click', function() {
        resetForm();
    });

});
</script>

<?php include "footer.php"; ?>