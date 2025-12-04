</main>
<footer class="main-footer">
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
            <p>Sistem Informasi E-Recruitment (SIPEKA) merupakan platform resmi <br> rekrutmen karyawan SYJURA COFFEE
                yang
                dirancang untuk mendukung <br> proses seleksi secara digital, transparan, dan efisien.</p><br>
            <p>Bergabunglah bersama kami dan wujudkan pengalaman kerja yang <br> hangat, profesional, dan penuh semangat
                di
                dunia kopi. <br> Alamat: Jl. Lohbener, Pamayahan</p><br>
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
                    <li><a href="index.php"
                        class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Beranda</a></li>
                    <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">Tentang
                            Kami</a></li>
                    <li><a href="job.php" class="<?php echo ($current_page == 'job.php') ? 'active' : ''; ?>">Lowongan</a>
                    </li>
                    <li><a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Kontak</a></li>
                    <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar'): ?>
                        <li><a href="status_lamaran.php" class="<?php echo ($current_page == 'status_lamaran.php') ? 'active' : ''; ?>">Status
                                Lamaran</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-list">
                <p>Halaman</p>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Daftar</a></li>
                </ul>
            </div>
        </div>
        <div class="bottom-footer">
            <div class="element-right" id="right1">
                <div class="element-right" id="right2">

                </div>
            </div>
</footer>
<script>
    const hamburger = document.getElementById('hamburgerMenu');
    const nav = document.getElementById('mainNav');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        nav.classList.toggle('open');
    });
</script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assetsjs/script.js"></script>
</body>

</html>