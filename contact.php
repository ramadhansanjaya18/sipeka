<?php
include_once 'templates/header.php';
?>

<section class="contact-section">
    <div class="contact-header">
        <h1>Hubungi Kami</h1>
        <p>Ada pertanyaan atau komentar? Cukup tulis pesan kepada kami!</p>
    </div>

    <div class="contact-container">
        <div class="contact-info">
            <h2>Informasi Kontak</h2>
            <p>Jika anda mempunyai pertanyaan atau kekhawatiran. Anda dapat menghubungi kami dengan mengisi formulir
                kontak, menelepon kami, datang ke kantor kami, menemukan kami di jejaring sosial lain, atau anda dapat
                mengirim email pribadi kepada kami di:</p>

            <div class="contact-list">
                <div><img src="./assets/img/contact/phone.png" alt="icon-phone"><span>0274 123456</span></div>
                <div><img src="./assets/img/contact/email.png" alt="icon-email"><span>hrdsyjuracoffe@gmail.com</span>
                </div>
                <div><img src="./assets/img/contact/location.png" alt="icon-location"><span>Jl. Lohbener,
                        Pamayahan</span></div>
            </div>
        </div>

        <form class="contact-form" action="#" method="post">
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button class="button-submit" type="submit">KIRIM</button>
        </form>
    </div>
</section>

<?php
include_once 'templates/footer.php';
?>