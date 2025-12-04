<?php
/**
 * Skrip Pemrosesan Lamaran Pekerjaan.
 *
 * File ini adalah endpoint yang dipanggil saat pelamar menekan tombol "Lamar".
 * Tugasnya adalah melakukan serangkaian validasi dan, jika semua valid,
 * membuat entri baru di tabel `lamaran`.
 * Skrip ini tidak menghasilkan output HTML, hanya melakukan redirect.
 */

// 1. Panggil init.php untuk session dan koneksi
include_once __DIR__ . '/config/init.php';

// 2. Pastikan yang mengakses adalah pelamar yang sudah login
include_once __DIR__ . '/config/auth_pelamar.php';

// 3. Validasi ID Lowongan dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Lowongan tidak valid.'];
    header("Location: index.php");
    exit();
}
$id_lowongan = (int)$_GET['id'];
$id_pelamar = $_SESSION['id_user'];

// 4. --- Validasi Prasyarat ---

// 4a. Ambil data lowongan untuk divalidasi
$stmt_lowongan = $koneksi->prepare("SELECT posisi_lowongan, (CURDATE() BETWEEN tanggal_buka AND tanggal_tutup) AS is_active FROM lowongan WHERE id_lowongan = ?");
$stmt_lowongan->bind_param("i", $id_lowongan);
$stmt_lowongan->execute();
$result_lowongan = $stmt_lowongan->get_result();
if ($result_lowongan->num_rows == 0) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Data lowongan tidak ditemukan.'];
    header("Location: index.php");
    exit();
}
$lowongan_data = $result_lowongan->fetch_assoc();
$stmt_lowongan->close();

// 4b. Ambil data pelamar untuk validasi CV
$stmt_pelamar = $koneksi->prepare("SELECT dokumen_cv FROM user WHERE id_user = ?");
$stmt_pelamar->bind_param("i", $id_pelamar);
$stmt_pelamar->execute();
$pelamar_data = $stmt_pelamar->get_result()->fetch_assoc();
$stmt_pelamar->close();

// 4c. Validasi: Apakah CV sudah diunggah?
if (empty($pelamar_data['dokumen_cv'])) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Anda harus mengunggah CV di halaman profil Anda sebelum melamar.'];
    header("Location: profil.php");
    exit();
}

// 4d. Validasi: Apakah lowongan masih aktif?
if (!$lowongan_data['is_active']) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Lowongan ini sudah ditutup.'];
    header("Location: detail_lowongan.php?id=" . $id_lowongan);
    exit();
}

// 4e. Validasi: Apakah sudah pernah melamar lowongan yang sama?
$stmt_cek = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_pelamar = ? AND id_lowongan = ?");
$stmt_cek->bind_param("ii", $id_pelamar, $id_lowongan);
$stmt_cek->execute();
if ($stmt_cek->get_result()->num_rows > 0) {
    $_SESSION['flash_message'] = ['type' => 'info', 'text' => 'Anda sudah pernah melamar lowongan ini.'];
    header("Location: status_lamaran.php");
    exit();
}
$stmt_cek->close();

// 5. --- Proses Lamaran (INSERT) ---
// Semua validasi lolos, lanjutkan untuk menyimpan lamaran.
$status_default = "Diproses";
$tanggal_lamaran = date("Y-m-d H:i:s");
$posisi_dilamar = $lowongan_data['posisi_lowongan'];

$query_insert = "INSERT INTO lamaran (id_pelamar, id_lowongan, tanggal_lamaran, status_lamaran, posisi_dilamar) 
                 VALUES (?, ?, ?, ?, ?)";
$stmt_insert = $koneksi->prepare($query_insert);
$stmt_insert->bind_param("iisss", $id_pelamar, $id_lowongan, $tanggal_lamaran, $status_default, $posisi_dilamar);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Lamaran Anda untuk posisi ' . htmlspecialchars($posisi_dilamar, ENT_QUOTES, 'UTF-8') . ' berhasil dikirim!'];
    header("Location: status_lamaran.php");
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan sistem. Gagal mengirim lamaran.'];
    header("Location: detail_lowongan.php?id=" . $id_lowongan);
}

$stmt_insert->close();
$koneksi->close();
exit(); // Pastikan skrip berhenti di sini.
