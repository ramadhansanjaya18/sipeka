<?php
require_once 'templates/header.php';
require_once 'config/auth_pelamar.php'; 
require_once 'logic/status_lamaran_logic.php'; 
?>

<link rel="stylesheet" href="assets/css/status.css?v=<?php echo time(); ?>">

<div class="status-container">
    <h1>Status Lamaran Saya</h1>
    <p>Lacak status semua lamaran yang telah Anda kirimkan.</p>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="message <?php echo $_SESSION['flash_message']['type'] == 'success' ? 'success' : 'error'; ?>" style="margin-bottom: 20px; text-align: center;">
            <?php echo $_SESSION['flash_message']['text']; ?>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="timeline-container">
        <?php if ($result_lamaran && $result_lamaran->num_rows > 0): ?>
            <?php while ($lamaran = $result_lamaran->fetch_assoc()):
                
                $status_clean = strtolower(str_replace(' ', '-', $lamaran['status_lamaran']));
                $status_class = 'status-' . $status_clean;
                
                
                $tgl_lamar = date('d M Y, H:i', strtotime($lamaran['tanggal_lamaran']));

                
                $jadwal_db = $lamaran['jadwal'];
                $belum_dijadwalkan = empty($jadwal_db);

                $tgl_modal = !$belum_dijadwalkan ? date('d-m-Y', strtotime($jadwal_db)) : '';
                $jam_modal = !$belum_dijadwalkan ? date('H.i', strtotime($jadwal_db)) : '';
            ?>
                <div class="timeline-item">
                    <div class="timeline-icon <?php echo $status_class; ?>"></div>
                    
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="job-title"><?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?></h3>
                            <span class="application-date">Dilamar pada: <?php echo $tgl_lamar; ?></span>
                        </div>
                        
                        <p class="company-name">Syjura Coffee - <?php echo htmlspecialchars($lamaran['judul_lowongan']); ?></p>
                        
                        <div class="timeline-footer">
                            <div class="footer-left">
                                <div class="status-badge <?php echo $status_class; ?>">
                                    Status: <strong><?php echo htmlspecialchars($lamaran['status_lamaran']); ?></strong>
                                </div>
                                <a href="job_detail.php?id=<?php echo $lamaran['id_lowongan']; ?>" class="link-detail-text">Lihat Detail Lowongan</a>
                            </div>

                            <div class="footer-right">
                                <?php if ($lamaran['status_lamaran'] == 'Wawancara'): ?>
                                    <button type="button" class="btn-jadwal" 
                                        data-id="<?php echo $lamaran['id_lamaran']; ?>"
                                        data-nama="<?php echo htmlspecialchars($nama_pelamar); ?>"
                                        data-posisi="<?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?>"
                                        data-status="<?php echo htmlspecialchars($lamaran['status_lamaran']); ?>"
                                        data-tanggal="<?php echo $tgl_modal; ?>"
                                        data-jam="<?php echo $jam_modal; ?>"
                                        data-lokasi="<?php echo htmlspecialchars($lamaran['lokasi'] ?? ''); ?>"
                                        data-catatan="<?php echo htmlspecialchars($lamaran['catatan'] ?? ''); ?>"
                                        data-belum="<?php echo $belum_dijadwalkan ? '1' : '0'; ?>">
                                    Lihat Jadwal
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-applications">
                <p>Anda belum pernah melamar pekerjaan apapun.</p>
                <a href="index.php" class="btn">Cari Lowongan Sekarang</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalJadwal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Jadwal Wawancara</h2>
            <span class="close-modal">&times;</span>
        </div>
        
        <div class="modal-body">
            <div class="modal-grid">
                <div class="grid-column">
                    <div class="form-group-modal">
                        <label>Pelamar (ID Lamaran)</label>
                        <input type="text" id="modalPelamar" class="modal-input" readonly>
                    </div>
                    
                    <div class="form-group-modal">
                        <label>Posisi</label>
                        <input type="text" id="modalPosisi" class="modal-input" readonly>
                    </div>

                    <div class="form-group-modal" style="flex-grow: 1;">
                        <label>Catatan HRD</label>
                        <textarea id="modalCatatan" class="modal-input modal-textarea" readonly></textarea>
                    </div>
                </div>

                <div class="grid-column">
                    <div class="form-group-modal">
                        <label>Status</label>
                        <input type="text" id="modalStatus" class="modal-input" readonly>
                    </div>

                    <div class="form-group-modal">
                        <label>Tanggal</label>
                        <div class="input-icon-wrapper">
                            <input type="text" id="modalTanggal" class="modal-input" readonly>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label>Jam</label>
                        <div class="input-icon-wrapper">
                            <input type="text" id="modalJam" class="modal-input" readonly>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label>Lokasi</label>
                        <div class="input-icon-wrapper">
                            <input type="text" id="modalLokasi" class="modal-input" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-close-modal">Tutup</button>
        </div>
    </div>
</div>

<?php

if (isset($stmt) && $stmt) $stmt->close();


$extra_js = 'assets/js/status_lamaran.js';
require_once 'templates/footer.php';

if (isset($koneksi)) $koneksi->close();
?>