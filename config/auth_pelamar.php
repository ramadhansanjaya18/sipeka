<?php
/**
 * Skrip Otentikasi untuk Halaman Pelamar.
 *
 * File ini disertakan di setiap halaman khusus pelamar (profil, status lamaran, dll).
 * Tujuannya adalah untuk memverifikasi bahwa pengguna yang mengakses halaman
 * sudah login dan memiliki peran (role) sebagai 'pelamar'.
 * Jika tidak, pengguna akan dialihkan ke halaman login.
 * 
 * File ini berasumsi `config/init.php` sudah dipanggil sebelumnya.
 */

// 1. Cek apakah session 'id_user' ada (menandakan sudah login).
if (!isset($_SESSION['id_user'])) {
    // Jika tidak ada, redirect ke halaman login.
    header("Location: login.php?pesan=Silakan+login+terlebih+dahulu");
    exit();
}

// 2. Cek apakah session 'role' adalah 'pelamar'.
if ($_SESSION['role'] != 'pelamar') {
    // Jika bukan 'pelamar' (misal: HRD), hancurkan session dan alihkan.
    session_unset();
    session_destroy();
    header("Location: login.php?pesan=Akses+Ditolak");
    exit();
}

// Jika lolos semua pengecekan, skrip akan berhenti dan halaman asli akan lanjut di-render.
