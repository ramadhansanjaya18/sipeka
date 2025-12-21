<?php

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $koneksi->real_escape_string(trim($_POST['username'] ?? ''));
    $email = $koneksi->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error_message = "Semua kolom wajib diisi!";
    } elseif (strlen($username) > 10) {
        $error_message = "Username maksimal 10 karakter!";
    } elseif ($password !== $konfirmasi_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
            
        $stmt_email = $koneksi->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt_email->bind_param("s", $email);
        $stmt_email->execute();
        $res_email = $stmt_email->get_result();
        $stmt_email->close();

        if ($res_email->num_rows > 0) {
            $error_message = "Email sudah digunakan"; 
        } else {
            
            $stmt_user = $koneksi->prepare("SELECT id_user FROM user WHERE username = ?");
            $stmt_user->bind_param("s", $username);
            $stmt_user->execute();
            $res_user = $stmt_user->get_result();
            $stmt_user->close();

            if ($res_user->num_rows > 0) {
                $error_message = "Username sudah terdaftar, silakan ganti.";
            } else {
                $koneksi->begin_transaction();
                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $role = "pelamar";
                    
                    $stmt_insert = $koneksi->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);
                    $stmt_insert->execute();
                    $new_user_id = $koneksi->insert_id;
                    $stmt_insert->close();
                    
                    $stmt_profil = $koneksi->prepare("INSERT INTO profil_pelamar (id_user, nama_lengkap) VALUES (?, ?)");
                    $stmt_profil->bind_param("is", $new_user_id, $username); 
                    $stmt_profil->execute();
                    $stmt_profil->close();
                    
                    $koneksi->commit();
                    $_SESSION['register_success'] = "Akun berhasil dibuat! Silakan login.";    
                    $koneksi->close();                                
                    header("Location: login.php");
                    exit(); 
                } catch (Exception $e) {
                    $koneksi->rollback();
                    $error_message = "Terjadi kesalahan sistem: " . $e->getMessage();
                }
            }
        }
    }
}
?>