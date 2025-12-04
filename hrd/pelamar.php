<?php
/**
 * Halaman Manajemen Pelamar untuk HRD.
 *
 * Fitur:
 * - Menampilkan daftar semua pelamar yang telah mengajukan lamaran.
 * - Pencarian pelamar berdasarkan nama atau posisi yang dilamar.
 * - Mengubah status lamaran (Diproses, Wawancara, Diterima, Ditolak) langsung dari tabel.
 * - Terdapat link untuk melihat detail lengkap setiap pelamar.
 */

$page = 'pelamar';
// 1. Panggil Header
include '../templates/hrd_header.php'; // Sudah memanggil init.php (session & koneksi)

// Inisialisasi variabel pesan
$message = "";

// 3. --- LOGIKA UPDATE STATUS (jika ada POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    // Ambil data dari form
    $id_lamaran = $_POST['id_lamaran'];
    $status_baru = $_POST['status_baru'];

    // Validasi status
    $status_valid = ['Diproses', 'Diterima', 'Ditolak', 'Wawancara'];
    if (in_array($status_baru, $status_valid)) {
        $stmt = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
        $stmt->bind_param("si", $status_baru, $id_lamaran);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Status lamaran berhasil diperbarui.'];
        } else {
            $error_message = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal memperbarui status: {$error_message}"];
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Status tidak valid.'];
    }

    // Redirect untuk mencegah resubmit dan menampilkan pesan dari session
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $redirect_url = 'pelamar.php';
    if (!empty($search_query)) {
        $redirect_url .= '?search=' . urlencode($search_query);
    }
    header("Location: " . $redirect_url);
    exit();
}

// Ambil kata kunci pencarian jika ada (real_escape_string tidak perlu)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// 4. --- LOGIKA READ (Mengambil data lamaran) ---
$query = "SELECT 
            l.id_lamaran, 
            u.id_user, 
            u.nama_lengkap, 
            l.posisi_dilamar, 
            DATE_FORMAT(l.tanggal_lamaran, '%d - %m - %Y') AS tgl_lamaran_formatted, 
            l.status_lamaran 
          FROM 
            lamaran l 
          JOIN 
            user u ON l.id_pelamar = u.id_user";

// Tambahkan kondisi pencarian jika ada kata kunci
if (!empty($search)) {
    $search_param = "%{$search}%";
    $full_query = $query . " WHERE u.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? ORDER BY l.tanggal_lamaran DESC";

    $stmt_read = $koneksi->prepare($full_query);
    $stmt_read->bind_param("ss", $search_param, $search_param);
    $stmt_read->execute();
    $result_lamaran = $stmt_read->get_result();
    $stmt_read->close();
} else {
    $full_query = $query . " ORDER BY l.tanggal_lamaran DESC";
    $result_lamaran = $koneksi->query($full_query);
}

?>
<div class="page-title">
    <h1>Manajemen Data Pelamar</h1>
    <p>Kelola data pelamar yang telah mengajukan lamaran pekerjaan.</p>
</div>

<div class="page-actions">
    <div class="search-container">
        <form action="pelamar.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar atau posisi..."
                value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                    <g fill="#6a4e3b" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt"
                        stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0"
                        font-family="none" font-weight="none" font-size="none" text-anchor="none"
                        style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path
                                d="M21,3c-9.37891,0 -17,7.62109 -17,17c0,9.37891 7.62109,17 17,17c3.71094,0 7.14063,-1.19531 9.9375,-3.21875l13.15625,13.125l2.8125,-2.8125l-13,-13.03125c2.55469,-2.97656 4.09375,-6.83984 4.09375,-11.0625c0,-9.37891 -7.62109,-17 -17,-17zM21,5c8.29688,0 15,6.70313 15,15c0,8.29688 -6.70312,15 -15,15c-8.29687,0 -15,-6.70312 -15,-15c0,-8.29687 6.70313,-15 15,-15z">
                            </path>
                        </g>
                    </g>
                </svg>
            </button>
        </form>
    </div>
</div>

<?php echo $message; // Pesan notifikasi (berisi HTML, tidak di-escape) ?>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Nama Pelamar</th>
                <th>Posisi Dilamar</th>
                <th>Tanggal Lamaran</th>
                <th class="col-status">Status</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_lamaran && $result_lamaran->num_rows > 0): ?>
                <?php while ($row = $result_lamaran->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Nama Pelamar"><?php echo htmlspecialchars($row['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td data-label="Posisi Dilamar">
                            <?php echo htmlspecialchars($row['posisi_dilamar'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Tanggal Lamaran">
                            <?php echo htmlspecialchars($row['tgl_lamaran_formatted'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td data-label="Status" class="col-status">
                            <form
                                action="pelamar.php<?php echo !empty($search) ? '?search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                method="POST" class="form-status">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id_lamaran"
                                    value="<?php echo htmlspecialchars($row['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>">
                                <select name="status_baru" class="table-select" onchange="this.form.submit()">
                                    <option value="Diproses" <?php if ($row['status_lamaran'] == 'Diproses')
                                        echo 'selected'; ?>>
                                        Diproses</option>
                                    <option value="Wawancara" <?php if ($row['status_lamaran'] == 'Wawancara')
                                        echo 'selected'; ?>>Wawancara</option>
                                    <option value="Diterima" <?php if ($row['status_lamaran'] == 'Diterima')
                                        echo 'selected'; ?>>
                                        Diterima</option>
                                    <option value="Ditolak" <?php if ($row['status_lamaran'] == 'Ditolak')
                                        echo 'selected'; ?>>
                                        Ditolak</option>
                                </select>
                            </form>
                        </td>

                        <td data-label="Aksi" class="col-aksi action-buttons">
                            <a href="detail_pelamar.php?id_lamaran=<?php echo htmlspecialchars($row['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>"
                                class="btn-view" title="Lihat Detail Pelamar">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; // Akhir while loop ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        <?php if (!empty($search)): ?>
                            Tidak ada pelamar yang cocok dengan pencarian
                            &quot;<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&quot;.
                        <?php else: ?>
                            Belum ada data pelamar.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; // Akhir if ($result_lamaran) ?>
        </tbody>
    </table>
</div>


<?php
// 6. Panggil Footer
include '../templates/hrd_footer.php';

// 7. Tutup koneksi database
$koneksi->close();
?>