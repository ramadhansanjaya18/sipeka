<?php
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'hrd') {
    session_destroy();
    header("Location: ../login.php?pesan=Akses+Ditolak");
    exit();
}


