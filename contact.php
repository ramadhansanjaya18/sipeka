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
            $mail->Username   = 'hrdsyjuracoffe@gmail.com'; 
            $mail->Password   = 'vtzl yffh yimv pcpa'; 
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // --- Pengaturan Pengirim & Penerima ---
            $mail->setFrom('hrdsyjuracoffe@gmail.com', "$nama (via Website SIPEKA)"); 
            $mail->addReplyTo($email_pengirim, $nama); 
            $mail->addAddress('hrdsyjuracoffe@gmail.com', 'Admin HRD'); 

            // --- Konten Email ---
            $mail->isHTML(true);
            $mail->Subject = "Pesan Baru dari: $nama";
            
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
    /* CSS Alert yang Dimodifikasi */
    .alert { 
        position: relative; /* Agar tombol close bisa diposisikan absolut */
        padding: 15px; 
        padding-right: 40px; /* Memberi ruang agar teks tidak tertumpuk tombol X */
        margin-bottom: 20px; 
        border-radius: 5px; 
        width: 100%; 
        text-align: center; 
        font-weight: 600;
        opacity: 1;
        transition: opacity 0.6s; /* Efek memudar saat hilang */
    }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Style untuk tombol X (Close Button) */
    .closebtn {
        position: absolute;
        top: 15px;
        right: 15px;
        color: inherit;
        font-weight: bold;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
        opacity: 0.7;
    }
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
                <div class="alert alert-<?php echo $tipe_pesan; ?>" id="notification">
                    <?php echo $status_pesan; ?>
                    <span class="closebtn" onclick="this.parentElement.style.opacity='0'; setTimeout(function(){ this.parentElement.style.display='none'; }, 600);">&times;</span>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var notification = document.getElementById("notification");
        
        // Cek apakah notifikasi muncul
        if (notification) {
            // Tunggu 5 detik (5000 ms) sebelum mulai menghilang
            setTimeout(function() {
                notification.style.opacity = "0"; // Mulai efek transisi (memudar)
                
                // Hapus elemen dari tampilan setelah transisi selesai (sesuai durasi CSS 0.6s)
                setTimeout(function() { 
                    notification.style.display = "none"; 
                }, 600);
            }, 5000);
        }
    });
</script>

<?php
include_once 'templates/footer.php';
?>