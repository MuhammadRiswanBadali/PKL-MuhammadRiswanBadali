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
    
    // 5. Validasi khusus Kalimantan Selatan
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

if (!isset($_GET['id_lokasi']) || empty($_GET['id_lokasi'])) {
?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ⚠️ ID lokasi tidak ditemukan!
    </div>
<?php
    exit;
}

$id_lokasi = $_GET['id_lokasi'];

$sql = mysqli_query($koneksi, "SELECT * FROM lokasi_pemantauan WHERE id_lokasi='$id_lokasi'");
if (mysqli_num_rows($sql) == 0) {
?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ⚠️ Data lokasi tidak ditemukan!
    </div>
<?php
    exit;
} 

$row = mysqli_fetch_assoc($sql);

if (isset($_POST['save'])) {

    $kode_lokasi     = trim($_POST['kode_lokasi']);
    $nama_lokasi     = trim($_POST['nama_lokasi']);
    $alamat_lokasi   = trim($_POST['alamat_lokasi']);
    $kabupaten_kota  = trim($_POST['kabupaten_kota']);
    $provinsi        = trim($_POST['provinsi']);
    $latitude        = trim($_POST['latitude']);
    $longitude       = trim($_POST['longitude']);
    $peruntukan      = trim($_POST['peruntukan']);

    // ===========================================
    // VALIDASI KOORDINAT SEBELUM UPDATE
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
        // Cek jika kode lokasi berubah dan sudah digunakan
        if ($kode_lokasi != $row['kode_lokasi']) {
            $cek = mysqli_query($koneksi, "SELECT id_lokasi FROM lokasi_pemantauan WHERE kode_lokasi='$kode_lokasi'");
            if (mysqli_num_rows($cek) > 0) {
                echo "
                    <div class='alert alert-danger alert-dismissable'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        ⚠️ Kode Lokasi <b>$kode_lokasi</b> sudah digunakan oleh lokasi lain!
                    </div>
                ";
            } else {
                $update = mysqli_query($koneksi, 
                    "UPDATE lokasi_pemantauan SET 
                        kode_lokasi='$kode_lokasi',
                        nama_lokasi='$nama_lokasi',
                        alamat_lokasi='$alamat_lokasi',
                        kabupaten_kota='$kabupaten_kota',
                        provinsi='$provinsi',
                        latitude='$latitude',
                        longitude='$longitude',
                        peruntukan='$peruntukan'
                    WHERE id_lokasi='$id_lokasi'"
                );
                
                if ($update) {
                    echo "
                        <div class='alert alert-success alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                            ✅ Data lokasi berhasil diperbarui.
                        </div>
                        <meta http-equiv='refresh' content='1; url=lokasi_pemantauan_data.php?halaman=$current_halaman'>
                    ";
                } else {
                    echo "
                        <div class='alert alert-danger alert-dismissable'>
                            <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                            ❌ Gagal memperbarui data. Silakan coba lagi.
                        </div>
                    ";
                }
            }
        } else {
            // Kode lokasi tidak berubah, langsung update
            $update = mysqli_query($koneksi, 
                "UPDATE lokasi_pemantauan SET 
                    nama_lokasi='$nama_lokasi',
                    alamat_lokasi='$alamat_lokasi',
                    kabupaten_kota='$kabupaten_kota',
                    provinsi='$provinsi',
                    latitude='$latitude',
                    longitude='$longitude',
                    peruntukan='$peruntukan'
                WHERE id_lokasi='$id_lokasi'"
            );
            
            if ($update) {
                echo "
                    <div class='alert alert-success alert-dismissable'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        ✅ Data lokasi berhasil diperbarui.
                    </div>
                    <meta http-equiv='refresh' content='1; url=lokasi_pemantauan_data.php?halaman=$current_halaman'>
                ";
            } else {
                echo "
                    <div class='alert alert-danger alert-dismissable'>
                        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                        ❌ Gagal memperbarui data. Silakan coba lagi.
                    </div>
                ";
            }
        }
    }
}
?>

