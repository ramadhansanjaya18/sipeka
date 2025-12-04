<?php
/**
 * Skrip Otentikasi untuk Halaman HRD.
 *
 * File ini disertakan di setiap halaman khusus HRD.
 * Tujuannya adalah untuk memverifikasi bahwa pengguna yang mengakses halaman
 * sudah login dan memiliki peran (role) sebagai 'hrd'.
 * Jika tidak, pengguna akan dialihkan ke halaman login.
 */

// 1. Cek apakah session 'id_user' ada (menandakan sudah login).
if (!isset($_SESSION['id_user'])) {
    // Jika tidak ada, redirect ke halaman login.
    header("Location: ../login.php");
    exit();
}

// 2. Cek apakah session 'role' adalah 'hrd'.
if ($_SESSION['role'] != 'hrd') {
    // Jika bukan 'hrd' (misalnya 'pelamar' yang mencoba akses), hancurkan session-nya
    // lalu redirect ke halaman login dengan pesan error.
    session_destroy();
    header("Location: ../login.php?pesan=Akses+Ditolak");
    exit();
}

// Jika lolos semua pengecekan, skrip akan berhenti dan halaman asli akan lanjut di-render.
