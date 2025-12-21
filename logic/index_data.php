<?php
if (!isset($koneksi)) {
    die("Error: Koneksi database belum dimuat.");
}

$query_lowongan = "SELECT id_lowongan, posisi_lowongan, deskripsi_singkat 
                   FROM lowongan 
                   WHERE status_lowongan = 'Aktif' 
                   AND tanggal_tutup >= CURDATE() 
                   ORDER BY id_lowongan DESC 
                   LIMIT 3";

$result_lowongan = $koneksi->query($query_lowongan);
?>