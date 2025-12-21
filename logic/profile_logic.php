<?php

if (!isset($koneksi) || !isset($_SESSION['id_user'])) {
    die("Akses ditolak. Silakan login.");
}

$id_pelamar = $_SESSION['id_user'];
$error_message = "";
$success_message = "";
$upload_dir_foto = 'uploads/foto_profil/';
$upload_dir_docs = 'uploads/dokumen/';
$placeholder_foto = 'assets/img/placeholder-profile.jpg';


$upload_configs = [
    'foto_profil' => [
        'db_column'   => 'foto_profil',
        'prefix'      => 'FOTO_',
        'allowed_ext' => ['jpg', 'jpeg', 'png'],
        'max_size_mb' => 2,
        'upload_dir'  => $upload_dir_foto,
        'label'       => 'Foto profil'
    ],
    'dokumen_cv' => [
        'db_column'   => 'dokumen_cv',
        'prefix'      => 'CV_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 5,
        'upload_dir'  => $upload_dir_docs,
        'label'       => 'CV'
    ],
    'surat_lamaran' => [
        'db_column'   => 'surat_lamaran',
        'prefix'      => 'SL_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 2,
        'upload_dir'  => $upload_dir_docs,
        'label'       => 'Surat lamaran'
    ],
    'sertifikat_pendukung' => [
        'db_column'   => 'sertifikat_pendukung',
        'prefix'      => 'SERTIFIKAT_',
        'allowed_ext' => ['pdf', 'zip'],
        'max_size_mb' => 10,
        'upload_dir'  => $upload_dir_docs,
        'label'       => 'Sertifikat'
    ],
    'ijasah' => [
        'db_column'   => 'ijasah',
        'prefix'      => 'IJASAH_',
        'allowed_ext' => ['pdf'],
        'max_size_mb' => 5,
        'upload_dir'  => $upload_dir_docs,
        'label'       => 'Ijasah'
    ]
];


