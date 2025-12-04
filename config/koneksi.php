<?php
/**
 * File Koneksi Database.
 */

// 1. Cek apakah konstanta sudah didefinisikan sebelum mendefinisikannya
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'sipeka');

// 2. Membuat koneksi baru menggunakan MySQLi.
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 3. Memeriksa apakah koneksi berhasil.
if ($koneksi->connect_error) {
    die("Koneksi ke database gagal: " . htmlspecialchars($koneksi->connect_error, ENT_QUOTES, 'UTF-8'));
}

// 4. Mengatur character set koneksi ke utf8mb4.
$koneksi->set_charset("utf8mb4");
?>