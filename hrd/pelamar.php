<?php
/**
 * View: Manajemen Pelamar
 */
$page = 'pelamar';
// 1. Header (Include Init & Auth)
include '../templates/hrd_header.php';

// 2. Logic (Include Helper Email di dalamnya)
require_once '../logic/hrd_pelamar_logic.php';
?>

<div class="page-title">
    <h1>Manajemen Data Pelamar</h1>
    <p>Kelola data pelamar yang telah mengajukan lamaran pekerjaan.</p>
</div>

<div class="page-actions">
    <div class="search-container">
        <form action="pelamar.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar atau posisi..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_lamaran && $result_lamaran->num_rows > 0): ?>
                <?php while ($row = $result_lamaran->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? 'Pelamar'); ?></td>
                        <td><?php echo htmlspecialchars($row['posisi_dilamar']); ?></td>
                        <td><?php echo htmlspecialchars($row['tgl']); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id_lamaran" value="<?php echo $row['id_lamaran']; ?>">
                                <select name="status_baru" onchange="this.form.submit()" class="table-select">
                                    <?php 
                                    foreach(['Diproses','Wawancara','Diterima','Ditolak'] as $st) {
                                        $sel = ($row['status_lamaran'] == $st) ? 'selected' : '';
                                        echo "<option value='$st' $sel>$st</option>";
                                    }
                                    ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="detail_pelamar.php?id_lamaran=<?php echo $row['id_lamaran']; ?>" class="btn-view" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" align="center">Data tidak ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
include '../templates/hrd_footer.php'; 
if(isset($koneksi)) $koneksi->close(); 
?>