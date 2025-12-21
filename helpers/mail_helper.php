<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../config/mail_config.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

function getMailerInstance() {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true
        )
    );
    
    $fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Recruitment Syjura Coffee';
    $mail->setFrom(SMTP_USER, $fromName);
    $mail->addReplyTo(SMTP_USER, 'HRD Syjura Coffee');
    $mail->isHTML(true);
    
    return $mail;
}

function wrapEmailTemplate($title, $content, $accentColor = '#6F4E37') {
    $year = date('Y');
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
        <style>
            body { 
                font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
                background-color: #f8f9fa; 
                margin: 0; padding: 0; 
                -webkit-font-smoothing: antialiased;
                color: #333333; 
            }
            .wrapper { width: 100%; padding: 40px 0; }
            .container { 
                max-width: 600px; 
                margin: 0 auto; 
                background-color: #ffffff; 
                border-top: 6px solid $accentColor; 
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            }
            
            /* Header Simple */
            .header { padding: 40px 40px 20px; text-align: center; border-bottom: 1px solid #eeeeee; }
            .header h1 { color: #333333; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
            .header p { color: #888888; margin: 5px 0 0; font-size: 13px; font-weight: 500; letter-spacing: 2px; text-transform: uppercase; }
            
            /* Content Area */
            .content { padding: 40px; font-size: 15px; line-height: 1.7; color: #333333; }
            .content p { margin-bottom: 1.5em; }
            .content strong { color: #000000; font-weight: 700; }
            
            /* Highlight Box */
            .info-box { 
                background-color: #fdfdfd; 
                border: 1px solid #e0e0e0; 
                border-left: 4px solid $accentColor; 
                padding: 20px; 
                margin: 25px 0; 
                border-radius: 4px; 
            }
            .info-table { width: 100%; }
            .info-table td { padding: 6px 0; vertical-align: top; color: #333333; }
            .info-table td:first-child { width: 120px; color: #666666; font-weight: 600; }
            
            /* Button */
            .btn-action {
                display: inline-block;
                background-color: $accentColor;
                color: #ffffff !important;
                text-decoration: none;
                padding: 12px 28px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 14px;
                margin-top: 10px;
                transition: background-color 0.3s;
            }
            
            /* Footer */
            .footer { 
                background-color: #f8f9fa; 
                padding: 30px; 
                text-align: center; 
                font-size: 12px; 
                color: #999999; 
                border-top: 1px solid #eeeeee;
            }
            .footer a { color: $accentColor; text-decoration: none; }
            
            @media only screen and (max-width: 600px) {
                .content { padding: 30px 20px; }
                .header { padding: 30px 20px 20px; }
                .info-table td { display: block; width: 100%; padding: 2px 0; }
                .info-table td:first-child { margin-top: 10px; color: $accentColor; }
            }
        </style>
    </head>
    <body>
        <div class='wrapper'>
            <div class='container'>
                <div class='header'>
                    <h1>SYJURA COFFEE</h1>
                    <p>$title</p>
                </div>
                
                <div class='content'>
                    $content
                </div>
                
                <div class='footer'>
                    <p><strong>Syjura Coffee Headquarter</strong></p>
                    <p>Jl. Lohbener, Pamayahan, Indramayu, Jawa Barat</p>
                    <p>Telepon: (0274) 123456 | Email: hrdsyjuracoffe@gmail.com</p>
                    <br>
                    <p>&copy; $year Syjura Coffee. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
}


if (!function_exists('kirimNotifikasiEmailPelamar')) {
    function kirimNotifikasiEmailPelamar($email_tujuan, $nama_pelamar, $status, $posisi, &$pesan_error) {
        try {
            $mail = getMailerInstance();
            $mail->addAddress($email_tujuan, $nama_pelamar);
            if ($status == 'Diterima') {
                $mail->Subject = "Selamat! Anda Diterima - Posisi $posisi";
                $accentColor = "#2E7D32";
                $body = "
                <p>Halo, <strong>$nama_pelamar</strong>.</p>
                
                <p>Terima kasih telah mengikuti seluruh rangkaian proses seleksi di Syjura Coffee. Kami sangat terkesan dengan kualifikasi dan antusiasme yang Anda tunjukkan.</p>
                
                <p>Dengan senang hati kami sampaikan bahwa Anda dinyatakan <strong>LULUS</strong> seleksi untuk posisi <strong>$posisi</strong>.</p>
                
                <div style='text-align: center; margin: 35px 0;'>
                    <span style='color: #2E7D32; font-size: 18px; font-weight: 700; border-bottom: 2px solid #2E7D32; padding-bottom: 5px;'>SELAMAT BERGABUNG!</span>
                </div>
                
                <p>Langkah selanjutnya, tim HRD kami akan segera menghubungi Anda melalui telepon/WhatsApp untuk membicarakan penawaran kerja (Offering Letter) dan jadwal onboarding.</p>
                
                <p>Kami menantikan kontribusi terbaik Anda bersama tim Syjura Coffee.</p>
                
                <br>
                <p>Salam hangat,<br><strong>Tim Rekrutmen</strong></p>";
                
                $mail->Body = wrapEmailTemplate("OFFERING LETTER", $body, $accentColor);

            } elseif ($status == 'Ditolak') {
                $mail->Subject = "Update Status Lamaran - Posisi $posisi";
                $accentColor = "#424242"; // Abu-abu Gelap (Netral & Profesional)
                
                $body = "
                <p>Halo, <strong>$nama_pelamar</strong>.</p>
                
                <p>Terima kasih banyak atas waktu dan usaha yang Anda luangkan untuk melamar posisi <strong>$posisi</strong> di Syjura Coffee.</p>
                
                <p>Setelah meninjau kualifikasi Anda secara menyeluruh, kami harus menyampaikan bahwa saat ini kami belum dapat melanjutkan proses lamaran Anda ke tahap berikutnya. Keputusan ini didasarkan pada kebutuhan spesifik perusahaan saat ini.</p>
                
                <div class='info-box' style='background-color: #f5f5f5; border-left-color: #757575;'>
                    <p style='margin: 0; font-size: 14px; color: #555;'>Data profil Anda akan kami simpan dalam database <em>talent pool</em> kami. Jika terdapat lowongan yang sesuai dengan kualifikasi Anda di masa mendatang, kami akan menghubungi Anda kembali.</p>
                </div>
                
                <p>Kami mendoakan kesuksesan untuk perjalanan karir Anda ke depannya.</p>
                
                <br>
                <p>Hormat kami,<br><strong>Tim Rekrutmen</strong></p>";

                $mail->Body = wrapEmailTemplate("UPDATE STATUS", $body, $accentColor);
            } else {
                return true; 
            }
            $mail->send();
            return true;
        } catch (Exception $e) {
            $pesan_error = $mail->ErrorInfo;
            return false;
        }
    }
}

if (!function_exists('kirimEmailUndanganWawancara')) {
    function kirimEmailUndanganWawancara($email, $nama, $posisi, $jadwal_info) {
        try {
            $mail = getMailerInstance();
            $mail->addAddress($email, $nama);
            $mail->Subject = "Undangan Wawancara - Posisi $posisi";

            $tgl = $jadwal_info['tanggal_indo'];
            $accentColor = "#6F4E37"; 
            
            $body = "
            <p>Halo, <strong>$nama</strong>.</p>
            <p>Terima kasih atas lamaran Anda untuk posisi <strong>$posisi</strong>. Kami terkesan dengan profil Anda dan ingin mengundang Anda untuk mengikuti tahapan <strong>Wawancara Kerja</strong>.</p>
            
            <p>Berikut adalah detail jadwal wawancara Anda:</p>
            
            <div class='info-box'>
                <table class='info-table'>
                    <tr><td>Hari/Tanggal</td><td>: <strong>$tgl</strong></td></tr>
                    <tr><td>Waktu</td><td>: <strong>{$jadwal_info['jam']} WIB - Selesai</strong></td></tr>
                    <tr><td>Lokasi</td><td>: {$jadwal_info['lokasi']}</td></tr>
                    <tr><td>Catatan</td><td>: " . nl2br($jadwal_info['catatan']) . "</td></tr>
                </table>
            </div>

            <p><strong>Mohon persiapkan hal berikut:</strong></p>
            <ul style='color: #333;'>
                <li>Hadir 15 menit sebelum jadwal dimulai.</li>
                <li>Membawa salinan CV (Hardcopy).</li>
                <li>Berpakaian formal dan rapi.</li>
            </ul>
            
            <div style='text-align: center; margin-top: 35px; margin-bottom: 20px;'>
                <a href='mailto:".SMTP_USER."?subject=Konfirmasi Kehadiran - $nama' class='btn-action'>KONFIRMASI KEHADIRAN</a>
                <p style='font-size: 12px; color: #999; margin-top: 10px;'>(Klik tombol di atas untuk membalas email ini)</p>
            </div>
            
            <br>
            <p>Sampai jumpa,<br><strong>Tim HRD Syjura Coffee</strong></p>";

            $mail->Body = wrapEmailTemplate("UNDANGAN INTERVIEW", $body, $accentColor);
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('kirimEmailRescheduleWawancara')) {
    function kirimEmailRescheduleWawancara($email, $nama, $posisi, $jadwal_info) {
        try {
            $mail = getMailerInstance();
            $mail->addAddress($email, $nama);
            $mail->Subject = "UPDATE JADWAL: Wawancara Posisi $posisi";

            $tgl = $jadwal_info['tanggal_indo'];
            $accentColor = "#D84315";

            $body = "
            <p>Halo, <strong>$nama</strong>.</p>
            
            <p>Sehubungan dengan adanya penyesuaian agenda internal, kami menginformasikan adanya <strong>PERUBAHAN JADWAL</strong> untuk sesi wawancara Anda.</p>
            
            <p>Mohon abaikan undangan sebelumnya. Berikut adalah jadwal terbaru Anda:</p>
            
            <div class='info-box' style='border-left-color: $accentColor; background-color: #fff8f6;'>
                <table class='info-table'>
                    <tr><td style='color: $accentColor;'>Hari/Tanggal</td><td>: <strong>$tgl</strong></td></tr>
                    <tr><td style='color: $accentColor;'>Waktu Baru</td><td>: <strong>{$jadwal_info['jam']} WIB</strong></td></tr>
                    <tr><td>Lokasi</td><td>: {$jadwal_info['lokasi']}</td></tr>
                    <tr><td>Catatan</td><td>: " . nl2br($jadwal_info['catatan']) . "</td></tr>
                </table>
            </div>

            <p>Kami memohon maaf atas ketidaknyamanan ini. Besar harapan kami Anda tetap dapat hadir sesuai jadwal terbaru.</p>
            
            <div style='text-align: center; margin-top: 35px; margin-bottom: 20px;'>
                <a href='mailto:".SMTP_USER."?subject=Konfirmasi Jadwal Baru - $nama' class='btn-action' style='background-color: $accentColor;'>KONFIRMASI JADWAL BARU</a>
            </div>
            
            <br>
            <p>Terima kasih,<br><strong>Tim HRD Syjura Coffee</strong></p>";

            $mail->Body = wrapEmailTemplate("PERUBAHAN JADWAL", $body, $accentColor);
            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>