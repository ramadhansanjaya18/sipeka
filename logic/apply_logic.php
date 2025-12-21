<?php
include_once __DIR__ . '/config/init.php';
include_once __DIR__ . '/config/auth_pelamar.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Lowongan tidak valid.'];
    header("Location: index.php");
    exit();
}
$id_lowongan = (int)$_GET['id'];
$id_pelamar = $_SESSION['id_user'];

$stmt_lowongan = $koneksi->prepare("SELECT posisi_lowongan, (CURDATE() BETWEEN tanggal_buka AND tanggal_tutup) AS is_active FROM lowongan WHERE id_lowongan = ?");
$stmt_lowongan->bind_param("i", $id_lowongan);
$stmt_lowongan->execute();
$result_lowongan = $stmt_lowongan->get_result();

if ($result_lowongan->num_rows == 0) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Data lowongan tidak ditemukan.'];
    header("Location: index.php");
    exit();
}
$lowongan_data = $result_lowongan->fetch_assoc();
$stmt_lowongan->close();

$stmt_pelamar = $koneksi->prepare("SELECT * FROM profil_pelamar WHERE id_user = ?");
$stmt_pelamar->bind_param("i", $id_pelamar);
$stmt_pelamar->execute();
$pelamar_data = $stmt_pelamar->get_result()->fetch_assoc();
$stmt_pelamar->close();

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

$missing_data = [];

if ($pelamar_data) {
    foreach ($required_fields as $db_col => $label) {   
        if (empty($pelamar_data[$db_col]) || trim($pelamar_data[$db_col]) === '') {
            $missing_data[] = $label;
        }
    }
} else {
    $missing_data[] = "Data Profil Belum Dibuat";
}

if (!empty($missing_data)) {
    $_SESSION['flash_message'] = [
        'type' => 'error', 
        'text' => 'Gagal melamar. Data profil belum lengkap: ' . implode(", ", $missing_data) . '.'
    ];
    header("Location: profil.php"); 
    exit();
}

if (!$lowongan_data['is_active']) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Lowongan ini sudah ditutup.'];
    header("Location: detail_lowongan.php?id=" . $id_lowongan);
    exit();
}

$stmt_cek = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_pelamar = ? AND id_lowongan = ?");
$stmt_cek->bind_param("ii", $id_pelamar, $id_lowongan);
$stmt_cek->execute();
if ($stmt_cek->get_result()->num_rows > 0) {
    $_SESSION['flash_message'] = ['type' => 'info', 'text' => 'Anda sudah pernah melamar lowongan ini.'];
    header("Location: status_lamaran.php");
    exit();
}
$stmt_cek->close();

$status_default = "Diproses";
$tanggal_lamaran = date("Y-m-d H:i:s");
$posisi_dilamar = $lowongan_data['posisi_lowongan'];

$query_insert = "INSERT INTO lamaran (id_pelamar, id_lowongan, tanggal_lamaran, status_lamaran, posisi_dilamar) 
                 VALUES (?, ?, ?, ?, ?)";
$stmt_insert = $koneksi->prepare($query_insert);
$stmt_insert->bind_param("iisss", $id_pelamar, $id_lowongan, $tanggal_lamaran, $status_default, $posisi_dilamar);

if ($stmt_insert->execute()) {
    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Lamaran Anda untuk posisi ' . htmlspecialchars($posisi_dilamar, ENT_QUOTES, 'UTF-8') . ' berhasil dikirim!'];
    header("Location: status_lamaran.php");
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan sistem. Gagal mengirim lamaran.'];
    header("Location: detail_lowongan.php?id=" . $id_lowongan);
}

$stmt_insert->close();
$koneksi->close();
exit(); 
?>