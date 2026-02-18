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

// ===========================================
// FUNGSI VALIDASI KOORDINAT (DITAMBAHKAN)
// ===========================================
function validasiKoordinat($latitude, $longitude) {
    $errors = [];
    
    // 1. Bersihkan input
    $latitude = trim(str_replace(',', '.', $latitude));
    $longitude = trim(str_replace(',', '.', $longitude));
    
    // 2. Validasi format latitude
    if (!preg_match('/^-?\d+(\.\d{1,6})?$/', $latitude)) {
        $errors[] = "Format latitude tidak valid. Gunakan: -3.123456";
    }
    
    // 3. Validasi format longitude
    if (!preg_match('/^-?\d+(\.\d{1,6})?$/', $longitude)) {
        $errors[] = "Format longitude tidak valid. Gunakan: 114.123456";
    }
    
    // 4. Validasi rentang global
    $lat_num = floatval($latitude);
    $lng_num = floatval($longitude);
    
    if ($lat_num < -90 || $lat_num > 90) {
        $errors[] = "Latitude harus antara -90 dan 90 derajat";
    }
    
    if ($lng_num < -180 || $lng_num > 180) {
        $errors[] = "Longitude harus antara -180 dan 180 derajat";
    }
    
    // 5. Validasi khusus Kalimantan Selatan (berdasarkan data Anda)
    if ($lat_num > -2.0 || $lat_num < -4.0) {
        $errors[] = "Latitude di luar wilayah Kalimantan Selatan (harus antara -4.0° dan -2.0°)";
    }
    
    if ($lng_num < 114.0 || $lng_num > 117.0) {
        $errors[] = "Longitude di luar wilayah Kalimantan Selatan (harus antara 114.0° dan 117.0°)";
    }
    
    // 6. Validasi konsistensi (Latitude KalSel harus negatif)
    if ($lat_num > 0) {
        $errors[] = "Latitude harus negatif untuk lokasi di Kalimantan Selatan";
    }
    
    // 7. Validasi bujur timur (Indonesia)
    if ($lng_num < 0) {
        $errors[] = "Longitude harus positif untuk lokasi di Indonesia (Bujur Timur)";
    }
    
    return $errors;
}

