<?php
require_once 'templates/header.php';
require_once 'logic/contact_process.php'; 
?>

<section class="contact-section">
    <div class="contact-header">
        <h1>Hubungi Kami</h1>
        <p>Ada pertanyaan atau komentar? Cukup tulis pesan kepada kami!</p>
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Informasi Kontak</h2>
            <p>Jika anda mempunyai pertanyaan, silakan hubungi kami:</p>
            <div class="contact-list">
                <div>
                    <img src="./assets/img/contact/phone.png" alt="icon-phone">
                    <span>0274 123456</span>
                </div>
                <div>
                    <img src="./assets/img/contact/email.png" alt="icon-email">
                    <span>hrdsyjuracoffe@gmail.com</span>
                </div>
                <div>
                    <img src="./assets/img/contact/location.png" alt="icon-location">
                    <span>Jl. Lohbener, Pamayahan</span>
                </div>
            </div>
        </div>

        <form class="contact-form" action="" method="post">
            
            <?php if (!empty($status_pesan)): ?>
                <div class="alert alert-<?php echo $tipe_pesan; ?>" id="notification">
                    <?php echo $status_pesan; ?>
                    <span class="closebtn" onclick="this.parentElement.style.opacity='0'; setTimeout(function(){ this.parentElement.style.display='none'; }, 600);">&times;</span>
                </div>
            <?php endif; ?>

            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Contoh: pengunjung@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>

            <button class="button-submit" type="submit">KIRIM PESAN</button>
        </form>
    </div>
</section>

<?php

$extra_js = 'assets/js/contact.js';
require_once 'templates/footer.php';
?>