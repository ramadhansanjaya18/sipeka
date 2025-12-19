<?php
require_once 'templates/header.php';
require_once 'logic/auth_login.php';
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