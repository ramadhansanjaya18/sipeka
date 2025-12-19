<?php
/**
 * Halaman View Profil Pelamar
 */
require_once 'templates/header.php';
require_once 'config/auth_pelamar.php';
require_once 'logic/profile_logic.php'; // Memuat logika utama
?>

<div class="profile-container">
    <h1>Profil Saya</h1>
    <p>Kelola informasi profil dan dokumen Anda untuk kemudahan dalam melamar.</p>

    <?php if ($error_message): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if ($success_message): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        
        <div class="profile-card form-card">
            <h2>Informasi Pribadi & Profesional</h2>
            <form action="profil.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($pelamar['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($pelamar['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($pelamar['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="no_telepon">No. Telepon</label>
                    <input type="tel" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($pelamar['no_telepon'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="tempat_tanggal_lahir">Tempat, Tanggal Lahir</label>
                    <input type="text" id="tempat_tanggal_lahir" name="tempat_tanggal_lahir" placeholder="Contoh: Jakarta, 17 Agustus 1990" value="<?php echo htmlspecialchars($pelamar['tempat_tanggal_lahir'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($pelamar['alamat'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="ringkasan_pribadi">Ringkasan Pribadi</label>
                    <textarea id="ringkasan_pribadi" name="ringkasan_pribadi" rows="4" placeholder="Contoh: Fresh graduate dengan semangat tinggi..."><?php echo htmlspecialchars($pelamar['ringkasan_pribadi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="riwayat_pendidikan">Riwayat Pendidikan</label>
                    <textarea id="riwayat_pendidikan" name="riwayat_pendidikan" rows="4" placeholder="Contoh: S1 Teknik Informatika, Universitas ABC (2018-2022)"><?php echo htmlspecialchars($pelamar['riwayat_pendidikan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="pengalaman_kerja">Pengalaman Kerja</label>
                    <textarea id="pengalaman_kerja" name="pengalaman_kerja" rows="4" placeholder="Contoh: Barista, Kopi Kenangan (2022-2023)"><?php echo htmlspecialchars($pelamar['pengalaman_kerja'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="keahlian">Keahlian</label>
                    <textarea id="keahlian" name="keahlian" rows="3" placeholder="Contoh: Latte Art, Customer Service, Microsoft Office"><?php echo htmlspecialchars($pelamar['keahlian'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                
                <button type="submit" name="update_profil" class="btn_apply">Simpan Perubahan</button>
                <a href="logout.php" class="btn_logout">
                    <img src="https://img.icons8.com/ios-filled/50/exit.png" alt="" style="width:16px; margin-right:5px;"> Logout
                </a>
            </form>
        </div>

        <div class="right-column">
            
            <div class="profile-card photo-card">
                <h2>Foto Profil</h2>
                <img src="<?php echo htmlspecialchars($foto_profil_path, ENT_QUOTES, 'UTF-8'); ?>?t=<?php echo time(); ?>" alt="Foto Profil" class="profile-picture-preview">
                
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="foto_profil">Ganti Foto (JPG, PNG - Max 2MB)</label>
                        <input type="file" id="foto_profil" name="foto_profil" accept=".jpg, .jpeg, .png">
                    </div>
                    <button type="submit" class="btn">Upload Foto</button>
                </form>
            </div>

            <?php foreach ($upload_configs as $key => $config): ?>
                <?php if ($key === 'foto_profil') continue; // Foto sudah di atas ?>
                
                <div class="profile-card cv-card">
                    <h2><?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    
                    <form action="profil.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="<?php echo $key; ?>">
                                Upload/Ganti <?php echo htmlspecialchars($config['label']); ?> <br>
                                <small>(<?php echo strtoupper(implode('/', $config['allowed_ext'])); ?> - Max <?php echo $config['max_size_mb']; ?>MB)</small>
                            </label>
                            <input type="file" id="<?php echo $key; ?>" name="<?php echo $key; ?>" accept=".<?php echo implode(',.', $config['allowed_ext']); ?>">
                        </div>

                        <?php if ($key === 'sertifikat_pendukung'): ?>
                            <p class="upload-instructions" style="font-size: 0.85rem; margin-bottom: 1rem; color:#666;">
                                *Jika sertifikat lebih dari satu, satukan dalam 1 file PDF atau ZIP.
                            </p>
                        <?php endif; ?>

                        <button type="submit" class="btn">Upload <?php echo htmlspecialchars($config['label']); ?></button>
                    </form>
                    
                    <hr>
                    
                    <h3>File Terunggah</h3>
                    <div class="cv-status">
                        <?php if (!empty($pelamar[$config['db_column']])): ?>
                            <p class="cv-exists">
                                <span class="cv-icon"><img src="assets/img/profil/document.png" alt="icon-document"></span>
                                <a href="<?php echo htmlspecialchars($config['upload_dir'] . $pelamar[$config['db_column']], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" title="Lihat File">
                                    <?php echo htmlspecialchars($pelamar[$config['db_column']], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <p class="cv-not-exists">Anda belum mengunggah file ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<?php
require_once 'templates/footer.php';
// Tutup koneksi jika diperlukan
if (isset($koneksi) && $koneksi) $koneksi->close();
?>