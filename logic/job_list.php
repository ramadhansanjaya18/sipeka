<?php
/**
 * Logika Pengambilan Daftar Lowongan
 */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$result_jobs = null;

// Query dasar: Ambil lowongan yang aktif (tanggal hari ini di antara buka & tutup)
$query_base = "SELECT 
                    id_lowongan, 
                    judul, 
                    posisi_lowongan, 
                    deskripsi_singkat, 
                    DATE_FORMAT(tanggal_tutup, '%d %M %Y') AS tanggal_tutup_formatted
                FROM 
                    lowongan 
                WHERE 
                    CURDATE() BETWEEN tanggal_buka AND tanggal_tutup";

// Logika Pencarian
if (!empty($search)) {
    $search_param = "%{$search}%";
    // Menggunakan prepared statement untuk keamanan pencarian
    $query_full = $query_base . " AND (judul LIKE ? OR posisi_lowongan LIKE ?) ORDER BY tanggal_buka DESC";
    
    $stmt = $koneksi->prepare($query_full);
    if ($stmt) {
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result_jobs = $stmt->get_result();
        // $stmt->close(); // Close dilakukan nanti atau otomatis oleh PHP
    }
} else {
    // Jika tidak ada pencarian, ambil semua
    $query_full = $query_base . " ORDER BY tanggal_buka DESC";
    $result_jobs = $koneksi->query($query_full);
}
?>