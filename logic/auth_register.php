<?php

// Inisialisasi variabel pesan
$error_message = "";

// =====================================================
// 2. Proses Form (POST) - Ditaruh SEBELUM load Header
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ambil data input
    $username = $koneksi->real_escape_string(trim($_POST['username'] ?? ''));
    $email = $koneksi->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Validasi Input
    if (empty($username) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error_message = "Semua kolom wajib diisi!";
    } elseif (strlen($username) > 10) {
        $error_message = "Username maksimal 10 karakter!";
    } elseif ($password !== $konfirmasi_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        // Cek Ketersediaan Akun
        // A. Cek Email
        $stmt_email = $koneksi->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt_email->bind_param("s", $email);
        $stmt_email->execute();
        $res_email = $stmt_email->get_result();
        $stmt_email->close();

        if ($res_email->num_rows > 0) {
            $error_message = "Email sudah digunakan"; 
        } else {
            // B. Cek Username
            $stmt_user = $koneksi->prepare("SELECT id_user FROM user WHERE username = ?");
            $stmt_user->bind_param("s", $username);
            $stmt_user->execute();
            $res_user = $stmt_user->get_result();
            $stmt_user->close();

            if ($res_user->num_rows > 0) {
                $error_message = "Username sudah terdaftar, silakan ganti.";
            } else {
                // Proses Insert Data
                $koneksi->begin_transaction();

                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $role = "pelamar";

                    // 1. Insert ke tabel user
                    $stmt_insert = $koneksi->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);
                    $stmt_insert->execute();
                    $new_user_id = $koneksi->insert_id;
                    $stmt_insert->close();

                    // 2. Insert ke tabel profil_pelamar
                    $stmt_profil = $koneksi->prepare("INSERT INTO profil_pelamar (id_user, nama_lengkap) VALUES (?, ?)");
                    $stmt_profil->bind_param("is", $new_user_id, $username); 
                    $stmt_profil->execute();
                    $stmt_profil->close();

                    // Simpan perubahan ke database
                    $koneksi->commit();

                    // === KUNCI PERBAIKAN ===
                    // Set session sukses
                    $_SESSION['register_success'] = "Akun berhasil dibuat! Silakan login.";
                    
                    // Pastikan koneksi tutup sebelum redirect (opsional tapi baik)
                    $koneksi->close();

                    // Redirect ke Login
                    header("Location: login.php");
                    exit(); // Penting: Hentikan script di sini agar HTML di bawah tidak dimuat

                } catch (Exception $e) {
                    $koneksi->rollback();
                    $error_message = "Terjadi kesalahan sistem: " . $e->getMessage();
                }
            }
        }
    }
}
?>