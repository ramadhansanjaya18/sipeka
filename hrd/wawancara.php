<?php
/**
 * Halaman Manajemen Wawancara untuk HRD.
 * Fitur: CRUD Jadwal Wawancara + Notifikasi Email Otomatis (Menggunakan Kredensial Contact.php)
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

// --- FUNGSI KIRIM EMAIL ---
function kirimEmailNotifikasi($data_pelamar, $jadwal_info) {
    // KREDENSIAL DARI contact.php
    $mail_host = 'smtp.gmail.com';
    $mail_username = 'hrdsyjuracoffe@gmail.com'; // Email Admin/Sistem
    $mail_password = 'vtzl yffh yimv pcpa';       // App Password Anda
    $mail_port = 587; // Port TLS (Sesuai contact.php)

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $mail_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mail_username;
        $mail->Password   = $mail_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Sesuai contact.php
        $mail->Port       = $mail_port;

        // Pengirim & Penerima
        // Dari: HRD Syjura Coffee (Email Sistem)
        $mail->setFrom($mail_username, 'HRD SIPEKA - Syjura Coffee');
        
        // Kepada: Pelamar
        $mail->addAddress($data_pelamar['email'], $data_pelamar['nama_lengkap']);

        // Jika pelamar me-reply email ini, arahkan ke email HRD utama
        $mail->addReplyTo($mail_username, 'HRD Syjura Coffee');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Undangan Wawancara Kerja - Syjura Coffee';
        
        // Template Email HTML
        $bodyContent = "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #6a4e3b;'>Undangan Wawancara</h2>
            <p>Halo <strong>{$data_pelamar['nama_lengkap']}</strong>,</p>
            <p>Selamat! Berdasarkan hasil seleksi administrasi untuk posisi <strong>{$data_pelamar['posisi_dilamar']}</strong>, kami mengundang Anda untuk mengikuti sesi wawancara.</p>
            
            <div style='background-color: #f9f9f9; padding: 15px; border-left: 4px solid #6a4e3b; margin: 20px 0;'>
                <h3 style='margin-top: 0;'>Detail Jadwal:</h3>
                <ul style='list-style: none; padding-left: 0;'>
                    <li style='margin-bottom: 8px;'>📅 <strong>Tanggal:</strong> {$jadwal_info['tanggal']}</li>
                    <li style='margin-bottom: 8px;'>⏰ <strong>Jam:</strong> {$jadwal_info['jam']} WIB</li>
                    <li style='margin-bottom: 8px;'>📍 <strong>Lokasi:</strong> {$jadwal_info['lokasi']}</li>
                    <li style='margin-bottom: 8px;'>📝 <strong>Catatan:</strong> " . nl2br($jadwal_info['catatan']) . "</li>
                </ul>
            </div>
            
            <p>Harap konfirmasi kehadiran Anda atau hubungi kami jika ada kendala.</p>
            <hr style='border: 0; border-top: 1px solid #eee;'>
            <p style='font-size: 12px; color: #777;'>Terima kasih,<br>Tim HRD Syjura Coffee</p>
        </div>";

        $mail->Body = $bodyContent;
        $mail->AltBody = "Halo {$data_pelamar['nama_lengkap']}, Anda diundang wawancara pada tanggal {$jadwal_info['tanggal']} jam {$jadwal_info['jam']} di {$jadwal_info['lokasi']}. Catatan: {$jadwal_info['catatan']}";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Log error jika perlu: error_log($mail->ErrorInfo);
        return false;
    }
}

// 3. --- LOGIKA CRUD (CREATE, UPDATE, DELETE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // AKSI: CREATE (Tambah Wawancara & Kirim Email)
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
                // 1. Ambil Data Pelamar (Email & Nama) untuk notifikasi SEBELUM insert
                $stmt_get_user = $koneksi->prepare("
                    SELECT u.email, u.nama_lengkap, l.posisi_dilamar 
                    FROM lamaran l 
                    JOIN user u ON l.id_pelamar = u.id_user 
                    WHERE l.id_lamaran = ?
                ");
                $stmt_get_user->bind_param("i", $id_lamaran);
                $stmt_get_user->execute();
                $res_user = $stmt_get_user->get_result();
                
                if ($res_user->num_rows == 0) {
                    throw new Exception("Data pelamar tidak ditemukan.");
                }
                
                $data_pelamar = $res_user->fetch_assoc();
                $stmt_get_user->close();

                // 2. Insert ke tabel wawancara
                $stmt_insert = $koneksi->prepare("INSERT INTO wawancara (id_lamaran, jadwal, lokasi, status_wawancara, catatan) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("issss", $id_lamaran, $jadwal, $lokasi, $status, $catatan);
                if (!$stmt_insert->execute()) {
                    throw new Exception($stmt_insert->error);
                }
                $stmt_insert->close();

                // 3. Update status di tabel lamaran
                $stmt_update = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
                $stmt_update->bind_param("si", $status_lamaran_baru, $id_lamaran);
                if (!$stmt_update->execute()) {
                    throw new Exception($stmt_update->error);
                }
                $stmt_update->close();

                // 4. Commit Transaksi Database
                $koneksi->commit();

                // 5. PROSES KIRIM EMAIL
                $tanggal_indo = date('d-m-Y', strtotime($tanggal));
                $jadwal_info = [
                    'tanggal' => $tanggal_indo,
                    'jam' => $jam,
                    'lokasi' => $lokasi,
                    'catatan' => $catatan
                ];

                $email_status = kirimEmailNotifikasi($data_pelamar, $jadwal_info);

                if ($email_status) {
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal berhasil dibuat dan notifikasi email terkirim ke pelamar.'];
                } else {
                    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Jadwal tersimpan, tetapi GAGAL mengirim email notifikasi.'];
                }

            } catch (Exception $e) {
                $koneksi->rollback();
                $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal: {$error_message}"];
            }
        }
    }

    // AKSI: UPDATE (Perbarui Wawancara)
    if ($_POST['action'] == 'update') {
        $id_wawancara = $_POST['id_wawancara'];
        $lokasi = $_POST['lokasi'];
        $catatan = $_POST['catatan'];
        $status = $_POST['status_wawancara'];
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];

        if (empty($id_wawancara) || empty($lokasi) || empty($status) || empty($tanggal) || empty($jam)) {
            $message = "<div class='message animated error'>Input tidak valid. Data wajib diisi.</div>";
        } else {
            $jadwal = "{$tanggal} {$jam}";
            $stmt = $koneksi->prepare("UPDATE wawancara SET jadwal = ?, lokasi = ?, status_wawancara = ?, catatan = ? WHERE id_wawancara = ?");
            $stmt->bind_param("ssssi", $jadwal, $lokasi, $status, $catatan, $id_wawancara);

            if ($stmt->execute()) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal wawancara berhasil diperbarui.'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui jadwal.'];
            }
            $stmt->close();
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

// 4. --- LOGIKA READ ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT w.id_wawancara, w.id_lamaran, u.nama_lengkap, l.posisi_dilamar,
            DATE_FORMAT(w.jadwal, '%d - %m - %Y') AS tanggal_formatted, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_formatted, 
            w.status_wawancara, w.lokasi, w.catatan,
            DATE_FORMAT(w.jadwal, '%Y-%m-%d') AS tanggal_raw,
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_raw
          FROM wawancara w
          JOIN lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN user u ON l.id_pelamar = u.id_user";

if (!empty($search)) {
    $search_param = "%{$search}%";
    $full_query = $query . " WHERE u.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? OR w.status_wawancara LIKE ? ORDER BY w.jadwal ASC";
    $stmt_read = $koneksi->prepare($full_query);
    $stmt_read->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt_read->execute();
    $result = $stmt_read->get_result();
    $stmt_read->close();
} else {
    $full_query = $query . " ORDER BY w.jadwal ASC";
    $result = $koneksi->query($full_query);
}

// 5. --- LOGIKA DROPDOWN PELAMAR (Status 'Wawancara' & Urutan Terbaru) ---
$query_pelamar = "SELECT l.id_lamaran, u.nama_lengkap, l.posisi_dilamar 
                  FROM lamaran l 
                  JOIN user u ON l.id_pelamar = u.id_user
                  WHERE l.status_lamaran = 'Wawancara'
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
    <div class="search-container">
        <form action="wawancara.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-button">Search</button>
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
        $("#btnTambahWawancara").click(function () {
            $("#formWawancara")[0].reset();
            $("#modalTitle").text("Jadwal Wawancara Baru");
            $("#formAction").val("create");
            $("#formIdWawancara").val("");
            $("#formIdLamaran").show().prop("disabled", false);
            $("#formNamaPelamarEdit").hide();
            $("#modalWawancara").css("display", "block");
        });

        $(".btn-edit").click(function () {
            $("#modalTitle").text("Edit Jadwal Wawancara");
            $("#formAction").val("update");
            $("#formIdWawancara").val($(this).data("id_wawancara"));
            $("#formIdLamaran").hide().val($(this).data("id_lamaran"));
            $("#formNamaPelamarEdit").show().val($(this).data("nama_pelamar"));
            $("#formLokasi").val($(this).data("lokasi"));
            $("#formCatatan").val($(this).data("catatan"));
            $("#formStatus").val($(this).data("status"));
            $("#formTanggal").val($(this).data("tanggal"));
            $("#formJam").val($(this).data("jam"));
            $("#modalWawancara").css("display", "block");
        });

        $(".modal-close, .btn-batal").click(function () {
            $("#modalWawancara").css("display", "none");
        });

        $(window).click(function (event) {
            if (event.target == $("#modalWawancara")[0]) {
                $("#modalWawancara").css("display", "none");
            }
        });
    });
</script>

<?php include '../templates/hrd_footer.php'; $koneksi->close(); ?>