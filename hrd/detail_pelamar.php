<?php
/**
 * Halaman detail pelamar untuk HRD.
 * VERSI: Tampilan Dokumen List (Bukan Card) dengan Teks Tombol Spesifik
 */

ob_start();

// 1. Load library PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

// 2. Panggil Header
include '../templates/hrd_header.php';

// Inisialisasi variabel
$lamaran = null;
$error_message = "";
$message = ""; 
$upload_dir_foto = '../uploads/foto_profil/';
$upload_dir_docs = '../uploads/dokumen/';
$placeholder_foto = '../assets/img/placeholder-profile.png';

// --- FUNGSI KIRIM EMAIL ---
function kirimNotifikasiEmail($email_tujuan, $nama_pelamar, $status, $posisi, &$pesan_error_detail) {
    $mail = new PHPMailer(true);

    try {
        // --- KONFIGURASI SMTP ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hrdsyjuracoffe@gmail.com';     // GANTI EMAIL ANDA
        $mail->Password   = 'vtzl yffh yimv pcpa';          // GANTI APP PASSWORD ANDA
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // FIX SSL (Untuk Localhost/XAMPP)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Pengirim & Penerima
        $mail->setFrom('noreply@syjuracoffee.com', 'HRD Syjura Coffee');
        $mail->addAddress($email_tujuan, $nama_pelamar);

        // Konten Email
        $mail->isHTML(true);
        
        if ($status == 'Diterima') {
            $mail->Subject = "Selamat! Lamaran Anda Diterima - Syjura Coffee";
            $mail->Body    = "
                <h3>Halo, $nama_pelamar</h3>
                <p>Selamat! Kami sampaikan bahwa Anda <b>DITERIMA</b> untuk posisi <b>$posisi</b> di Syjura Coffee.</p>
                <p>Tim HRD akan segera menghubungi Anda untuk langkah selanjutnya.</p>
                <br><p>Salam,<br>HRD Syjura Coffee</p>";
        } elseif ($status == 'Ditolak') {
            $mail->Subject = "Status Lamaran - Syjura Coffee";
            $mail->Body    = "
                <h3>Halo, $nama_pelamar</h3>
                <p>Terima kasih telah melamar posisi <b>$posisi</b>.</p>
                <p>Mohon maaf, saat ini kami belum dapat melanjutkan proses lamaran Anda.</p>
                <br><p>Salam,<br>HRD Syjura Coffee</p>";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        $pesan_error_detail = $mail->ErrorInfo;
        return false; 
    }
}

// --- LOGIKA UPDATE STATUS (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id_lamaran_update = $_POST['id_lamaran'];
    $status_baru = $_POST['status'];

    $status_valid = ['Diproses', 'Wawancara', 'Diterima', 'Ditolak'];
    if (in_array($status_baru, $status_valid)) {
        // Update Database
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status_baru, $id_lamaran_update);
        
        if ($stmt->execute()) {
            $notif_msg = "";
            $error_mail = "";
            $terkirim = false;
            
            // Cek apakah perlu kirim email (Hanya jika Diterima / Ditolak)
            if ($status_baru == 'Diterima' || $status_baru == 'Ditolak') {
                $q_mail = "SELECT u.email, pp.nama_lengkap, l.posisi_dilamar 
                           FROM lamaran l
                           JOIN user u ON l.id_pelamar = u.id_user
                           LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user
                           WHERE l.id_lamaran = ?";
                $stmt_m = $koneksi->prepare($q_mail);
                $stmt_m->bind_param("i", $id_lamaran_update);
                $stmt_m->execute();
                $res_m = $stmt_m->get_result();

                if ($row_m = $res_m->fetch_assoc()) {
                    $terkirim = kirimNotifikasiEmail($row_m['email'], $row_m['nama_lengkap'], $status_baru, $row_m['posisi_dilamar'], $error_mail);
                    
                    if ($terkirim) {
                        $notif_msg = " (Email notifikasi BERHASIL terkirim)";
                    } else {
                        $notif_msg = " (Email GAGAL: " . $error_mail . ")";
                    }
                }
                $stmt_m->close();
            }

            $_SESSION['message'] = ['type' => $terkirim ? 'success' : ($error_mail ? 'warning' : 'success'), 'text' => "Status diubah menjadi '$status_baru'. $notif_msg"];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui status database.'];
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Status tidak valid.'];
    }

    header("Location: detail_pelamar.php?id_lamaran=" . $id_lamaran_update);
    exit();
}

