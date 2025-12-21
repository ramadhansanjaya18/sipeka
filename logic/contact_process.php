<?php
require_once __DIR__ . '/../config/mail_config.php';
require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$status_pesan = "";
$tipe_pesan = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
    $email_pengirim = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telepon = htmlspecialchars(trim($_POST['phone'] ?? ''));
    $isi_pesan = htmlspecialchars(trim($_POST['message'] ?? ''));

    
    if (empty($nama) || empty($email_pengirim) || empty($isi_pesan)) {
        $status_pesan = "Nama, Email, dan Pesan wajib diisi.";
        $tipe_pesan = "error";
    } else {
        $mail = new PHPMailer(true);

        try {
            
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            
            
            $mail->setFrom(SMTP_USER, "$nama (via Website)"); 
            $mail->addReplyTo($email_pengirim, $nama); 
            $mail->addAddress(SMTP_USER, 'Admin HRD'); 

            
            $mail->isHTML(true);
            $mail->Subject = "Pesan Baru dari: $nama";
            
            $mail->Body    = "
                <h3>Pesan Baru Masuk</h3>
                <p><strong>Dari:</strong> $nama ($email_pengirim)</p>
                <p><strong>No. Telepon:</strong> $telepon</p>
                <br>
                <p><strong>Isi Pesan:</strong></p>
                <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #6A4E3B; font-family: sans-serif;'>
                    " . nl2br($isi_pesan) . "
                </div>
            ";
            
            $mail->AltBody = "Dari: $nama ($email_pengirim)\nTelepon: $telepon\nPesan: $isi_pesan";

            $mail->send();
            $status_pesan = "Pesan berhasil dikirim! Kami akan segera menghubungi Anda.";
            $tipe_pesan = "success";

        } catch (Exception $e) {
            
            
            $status_pesan = "Gagal mengirim pesan. Error: " . $mail->ErrorInfo;
            $tipe_pesan = "error";
        }
    }
}
?>