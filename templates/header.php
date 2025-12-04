<?php
// Panggil init.php (session & koneksi) di paling atas
include_once __DIR__ . '/../config/init.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPEKA - Sistem Penerimaan Karyawan</title>

    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <?php if (in_array($current_page, ['login.php', 'register.php'])): ?>
        <link rel="stylesheet" href="assets/css/auth.css">
    <?php elseif ($current_page == 'index.php'): ?>
        <link rel="stylesheet" href="assets/css/style.css">
    <?php elseif ($current_page == 'about.php'): ?>
        <link rel="stylesheet" href="assets/css/about.css">
    <?php elseif ($current_page == 'job.php' || $current_page == 'job_detail.php'): ?>
        <link rel="stylesheet" href="assets/css/job.css">
    <?php elseif ($current_page == 'contact.php'): ?>
        <link rel="stylesheet" href="assets/css/contact.css">
    <?php elseif ($current_page == 'profil.php'): ?>
        <link rel="stylesheet" href="assets/css/profil.css">
    <?php elseif ($current_page == 'status_lamaran.php'): ?>
        <link rel="stylesheet" href="assets/css/status.css">
    <?php endif; ?>
</head>

<body>
    <header class="main-header">
        <div class="nav-logo">
            <a href="index.php" class="logo">
                <img src="assets/img/logo.png" alt="logo-syjuracoffee">
            </a>
            <a href="index.php">
                <h2 class="nav-title">SYJURA COFFEE</h2>
            </a>
        </div>


        <nav class="main-nav" id="mainNav">
            <ul class="nav-list">
                <li><a href="index.php"
                        class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Beranda</a></li>
                <li><a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">Tentang
                        Kami</a></li>
                <li><a href="job.php" class="<?php echo ($current_page == 'job.php') ? 'active' : ''; ?>">Lowongan</a>
                </li>
                <li><a href="contact.php"
                        class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Kontak</a></li>
                <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar'): ?>
                    <li><a href="status_lamaran.php"
                            class="<?php echo ($current_page == 'status_lamaran.php') ? 'active' : ''; ?>">Status
                            Lamaran</a></li>
                <?php endif; ?>
            </ul>

            <div class="user-menu">
                <?php if (isset($_SESSION['id_user'])): ?>
                    <?php if ($_SESSION['role'] == 'pelamar'): ?>
                        <a href="profil.php" class="btn-profile">
                            Profil (<?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>)
                        </a>
                    <?php endif; ?>
                    <!-- <a href="logout.php" class="btn-logout">Logout</a> -->
                <?php else: ?>
                    <a href="login.php" class="btn-login">Daftar/Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Tombol Hamburger -->
        <button class="hamburger-menu" id="hamburgerMenu" aria-label="Buka menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <main>
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="container">
                <div class="message <?php echo $_SESSION['flash_message']['type'] == 'success' ? 'success' : 'error'; ?>"
                    style="margin: 1rem auto; text-align: center;">
                    <?php echo $_SESSION['flash_message']['text']; ?>
                </div>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>