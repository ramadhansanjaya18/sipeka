<?php
/**
 * Halaman Detail Lowongan (View)
 */
require_once 'templates/header.php';
require_once 'logic/job_detail_logic.php'; // Memuat data $lowongan dan validasi pelamar
?>

<div class="container job-detail-container">
    <?php if ($error_message): ?>
        <div class="job-detail-content">
            <p class="no-jobs"><?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?></p>
            <a href="job.php" class="btn-back">← Kembali ke Lowongan</a>
        </div>
    <?php else: ?>
        
        <a href="job.php" class="btn-back">← Kembali ke Lowongan</a>
        
        <div class="job-detail-header">
            <h1><?= htmlspecialchars($lowongan['posisi_lowongan'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="company-name">Posted by <?= htmlspecialchars($lowongan['nama_hrd'], ENT_QUOTES, 'UTF-8') ?> on
                <?= htmlspecialchars($lowongan['tgl_buka_formatted'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <div class="job-detail-content">
            <div class="main-content">
                <h2>Deskripsi Pekerjaan</h2>
                <p><?= nl2br(htmlspecialchars($lowongan['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>

                <h2>Persyaratan</h2>
                <div><?= nl2br(htmlspecialchars($lowongan['persyaratan'], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>

            <aside class="sidebar-content">
                <div class="summary-card">
                    <h3>Ringkasan Lowongan</h3>
                    <ul>
                        <li><strong>Posisi:</strong> <?= htmlspecialchars($lowongan['posisi_lowongan'], ENT_QUOTES, 'UTF-8') ?></li>
                        <li><strong>Perusahaan:</strong> Syjura Coffee</li>
                        <li><strong>Tanggal Tutup:</strong> <?= htmlspecialchars($lowongan['tgl_tutup_formatted'], ENT_QUOTES, 'UTF-8') ?></li>
                        <li><strong>Status:</strong>
                            <span class="status-<?= strtolower(htmlspecialchars($lowongan['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8')) ?>">
                                <?= htmlspecialchars($lowongan['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8') ?>
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
                                    <button type="button" 
                                            class="btn-apply" 
                                            id="btn-incomplete-apply"
                                            style="background-color: #dc3545;" 
                                            data-missing='<?= json_encode($missing_fields, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        Lamar Sekarang
                                    </button>
                                    <p class="info-text" style="font-size: 0.85rem; margin-top: 0.5rem; color: #dc3545;">
                                        *Data profil Anda belum lengkap.
                                    </p>

                                <?php else: ?>
                                    <a href="apply.php?id=<?= htmlspecialchars($id_lowongan, ENT_QUOTES, 'UTF-8') ?>"
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

<?php
// Definisikan script tambahan sebelum footer dimuat
$extra_js = 'assets/js/job_detail.js';

require_once 'templates/footer.php';
if (isset($koneksi) && $koneksi) $koneksi->close();
?>