<?php
/**
 * Header Template
 */

// Panggil init (koneksi/session)
require_once __DIR__ . '/../config/init.php';

// Panggil Helper Tampilan
require_once __DIR__ . '/../helpers/view_helper.php';

// Inisialisasi variabel tampilan menggunakan helper
$current_page = getCurrentPage();
$active_css   = getPageCss($current_page);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPEKA - Sistem Informasi E-Recruitment Karyawan</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <?php if ($active_css): ?>
        <link rel="stylesheet" href="<?= $active_css ?>">
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
                <li>
                    <a href="index.php" class="<?= menuActive('index.php', $current_page) ?>">Beranda</a>
                </li>
                <li>
                    <a href="about.php" class="<?= menuActive('about.php', $current_page) ?>">Tentang Kami</a>
                </li>
                <li>
                    <a href="job.php" class="<?= menuActive(['job.php', 'job_detail.php'], $current_page) ?>">Lowongan</a>
                </li>
                
                <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar'): ?>
                    <li>
                        <a href="status_lamaran.php" class="<?= menuActive('status_lamaran.php', $current_page) ?>">Status Lamaran</a>
                    </li>
                <?php endif; ?>
                
                <li>
                    <a href="contact.php" class="<?= menuActive('contact.php', $current_page) ?>">Kontak</a>
                </li>
            </ul>

            <div class="user-menu">
                <?php if (isset($_SESSION['id_user'])): ?>
                    <?php if ($_SESSION['role'] == 'pelamar'): ?>
                        <a href="profil.php" class="btn-profile">
                            Profil (<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>)
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Daftar/Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <button class="hamburger-menu" id="hamburgerMenu" aria-label="Buka menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <main>
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="container">
                <div class="message <?= $_SESSION['flash_message']['type'] == 'success' ? 'success' : 'error' ?>" 
                     style="margin: 1rem auto; text-align: center;">
                    <?= $_SESSION['flash_message']['text'] ?>
                </div>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>