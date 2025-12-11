<?php
/**
 * Halaman Detail Lowongan.
 */

// 1. Memanggil header (init session & koneksi)
include_once 'templates/header.php';

$lowongan = null;
$error_message = "";

// 2. Validasi dan ambil ID lowongan dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error_message = "ID lowongan tidak valid atau tidak ditemukan.";
} else {
    $id_lowongan = (int) $_GET['id'];

    // 3. Query untuk mengambil detail lowongan
    $query = "SELECT 
                l.id_lowongan, l.judul, l.posisi_lowongan, l.deskripsi, l.persyaratan, 
                DATE_FORMAT(l.tanggal_buka, '%d %M %Y') AS tgl_buka_formatted, 
                DATE_FORMAT(l.tanggal_tutup, '%d %M %Y') AS tgl_tutup_formatted,
                (CASE 
                    WHEN CURDATE() BETWEEN l.tanggal_buka AND l.tanggal_tutup THEN 'Aktif'
                    ELSE 'Tutup'
                END) AS status_lowongan_realtime,
                u.username AS nama_hrd
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

// 4. --- LOGIKA VALIDASI KELENGKAPAN PROFIL (SEMUA KOLOM) ---
$user_has_applied = false;
$profile_is_complete = false;
$missing_fields = []; 
$status_lowongan_aktif = false;

// Cek hanya jika user login sebagai pelamar
if (!$error_message && isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar') {
    $id_pelamar = $_SESSION['id_user'];
    $status_lowongan_aktif = ($lowongan['status_lowongan_realtime'] == 'Aktif');

    // A. Cek lamaran
    $stmt_check_lamaran = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_pelamar = ? AND id_lowongan = ?");
    $stmt_check_lamaran->bind_param("ii", $id_pelamar, $id_lowongan);
    $stmt_check_lamaran->execute();
    if ($stmt_check_lamaran->get_result()->num_rows > 0) {
        $user_has_applied = true;
    }
    $stmt_check_lamaran->close();

    // B. Ambil data profil pelamar
    $stmt_profile = $koneksi->prepare("
        SELECT * FROM profil_pelamar WHERE id_user = ?
    ");
    $stmt_profile->bind_param("i", $id_pelamar);
    $stmt_profile->execute();
    $pelamar = $stmt_profile->get_result()->fetch_assoc();
    $stmt_profile->close();

    // C. Definisi Data Wajib (SEMUA KOLOM)
    $required_fields = [
        'nama_lengkap'         => 'Nama Lengkap',
        'no_telepon'           => 'No. Telepon',
        'alamat'               => 'Alamat',
        'tempat_tanggal_lahir' => 'Tempat, Tanggal Lahir',
        'riwayat_pendidikan'   => 'Riwayat Pendidikan',
        'pengalaman_kerja'     => 'Pengalaman Kerja',
        'keahlian'             => 'Keahlian',
        'ringkasan_pribadi'    => 'Ringkasan Pribadi',
        'foto_profil'          => 'Foto Profil',
        'dokumen_cv'           => 'CV (Curriculum Vitae)',
        'surat_lamaran'        => 'Surat Lamaran',
        'sertifikat_pendukung' => 'Sertifikat Pendukung',
        'ijasah'               => 'Ijazah'
    ];

    // D. Loop cek kelengkapan
    if ($pelamar) {
        foreach ($required_fields as $db_column => $label) {
            if (empty($pelamar[$db_column]) || trim($pelamar[$db_column]) === '') {
                $missing_fields[] = $label;
            }
        }
    } else {
        $missing_fields[] = "Profil Belum Dibuat Sama Sekali";
    }

    if (empty($missing_fields)) {
        $profile_is_complete = true;
    }
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
                            <span class="status-<?php echo strtolower(htmlspecialchars($lowongan['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8')); ?>">
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

                                <?php elseif (!$profile_is_complete): ?>
                                    <button type="button" class="btn-apply" style="background-color: #dc3545;" onclick="showIncompleteAlert()">
                                        Lamar Sekarang
                                    </button>
                                    <p class="info-text" style="font-size: 0.85rem; margin-top: 0.5rem; color: #dc3545;">
                                        *Data profil Anda belum lengkap.
                                    </p>

                                <?php else: ?>
                                    <a href="apply.php?id=<?php echo htmlspecialchars($id_lowongan, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="btn-apply" onclick="return confirm('Apakah Anda yakin ingin melamar posisi ini?');">
                                        Lamar Sekarang
                                    </a>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="info-text">Anda login sebagai HRD.</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php?pesan=Login+untuk+melamar" class="btn-apply">Login untuk Melamar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
function showIncompleteAlert() {
    const missingItems = <?php echo json_encode($missing_fields); ?>;
    
    let listText = "";
    if (missingItems && missingItems.length > 0) {
        missingItems.forEach(function(item) {
            listText += "- " + item + "\n";
        });
    }

    const message = "Maaf, Anda belum dapat melamar.\n\n" +
                    "Mohon lengkapi data berikut di halaman Profil:\n" + 
                    listText + 
                    "\nKlik OK untuk menuju halaman Profil.";

    if (confirm(message)) {
        window.location.href = "profil.php";
    }
}
</script>

<?php
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
include_once 'templates/footer.php';
?>