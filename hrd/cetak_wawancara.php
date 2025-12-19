<?php
require '../config/init.php';      // Memuat koneksi dan konfigurasi sesi
require '../config/auth_hrd.php';  // Memastikan user adalah HRD

// --- BARU: Ambil Nama HRD dari Database berdasarkan User yang Login ---
// Menggunakan $_SESSION['id_user'] yang diset saat login
$id_user_login = $_SESSION['id_user'];

$stmt_hrd = $koneksi->prepare("SELECT username FROM user WHERE id_user = ?");
$stmt_hrd->bind_param("i", $id_user_login);
$stmt_hrd->execute();
$res_hrd = $stmt_hrd->get_result();

if ($res_hrd->num_rows > 0) {
    $data_hrd = $res_hrd->fetch_assoc();
    // Menggunakan username sebagai nama tampilan (sesuai struktur database user hrd)
    $nama_hrd = $data_hrd['username']; 
} else {
    $nama_hrd = "HRD Manager"; // Fallback jika data tidak ditemukan
}
$stmt_hrd->close();
// ---------------------------------------------------------------------

// Ambil parameter filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_label = "Semua Data";

// Query dasar
$query = "SELECT w.id_wawancara, p.nama_lengkap, l.posisi_dilamar, 
            DATE_FORMAT(w.jadwal, '%d-%m-%Y') AS tanggal, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam, 
            w.lokasi, w.status_wawancara, w.catatan,
            p.no_telepon
          FROM wawancara w
          JOIN lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN user u ON l.id_pelamar = u.id_user
          JOIN profil_pelamar p ON u.id_user = p.id_user";

// LOGIKA FILTER
if (!empty($search)) {
    $s = $koneksi->real_escape_string($search);
    $query .= " WHERE (p.nama_lengkap LIKE '%$s%' OR l.posisi_dilamar LIKE '%$s%' OR w.status_wawancara LIKE '%$s%')";
    $status_label = "Hasil Pencarian: \"" . htmlspecialchars($search) . "\"";
} elseif (!empty($status_filter)) {
    $s_status = $koneksi->real_escape_string($status_filter);
    $query .= " WHERE w.status_wawancara = '$s_status'";
    $status_label = "Status: " . htmlspecialchars($status_filter);
}

$query .= " ORDER BY w.jadwal ASC";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jadwal Wawancara - SIPEKA</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; color: #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 18px; font-weight: normal; }
        .header p { margin: 0; font-size: 14px; }
        
        .meta-info { margin-bottom: 20px; }
        .meta-info table { width: auto; border: none; }
        .meta-info td { padding: 2px 10px 2px 0; border: none; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f0f0f0; text-align: center; }
        
        /* Tanda tangan */
        .signature { margin-top: 50px; float: right; text-align: center; width: 200px; page-break-inside: avoid; }
        .signature p { margin-top: 60px; border-top: 1px solid #000; }

        /* Tombol Print (Sembunyi saat dicetak) */
        @media print {
            .no-print { display: none !important; }
            @page { margin: 2cm; size: A4 landscape; }
        }
        
        .btn-print {
            background-color: #6a4e3b; color: white; border: none; padding: 10px 20px; 
            cursor: pointer; border-radius: 5px; font-family: sans-serif; margin-bottom: 20px; font-size: 14px;
        }
        .btn-print:hover { background-color: #5a3e2b; }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: right;  padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn-print" style="background-color: #dc3545;">Tutup</button>
    </div>

    <div class="header">
        <h1>Syjura Coffee</h1>
        <h2>Laporan Data Wawancara Pelamar</h2>
        <p>Alamat: Jl. Lohbener, Pamayahan, Indramayu , Indonesia   | Telp: (0274) 123456</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td><strong>Laporan</strong></td>
                <td>: <?php echo htmlspecialchars($status_label); ?></td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: <?php echo date('d-m-Y H:i'); ?> WIB</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: <?php echo htmlspecialchars($nama_hrd); ?></td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Pelamar</th>
                <th style="width: 15%;">Posisi</th>
                <th style="width: 15%;">Jadwal</th>
                <th style="width: 15%;">Kontak</th>
                <th style="width: 20%;">Lokasi & Catatan</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style='text-align:center;'>" . $no++ . "</td>";
                    echo "<td><strong>" . htmlspecialchars($row['nama_lengkap']) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($row['posisi_dilamar']) . "</td>";
                    echo "<td>" . $row['tanggal'] . "<br><small>" . $row['jam'] . " WIB</small></td>";
                    echo "<td>" . htmlspecialchars($row['no_telepon']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lokasi']) . "<br><small><i>" . htmlspecialchars($row['catatan']) . "</i></small></td>";
                    echo "<td style='text-align:center;'>" . htmlspecialchars($row['status_wawancara']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>Tidak ada data wawancara yang ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="signature">
        Mengetahui,
        <br>HRD Manager
        <p><?php echo htmlspecialchars($nama_hrd); ?></p>
    </div>

</body>
</html>