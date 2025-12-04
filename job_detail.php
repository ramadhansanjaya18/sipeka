<?php
/**
 * Halaman Detail Lowongan.
 *
 * Menampilkan informasi lengkap dari satu lowongan pekerjaan, termasuk deskripsi,
 * persyaratan, dan status. Halaman ini juga berisi logika untuk menampilkan
 * tombol "Lamar" yang sesuai dengan kondisi pengguna (sudah login, peran, 
 * status lamaran, dan kelengkapan profil).
 */

// 1. Memanggil header (yang juga menginisialisasi session dan koneksi database)
include_once 'templates/header.php';

$lowongan = null;
$error_message = "";

// 2. Validasi dan ambil ID lowongan dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error_message = "ID lowongan tidak valid atau tidak ditemukan.";
} else {
    $id_lowongan = (int) $_GET['id'];

    // 3. Query untuk mengambil detail lowongan beserta nama HRD yang memposting
    $query = "SELECT 
                l.id_lowongan, l.judul, l.posisi_lowongan, l.deskripsi, l.persyaratan, 
                DATE_FORMAT(l.tanggal_buka, '%d %M %Y') AS tgl_buka_formatted, 
                DATE_FORMAT(l.tanggal_tutup, '%d %M %Y') AS tgl_tutup_formatted,
                (CASE 
                    WHEN CURDATE() BETWEEN l.tanggal_buka AND l.tanggal_tutup THEN 'Aktif'
                    ELSE 'Tutup'
                END) AS status_lowongan_realtime,
                u.nama_lengkap AS nama_hrd
            FROM 
                lowongan l
            JOIN 
                user u ON l.id_hrd = u.id_user
            WHERE 
                l.id_lowongan = ?";

    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_lowongan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error_message = "Detail lowongan tidak ditemukan atau sudah tidak tersedia.";
    } else {
        $lowongan = $result->fetch_assoc();
    }
    $stmt->close();
}

// 4. --- LOGIKA UNTUK TOMBOL APPLY ---
// Inisialisasi variabel status untuk tombol "Lamar"
$user_has_applied = false;
$user_cv_exists = false;
$status_lowongan_aktif = false;

// Lakukan pengecekan hanya jika tidak ada error dan pengguna adalah pelamar
if (!$error_message && isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar') {
    $id_pelamar = $_SESSION['id_user'];
    $status_lowongan_aktif = ($lowongan['status_lowongan_realtime'] == 'Aktif');

    // Cek 1: Apakah pelamar sudah pernah melamar lowongan ini?
    $stmt_check_lamaran = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_pelamar = ? AND id_lowongan = ?");
    $stmt_check_lamaran->bind_param("ii", $id_pelamar, $id_lowongan);
    $stmt_check_lamaran->execute();
    if ($stmt_check_lamaran->get_result()->num_rows > 0) {
        $user_has_applied = true;
    }
    $stmt_check_lamaran->close();

    // Cek 2: Apakah pelamar sudah mengunggah CV di profilnya?
    $stmt_check_cv = $koneksi->prepare("SELECT dokumen_cv FROM user WHERE id_user = ?");
    $stmt_check_cv->bind_param("i", $id_pelamar);
    $stmt_check_cv->execute();
    $user_data = $stmt_check_cv->get_result()->fetch_assoc();
    if (!empty($user_data['dokumen_cv'])) {
        $user_cv_exists = true;
    }
    $stmt_check_cv->close();
}

?>

<div class="container job-detail-container">
    <?php if ($error_message): ?>
        <div class="job-detail-content">
            <p class="no-jobs"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="job.php" class="btn-back">← Kembali ke Lowongan</a>
        </div>
    <?php else: ?>
        <a href="job.php" class="btn-back">← Kembali ke Lowongan</a>
        <div class="job-detail-header">
            <h1><?php echo htmlspecialchars($lowongan['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="company-name">Posted by <?php echo htmlspecialchars($lowongan['nama_hrd'], ENT_QUOTES, 'UTF-8'); ?> on
                <?php echo htmlspecialchars($lowongan['tgl_buka_formatted'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>

        <div class="job-detail-content">
            <div class="main-content">
                <h2>Deskripsi Pekerjaan</h2>
                <p><?php echo nl2br(htmlspecialchars($lowongan['deskripsi'], ENT_QUOTES, 'UTF-8')); ?></p>

                <h2>Persyaratan</h2>
                <div><?php echo nl2br(htmlspecialchars($lowongan['persyaratan'], ENT_QUOTES, 'UTF-8')); ?></div>
            </div>

            <aside class="sidebar-content">
                <div class="summary-card">
                    <h3>Ringkasan Lowongan</h3>
                    <ul>
                        <li><strong>Posisi:</strong>
                            <?php echo htmlspecialchars($lowongan['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Perusahaan:</strong> Syjura Coffee</li>
                        <li><strong>Tanggal Tutup:</strong>
                            <?php echo htmlspecialchars($lowongan['tgl_tutup_formatted'], ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Status:</strong>
                            <span
                                class="status-<?php echo strtolower(htmlspecialchars($lowongan['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8')); ?>">
                                <?php echo htmlspecialchars($lowongan['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </li>
                    </ul>

                    <div class="apply-button-container">
                        <?php if (isset($_SESSION['id_user'])): ?>
                            <?php if ($_SESSION['role'] == 'pelamar'): ?>

                                <?php if ($user_has_applied): ?>
                                    <button class="btn-disabled" disabled>Anda Sudah Melamar</button>
                                <?php elseif (!$status_lowongan_aktif): ?>
                                    <button class="btn-disabled" disabled>Lowongan Ditutup</button>
                                <?php elseif (!$user_cv_exists): ?>
                                    <a href="profil.php" class="btn-apply" style="background-color: #dc3545;">Upload CV Dulu</a>
                                    <p class="info-text" style="font-size: 0.9rem; margin-top: 0.5rem;">Anda harus upload CV di profil
                                        sebelum melamar.</p>
                                <?php else: ?>
                                    <a href="apply.php?id=<?php echo htmlspecialchars($id_lowongan, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="btn-apply">Lamar Sekarang</a>
                                <?php endif; ?>

                            <?php else:   // Jika role adalah HRD ?>
                                <p class="info-text">Anda login sebagai HRD.</p>
                            <?php endif; ?>
                        <?php else:   // Jika belum login ?>
                            <a href="login.php?pesan=Login+untuk+melamar" class="btn-apply">Login untuk Melamar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<?php
// Tutup koneksi dan panggil footer
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
include_once 'templates/footer.php';
?>