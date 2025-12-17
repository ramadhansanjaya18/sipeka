<?php
include_once __DIR__ . '/config/init.php';
include_once 'templates/header.php';

if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'hrd') {
        header("Location: hrd/index.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

$error_message = "";!
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
        // Ambil data login dari tabel user (menggunakan username, bukan nama_lengkap)
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
                
                // Ambil Nama Lengkap dari tabel profil_pelamar untuk tampilan UI
                // Jika role bukan HRD (atau jika HRD juga punya profil di profil_pelamar)
                $stmt_profil = $koneksi->prepare("SELECT nama_lengkap FROM profil_pelamar WHERE id_user = ?");
                $stmt_profil->bind_param("i", $user['id_user']);
                $stmt_profil->execute();
                $res_profil = $stmt_profil->get_result();
                if ($res_profil->num_rows > 0) {
                    $profil = $res_profil->fetch_assoc();
                    $_SESSION['nama_lengkap'] = $profil['nama_lengkap'];
                } else {
                    // Fallback jika belum ada profil (misal HRD lama), pakai username
                    $_SESSION['nama_lengkap'] = $user['username'];
                }
                $stmt_profil->close();

                if ($user['role'] == 'hrd') {
                    header("Location: hrd/index.php");
                } else {
                    header("Location: index.php");
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
                    <div class="notification"><div class="message error"><?php echo htmlspecialchars($error_message); ?><span class="close-btn">&times;</span></div></div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="notification"><div class="message success"><?php echo htmlspecialchars($success_message); ?><span class="close-btn">&times;</span></div></div>
                <?php endif; ?>
                <?php if (empty($error_message) && isset($_GET['pesan'])): ?>
                    <div class="notification"><div class="message success"><?php echo htmlspecialchars($_GET['pesan']); ?><span class="close-btn">&times;</span></div></div>
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

                <p class="register-text">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                <button type="submit" class="btn-login-register">Login</button>
            </form>
        </div>
    </div>
</div>
<?php include_once 'templates/footer.php'; ?>
<script src="assets/js/auth.js"></script>