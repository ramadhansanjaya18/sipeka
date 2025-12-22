<?php
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'hrd') {
        header("Location: hrd/index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

$error_message = "";
!
    $success_message = "";

if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan Password wajib diisi!";
    } else {
        $stmt = $koneksi->prepare("SELECT id_user, username, password, role FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                $stmt_profil = $koneksi->prepare("SELECT nama_lengkap FROM profil_pelamar WHERE id_user = ?");
                $stmt_profil->bind_param("i", $user['id_user']);
                $stmt_profil->execute();
                $res_profil = $stmt_profil->get_result();
                if ($res_profil->num_rows > 0) {
                    $profil = $res_profil->fetch_assoc();
                    $_SESSION['nama_lengkap'] = $profil['nama_lengkap'];
                } else {
                    $_SESSION['nama_lengkap'] = $user['username'];
                }
                $stmt_profil->close();

                if ($user['role'] == 'hrd') {
                    header("Location: hrd/index.php");
                } else {
                    header("Location: profil.php");
                }
                exit();
            } else {
                $error_message = "Email atau Password salah.";
            }
        } else {
            $error_message = "Email atau Password salah.";
        }
        $stmt->close();
    }
}
?>