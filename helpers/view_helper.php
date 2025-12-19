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
?>