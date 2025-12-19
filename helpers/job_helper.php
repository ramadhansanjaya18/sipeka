<?php
/**
 * Helper khusus untuk logika yang berkaitan dengan Lowongan/Pekerjaan.
 */

function getJobIcon($posisi) {
    $posisi_lower = strtolower($posisi);
    $base_path = 'assets/img/beranda/';
    
    // Mapping kata kunci ke nama file gambar
    $icon_map = [
        'barista' => 'icon_barista.png',
        'waiter' => 'icon_waiter.png',
        'pelayan' => 'icon_waiter.png',
        'kitchen' => 'icon_kitchen_staff.png',
        'dapur' => 'icon_kitchen_staff.png',
        'cook' => 'icon_kitchen_staff.png',
        'kasir' => 'icon_accounting.png',
        'cashier' => 'icon_accounting.png',
        'finance' => 'icon_accounting.png',
        'accounting' => 'icon_accounting.png',
        'cleaning' => 'icon_cleaning_services.png',
        'supervisor' => 'icon_supervisor.png'
    ];

    foreach ($icon_map as $keyword => $filename) {
        if (strpos($posisi_lower, $keyword) !== false) {
            return $base_path . $filename;
        }
    }
    
    return $base_path . 'icon_default.png';
}
?>