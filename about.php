// Isi file sipeka/about.php (Tidak Berubah)
<?php

include_once 'templates/header.php';
?>
<section class="section-1">
    <div class="hero_about">
        <h1>Tentang SYJURA COFFEE</h1>
    </div>
</section>
<section class="section-2">
    <div class="visi-misi">
        <div class="top-contain">
            <img src="./assets/img/beranda/beans-left.png" alt="icont-kopi-kiri">
            <h1>SYJURA COFFEE</h1>
            <img src="./assets/img/beranda/beans -right.png" alt="icont-kopi-kanan">
        </div>
        <div class="container-visi-misi">
            <div class="container-left">
                <h1>Visi</h1>
                <p>Menjadi coffee shop lokal terdepan yang memadukan cita <br>
                    rasa kopi Nusantara dengan konsep pelayanan modern dan <br>
                    berorientasi pada pengalaman pelanggan.</p>
            </div>
            <div class="container-right">
                <h1>Misi</h1>
                <ul>
                    <li class="list-misi">Menghadirkan produk kopi berkualitas dengan bahan baku terbaik dari petani
                        lokal.</li>
                    <li class="list-misi">Meningkatkan profesionalitas dan kesejahteraan karyawan melalui sistem
                        rekrutmen dan pelatihan yang transparan.</li>
                    <li class="list-misi">Mengembangkan inovasi digital dalam pelayanan, termasuk sistem pemesanan dan
                        rekrutmen berbasis web.</li>
                    <li class="list-misi">Membangun hubungan jangka panjang dengan pelanggan melalui layanan yang ramah,
                        cepat, dan konsisten.</li>
                    <li class="list-misi">Berkontribusi pada pengembangan ekonomi lokal melalui kerja sama dengan pelaku
                        UMKM dan petani kopi.</li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</section>
<section class="section-3">
    <div class="main-container">
        <div class="row-header">
            <div class="col-title-left">
                <h2>Struktur Organisasi</h2>
            </div>
            <div class="col-title-right">
                <h2>Tim Kami</h2>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="org-chart-area">
                <div class="tree">
                    <ul>
                        <li>
                            <div class="card-box">CEO / OWNER</div>
                            <ul>
                                <li>
                                    <div class="card-box">General Manager</div>
                                    <ul>
                                        <li>
                                            <div class="card-box">HRD</div>
                                        </li>
                                        <li>
                                            <div class="card-box">Finance &<br>Accounting</div>
                                            <ul>
                                                <li>
                                                    <div class="card-box">Operational Supervisor</div>
                                                    <ul>
                                                        <li><div class="card-box">Barista</div></li>
                                                        <li><div class="card-box">Waiter/<br>Waitress</div></li>
                                                        <li><div class="card-box">Kitchen Staff</div></li>
                                                        <li><div class="card-box">Cleaning<br>Service</div></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li>
                                            <div class="card-box">Marketing &<br>Creative</div>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="card-box">IT &<br>Digital Support</div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="team-area">
                <div class="image-frame">
                    <img src="assets/img/about/karywan-syjura.png" alt="Tim Syjura Coffee">
                </div>
            </div>
        </div>
    </div>
</section>

</section>
<section class="section-4">
    <div class="main-container">
        <div class="content-grid">
            
            <div class="left-panel">
                <div class="character-wrapper">
                    <img src="assets/img/about/karakter-syjura.png" alt="Karakter Syjura">
                </div>
                
                <div class="schedule-wrapper">
                    <h3>Jam Operasional SYJURA COFFEE</h3>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th>Jam Operasional</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Senin - Jum'at</td>
                                <td>08.00 - 23.00 WIB</td>
                            </tr>
                            <tr>
                                <td>Sabtu - Minggu</td>
                                <td>09.00 - 22.00 WIB</td>
                            </tr>
                            <tr>
                                <td>Libur Nasional</td>
                                <td>10.00 - 21.00 WIB</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="right-panel">
                <div class="info-badge">
                    <img src="https://img.icons8.com/ios/50/000000/info--v1.png" alt="info">
                    <span>Tentang SIPEKA</span>
                </div>
                <div class="info-content-box">
                    <p>Sebagai bagian dari inovasi digital,</p>
                    <p><strong>SYJURA COFFEE menghadirkan SIPEKA,</strong><br>
                    Sistem Informasi E-Recruitment Karyawan Berbasis Web,</p>
                    <p>untuk mempermudah proses rekrutmen secara transparan dan efisien. Pelamar dapat mendaftar, mengunggah dokumen, serta memantau status lamaran secara real-time.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<?php
// Panggil Footer
include_once 'templates/footer.php';

// Tutup koneksi
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
?>