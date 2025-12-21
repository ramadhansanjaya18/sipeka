<?php

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php?pesan=Silakan+login+terlebih+dahulu");
    exit();
}

if ($_SESSION['role'] != 'pelamar') {  
    session_unset();
    session_destroy();
    header("Location: login.php?pesan=Akses+Ditolak");
    exit();
}

