<?php
/**
 * Logic: Cetak Laporan Wawancara
 */

if (!isset($koneksi)) require_once __DIR__ . '/../config/koneksi.php';

// 1. Ambil Nama HRD
$id_user = $_SESSION['id_user'] ?? 0;
$nama_hrd = "HRD Manager";
$res_hrd = $koneksi->query("SELECT username FROM user WHERE id_user = $id_user");
if ($res_hrd->num_rows > 0) $nama_hrd = $res_hrd->fetch_assoc()['username'];

// 2. Filter Data
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_label = !empty($search) ? "Pencarian: \"$search\"" : "Semua Data";

$query = "SELECT w.id_wawancara, p.nama_lengkap, l.posisi_dilamar, 
            DATE_FORMAT(w.jadwal, '%d-%m-%Y') AS tanggal, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam, 
            w.lokasi, w.status_wawancara, w.catatan,
            p.no_telepon
          FROM wawancara w
          JOIN lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN user u ON l.id_pelamar = u.id_user
          JOIN profil_pelamar p ON u.id_user = p.id_user";

if (!empty($search)) {
    $s = $koneksi->real_escape_string($search);
    $query .= " WHERE p.nama_lengkap LIKE '%$s%' OR l.posisi_dilamar LIKE '%$s%' OR w.status_wawancara LIKE '%$s%'";
}

$query .= " ORDER BY w.jadwal ASC";
$result = $koneksi->query($query);
?>