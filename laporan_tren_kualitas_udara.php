<?php  
include "header-admin.php"; 
include "sessionlogin.php"; 
include "koneksi.php"; 
?>

<style>
.container-fluid {
    width: 100%;
    margin: 0;
    padding: 10px 20px;
}
.table th {
    background-color: #007bff;
    color: white;
    text-align: center;
    vertical-align: middle;
}
.table td {
    vertical-align: middle;
    text-align: center;
}
.filter-row {
    margin-bottom: 10px;
}

.filter-info-box {
    background: #e9ecef;
    padding: 10px 15px;
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
    border-radius: 4px;
}
.filter-label {
    font-weight: bold;
    color: #495057;
}
.filter-value {
    font-weight: bold;
}
.filter-separator {
    margin: 0 10px;
    color: #6c757d;
}

.ikon-naik {
    color: #dc3545;
    font-weight: bold;
    margin-left: 5px;
    display: inline-block;
}
.ikon-turun {
    color: #28a745;
    font-weight: bold;
    margin-left: 5px;
    display: inline-block;
}
.ikon-stabil {
    color: #6c757d;
    font-weight: normal;
    margin-left: 5px;
    font-size: 0.9em;
}
.nilai-polutan {
    font-weight: bold;
    display: inline-block;
}
.persen-change {
    font-size: 0.75em;
    margin-left: 2px;
}
.badge-anomali {
    background-color: #dc3545;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7em;
    font-weight: bold;
    margin-left: 5px;
    display: inline-block;
}
.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-left: 5px solid #007bff;
    margin-bottom: 20px;
}

