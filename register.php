<?php
require_once 'logic/auth_register.php';
require_once 'templates/header.php';
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

                <fieldset>
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