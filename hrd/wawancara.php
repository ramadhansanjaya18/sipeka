<?php
$page = 'wawancara';
include '../templates/hrd_header.php';
require_once '../logic/hrd_wawancara_logic.php';
?>

<div class="page-title">
    <h1>Manajemen Jadwal Wawancara</h1>
    <p>Kelola jadwal wawancara untuk pelamar yang lolos seleksi.</p>
</div>

<div class="page-actions">
    <button class="btn-primary" id="btnTambahWawancara">+ Jadwal Wawancara Baru</button>
    
    <a href="cetak_wawancara.php?search=<?php echo urlencode($search); ?>" target="_blank" class="btn-primary" style="background-color: #d35400; text-decoration: none; margin-left: 10px; display:inline-block; line-height: normal;">
        <i class="fas fa-print"></i> Cetak Data
    </a>

    <div class="search-container">
        <form action="wawancara.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Pelamar</th>
                <th>Posisi</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($row['posisi_dilamar']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_formatted']); ?></td>
                        <td><?php echo htmlspecialchars($row['jam_formatted']); ?> WIB</td>
                        <td><?php echo htmlspecialchars($row['status_wawancara']); ?></td>
                        <td class="action-buttons">
                            <button class="btn-edit"
                                data-id_wawancara="<?php echo $row['id_wawancara']; ?>"
                                data-id_lamaran="<?php echo $row['id_lamaran']; ?>"
                                data-nama_pelamar="<?php echo htmlspecialchars($row['nama_lengkap']); ?>"
                                data-lokasi="<?php echo htmlspecialchars($row['lokasi']); ?>"
                                data-catatan="<?php echo htmlspecialchars($row['catatan']); ?>"
                                data-status="<?php echo $row['status_wawancara']; ?>"
                                data-tanggal="<?php echo $row['tanggal_raw']; ?>"
                                data-jam="<?php echo $row['jam_raw']; ?>">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form action="wawancara.php" method="POST" onsubmit="return confirm('Hapus jadwal ini?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_wawancara" value="<?php echo $row['id_wawancara']; ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">Belum ada jadwal wawancara.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalWawancara" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Jadwal Wawancara Baru</h2>
            <span class="modal-close">&times;</span>
        </div>
        <form id="formWawancara" action="wawancara.php" method="POST">
            <div class="modal-body">
                <div class="modal-body-left">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id_wawancara" id="formIdWawancara">
                    <div class="form-group" id="divSelectPelamar">
                        <label>Pelamar (ID Lamaran)</label>
                        <select id="formIdLamaran" name="id_lamaran" required>
                            <option value="">-- Pilih Pelamar --</option>
                            <?php foreach ($opsi_pelamar as $pelamar): ?>
                                <option value="<?php echo $pelamar['id_lamaran']; ?>">
                                    <?php echo htmlspecialchars($pelamar['nama_lengkap'] . " - " . $pelamar['posisi_dilamar']); ?> (ID: <?php echo $pelamar['id_lamaran']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" id="divNamaPelamar" style="display:none;">
                        <label>Nama Pelamar</label>
                        <input type="text" id="formNamaPelamarEdit" readonly style="background: #eee;">
                    </div>
                    <div class="form-group">
                        <label>Lokasi</label>
                        <input type="text" id="formLokasi" name="lokasi" placeholder="Online Zoom / Kantor" required>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea id="formCatatan" name="catatan" placeholder="Detail tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-body-right">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="formStatus" name="status_wawancara" required>
                            <option value="Terjadwal">Terjadwal</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" id="formTanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label>Jam</label>
                        <input type="time" id="formJam" name="jam" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-batal">Batal</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php 
// Inject JS khusus wawancara
$extra_js = '../assets/js/hrd_wawancara.js';
include '../templates/hrd_footer.php';
if(isset($koneksi)) $koneksi->close(); 
?>