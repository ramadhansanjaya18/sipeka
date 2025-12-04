<?php
/**
 * Halaman detail pelamar untuk HRD.
 *
 * Menampilkan informasi lengkap seorang pelamar berdasarkan lamaran yang dipilih.
 * HRD dapat mengubah status lamaran (Diproses, Wawancara, Diterima, Ditolak)
 * dan melihat semua dokumen yang diunggah oleh pelamar.
 */

// Aktifkan output buffering agar header() tidak error
ob_start();

// 1. Panggil Header (sudah memanggil init.php -> session & koneksi)
include '../templates/hrd_header.php';

// Inisialisasi variabel
$lamaran = null;
$error_message = "";
$message = ""; 
$upload_dir_foto = '../uploads/foto_profil/';
$upload_dir_docs = '../uploads/dokumen/';
$placeholder_foto = '../assets/img/placeholder-profile.png';

// --- LOGIKA UPDATE STATUS ---
// Dijalankan sebelum output HTML dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id_lamaran_update = $_POST['id_lamaran'];
    $status_baru = $_POST['status'];

    $status_valid = ['Diproses', 'Wawancara', 'Diterima', 'Ditolak'];
    if (in_array($status_baru, $status_valid)) {
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status_baru, $id_lamaran_update);
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => "Status lamaran berhasil diperbarui menjadi '" . htmlspecialchars($status_baru, ENT_QUOTES, 'UTF-8') . "'."];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui status.'];
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Status tidak valid.'];
    }

    // Redirect agar tidak terjadi resubmit form
    header("Location: detail_pelamar.php?id_lamaran=" . $id_lamaran_update);
    exit();
}



// --- LOGIKA READ ---
// Ambil data lamaran dari database
if (isset($_GET['id_lamaran']) && !empty($_GET['id_lamaran'])) {
    $id_lamaran = (int) $_GET['id_lamaran'];

    // [PERBAIKAN] Baris 'u.ijasah,' telah dihapus dari query di bawah ini
    $query = "SELECT 
                l.id_lamaran, l.id_pelamar, l.id_lowongan, l.status_lamaran,
                u.dokumen_cv, u.surat_lamaran, u.sertifikat_pendukung, 
                u.nama_lengkap, u.email, u.no_telepon, u.alamat, u.tempat_tanggal_lahir,
                u.riwayat_pendidikan, u.pengalaman_kerja, u.keahlian, u.ringkasan_pribadi, u.foto_profil,
                lw.posisi_lowongan
              FROM lamaran l
              JOIN user u ON l.id_pelamar = u.id_user
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
    $error_message = "ID Lamaran tidak valid atau tidak disediakan.";
}

// Tentukan path foto profil
$foto_profil_path = $placeholder_foto;
if ($lamaran && !empty($lamaran['foto_profil']) && file_exists($upload_dir_foto . $lamaran['foto_profil'])) {
    $foto_profil_path = $upload_dir_foto . $lamaran['foto_profil'];
}
?>

<div class="page-title">
    <h1>Detail Pelamaran</h1>
    <?php if ($lamaran): ?>
        <p>Lamaran untuk posisi
            <strong><?php echo htmlspecialchars($lamaran['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></strong>
        </p>
    <?php endif; ?>
</div>



<div class="detail-container">
    <?php if (!empty($error_message)): ?>
        <div class="message animated error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php elseif ($lamaran): ?>
        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($foto_profil_path, ENT_QUOTES, 'UTF-8'); ?>?t=<?php echo time(); ?>"
                alt="Foto Profil" class="profile-pic">

            <div class="profile-info">
                <h2><?php echo htmlspecialchars($lamaran['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($lamaran['email'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <p><i class="fas fa-phone"></i>
                    <?php echo htmlspecialchars($lamaran['no_telepon'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div class="profile-actions">
                <form
                    action="detail_pelamar.php?id_lamaran=<?php echo htmlspecialchars($lamaran['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>"
                    method="POST" class="status-update-form">
                    <input type="hidden" name="id_lamaran"
                        value="<?php echo htmlspecialchars($lamaran['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>">
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
                    <a href="wawancara.php?id_lamaran=<?php echo htmlspecialchars($lamaran['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>"
                        class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i> Jadwalkan Wawancara
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="additional-info">
            <h3>Informasi Pelamar</h3>
            <dl class="info-grid">
                <dt>Alamat</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['alamat'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8')); ?></dd>

                <dt>Tempat/Tgl Lahir</dt>
                <dd><?php echo htmlspecialchars($lamaran['tempat_tanggal_lahir'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8'); ?>
                </dd>

                <dt>Riwayat Pendidikan</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['riwayat_pendidikan'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8')); ?>
                </dd>

                <dt>Pengalaman Kerja</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['pengalaman_kerja'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8')); ?>
                </dd>

                <dt>Keahlian</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['keahlian'] ?? 'Belum diisi', ENT_QUOTES, 'UTF-8')); ?></dd>
            </dl>

            <h3 style="margin-top: 2rem;">Dokumen Lamaran</h3>
            <dl class="info-grid">
                <?php
                $dokumen_list = [
                    'Dokumen CV' => 'dokumen_cv',
                    'Surat Lamaran' => 'surat_lamaran',
                    'Sertifikat Pendukung' => 'sertifikat_pendukung',
                    'Ijasah' => 'ijasah'
                ];
                foreach ($dokumen_list as $label => $field):
                    echo "<dt>$label</dt><dd>";
                    if (!empty($lamaran[$field])) {
                        echo "<a href='" . htmlspecialchars($upload_dir_docs . $lamaran[$field], ENT_QUOTES, 'UTF-8') . "' target='_blank' class='btn-download'>
                                <i class='fas fa-file-pdf'></i> Lihat/Download $label
                              </a>";
                    } else {
                        // Kode ini akan otomatis berjalan untuk 'Ijasah' karena $lamaran['ijasah'] tidak ada
                        echo "<span>Tidak ada $label.</span>";
                    }
                    echo "</dd>";
                endforeach;
                ?>
            </dl>
        </div>
    <?php endif; ?>

    <br>
    <a href="pelamar.php" class="btn-batal" style="text-decoration: none;"><i class="fas fa-arrow-left"></i> Kembali ke
        Daftar Pelamar</a>
</div>

<?php
include '../templates/hrd_footer.php';
$koneksi->close();
ob_end_flush();
?>