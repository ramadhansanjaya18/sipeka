<?php

function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

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

function menuActive($target_pages, $current_page) {
    if (is_array($target_pages)) {
        return in_array($current_page, $target_pages) ? 'active' : '';
    }
    return ($current_page == $target_pages) ? 'active' : '';
}

function displayHrdMessage() {
    if (isset($_SESSION['message'])) {
        $allowed_types = ['success', 'error', 'warning', 'info'];
        $type = in_array($_SESSION['message']['type'], $allowed_types) ? $_SESSION['message']['type'] : 'info';
        $text = $_SESSION['message']['text'];
        echo '<div class="message-container">';
        echo '    <div class="message animated ' . $type . '">';
        echo '        <button class="close-btn">&times;</button>';
        echo '        <p>' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '    </div>';
        echo '</div>';
        unset($_SESSION['message']);
    }
}
?>