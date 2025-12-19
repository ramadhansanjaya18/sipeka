<?php
/**
 * Logika Halaman Status Lamaran
 * Mengambil riwayat lamaran dan jadwal wawancara pelamar yang sedang login.
 */

// Pastikan koneksi dan session tersedia
if (!isset($koneksi) || !isset($_SESSION['id_user'])) {
    // Jika file ini diakses langsung tanpa melalui index/header
    header("Location: ../login.php");
    exit;
}

$id_pelamar = $_SESSION['id_user'];
$nama_pelamar = $_SESSION['nama_lengkap'] ?? 'Pelamar'; 

// Query untuk mengambil data lamaran beserta detail lowongan dan jadwal wawancara (jika ada)
$query = "SELECT 
            l.judul AS judul_lowongan,
            l.posisi_lowongan,
            la.id_lamaran,
            la.tanggal_lamaran,
            la.status_lamaran,
            la.id_lowongan,
            w.jadwal,
            w.lokasi,
            w.catatan
          FROM lamaran la
          JOIN lowongan l ON la.id_lowongan = l.id_lowongan
          LEFT JOIN wawancara w ON la.id_lamaran = w.id_lamaran
          WHERE la.id_pelamar = ?
          ORDER BY la.tanggal_lamaran DESC";

$stmt = $koneksi->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $id_pelamar);
    $stmt->execute();
    $result_lamaran = $stmt->get_result();
    // $stmt->close(); // Close nanti setelah loop di view selesai
} else {
    // Penanganan error jika query gagal prepare
    $result_lamaran = null;
    error_log("Query Error di status_lamaran_logic.php: " . $koneksi->error);
}
?>