/* CSS untuk ringkasan anomali */
.ringkasan-anomali {
   
    border: 2px solid #ff0707;
    border-radius: 10px;
    padding: 20px;
    margin-top: 30px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.ringkasan-header {
    background: #dc3545;
    color: white;
    padding: 10px 15px;
    border-radius: 8px 8px 0 0;
    margin: -20px -20px 20px -20px;
    font-size: 18px;
    font-weight: bold;
}
.ringkasan-item {
    background: white;
    border-left: 5px solid #dc3545;
    padding: 12px 15px;
    margin-bottom: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.ringkasan-item strong {
    color: #dc3545;
}
.ringkasan-footer {
    background: #f8f9fa;
    padding: 12px 15px;
    margin-top: 15px;
    border-radius: 5px;
    font-style: italic;
    border-left: 5px solid #007bff;
}
</style>

<div class="container-fluid">
    <h2 align="center" style=font-weight:bold>Laporan Tren Kualitas Udara</h2>
    <hr/>

    <div class="info-box">
       <b>Laporan Tren Kualitas udara ini diurutkan dari waktu terlama ke terbaru dan menampilkan persentase kenaikan dari waktu ke waktu</b>
    </div>

    <form method="get" action="">
        <div class="row filter-row">

            <div class="col-sm-4">
                <label>Kabupaten / Kota</label>
                <select name="kabupaten" class="form-control">
                    <option value="">-- Semua Kabupaten/Kota --</option>
                    <?php
                    $kabQuery = mysqli_query($koneksi, "SELECT DISTINCT kabupaten_kota FROM lokasi_pemantauan ORDER BY kabupaten_kota ASC");
                    while ($k = mysqli_fetch_assoc($kabQuery)) {
                        $selected = (@$_GET['kabupaten'] == $k['kabupaten_kota']) ? 'selected' : '';
                        echo "<option value='{$k['kabupaten_kota']}' $selected>{$k['kabupaten_kota']}</option>";
                    }
                    ?>
                </select>
            </div>

         
            <div class="col-sm-4">
                <label>Peruntukan</label>
                <select name="peruntukan" class="form-control">
                    <option value="">-- Semua Peruntukan --</option>
                    <option value="PERKANTORAN" <?= (@$_GET['peruntukan']=='PERKANTORAN') ? 'selected' : '' ?>>Perkantoran</option>
                    <option value="PEMUKIMAN" <?= (@$_GET['peruntukan']=='PEMUKIMAN') ? 'selected' : '' ?>>Pemukiman</option>
                    <option value="INDUSTRI" <?= (@$_GET['peruntukan']=='INDUSTRI') ? 'selected' : '' ?>>Industri</option>
                    <option value="TRANSPORTASI" <?= (@$_GET['peruntukan']=='TRANSPORTASI') ? 'selected' : '' ?>>Transportasi</option>
                </select>
            </div>

            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="glyphicon glyphicon-search"></span> Tampilkan
                </button>
            </div>

            <div class="col-sm-2">
                <label>&nbsp;</label><br>
                <a href="laporan_tren_kualitas_udara.php" class="btn btn-default btn-block">
                    <span class="glyphicon glyphicon-refresh"></span> Hapus
                </a>
            </div>

        </div>
    </form>

    <hr/>

<?php

if (isset($_GET['kabupaten']) || isset($_GET['peruntukan'])) {

    $kabupaten  = $_GET['kabupaten'] ?? '';
    $peruntukan = $_GET['peruntukan'] ?? '';

    $filter_terpakai = [];
    $filter_display = "";

    if (!empty($kabupaten)) {
        $filter_terpakai[] = "Kabupaten/Kota: <span class='filter-value'>" . htmlspecialchars($kabupaten) . "</span>";
    }

    if (!empty($peruntukan)) {
        $filter_terpakai[] = "Peruntukan: <span class='filter-value'>" . htmlspecialchars($peruntukan) . "</span>";
    }

    if (!empty($filter_terpakai)) {
        $filter_display = implode("<span class='filter-separator'> | </span>", $filter_terpakai);
    }
 
    $filter = "WHERE 1=1 ";
    if (!empty($kabupaten))  $filter .= "AND l.kabupaten_kota = '$kabupaten' ";
    if (!empty($peruntukan)) $filter .= "AND l.peruntukan = '$peruntukan' ";

    $sql = "
        SELECT 
            p.tanggal_pemantauan,
            l.kode_lokasi,
            l.nama_lokasi,
            l.alamat_lokasi,
            l.kabupaten_kota,
            l.provinsi,
            l.peruntukan,
            h.no2,
            h.so2,
            h.pm25
        FROM hasil_pemantauan h
        JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
        JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
        $filter
        ORDER BY 
            l.kabupaten_kota ASC,
            l.alamat_lokasi ASC,
            p.tanggal_pemantauan ASC
    ";

    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) == 0) {
        echo "<div class='alert alert-warning'>⚠️ Tidak ada data ditemukan.</div>";
        
        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }
    } else {

        if (!empty($filter_display)) {
            echo "<div class='filter-info-box'>
                    <span class='filter-label'>Filter yang digunakan:</span><br>
                    " . $filter_display . "
                  </div>";
        }

        $data_mentah = [];
        while ($data = mysqli_fetch_assoc($query)) {
            $data_mentah[] = $data;
        }

        $data_per_lokasi = [];
        foreach ($data_mentah as $data) {
            $key = $data['kode_lokasi'] . '|' . $data['nama_lokasi'] . '|' . $data['peruntukan'];
            $data_per_lokasi[$key][] = [
                'tanggal' => $data['tanggal_pemantauan'],
                'no2' => $data['no2'],
                'so2' => $data['so2'],
                'pm25' => $data['pm25'],
                'lokasi' => $data['nama_lokasi'],
                'peruntukan' => $data['peruntukan'],
                'kode_lokasi' => $data['kode_lokasi'],
                'alamat' => $data['alamat_lokasi'],
                'kabupaten' => $data['kabupaten_kota']
            ];
        }

        foreach ($data_per_lokasi as $key => $data_lokasi) {
            usort($data_per_lokasi[$key], function($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });
        }

        function hitungPersentase($nilaiAwal, $nilaiAkhir) {
            if ($nilaiAwal == 0) return 0;
            return round((($nilaiAkhir - $nilaiAwal) / $nilaiAwal) * 100, 1);
        }

        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode Lokasi</th>
                        <th>Nama Lokasi</th>
                        <th>Alamat Lokasi</th>
                        <th>Kab/Kota</th>
                        <th>Peruntukan</th>
                        <th>NO₂ (µg/m³)</th>
                        <th>SO₂ (µg/m³)</th>
                        <th>PM₂.₅ (µg/m³)</th>
                    </tr>
                </thead>
                <tbody>";

        $no = 1;
        $data_sebelumnya = [];
        
        foreach ($data_per_lokasi as $key => $data_lokasi):
            $sebelumnya = null;
            
            foreach ($data_lokasi as $index => $data):
                $tgl = date('d-m-Y', strtotime($data['tanggal']));
                
                $no2_display = "<span class='nilai-polutan'>" . number_format($data['no2'], 2) . "</span>";
                $so2_display = "<span class='nilai-polutan'>" . number_format($data['so2'], 2) . "</span>";
                $pm25_display = "<span class='nilai-polutan'>" . number_format($data['pm25'], 2) . "</span>";
                
                if ($sebelumnya !== null) {
             
                    if ($data['no2'] > $sebelumnya['no2']) {
                        $persen = hitungPersentase($sebelumnya['no2'], $data['no2']);
                        $no2_display .= " <span class='ikon-naik'>▲ <span class='persen-change'>" . $persen . "%</span></span>";
                    } elseif ($data['no2'] < $sebelumnya['no2']) {
                        $persen = hitungPersentase($sebelumnya['no2'], $data['no2']);
                        $no2_display .= " <span class='ikon-turun'>▼ <span class='persen-change'>" . abs($persen) . "%</span></span>";
                    } else {
                        $no2_display .= " <span class='ikon-stabil'>(tetap)</span>";
                    }
                    
                    if ($data['so2'] > $sebelumnya['so2']) {
                        $persen = hitungPersentase($sebelumnya['so2'], $data['so2']);
                        $so2_display .= " <span class='ikon-naik'>▲ <span class='persen-change'>" . $persen . "%</span></span>";
                    } elseif ($data['so2'] < $sebelumnya['so2']) {
                        $persen = hitungPersentase($sebelumnya['so2'], $data['so2']);
                        $so2_display .= " <span class='ikon-turun'>▼ <span class='persen-change'>" . abs($persen) . "%</span></span>";
                    } else {
                        $so2_display .= " <span class='ikon-stabil'>(tetap)</span>";
                    }
                    
                    if ($data['pm25'] > $sebelumnya['pm25']) {
                        $persen = hitungPersentase($sebelumnya['pm25'], $data['pm25']);
                        $pm25_display .= " <span class='ikon-naik'>▲ <span class='persen-change'>" . $persen . "%</span></span>";
                    } elseif ($data['pm25'] < $sebelumnya['pm25']) {
                        $persen = hitungPersentase($sebelumnya['pm25'], $data['pm25']);
                        $pm25_display .= " <span class='ikon-turun'>▼ <span class='persen-change'>" . abs($persen) . "%</span></span>";
                    } else {
                        $pm25_display .= " <span class='ikon-stabil'>(tetap)</span>";
                    }
                    
                    if (abs(hitungPersentase($sebelumnya['no2'], $data['no2'])) > 100) {
                        $no2_display .= " <span class='badge-anomali'>⚠️ Tidak Wajar</span>";
                    }
                    if (abs(hitungPersentase($sebelumnya['so2'], $data['so2'])) > 100) {
                        $so2_display .= " <span class='badge-anomali'>⚠️ Tidak Wajar</span>";
                    }
                }
                
                echo "<tr>
                        <td>$no</td>
                        <td>$tgl</td>
                        <td>{$data['kode_lokasi']}</td>
                        <td>{$data['lokasi']}</td>
                        <td>{$data['alamat']}</td>
                        <td>{$data['kabupaten']}</td>
                        <td>{$data['peruntukan']}</td>
                        <td>$no2_display</td>
                        <td>$so2_display</td>
                        <td>$pm25_display</td>
                    </tr>";
                
                $no++;
                $sebelumnya = $data;
            endforeach;
            
            $sebelumnya = null;
            
        endforeach;

        echo "</tbody></table></div>";

        echo "<div class='text-right' style='margin-top:15px;'>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=pdf' class='btn btn-danger btn-sm'>Export PDF</a>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=excel' class='btn btn-success btn-sm'>Export Excel</a>
                <a href='export_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan&type=word' class='btn btn-primary btn-sm'>Export Word</a>
                <a href='grafik_tren_kualitas_udara.php?kabupaten=$kabupaten&peruntukan=$peruntukan' target='_blank' class='btn btn-info btn-sm'>Lihat Grafik Tren</a>
              </div>";

        $ringkasan_anomali = [];
        
        foreach ($data_per_lokasi as $key => $data_lokasi):
            $sebelumnya = null;
            
            foreach ($data_lokasi as $index => $data):
                if ($sebelumnya !== null):
           
                    $persen_no2 = hitungPersentase($sebelumnya['no2'], $data['no2']);
                    if ($persen_no2 > 100):
                        $ringkasan_anomali[] = [
                            'parameter' => 'NO₂',
                            'lokasi' => $data['lokasi'],
                            'peruntukan' => $data['peruntukan'],
                            'tanggal' => $data['tanggal'],
                            'nilai_sebelum' => $sebelumnya['no2'],
                            'nilai_sesudah' => $data['no2'],
                            'persen' => $persen_no2
                        ];
                    endif;
                    
                    $persen_so2 = hitungPersentase($sebelumnya['so2'], $data['so2']);
                    if ($persen_so2 > 100):
                        $ringkasan_anomali[] = [
                            'parameter' => 'SO₂',
                            'lokasi' => $data['lokasi'],
                            'peruntukan' => $data['peruntukan'],
                            'tanggal' => $data['tanggal'],
                            'nilai_sebelum' => $sebelumnya['so2'],
                            'nilai_sesudah' => $data['so2'],
                            'persen' => $persen_so2
                        ];
                    endif;
                    
                    $persen_pm25 = hitungPersentase($sebelumnya['pm25'], $data['pm25']);
                    if ($persen_pm25 > 100):
                        $ringkasan_anomali[] = [
                            'parameter' => 'PM2.5',
                            'lokasi' => $data['lokasi'],
                            'peruntukan' => $data['peruntukan'],
                            'tanggal' => $data['tanggal'],
                            'nilai_sebelum' => $sebelumnya['pm25'],
                            'nilai_sesudah' => $data['pm25'],
                            'persen' => $persen_pm25
                        ];
                    endif;
                    
                endif;
                $sebelumnya = $data;
            endforeach;
        endforeach;
        
        if (count($ringkasan_anomali) > 0):
?>
        
        <div class="ringkasan-anomali">
            <div class="ringkasan-header">
                ⚠️ RINGKASAN KENAIKAN TIDAK WAJAR (>100%)
            </div>
            
            <?php 
            $no_anomali = 1;
            foreach ($ringkasan_anomali as $a): 
                $tgl = date('d-m-Y', strtotime($a['tanggal']));
                
                $rekomendasi = '';
                
                if ($a['parameter'] == 'NO₂') {
                    if ($a['peruntukan'] == 'TRANSPORTASI') {
                        $rekomendasi = 'Investigasi kemacetan dan lakukan uji emisi kendaraan';
                    } elseif ($a['peruntukan'] == 'INDUSTRI') {
                        $rekomendasi = 'Periksa emisi cerobong industri dan kendaraan operasional';
                    } else {
                        $rekomendasi = 'Periksa kepadatan lalu lintas dan sumber emisi kendaraan di sekitar';
                    }
                } elseif ($a['parameter'] == 'SO₂') {
                    if ($a['peruntukan'] == 'INDUSTRI') {
                        $rekomendasi = 'Inspeksi bahan bakar industri dan sistem pengendalian emisi';
                    } else {
                        $rekomendasi = 'Investigasi potensi pembakaran terbuka atau industri tersembunyi';
                    }
                } elseif ($a['parameter'] == 'PM2.5') {
                    if ($a['peruntukan'] == 'PEMUKIMAN') {
                        $rekomendasi = 'Investigasi pembakaran sampah atau pembakaran lahan';
                    } elseif ($a['peruntukan'] == 'INDUSTRI') {
                        $rekomendasi = 'Periksa pengendalian debu dan proses produksi';
                    } elseif ($a['peruntukan'] == 'TRANSPORTASI') {
                        $rekomendasi = 'Periksa debu jalan dan emisi partikulat kendaraan';
                    } else {
                        $rekomendasi = 'Periksa aktivitas konstruksi atau pembakaran terbuka';
                    }
                }
            ?>
            <div class="ringkasan-item">
                <strong><?= $no_anomali++ ?>. <?= $a['parameter'] ?> di <?= htmlspecialchars($a['lokasi']) ?></strong>
                <div style="margin-top: 5px; color: #555;">
                    📅 <?= $tgl ?> | 
                    📍 <?= htmlspecialchars($a['peruntukan']) ?> | 
                    📈 <?= number_format($a['nilai_sebelum'], 2) ?> → <?= number_format($a['nilai_sesudah'], 2) ?> µg/m³ 
                    <span class="ikon-naik">▲ <?= round($a['persen']) ?>%</span>
                </div>
                <div style="margin-top: 8px; padding: 8px; background: #f8f9fa; border-radius: 5px;">
                    <b>Tindakan:</b> <?= $rekomendasi ?>
                </div>
            </div>
            <?php endforeach; ?>
            
        </div>
        
<?php
        endif;
        
    }

} else {
    echo "<div class='alert alert-info'>
            Silakan pilih filter Kabupaten/Kota atau Peruntukan, lalu klik <b>Tampilkan</b>.
          </div>";
}
?>

</div>

<?php include "footer.php"; ?>