if (isset($_POST['add'])) {

    $kode_lokasi    = trim($_POST['kode_lokasi']);
    $nama_lokasi    = trim($_POST['nama_lokasi']);
    $alamat_lokasi  = trim($_POST['alamat_lokasi']);
    $kabupaten_kota = trim($_POST['kabupaten_kota']);
    $provinsi       = trim($_POST['provinsi']);
    $latitude       = trim($_POST['latitude']);
    $longitude      = trim($_POST['longitude']);
    $peruntukan     = trim($_POST['peruntukan']);

    // ===========================================
    // VALIDASI KOORDINAT SEBELUM CEK DATABASE
    // ===========================================
    $error_koordinat = validasiKoordinat($latitude, $longitude);
    
    if (!empty($error_koordinat)) {
        echo "
            <div class='alert alert-danger alert-dismissable'>
                <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                <h4><i class='icon fa fa-warning'></i> Validasi Koordinat Gagal!</h4>
                <ul style='margin-bottom: 0;'>";
        foreach ($error_koordinat as $error) {
            echo "<li>$error</li>";
        }
        echo "
                </ul>
            </div>
        ";
    } else {
        // Lanjutkan validasi kode lokasi seperti sebelumnya
        $cek = mysqli_query($koneksi, "SELECT id_lokasi FROM lokasi_pemantauan WHERE kode_lokasi='$kode_lokasi'");
        if (mysqli_num_rows($cek) > 0) {
            echo "
                <div class='alert alert-danger alert-dismissable'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                    ⚠️ Kode Lokasi <b>$kode_lokasi</b> sudah terdaftar!
                </div>
            ";
        } else {

            $id_user = $_SESSION['id_user']; // Ambil ID user dari session

            $insert = mysqli_query($koneksi, "
                INSERT INTO lokasi_pemantauan 
                (id_user, kode_lokasi, nama_lokasi, alamat_lokasi, kabupaten_kota, provinsi, latitude, longitude, peruntukan)
                VALUES
                ('$id_user', '$kode_lokasi', '$nama_lokasi', '$alamat_lokasi', '$kabupaten_kota', '$provinsi', '$latitude', '$longitude', '$peruntukan')
            ");

            if ($insert) {
                echo "
                    <div class='alert alert-success alert-dismissable'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        ✅ Data lokasi berhasil disimpan!
                    </div>
                    <meta http-equiv='refresh' content='1; url=lokasi_pemantauan_data.php?halaman=$current_halaman'>
                ";
            } else {
                echo "
                    <div class='alert alert-danger alert-dismissable'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        ❌ Gagal menyimpan data lokasi. Silakan coba lagi!
                    </div>
                ";
            }
        }
    }
}
?>

<!-- =========================================== -->
<!-- TAMBAHKAN JAVASCRIPT UNTUK VALIDASI REAL-TIME -->
<!-- =========================================== -->
<script>
function formatCoordinate(input) {
    // Ganti koma dengan titik
    input.value = input.value.replace(/,/g, '.');
    
    // Hapus karakter selain angka, titik, dan minus
    input.value = input.value.replace(/[^0-9.-]/g, '');
    
    // Untuk latitude, pastikan hanya satu minus di awal
    if (input.name === 'latitude') {
        // Hapus semua minus
        let value = input.value.replace(/-/g, '');
        // Tambahkan minus di depan jika perlu
        if (input.value.includes('-')) {
            value = '-' + value;
        }
        // Hapus minus di tengah
        if (value.lastIndexOf('-') > 0) {
            value = '-' + value.replace(/-/g, '');
        }
        input.value = value;
    }
    
    // Hanya satu titik desimal
    const parts = input.value.split('.');
    if (parts.length > 2) {
        input.value = parts[0] + '.' + parts.slice(1).join('');
    }
}

function validateCoordinate(input) {
    const value = input.value.trim();
    const name = input.name;
    
    if (!value) return true;
    
    // Validasi format
    let regex;
    if (name === 'latitude') {
        regex = /^-?\d+(\.\d{1,6})?$/;
    } else {
        regex = /^\d+(\.\d{1,6})?$/;
    }
    
    if (!regex.test(value)) {
        input.style.borderColor = '#f56954';
        input.style.backgroundColor = '#fff5f5';
        return false;
    }
    
    // Validasi numerik
    const numValue = parseFloat(value);
    
    if (name === 'latitude') {
        if (numValue > -2.0 || numValue < -4.0) {
            input.style.borderColor = '#f56954';
            input.style.backgroundColor = '#fff5f5';
            input.title = 'Latitude harus antara -4.0° dan -2.0° untuk Kalimantan Selatan';
            return false;
        }
    } else {
        if (numValue < 114.0 || numValue > 117.0) {
            input.style.borderColor = '#f56954';
            input.style.backgroundColor = '#fff5f5';
            input.title = 'Longitude harus antara 114.0° dan 117.0° untuk Kalimantan Selatan';
            return false;
        }
    }
    
    // Jika valid
    input.style.borderColor = '#00a65a';
    input.style.backgroundColor = '#f0fff4';
    return true;
}

// Event listeners untuk real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.querySelector('input[name="latitude"]');
    const lngInput = document.querySelector('input[name="longitude"]');
    
    if (latInput) {
        latInput.addEventListener('input', function() {
            formatCoordinate(this);
            validateCoordinate(this);
        });
        latInput.addEventListener('blur', function() {
            validateCoordinate(this);
        });
    }
    
    if (lngInput) {
        lngInput.addEventListener('input', function() {
            formatCoordinate(this);
            validateCoordinate(this);
        });
        lngInput.addEventListener('blur', function() {
            validateCoordinate(this);
        });
    }
});
</script>

<h2>Data Lokasi Pemantauan &raquo; Tambah Data</h2>
<hr />

