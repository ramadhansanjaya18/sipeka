<?php
ob_start(); 
$page = 'pelamar';
include '../templates/hrd_header.php';
require_once '../logic/hrd_detail_pelamar_logic.php';
?>

<div class="page-title">
    <h1>Detail Pelamaran</h1>
    <?php if ($lamaran): ?>
        <p>Posisi: <strong><?php echo htmlspecialchars($lamaran['posisi_dilamar']); ?></strong></p>
    <?php endif; ?>
</div>

<div class="detail-container">
    <?php if (!empty($error_message)): ?>
        <div class="message animated error"><?php echo htmlspecialchars($error_message); ?></div>
        <a href="pelamar.php" class="btn-batal"><i class="fas fa-arrow-left"></i> Kembali</a>
    
    <?php elseif ($lamaran): ?>
        
        <div class="profile-card">
            <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto" class="profile-pic">
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($lamaran['nama_lengkap'] ?? 'Pelamar'); ?></h2>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($lamaran['email']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($lamaran['no_telepon'] ?? '-'); ?></p>
            </div>

            <div class="profile-actions">
                <form action="" method="POST" class="status-update-form">
                    <input type="hidden" name="id_lamaran" value="<?php echo $lamaran['id_lamaran']; ?>">
                    <select name="status" class="form-control">
                        <?php
                        foreach (['Diproses', 'Wawancara', 'Diterima', 'Ditolak'] as $st) {
                            $sel = ($lamaran['status_lamaran'] === $st) ? 'selected' : '';
                            echo "<option value='$st' $sel>$st</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-primary">
                        <i class="fas fa-sync-alt"></i> Update
                    </button>
                </form>

                <?php if (in_array($lamaran['status_lamaran'], ['Diproses', 'Wawancara'])): ?>
                    <a href="wawancara.php?id_lamaran=<?php echo $lamaran['id_lamaran']; ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-plus"></i> Jadwal Wawancara
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="additional-info">
            <h3>Informasi Pribadi</h3>
            <dl class="info-grid">
                <dt>Alamat</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['alamat'] ?? '-')); ?></dd>
                <dt>TTL</dt>
                <dd><?php echo htmlspecialchars($lamaran['tempat_tanggal_lahir'] ?? '-'); ?></dd>
                <dt>Pendidikan</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['riwayat_pendidikan'] ?? '-')); ?></dd>
                <dt>Pengalaman</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['pengalaman_kerja'] ?? '-')); ?></dd>
                <dt>Keahlian</dt>
                <dd><?php echo nl2br(htmlspecialchars($lamaran['keahlian'] ?? '-')); ?></dd>
            </dl>

            <h3 style="margin-top: 2rem;">Dokumen</h3>
            <dl class="info-grid">
                <?php
                $docs = [
                    'CV' => ['col' => 'dokumen_cv', 'label' => 'Lihat/Download Dokumen CV'],
                    'Surat Lamaran' => ['col' => 'surat_lamaran', 'label' => 'Lihat/Download Surat Lamaran'],
                    'Sertifikat' => ['col' => 'sertifikat_pendukung', 'label' => 'Lihat/Download Sertifikat'],
                    'Ijazah' => ['col' => 'ijasah', 'label' => 'Lihat/Download Ijazah']
                ];

                foreach ($docs as $key => $val) {
                    echo "<dt>$key</dt><dd>";
                    if (!empty($lamaran[$val['col']])) {
                        $link = $upload_dir_docs . $lamaran[$val['col']];
                        echo "<a href='" . htmlspecialchars($link) . "' target='_blank' class='btn-download'>";
                        echo "<i class='fas fa-file-pdf'></i> " . $val['label'];
                        echo "</a>";
                    } else {
                        echo "<span class='text-muted'>Tidak ada</span>";
                    }
                    echo "</dd>";
                }
                ?>
            </dl>
        </div>

        <br>
        <a href="pelamar.php" class="btn-batal"><i class="fas fa-arrow-left"></i> Kembali</a>

    <?php endif; ?>
</div>

<?php 
include '../templates/hrd_footer.php';
if(isset($koneksi)) $koneksi->close();
ob_end_flush(); 
?>