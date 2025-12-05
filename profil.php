<?php
/**
 * Halaman Profil Pelamar.
 *
 * Pelamar dapat mengelola informasi pribadi, profesional, dan mengunggah
 * semua dokumen yang diperlukan untuk melamar pekerjaan (Foto, CV, dll).
 * File ini menerapkan pola Post-Redirect-Get (PRG) untuk menangani pemrosesan form.
 */

// 1. Inisialisasi dan Otentikasi
include_once 'templates/header.php';
include_once 'config/auth_pelamar.php';

// 2. Inisialisasi Variabel dan Konfigurasi Upload
$id_pelamar = $_SESSION['id_user'];
$error_message = "";
$success_message = "";
$upload_dir_foto = 'uploads/foto_profil/';
$upload_dir_docs = 'uploads/dokumen/';
$placeholder_foto = 'assets/img/placeholder-profile.png';

// Konfigurasi untuk setiap jenis file yang bisa di-upload
$upload_configs = [
    'foto_profil' => [
        'db_column' => 'foto_profil',
        'prefix' => 'FOTO_',
        'allowed_ext' => ['jpg', 'jpeg', 'png'],
        'max_size_mb' => 2,
        'upload_dir' => $upload_dir_foto,
        'label' => 'Foto profil'
    ],
    'dokumen_cv' => [
        'db_column' => 'dokumen_cv',
        'prefix' => 'CV_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 5,
        'upload_dir' => $upload_dir_docs,
        'label' => 'CV'
    ],
    'surat_lamaran' => [
        'db_column' => 'surat_lamaran',
        'prefix' => 'SL_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 2,
        'upload_dir' => $upload_dir_docs,
        'label' => 'Surat lamaran'
    ],
    'sertifikat_pendukung' => [
        'db_column' => 'sertifikat_pendukung',
        'prefix' => 'SERTIFIKAT_',
        'allowed_ext' => ['pdf', 'zip'],
        'max_size_mb' => 10,
        'upload_dir' => $upload_dir_docs,
        'label' => 'Sertifikat'
    ],
    'ijasah' => [
        'db_column' => 'ijasah',
        'prefix' => 'IJASAH_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 5,
        'upload_dir' => $upload_dir_docs,
        'label' => 'Ijasah'
    ]
];

/**
 * Fungsi helper untuk menangani proses upload file.
 *
 * @param array $file Data file dari superglobal $_FILES.
 * @param int $id_pelamar ID pengguna yang sedang login.
 * @param array $current_user_data Data pengguna saat ini untuk mendapatkan nama file lama.
 * @param array $config Konfigurasi upload yang spesifik untuk jenis file ini.
 * @return array Mengembalikan array berisi status 'success' (boolean) dan 'message' (string).
 */
function handleFileUpload($file, $id_pelamar, $current_user_data, $config)
{
    global $koneksi; // Akses variabel koneksi global

    // Daftar kolom yang diizinkan untuk diupdate, untuk mencegah SQL Injection pada nama kolom.
    $allowed_columns = ['foto_profil', 'dokumen_cv', 'surat_lamaran', 'sertifikat_pendukung', 'ijasah'];
    if (!in_array($config['db_column'], $allowed_columns)) {
        return ['success' => false, 'message' => 'Konfigurasi kolom database tidak valid.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE)
            return ['success' => true, 'message' => '']; // Bukan error, lewati saja
        return ['success' => false, 'message' => "Terjadi kesalahan saat mengunggah {$config['label']}. Kode: {$file['error']}"];
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size_bytes = $config['max_size_mb'] * 1024 * 1024;

    if (!in_array($file_ext, $config['allowed_ext'])) {
        return ['success' => false, 'message' => "Format file {$config['label']} tidak valid. Hanya " . implode(', ', $config['allowed_ext']) . " yang diizinkan."];
    }

    if ($file['size'] > $max_size_bytes) {
        return ['success' => false, 'message' => "Ukuran file {$config['label']} terlalu besar. Maksimal {$config['max_size_mb']} MB."];
    }

    if (!is_dir($config['upload_dir'])) {
        mkdir($config['upload_dir'], 0755, true);
    }

    $new_file_name = $config['prefix'] . $id_pelamar . '_' . uniqid() . '.' . $file_ext;
    $dest_path = $config['upload_dir'] . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        // Hapus file lama jika ada
        $old_file = $current_user_data[$config['db_column']];
        if (!empty($old_file) && file_exists($config['upload_dir'] . $old_file)) {
            unlink($config['upload_dir'] . $old_file);
        }

        // Update path file di database
        $stmt = $koneksi->prepare("UPDATE user SET {$config['db_column']} = ? WHERE id_user = ?");
        $stmt->bind_param("si", $new_file_name, $id_pelamar);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => "{$config['label']} berhasil diunggah."];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => "Gagal menyimpan path {$config['label']} ke database."];
        }
    } else {
        return ['success' => false, 'message' => "Gagal memindahkan file {$config['label']} ke folder tujuan."];
    }
}

