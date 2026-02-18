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


$current_halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;


if (isset($_POST['add'])) {
    $id_pemantauan = mysqli_real_escape_string($koneksi, $_POST['id_pemantauan']);
    $no2  = mysqli_real_escape_string($koneksi, $_POST['no2']);
    $so2  = mysqli_real_escape_string($koneksi, $_POST['so2']);
    $pm25 = mysqli_real_escape_string($koneksi, $_POST['pm25']);
   
    $current_halaman = isset($_POST['current_halaman']) ? (int)$_POST['current_halaman'] : 1;

    // CEK VALID ID DI pemantauan_udara
    $cek_id = mysqli_query($koneksi, 
        "SELECT id_pemantauan 
         FROM pemantauan_udara 
         WHERE id_pemantauan='$id_pemantauan'"
    );

    if (mysqli_num_rows($cek_id) == 0) {
        echo '<div class="alert alert-danger">
                ❌ ID pemantauan tidak valid!
              </div>';
    } else {

        // CEK DUPLIKAT DI hasil_pemantauan
        $cek_duplikasi = mysqli_query($koneksi,
            "SELECT id_pemantauan 
             FROM hasil_pemantauan 
             WHERE id_pemantauan='$id_pemantauan'"
        );

        if (mysqli_num_rows($cek_duplikasi) > 0) {
            echo '<div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    ❌ <b>Gagal menyimpan!</b><br>
                    Data hasil pemantauan untuk ID ini <b>sudah ada</b>.
                  </div>';
        } else {
        
            // SIMPAN JIKA TIDAK DUPLIKAT
            // $insert = mysqli_query($koneksi, "
            //     INSERT INTO hasil_pemantauan (id_pemantauan, no2, so2, pm25)
            //     VALUES ('$id_pemantauan', '$no2', '$so2', '$pm25')
            // ") or die(mysqli_error($koneksi));

            $id_user = $_SESSION['id_user']; // Ambil ID user dari session

            $insert = mysqli_query($koneksi, "
                INSERT INTO hasil_pemantauan (id_user, id_pemantauan, no2, so2, pm25)
                VALUES ('$id_user', '$id_pemantauan', '$no2', '$so2', '$pm25')
            ") or die(mysqli_error($koneksi));

            if ($insert) {
                echo '<div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        ✅ Data hasil pemantauan berhasil disimpan!
                      </div>
                      <meta http-equiv="refresh" content="1; url=hasil_pemantauan_data.php?halaman=' . $current_halaman . '">';
            } else {
                echo '<div class="alert alert-danger">
                        ❌ Gagal menyimpan data hasil pemantauan.
                      </div>';
            }
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

<h2>Data Hasil Pemantauan &raquo; Tambah Data</h2>
<hr />

<form class="form-horizontal" action="" method="post">

    <input type="hidden" name="current_halaman" value="<?php echo $current_halaman; ?>">

    <div class="form-group">
        <label class="col-sm-3 control-label">Pilih Pemantauan Udara</label>
        <div class="col-sm-7">
            <select name="id_pemantauan" id="selectPemantauan" class="form-control" required>
                <option value="">-- Silahkan Pilih Lokasi --</option>
                <?php
                $sql = mysqli_query($koneksi, "
                    SELECT 
                        p.id_pemantauan,
                        l.kode_lokasi,
                        l.nama_lokasi,
                        l.alamat_lokasi,
                        p.tanggal_pemantauan
                    FROM pemantauan_udara p
                    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
                    ORDER BY p.tanggal_pemantauan DESC
                ");
                while ($row = mysqli_fetch_assoc($sql)) {
                    echo "<option value='{$row['id_pemantauan']}'>
                            {$row['kode_lokasi']} | {$row['tanggal_pemantauan']} | {$row['nama_lokasi']} | {$row['alamat_lokasi']}
                          </option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Kadar NO₂ (µg/m³)</label>
        <div class="col-sm-3">
            <input type="number" step="0.01" name="no2" class="form-control" placeholder="Masukkan kadar NO₂" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Kadar SO₂ (µg/m³)</label>
        <div class="col-sm-3">
            <input type="number" step="0.01" name="so2" class="form-control" placeholder="Masukkan kadar SO₂" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Kadar PM₂.₅ (µg/m³)</label>
        <div class="col-sm-3">
            <input type="number" step="0.01" name="pm25" class="form-control" placeholder="Masukkan kadar PM₂.₅" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="add" class="btn btn-sm btn-primary">Simpan</button>
            <button type="reset" class="btn btn-sm btn-warning">Reset</button>
            <a href="hasil_pemantauan_data.php?halaman=<?php echo $current_halaman; ?>" 
               class="btn btn-sm btn-danger">Kembali</a>
        </div>
    </div>

</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('#selectPemantauan').select2({
        placeholder: "Cari data pemantauan udara...",
        width: '100%',
        allowClear: true,
        language: {
            noResults: () => "Data tidak ditemukan",
            searching: () => "Mencari..."
        }
    });

    $("button[type='reset']").on("click", function() {
        $('#selectPemantauan').val('').trigger('change');
    });
});
</script>

<?php include "footer.php"; ?>