function handleFileUpload($file, $id_pelamar, $current_profil_data, $config)
{
    global $koneksi;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'message' => ''];
        }
        return ['success' => false, 'message' => "Terjadi kesalahan saat mengunggah {$config['label']}. Kode: {$file['error']}"];
    }
  
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size_bytes = $config['max_size_mb'] * 1024 * 1024;

    if (!in_array($file_ext, $config['allowed_ext'])) {
        return ['success' => false, 'message' => "Format file {$config['label']} tidak valid. (Diizinkan: " . implode(', ', $config['allowed_ext']) . ")"];
    }

    if ($file['size'] > $max_size_bytes) {
        return ['success' => false, 'message' => "Ukuran file {$config['label']} terlalu besar (Max: {$config['max_size_mb']}MB)."];
    }
 
    if (!is_dir($config['upload_dir'])) {
        mkdir($config['upload_dir'], 0755, true);
    }
  
    $new_file_name = $config['prefix'] . $id_pelamar . '_' . uniqid() . '.' . $file_ext;
    $dest_path = $config['upload_dir'] . $new_file_name;

    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
        
        if ($current_profil_data && !empty($current_profil_data[$config['db_column']])) {
            $old_file = $current_profil_data[$config['db_column']];
            if (file_exists($config['upload_dir'] . $old_file)) {
                unlink($config['upload_dir'] . $old_file);
            }
        }

        $check = $koneksi->prepare("SELECT id_profil FROM profil_pelamar WHERE id_user = ?");
        $check->bind_param("i", $id_pelamar);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $stmt = $koneksi->prepare("UPDATE profil_pelamar SET {$config['db_column']} = ? WHERE id_user = ?");
            $stmt->bind_param("si", $new_file_name, $id_pelamar);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO profil_pelamar (id_user, {$config['db_column']}) VALUES (?, ?)");
            $stmt->bind_param("is", $id_pelamar, $new_file_name);
        }
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => "{$config['label']} berhasil diunggah."];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => "Gagal menyimpan data database."];
        }
    } else {
        return ['success' => false, 'message' => "Gagal memindahkan file ke folder tujuan."];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $redirect_url = "profil.php";
    $status_query = [];

    $stmt_curr = $koneksi->prepare("SELECT * FROM profil_pelamar WHERE id_user = ?");
    $stmt_curr->bind_param("i", $id_pelamar);
    $stmt_curr->execute();
    $current_profil_data = $stmt_curr->get_result()->fetch_assoc();
    $stmt_curr->close();
    
    if (isset($_POST['update_profil'])) {
        $username   = trim($_POST['username']);
        $nama       = trim($_POST['nama_lengkap']);
        $email      = trim($_POST['email']);
        $no_hp      = trim($_POST['no_telepon']);
        $alamat     = trim($_POST['alamat']);
        $ttl        = trim($_POST['tempat_tanggal_lahir']);
        $pendidikan = trim($_POST['riwayat_pendidikan']);
        $pengalaman = trim($_POST['pengalaman_kerja']);
        $keahlian   = trim($_POST['keahlian']);
        $ringkasan  = trim($_POST['ringkasan_pribadi']);

        if (empty($nama) || empty($email) || empty($username)) {
            $status_query['error'] = "Nama, Username, dan Email wajib diisi.";
        } else {
            $koneksi->begin_transaction();
            try {
                
                $stmt_user = $koneksi->prepare("UPDATE user SET email = ?, username = ? WHERE id_user = ?");
                $stmt_user->bind_param("ssi", $email, $username, $id_pelamar);
                $stmt_user->execute();
                $stmt_user->close();

                
                if ($current_profil_data) {
                    $sql_profil = "UPDATE profil_pelamar SET nama_lengkap=?, no_telepon=?, alamat=?, tempat_tanggal_lahir=?, riwayat_pendidikan=?, pengalaman_kerja=?, keahlian=?, ringkasan_pribadi=? WHERE id_user=?";
                    $stmt_p = $koneksi->prepare($sql_profil);
                    $stmt_p->bind_param("ssssssssi", $nama, $no_hp, $alamat, $ttl, $pendidikan, $pengalaman, $keahlian, $ringkasan, $id_pelamar);
                } else {
                    $sql_profil = "INSERT INTO profil_pelamar (id_user, nama_lengkap, no_telepon, alamat, tempat_tanggal_lahir, riwayat_pendidikan, pengalaman_kerja, keahlian, ringkasan_pribadi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_p = $koneksi->prepare($sql_profil);
                    $stmt_p->bind_param("issssssss", $id_pelamar, $nama, $no_hp, $alamat, $ttl, $pendidikan, $pengalaman, $keahlian, $ringkasan);
                }
                $stmt_p->execute();
                $stmt_p->close();

                $koneksi->commit();
                
                
                $_SESSION['nama_lengkap'] = $nama;
                if (isset($_SESSION['username'])) $_SESSION['username'] = $username;

                $status_query['success'] = "Profil berhasil diperbarui.";

            } catch (Exception $e) {
                $koneksi->rollback();
                $status_query['error'] = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }

    foreach ($upload_configs as $key => $config) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            $result = handleFileUpload($_FILES[$key], $id_pelamar, $current_profil_data, $config);
            
            if ($result['success']) {
                if (!empty($result['message'])) $status_query['success'] = $result['message'];
            } else {
                $status_query['error'] = $result['message'];
            }
            
            break; 
        }
    }
    
    if (!empty($status_query)) {
        $redirect_url .= "?" . http_build_query($status_query);
    }
    header("Location: " . $redirect_url);
    exit();
}

if (isset($_GET['success'])) $success_message = htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8');
if (isset($_GET['error'])) $error_message = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');

$sql_get = "SELECT u.id_user, u.email, u.username, p.* FROM user u 
            LEFT JOIN profil_pelamar p ON u.id_user = p.id_user 
            WHERE u.id_user = ?";
$stmt = $koneksi->prepare($sql_get);
$stmt->bind_param("i", $id_pelamar);
$stmt->execute();
$pelamar = $stmt->get_result()->fetch_assoc();
$stmt->close();


$foto_profil_path = $placeholder_foto;
if (!empty($pelamar['foto_profil']) && file_exists($upload_dir_foto . $pelamar['foto_profil'])) {
    $foto_profil_path = $upload_dir_foto . $pelamar['foto_profil'];
}
?>