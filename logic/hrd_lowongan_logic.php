<?php
/**
 * Logika Halaman Manajemen Lowongan (HRD)
 * Menangani CRUD: Create, Read, Update, Delete
 */

// Pastikan koneksi tersedia
if (!isset($koneksi)) require_once __DIR__ . '/../config/koneksi.php';

// --- PROSES CRUD (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // Ambil ID HRD dari session (jika ada, atau default ke 1 untuk dev)
    $id_hrd = $_SESSION['id_user'] ?? 1; 

    // 1. AKSI: CREATE (Tambah)
    if ($_POST['action'] == 'create') {
        $judul             = $_POST['judul'];
        $posisi            = $_POST['posisi_lowongan'];
        $deskripsi_singkat = $_POST['deskripsi_singkat'];
        $deskripsi         = $_POST['deskripsi'];
        $persyaratan       = $_POST['persyaratan'];
        $tgl_buka          = $_POST['tanggal_buka'];
        $tgl_tutup         = $_POST['tanggal_tutup'];

        $query = "INSERT INTO lowongan (id_hrd, judul, posisi_lowongan, deskripsi_singkat, deskripsi, persyaratan, tanggal_buka, tanggal_tutup) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("isssssss", $id_hrd, $judul, $posisi, $deskripsi_singkat, $deskripsi, $persyaratan, $tgl_buka, $tgl_tutup);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan baru berhasil ditambahkan.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal: " . $stmt->error];
        }
        $stmt->close();
        header("Location: lowongan.php");
        exit();
    }

    // 2. AKSI: UPDATE (Edit)
    if ($_POST['action'] == 'update') {
        $id_lowongan       = $_POST['id_lowongan'];
        $judul             = $_POST['judul'];
        $posisi            = $_POST['posisi_lowongan'];
        $deskripsi_singkat = $_POST['deskripsi_singkat'];
        $deskripsi         = $_POST['deskripsi'];
        $persyaratan       = $_POST['persyaratan'];
        $tgl_buka          = $_POST['tanggal_buka'];
        $tgl_tutup         = $_POST['tanggal_tutup'];

        $query = "UPDATE lowongan SET 
                    judul = ?, posisi_lowongan = ?, deskripsi_singkat = ?,
                    deskripsi = ?, persyaratan = ?, 
                    tanggal_buka = ?, tanggal_tutup = ? 
                  WHERE id_lowongan = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssssssi", $judul, $posisi, $deskripsi_singkat, $deskripsi, $persyaratan, $tgl_buka, $tgl_tutup, $id_lowongan);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan berhasil diperbarui.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal update: " . $stmt->error];
        }
        $stmt->close();
        header("Location: lowongan.php");
        exit();
    }

    // 3. AKSI: DELETE (Hapus)
    if ($_POST['action'] == 'delete') {
        $id_lowongan = $_POST['id_lowongan'];

        $koneksi->begin_transaction();
        try {
            // Hapus data terkait di wawancara & lamaran dulu (Relational Integrity)
            $stmt_check = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_lowongan = ?");
            $stmt_check->bind_param("i", $id_lowongan);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            
            $lamaran_ids = [];
            while ($row = $res_check->fetch_assoc()) $lamaran_ids[] = $row['id_lamaran'];
            $stmt_check->close();

            if (!empty($lamaran_ids)) {
                // Hapus Wawancara
                $placeholders = implode(',', array_fill(0, count($lamaran_ids), '?'));
                $stmt_del_w = $koneksi->prepare("DELETE FROM wawancara WHERE id_lamaran IN ($placeholders)");
                // Trik bind_param array dinamis
                $types = str_repeat('i', count($lamaran_ids));
                $stmt_del_w->bind_param($types, ...$lamaran_ids);
                $stmt_del_w->execute();
                $stmt_del_w->close();

                // Hapus Lamaran
                $stmt_del_l = $koneksi->prepare("DELETE FROM lamaran WHERE id_lowongan = ?");
                $stmt_del_l->bind_param("i", $id_lowongan);
                $stmt_del_l->execute();
                $stmt_del_l->close();
            }

            // Hapus Lowongan Utama
            $stmt_del = $koneksi->prepare("DELETE FROM lowongan WHERE id_lowongan = ?");
            $stmt_del->bind_param("i", $id_lowongan);
            $stmt_del->execute();

            if ($stmt_del->affected_rows > 0) {
                $koneksi->commit();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan dan data terkait berhasil dihapus.'];
            } else {
                throw new Exception("Data tidak ditemukan.");
            }
            $stmt_del->close();

        } catch (Exception $e) {
            $koneksi->rollback();
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal hapus: " . $e->getMessage()];
        }
        header("Location: lowongan.php");
        exit();
    }
}

// --- PROSES READ (GET) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT 
            id_lowongan, judul, posisi_lowongan, deskripsi_singkat, deskripsi, persyaratan, 
            DATE_FORMAT(tanggal_buka, '%d - %m - %Y') AS tgl_buka_formatted, 
            DATE_FORMAT(tanggal_tutup, '%d - %m - %Y') AS tgl_tutup_formatted,
            tanggal_buka, tanggal_tutup, 
            (CASE 
                WHEN CURDATE() BETWEEN tanggal_buka AND tanggal_tutup THEN 'Aktif'
                ELSE 'Tutup'
            END) AS status_lowongan_realtime
          FROM lowongan";

if (!empty($search)) {
    $search_param = "%{$search}%";
    $query .= " WHERE posisi_lowongan LIKE ? OR 
              (CASE WHEN CURDATE() BETWEEN tanggal_buka AND tanggal_tutup THEN 'Aktif' ELSE 'Tutup' END) LIKE ?";
    $query .= " ORDER BY tanggal_buka DESC";
    
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query .= " ORDER BY tanggal_buka DESC";
    $result = $koneksi->query($query);
}
?>