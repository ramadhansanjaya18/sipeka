<?php
require_once __DIR__ . '/../helpers/mail_helper.php';

$lamaran = null;
$error_message = "";
$upload_dir_foto = '../uploads/foto_profil/';
$upload_dir_docs = '../uploads/dokumen/';
$placeholder_foto = '../assets/img/placeholder-profile.jpg';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id_upd = filter_var($_POST['id_lamaran'], FILTER_VALIDATE_INT);
    $status = $_POST['status'];
    
    if ($id_upd) {
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status, $id_upd);
        
        if ($stmt->execute()) {
            $msg_email = "";
            if ($status == 'Diterima' || $status == 'Ditolak') {
                
                $q = "SELECT u.email, pp.nama_lengkap, l.posisi_dilamar 
                      FROM lamaran l JOIN user u ON l.id_pelamar = u.id_user 
                      LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user 
                      WHERE l.id_lamaran = ?";
                $s = $koneksi->prepare($q);
                $s->bind_param("i", $id_upd);
                $s->execute();
                $d = $s->get_result()->fetch_assoc();
                
                $err_mail = "";
                $sent = kirimNotifikasiEmailPelamar($d['email'], $d['nama_lengkap'], $status, $d['posisi_dilamar'], $err_mail);
                $msg_email = $sent ? " (Email Terkirim)" : " (Gagal Email)";
                $s->close();
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => "Status diperbarui.$msg_email"];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal update."];
        }
        $stmt->close();
    }
    header("Location: detail_pelamar.php?id_lamaran=" . $id_upd);
    exit();
}


if (isset($_GET['id_lamaran']) && !empty($_GET['id_lamaran'])) {
    $id = (int) $_GET['id_lamaran'];
    
    
    $sql = "SELECT l.id_lamaran, l.status_lamaran, l.posisi_dilamar,
                   pp.nama_lengkap, pp.no_telepon, pp.alamat, pp.tempat_tanggal_lahir,
                   pp.riwayat_pendidikan, pp.pengalaman_kerja, pp.keahlian, pp.foto_profil,
                   pp.dokumen_cv, pp.surat_lamaran, pp.sertifikat_pendukung, pp.ijasah,
                   u.email
            FROM lamaran l
            JOIN user u ON l.id_pelamar = u.id_user
            LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user
            WHERE l.id_lamaran = ?";
            
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows == 1) {
        $lamaran = $res->fetch_assoc();
        
        $foto_path = (!empty($lamaran['foto_profil']) && file_exists($upload_dir_foto.$lamaran['foto_profil'])) 
                     ? $upload_dir_foto.$lamaran['foto_profil'] 
                     : $placeholder_foto;
    } else {
        $error_message = "Data tidak ditemukan.";
    }
    $stmt->close();
} else {
    $error_message = "ID tidak valid.";
}
?>