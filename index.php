<?php
include('header.php');
include('koneksi.php');


if (!function_exists('get_image_path')) {
    function get_image_path($filename, $folder = 'uploads/') {
        if (empty($filename)) return "img/no-image.png";
        $path1 = $folder . $filename;
        $path2 = "uploads/" . $filename;
        if (file_exists($path1)) return $path1;
        if (file_exists($path2)) return $path2;
        return "img/no-image.png";
    }
}


if (!function_exists('get_youtube_id')) {
    function get_youtube_id($url) {

        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        if (isset($vars['v'])) return $vars['v'];


        if (preg_match('/youtu\.be\/([^\?]+)/', $url, $match)) {
            return $match[1];
        }


        if (preg_match('/youtube\.com\/embed\/([^\?]+)/', $url, $match)) {
            return $match[1];
        }

        return null;
    }
}
?>

<style>
.scroll-horizontal-wrapper {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    padding: 10px 0;
    scroll-behavior: smooth;
}
.card-horizontal {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    padding: 12px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    min-width: 30%;
    max-width: 30%;
}
@media (max-width: 768px) {
    .card-horizontal { min-width: 45%; max-width: 45%; }
}
@media (max-width: 576px) {
    .card-horizontal { min-width: 80%; max-width: 80%; }
}
.card-horizontal:hover {
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}
.card-image-wrapper {
    width: 100%;
    height: 160px;
    overflow: hidden;
    border-radius: 6px;
    margin-bottom: 10px;
}
.card-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
}
.card-body-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}
.card-body-content h4 {
    font-size: 16px;
    margin-top: 0;
    margin-bottom: 5px;
    color: #333;
    height: 40px;
    overflow: hidden;
    line-height: 1.3;
}
.card-body-content p {
    font-size: 14px;
    color: #666;
    margin-bottom: 5px;
    height: 40px;
    overflow: hidden;
}
.card-body-content small {
    display: block;
    color: #777;
    margin-top: auto;
}
.info-box {
    background: #f8f9fa;
    padding: 15px;
    border-left: 5px solid #007bff;
    margin-bottom: 20px;
}

/* Tambahkan di dalam <style> yang sudah ada */
.chart-container {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 25px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border: 1px solid #eaeaea;
}
.chart-title {
    color: #2c3e50;
    font-size: 18px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
    display: flex;
    align-items: center;
    gap: 10px;
}
.chart-title i {
    color: #3498db;
}
.chart-wrapper {
    position: relative;
    height: 300px;
    margin-bottom: 10px;
}
.legend-item {
    display: inline-flex;
    align-items: center;
    margin-right: 15px;
    font-size: 12px;
}
.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 5px;
}
.stats-box {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 15px;
}
.stat-item {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    border-left: 3px solid #3498db;
}
.stat-value {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
}
.stat-label {
    font-size: 12px;
    color: #7f8c8d;
}

.sidebar {
    position: fixed;
    top: 160px;
    right: 80px;
    width: 400px;
    max-height: calc(100vh - 180px);
    overflow-y: auto;
    background: none; 
    border-radius: 8px; 
    padding: 20px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.1); 
    z-index: 999;
}

.col-sm-9 {
    margin-right: 280px !important;
}

@media (max-width: 1200px) {
    .sidebar {
        width: 350px;
        right: 40px;
    }
    .col-sm-9 {
        margin-right: 230px !important;
    }
}

@media (max-width: 992px) {
    .sidebar {
        position: relative;
        top: auto;
        right: auto;
        width: 100%;
        max-height: none;
        margin: 20px auto;
    }
    .col-sm-9 {
        margin-right: 0 !important;
    }
}

.scroll-vertical-wrapper {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 10px;
}



</style>
<div class="scroll-vertical-wrapper">

