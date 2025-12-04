<?php
/**
 * Skrip Logout Pengguna.
 *
 * File ini bertanggung jawab untuk mengakhiri session pengguna.
 * Ia akan menghapus semua data session dan mengalihkan pengguna
 * kembali ke halaman login dengan sebuah pesan notifikasi.
 */

// 1. Selalu mulai session untuk dapat memanipulasinya.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Hapus semua variabel session dan hancurkan session itu sendiri.
session_unset();
session_destroy();

// 3. Arahkan kembali ke halaman login dengan pesan.
header("Location: login.php?pesan=Anda+telah+logout");
exit();