<!-- =========================================== -->
<!-- JAVASCRIPT UNTUK VALIDASI REAL-TIME -->
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

<div class="container-fluid">
    <div class="content-fluid">
        <h2>Data Lokasi Pemantauan &raquo; Edit Data</h2>
        <hr/>

        <form class="form-horizontal" action="" method="post" onsubmit="return validasiFormKoordinat()">

            <input type="hidden" name="current_halaman" value="<?= $current_halaman; ?>">

            <div class="form-group">
                <label class="col-sm-3 control-label">Kode Lokasi</label>
                <div class="col-sm-3">
                    <input type="text" name="kode_lokasi" value="<?= htmlspecialchars($row['kode_lokasi']); ?>" 
                           class="form-control" pattern="U\d-KS-\d{2}-\d{3}" 
                           title="Format: U1-KS-72-004" required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Nama Lokasi</label>
                <div class="col-sm-3">
                    <select name="nama_lokasi" class="form-control" required>
                        <option value="">-- Pilih Nama Lokasi --</option>
                        <option value="Transportasi"            <?= ($row['nama_lokasi'] == 'Transportasi') ? 'selected' : ''; ?>>Transportasi</option>
                        <option value="Industri/Agro Industri" <?= ($row['nama_lokasi'] == 'Industri/Agro Industri') ? 'selected' : ''; ?>>Industri/Agro Industri</option>
                        <option value="Perumahan"              <?= ($row['nama_lokasi'] == 'Perumahan') ? 'selected' : ''; ?>>Perumahan</option>
                        <option value="Perkantoran/Komersial"  <?= ($row['nama_lokasi'] == 'Perkantoran/Komersial') ? 'selected' : ''; ?>>Perkantoran/Komersial</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Alamat Lokasi</label>
                <div class="col-sm-5">
                    <textarea name="alamat_lokasi" class="form-control" required><?= htmlspecialchars($row['alamat_lokasi']); ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Kabupaten/Kota</label>
                <div class="col-sm-4">
                    <select name="kabupaten_kota" class="form-control" required>
                        <option value="">-- Pilih Kabupaten/Kota --</option>
                        <option value="Kota Banjarbaru" <?= ($row['kabupaten_kota'] == 'Kota Banjarbaru') ? 'selected' : ''; ?>>Kota Banjarbaru</option>
                        <option value="Kota Banjarmasin" <?= ($row['kabupaten_kota'] == 'Kota Banjarmasin') ? 'selected' : ''; ?>>Kota Banjarmasin</option>
                        <option value="Kabupaten Balangan" <?= ($row['kabupaten_kota'] == 'Kabupaten Balangan') ? 'selected' : ''; ?>>Kabupaten Balangan</option>
                        <option value="Kabupaten Tanah Bumbu" <?= ($row['kabupaten_kota'] == 'Kabupaten Tanah Bumbu') ? 'selected' : ''; ?>>Kabupaten Tanah Bumbu</option>
                        <option value="Kabupaten Tabalong" <?= ($row['kabupaten_kota'] == 'Kabupaten Tabalong') ? 'selected' : ''; ?>>Kabupaten Tabalong</option>
                        <option value="Kabupaten Hulu Sungai Utara" <?= ($row['kabupaten_kota'] == 'Kabupaten Hulu Sungai Utara') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Utara</option>
                        <option value="Kabupaten Hulu Sungai Tengah" <?= ($row['kabupaten_kota'] == 'Kabupaten Hulu Sungai Tengah') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Tengah</option>
                        <option value="Kabupaten Hulu Sungai Selatan" <?= ($row['kabupaten_kota'] == 'Kabupaten Hulu Sungai Selatan') ? 'selected' : ''; ?>>Kabupaten Hulu Sungai Selatan</option>
                        <option value="Kabupaten Tapin" <?= ($row['kabupaten_kota'] == 'Kabupaten Tapin') ? 'selected' : ''; ?>>Kabupaten Tapin</option>
                        <option value="Kabupaten Barito Kuala" <?= ($row['kabupaten_kota'] == 'Kabupaten Barito Kuala') ? 'selected' : ''; ?>>Kabupaten Barito Kuala</option>
                        <option value="Kabupaten Banjar" <?= ($row['kabupaten_kota'] == 'Kabupaten Banjar') ? 'selected' : ''; ?>>Kabupaten Banjar</option>
                        <option value="Kabupaten Kotabaru" <?= ($row['kabupaten_kota'] == 'Kabupaten Kotabaru') ? 'selected' : ''; ?>>Kabupaten Kotabaru</option>
                        <option value="Kabupaten Tanah Laut" <?= ($row['kabupaten_kota'] == 'Kabupaten Tanah Laut') ? 'selected' : ''; ?>>Kabupaten Tanah Laut</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Provinsi</label>
                <div class="col-sm-4">
                    <input type="text" name="provinsi" value="<?= htmlspecialchars($row['provinsi']); ?>" class="form-control" readonly required>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Latitude</label>
                <div class="col-sm-3">
                    <input type="text" name="latitude" value="<?= htmlspecialchars($row['latitude']); ?>" 
                           class="form-control" pattern="-?\d+(\.\d{1,6})?"
                           title="Format: -3.123456 (6 digit desimal, negatif untuk KalSel)"
                           placeholder="Contoh: -3.458667" required>
                    <small class="help-block" style="color: #666; font-size: 12px;">
                        • Format: -3.123456<br>
                        • Rentang KalSel: -4.0° sampai -2.0°
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Longitude</label>
                <div class="col-sm-3">
                    <input type="text" name="longitude" value="<?= htmlspecialchars($row['longitude']); ?>" 
                           class="form-control" pattern="\d+(\.\d{1,6})?"
                           title="Format: 114.123456 (6 digit desimal)"
                           placeholder="Contoh: 114.842944" required>
                    <small class="help-block" style="color: #666; font-size: 12px;">
                        • Format: 114.123456<br>
                        • Rentang KalSel: 114.0° sampai 117.0°
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Peruntukan</label>
                <div class="col-sm-3">
                    <select name="peruntukan" class="form-control" required>
                        <option value="">-- Pilih Peruntukan --</option>
                        <option value="PERKANTORAN"  <?= ($row['peruntukan'] == 'PERKANTORAN') ? 'selected' : ''; ?>>Perkantoran</option>
                        <option value="PEMUKIMAN"    <?= ($row['peruntukan'] == 'PEMUKIMAN') ? 'selected' : ''; ?>>Pemukiman</option>
                        <option value="INDUSTRI"     <?= ($row['peruntukan'] == 'INDUSTRI') ? 'selected' : ''; ?>>Industri</option>
                        <option value="TRANSPORTASI" <?= ($row['peruntukan'] == 'TRANSPORTASI') ? 'selected' : ''; ?>>Transportasi</option>
                    </select>
                </div>

                <div class="col-sm-3">
                    <b>Peruntukan Saat Ini:</b>  
                    <span class="label label-success"><?= htmlspecialchars($row['peruntukan']); ?></span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">&nbsp;</label>
                <div class="col-sm-6">
                    <button type="submit" name="save" class="btn btn-sm btn-primary">Simpan</button>
                    <button type="reset" class="btn btn-sm btn-warning" onclick="resetForm()">Reset</button>
                    <a href="lokasi_pemantauan_data.php?halaman=<?= $current_halaman; ?>" class="btn btn-sm btn-danger">Kembali</a>
                </div>
            </div>

        </form>
    </div>
</div>

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

// Fungsi reset form
function resetForm() {
    // Reset semua select ke nilai awal (data asli)
    document.querySelector('select[name="kabupaten_kota"]').value = '<?= addslashes($row['kabupaten_kota']); ?>';
    document.querySelector('select[name="nama_lokasi"]').value = '<?= addslashes($row['nama_lokasi']); ?>';
    document.querySelector('select[name="peruntukan"]').value = '<?= addslashes($row['peruntukan']); ?>';
    
    // Reset koordinat styling
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