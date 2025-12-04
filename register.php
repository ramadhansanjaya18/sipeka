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

    // Gunakan operator ?? untuk mencegah undefined array key
    $nama_lengkap = $koneksi->real_escape_string(trim($_POST['nama_lengkap'] ?? ''));
    $email = $koneksi->real_escape_string(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // =====================================================
    // 4. Validasi input sederhana
    // =====================================================
    if (empty($nama_lengkap) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error_message = "Semua kolom wajib diisi!";
    } elseif ($password !== $konfirmasi_password) {
        $error_message = "Password dan Konfirmasi Password tidak cocok!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } else {
        // =====================================================
        // 5. Cek apakah email sudah terdaftar
        // =====================================================
        $stmt_cek = $koneksi->prepare("SELECT email FROM user WHERE email = ?");
        $stmt_cek->bind_param("s", $email);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();

        if ($result_cek->num_rows > 0) {
            $error_message = "Email yang Anda masukkan sudah terdaftar.";
        } else {
            // =====================================================
            // 6. Lanjutkan registrasi
            // =====================================================
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "pelamar"; // Role default

            $stmt_insert = $koneksi->prepare(
                "INSERT INTO user (nama_lengkap, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt_insert->bind_param("ssss", $nama_lengkap, $email, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                $success_message = "Registrasi berhasil! Anda akan diarahkan ke halaman login dalam 3 detik.";
                $_SESSION['register_success'] = "Registrasi berhasil! Silakan login dengan akun Anda.";
                // header("refresh:3;url=login.php");
            } else {
                $error_message = "Registrasi gagal. Silakan coba lagi. Error: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt_cek->close();
    }

    // Tutup koneksi setelah selesai
    $koneksi->close();
}
?>

<!-- =====================================================
     7. Tampilan Form Register (HTML + PHP)
===================================================== -->
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

                <!-- Pesan Error -->
                <?php if (!empty($error_message)) : ?>
                    <div class="notification">
                        <div class="message error">
                            <?php echo htmlspecialchars($error_message); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Pesan Sukses -->
                <?php if (!empty($success_message)) : ?>
                    <div class="notification">
                        <div class="message success">
                            <?php echo htmlspecialchars($success_message); ?>
                            <span class="close-btn">&times;</span>
                        </div>
                    </div>
                <?php endif; ?>

                <fieldset <?php if (!empty($success_message)) echo 'disabled'; ?>>
                    <!-- Nama Lengkap -->
                    <label for="nama_lengkap">Nama Lengkap</label>
                    <div class="input-group">
                        <img src="assets/img/auth/person_icon.png" alt="icon_nama">
                        <input type="text" id="nama_lengkap" name="nama_lengkap"
                            value="<?php echo htmlspecialchars($_POST['nama_lengkap'] ?? ''); ?>" required>
                    </div>

                    <!-- Email -->
                    <label for="email">Email</label>
                    <div class="input-group">
                        <img src="assets/img/auth/person_icon.png" alt="icon_email">
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <!-- Password -->
                    <label for="password">Password</label>
                    <div class="input-group">
                        <img src="assets/img/auth/lock_icon.png" alt="icon_password">
                        <input type="password" id="password" name="password" required>
                    </div>

                    <!-- Konfirmasi Password -->
                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <div class="input-group">
                        <img src="assets/img/auth/lock_icon.png" alt="icon_konfirmasi_password">
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password" required>
                    </div>

                    <!-- Link ke Login -->
                    <p class="register-text">
                        Sudah punya akun? <a href="login.php">Login di sini</a>
                    </p>

                    <!-- Tombol Daftar -->
                    <button type="submit" class="btn-login-register">Daftar</button>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<?php
// =====================================================
// 8. Panggil Footer dan tutup koneksi jika belum tertutup
// =====================================================
include_once 'templates/footer.php';

if (isset($koneksi) && $koneksi instanceof mysqli) {
    $koneksi->close();
}
?>
<script src="assets/js/auth.js"></script>