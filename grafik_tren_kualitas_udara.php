<?php
include "header-admin.php";
include "sessionlogin.php";
include "koneksi.php";

$kabupaten  = $_GET['kabupaten'] ?? '';
$peruntukan = $_GET['peruntukan'] ?? '';

// ================= VALIDASI FILTER =================
if (empty($kabupaten) && empty($peruntukan)) {
    echo "
        <div class='alert alert-danger text-center' style='margin:30px;'>
            <b>Grafik tidak dapat ditampilkan.</b><br>
            Silakan pilih <b>Kabupaten/Kota</b> atau <b>Peruntukan</b> terlebih dahulu
            melalui halaman <b>Laporan Tren Kualitas Udara</b>.
        </div>
    ";
    include "footer.php";
    exit;
}

// ================= BUILD FILTER SQL =================
$filter = "WHERE 1=1 ";
if (!empty($kabupaten)) {
    $filter .= "AND l.kabupaten_kota = '$kabupaten' ";
}
if (!empty($peruntukan)) {
    $filter .= "AND l.peruntukan = '$peruntukan' ";
}

// ================= QUERY UTAMA =================
// KUNCI KONSEP:
// 1. ORDER BY kode_lokasi
// 2. LALU ORDER BY tanggal_pemantauan (lama → baru)
$sql = "
    SELECT
        p.tanggal_pemantauan,
        l.kode_lokasi,
        l.kabupaten_kota,
        l.peruntukan,
        h.no2,
        h.so2,
        h.pm25
    FROM hasil_pemantauan h
    JOIN pemantauan_udara p ON h.id_pemantauan = p.id_pemantauan
    JOIN lokasi_pemantauan l ON p.id_lokasi = l.id_lokasi
    $filter
    ORDER BY 
        l.kode_lokasi ASC,
        p.tanggal_pemantauan ASC
";

$result = mysqli_query($koneksi, $sql);

// ================= DATA UNTUK CHART =================
$labels = [];
$no2    = [];
$so2    = [];
$pm25   = [];

while ($row = mysqli_fetch_assoc($result)) {

    // LABEL SESUAI KONSEP:
    // KODE LOKASI | TANGGAL
    $labels[] = $row['kode_lokasi'] . " | " .
                date('d-m-Y', strtotime($row['tanggal_pemantauan']));

    $no2[]  = (float)$row['no2'];
    $so2[]  = (float)$row['so2'];
    $pm25[] = (float)$row['pm25'];
}

