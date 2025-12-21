<?php

if (!isset($koneksi)) {
    die("Koneksi database tidak tersedia.");
}

$total_pelamar = 0;
$lowongan_aktif = 0;
$wawancara_terjadwal = 0;
$pelamar_baru_hari_ini = 0;

$result1 = $koneksi->query("SELECT COUNT(DISTINCT id_pelamar) AS total FROM lamaran");
if ($result1) $total_pelamar = $result1->fetch_assoc()['total'];

$result2 = $koneksi->query("SELECT COUNT(id_lowongan) AS total FROM lowongan WHERE CURDATE() >= tanggal_buka AND CURDATE() <= tanggal_tutup");
if ($result2) $lowongan_aktif = $result2->fetch_assoc()['total'];

$result3 = $koneksi->query("SELECT COUNT(id_wawancara) AS total FROM wawancara WHERE status_wawancara = 'Terjadwal'");
if ($result3) $wawancara_terjadwal = $result3->fetch_assoc()['total'];

$result4 = $koneksi->query("SELECT COUNT(id_lamaran) AS total FROM lamaran WHERE DATE(tanggal_lamaran) = CURDATE()");
if ($result4) $pelamar_baru_hari_ini = $result4->fetch_assoc()['total'];

$query_bar = "SELECT COALESCE(posisi_dilamar, 'Lainnya') AS posisi, COUNT(id_lamaran) AS jumlah 
              FROM lamaran GROUP BY posisi ORDER BY jumlah DESC";
$result_bar = $koneksi->query($query_bar);

$bar_labels = [];
$bar_data = [];
if ($result_bar) {
    while ($row = $result_bar->fetch_assoc()) {
        $bar_labels[] = $row['posisi'];
        $bar_data[]   = (int) $row['jumlah'];
    }
}
$json_bar_labels = json_encode($bar_labels);
$json_bar_data   = json_encode($bar_data);

$query_pie = "SELECT status_lamaran, COUNT(id_lamaran) AS jumlah 
              FROM lamaran 
              WHERE status_lamaran IN ('Diproses', 'Diterima', 'Ditolak', 'Wawancara') 
              GROUP BY status_lamaran";
$result_pie = $koneksi->query($query_pie);

$pieDataPoints = [];
$warna_status = [
    'Wawancara' => '#E9DFD2',
    'Diterima'  => '#9EA98E',
    'Ditolak'   => '#C98664',
    'Diproses'  => '#8BC7C0'
];

if ($result_pie) {
    while ($row = $result_pie->fetch_assoc()) {
        $status = $row['status_lamaran'];
        $pieDataPoints[] = [
            "label" => $status,
            "y"     => (int) $row['jumlah'],
            "color" => $warna_status[$status] ?? null
        ];
    }
}
$json_pie_data = json_encode($pieDataPoints, JSON_NUMERIC_CHECK);
?>