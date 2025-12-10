<?php
// 1. Memulai session dan memanggil file-file penting
include 'templates/header.php'; 
include 'config/auth_pelamar.php';

// 2. Mengambil ID pelamar dari session
$id_pelamar = $_SESSION['id_user'];
$nama_pelamar = $_SESSION['nama_lengkap']; 

// 3. Query Database
// Mengambil data lamaran beserta jadwal wawancara (jika ada)
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

<link rel="stylesheet" href="assets/css/status.css">

<div class="status-container">
    <h1>Status Lamaran Saya</h1>
    <p>Lacak status semua lamaran yang telah Anda kirimkan.</p>

    <div class="timeline-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($lamaran = $result->fetch_assoc()):
                // Menentukan kelas CSS untuk warna status
                $status_clean = strtolower(str_replace(' ', '-', $lamaran['status_lamaran']));
                $status_class = 'status-' . $status_clean;
                
                // Format Tanggal Pelamaran
                $tgl_lamar = date('d M Y, H:i', strtotime($lamaran['tanggal_lamaran']));

                // Persiapan Data untuk Modal
                $jadwal_db = $lamaran['jadwal'];
                $tgl_modal = !empty($jadwal_db) ? date('d-m-Y', strtotime($jadwal_db)) : '-';
                $jam_modal = !empty($jadwal_db) ? date('H.i', strtotime($jadwal_db)) : '-';
                $lokasi_modal = !empty($lamaran['lokasi']) ? $lamaran['lokasi'] : '-';
                $catatan_modal = !empty($lamaran['catatan']) ? $lamaran['catatan'] : 'Tidak ada catatan tambahan.';
            ?>
                <div class="timeline-item <?php echo $status_class; ?>">
                    <div class="timeline-icon">
                        <?php if($lamaran['status_lamaran'] == 'Diterima') echo '&#10003;'; 
                              else if($lamaran['status_lamaran'] == 'Ditolak') echo '&#10005;'; 
                              else echo '&#9679;'; ?>
                    </div>
                    
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3 class="job-title"><?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?></h3>
                            <span class="application-date"><?php echo $tgl_lamar; ?> WIB</span>
                        </div>
                        
                        <p class="company-name">Syjura Coffee - <?php echo htmlspecialchars($lamaran['judul_lowongan']); ?></p>
                        
                        <div class="timeline-footer">
                            <div class="footer-left">
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($lamaran['status_lamaran']); ?>
                                </span>
                                <a href="job_detail.php?id=<?php echo $lamaran['id_lowongan']; ?>" class="link-detail-text">Lihat Lowongan</a>
                            </div>

                            <div class="footer-right">
                                <?php if ($lamaran['status_lamaran'] == 'Wawancara'): ?>
                                    <button type="button" class="btn-jadwal" 
                                        data-posisi="<?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?>"
                                        data-tanggal="<?php echo $tgl_modal; ?>"
                                        data-jam="<?php echo $jam_modal; ?>"
                                        data-lokasi="<?php echo htmlspecialchars($lokasi_modal); ?>"
                                        data-catatan="<?php echo htmlspecialchars($catatan_modal); ?>">
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
                <p>Anda belum melamar pekerjaan apapun.</p>
                <a href="index.php" class="btn-jadwal" style="text-decoration: none;">Cari Lowongan</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="modalJadwal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Jadwal Wawancara</h2>
            <span class="close-btn">&times;</span>
        </div>
        
        <div class="modal-body">
            <div class="modal-row">
                <span class="modal-label">Posisi Dilamar</span>
                <span class="modal-value" id="modalPosisi"></span>
            </div>
            
            <div class="modal-row">
                <span class="modal-label">Tanggal</span>
                <span class="modal-value" id="modalTanggal"></span>
            </div>

            <div class="modal-row">
                <span class="modal-label">Jam</span>
                <span class="modal-value"><span id="modalJam"></span> WIB</span>
            </div>

            <div class="modal-row">
                <span class="modal-label">Lokasi</span>
                <span class="modal-value" id="modalLokasi"></span>
            </div>

            <div class="modal-note">
                <strong>Catatan HRD:</strong><br>
                <span id="modalCatatan"></span>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-tutup-modal">Tutup</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("modalJadwal");
    var closeSpan = document.getElementsByClassName("close-btn")[0];
    var closeBtn = document.getElementsByClassName("btn-tutup-modal")[0];

    // Fungsi Buka Modal
    var buttons = document.querySelectorAll('.btn-jadwal');
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Ambil data dari atribut tombol
            var posisi = this.getAttribute('data-posisi');
            var tanggal = this.getAttribute('data-tanggal');
            var jam = this.getAttribute('data-jam');
            var lokasi = this.getAttribute('data-lokasi');
            var catatan = this.getAttribute('data-catatan');

            // Isi data ke dalam modal
            document.getElementById('modalPosisi').textContent = posisi;
            document.getElementById('modalTanggal').textContent = tanggal;
            document.getElementById('modalJam').textContent = jam;
            document.getElementById('modalLokasi').textContent = lokasi;
            document.getElementById('modalCatatan').textContent = catatan;

            // Tampilkan modal dengan display flex agar centered
            modal.style.display = "flex";
        });
    });

    // Fungsi Tutup Modal
    function tutupModal() {
        modal.style.display = "none";
    }

    closeSpan.onclick = tutupModal;
    closeBtn.onclick = tutupModal;

    // Tutup jika klik di luar area modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            tutupModal();
        }
    }
});
</script>

<?php
$stmt->close();
$koneksi->close();
include 'templates/footer.php';
?>