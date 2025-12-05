<?php


// 1. Memanggil init.php (yang sudah memulai session dan koneksi database)
include_once __DIR__ . '/config/init.php';
include_once 'templates/header.php';

// 2. Jika pengguna sudah login, arahkan ke halaman yang sesuai untuk mencegah login ulang.
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
$success_message = "";

// 3. Menampilkan pesan sukses dari registrasi (Flash Message).
if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    // Hapus session agar pesan tidak muncul lagi setelah di-refresh.
    unset($_SESSION['register_success']);
}

// 4. Cek apakah form telah disubmit (method POST).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form (real_escape_string tidak perlu karena menggunakan prepared statement).
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan Password wajib diisi!";
    } else {
        // Query untuk mencari user berdasarkan email.
        $stmt = $koneksi->prepare("SELECT id_user, nama_lengkap, password, role FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password yang di-hash.
            if (password_verify($password, $user['password'])) {
                // Password cocok, buat session.
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];

                // Arahkan (redirect) berdasarkan peran (role).
                if ($user['role'] == 'hrd') {
                    header("Location: hrd/index.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                // Password tidak cocok.
                $error_message = "Email atau Password yang Anda masukkan salah.";
            }
        } else {
            // User tidak ditemukan.
            $error_message = "Email atau Password yang Anda masukkan salah.";
        }
        $stmt->close();
    }
}
?>
<div class="container">
    <div class="left-side">
        <div class="logo-box">
            <img src="assets/img/logo.png" alt="Syjura Coffee" class="logo">
            <h2>SYJURA COFFEE</h2>
        </div>
    </div>

    <div class="right-side">
        <div class="login-box">
            <form action="login.php" method="POST">
                <?php if (!empty($error_message)): ?>
                    <div class="notification">
                        <div class="message error">
                            <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="notification">
                        <div class="message success">
                            <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (empty($error_message) && isset($_GET['pesan'])): ?>
                    <div class="notification">
                        <div class="message success">
                            <?php echo htmlspecialchars($_GET['pesan'], ENT_QUOTES, 'UTF-8'); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <label>Email</label>
                <div class="input-group">
                    <img src="assets/img/auth/person_icon.png" alt="icon_email">
                    <input type="email" name="email" placeholder="Masukkan Email" required>
                </div>

                <label>Password</label>
                <div class="input-group">
                    <img src="assets/img/auth/lock_icon.png" alt="icon_password">
                    <input type="password" name="password" placeholder="Masukkan Password" required>
                </div>

                <p class="register-text">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </p>

                <button type="submit" class="btn-login-register">Login</button>
            </form>
        </div>
    </div>
</div>

<?php
// Panggil Footer
include_once 'templates/footer.php';

// Tutup koneksi
if (isset($koneksi) && $koneksi) {
    $koneksi->close();
}
?>
<script src="assets/js/auth.js"></script>