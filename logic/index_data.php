<?php
/**
 * Logika pengambilan data untuk Halaman Beranda.
 * Membutuhkan variabel $koneksi dari init/header.
 */

if (!isset($koneksi)) {
    die("Error: Koneksi database belum dimuat.");
}

// Query mengambil 3 lowongan aktif terbaru yang belum kadaluarsa
$query_lowongan = "SELECT id_lowongan, posisi_lowongan, deskripsi_singkat 
                   FROM lowongan 
                   WHERE status_lowongan = 'Aktif' 
                   AND tanggal_tutup >= CURDATE() 
                   ORDER BY id_lowongan DESC 
                   LIMIT 3";

$result_lowongan = $koneksi->query($query_lowongan);
?>