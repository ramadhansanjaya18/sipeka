<?php
// 1. Memulai session dan memanggil file-file penting
// session_start(); // DIHAPUS
include 'templates/header.php'; // Sudah memanggil init.php (session & koneksi)
include 'config/auth_pelamar.php'; // Memastikan hanya pelamar yang bisa akses
// include 'config/koneksi.php'; // DIHAPUS
// include 'templates/header.phpff'; // DIHAPUS (Pindah ke atas)

// 2. Mengambil ID pelamar dari session
$id_pelamar = $_SESSION['id_user'];

// 3. Query untuk mengambil riwayat lamaran pelamar
// ... (Query tetap sama) ...
$query = "SELECT 
            l.judul AS judul_lowongan,
            l.posisi_lowongan,
            la.tanggal_lamaran,
            la.status_lamaran,
            la.id_lowongan
          FROM lamaran la
          JOIN lowongan l ON la.id_lowongan = l.id_lowongan
          WHERE la.id_pelamar = ?
          ORDER BY la.tanggal_lamaran DESC";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_pelamar);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="status-container">
    <h1>Status Lamaran Saya</h1>
    <p>Lacak status semua lamaran yang telah Anda kirimkan.</p>

    <div class="timeline-container">
        <?php if ($result->num_rows > 0): ?>
                <?php while ($lamaran = $result->fetch_assoc()):
                    // Menentukan kelas CSS berdasarkan status lamaran
                    $status_class = 'status-' . strtolower(str_replace(' ', '-', $lamaran['status_lamaran']));
                    ?>
                        <div class="timeline-item <?php echo $status_class; ?>">
                            <div class="timeline-icon">
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h3 class="job-title"><?php echo htmlspecialchars($lamaran['posisi_lowongan']); ?></h3>
                                    <span class="application-date">Dilamar pada:
                                        <?php echo date('d M Y, H:i', strtotime($lamaran['tanggal_lamaran'])); ?></span>
                                </div>
                                <p class="company-name">Syjura Coffee - <?php echo htmlspecialchars($lamaran['judul_lowongan']); ?></p>
                                <div class="status-badge <?php echo $status_class; ?>">
                                    Status: <strong><?php echo htmlspecialchars($lamaran['status_lamaran']); ?></strong>
                                </div>
                                <a href="job_detail.php?id=<?php echo $lamaran['id_lowongan']; ?>"
                                    class="btn-detail-timeline">Lihat Detail Lowongan</a>
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

<?php
$stmt->close();
$koneksi->close();
include 'templates/footer.php';
?>