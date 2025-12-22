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
            <input type="text" name="search" placeholder="Cari posisi atau status..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                    <g fill="#6a4e3b" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path d="M21,3c-9.37891,0 -17,7.62109 -17,17c0,9.37891 7.62109,17 17,17c3.71094,0 7.14063,-1.19531 9.9375,-3.21875l13.15625,13.125l2.8125,-2.8125l-13,-13.03125c2.55469,-2.97656 4.09375,-6.83984 4.09375,-11.0625c0,-9.37891 -7.62109,-17 -17,-17zM21,5c8.29688,0 15,6.70313 15,15c0,8.29688 -6.70312,15 -15,15c-8.29687,0 -15,-6.70312 -15,-15c0,-8.29687 6.70313,-15 15,-15z"></path>
                        </g>
                    </g>
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