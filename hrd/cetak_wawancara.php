<?php
/**
 * Halaman Cetak Laporan
 */
require '../config/init.php';
require '../config/auth_hrd.php';
require_once '../logic/hrd_cetak_logic.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Wawancara</title>
    <link rel="stylesheet" href="../assets/css/hrd/cetak.css">
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
        <button onclick="window.close()" class="btn-print btn-close">Tutup</button>
    </div>

    <div class="header">
        <h1>Syjura Coffee</h1>
        <h2>Laporan Data Wawancara Pelamar</h2>
        <p>Alamat: Jl. Lohbener, Pamayahan, Indramayu, Indonesia | Telp: (0274) 123456</p>
    </div>

    <div class="meta-info">
        <table>
            <tr><td><strong>Laporan</strong></td><td>: <?php echo htmlspecialchars($status_label); ?></td></tr>
            <tr><td><strong>Tanggal Cetak</strong></td><td>: <?php echo date('d-m-Y H:i'); ?> WIB</td></tr>
            <tr><td><strong>Dicetak Oleh</strong></td><td>: <?php echo htmlspecialchars($nama_hrd); ?></td></tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Pelamar</th>
                <th width="15%">Posisi</th>
                <th width="15%">Jadwal</th>
                <th width="15%">Jam</th>
                <th width="15%">Kontak</th>
                <th width="20%">Lokasi & Catatan</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td style="text-align:center;"><?php echo $no++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['posisi_dilamar']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal']); ?> <br></td>
                    <td><?php echo htmlspecialchars($row['jam']); ?> WIB <br></td>
                    <td><?php echo htmlspecialchars($row['no_telepon']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['lokasi']); ?> <br>
                        <small><i><?php echo htmlspecialchars($row['catatan']); ?></i></small>
                    </td>
                    <td style="text-align:center;"><?php echo htmlspecialchars($row['status_wawancara']); ?></td>
                </tr>
            <?php 
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
<?php
if (isset($stmt)) $stmt->close();
if (isset($koneksi)) $koneksi->close();
?>