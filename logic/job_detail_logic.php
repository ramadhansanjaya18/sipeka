<?php
/**
 * Logika Detail Lowongan & Validasi Pelamar
 */

$lowongan = null;
$error_message = "";

// Variabel status pelamar
$user_has_applied = false;
$profile_is_complete = false;
$missing_fields = []; 
$status_lowongan_aktif = false;

// 1. Validasi ID dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $error_message = "ID lowongan tidak valid atau tidak ditemukan.";
} else {
    $id_lowongan = (int) $_GET['id'];

    // 2. Query Detail Lowongan
    $query = "SELECT 
                l.id_lowongan, l.judul, l.posisi_lowongan, l.deskripsi, l.persyaratan, 
                DATE_FORMAT(l.tanggal_buka, '%d %M %Y') AS tgl_buka_formatted, 
                DATE_FORMAT(l.tanggal_tutup, '%d %M %Y') AS tgl_tutup_formatted,
                (CASE 
                    WHEN CURDATE() BETWEEN l.tanggal_buka AND l.tanggal_tutup THEN 'Aktif'
                    ELSE 'Tutup'
                END) AS status_lowongan_realtime,
                u.username AS nama_hrd
            FROM lowongan l
            JOIN user u ON l.id_hrd = u.id_user
            WHERE l.id_lowongan = ?";

    $stmt = $koneksi->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $id_lowongan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error_message = "Detail lowongan tidak ditemukan atau sudah tidak tersedia.";
        } else {
            $lowongan = $result->fetch_assoc();
            $status_lowongan_aktif = ($lowongan['status_lowongan_realtime'] == 'Aktif');
        }
        $stmt->close();
    }
}

// 3. Logika Validasi Pelamar (Hanya jika user login & lowongan ditemukan)
if (!$error_message && isset($_SESSION['id_user']) && $_SESSION['role'] == 'pelamar') {
    $id_pelamar = $_SESSION['id_user'];

    // A. Cek apakah sudah pernah melamar di lowongan ini
    $stmt_check = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_pelamar = ? AND id_lowongan = ?");
    $stmt_check->bind_param("ii", $id_pelamar, $id_lowongan);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        $user_has_applied = true;
    }
    $stmt_check->close();

    // B. Ambil Data Profil untuk validasi kelengkapan
    $stmt_prof = $koneksi->prepare("SELECT * FROM profil_pelamar WHERE id_user = ?");
    $stmt_prof->bind_param("i", $id_pelamar);
    $stmt_prof->execute();
    $pelamar = $stmt_prof->get_result()->fetch_assoc();
    $stmt_prof->close();

    // C. Daftar Kolom Wajib
    $required_fields = [
        'nama_lengkap'         => 'Nama Lengkap',
        'no_telepon'           => 'No. Telepon',
        'alamat'               => 'Alamat',
        'tempat_tanggal_lahir' => 'Tempat, Tanggal Lahir',
        'riwayat_pendidikan'   => 'Riwayat Pendidikan',
        'pengalaman_kerja'     => 'Pengalaman Kerja',
        'keahlian'             => 'Keahlian',
        'ringkasan_pribadi'    => 'Ringkasan Pribadi',
        'foto_profil'          => 'Foto Profil',
        'dokumen_cv'           => 'CV (Curriculum Vitae)',
        'surat_lamaran'        => 'Surat Lamaran',
        'sertifikat_pendukung' => 'Sertifikat Pendukung',
        'ijasah'               => 'Ijazah'
    ];

    // D. Cek field yang kosong
    if ($pelamar) {
        foreach ($required_fields as $col => $label) {
            if (empty($pelamar[$col]) || trim($pelamar[$col]) === '') {
                $missing_fields[] = $label;
            }
        }
    } else {
        $missing_fields[] = "Profil Belum Dibuat Sama Sekali";
    }

    if (empty($missing_fields)) {
        $profile_is_complete = true;
    }
}
?>