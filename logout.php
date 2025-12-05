<?php

// 1. Selalu mulai session untuk dapat memanipulasinya.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


session_unset();
session_destroy();

// 3. Arahkan kembali ke halaman login dengan pesan.
header("Location: login.php?pesan=Anda+telah+logout");
exit();
