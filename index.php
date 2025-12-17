<?php
// Pastikan file header sudah memuat koneksi database ($koneksi)
include_once 'templates/header.php';
?>

<div class="hero-section">
    <div class="title-hero">
        <h1>Gabung Bersama</h1>
        <a href="#top-contain">
            <p>SYJURA COFFEE</p>
        </a>
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
            <p>SYJURA COFFEE adalah modern coffee shop asal Indramayu
                yang mengusung konsep: ‘Ngopi Modern, Rasa Lokal’. <br>
                Kami percaya bahwa SYJURA COFFEE ini adalah tempat kopi terbaik
                di Indramayu</p>
            <div class="button-lamar-sekarang">
                <a href="job.php">
                    Lamar Sekarang
                </a>
            </div>
        </div>
    </div>

    <div class="new_job_container">
        <h1>Lowongan Terbaru</h1>
        <div class="container_job">
            <?php
            // --- QUERY PERBAIKAN (UPDATE PENTING) ---
            // 1. status_lowongan = 'Aktif': Hanya ambil yang dilabeli Aktif
            // 2. AND tanggal_tutup >= CURDATE(): PENTING! Hanya ambil yang tanggal tutupnya BELUM LEWAT hari ini.
            //    (Ini mengatasi masalah lowongan '123456789' yang muncul meski sudah tanggal 17-12)
            // 3. ORDER BY id_lowongan DESC: Urutkan dari yang terbaru
            // 4. LIMIT 3: Batasi 3 data
            
            $query = "SELECT * FROM lowongan 
                      WHERE status_lowongan = 'Aktif' 
                      AND tanggal_tutup >= CURDATE() 
                      ORDER BY id_lowongan DESC 
                      LIMIT 3";
            
            $result = $koneksi->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $posisi_lowongan = htmlspecialchars($row['posisi_lowongan']);
                    $posisi_lower = strtolower($posisi_lowongan);
                    
                    // --- Logika Penentuan Icon ---
                    $icon_path = 'assets/img/beranda/icon_default.png'; 

                    if (strpos($posisi_lower, 'barista') !== false) {
                        $icon_path = 'assets/img/beranda/icon_barista.png';
                    } elseif (strpos($posisi_lower, 'waiter') !== false || strpos($posisi_lower, 'pelayan') !== false) {
                        $icon_path = 'assets/img/beranda/icon_waiter.png';
                    } elseif (strpos($posisi_lower, 'kitchen') !== false || strpos($posisi_lower, 'dapur') !== false || strpos($posisi_lower, 'cook') !== false) {
                        $icon_path = 'assets/img/beranda/icon_kitchen_staff.png';
                    } elseif (strpos($posisi_lower, 'kasir') !== false || strpos($posisi_lower, 'cashier') !== false || strpos($posisi_lower, 'finance') !== false || strpos($posisi_lower, 'accounting') !== false) {
                        $icon_path = 'assets/img/beranda/icon_accounting.png';
                    } elseif (strpos($posisi_lower, 'cleaning') !== false) {
                        $icon_path = 'assets/img/beranda/icon_cleaning_services.png';
                    } elseif (strpos($posisi_lower, 'supervisor') !== false) {
                        $icon_path = 'assets/img/beranda/icon_supervisor.png';
                    }
            ?>
                    <div class="card_job_new">
                        <div class="icons_job">
                            <img src="<?php echo $icon_path; ?>" alt="icon_job">
                        </div>
                        <div class="contain_job">
                            <h2><?php echo $posisi_lowongan; ?></h2>
                            <p><?php echo htmlspecialchars(substr($row['deskripsi_singkat'], 0, 100)); ?>...</p>
                            <div class="button_job_new">
                                <a href="job_detail.php?id=<?php echo htmlspecialchars($row['id_lowongan']); ?>">Lamar Sekarang</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                // Tampilan jika tidak ada lowongan aktif
                echo '<div style="width:100%; text-align:center; padding: 20px; color: #666;">';
                echo '<p>Belum ada lowongan aktif terbaru saat ini.</p>';
                echo '</div>';
            }
            ?>
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
// Panggil Footer
include_once 'templates/footer.php';

// Tutup koneksi database
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
?>