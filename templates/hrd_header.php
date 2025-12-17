<?php
// 1. Memanggil file init (yang sudah start session & koneksi)
include_once __DIR__ . '/../config/init.php';

// 2. Memanggil file auth_hrd.php
// File ini akan mengecek session, jika tidak valid, akan redirect ke login
include_once __DIR__ . '/../config/auth_hrd.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRD Dashboard - SIPEKA</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/hrd/hrd.css">
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/hrd.js"></script>
</head>

<body>
    <header class="top-bar">
        <button class="mobile-menu-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20"
                height="20" viewBox="0 0 50 50">
                <path
                    d="M 0 7.5 L 0 12.5 L 50 12.5 L 50 7.5 Z M 0 22.5 L 0 27.5 L 50 27.5 L 50 22.5 Z M 0 37.5 L 0 42.5 L 50 42.5 L 50 37.5 Z">
                </path>
            </svg></button>

            <img src="../assets/img/logo.png" alt="logo-syjuracoffee">
            <h4>SYJURA COFFEE</h4>
    </header>
    <div class="sidebar-overlay"></div>
    <div class="hrd-wrapper">

        <?php
        // 4. Memanggil file sidebar
        include_once 'hrd_sidebar.php';
        ?>

        <div class="main-content">

            <main class="content-area">

                <?php
                if (isset($_SESSION['message'])) {
                    // Pastikan tipe pesan valid untuk keamanan
                    $allowed_types = ['success', 'error', 'warning', 'info'];
                    $message_type = in_array($_SESSION['message']['type'], $allowed_types) ? $_SESSION['message']['type'] : 'info';
                    $message_text = $_SESSION['message']['text'];

                    // Tampilkan pesan dalam format baru
                    echo '<div class="message-container">';
                    echo '    <div class="message animated ' . $message_type . '">';
                    echo '        <button class="close-btn">&times;</button>';
                    echo '        <p>' . htmlspecialchars($message_text, ENT_QUOTES, 'UTF-8') . '</p>';
                    echo '    </div>';
                    echo '</div>';

                    // Hapus pesan dari session setelah ditampilkan
                    unset($_SESSION['message']);
                }
                ?>