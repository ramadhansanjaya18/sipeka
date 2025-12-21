<?php
require_once 'templates/header.php';
require_once 'logic/job_list.php';
?>

<div class="container">
    <div class="hero-section">
        <h1>Temukan Karir Impian Anda</h1>
        <p>Jelajahi berbagai lowongan pekerjaan yang tersedia di Syjura Coffee dan temukan yang paling cocok untuk Anda.</p>
        
        <form action="job.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari posisi atau kata kunci..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <div class="job-listings">
        <h2>Lowongan Tersedia</h2>

        <div class="job-cards-container">
            <?php if ($result_jobs && $result_jobs->num_rows > 0) : ?>
                <?php while ($row = $result_jobs->fetch_assoc()) : ?>
                    <div class="job-card">
                        <h3><?= htmlspecialchars($row['posisi_lowongan'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="company-name">Syjura Coffee</p>
                        <p class="job-description">
                            <?= htmlspecialchars(substr($row['deskripsi_singkat'], 0, 100), ENT_QUOTES, 'UTF-8') ?>...
                        </p>
                        <div class="job-card-footer">
                            <span class="deadline">Tutup pada: <?= htmlspecialchars($row['tanggal_tutup_formatted'], ENT_QUOTES, 'UTF-8') ?></span>
                            <a href="job_detail.php?id=<?= htmlspecialchars($row['id_lowongan'], ENT_QUOTES, 'UTF-8') ?>" class="btn-detail">Lihat Detail</a>
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
require_once 'templates/footer.php';
if (isset($koneksi) && $koneksi) $koneksi->close();
?>