// --- LOGIKA READ DATA LAMARAN ---
if (isset($_GET['id_lamaran']) && !empty($_GET['id_lamaran'])) {
    $id_lamaran = (int) $_GET['id_lamaran'];

    $query = "SELECT 
                l.id_lamaran, l.id_pelamar, l.id_lowongan, l.status_lamaran,
                pp.nama_lengkap, pp.no_telepon, pp.alamat, pp.tempat_tanggal_lahir,
                pp.riwayat_pendidikan, pp.pengalaman_kerja, pp.keahlian, 
                pp.ringkasan_pribadi, pp.foto_profil,
                pp.dokumen_cv, pp.surat_lamaran, pp.sertifikat_pendukung, pp.ijasah,
                u.email,
                lw.posisi_lowongan
              FROM lamaran l
              JOIN user u ON l.id_pelamar = u.id_user
              LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user 
              JOIN lowongan lw ON l.id_lowongan = lw.id_lowongan
              WHERE l.id_lamaran = ?";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_lamaran);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $lamaran = $result->fetch_assoc();
    } else {
        $error_message = "Data lamaran tidak ditemukan.";
    }
    $stmt->close();
} else {
    $error_message = "ID Lamaran tidak valid.";
}

// Path Foto
$foto_profil_path = $placeholder_foto;
if ($lamaran && !empty($lamaran['foto_profil']) && file_exists($upload_dir_foto . $lamaran['foto_profil'])) {
    $foto_profil_path = $upload_dir_foto . $lamaran['foto_profil'];
}
?>

<div class="page-title">
    <h1>Detail Pelamaran</h1>
    <?php if ($lamaran): ?>
        <p>Lamaran untuk posisi <strong><?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?></strong></p>
    <?php endif; ?>
</div>

<div class="detail-container">
    <?php if (!empty($error_message)): ?>
        <div class="message animated error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php elseif ($lamaran): ?>
        
        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($foto_profil_path); ?>?t=<?php echo time(); ?>" alt="Foto Profil" class="profile-pic">

            <div class="profile-info">
                <h2><?php echo htmlspecialchars($lamaran['nama_lengkap'] ?? 'Pelamar'); ?></h2>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($lamaran['email']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($lamaran['no_telepon'] ?? '-'); ?></p>
            </div>

            <div class="profile-actions">
                <form action="" method="POST" class="status-update-form">
                    <input type="hidden" name="id_lamaran" value="<?php echo htmlspecialchars($lamaran['id_lamaran']); ?>">
                    <select name="status" class="form-control">
                        <?php
                        $status_list = ['Diproses', 'Wawancara', 'Diterima', 'Ditolak'];
                        foreach ($status_list as $status) {
                            $selected = ($lamaran['status_lamaran'] === $status) ? 'selected' : '';
                            echo "<option value='$status' $selected>$status</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                </form>

                <?php if (in_array($lamaran['status_lamaran'], ['Diproses', 'Wawancara'])): ?>
                    <a href="wawancara.php?id_lamaran=<?php echo $lamaran['id_lamaran']; ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i> Jadwalkan Wawancara
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="additional-info">
            <h3>Informasi Pelamar</h3>
            <dl class="info-grid">
                <dt>Alamat</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['alamat'] ?? '-')); ?></dd>
                <dt>Tempat/Tgl Lahir</dt>
                <dd><?php echo htmlspecialchars($lamaran['tempat_tanggal_lahir'] ?? '-'); ?></dd>
                <dt>Riwayat Pendidikan</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['riwayat_pendidikan'] ?? '-')); ?></dd>
                <dt>Pengalaman Kerja</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['pengalaman_kerja'] ?? '-')); ?></dd>
                <dt>Keahlian</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['keahlian'] ?? '-')); ?></dd>
            </dl>

            <h3 style="margin-top: 2rem;">Dokumen Lamaran</h3>
            <dl class="info-grid">
                <?php
                // Array definisi dokumen: Label Tampilan => [Nama Kolom Database, Label Tombol]
                $docs = [
                    'CV' => [
                        'col' => 'dokumen_cv', 
                        'btn_text' => 'Dokumen CV'
                    ],
                    'Surat Lamaran' => [
                        'col' => 'surat_lamaran', 
                        'btn_text' => 'Surat Lamaran'
                    ],
                    'Sertifikat Pendukung' => [
                        'col' => 'sertifikat_pendukung', 
                        'btn_text' => 'Sertifikat Pendukung'
                    ],
                    'Ijazah' => [
                        'col' => 'ijasah', 
                        'btn_text' => 'Ijasah'
                    ]
                ];

                foreach ($docs as $label => $data) {
                    $colName = $data['col'];
                    $btnLabel = $data['btn_text'];
                    
                    echo "<dt>" . htmlspecialchars($label) . "</dt><dd>";
                    
                    if (!empty($lamaran[$colName])) {
                        // Tampilkan tombol dengan teks spesifik
                        echo "<a href='" . htmlspecialchars($upload_dir_docs . $lamaran[$colName]) . "' target='_blank' class='btn-download'>";
                        echo "<i class='fas fa-file-pdf'></i> Lihat/Download " . htmlspecialchars($btnLabel);
                        echo "</a>";
                    } else {
                        echo "<span class='text-muted'>Tidak ada</span>";
                    }
                    
                    echo "</dd>";
                }
                ?>
            </dl>
            </div>
    <?php endif; ?>
    <br>
    <a href="pelamar.php" class="btn-batal"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<?php
include '../templates/hrd_footer.php';
$koneksi->close();
ob_end_flush();
?>