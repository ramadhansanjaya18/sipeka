<?php
/**
 * Halaman Dashboard HRD (View)
 */

// 1. Panggil Header (sudah include init & auth_hrd)
include '../templates/hrd_header.php';

// 2. Panggil Logic Dashboard
require_once '../logic/hrd_dashboard_logic.php';
?>

<div class="dashboard-container">
    <div class="page-title">
        <h1>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
        <p>Berikut ringkasan aktivitas rekrutmen hari ini.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-pelamar">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/groups.png" alt="groups" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($total_pelamar); ?></h2>
                <p>Total Pelamar</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-lowongan">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 50 50" fill="currentColor">
                     <path d="M 20 3 C 18.355469 3 17 4.355469 17 6 L 17 9 L 3 9 C 1.347656 9 0 10.347656 0 12 L 0 25 C 0 26.652344 1.347656 28 3 28 L 47 28 C 48.652344 28 50 26.652344 50 25 L 50 12 C 50 10.347656 48.652344 9 47 9 L 33 9 L 33 6 C 33 4.355469 31.644531 3 30 3 Z M 20 5 L 30 5 C 30.5625 5 31 5.4375 31 6 L 31 9 L 19 9 L 19 6 C 19 5.4375 19.4375 5 20 5 Z M 25 22 C 26.105469 22 27 22.894531 27 24 C 27 25.105469 26.105469 26 25 26 C 23.894531 26 23 25.105469 23 24 C 23 22.894531 23.894531 22 25 22 Z M 0 27 L 0 44 C 0 45.652344 1.347656 47 3 47 L 47 47 C 48.652344 47 50 45.652344 50 44 L 50 27 C 50 28.652344 48.652344 30 47 30 L 3 30 C 1.347656 30 0 28.652344 0 27 Z"></path>
                </svg>
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($lowongan_aktif); ?></h2>
                <p>Lowongan Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-wawancara">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/month-view.png" alt="month-view" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($wawancara_terjadwal); ?></h2>
                <p>Wawancara Terjadwal</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-pelamar-baru">
                <img width="40" height="40" src="https://img.icons8.com/ios-filled/50/add-user-male.png" alt="add-user-male" />
            </div>
            <div class="stat-info">
                <h2><?php echo htmlspecialchars($pelamar_baru_hari_ini); ?></h2>
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
                <div id="pieChartContainer" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<div id="dashboardChartData" 
     style="display: none;" 
     data-bar-labels='<?php echo $json_bar_labels; ?>' 
     data-bar-data='<?php echo $json_bar_data; ?>'
     data-pie-data='<?php echo $json_pie_data; ?>'>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>

<?php
// Inject Script Dashboard
$extra_js = '../assets/js/hrd_dashboard.js';
require_once '../templates/hrd_footer.php';
?>