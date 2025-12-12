<?php
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
        <img src="assets/img/beranda/beans -right.png" alt="icon-kopi-kanan">
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
            // Query Lowongan
            $query = "SELECT * FROM lowongan ORDER BY id_lowongan DESC LIMIT 3";
            $result = $koneksi->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $posisi_lowongan = htmlspecialchars($row['posisi_lowongan']);
                    $posisi_lower = strtolower($posisi_lowongan);
                    
                    // PERBAIKAN: Gunakan logo.png sebagai default karena default_icon.png tidak ada
                    $icon_path = 'assets/img/beranda/icon_default.png'; 

                    // Logika Penentuan Icon
                    if (strpos($posisi_lower, 'barista') !== false) {
                        $icon_path = 'assets/img/beranda/icon_barista.png';
                    } elseif (strpos($posisi_lower, 'waiter') !== false) {
                        $icon_path = 'assets/img/beranda/icon_waiter.png';
                    } elseif (strpos($posisi_lower, 'kitchen') !== false || strpos($posisi_lower, 'dapur') !== false) {
                        $icon_path = 'assets/img/beranda/icon_kitchen_staff.png';
                    } elseif (strpos($posisi_lower, 'kasir') !== false || strpos($posisi_lower, 'cashier') !== false) {
                        $icon_path = 'assets/img/beranda/icon_accounting.png';
                    } elseif (strpos($posisi_lower, 'cleaning') !== false) {
                        $icon_path = 'assets/img/beranda/icon_cleaning_services.png';
                    } elseif (strpos($posisi_lower, 'supervisor') !== false) {
                        // Menambahkan icon supervisor
                        $icon_path = 'assets/img/beranda/icon_supervisor.png';
                    }

                    echo '<div class="card_job_new">';
                    echo '    <div class="icons_job">';
                    // Pastikan path tidak menggunakan ./ agar lebih aman di berbagai server
                    echo '        <img src="' . $icon_path . '" alt="icon_job">';
                    echo '    </div>';
                    echo '    <div class="contain_job">';
                    echo '        <h2>' . $posisi_lowongan . '</h2>';
                    echo '        <p>' . htmlspecialchars(substr($row['deskripsi_singkat'], 0, 100)) . '...</p>';
                    echo '        <div class="button_job_new">';
                    echo '            <a href="job_detail.php?id=' . htmlspecialchars($row['id_lowongan']) . '">Lamar Sekarang</a>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p style="text-align:center; width:100%;">Belum ada lowongan terbaru saat ini.</p>';
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

<?php
// Panggil Footer
include_once 'templates/footer.php';

// Tutup koneksi
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
?>