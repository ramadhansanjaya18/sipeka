<?php
/**
 * Halaman Manajemen Pelamar.
 * Dilengkapi notifikasi email dengan FIX SSL (SMTPOptions) untuk Localhost.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$page = 'pelamar';
include '../templates/hrd_header.php';

// Load PHPMailer
require_once '../PHPMailer/Exception.php';
require_once '../PHPMailer/PHPMailer.php';
require_once '../PHPMailer/SMTP.php';

$message = "";

// --- FUNGSI KIRIM EMAIL (Sudah Diperbaiki dengan SSL Fix) ---
function kirimNotifikasiEmailPelamar($email_tujuan, $nama_pelamar, $status, $posisi, &$pesan_error) {
    $mail = new PHPMailer(true);
    try {
        // --- KONFIGURASI SMTP ---
        // PENTING: Pastikan Email & App Password disalin dari detail_pelamar.php
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        // --- GANTI BAGIAN INI DENGAN EMAIL ASLI ANDA ---
        $mail->Username   = 'hrdsyjuracoffe@gmail.com';   // <--- GANTI INI
        $mail->Password   = 'vtzl yffh yimv pcpa';      // <--- GANTI INI (16 digit)
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // --- FIX SSL UNTUK XAMPP/LOCALHOST (WAJIB ADA) ---
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
     // Konten Email
        $mail->isHTML(true);

        // Styling dasar untuk email agar terlihat rapi
        $header_email = "
            <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px;'>
                <h2 style='color: #8B4513;'>SYJURA COFFEE</h2>
                <hr style='border: 0; border-top: 1px solid #eee;'>
        ";

        $footer_email = "
                <br><br>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 12px; color: #777;'>
                    <b>HR Department - Syjura Coffee</b><br>
                    Jl. Lohbener, Pamayahan<br>
                    Website: sipeka.gt.tc | Email: hrd@syjuracoffee.com
                </p>
                <p style='font-size: 11px; color: #999;'>
                    <i>Email ini dibuat secara otomatis oleh sistem SIPEKA. Mohon untuk tidak membalas email ini secara langsung.</i>
                </p>
            </div>
        ";

        if ($status == 'Diterima') {
            $mail->Subject = "Konfirmasi Penerimaan Lamaran Kerja - Posisi $posisi";
            $mail->Body    = $header_email . "
                <h3>Yth. Sdr/i $nama_pelamar,</h3>
                
                <p>Terima kasih telah mengikuti serangkaian proses seleksi di <b>Syjura Coffee</b>.</p>
                
                <p>Berdasarkan hasil evaluasi kualifikasi dan wawancara, dengan senang hati kami sampaikan bahwa Anda dinyatakan <b>DITERIMA</b> untuk bergabung bersama kami pada posisi <b>$posisi</b>.</p>
                
                <p>Kami percaya bahwa kemampuan dan pengalaman yang Anda miliki akan memberikan kontribusi positif bagi tim kami. Selanjutnya, perwakilan dari tim HRD kami akan segera menghubungi Anda melalui telepon atau WhatsApp untuk membahas penawaran kerja (Offering Letter) dan jadwal orientasi.</p>
                
                <p>Sekali lagi, selamat bergabung dengan keluarga besar Syjura Coffee.</p>
                
                <p>Salam hangat,</p>
            " . $footer_email;

        } elseif ($status == 'Ditolak') {
            $mail->Subject = "Informasi Status Lamaran Kerja - Posisi $posisi";
            $mail->Body    = $header_email . "
                <h3>Yth. Sdr/i $nama_pelamar,</h3>
                
                <p>Terima kasih banyak atas ketertarikan Anda untuk berkarir di <b>Syjura Coffee</b> dan telah meluangkan waktu untuk mengikuti proses seleksi posisi <b>$posisi</b>.</p>
                
                <p>Kami telah meninjau kualifikasi Anda secara menyeluruh. Namun, dengan berat hati kami sampaikan bahwa saat ini kami belum dapat melanjutkan proses lamaran Anda ke tahap berikutnya, karena kami telah memutuskan untuk melanjutkan dengan kandidat lain yang profilnya lebih mendekati kebutuhan spesifik kami saat ini.</p>
                
                <p>Profil Anda akan kami simpan dalam database talenta kami. Apabila terdapat lowongan yang sesuai dengan kualifikasi Anda di masa mendatang, kami tidak akan ragu untuk menghubungi Anda kembali.</p>
                
                <p>Kami mendoakan yang terbaik untuk kesuksesan karir Anda ke depannya.</p>
                
                <p>Hormat kami,</p>
            " . $footer_email;
        }
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Tangkap pesan error asli
        $pesan_error = $mail->ErrorInfo;
        return false;
    }
}

// --- LOGIKA UPDATE STATUS (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $id_lamaran = $_POST['id_lamaran'];
    $status_baru = $_POST['status_baru'];
    $valid_status = ['Diproses', 'Diterima', 'Ditolak', 'Wawancara'];

    if (in_array($status_baru, $valid_status)) {
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status_baru, $id_lamaran);

        if ($stmt->execute()) {
            $msg_add = "";
            $error_mail_msg = "";

            // Kirim email hanya jika Diterima / Ditolak
            if ($status_baru == 'Diterima' || $status_baru == 'Ditolak') {
                // Ambil data pelamar (Join ke profil_pelamar & user)
                $q = "SELECT u.email, pp.nama_lengkap, l.posisi_dilamar 
                      FROM lamaran l
                      JOIN user u ON l.id_pelamar = u.id_user
                      LEFT JOIN profil_pelamar pp ON u.id_user = pp.id_user
                      WHERE l.id_lamaran = ?";
                $s = $koneksi->prepare($q);
                $s->bind_param("i", $id_lamaran);
                $s->execute();
                $res = $s->get_result();
                
                if ($r = $res->fetch_assoc()) {
                    // Panggil fungsi kirim email
                    $sent = kirimNotifikasiEmailPelamar($r['email'], $r['nama_lengkap'], $status_baru, $r['posisi_dilamar'], $error_mail_msg);
                    
                    if ($sent) {
                        $msg_add = " (Email notifikasi BERHASIL terkirim)";
                    } else {
                        // Tampilkan alasan error di layar
                        $msg_add = " <br><b>Info Email:</b> Gagal kirim. Error: " . $error_mail_msg;
                    }
                }
                $s->close();
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => "Status diperbarui.$msg_add"];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal update status database."];
        }
        $stmt->close();
    }
    
    // Redirect
    $url = 'pelamar.php';
    if (!empty($_GET['search'])) $url .= '?search=' . urlencode($_GET['search']);
    header("Location: " . $url);
    exit();
}

// --- READ DATA (TAMPILKAN TABEL) ---
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

<div class="page-title">
    <h1>Manajemen Data Pelamar</h1>
    <p>Kelola data pelamar yang telah mengajukan lamaran pekerjaan.</p>
</div>

<div class="page-actions">
    <div class="search-container">
        <form action="pelamar.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar atau posisi..."
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                    <g fill="#6a4e3b" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt"
                        stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0"
                        font-family="none" font-weight="none" font-size="none" text-anchor="none"
                        style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path
                                d="M21,3c-9.37891,0 -17,7.62109 -17,17c0,9.37891 7.62109,17 17,17c3.71094,0 7.14063,-1.19531 9.9375,-3.21875l13.15625,13.125l2.8125,-2.8125l-13,-13.03125c2.55469,-2.97656 4.09375,-6.83984 4.09375,-11.0625c0,-9.37891 -7.62109,-17 -17,-17zM21,5c8.29688,0 15,6.70313 15,15c0,8.29688 -6.70312,15 -15,15c-8.29687,0 -15,-6.70312 -15,-15c0,-8.29687 6.70313,-15 15,-15z">
                            </path>
                        </g>
                    </g>
                </svg>
            </button>
        </form>
    </div>
</div>

<?php echo $message; // Pesan notifikasi muncul di sini ?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_lamaran && $result_lamaran->num_rows > 0): ?>
                <?php while ($row = $result_lamaran->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? 'Pelamar'); ?></td>
                        <td><?php echo htmlspecialchars($row['posisi_dilamar']); ?></td>
                        <td><?php echo htmlspecialchars($row['tgl']); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id_lamaran" value="<?php echo $row['id_lamaran']; ?>">
                                <select name="status_baru" onchange="this.form.submit()" class="table-select">
                                    <?php 
                                    foreach(['Diproses','Wawancara','Diterima','Ditolak'] as $st) {
                                        $sel = ($row['status_lamaran'] == $st) ? 'selected' : '';
                                        echo "<option value='$st' $sel>$st</option>";
                                    }
                                    ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="detail_pelamar.php?id_lamaran=<?php echo $row['id_lamaran']; ?>" class="btn-view"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" align="center">Data tidak ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include '../templates/hrd_footer.php';
$koneksi->close();
?>