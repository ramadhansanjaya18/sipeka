<?php
// 1. Memulai session dan memanggil file-file penting
include 'templates/header.php'; 
include 'config/auth_pelamar.php';

// 2. Mengambil ID pelamar dari session
$id_pelamar = $_SESSION['id_user'];
$nama_pelamar = $_SESSION['nama_lengkap']; 

// 3. Query Database
$query = "SELECT 
            l.judul AS judul_lowongan,
            l.posisi_lowongan,
            la.id_lamaran,
            la.tanggal_lamaran,
            la.status_lamaran,
            la.id_lowongan,
            w.jadwal,
            w.lokasi,
            w.catatan
          FROM lamaran la
          JOIN lowongan l ON la.id_lowongan = l.id_lowongan
          LEFT JOIN wawancara w ON la.id_lamaran = w.id_lamaran
          WHERE la.id_pelamar = ?
          ORDER BY la.tanggal_lamaran DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pelamar);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="assets/css/status.css?v=<?php echo time(); ?>">

<div class="status-container">
    <h1>Status Lamaran Saya</h1>
    <p>Lacak status semua lamaran yang telah Anda kirimkan.</p>

    <div class="timeline-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($lamaran = $result->fetch_assoc()):
                $status_clean = strtolower(str_replace(' ', '-', $lamaran['status_lamaran']));
                $status_class = 'status-' . $status_clean;
                
                // Format Tanggal Pelamaran
                $tgl_lamar = date('d M Y, H:i', strtotime($lamaran['tanggal_lamaran']));

                // Data Modal
                $jadwal_db = $lamaran['jadwal'];
                $tgl_modal = !empty($jadwal_db) ? date('d-m-Y', strtotime($jadwal_db)) : '-';
                $jam_modal = !empty($jadwal_db) ? date('H.i', strtotime($jadwal_db)) : '-';
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
                                        data-lokasi="<?php echo htmlspecialchars($lamaran['lokasi'] ?? '-'); ?>"
                                        data-catatan="<?php echo htmlspecialchars($lamaran['catatan'] ?? '-'); ?>">
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

<div id="modalJadwal" class="modal-overlay">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalJadwal');
    const closeBtn = document.querySelector('.close-modal');
    const btnCloseBottom = document.querySelector('.btn-close-modal');
    const jadwalButtons = document.querySelectorAll('.btn-jadwal');

    jadwalButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil data
            const idLamaran = this.dataset.id;
            const nama = this.dataset.nama;
            const posisi = this.dataset.posisi;
            const status = this.dataset.status;
            const tanggal = this.dataset.tanggal;
            const jam = this.dataset.jam;
            const lokasi = this.dataset.lokasi;
            const catatan = this.dataset.catatan;

            // Set Data ke Input
            document.getElementById('modalPelamar').value = `${nama} (ID:${idLamaran})`;
            document.getElementById('modalPosisi').value = posisi;
            document.getElementById('modalCatatan').value = catatan;
            document.getElementById('modalStatus').value = status;
            document.getElementById('modalTanggal').value = tanggal;
            document.getElementById('modalJam').value = jam;
            document.getElementById('modalLokasi').value = lokasi;
            
            modal.style.display = 'flex';
        });
    });

    function closeModal() {
        modal.style.display = 'none';
    }

    if(closeBtn) closeBtn.addEventListener('click', closeModal);
    if(btnCloseBottom) btnCloseBottom.addEventListener('click', closeModal);

    window.addEventListener('click', function(e) {
        if (e.target == modal) closeModal();
    });
});
</script>

<?php
$stmt->close();
$koneksi->close();
include 'templates/footer.php';
?>