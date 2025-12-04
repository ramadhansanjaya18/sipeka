<?php
/**
 * Halaman Utama (Beranda) untuk Pelamar.
 *
 * Menampilkan daftar semua lowongan pekerjaan yang sedang aktif.
 * Pengguna (baik yang sudah login maupun belum) dapat melihat daftar lowongan
 * dan melakukan pencarian berdasarkan judul atau posisi.
 */

// 1. Memanggil header (yang juga menginisialisasi session dan koneksi database)
include_once 'templates/header.php';

// 2. Logika untuk mengambil data lowongan
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Query dasar untuk mengambil lowongan yang aktif (tanggal hari ini berada di antara tgl_buka dan tgl_tutup)
$query_base = "SELECT 
                    id_lowongan, 
                    judul, 
                    posisi_lowongan, 
                    deskripsi, 
                    DATE_FORMAT(tanggal_tutup, '%d %M %Y') AS tanggal_tutup_formatted
                FROM 
                    lowongan 
                WHERE 
                    CURDATE() BETWEEN tanggal_buka AND tanggal_tutup";

// Jika ada kata kunci pencarian, gunakan prepared statement untuk keamanan
if (!empty($search)) {
    $search_param = "%{$search}%";
    $query_full = $query_base . " AND (judul LIKE ? OR posisi_lowongan LIKE ?) ORDER BY tanggal_buka DESC";
    
    $stmt = $koneksi->prepare($query_full);
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Jika tidak ada pencarian, eksekusi query biasa
    $query_full = $query_base . " ORDER BY tanggal_buka DESC";
    $result = $koneksi->query($query_full);
}

?>

<div class="container">
    <div class="hero-section">
        <h1>Temukan Karir Impian Anda</h1>
        <p>Jelajahi berbagai lowongan pekerjaan yang tersedia di Syjura Coffee dan temukan yang paling cocok untuk Anda.</p>
        <form action="job.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari posisi atau kata kunci..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="job-listings">
        <h2>Lowongan Tersedia</h2>

        <div class="job-cards-container">
            <?php if ($result && $result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($row['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="company-name">Syjura Coffee</p>
                        <p class="job-description">
                            <?php echo htmlspecialchars(substr($row['deskripsi'], 0, 100), ENT_QUOTES, 'UTF-8'); ?>...
                        </p>
                        <div class="job-card-footer">
                            <span class="deadline">Tutup pada: <?php echo htmlspecialchars($row['tanggal_tutup_formatted'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <a href="job_detail.php?id=<?php echo htmlspecialchars($row['id_lowongan'], ENT_QUOTES, 'UTF-8'); ?>" class="btn-detail">Lihat Detail</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="no-jobs">Saat ini belum ada lowongan yang tersedia atau sesuai dengan pencarian Anda.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Panggil Footer
include_once 'templates/footer.php';

// Tutup koneksi
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
?>