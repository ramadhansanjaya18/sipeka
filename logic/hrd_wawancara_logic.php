<?php
/**
 * Logic: Manajemen Wawancara (HRD)
 */

require_once __DIR__ . '/../helpers/mail_helper.php';
if (!isset($koneksi)) require_once __DIR__ . '/../config/koneksi.php';

// --- CREATE & UPDATE & DELETE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    
    // 1. CREATE
    if ($_POST['action'] == 'create') {
        $id_lamaran = $_POST['id_lamaran'];
        $lokasi     = $_POST['lokasi'];
        $catatan    = $_POST['catatan'];
        $status     = $_POST['status_wawancara'];
        $tanggal    = $_POST['tanggal'];
        $jam        = $_POST['jam'];

        if (empty($id_lamaran) || empty($lokasi) || empty($tanggal) || empty($jam)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Data wajib diisi.'];
        } else {
            $jadwal = "$tanggal $jam";
            $koneksi->begin_transaction();
            try {
                // Cek Duplikat
                $cek = $koneksi->query("SELECT id_wawancara FROM wawancara WHERE id_lamaran = $id_lamaran");
                if ($cek->num_rows > 0) throw new Exception("Pelamar ini sudah punya jadwal.");

                // Data untuk Email (JOIN profil_pelamar)
                $q_user = "SELECT u.email, pp.nama_lengkap, l.posisi_dilamar 
                           FROM lamaran l 
                           JOIN user u ON l.id_pelamar = u.id_user 
                           JOIN profil_pelamar pp ON u.id_user = pp.id_user 
                           WHERE l.id_lamaran = $id_lamaran";
                $d_user = $koneksi->query($q_user)->fetch_assoc();

                // Insert
                $stmt = $koneksi->prepare("INSERT INTO wawancara (id_lamaran, jadwal, lokasi, status_wawancara, catatan) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $id_lamaran, $jadwal, $lokasi, $status, $catatan);
                $stmt->execute();
                $stmt->close();

                // Update Lamaran
                $koneksi->query("UPDATE lamaran SET status_lamaran = 'Wawancara' WHERE id_lamaran = $id_lamaran");
                $koneksi->commit();

                // Kirim Email
                $info = ['tanggal_indo' => date('d F Y', strtotime($tanggal)), 'jam' => $jam, 'lokasi' => $lokasi, 'catatan' => $catatan];
                $sent = kirimEmailUndanganWawancara($d_user['email'], $d_user['nama_lengkap'], $d_user['posisi_dilamar'], $info);
                
                $_SESSION['message'] = ['type' => 'success', 'text' => "Jadwal dibuat." . ($sent ? " Email terkirim." : "")];

            } catch (Exception $e) {
                $koneksi->rollback();
                $_SESSION['message'] = ['type' => 'error', 'text' => $e->getMessage()];
            }
        }
    }

    // 2. UPDATE
    if ($_POST['action'] == 'update') {
        $id_wawancara = $_POST['id_wawancara'];
        $lokasi       = $_POST['lokasi'];
        $catatan      = $_POST['catatan'];
        $status       = $_POST['status_wawancara'];
        $tanggal      = $_POST['tanggal'];
        $jam          = $_POST['jam'];
        $jadwal_baru  = "$tanggal $jam";

        // Ambil Data Lama
        $q_old = "SELECT w.jadwal, w.lokasi, u.email, pp.nama_lengkap, l.posisi_dilamar 
                  FROM wawancara w 
                  JOIN lamaran l ON w.id_lamaran = l.id_lamaran 
                  JOIN user u ON l.id_pelamar = u.id_user 
                  JOIN profil_pelamar pp ON u.id_user = pp.id_user
                  WHERE w.id_wawancara = $id_wawancara";
        $old = $koneksi->query($q_old)->fetch_assoc();

        $stmt = $koneksi->prepare("UPDATE wawancara SET jadwal = ?, lokasi = ?, status_wawancara = ?, catatan = ? WHERE id_wawancara = ?");
        $stmt->bind_param("ssssi", $jadwal_baru, $lokasi, $status, $catatan, $id_wawancara);
        
        if ($stmt->execute()) {
            // Cek Reschedule
            if ((strtotime($old['jadwal']) !== strtotime($jadwal_baru)) || ($old['lokasi'] !== $lokasi)) {
                $info = ['tanggal_indo' => date('d F Y', strtotime($tanggal)), 'jam' => $jam, 'lokasi' => $lokasi, 'catatan' => $catatan];
                kirimEmailRescheduleWawancara($old['email'], $old['nama_lengkap'], $old['posisi_dilamar'], $info);
            }
            $_SESSION['message'] = ['type' => 'success', 'text' => "Jadwal diperbarui."];
        }
        $stmt->close();
    }

    // 3. DELETE
    if ($_POST['action'] == 'delete') {
        $id = $_POST['id_wawancara'];
        $id_lam = $koneksi->query("SELECT id_lamaran FROM wawancara WHERE id_wawancara = $id")->fetch_assoc()['id_lamaran'];
        
        $koneksi->query("DELETE FROM wawancara WHERE id_wawancara = $id");
        $koneksi->query("UPDATE lamaran SET status_lamaran = 'Diproses' WHERE id_lamaran = $id_lam");
        
        $_SESSION['message'] = ['type' => 'success', 'text' => "Jadwal dihapus."];
    }
    
    header("Location: wawancara.php");
    exit();
}

// --- READ DATA ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT w.id_wawancara, w.id_lamaran, pp.nama_lengkap, l.posisi_dilamar,
            DATE_FORMAT(w.jadwal, '%d-%m-%Y') AS tanggal_formatted, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_formatted, 
            w.status_wawancara, w.lokasi, w.catatan,
            DATE_FORMAT(w.jadwal, '%Y-%m-%d') AS tanggal_raw,
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_raw
          FROM wawancara w
          JOIN lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN user u ON l.id_pelamar = u.id_user
          JOIN profil_pelamar pp ON u.id_user = pp.id_user";

if (!empty($search)) {
    $s = "%$search%";
    $query .= " WHERE pp.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? ORDER BY w.jadwal ASC";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ss", $s, $s);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $koneksi->query($query . " ORDER BY w.jadwal ASC");
}

// Dropdown Pelamar (JOIN profil_pelamar)
// UPDATE: Hanya menampilkan status 'Wawancara' yang belum memiliki jadwal
$q_list = "SELECT l.id_lamaran, pp.nama_lengkap, l.posisi_dilamar 
           FROM lamaran l 
           JOIN user u ON l.id_pelamar = u.id_user 
           JOIN profil_pelamar pp ON u.id_user = pp.id_user
           LEFT JOIN wawancara w ON l.id_lamaran = w.id_lamaran
           WHERE l.status_lamaran = 'Wawancara' AND w.id_wawancara IS NULL
           ORDER BY l.id_lamaran DESC";
$opsi_pelamar = $koneksi->query($q_list);
?>