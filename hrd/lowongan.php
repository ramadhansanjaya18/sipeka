<?php
$page = 'lowongan';
include '../templates/hrd_header.php';
require_once '../logic/hrd_lowongan_logic.php';
?>

<div class="page-title">
    <h1>Manajemen Data Lowongan Pekerjaan</h1>
    <p>Kelola lowongan pekerjaan yang tersedia di Syjura Coffee.</p>
</div>

<div class="page-actions">
    <button class="btn-primary" id="btnTambahLowongan">+ Tambahkan Lowongan</button>
    <div class="search-container">
        <form action="lowongan.php" method="GET">
            <input type="text" name="search" placeholder="Cari posisi atau status..." 
                   value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button">
                <svg xmlns="http:
                    <path d="M 21 3 C 11.621094 3 4 10.621094 4 20 C 4 29.378906 11.621094 37 21 37 C 24.710938 37 28.140625 35.804688 30.9375 33.78125 L 44.09375 46.90625 L 46.90625 44.09375 L 33.90625 31.0625 C 36.460938 28.085938 38 24.222656 38 20 C 38 10.621094 30.378906 3 21 3 Z M 21 5 C 29.296875 5 36 11.703125 36 20 C 36 28.296875 29.296875 35 21 35 C 12.703125 35 6 28.296875 6 20 C 6 11.703125 12.703125 5 21 5 Z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Posisi</th>
                <th class="col-status">Status (Real-time)</th>
                <th>Tanggal Buka</th>
                <th>Tanggal Tutup</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php
                    $status_class = $row['status_lowongan_realtime'] == 'Aktif' ? 'status-aktif' : 'status-ditutup';
                    ?>
                    <tr>
                        <td data-label="Posisi"><?php echo htmlspecialchars($row['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td data-label="Status" class="col-status">
                            <span class="<?php echo htmlspecialchars($status_class); ?>" style="font-weight: bold;">
                                <?php echo htmlspecialchars($row['status_lowongan_realtime']); ?>
                            </span>
                        </td>

                        <td data-label="Tanggal Buka"><?php echo htmlspecialchars($row['tgl_buka_formatted']); ?></td>
                        <td data-label="Tanggal Tutup"><?php echo htmlspecialchars($row['tgl_tutup_formatted']); ?></td>
                        
                        <td data-label="Aksi" class="col-aksi action-buttons">
                            <button class="btn-edit" 
                                data-id="<?php echo $row['id_lowongan']; ?>" 
                                data-judul="<?php echo htmlspecialchars($row['judul']); ?>" 
                                data-posisi="<?php echo htmlspecialchars($row['posisi_lowongan']); ?>" 
                                data-deskripsi_singkat="<?php echo htmlspecialchars($row['deskripsi_singkat']); ?>"
                                data-deskripsi="<?php echo htmlspecialchars($row['deskripsi']); ?>" 
                                data-persyaratan="<?php echo htmlspecialchars($row['persyaratan']); ?>" 
                                data-tgl_buka="<?php echo $row['tanggal_buka']; ?>" 
                                data-tgl_tutup="<?php echo $row['tanggal_tutup']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="lowongan.php" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus lowongan juga akan menghapus SEMUA data lamaran dan jadwal wawancara yang terkait. Lanjutkan?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_lowongan" value="<?php echo $row['id_lowongan']; ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        <?php echo !empty($search) ? 'Tidak ada data ditemukan.' : 'Belum ada data lowongan.'; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalLowongan" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Lowongan Baru</h2>
            <span class="modal-close">&times;</span>
        </div>

        <form id="formLowongan" action="lowongan.php" method="POST">
            <div class="modal-body">
                <div class="modal-body-left">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id_lowongan" id="formIdLowongan">

                    <div class="form-group">
                        <label for="formJudul">Judul Lowongan</label>
                        <input type="text" id="formJudul" name="judul" placeholder="Masukan Judul Lowongan" required>
                    </div>

                    <div class="form-group">
                        <label for="formPosisi">Posisi Lowongan</label>
                        <input type="text" id="formPosisi" name="posisi_lowongan" placeholder="Masukan Posisi" required>
                    </div>

                    <div class="form-group">
                        <label for="formDeskripsiSingkat">Deskripsi Singkat</label>
                        <input type="text" id="formDeskripsiSingkat" name="deskripsi_singkat" placeholder="Ringkasan singkat (Max 255 char)" required>
                    </div>

                    <div class="form-group">
                        <label for="formDeskripsi">Deskripsi Lengkap</label>
                        <textarea id="formDeskripsi" name="deskripsi" placeholder="Deskripsikan Lowongan..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formPersyaratan">Persyaratan</label>
                        <textarea id="formPersyaratan" name="persyaratan" placeholder="Jelaskan Persyaratan..."></textarea>
                    </div>
                </div>

                <div class="modal-body-right">
                    <div class="form-group">
                        <label for="formTglBuka">Tanggal Buka</label>
                        <input type="date" id="formTglBuka" name="tanggal_buka" required>
                    </div>

                    <div class="form-group">
                        <label for="formTglTutup">Tanggal Tutup</label>
                        <input type="date" id="formTglTutup" name="tanggal_tutup" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" value="Otomatis (berdasarkan tanggal)" disabled style="background-color: #eee;">
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

$extra_js = '../assets/js/hrd_lowongan.js';
include '../templates/hrd_footer.php';

if (isset($koneksi)) $koneksi->close();
?>