<div class="row"> 
    <div class="col-sm-9">
        <div class="stats-box">
                <!-- <div class="stat-item">
                   <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                        <caption><b>Informasi Data pada Grafik</b></caption>
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">Kategori</th>
                                <th style="text-align: center; vertical-align: middle;">NO₂ µg/m³</th>
                                <th style="text-align: center; vertical-align: middle;">SO₂ µg/m³</th>
                                <th style="text-align: center; vertical-align: middle;">PM₂.₅ µg/m³</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">Baik</td>
                                <td style="text-align: center; vertical-align: middle;">0-150</td>
                                <td style="text-align: center; vertical-align: middle;">0-100</td>
                                <td style="text-align: center; vertical-align: middle;">0-15,5</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">Sedang</td>
                                <td style="text-align: center; vertical-align: middle;">151-300</td>
                                <td style="text-align: center; vertical-align: middle;">101-200</td>
                                <td style="text-align: center; vertical-align: middle;">15,6-55,4</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">Tidak Sehat</td>
                                <td style="text-align: center; vertical-align: middle;">301-750</td>
                                <td style="text-align: center; vertical-align: middle;">201-500</td>
                                <td style="text-align: center; vertical-align: middle;">55,5-150,4</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">Sangat Tidak Sehat</td>
                                <td style="text-align: center; vertical-align: middle;">751-1130</td>
                                <td style="text-align: center; vertical-align: middle;">501-750</td>
                                <td style="text-align: center; vertical-align: middle;">150,5-250,4</td>
                            </tr>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">Berbahaya</td>
                                <td style="text-align: center; vertical-align: middle;">≥1131</td>
                                <td style="text-align: center; vertical-align: middle;">≥751</td>
                                <td style="text-align: center; vertical-align: middle;">≥250,5</td>
                            </tr>
                        </tbody>

                    </table>
                </div> -->
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; padding: 18px; border-radius: 6px; border-left: 4px solid #3498db; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <p style="margin: 0 0 12px 0; font-size: 20px; font-weight: bold; color: #000000ff;">
        Informasi Data pada Grafik
    </p>
    
    <div style="margin-left: 10px;">
        <p style="margin: 0 0 10px 0; font-size: 15px;">
            <span style="color: #3498db; font-weight: bold;">•</span> Nilai yang ditampilkan adalah <span style="font-weight: 600;">konsentrasi polutan udara</span>, diukur dalam satuan <span style="font-weight: 600;">mikrogram per meter kubik (µg/m³)</span>.
        </p>
        
        <p style="margin: 0 0 10px 0; font-size: 15px;">
            <span style="color: #3498db; font-weight: bold;">•</span> Data merupakan hasil pengukuran dengan metode <span style="font-weight: 600;">Manual Passive</span> selama <span style="font-weight: 600;">14 hari</span>.
        </p>
        
        <p style="margin: 0 0 10px 0; font-size: 15px;">
            <span style="color: #3498db; font-weight: bold;">•</span> Parameter yang ditampilkan: <span style="font-weight: 600;">Nitrogen Dioksida (NO₂)</span>, <span style="font-weight: 600;">Sulfur Dioksida (SO₂)</span>, dan <span style="font-weight: 600;">Partikulat Halus (PM2.5)</span>.
        </p>

        <p style="margin: 0 0 10px 0; font-size: 15px;">
            <span style="color: #3498db; font-weight: bold;">•</span>
            <span style="font-weight: 600;">Parameter NO₂ dihasilkan oleh</span> terutama dari gas buang kendaraan bermotor,
            <span style="font-weight: 600;">Parameter SO₂ dihasilan oleh</span> utamanya dari pembakaran bahan bakar industri, dan 
            <span style="font-weight: 600;">Parameter PM2.5 dihasilkan oleh</span> partikel mikroskopis dari berbagai sumber termasuk pembakaran dan debu.
        </p>
        
        <p style="margin: 0 0 10px 0; font-size: 15px;">
            <span style="color: #3498db; font-weight: bold;">•</span> Semakin rendah nilainya, semakin baik. Untuk konteks, nilai <span style="font-weight: 600;">Baku Mutu</span> tahunan menurut <span style="font-style: italic;">PP No. 22 Tahun 2021</span> adalah:
            <strong>SO₂:</strong> 60 µg/m³,
            <strong>NO₂:</strong> 50 µg/m³, dan
            <strong>PM2.5:</strong> 15 µg/m³.
        </p>
        
    </div>
