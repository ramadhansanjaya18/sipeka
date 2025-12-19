<?php
/**
 * Logic: Manajemen Pelamar (List & Update Cepat)
 */

// Memuat Helper Email
require_once __DIR__ . '/../helpers/mail_helper.php';

// Pastikan koneksi tersedia
if (!isset($koneksi)) require_once __DIR__ . '/../config/koneksi.php';

// --- A. PROSES UPDATE STATUS (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_lamaran = filter_var($_POST['id_lamaran'], FILTER_VALIDATE_INT);
    $status_baru = $_POST['status_baru'];
    $valid_status = ['Diproses', 'Diterima', 'Ditolak', 'Wawancara'];

    if ($id_lamaran && in_array($status_baru, $valid_status)) {
        // 1. Update Database
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status_baru, $id_lamaran);

        if ($stmt->execute()) {
            $msg_extra = "";
            
            // 2. Kirim Email (Gunakan Helper)
            if ($status_baru == 'Diterima' || $status_baru == 'Ditolak') {
                // Ambil info pelamar
                $q = "SELECT u.email, pp.nama_lengkap, l.posisi_dilamar 
                      FROM lamaran l 
                      JOIN user u ON l.id_pelamar = u.id_user 
                      LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user 
                      WHERE l.id_lamaran = ?";
                $s = $koneksi->prepare($q);
                $s->bind_param("i", $id_lamaran);
                $s->execute();
                $data = $s->get_result()->fetch_assoc();
                
                if ($data) {
                    $err = "";
                    $kirim = kirimNotifikasiEmailPelamar($data['email'], $data['nama_lengkap'], $status_baru, $data['posisi_dilamar'], $err);
                    $msg_extra = $kirim ? " (Notifikasi Terkirim)" : " (Gagal Kirim Email)";
                }
                $s->close();
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => "Status diubah ke $status_baru. $msg_extra"];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal update database."];
        }
        $stmt->close();
    }
    
    // Redirect agar tidak resubmit
    header("Location: pelamar.php" . (isset($_GET['search']) ? "?search=".$_GET['search'] : ""));
    exit();
}

// --- B. PROSES READ DATA (GET) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT l.id_lamaran, pp.nama_lengkap, l.posisi_dilamar, 
            DATE_FORMAT(l.tanggal_lamaran, '%d-%m-%Y') as tgl, l.status_lamaran
          FROM lamaran l
          JOIN user u ON l.id_pelamar = u.id_user
          LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user";

if (!empty($search)) {
    $param = "%$search%";
    $query .= " WHERE pp.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? ORDER BY l.tanggal_lamaran DESC";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $param, $param);
    $stmt->execute();
    $result_lamaran = $stmt->get_result();
} else {
    $query .= " ORDER BY l.tanggal_lamaran DESC";
    $result_lamaran = $koneksi->query($query);
}
?>