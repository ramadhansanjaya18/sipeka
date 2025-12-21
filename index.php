<?php
require_once 'templates/header.php';
require_once 'helpers/job_helper.php'; 
require_once 'logic/index_data.php';   
?>

<div class="hero-section">
    <div class="title-hero">
        <h1>Gabung Bersama</h1>
        <a href="#top-contain"><p>SYJURA COFFEE</p></a>
    </div>
</div>

<div class="tentang-syjura">
    <div class="top-contain" id="top-contain">
        <img src="assets/img/beranda/beans-left.png" alt="icon-kopi-kiri">
        <h1>SYJURA COFFEE</h1>
        <img src="assets/img/beranda/beans-right.png" alt="icon-kopi-kanan">
    </div>

    <div class="contain-syjura">
        <div class="container-left">
            <img src="assets/img/beranda/gambar-barista.png" alt="gambar-barista-tentang-kami">
        </div>
        <div class="container-right">
            <h1>Tentang SYJURA COFFEE</h1>
            <p>SYJURA COFFEE adalah modern coffee shop asal Indramayu yang mengusung konsep: ‘Ngopi Modern, Rasa Lokal’. <br>
               Kami percaya bahwa SYJURA COFFEE ini adalah tempat kopi terbaik di Indramayu</p>
            <div class="button-lamar-sekarang">
                <a href="job.php">Lamar Sekarang</a>
            </div>
        </div>
    </div>

    <div class="new_job_container">
        <h1>Lowongan Terbaru</h1>
        <div class="container_job">
            <?php if ($result_lowongan && $result_lowongan->num_rows > 0): ?>
                <?php while ($row = $result_lowongan->fetch_assoc()): ?>
                    <div class="card_job_new">
                        <div class="icons_job">
                            <img src="<?= getJobIcon($row['posisi_lowongan']) ?>" alt="icon_job">
                        </div>
                        <div class="contain_job">
                            <h2><?= htmlspecialchars($row['posisi_lowongan']) ?></h2>
                            <p><?= htmlspecialchars(substr($row['deskripsi_singkat'], 0, 100)) ?>...</p>
                            <div class="button_job_new">
                                <a href="job_detail.php?id=<?= $row['id_lowongan'] ?>">Lamar Sekarang</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="width:100%; text-align:center; padding: 20px; color: #666;">
                    <p>Belum ada lowongan aktif terbaru saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container_recruitmen">
        <div class="left_recruitmen">
            <h1>Bagaimana Cara Rekrutmen <br> Karyawan di SYJURA COFFEE?</h1>
            <ol class="recruitmen_list">
                <li class="recruitment-items">1. Membuka Menu Lowongan</li>
                <li class="recruitment-items">2. Melihat Detail Pekerjaan</li>
                <li class="recruitment-items">3. Mengirim Lamaran</li>
                <li class="recruitment-items">4. Validasi Sistem</li>
                <li class="recruitment-items">5. Konfirmasi & Tindak Lanjut</li>
            </ol>
        </div>
        <div class="right_recruitmen">
            <div class="card_recruit" id="recruit_1"></div>
            <div class="card_recruit" id="recruit_2"></div>
            <div class="card_recruit" id="recruit_3"></div>
        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
if (isset($koneksi)) $koneksi->close();
?>