</div>
        </div><br>        

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                <div class="card-header bg-primary text-white d-flex align-items-center" style="gap: 10px; padding: 12px 20px;">
                        <i class="fas fa-filter"></i>
                        <span>Filter Untuk Visualisasi Rata-Rata Kualitas Udara</span>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" class="form-inline">
                            <div class="form-group mr-3" style="gap: 10px; padding: 12px 20px;">
                                <label for="tahun" class="mr-2"><strong>Pilih Tahun:</strong></label>
                                <select name="tahun" id="tahun" class="form-control" onchange="this.form.submit()" style="min-width: 120px;">
                                    <?php
                                    // Ambil tahun-tahun yang SUDAH ADA DATA di database
                                    $queryTahun = "SELECT DISTINCT YEAR(pu.tanggal_pemantauan) AS tahun 
                                                FROM pemantauan_udara pu
                                                JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                                                WHERE pu.tanggal_pemantauan IS NOT NULL 
                                                AND hp.pm25 IS NOT NULL
                                                ORDER BY tahun DESC";
                                    $resultTahun = mysqli_query($koneksi, $queryTahun);
                                    
                                    // Cari tahun dengan data terbanyak sebagai default
                                    $queryTahunDefault = "SELECT 
                                                            YEAR(pu.tanggal_pemantauan) AS tahun,
                                                            COUNT(*) AS jumlah_data
                                                        FROM pemantauan_udara pu
                                                        JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                                                        WHERE pu.tanggal_pemantauan IS NOT NULL
                                                        GROUP BY YEAR(pu.tanggal_pemantauan)
                                                        ORDER BY jumlah_data DESC, tahun DESC
                                                        LIMIT 1";
                                    $resultDefault = mysqli_query($koneksi, $queryTahunDefault);
                                    $rowDefault = mysqli_fetch_assoc($resultDefault);
                                    $tahunDefault = $rowDefault['tahun'] ?? date('Y');
                                    
                                    // Tahun aktif dari filter atau default
                                    $tahunAktif = isset($_GET['tahun']) ? intval($_GET['tahun']) : $tahunDefault;
                                    
                                    $adaData = false;
                                    while ($row = mysqli_fetch_assoc($resultTahun)) {
                                        $tahun = $row['tahun'];
                                        $selected = ($tahun == $tahunAktif) ? 'selected' : '';
                                        echo "<option value='$tahun' $selected>$tahun</option>";
                                        $adaData = true;
                                    }
                                    
                                    // Jika tidak ada data sama sekali
                                    if (!$adaData) {
                                        echo "<option value='0' selected>-- Tidak ada data --</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            
                            <!-- Info data yang ditampilkan -->
                            <div class="ml-3" style="margin-top: 5px;">
                                <?php
                                // Hitung jumlah data untuk tahun yang dipilih
                                $queryCount = "SELECT COUNT(DISTINCT lp.kabupaten_kota) AS jumlah_kabupaten
                                            FROM lokasi_pemantauan lp
                                            LEFT JOIN pemantauan_udara pu ON lp.id_lokasi = pu.id_lokasi
                                            LEFT JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                                            WHERE 1=1";
                                
                                if ($tahunAktif != 'all' && $tahunAktif > 0) {
                                    $queryCount .= " AND YEAR(pu.tanggal_pemantauan) = '$tahunAktif'";
                                }
                                
                                $resultCount = mysqli_query($koneksi, $queryCount);
                                $rowCount = mysqli_fetch_assoc($resultCount);
                                $jumlahKabupaten = $rowCount['jumlah_kabupaten'] ?? 0;
                                ?>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Menampilkan data dari <strong><?= $jumlahKabupaten ?></strong> kabupaten/kota 
                                    tahun <strong><?= $tahunAktif ?></strong>
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title"><i class="fas fa-chart-bar"></i> Visualisasi Kualitas Udara Dalam Bentuk Grafik</h3>
            
            <?php
            // Tentukan filter tahun
            if (isset($_GET['tahun']) && !empty($_GET['tahun']) && $_GET['tahun'] != 'all') {
                $tahunFilter = intval($_GET['tahun']);
                $whereClause = "YEAR(pu.tanggal_pemantauan) = '$tahunFilter'";
                $tahunTampil = $tahunFilter;
            } else {
                // Default: tahun dengan data terbanyak atau tahun terakhir
                $queryTahunDefault = "SELECT 
                                        YEAR(pu.tanggal_pemantauan) AS tahun,
                                        COUNT(*) AS jumlah_data
                                    FROM pemantauan_udara pu
                                    JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                                    WHERE pu.tanggal_pemantauan IS NOT NULL
                                    GROUP BY YEAR(pu.tanggal_pemantauan)
                                    ORDER BY jumlah_data DESC, tahun DESC
                                    LIMIT 1";
                $resultDefault = mysqli_query($koneksi, $queryTahunDefault);
                $rowDefault = mysqli_fetch_assoc($resultDefault);
                $tahunFilter = $rowDefault['tahun'] ?? date('Y');
                $whereClause = "YEAR(pu.tanggal_pemantauan) = '$tahunFilter'";
                $tahunTampil = $tahunFilter;
            }
            
            // BAR CHART
            $queryKabupaten = "
                SELECT 
                    lp.kabupaten_kota,
                    COUNT(DISTINCT lp.id_lokasi) AS jumlah_lokasi,
                    COALESCE(ROUND(AVG(hp.no2), 2), 0) AS avg_no2,
                    COALESCE(ROUND(AVG(hp.so2), 2), 0) AS avg_so2,
                    COALESCE(ROUND(AVG(hp.pm25), 2), 0) AS avg_pm25,
                    CASE 
                        WHEN COALESCE(AVG(hp.pm25),0) <= 50 THEN 'BAIK'
                        WHEN COALESCE(AVG(hp.pm25),0) <= 100 THEN 'SEDANG'
                        WHEN COALESCE(AVG(hp.pm25),0) <= 200 THEN 'TIDAK SEHAT'
                        WHEN COALESCE(AVG(hp.pm25),0) <= 300 THEN 'SANGAT TIDAK SEHAT'
                        ELSE 'BERBAHAYA'
                    END AS status
                FROM lokasi_pemantauan lp
                LEFT JOIN pemantauan_udara pu ON lp.id_lokasi = pu.id_lokasi
                LEFT JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                WHERE $whereClause
                GROUP BY lp.kabupaten_kota
                HAVING COUNT(DISTINCT pu.id_pemantauan) > 0
                ORDER BY avg_pm25 DESC
            ";

            $resultKabupaten = mysqli_query($koneksi, $queryKabupaten);
        
       
                $kabupatenData = [];
                $pieLabels = [];
                $pieData = [];
                $pieColors = [];
                $pieTooltips = [];

                if (mysqli_num_rows($resultKabupaten) > 0) {
                    $barLabels = [];
                    $barNO2 = [];
                    $barSO2 = [];
                    $barPM25 = [];
                    $barColorsNO2 = [];
                    $barColorsSO2 = [];
                    $barColorsPM25 = [];

                    function getISPUColor($value) {
                        if ($value <= 50) return "#2ECC71";
                        if ($value <= 100) return "#3498DB";
                        if ($value <= 200) return "#F1C40F";
                        if ($value <= 300) return "#E74C3C";
                        return "#000000";
                    }

                    while ($row = mysqli_fetch_assoc($resultKabupaten)) {

                        $barLabels[] = $row['kabupaten_kota'];

                        $barNO2[] = $row['avg_no2'];
                        $barSO2[] = $row['avg_so2'];
                        $barPM25[] = $row['avg_pm25'];

                        $barColorsNO2[] = getISPUColor($row['avg_no2']);
                        $barColorsSO2[] = getISPUColor($row['avg_so2']);
                        $barColorsPM25[] = getISPUColor($row['avg_pm25']);
                    }

                }


                $queryTrend = "
                    SELECT 
                        lp.kabupaten_kota,
                        MAX(pu.tanggal_pemantauan) AS tanggal_terakhir,
                        COALESCE(ROUND(AVG(hp.no2), 2), 0) AS avg_no2,
                        COALESCE(ROUND(AVG(hp.so2), 2), 0) AS avg_so2,
                        COALESCE(ROUND(AVG(hp.pm25), 2), 0) AS avg_pm25,
                        DATEDIFF(CURDATE(), MAX(pu.tanggal_pemantauan)) AS hari_sejak
                    FROM lokasi_pemantauan lp
                    JOIN pemantauan_udara pu ON lp.id_lokasi = pu.id_lokasi
                    JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                    WHERE pu.tanggal_pemantauan = (
                        SELECT MAX(p2.tanggal_pemantauan) 
                        FROM pemantauan_udara p2
                        JOIN lokasi_pemantauan l2 ON p2.id_lokasi = l2.id_lokasi
                        WHERE l2.kabupaten_kota = lp.kabupaten_kota
                    )
                    GROUP BY lp.kabupaten_kota
                    ORDER BY tanggal_terakhir DESC
                ";

                // $queryTrend = "
                //     SELECT 
                //         t.kabupaten_kota,
                //         t.nama_lokasi,
                //         t.tanggal_pemantauan AS tanggal_terakhir,
                //         t.no2,
                //         t.so2,
                //         t.pm25,
                //         DATEDIFF(CURDATE(), t.tanggal_pemantauan) AS hari_sejak
                //     FROM (
                //         SELECT 
                //             lp.kabupaten_kota,
                //             lp.nama_lokasi,
                //             pu.tanggal_pemantauan,
                //             hp.no2,
                //             hp.so2,
                //             hp.pm25,
                //             ROW_NUMBER() OVER(
                //                 PARTITION BY lp.kabupaten_kota 
                //                 ORDER BY pu.tanggal_pemantauan DESC
                //             ) AS rn
                //         FROM lokasi_pemantauan lp
                //         JOIN pemantauan_udara pu ON lp.id_lokasi = pu.id_lokasi
                //         JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                //     ) AS t
                //     WHERE t.rn = 1
                //     ORDER BY t.tanggal_pemantauan DESC;
                // ";



                $resultTrend = mysqli_query($koneksi, $queryTrend);

                $trendLabels = [];
                $trendNO2 = [];
                $trendSO2 = [];
                $trendPM25 = [];
                $trendDays = [];

                if (mysqli_num_rows($resultTrend) > 0) {
                    while ($row = mysqli_fetch_assoc($resultTrend)) {
                        $trendLabels[] = $row['kabupaten_kota'];
                        $trendNO2[] = $row['avg_no2'];
                        $trendSO2[] = $row['avg_so2'];
                        $trendPM25[] = $row['avg_pm25'];
                        $trendDays[] = $row['hari_sejak'];
                    }
                }


                // $resultTrend = mysqli_query($koneksi, $queryTrend);

                // $trendLabels = [];
                // $trendNO2 = [];
                // $trendSO2 = [];
                // $trendPM25 = [];
                // $trendDays = [];

                // if (mysqli_num_rows($resultTrend) > 0) {
                //     while ($row = mysqli_fetch_assoc($resultTrend)) {

                //         // Label bisa kabupaten atau kabupaten + nama lokasi
                //         $trendLabels[] = $row['kabupaten_kota'];

                //         $trendNO2[] = $row['no2'];
                //         $trendSO2[] = $row['so2'];
                //         $trendPM25[] = $row['pm25'];
                //         $trendDays[] = $row['hari_sejak'];
                //     }
                // }


                $queryStats = "
                    SELECT 
                        COUNT(DISTINCT lp.id_lokasi) AS total_lokasi,
                        COUNT(DISTINCT lp.kabupaten_kota) AS total_kabupaten,
                        COUNT(DISTINCT pu.id_pemantauan) AS total_pemantauan,
                        COALESCE(ROUND(AVG(hp.no2), 2), 0) AS avg_no2,
                        COALESCE(ROUND(AVG(hp.so2), 2), 0) AS avg_so2,
                        COALESCE(ROUND(AVG(hp.pm25), 2), 0) AS avg_pm25
                    FROM lokasi_pemantauan lp
                    LEFT JOIN pemantauan_udara pu ON lp.id_lokasi = pu.id_lokasi
                    LEFT JOIN hasil_pemantauan hp ON pu.id_pemantauan = hp.id_pemantauan
                ";

                $resultStats = mysqli_query($koneksi, $queryStats);
                $stats = mysqli_fetch_assoc($resultStats);
            ?>

            
            <div class="row">
                <div class="col-md-6">
                    <p style="font-size: 0.9em; margin-top: 10px; color: #555;" align="center">
                        <b>Visualisasi rata-rata kualitas udara tahun <?= $tahunTampil ?></b>
                    </p>
                    <div class="chart-wrapper">
                        <canvas id="pieChartKabupaten"></canvas>
                    </div>
                </div>
                
                <div class="col-md-6">
                     <p style="font-size: 0.9em; margin-top: 10px; color: #555;" align="center">
                        <b>Trend kualitas udara pada pemantauan terakhir</b>
                    </p>
                    <div class="chart-wrapper">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- STATISTIK RINGKAS -->
            <p style="font-size: 0.9em; margin-top: 10px; color: #555;" align="center">
                <b>Rata-rata kualitas udara dalam seluruh data historis</b>
            </p>
            <div class="stats-box">
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['total_lokasi'] ?? 0 ?></div>
                    <div class="stat-label">LOKASI</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['total_kabupaten'] ?? 0 ?></div>
                    <div class="stat-label">KABUPATEN</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['total_pemantauan'] ?? 0 ?></div>
                    <div class="stat-label">PEMANTAUAN</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['avg_no2'] ?? '0.00' ?></div>
                    <div class="stat-label">RATA RATA NO₂</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['avg_so2'] ?? '0.00' ?></div>
                    <div class="stat-label">RATA RATA SO₂</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $stats['avg_pm25'] ?? '0.00' ?></div>
                    <div class="stat-label">RATA RATA PM₂.5</div>
                </div>
            </div>
        </div>
        <script>

        document.addEventListener('DOMContentLoaded', function() {
            // ===== BAR CHART =====
            <?php if (!empty($barLabels)): ?>
            const barCtx = document.getElementById('pieChartKabupaten').getContext('2d');

            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($barLabels) ?>,
                    datasets: [
                        {
                            label: 'NO₂ (µg/m³)',
                            data: <?= json_encode($barNO2) ?>,
                            backgroundColor: '#ff4d4d',
                            borderColor: '#ff4d4d',
                            borderWidth: 2
                        },
                        {
                            label: 'SO₂ (µg/m³)',
                            data: <?= json_encode($barSO2) ?>,
                            backgroundColor: '#3498db',
                            borderColor: '#3498db',
                            borderWidth: 2
                        },
                        {
                            label: 'PM2.5 (µg/m³)',
                            data: <?= json_encode($barPM25) ?>,
                            backgroundColor: '#2ecc71',
                            borderColor: '#2ecc71',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                usePointStyle: false,
                                boxWidth: 20,
                                boxHeight: 10,
                                borderRadius: 2
                            }
                        },

                        tooltip: {
                            mode: 'nearest',
                            intersect: false,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const pollutant = context.dataset.label;

                                    const data = <?= json_encode($pieTooltips) ?>[index];

                                    return [
                                        data.kabupaten,
                                        `${pollutant}: ${context.raw} μg/m³`,
                                        `NO₂: ${data.no2} μg/m³`,
                                        `SO₂: ${data.so2} μg/m³`,
                                        `PM2.5: ${data.pm25} μg/m³`,
                                        `Status (PM2.5): ${data.status}`,
                                        `Lokasi: ${data.lokasi}`
                                    ];
                                }
                            }
                        },

                        datalabels: false
                    },

                    interaction: {
                        mode: 'nearest',
                        intersect: true
                    },

                    onHover: (event, elements) => {
                        const chart = event.chart;

                        chart.data.datasets.forEach((dataset) => {
                            dataset._showValueIndex = null;
                        });

                        if (elements.length > 0) {
                            const element = elements[0];
                            const datasetIndex = element.datasetIndex;
                            const index = element.index;

                            chart.data.datasets[datasetIndex]._showValueIndex = index;
                        }

                        chart.update('none');
                    },

                    plugins: {
                        ...this?.plugins,
                        afterDatasetsDraw: (chart) => {
                            const ctx = chart.ctx;

                            chart.data.datasets.forEach((dataset, datasetIndex) => {
                                const showIndex = dataset._showValueIndex;

                                if (showIndex !== null && showIndex !== undefined) {
                                    const meta = chart.getDatasetMeta(datasetIndex);
                                    const bar = meta.data[showIndex];

                                    if (bar) {
                                        ctx.save();
                                        ctx.font = '12px sans-serif';
                                        ctx.fillStyle = '#000';
                                        ctx.textAlign = 'center';
                                        ctx.fillText(dataset.data[showIndex], bar.x, bar.y - 5);
                                        ctx.restore();
                                    }
                                }
                            });
                        }
                    }
                }

            });
            <?php else: ?>
            document.getElementById('pieChartKabupaten').innerHTML = 
                '<div class="text-center p-5 text-muted">Data belum tersedia</div>';
            <?php endif; ?>
            
            // ===== LINE CHART =====
            <?php if (!empty($trendLabels)): ?>
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($trendLabels) ?>,
                    datasets: [
                        {
                            label: 'NO₂ (μg/m³)',
                            data: <?= json_encode($trendNO2) ?>,
                            borderColor: '#e74c3c',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'SO₂ (μg/m³)',
                            data: <?= json_encode($trendSO2) ?>,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'PM2.5 (μg/m³)',
                            data: <?= json_encode($trendPM25) ?>,
                            borderColor: '#2ECC71',
                            backgroundColor: 'rgba(46, 204, 113, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }

                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                afterLabel: function(context) {
                                    const days = <?= json_encode($trendDays) ?>[context.dataIndex];
                                    return `Diperiksa: ${days} hari lalu`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Konsentrasi (μg/m³)'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
            <?php else: ?>
            document.getElementById('trendChart').innerHTML = 
                '<div class="text-center p-5 text-muted">Data trend belum tersedia</div>';
            <?php endif; ?>
        });
        </script>

        
        <h3 class="section-title">BERITA TERBARU</h3>
        <div class="scroll-horizontal-wrapper"> 
            <?php
            $qBerita = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY tanggal_posting DESC LIMIT 10");
            if (mysqli_num_rows($qBerita) == 0) {
                echo "<p style='padding:10px;'>Belum ada berita.</p>";
            } else {
                while ($b = mysqli_fetch_assoc($qBerita)) {
                    $gambar = get_image_path($b['gambar'], 'uploads/');
                    $judul = htmlspecialchars($b['judul_berita']);
                    $isiSingkat = substr(strip_tags($b['isi_berita']), 0, 100) . "...";
                    $tanggal = date('d M Y', strtotime($b['tanggal_posting']));
                    $link_detail = "detail_berita.php?id=" . $b['id_berita'];
                    ?>
                    <a href="<?= $link_detail ?>" class="card-horizontal">
                        <div class="card-image-wrapper">
                            <img src="<?= $gambar ?>" alt="<?= $judul ?>">
                        </div>
                        <div class="card-body-content">
                            <h4><?= $judul ?></h4>
                            <p><?= $isiSingkat ?></p>
                            <small>📅 <?= $tanggal ?></small>
                        </div>
                    </a>
                    <?php
                }
            }
            ?>
        </div>

        <h3 class="section-title">EDUKASI LINGKUNGAN</h3>
        <div class="scroll-horizontal-wrapper">
            <?php
            $qEdukasi = mysqli_query($koneksi, "SELECT * FROM edukasi ORDER BY tanggal_posting DESC LIMIT 10");
            if (mysqli_num_rows($qEdukasi) == 0) {
                echo "<p style='padding:10px;'>Belum ada konten edukasi.</p>";
            } else {
                while ($e = mysqli_fetch_assoc($qEdukasi)) {
                    $judul = htmlspecialchars($e['judul_edukasi']);
                    $isiSingkat = substr(strip_tags($e['isi_edukasi']), 0, 100) . "...";
                    $tanggal = date('d M Y', strtotime($e['tanggal_posting']));
                    $tipe = $e['tipe_konten']; 

                    if ($tipe == 'file' && !empty($e['file_path'])) {
                        if (!empty($e['gambar'])) {
                            $gambar = get_image_path($e['gambar'], 'uploads/cover_edukasi/');
                        } else {
                            $gambar = "img/doc-icon.png";
                        }
                        $link = "uploads/edukasi/" . $e['file_path'];
                    } 
                    elseif ($tipe == 'video' && !empty($e['link_video'])) {
                        $youtube_id = get_youtube_id($e['link_video']);
                        if (!empty($youtube_id)) {
                            $gambar = "https://img.youtube.com/vi/$youtube_id/hqdefault.jpg";
                        } else {
                            $gambar = "img/video-icon.png";
                        }
                        $link = $e['link_video'];
                    } 
                    else {
                        $gambar = "img/no-image.png";
                        $link = "#";
                    }
                    ?>
                    <a href="<?= $link ?>" target="_blank" class="card-horizontal">
                        <div class="card-image-wrapper">
                            <img src="<?= $gambar ?>" alt="<?= $judul ?>">
                        </div>
                        <div class="card-body-content">
                            <h4><?= $judul ?></h4>
                            <p><?= $isiSingkat ?></p>
                            <small>📅 <?= $tanggal ?></small>
                        </div>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    

    <div class="col-sm-3">
        <div class="sidebar">
            <h2 align="center">Dinas Lingkungan Hidup Provinsi Kalimantan Selatan</h2>
            <hr/>
            <div class="well" align="center">
                <img class="img" src="img/logo_kalsel.png" width="50%">
            </div>
        </div>
    </div>

    </div>
</div>

<?php include('footer.php'); ?>
