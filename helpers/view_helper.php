<?php
/**
 * Helper untuk Logika Tampilan (View)
 * Berisi fungsi-fungsi untuk mendeteksi halaman aktif, mapping CSS, dll.
 */

/**
 * Mendapatkan nama file halaman saat ini.
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Mendapatkan path CSS spesifik berdasarkan halaman yang sedang dibuka.
 */
function getPageCss($current_page) {
    $css_map = [
        'login.php'          => 'assets/css/auth.css',
        'register.php'       => 'assets/css/auth.css',
        'index.php'          => 'assets/css/style.css',
        'about.php'          => 'assets/css/about.css',
        'job.php'            => 'assets/css/job.css',
        'job_detail.php'     => 'assets/css/job.css',
        'contact.php'        => 'assets/css/contact.css',
        'profil.php'         => 'assets/css/profil.css',
        'status_lamaran.php' => 'assets/css/status.css'
    ];

    return $css_map[$current_page] ?? null;
}

/**
 * Mengecek apakah menu harus aktif.
 * Menerima string (satu halaman) atau array (beberapa halaman).
 */
function menuActive($target_pages, $current_page) {
    if (is_array($target_pages)) {
        return in_array($current_page, $target_pages) ? 'active' : '';
    }
    return ($current_page == $target_pages) ? 'active' : '';
}

/**
 * Menampilkan Flash Message khusus halaman HRD.
 * (Disisipkan untuk menangani notifikasi HRD)
 */
function displayHrdMessage() {
    if (isset($_SESSION['message'])) {
        // Validasi tipe pesan (success, error, warning, info)
        $allowed_types = ['success', 'error', 'warning', 'info'];
        $type = in_array($_SESSION['message']['type'], $allowed_types) ? $_SESSION['message']['type'] : 'info';
        $text = $_SESSION['message']['text'];

        // Output HTML yang sesuai dengan selector di hrd.js (.message.animated)
        echo '<div class="message-container">';
        echo '    <div class="message animated ' . $type . '">';
        echo '        <button class="close-btn">&times;</button>';
        echo '        <p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '    </div>';
        echo '</div>';

        // Hapus pesan agar tidak muncul lagi saat refresh
        unset($_SESSION['message']);
    }
}
?>