// 3. --- BLOK PEMROSESAN FORM (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $redirect_url = "profil.php";
    $status_query = [];

    // Ambil data user saat ini untuk referensi (misal: nama file lama)
    $stmt_get_current = $koneksi->prepare("SELECT * FROM user WHERE id_user = ?");
    $stmt_get_current->bind_param("i", $id_pelamar);
    $stmt_get_current->execute();
    $current_user_data = $stmt_get_current->get_result()->fetch_assoc();
    $stmt_get_current->close();

    // A. Proses Update Profil Teks
    if (isset($_POST['update_profil'])) {
        $nama_lengkap = $_POST['nama_lengkap'];
        $email = $_POST['email'];
        if (empty($nama_lengkap) || empty($email)) {
            $status_query['error'] = "Nama Lengkap dan Email wajib diisi.";
        } else {
            $stmt_update = $koneksi->prepare("UPDATE user SET nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ?, tempat_tanggal_lahir = ?, riwayat_pendidikan = ?, pengalaman_kerja = ?, keahlian = ?, ringkasan_pribadi = ? WHERE id_user = ?");
            $stmt_update->bind_param("sssssssssi", $_POST['nama_lengkap'], $_POST['email'], $_POST['no_telepon'], $_POST['alamat'], $_POST['tempat_tanggal_lahir'], $_POST['riwayat_pendidikan'], $_POST['pengalaman_kerja'], $_POST['keahlian'], $_POST['ringkasan_pribadi'], $id_pelamar);
            if ($stmt_update->execute()) {
                $_SESSION['nama_lengkap'] = $nama_lengkap; // Update nama di session
                $status_query['success'] = "Profil berhasil diperbarui.";
            } else {
                $status_query['error'] = "Gagal memperbarui profil: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }

    // B. Proses Semua Upload File
    foreach ($upload_configs as $key => $config) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = handleFileUpload($_FILES[$key], $id_pelamar, $current_user_data, $config);
            if ($result['success']) {
                if (!empty($result['message']))
                    $status_query['success'] = $result['message'];
            } else {
                $status_query['error'] = $result['message'];
            }
            // Hentikan setelah satu proses agar tidak ada pesan ganda
            break;
        }
    }

    // C. Redirect dengan pesan status (Pola PRG)
    if (!empty($status_query)) {
        $redirect_url .= "?" . http_build_query($status_query);
    }
    header("Location: " . $redirect_url);
    exit();
}

// 4. --- BLOK PENGAMBILAN DATA (GET REQUEST) ---

// Ambil pesan dari URL jika ada (hasil dari redirect PRG)
if (isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8');
}
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
}

// Ambil data terbaru pelamar untuk ditampilkan
$stmt = $koneksi->prepare("SELECT * FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id_pelamar);
$stmt->execute();
$pelamar = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Tentukan path foto yang akan ditampilkan, gunakan placeholder jika tidak ada
$foto_profil_path = $placeholder_foto;
if (!empty($pelamar['foto_profil']) && file_exists($upload_dir_foto . $pelamar['foto_profil'])) {
    $foto_profil_path = $upload_dir_foto . $pelamar['foto_profil'];
}

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
                        <a href="logout.php" class="btn_logout"><img src="https://img.icons8.com/ios-filled/50/exit.png" alt=""> Logout</a>
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
                    <?php if ($key === 'foto_profil')
                        continue; // Foto sudah di atas, jadi lewati ?>
                    <div class="profile-card cv-card">
                        <h2><?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <form action="profil.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="<?php echo $key; ?>">Upload/Ganti <?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo strtoupper(implode('/', $config['allowed_ext'])); ?> - Max <?php echo $config['max_size_mb']; ?>MB)</label>
                                <input type="file" id="<?php echo $key; ?>" name="<?php echo $key; ?>" accept=".<?php echo implode(',.', $config['allowed_ext']); ?>">
                            </div>
                            <?php if ($key === 'sertifikat_pendukung'): ?>
                                    <p class="upload-instructions" style="font-size: 0.85rem; margin-bottom: 1rem;">*Jika sertifikat lebih dari satu, satukan dalam 1 file PDF atau ZIP.</p>
                            <?php endif; ?>
                            <button type="submit" class="btn">Upload <?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?></button>
                        </form>
                        <hr>
                        <h3><?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?> Terunggah</h3>
                        <div class="cv-status">
                            <?php if (!empty($pelamar[$config['db_column']])): ?>
                                    <p class="cv-exists">
                                        <span class="cv-icon"><img src="assets/img/profil/document.png" alt="icont-document"></span>
                                        <a href="<?php echo htmlspecialchars($config['upload_dir'] . $pelamar[$config['db_column']], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" title="Lihat <?php echo htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($pelamar[$config['db_column']], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </p>
                            <?php else: ?>
                                    <p class="cv-not-exists">Anda belum mengunggah <?php echo htmlspecialchars(strtolower($config['label']), ENT_QUOTES, 'UTF-8'); ?>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
// Koneksi sudah ditutup secara otomatis oleh PHP saat skrip berakhir, 
// namun menutupnya secara eksplisit di sini juga tidak masalah.
if ($koneksi) {
    $koneksi->close();
}
?>