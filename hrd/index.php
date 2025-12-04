<?php
/**
 * Halaman Dashboard untuk HRD.
 *
 * Menampilkan ringkasan statistik utama seperti jumlah pelamar, lowongan aktif,
 * wawancara terjadwal, dan lamaran baru hari ini. Juga menampilkan visualisasi
 * data dalam bentuk bar chart (pelamar per lowongan) dan pie chart (komposisi status lamaran).
 */

$page = 'dashboard';
// 1. Panggil Header
include '../templates/hrd_header.php';

// Inisialisasi variabel untuk statistik
$total_pelamar = 0;
$lowongan_aktif = 0;
$wawancara_terjadwal = 0;
$pelamar_baru_hari_ini = 0;

// --- LOGIKA STATISTIK UTAMA ---

// Query 1: Hitung Total Pelamar (unik berdasarkan id_pelamar)
$result1 = $koneksi->query("SELECT COUNT(DISTINCT id_pelamar) AS total FROM lamaran");
if ($result1) {
    $total_pelamar = $result1->fetch_assoc()['total'];
}

// Query 2: Hitung Lowongan yang masih aktif berdasarkan tanggal
$result2 = $koneksi->query("SELECT COUNT(id_lowongan) AS total FROM lowongan WHERE CURDATE() >= tanggal_buka AND CURDATE() <= tanggal_tutup");
if ($result2) {
    $lowongan_aktif = $result2->fetch_assoc()['total'];
}

// Query 3: Hitung Wawancara dengan status 'Terjadwal'
$result3 = $koneksi->query("SELECT COUNT(id_wawancara) AS total FROM wawancara WHERE status_wawancara = 'Terjadwal'");
if ($result3) {
    $wawancara_terjadwal = $result3->fetch_assoc()['total'];
}

// Query 4: Hitung Lamaran yang masuk pada hari ini
$result4 = $koneksi->query("SELECT COUNT(id_lamaran) AS total FROM lamaran WHERE DATE(tanggal_lamaran) = CURDATE()");
if ($result4) {
    $pelamar_baru_hari_ini = $result4->fetch_assoc()['total'];
}


// --- LOGIKA UNTUK BAR CHART (Jumlah Pelamar per Lowongan) ---
$query_bar_chart = "SELECT 
                        COALESCE(posisi_dilamar, 'Lainnya') AS posisi, 
                        COUNT(id_lamaran) AS jumlah
                    FROM lamaran
                    GROUP BY posisi
                    ORDER BY jumlah DESC";
$result_bar_chart = $koneksi->query($query_bar_chart);

$bar_labels = [];
$bar_data = [];

if ($result_bar_chart) {
    while ($row = $result_bar_chart->fetch_assoc()) {
        $bar_labels[] = $row['posisi'];
        $bar_data[] = (int) $row['jumlah'];
    }
}
$bar_labels_json = json_encode($bar_labels);
$bar_data_json = json_encode($bar_data);


// --- LOGIKA UNTUK PIE CHART (Komposisi Status Lamaran) ---
$query_pie_chart = "SELECT 
                        status_lamaran, 
                        COUNT(id_lamaran) AS jumlah
                    FROM lamaran
                    WHERE status_lamaran IN ('Diproses', 'Diterima', 'Ditolak', 'Wawancara')
                    GROUP BY status_lamaran";
$result_pie_chart = $koneksi->query($query_pie_chart);

$pieDataPoints = []; // Menggunakan short array syntax

// Definisikan palet warna kustom untuk status lamaran
$warna_status = [
    'Wawancara' => '#E9DFD2',
    'Diterima' => '#9EA98E',
    'Ditolak' => '#C98664',
    'Diproses' => '#8BC7C0'
];

if ($result_pie_chart) {
    while ($row = $result_pie_chart->fetch_assoc()) {
        $status = $row['status_lamaran'];
        // Masukkan data ke array dengan format yang dibutuhkan CanvasJS
        $pieDataPoints[] = [
            "label" => $status,
            "y" => (int) $row['jumlah'],
            "color" => $warna_status[$status] ?? null // Gunakan warna dari palet
        ];
    }
}
?>

