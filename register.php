<?php
// =====================================================
// 1. Panggil file init.php (session & koneksi database)
// =====================================================
include_once __DIR__ . '/config/init.php';
include_once 'templates/header.php';

// =====================================================
// 2. Inisialisasi variabel untuk pesan
// =====================================================
$error_message = "";
$success_message = "";

// =====================================================
// 3. Cek apakah form dikirim (method POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ambil data input (Nama Lengkap dihapus)
    $username = $koneksi->real_escape_string(trim($_POST['username'] ?? ''));
    $email = $koneksi->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // =====================================================
    // 4. Validasi input
    // =====================================================
    // Cek empty dihapus untuk nama_lengkap
    if (empty($username) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error_message = "Semua kolom wajib diisi!";
    } elseif (strlen($username) > 10) {
        $error_message = "Username maksimal 10 karakter!";
    } elseif ($password !== $konfirmasi_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        // =====================================================
        // 5. Cek apakah email atau username sudah terdaftar
        // =====================================================
        $stmt_cek = $koneksi->prepare("SELECT id_user FROM user WHERE email = ? OR username = ?");
        $stmt_cek->bind_param("ss", $email, $username);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0) {
            $error_message = "Email atau Username sudah terdaftar.";
        } else {
            // =====================================================
            // 6. Lanjutkan registrasi (TRANSAKSI)
            // =====================================================
            $koneksi->begin_transaction();

            try {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = "pelamar";

                // A. Insert ke tabel USER (Data Login)
                $stmt_insert_user = $koneksi->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt_insert_user->bind_param("ssss", $username, $email, $hashed_password, $role);
                $stmt_insert_user->execute();
                
                // Ambil ID User yang baru dibuat
                $new_user_id = $koneksi->insert_id;
                $stmt_insert_user->close();

                // B. Insert ke tabel PROFIL_PELAMAR (Data Diri)
                // Karena input nama_lengkap dihapus, kita isi default dengan username agar query tidak error
                $stmt_insert_profil = $koneksi->prepare("INSERT INTO profil_pelamar (id_user, nama_lengkap) VALUES (?, ?)");
                $stmt_insert_profil->bind_param("is", $new_user_id, $username); 
                $stmt_insert_profil->execute();
                $stmt_insert_profil->close();

                // Jika kedua query berhasil, simpan perubahan
                $koneksi->commit();

                $success_message = "Registrasi berhasil! Anda akan diarahkan ke halaman login.";
                $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
                // header("refresh:3;url=login.php");

            } catch (Exception $e) {
                // Jika ada error, batalkan semua perubahan
                $koneksi->rollback();
                $error_message = "Registrasi gagal: " . $e->getMessage();
            }
        }
        $stmt_cek->close();
    }
    $koneksi->close();
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
            <form action="register.php" method="POST">
                <?php if (!empty($error_message)) : ?>
                    <div class="notification">
                        <div class="message error">
                            <?php echo htmlspecialchars($error_message); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)) : ?>
                    <div class="notification">
                        <div class="message success">
                            <?php echo htmlspecialchars($success_message); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <fieldset <?php if (!empty($success_message)) echo 'disabled'; ?>>
                    
                    <label for="username">Username</label>
                    <div class="input-group">
                        <img src="assets/img/auth/person_icon.png" alt="icon_user">
                        <input type="text" id="username" name="username" maxlength="10" 
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>

                    <label for="email">Email</label>
                    <div class="input-group">
                        <img src="assets/img/auth/person_icon.png" alt="icon_email">
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <label for="password">Password</label>
                    <div class="input-group">
                        <img src="assets/img/auth/lock_icon.png" alt="icon_password">
                        <input type="password" id="password" name="password" required>
                    </div>

                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <div class="input-group">
                        <img src="assets/img/auth/lock_icon.png" alt="icon_konfirmasi_password">
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                    </div>

                    <p class="register-text">
                        Sudah punya akun? <a href="login.php">Login di sini</a>
                    </p>

                    <button type="submit" class="btn-login-register">Daftar</button>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<?php
include_once 'templates/footer.php';
?>
<script src="assets/js/auth.js"></script>