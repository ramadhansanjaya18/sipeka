<?php
include_once __DIR__ . '/../config/init.php';
include_once __DIR__ . '/../config/auth_hrd.php';
include_once __DIR__ . '/../helpers/view_helper.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRD Dashboard - SIPEKA</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="../assets/css/hrd/base.css">
    <link rel="stylesheet" href="../assets/css/hrd/layout.css">
    <link rel="stylesheet" href="../assets/css/hrd/components.css">
    <link rel="stylesheet" href="../assets/css/hrd/pages.css">
    <link rel="stylesheet" href="../assets/css/hrd/responsive.css">
    <link rel="stylesheet" href="../assets/css/hrd/hrd.css"> <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/hrd.js"></script>
</head>
<body>
    <header class="top-bar">
        <button class="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo-wrapper" style="display:flex; align-items:center; gap:10px;">
            <img src="../assets/img/logo.png" alt="logo" style="height:40px;">
            <h4 style="margin:0;">SYJURA COFFEE</h4>
        </div>
    </header>

    <div class="sidebar-overlay"></div>
    
    <div class="hrd-wrapper">
        <?php include_once 'hrd_sidebar.php'; ?>
        <div class="main-content">
            <main class="content-area">
                <?php if (function_exists('displayHrdMessage')) displayHrdMessage(); ?>