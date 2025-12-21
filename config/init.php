<?php
date_default_timezone_set("Asia/Jakarta");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/koneksi.php';