<div class="dashboard-container">

    <div class="page-title">
        <h1>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>!
        </h1>
        <p>Berikut ringkasan aktivitas rekrutmen hari ini.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-pelamar">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/groups.png" alt="groups" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($total_pelamar, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Total Pelamar</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-lowongan">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="40" height="40" viewBox="0 0 50 50">
                    <path
                        d="M 20 3 C 18.355469 3 17 4.355469 17 6 L 17 9 L 3 9 C 1.347656 9 0 10.347656 0 12 L 0 25 C 0 26.652344 1.347656 28 3 28 L 47 28 C 48.652344 28 50 26.652344 50 25 L 50 12 C 50 10.347656 48.652344 9 47 9 L 33 9 L 33 6 C 33 4.355469 31.644531 3 30 3 Z M 20 5 L 30 5 C 30.5625 5 31 5.4375 31 6 L 31 9 L 19 9 L 19 6 C 19 5.4375 19.4375 5 20 5 Z M 25 22 C 26.105469 22 27 22.894531 27 24 C 27 25.105469 26.105469 26 25 26 C 23.894531 26 23 25.105469 23 24 C 23 22.894531 23.894531 22 25 22 Z M 0 27 L 0 44 C 0 45.652344 1.347656 47 3 47 L 47 47 C 48.652344 47 50 45.652344 50 44 L 50 27 C 50 28.652344 48.652344 30 47 30 L 3 30 C 1.347656 30 0 28.652344 0 27 Z">
                    </path>
                </svg>
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($lowongan_aktif, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Lowongan Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-wawancara">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/month-view.png"
                    alt="month-view" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($wawancara_terjadwal, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Wawancara Terjadwal</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-pelamar-baru">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/add-user-male.png"
                    alt="add-user-male" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($pelamar_baru_hari_ini, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p>Lamaran Baru (Hari ini)</p>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3>Jumlah Pelamar per Lowongan</h3>
            <div class="placeholder-chart">
                <canvas id="chartPelamarPerLowongan"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Komposisi Status Pelamar</h3>
            <div class="placeholder-chart">
                <!-- ELEMEN UNTUK CANVASJS PIE CHART -->
                <div id="pieChartContainer" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>

</div>

<!-- Library untuk Chart.js (Bar Chart) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<!-- Library untuk CanvasJS (Pie Chart) -->
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>


<script>
    $(document).ready(function () {

        // Validasi agar chart hanya diinisialisasi jika elemen canvas ada
        if ($('#chartPelamarPerLowongan').length) {

            // === 1. Inisialisasi Bar Chart (Chart.js) ===
            const barLabels = <?php echo $bar_labels_json; ?>;
            const barData = <?php echo $bar_data_json; ?>;

            // Ambil warna dari CSS Variables untuk konsistensi tema
            const barColor = getComputedStyle(document.documentElement).getPropertyValue('--side-bg');
            const barBorderColor = getComputedStyle(document.documentElement).getPropertyValue('--dark-brown');
            const gridColor = getComputedStyle(document.documentElement).getPropertyValue('--border-color');
            const labelColor = getComputedStyle(document.documentElement).getPropertyValue('--text-muted');
            const darkColor = getComputedStyle(document.documentElement).getPropertyValue('--button-primary');

            const ctxBar = document.getElementById('chartPelamarPerLowongan').getContext('2d');

            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: barLabels,
                    datasets: [{
                        label: 'Jumlah Pelamar',
                        data: barData,
                        backgroundColor: barColor,
                        borderColor: barBorderColor,
                        borderWidth: 1.5,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: darkColor,
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: gridColor,
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: labelColor,
                                callback: function (value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: labelColor
                            }
                        }
                    }
                }
            });
        }

        // === 2. Inisialisasi Pie Chart (CanvasJS) ===
        if ($('#pieChartContainer').length) {
            // Ambil warna dari CSS Variable untuk konsistensi tulisan
            const pieLabelColor = getComputedStyle(document.documentElement).getPropertyValue('--text-primary');

            var chart = new CanvasJS.Chart("pieChartContainer", {
                animationEnabled: true,
                backgroundColor: "transparent",
                data: [{
                    type: "pie",
                    radius: "85%",
                    indexLabelFontSize: 10,
                    indexLabelFontColor: pieLabelColor,
                    indexLabel: "{label} - {y}",
                    yValueFormatString: "#,##0\" pelamar\"",
                    toolTipContent: "{label}: <strong>{y}</strong> pelamar",
                    dataPoints: <?php echo json_encode($pieDataPoints, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();
        }

    });
</script>


<?php
// Koneksi ditutup di sini setelah semua query dan pemrosesan selesai.
$koneksi->close();

// 7. Panggil Footer
include '../templates/hrd_footer.php';
?>