<?php
if (!isset($koneksi) || !isset($_SESSION['id_user'])) {   
    header("Location: ../login.php");
    exit;
}

$id_pelamar = $_SESSION['id_user'];
$nama_pelamar = $_SESSION['nama_lengkap'] ?? 'Pelamar'; 


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
} else {
    $result_lamaran = null;
    error_log("Query Error di status_lamaran_logic.php: " . $koneksi->error);
}
?>