<form class="form-horizontal" action="" method="post" onsubmit="return validasiFormKoordinat()">

    <input type="hidden" name="current_halaman" value="<?php echo $current_halaman; ?>">

    <div class="form-group">
        <label class="col-sm-3 control-label">Kode Lokasi</label>
        <div class="col-sm-3">
            <input type="text" name="kode_lokasi" class="form-control" 
                   pattern="U\d-KS-\d{2}-\d{3}"
                   title="Format: U1-KS-72-004 (U[angka]-KS-[2 digit]-[3 digit])"
                   placeholder="Misal: U1-KS-72-004" required>
            <small class="help-block" style="color: #666; font-size: 12px;">
                • Format & Contoh : U1-KS-72-004
            </small>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Nama Lokasi</label>
        <div class="col-sm-4">
            <select name="nama_lokasi" class="form-control" required>
                <option value="">-- Pilih Nama Lokasi --</option>
                <option value="Transportasi">Transportasi</option>
                <option value="Industri/Agro Industri">Industri/Agro Industri</option>
                <option value="Perumahan">Perumahan</option>
                <option value="Perkantoran/Komersial">Perkantoran/Komersial</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Alamat Lokasi</label>
        <div class="col-sm-5">
            <textarea name="alamat_lokasi" class="form-control" required placeholder="Alamat lokasi"></textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Kabupaten/Kota</label>
        <div class="col-sm-4">
            <select name="kabupaten_kota" class="form-control" required>
                <option value="">-- Pilih Kabupaten/Kota --</option>
                <option value="Kota Banjarbaru">Kota Banjarbaru</option>
                <option value="Kota Banjarmasin">Kota Banjarmasin</option>
                <option value="Kabupaten Balangan">Kabupaten Balangan</option>
                <option value="Kabupaten Tanah Bumbu">Kabupaten Tanah Bumbu</option>
                <option value="Kabupaten Tabalong">Kabupaten Tabalong</option>
                <option value="Kabupaten Hulu Sungai Utara">Kabupaten Hulu Sungai Utara</option>
                <option value="Kabupaten Hulu Sungai Tengah">Kabupaten Hulu Sungai Tengah</option>
                <option value="Kabupaten Hulu Sungai Selatan">Kabupaten Hulu Sungai Selatan</option>
                <option value="Kabupaten Tapin">Kabupaten Tapin</option>
                <option value="Kabupaten Barito Kuala">Kabupaten Barito Kuala</option>
                <option value="Kabupaten Banjar">Kabupaten Banjar</option>
                <option value="Kabupaten Kotabaru">Kabupaten Kotabaru</option>
                <option value="Kabupaten Tanah Laut">Kabupaten Tanah Laut</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Provinsi</label>
        <div class="col-sm-4">
            <input type="text" name="provinsi" class="form-control" value="Kalimantan Selatan" readonly required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Latitude</label>
        <div class="col-sm-3">
            <input type="text" name="latitude" class="form-control" 
                   placeholder="Contoh: -3.458667"
                   pattern="-?\d+(\.\d{1,6})?"
                   title="Format: -3.123456 (6 digit desimal, negatif untuk KalSel)"
                   required>
            <small class="help-block" style="color: #666; font-size: 12px;">
                • Format: -3.123456<br>
                • Rentang KalSel: -4.0° sampai -2.0°
            </small>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Longitude</label>
        <div class="col-sm-3">
            <input type="text" name="longitude" class="form-control" 
                   placeholder="Contoh: 114.842944"
                   pattern="\d+(\.\d{1,6})?"
                   title="Format: 114.123456 (6 digit desimal)"
                   required>
            <small class="help-block" style="color: #666; font-size: 12px;">
                • Format: 114.123456<br>
                • Rentang KalSel: 114.0° sampai 117.0°
            </small>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">Peruntukan</label>
        <div class="col-sm-4">
            <select name="peruntukan" class="form-control" required>
                <option value="">-- Pilih Peruntukan --</option>
                <option value="PERKANTORAN">Perkantoran</option>
                <option value="PEMUKIMAN">Pemukiman</option>
                <option value="INDUSTRI">Industri</option>
                <option value="TRANSPORTASI">Transportasi</option>
            </select>
        </div>
    </div>

    <!-- Tombol -->
    <div class="form-group">
        <label class="col-sm-3 control-label">&nbsp;</label>
        <div class="col-sm-6">
            <button type="submit" name="add" class="btn btn-sm btn-primary">Simpan</button>
            <button type="reset" class="btn btn-sm btn-warning" onclick="resetKoordinatValidasi()">Reset</button>
            <a href="lokasi_pemantauan_data.php?halaman=<?php echo $current_halaman; ?>" 
               class="btn btn-sm btn-danger">Kembali</a>
        </div>
    </div>

</form>

<script>
// Fungsi validasi form sebelum submit
function validasiFormKoordinat() {
    const latInput = document.querySelector('input[name="latitude"]');
    const lngInput = document.querySelector('input[name="longitude"]');
    
    const isLatValid = validateCoordinate(latInput);
    const isLngValid = validateCoordinate(lngInput);
    
    if (!isLatValid || !isLngValid) {
        alert('Mohon perbaiki data koordinat terlebih dahulu!');
        if (!isLatValid) latInput.focus();
        else lngInput.focus();
        return false;
    }
    
    return true;
}

// Fungsi reset styling koordinat
function resetKoordinatValidasi() {
    const latInput = document.querySelector('input[name="latitude"]');
    const lngInput = document.querySelector('input[name="longitude"]');
    
    if (latInput) {
        latInput.style.borderColor = '';
        latInput.style.backgroundColor = '';
        latInput.title = '';
    }
    
    if (lngInput) {
        lngInput.style.borderColor = '';
        lngInput.style.backgroundColor = '';
        lngInput.title = '';
    }
}
</script>

<?php include "footer.php"; ?>