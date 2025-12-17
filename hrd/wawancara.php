<?php
/**
 * Halaman Manajemen Wawancara untuk HRD.
 * Fitur: CRUD Jadwal Wawancara + Notifikasi Email Otomatis & Reschedule
 */

// --- 1. Konfigurasi PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load library PHPMailer
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

$page = 'wawancara';
include '../templates/hrd_header.php'; // Sudah memanggil init.php (session & koneksi)

$message = "";

// --- FUNGSI KIRIM EMAIL (UNDANGAN BARU - VERSI PROFESIONAL) ---
function kirimEmailNotifikasi($data_pelamar, $jadwal_info) {
    // KONFIGURASI SMTP
    $mail_host = 'smtp.gmail.com';
    $mail_username = 'hrdsyjuracoffe@gmail.com'; 
    $mail_password = 'vtzl yffh yimv pcpa';       
    $mail_port = 465; // Menggunakan Port 465 (SMTPS) agar lebih stabil di Gmail

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port       = $mail_port;

        // Recipients
        $mail->setFrom($mail_username, 'Recruitment Team - Syjura Coffee');
        $mail->addAddress($data_pelamar['email'], $data_pelamar['nama_lengkap']);
        $mail->addReplyTo($mail_username, 'HRD Syjura Coffee');

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Undangan Wawancara Kerja - Posisi " . $data_pelamar['posisi_dilamar'];

        // Format Tanggal (Contoh: 18 December 2025)
        $tgl_formatted = date('d F Y', strtotime($jadwal_info['tanggal']));

        // Styling Email
        $header_style = "background-color: #6F4E37; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;";
        $body_style   = "font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px;";
        
        $bodyContent = "
        <div style=\"$body_style\">
            <div style=\"$header_style\">
                <h2 style='color: #ffffff; margin: 0; letter-spacing: 1px;'>SYJURA COFFEE</h2>
            </div>
            
            <div style='padding: 30px;'>
                <p>Yth. Sdr/i <strong>{$data_pelamar['nama_lengkap']}</strong>,</p>
                
                <p>Terima kasih atas ketertarikan Anda untuk bergabung dengan Syjura Coffee. Setelah meninjau lamaran dan kualifikasi Anda untuk posisi <strong>{$data_pelamar['posisi_dilamar']}</strong>, dengan senang hati kami mengundang Anda untuk mengikuti tahapan seleksi selanjutnya, yaitu Sesi Wawancara.</p>
                
                <p>Jadwal wawancara Anda telah ditentukan sebagai berikut:</p>
                
                <div style='background-color: #f8f9fa; border-left: 4px solid #6F4E37; padding: 15px; margin: 20px 0;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 5px 0; width: 100px; font-weight: bold;'>Hari/Tanggal</td>
                            <td>: $tgl_formatted</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold;'>Waktu</td>
                            <td>: {$jadwal_info['jam']} WIB - Selesai</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold;'>Lokasi</td>
                            <td>: {$jadwal_info['lokasi']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold; vertical-align: top;'>Catatan</td>
                            <td>: " . nl2br($jadwal_info['catatan']) . "</td>
                        </tr>
                    </table>
                </div>
                
                <p><strong>Hal-hal yang perlu diperhatikan:</strong></p>
                <ul style='color: #555;'>
                    <li>Harap hadir 15 menit sebelum jadwal yang ditentukan.</li>
                    <li>Membawa hardcopy CV, Portofolio (jika ada), dan alat tulis.</li>
                    <li>Berpakaian rapi dan formal (Business Casual/Formal).</li>
                </ul>

                <p>Mohon konfirmasikan kehadiran Anda melalui balasan email ini. Jika Anda memiliki kendala mengenai jadwal tersebut, harap segera menghubungi kami.</p>
                
                <p>Hormat kami,<br>
                <strong>HR Department - Syjura Coffee</strong></p>
            </div>

            <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #888; border-radius: 0 0 8px 8px;'>
                <p style='margin: 0;'>Syjura Coffee &copy; " . date('Y') . "</p>
                <p style='margin: 5px 0;'>Email ini dibuat secara otomatis oleh sistem SIPEKA. Mohon cek kembali detail jadwal Anda.</p>
            </div>
        </div>";

        $mail->Body = $bodyContent;
        $mail->AltBody = "Yth. {$data_pelamar['nama_lengkap']}, Anda diundang wawancara pada $tgl_formatted jam {$jadwal_info['jam']} di {$jadwal_info['lokasi']}. Mohon cek email versi HTML untuk detail lengkap.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// --- FUNGSI KIRIM EMAIL (RESCHEDULE - VERSI PROFESIONAL) ---
function kirimEmailReschedule($data_pelamar, $jadwal_info) {
    // KONFIGURASI SMTP
    $mail_host = 'smtp.gmail.com';
    $mail_username = 'hrdsyjuracoffe@gmail.com'; 
    $mail_password = 'vtzl yffh yimv pcpa';       
    $mail_port = 465;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $mail_port;

        $mail->setFrom($mail_username, 'Recruitment Team - Syjura Coffee');
        $mail->addAddress($data_pelamar['email'], $data_pelamar['nama_lengkap']);
        $mail->addReplyTo($mail_username, 'HRD Syjura Coffee');

        $mail->isHTML(true);
        $mail->Subject = "PENTING: Perubahan Jadwal Wawancara - Posisi " . $data_pelamar['posisi_dilamar'];

        $tgl_formatted = date('d F Y', strtotime($jadwal_info['tanggal']));

        // Styling
        $header_style = "background-color: #A52A2A; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;"; 
        $body_style   = "font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px;";

        $bodyContent = "
        <div style=\"$body_style\">
            <div style=\"$header_style\">
                <h2 style='color: #ffffff; margin: 0; letter-spacing: 1px;'>PEMBERITAHUAN JADWAL</h2>
            </div>
            
            <div style='padding: 30px;'>
                <p>Yth. Sdr/i <strong>{$data_pelamar['nama_lengkap']}</strong>,</p>
                
                <p>Sehubungan dengan adanya penyesuaian agenda internal manajemen Syjura Coffee, kami bermaksud menginformasikan adanya <strong>perubahan jadwal (reschedule)</strong> untuk sesi wawancara Anda yang sebelumnya telah kami kirimkan.</p>
                
                <p>Mohon abaikan jadwal sebelumnya. Berikut adalah detail <strong>Jadwal Terbaru</strong> Anda:</p>
                
                <div style='background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; color: #856404;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 5px 0; width: 100px; font-weight: bold;'>Hari/Tanggal</td>
                            <td>: $tgl_formatted</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold;'>Waktu Baru</td>
                            <td>: {$jadwal_info['jam']} WIB - Selesai</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold;'>Lokasi</td>
                            <td>: {$jadwal_info['lokasi']}</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; font-weight: bold; vertical-align: top;'>Catatan HRD</td>
                            <td>: " . nl2br($jadwal_info['catatan']) . "</td>
                        </tr>
                    </table>
                </div>
                
                <p>Kami memohon maaf atas ketidaknyamanan yang ditimbulkan akibat perubahan ini. Besar harapan kami Anda tetap dapat menghadiri sesi wawancara sesuai jadwal terbaru tersebut.</p>

                <p>Mohon konfirmasikan kesediaan Anda untuk hadir pada jadwal baru ini dengan membalas email ini.</p>
                
                <p>Hormat kami,<br>
                <strong>HR Department - Syjura Coffee</strong></p>
            </div>

            <div style='background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #888; border-radius: 0 0 8px 8px;'>
                <p style='margin: 0;'>Syjura Coffee &copy; " . date('Y') . "</p>
            </div>
        </div>";

        $mail->Body = $bodyContent;
        $mail->AltBody = "PENTING: Terdapat perubahan jadwal wawancara Anda menjadi tanggal $tgl_formatted jam {$jadwal_info['jam']}. Mohon cek email Anda untuk detail lengkap.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// 3. --- LOGIKA CRUD ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // AKSI: CREATE (Tambah Wawancara)
    if ($_POST['action'] == 'create') {
        $id_lamaran = $_POST['id_lamaran'];
        $lokasi = $_POST['lokasi'];
        $catatan = $_POST['catatan'];
        $status = $_POST['status_wawancara'];
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];
        $status_lamaran_baru = "Wawancara";

        if (empty($id_lamaran) || empty($lokasi) || empty($status) || empty($tanggal) || empty($jam)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Input tidak valid. Data wajib diisi lengkap.'];
        } else {
            $jadwal = "{$tanggal} {$jam}";
            $koneksi->begin_transaction();
            
            try {
                // [FIX DUPLIKAT] 1. Cek apakah pelamar ini SUDAH punya jadwal?
                $stmt_cek = $koneksi->prepare("SELECT id_wawancara FROM wawancara WHERE id_lamaran = ?");
                $stmt_cek->bind_param("i", $id_lamaran);
                $stmt_cek->execute();
                if ($stmt_cek->get_result()->num_rows > 0) {
                    throw new Exception("Pelamar ini SUDAH memiliki jadwal. Gunakan tombol edit untuk mengubahnya.");
                }
                $stmt_cek->close();

                // 2. Ambil Data Pelamar (FIX: JOIN ke profil_pelamar untuk nama_lengkap)
                $stmt_get_user = $koneksi->prepare("
                    SELECT u.email, p.nama_lengkap, l.posisi_dilamar 
                    FROM lamaran l 
                    JOIN user u ON l.id_pelamar = u.id_user 
                    JOIN profil_pelamar p ON u.id_user = p.id_user
                    WHERE l.id_lamaran = ?
                ");
                $stmt_get_user->bind_param("i", $id_lamaran);
                $stmt_get_user->execute();
                $res_user = $stmt_get_user->get_result();
                
                if ($res_user->num_rows == 0) throw new Exception("Data pelamar tidak ditemukan.");
                $data_pelamar = $res_user->fetch_assoc();
                $stmt_get_user->close();

                // 3. Insert Wawancara
                $stmt_insert = $koneksi->prepare("INSERT INTO wawancara (id_lamaran, jadwal, lokasi, status_wawancara, catatan) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("issss", $id_lamaran, $jadwal, $lokasi, $status, $catatan);
                if (!$stmt_insert->execute()) throw new Exception($stmt_insert->error);
                $stmt_insert->close();

                // 4. Update Status Lamaran
                $stmt_update = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
                $stmt_update->bind_param("si", $status_lamaran_baru, $id_lamaran);
                if (!$stmt_update->execute()) throw new Exception($stmt_update->error);
                $stmt_update->close();

                $koneksi->commit();

                // 5. Kirim Email Undangan
                $jadwal_info = [
                    'tanggal' => date('d-m-Y', strtotime($tanggal)),
                    'jam' => $jam,
                    'lokasi' => $lokasi,
                    'catatan' => $catatan
                ];

                if (kirimEmailNotifikasi($data_pelamar, $jadwal_info)) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal berhasil dibuat dan notifikasi email terkirim.'];
                } else {
                    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Jadwal tersimpan, tetapi GAGAL mengirim email notifikasi.'];
                }

            } catch (Exception $e) {
                $koneksi->rollback();
                $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal: " . $e->getMessage()];
            }
        }
    }

    // AKSI: UPDATE (Perbarui & Reschedule)
    if ($_POST['action'] == 'update') {
        $id_wawancara = $_POST['id_wawancara'];
        $lokasi = $_POST['lokasi'];
        $catatan = $_POST['catatan'];
        $status = $_POST['status_wawancara'];
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];

        if (empty($id_wawancara) || empty($lokasi) || empty($status) || empty($tanggal) || empty($jam)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Input tidak valid. Data wajib diisi.'];
        } else {
            $new_jadwal = "{$tanggal} {$jam}";

            // 1. Ambil Data Lama untuk Cek Perubahan & Data Email (FIX: JOIN ke profil_pelamar)
            $stmt_old = $koneksi->prepare("
                SELECT w.jadwal, w.lokasi, w.catatan, u.email, p.nama_lengkap, l.posisi_dilamar 
                FROM wawancara w 
                JOIN lamaran l ON w.id_lamaran = l.id_lamaran 
                JOIN user u ON l.id_pelamar = u.id_user 
                JOIN profil_pelamar p ON u.id_user = p.id_user
                WHERE w.id_wawancara = ?
            ");
            $stmt_old->bind_param("i", $id_wawancara);
            $stmt_old->execute();
            $res_old = $stmt_old->get_result();
            $old_data = $res_old->fetch_assoc();
            $stmt_old->close();

            if (!$old_data) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Data wawancara tidak ditemukan.'];
            } else {
                // 2. Bandingkan Data (Apakah Reschedule?)
                $old_time = strtotime($old_data['jadwal']);
                $new_time = strtotime($new_jadwal);
                
                $is_rescheduled = ($old_time !== $new_time) || ($old_data['lokasi'] !== $lokasi);

                // 3. Update Database
                $stmt = $koneksi->prepare("UPDATE wawancara SET jadwal = ?, lokasi = ?, status_wawancara = ?, catatan = ? WHERE id_wawancara = ?");
                $stmt->bind_param("ssssi", $new_jadwal, $lokasi, $status, $catatan, $id_wawancara);

                if ($stmt->execute()) {
                    $pesan_sukses = 'Jadwal wawancara berhasil diperbarui.';

                    // 4. Jika Reschedule, Kirim Email
                    if ($is_rescheduled) {
                        $jadwal_info = [
                            'tanggal' => date('d-m-Y', strtotime($tanggal)),
                            'jam' => $jam,
                            'lokasi' => $lokasi,
                            'catatan' => $catatan
                        ];

                        if (kirimEmailReschedule($old_data, $jadwal_info)) {
                            $pesan_sukses .= ' Notifikasi Reschedule telah dikirim ke pelamar.';
                        } else {
                            $pesan_sukses .= ' Namun GAGAL mengirim email notifikasi reschedule.';
                        }
                    }

                    $_SESSION['message'] = ['type' => 'success', 'text' => $pesan_sukses];
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui jadwal.'];
                }
                $stmt->close();
            }
        }
    }

    // AKSI: DELETE (Hapus Wawancara)
    if ($_POST['action'] == 'delete') {
        $id_wawancara = $_POST['id_wawancara'];
        $koneksi->begin_transaction();
        try {
            $stmt_get_id = $koneksi->prepare("SELECT id_lamaran FROM wawancara WHERE id_wawancara = ?");
            $stmt_get_id->bind_param("i", $id_wawancara);
            $stmt_get_id->execute();
            $result_id = $stmt_get_id->get_result();
            if ($result_id->num_rows == 0) throw new Exception("Jadwal tidak ditemukan.");
            
            $id_lamaran = $result_id->fetch_assoc()['id_lamaran'];
            $stmt_get_id->close();

            $stmt_del = $koneksi->prepare("DELETE FROM wawancara WHERE id_wawancara = ?");
            $stmt_del->bind_param("i", $id_wawancara);
            $stmt_del->execute();
            $stmt_del->close();

            $stmt_update = $koneksi->prepare("UPDATE lamaran SET status_lamaran = 'Diproses' WHERE id_lamaran = ?");
            $stmt_update->bind_param("i", $id_lamaran);
            $stmt_update->execute();
            $stmt_update->close();

            $koneksi->commit();
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal dihapus, status kembali ke Diproses.'];
        } catch (Exception $e) {
            $koneksi->rollback();
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal menghapus: " . $e->getMessage()];
        }
    }
}

// 4. --- LOGIKA READ (TAMPIL DATA) ---
// FIX: JOIN ke profil_pelamar (p) untuk mengambil nama_lengkap
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT w.id_wawancara, w.id_lamaran, p.nama_lengkap, l.posisi_dilamar,
            DATE_FORMAT(w.jadwal, '%d - %m - %Y') AS tanggal_formatted, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_formatted, 
            w.status_wawancara, w.lokasi, w.catatan,
            DATE_FORMAT(w.jadwal, '%Y-%m-%d') AS tanggal_raw,
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_raw
          FROM wawancara w
          JOIN lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN user u ON l.id_pelamar = u.id_user
          JOIN profil_pelamar p ON u.id_user = p.id_user";

if (!empty($search)) {
    $search_param = "%{$search}%";
    // Search juga menggunakan p.nama_lengkap
    $full_query = $query . " WHERE p.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? OR w.status_wawancara LIKE ? ORDER BY w.jadwal ASC";
    $stmt_read = $koneksi->prepare($full_query);
    $stmt_read->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt_read->execute();
    $result = $stmt_read->get_result();
    $stmt_read->close();
} else {
    $full_query = $query . " ORDER BY w.jadwal ASC";
    $result = $koneksi->query($full_query);
}

// 5. --- LOGIKA DROPDOWN PELAMAR ---
// FIX: JOIN ke profil_pelamar (p) untuk nama_lengkap di dropdown
// UPDATE: Filter pelamar yang SUDAH punya jadwal (agar tidak dobel jadwal)
$query_pelamar = "SELECT l.id_lamaran, p.nama_lengkap, l.posisi_dilamar 
                  FROM lamaran l 
                  JOIN user u ON l.id_pelamar = u.id_user
                  JOIN profil_pelamar p ON u.id_user = p.id_user
                  LEFT JOIN wawancara w ON l.id_lamaran = w.id_lamaran
                  WHERE l.status_lamaran = 'Wawancara' AND w.id_wawancara IS NULL
                  ORDER BY l.id_lamaran DESC"; 
$result_pelamar = $koneksi->query($query_pelamar);
$opsi_pelamar = [];
if ($result_pelamar) {
    while ($row_p = $result_pelamar->fetch_assoc()) {
        $opsi_pelamar[] = $row_p;
    }
}
?>

<div class="page-title">
    <h1>Manajemen Jadwal Wawancara</h1>
    <p>Kelola jadwal wawancara untuk pelamar yang lolos seleksi.</p>
</div>

<div class="page-actions">
    <button class="btn-primary" id="btnTambahWawancara">+ Jadwal wawancara baru</button>
    
    <a href="cetak_wawancara.php?search=<?php echo urlencode($search); ?>" target="_blank" class="btn-primary" style="background-color: #d35400; text-decoration: none; margin-left: 10px; display:inline-block; line-height: normal;">
        <i class="fas fa-print"></i> Cetak Data
    </a>
    <div class="search-container">
        <form action="wawancara.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar, posisi, atau status..."
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

<?php echo $message; ?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Pelamar</th>
                <th>Posisi</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($row['posisi_dilamar']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_formatted']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_formatted']); ?> WIB</td>
                        <td><?php echo htmlspecialchars($row['status_wawancara']); ?></td>
                        <td class="action-buttons">
                            <button class="btn-edit"
                                data-id_wawancara="<?php echo $row['id_wawancara']; ?>"
                                data-id_lamaran="<?php echo $row['id_lamaran']; ?>"
                                data-nama_pelamar="<?php echo htmlspecialchars($row['nama_lengkap']); ?>"
                                data-lokasi="<?php echo htmlspecialchars($row['lokasi']); ?>"
                                data-catatan="<?php echo htmlspecialchars($row['catatan']); ?>"
                                data-status="<?php echo $row['status_wawancara']; ?>"
                                data-tanggal="<?php echo $row['tanggal_raw']; ?>"
                                data-jam="<?php echo $row['jam_raw']; ?>">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form action="wawancara.php" method="POST" onsubmit="return confirm('Hapus jadwal ini?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_wawancara" value="<?php echo $row['id_wawancara']; ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada jadwal wawancara.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalWawancara" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Jadwal Wawancara Baru</h2>
            <span class="modal-close">&times;</span>
        </div>
        <form id="formWawancara" action="wawancara.php" method="POST">
            <div class="modal-body">
                <div class="modal-body-left">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id_wawancara" id="formIdWawancara">
                    <div class="form-group">
                        <label>Pelamar (ID Lamaran)</label>
                        <select id="formIdLamaran" name="id_lamaran" required>
                            <option value="">-- Pilih Pelamar --</option>
                            <?php foreach ($opsi_pelamar as $pelamar): ?>
                                <option value="<?php echo $pelamar['id_lamaran']; ?>">
                                    <?php echo htmlspecialchars($pelamar['nama_lengkap'] . " - " . $pelamar['posisi_dilamar']); ?> (ID: <?php echo $pelamar['id_lamaran']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="formNamaPelamarEdit" readonly style="display: none; background: #eee;">
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" id="formLokasi" name="lokasi" placeholder="Online Zoom / Kantor" required>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea id="formCatatan" name="catatan" placeholder="Detail tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-body-right">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="formStatus" name="status_wawancara" required>
                            <option value="Terjadwal">Terjadwal</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" id="formTanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Jam</label>
                        <input type="time" id="formJam" name="jam" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-batal">Batal</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // --- KLIK TOMBOL TAMBAH (CREATE) ---
        $("#btnTambahWawancara").click(function () {
            $("#formWawancara")[0].reset(); // Reset form
            $("#modalTitle").text("Jadwal Wawancara Baru");
            $("#formAction").val("create");
            $("#formIdWawancara").val("");

            // Tampilkan Dropdown & Jadikan Wajib Diisi
            $("#formIdLamaran").show().prop("disabled", false).prop("required", true); 
            
            // Sembunyikan Input Teks Nama (Hanya untuk Edit)
            $("#formNamaPelamarEdit").hide();
            
            $("#modalWawancara").css("display", "block");
        });

        // --- KLIK TOMBOL EDIT (UPDATE) ---
        $(".btn-edit").click(function () {
            $("#modalTitle").text("Edit Jadwal Wawancara");
            $("#formAction").val("update");
            $("#formIdWawancara").val($(this).data("id_wawancara"));

            // Sembunyikan Dropdown & HAPUS WAJIB DIISI
            // Ini PENTING: Menghapus 'required' agar browser mengizinkan submit meski dropdown hidden/kosong
            $("#formIdLamaran").hide().prop("required", false); 
            
            // Tampilkan Input Teks Nama (Readonly)
            $("#formNamaPelamarEdit").show().val($(this).data("nama_pelamar"));
            
            // Isi data lainnya
            $("#formLokasi").val($(this).data("lokasi"));
            $("#formCatatan").val($(this).data("catatan"));
            $("#formStatus").val($(this).data("status"));
            $("#formTanggal").val($(this).data("tanggal"));
            $("#formJam").val($(this).data("jam"));
            
            $("#modalWawancara").css("display", "block");
        });

        // --- KLIK BATAL / CLOSE ---
        $(".modal-close, .btn-batal").click(function () {
            $("#modalWawancara").css("display", "none");
        });

        // Klik di luar modal untuk menutup
        $(window).click(function (event) {
            if (event.target == $("#modalWawancara")[0]) {
                $("#modalWawancara").css("display", "none");
            }
        });
    });
</script>

<?php include '../templates/hrd_footer.php'; $koneksi->close(); ?>