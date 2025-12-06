<?php
// =================================================================
// 1. Load Library PHPMailer & Konfigurasi Dasar
// =================================================================
include_once 'templates/header.php';

// Pastikan jalur file ini sesuai dengan struktur folder Anda
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status_pesan = "";
$tipe_pesan = "";

// =================================================================
// 2. Proses Pengiriman Saat Form Disubmit
// =================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data input dari Form
    $nama = htmlspecialchars(trim($_POST['nama']));
    $email_pengirim = htmlspecialchars(trim($_POST['email'])); // Email pengunjung
    $telepon = htmlspecialchars(trim($_POST['phone']));
    $isi_pesan = htmlspecialchars(trim($_POST['message']));

    // Validasi
    if (empty($nama) || empty($email_pengirim) || empty($isi_pesan)) {
        $status_pesan = "Nama, Email, dan Pesan wajib diisi.";
        $tipe_pesan = "error";
    } else {
        $mail = new PHPMailer(true);

        try {
            // --- Konfigurasi Server SMTP (GMAIL) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // -----------------------------------------------------------
            // BAGIAN INI WAJIB DIISI DENGAN EMAIL & APP PASSWORD ADMIN
            // -----------------------------------------------------------
            // Email ini bertindak sebagai "Kurir" pengirim surat
            $mail->Username   = 'hrdsyjuracoffe@gmail.com'; 
            
            // Masukkan 16 digit App Password Anda di sini (BUKAN password login biasa)
            $mail->Password   = 'vtzl yffh yimv pcpa'; 
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // --- Pengaturan Pengirim & Penerima ---
            
            // 1. FROM: Tetap harus email Admin agar tidak diblokir Google (Anti-Spam)
            // Tapi kita ubah 'Nama'-nya agar terlihat dari pengunjung
            $mail->setFrom('hrdsyjuracoffe@gmail.com', "$nama (via Website SIPEKA)"); 
            
            // 2. REPLY-TO: Ini kuncinya! Jika admin klik "Reply", akan ke email pengunjung
            $mail->addReplyTo($email_pengirim, $nama); 
            
            // 3. ADD ADDRESS: Email tujuan (Admin HRD)
            $mail->addAddress('hrdsyjuracoffe@gmail.com', 'Admin HRD'); 

            // --- Konten Email ---
            $mail->isHTML(true);
            $mail->Subject = "Pesan Baru dari: $nama";
            
            // Desain Body Email
            $mail->Body    = "
                <h3>Pesan Baru Masuk</h3>
                <p><strong>Dari:</strong> $nama ($email_pengirim)</p>
                <p><strong>No. Telepon:</strong> $telepon</p>
                <br>
                <p><strong>Isi Pesan:</strong></p>
                <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #6A4E3B;'>
                    $isi_pesan
                </div>
            ";
            
            // Versi teks biasa untuk email client lama
            $mail->AltBody = "Dari: $nama ($email_pengirim)\nTelepon: $telepon\nPesan: $isi_pesan";

            $mail->send();
            $status_pesan = "Pesan berhasil dikirim! Kami akan segera menghubungi Anda.";
            $tipe_pesan = "success";

        } catch (Exception $e) {
            $status_pesan = "Gagal mengirim pesan. Pastikan koneksi internet lancar. Error: {$mail->ErrorInfo}";
            $tipe_pesan = "error";
        }
    }
}
?>

<style>
    .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; width: 100%; text-align: center; font-weight: 600; }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<section class="contact-section">
    <div class="contact-header">
        <h1>Hubungi Kami</h1>
        <p>Ada pertanyaan atau komentar? Cukup tulis pesan kepada kami!</p>
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Informasi Kontak</h2>
            <p>Jika anda mempunyai pertanyaan, silakan hubungi kami:</p>
            <div class="contact-list">
                <div><img src="./assets/img/contact/phone.png" alt="icon-phone"><span>0274 123456</span></div>
                <div><img src="./assets/img/contact/email.png" alt="icon-email"><span>hrdsyjuracoffe@gmail.com</span></div>
                <div><img src="./assets/img/contact/location.png" alt="icon-location"><span>Jl. Lohbener, Pamayahan</span></div>
            </div>
        </div>

        <form class="contact-form" action="" method="post">
            
            <?php if (!empty($status_pesan)): ?>
                <div class="alert alert-<?php echo $tipe_pesan; ?>">
                    <?php echo $status_pesan; ?>
                </div>
            <?php endif; ?>

            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Contoh: pengunjung@gmail.com">

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button class="button-submit" type="submit">KIRIM PESAN</button>
        </form>
    </div>
</section>

<?php
include_once 'templates/footer.php';
?>