// ================= CEK DATA =================
if (empty($labels)) {
    echo "
        <div class='alert alert-warning text-center' style='margin:30px;'>
            ⚠️ Tidak ada data yang sesuai dengan filter untuk ditampilkan pada grafik.
        </div>
    ";
    include "footer.php";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Tren Kualitas Udara</title>
    <!-- Tambahkan library untuk PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <!-- Tambahkan Font Awesome untuk icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .download-btn {
            margin: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .download-btn:hover {
            background-color: #218838;
        }
        .download-btn i {
            margin-right: 8px;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .chart-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 95%;
        }
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            z-index: 9999;
        }
    </style>
</head>
<body>
    <div class="loading" id="loading">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Sedang membuat PDF...</p>
        </div>
    </div>

    <div class="container-fluid">
        <h2 class="text-center">Grafik Tren Kualitas Udara</h2>

        <p class="text-center">
            <?= !empty($kabupaten) ? "<b>Kabupaten/Kota:</b> $kabupaten<br>" : "" ?>
            <?= !empty($peruntukan) ? "<b>Peruntukan:</b> $peruntukan" : "" ?>
        </p>

        <div id="pdf-content">
            <div class="chart-container">
                <canvas id="trenChart" height="110"></canvas>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn btn-primary" id="downloadPdf">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <button class="btn btn-secondary" onclick="window.close();">
                <i class="fas fa-times"></i> Tutup Halaman
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('trenChart').getContext('2d');

    const trenChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'NO₂ (µg/m³)',
                    data: <?= json_encode($no2) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'SO₂ (µg/m³)',
                    data: <?= json_encode($so2) ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                },
                {
                    label: 'PM₂.₅ (µg/m³)',
                    data: <?= json_encode($pm25) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,0.1)',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 20
                    }
                },
                title: {
                    display: true,
                    text: 'Tren Kualitas Udara Berdasarkan Kode Lokasi dan Waktu',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + ' µg/m³';
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 12
                    },
                    bodyFont: {
                        size: 12
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Kode Lokasi | Tanggal Pemantauan',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        maxRotation: 90,
                        minRotation: 60,
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Konsentrasi (µg/m³)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                }
            },
            elements: {
                point: {
                    radius: 3,
                    hoverRadius: 6
                }
            }
        }
    });

    // Fungsi untuk download PDF
    document.getElementById('downloadPdf').addEventListener('click', function() {
        const loading = document.getElementById('loading');
        const downloadBtn = document.getElementById('downloadPdf');
        
        // Tampilkan loading
        loading.style.display = 'block';
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Membuat PDF...';
        
        // Ambil data filter untuk judul
        const kabupaten = "<?= addslashes($kabupaten) ?>";
        const peruntukan = "<?= addslashes($peruntukan) ?>";
        const totalData = <?= count($labels) ?>;
        
        // Buat judul PDF
        let title = 'LAPORAN GRAFIK TREN KUALITAS UDARA\n\n';
        if (kabupaten) title += `Kabupaten/Kota: ${kabupaten}\n`;
        if (peruntukan) title += `Peruntukan: ${peruntukan}\n`;
        title += `Jumlah Data: ${totalData} titik pemantauan\n`;
        title += `Diunduh pada: ${new Date().toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })} ${new Date().toLocaleTimeString('id-ID')}`;
        
        // Ambil konten yang akan dijadikan PDF
        const element = document.getElementById('pdf-content');
        
        // Gunakan html2canvas untuk capture grafik
        html2canvas(element, {
            scale: 2, // Kualitas 2x lebih tinggi
            backgroundColor: '#ffffff',
            useCORS: true,
            logging: false,
            allowTaint: true,
            onclone: function(clonedDoc) {
                // Optimasi tampilan untuk PDF
                const clonedCanvas = clonedDoc.getElementById('trenChart');
                if (clonedCanvas) {
                    clonedCanvas.style.width = '100%';
                    clonedCanvas.style.height = 'auto';
                }
            }
        }).then(canvas => {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('landscape', 'mm', 'a4');
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            
            // Tambahkan judul
            pdf.setFontSize(14);
            pdf.setFont('helvetica', 'bold');
            
            // Split judul jika terlalu panjang
            const titleLines = pdf.splitTextToSize(title, pdfWidth - 20);
            
            // Hitung tinggi judul
            const titleHeight = titleLines.length * 7;
            
            // Tambahkan judul
            pdf.text(titleLines, 10, 10);
            
            // Konversi canvas ke image
            const imgData = canvas.toDataURL('image/jpeg', 1.0);
            
            // Hitung dimensi gambar
            const imgWidth = pdfWidth - 20; // Margin kiri-kanan 10mm
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            
            // Posisi Y untuk gambar (setelah judul + margin)
            const yPosition = 10 + titleHeight + 5;
            
            // Cek jika gambar terlalu panjang untuk satu halaman
            if (yPosition + imgHeight > pdfHeight - 10) {
                // Jika terlalu panjang, skala ulang
                const availableHeight = pdfHeight - yPosition - 10;
                const scaledHeight = availableHeight;
                const scaledWidth = (canvas.width * scaledHeight) / canvas.height;
                
                // Tambahkan gambar dengan skala
                pdf.addImage(imgData, 'JPEG', 10, yPosition, scaledWidth, scaledHeight);
            } else {
                // Tambahkan gambar dengan ukuran normal
                pdf.addImage(imgData, 'JPEG', 10, yPosition, imgWidth, imgHeight);
            }
            
            // Tambahkan footer
            // pdf.setFontSize(10);
            // pdf.setFont('helvetica', 'normal');
            // pdf.text(
            //     `Halaman 1/1 - Sumber Data: Sistem Pemantauan Kualitas Udara`, 
            //     pdfWidth / 2, 
            //     pdfHeight - 10,
            //     { align: 'center' }
            // );
            
            // Simpan PDF
            const filename = `grafik-kualitas-udara-${kabupaten || 'all'}-${peruntukan || 'all'}-${Date.now()}.pdf`;
            pdf.save(filename);
            
            // Sembunyikan loading
            loading.style.display = 'none';
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Download PDF';
            
        }).catch(error => {
            console.error('Error generating PDF:', error);
            alert('Gagal mengunduh PDF. Silakan coba lagi atau periksa konsol browser untuk detail error.');
            
            // Sembunyikan loading
            loading.style.display = 'none';
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Download PDF';
        });
    });
    
    // Fungsi untuk menangani resize window
    window.addEventListener('resize', function() {
        trenChart.resize();
    });
    </script>
</body>
</html>

<?php include "footer.php"; ?>