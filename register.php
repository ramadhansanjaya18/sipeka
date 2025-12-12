<?php
// =====================================================
// 1. Panggil file init.php (session & koneksi database)
// =====================================================
include_once __DIR__ . '/config/init.php';
include_once 'templates/header.php';

// =====================================================
// 2. Inisialisasi variabel pesan
// =====================================================
$error_message = "";
$success_message = "";

// =====================================================
// 3. Proses Form (POST)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Ambil data input
    $username = $koneksi->real_escape_string(trim($_POST['username'] ?? ''));
    $email = $koneksi->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // =====================================================
    // 4. Validasi Input Dasar
    // =====================================================
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
        // 5. Cek Ketersediaan Akun (LOGIKA DIPISAH)
        // =====================================================
        
        // A. Cek Email Terlebih Dahulu (Prioritas Utama)
        $stmt_email = $koneksi->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt_email->bind_param("s", $email);
        $stmt_email->execute();
        $res_email = $stmt_email->get_result();
        $stmt_email->close();

        if ($res_email->num_rows > 0) {
            // Jika email ditemukan di database
            $error_message = "email sudah digunakan"; 
        } else {
            // B. Jika Email Aman, Baru Cek Username
            $stmt_user = $koneksi->prepare("SELECT id_user FROM user WHERE username = ?");
            $stmt_user->bind_param("s", $username);
            $stmt_user->execute();
            $res_user = $stmt_user->get_result();
            $stmt_user->close();

            if ($res_user->num_rows > 0) {
                $error_message = "Username sudah terdaftar, silakan ganti.";
            } else {
                // =====================================================
                // 6. Proses Insert Data (Transaksi Database)
                // =====================================================
                $koneksi->begin_transaction();

                try {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $role = "pelamar";

                    // 1. Insert ke tabel user
                    $stmt_insert = $koneksi->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssss", $username, $email, $hashed_password, $role);
                    $stmt_insert->execute();
                    $new_user_id = $koneksi->insert_id; // Ambil ID baru
                    $stmt_insert->close();

                    // 2. Insert ke tabel profil_pelamar
                    $stmt_profil = $koneksi->prepare("INSERT INTO profil_pelamar (id_user, nama_lengkap) VALUES (?, ?)");
                    $stmt_profil->bind_param("is", $new_user_id, $username); 
                    $stmt_profil->execute();
                    $stmt_profil->close();

                    // Simpan perubahan ke database
                    $koneksi->commit();

                    // Set pesan sukses sesuai permintaan (Akan tampil Hijau)
                    $success_message = "pendaftaran akun telah berhasil";
                    
                    // (Opsional) Set session untuk pesan di halaman login nanti
                    $_SESSION['register_success'] = "Akun berhasil dibuat. Silakan login.";

                } catch (Exception $e) {
                    $koneksi->rollback();
                    $error_message = "Terjadi kesalahan sistem: " . $e->getMessage();
                }
            }
        }
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
                    <div class="notification" style="display: block;">
                        <div class="message error">
                            <?php echo htmlspecialchars($error_message); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)) : ?>
                    <div class="notification" style="display: block;">
                        <div class="message success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 3000); 
                    </script>
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