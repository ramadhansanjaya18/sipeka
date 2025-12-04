<?php
/**
 * File Inisialisasi Global.
 *
 * File ini bertanggung jawab untuk melakukan pengaturan awal yang diperlukan
 * di seluruh aplikasi, seperti:
 * 1. Mengatur zona waktu (timezone).
 * 2. Memulai session PHP dengan aman.
 * 3. Menyertakan file koneksi database.
 *
 * File ini harus di-include di awal pada setiap entry point aplikasi (misal: header).
 */

// 1. Mengatur zona waktu default ke Waktu Indonesia Barat (WIB)
date_default_timezone_set("Asia/Jakarta");

// 2. Memulai session hanya jika belum ada session yang aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 3. Memanggil file koneksi database sekali saja menggunakan path absolut
include_once __DIR__ . '/koneksi.php';
