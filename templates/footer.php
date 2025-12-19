<?php
/**
 * Footer Template
 * Menutup struktur HTML utama dan memuat script.
 */

// Pastikan helper tersedia (untuk menuActive)
require_once __DIR__ . '/../helpers/view_helper.php';
$current_page = getCurrentPage();
?>
    </main> <footer class="main-footer">
        <div class="wrapper-left">
            <div class="top-footer">
                <div class="element-left" id="left1"></div>
                <div class="element-left" id="left2">
                    <img src="assets/img/logo.png" alt="logo-footer" class="logo-footer">
                    <h2>SYJURA COFFEE</h2>
                </div>
            </div>
            
            <div class="contain-left-footer">
                <p>© 2025 SYJURA COFFEE </p> <br>
                <p>Sistem Informasi E-Recruitment (SIPEKA) merupakan platform resmi <br> 
                   rekrutmen karyawan SYJURA COFFEE yang dirancang untuk mendukung <br> 
                   proses seleksi secara digital, transparan, dan efisien.</p><br>
                <p>Bergabunglah bersama kami dan wujudkan pengalaman kerja yang <br> 
                   hangat, profesional, dan penuh semangat di dunia kopi. <br> 
                   Alamat: Jl. Lohbener, Pamayahan</p><br>
                <p>Email: hrdsyjuracoffe@gmail.com | Telepon: (0274) 123456 <br> <br> Temukan kami di:</p>
            </div>
            
            <div class="sosmed-footer">
                <a href="#"><img src="assets/img/footer/facebook.png" alt="facebook" class="icon-sosmed"></a>
                <a href="#"><img src="assets/img/footer/instagram.png" alt="instagram" class="icon-sosmed"></a>
                <a href="#"><img src="assets/img/footer/twitter.png" alt="twitter" class="icon-sosmed"></a>
            </div>
        </div>

        <div class="wrapper-right">
            <div class="contain-right-footer">
                <div class="footer-list">
                    <p>Halaman</p>
                    <ul>
                        <li><a href="index.php" class="<?= menuActive('index.php', $current_page) ?>">Beranda</a></li>
                        <li><a href="about.php" class="<?= menuActive('about.php', $current_page) ?>">Tentang Kami</a></li>
                        <li><a href="job.php" class="<?= menuActive(['job.php', 'job_detail.php'], $current_page) ?>">Lowongan</a></li>
                        <li><a href="contact.php" class="<?= menuActive('contact.php', $current_page) ?>">Kontak</a></li>
                        <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar'): ?>
                            <li><a href="status_lamaran.php" class="<?= menuActive('status_lamaran.php', $current_page) ?>">Status Lamaran</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <?php if (!isset($_SESSION['id_user'])): ?>
                <div class="footer-list">
                    <p>Akun</p> <ul>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Daftar</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bottom-footer">
                <div class="element-right" id="right1">
                    <div class="element-right" id="right2"></div>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/script.js"></script> <?php if (isset($extra_js)): ?>
        <script src="<?= htmlspecialchars($extra_js) ?>"></script>
    <?php endif; ?>
</body>
</html>