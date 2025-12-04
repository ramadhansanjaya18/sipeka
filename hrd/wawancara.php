<?php
/**
 * Halaman Manajemen Wawancara untuk HRD.
 *
 * Fitur:
 * - Menampilkan daftar semua jadwal wawancara.
 * - Pencarian jadwal berdasarkan nama pelamar, posisi, atau status wawancara.
 * - CRUD (Create, Update, Delete) jadwal wawancara melalui modal form.
 * - Create: Menjadwalkan wawancara baru dan secara otomatis mengubah status pelamar menjadi 'Wawancara'. Proses ini dibungkus dalam transaksi database untuk memastikan integritas data.
 * - Delete: Membatalkan jadwal wawancara dan mengembalikan status pelamar menjadi 'Diproses'. Proses ini juga menggunakan transaksi.
 */

$page = 'wawancara';
// 1. Panggil Header
include '../templates/hrd_header.php'; // Sudah memanggil init.php (session & koneksi)

// Inisialisasi variabel pesan
$message = "";

// 3. --- LOGIKA CRUD (CREATE, UPDATE, DELETE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    // AKSI: CREATE (Tambah Wawancara)
    if ($_POST['action'] == 'create') {
        $id_lamaran = $_POST['id_lamaran'];
        $lokasi = $_POST['lokasi'];
        $catatan = $_POST['catatan'];
        $status = $_POST['status_wawancara'];
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];
        $status_lamaran_baru = "Wawancara";

        if (empty($id_lamaran) || empty($lokasi) || empty($status) || empty($tanggal) || empty($jam)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Input tidak valid. Pelamar, Lokasi, Status, Tanggal, dan Jam wajib diisi.'];
        } else {
            $jadwal = "{$tanggal} {$jam}";

            // Gunakan transaksi untuk memastikan kedua query (INSERT dan UPDATE) berhasil atau gagal bersamaan.
            // Ini menjaga konsistensi data antara tabel `wawancara` dan `lamaran`.
            $koneksi->begin_transaction();
            try {
                // Query 1: Cek apakah ID Lamaran valid sebelum melanjutkan
                $stmt_cek = $koneksi->prepare("SELECT id_pelamar FROM lamaran WHERE id_lamaran = ?");
                $stmt_cek->bind_param("i", $id_lamaran);
                $stmt_cek->execute();
                if ($stmt_cek->get_result()->num_rows == 0) {
                    throw new Exception("ID Lamaran '" . htmlspecialchars($id_lamaran, ENT_QUOTES, 'UTF-8') . "' tidak ditemukan.");
                }
                $stmt_cek->close();

                // Query 2: Insert ke tabel wawancara
                $stmt_insert = $koneksi->prepare("INSERT INTO wawancara (id_lamaran, jadwal, lokasi, status_wawancara, catatan) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("issss", $id_lamaran, $jadwal, $lokasi, $status, $catatan);
                if (!$stmt_insert->execute()) {
                    throw new Exception($stmt_insert->error);
                }
                $stmt_insert->close();

                // Query 3: Update status di tabel lamaran menjadi 'Wawancara'
                $stmt_update = $koneksi->prepare("UPDATE lamaran SET status_lamaran = ? WHERE id_lamaran = ?");
                $stmt_update->bind_param("si", $status_lamaran_baru, $id_lamaran);
                if (!$stmt_update->execute()) {
                    throw new Exception($stmt_update->error);
                }
                $stmt_update->close();

                $koneksi->commit();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal wawancara berhasil ditambahkan dan status lamaran telah diperbarui.'];
            } catch (Exception $e) {
                $koneksi->rollback();
                $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal menambahkan jadwal: {$error_message}"];
            }
        }
    }

    // AKSI: UPDATE (Perbarui Wawancara)
    if ($_POST['action'] == 'update') {
        $id_wawancara = $_POST['id_wawancara'];
        $lokasi = $_POST['lokasi'];
        $catatan = $_POST['catatan'];
        $status = $_POST['status_wawancara'];
        $tanggal = $_POST['tanggal'];
        $jam = $_POST['jam'];

        if (empty($id_wawancara) || empty($lokasi) || empty($status) || empty($tanggal) || empty($jam)) {
            $message = "<div class='message animated error'>Input tidak valid. Lokasi, Status, Tanggal, dan Jam wajib diisi.</div>";
        } else {
            $jadwal = "{$tanggal} {$jam}";

            $stmt = $koneksi->prepare("UPDATE wawancara SET jadwal = ?, lokasi = ?, status_wawancara = ?, catatan = ? WHERE id_wawancara = ?");
            $stmt->bind_param("ssssi", $jadwal, $lokasi, $status, $catatan, $id_wawancara);

            if ($stmt->execute()) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Jadwal wawancara berhasil diperbarui.'];
            } else {
                $error_message = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
                $message = "<div class='notification message animated error'><span class='close-btn'>&times;</span>Gagal memperbarui jadwal: {$error_message}</div>";
            }
            $stmt->close();
        }
    }

    // AKSI: DELETE (Hapus Wawancara)
    if ($_POST['action'] == 'delete') {
        $id_wawancara = $_POST['id_wawancara'];

        // Gunakan transaksi untuk memastikan jadwal terhapus dan status lamaran dikembalikan dengan benar.
        $koneksi->begin_transaction();
        try {
            // Ambil id_lamaran dari jadwal yang akan dihapus
            $stmt_get_id = $koneksi->prepare("SELECT id_lamaran FROM wawancara WHERE id_wawancara = ?");
            $stmt_get_id->bind_param("i", $id_wawancara);
            $stmt_get_id->execute();
            $result_id = $stmt_get_id->get_result();
            if ($result_id->num_rows == 0) {
                throw new Exception("Jadwal wawancara tidak ditemukan.");
            }
            $id_lamaran = $result_id->fetch_assoc()['id_lamaran'];
            $stmt_get_id->close();

            // Hapus jadwal wawancara
            $stmt_del = $koneksi->prepare("DELETE FROM wawancara WHERE id_wawancara = ?");
            $stmt_del->bind_param("i", $id_wawancara);
            $stmt_del->execute();
            $stmt_del->close();

            // Kembalikan status lamaran ke 'Diproses'
            $stmt_update = $koneksi->prepare("UPDATE lamaran SET status_lamaran = 'Diproses' WHERE id_lamaran = ?");
            $stmt_update->bind_param("i", $id_lamaran);
            $stmt_update->execute();
            $stmt_update->close();

            $koneksi->commit();
            $message = "<div class='notification message animated success'><span class='close-btn'>&times;</span>Jadwal wawancara berhasil dihapus dan status pelamar dikembalikan ke 'Diproses'.</div>";
        } catch (Exception $e) {
            $koneksi->rollback();
            $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            $message = "<div class='notification message animated error'><span class='close-btn'>&times;</span>Gagal menghapus jadwal: {$error_message}</div>";
        }
    }
}


// 4. --- LOGIKA READ (Mengambil data wawancara untuk ditampilkan) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT 
            w.id_wawancara, w.id_lamaran,
            u.nama_lengkap, l.posisi_dilamar,
            DATE_FORMAT(w.jadwal, '%d - %m - %Y') AS tanggal_formatted, 
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_formatted, 
            w.status_wawancara,
            w.lokasi, w.catatan,
            DATE_FORMAT(w.jadwal, '%Y-%m-%d') AS tanggal_raw, -- Untuk form edit
            DATE_FORMAT(w.jadwal, '%H:%i') AS jam_raw      -- Untuk form edit
          FROM 
            wawancara w
          JOIN 
            lamaran l ON w.id_lamaran = l.id_lamaran
          JOIN 
            user u ON l.id_pelamar = u.id_user";

if (!empty($search)) {
    $search_param = "%{$search}%";
    $full_query = $query . " WHERE u.nama_lengkap LIKE ? OR l.posisi_dilamar LIKE ? OR w.status_wawancara LIKE ? ORDER BY w.jadwal ASC";
    $stmt_read = $koneksi->prepare($full_query);
    $stmt_read->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt_read->execute();
    $result = $stmt_read->get_result();
    $stmt_read->close();
} else {
    $full_query = $query . " ORDER BY w.jadwal ASC";
    $result = $koneksi->query($full_query);
}


// 5. --- LOGIKA UNTUK MODAL (Ambil data pelamar untuk dropdown) ---
$query_pelamar = "SELECT 
                        l.id_lamaran, u.nama_lengkap, l.posisi_dilamar 
                    FROM 
                        lamaran l 
                    JOIN 
                        user u ON l.id_pelamar = u.id_user
                    WHERE 
                        l.status_lamaran = 'Diproses' OR l.status_lamaran = 'Wawancara'
                    ORDER BY u.nama_lengkap";
$result_pelamar = $koneksi->query($query_pelamar);
$opsi_pelamar = [];
if ($result_pelamar) {
    while ($row_p = $result_pelamar->fetch_assoc()) {
        $opsi_pelamar[] = $row_p;
    }
}

?>
<div class="page-title">
    <h1>Manajemen Jadwal Wawancara</h1>
    <p>Kelola jadwal wawancara untuk pelamar yang lolos seleksi.</p>
</div>

<div class="page-actions">
    <button class="btn-primary" id="btnTambahWawancara">+ Jadwal wawancara baru</button>
    <div class="search-container">
        <form action="wawancara.php" method="GET">
            <input type="text" name="search" placeholder="Cari pelamar, posisi, atau status..."
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
                <th>Tanggal</th>
                <th>Jam</th>
                <th class="col-status">Status</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Nama Pelamar"><?php echo htmlspecialchars($row['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td data-label="Posisi Dilamar">
                            <?php echo htmlspecialchars($row['posisi_dilamar'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Tanggal"><?php echo htmlspecialchars($row['tanggal_formatted'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td data-label="Jam"><?php echo htmlspecialchars($row['jam_formatted'], ENT_QUOTES, 'UTF-8'); ?> WIB
                        </td>

                        <td data-label="Status" class="col-status">
                            <?php echo htmlspecialchars($row['status_wawancara'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td data-label="Aksi" class="col-aksi action-buttons">
                            <button class="btn-edit"
                                data-id_wawancara="<?php echo htmlspecialchars($row['id_wawancara'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-id_lamaran="<?php echo htmlspecialchars($row['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-nama_pelamar="<?php echo htmlspecialchars($row['nama_lengkap'] . ' (' . $row['posisi_dilamar'] . ')', ENT_QUOTES, 'UTF-8'); ?>"
                                data-lokasi="<?php echo htmlspecialchars($row['lokasi'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-catatan="<?php echo htmlspecialchars($row['catatan'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-status="<?php echo htmlspecialchars($row['status_wawancara'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-tanggal="<?php echo htmlspecialchars($row['tanggal_raw'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-jam="<?php echo htmlspecialchars($row['jam_raw'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <form action="wawancara.php" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Status lamaran pelamar akan dikembalikan ke \'Diproses\'.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_wawancara"
                                    value="<?php echo htmlspecialchars($row['id_wawancara'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">
                        <?php if (!empty($search)): ?>
                            Tidak ada jadwal yang cocok dengan pencarian
                            &quot;<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&quot;.
                        <?php else: ?>
                            Belum ada jadwal wawancara.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalWawancara" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Jadwal Wawancara Baru</h2>
            <span class="modal-close">&times;</span>
        </div>
        <form id="formWawancara" action="wawancara.php" method="POST">
            <div class="modal-body">
                <div class="modal-body-left">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id_wawancara" id="formIdWawancara">
                    <div class="form-group">
                        <label for="formIdLamaran">Pelamar (ID Lamaran)</label>
                        <select id="formIdLamaran" name="id_lamaran" required>
                            <option value="">-- Pilih Pelamar --</option>
                            <?php foreach ($opsi_pelamar as $pelamar): ?>
                                <option
                                    value="<?php echo htmlspecialchars($pelamar['id_lamaran'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($pelamar['nama_lengkap'] . " - " . $pelamar['posisi_dilamar'] . " (ID: " . $pelamar['id_lamaran'] . ")", ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="formNamaPelamarEdit" readonly style="display: none; background: #eee;">
                    </div>
                    <div class="form-group">
                        <label for="formLokasi">Lokasi</label>
                        <input type="text" id="formLokasi" name="lokasi"
                            placeholder="Contoh : Online Via Zoom / Kantor Syjura Coffe" required>
                    </div>
                    <div class="form-group">
                        <label for="formCatatan">Tambahkan Catatan</label>
                        <textarea id="formCatatan" name="catatan"
                            placeholder="Tambahkan Detail Penting, link Zoom, pengingat, dll."></textarea>
                    </div>
                </div>
                <div class="modal-body-right">
                    <div class="form-group">
                        <label for="formStatus">Status</label>
                        <select id="formStatus" name="status_wawancara" required>
                            <option value="Terjadwal">Terjadwal</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="formTanggal">Tanggal</label>
                        <input type="date" id="formTanggal" name="tanggal" required>
                    </div>
                    <div class="form-group">
                        <label for="formJam">Jam</label>
                        <input type="time" id="formJam" name="jam" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-batal">Batal</button>
                <button type="submit" class="btn-simpan">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        // --- Tombol Tambah Wawancara ---
        $("#btnTambahWawancara").click(function () {
            $("#formWawancara")[0].reset();
            $("#modalTitle").text("Jadwal Wawancara Baru");
            $("#formAction").val("create");
            $("#formIdWawancara").val("");
            $("#formIdLamaran").show().prop("disabled", false);
            $("#formNamaPelamarEdit").hide();
            $("#modalWawancara").css("display", "block");
        });

        // --- Tombol Edit di Tabel ---
        $(".btn-edit").click(function () {
            var id_wawancara = $(this).data("id_wawancara");
            var id_lamaran = $(this).data("id_lamaran");
            var nama_pelamar = $(this).data("nama_pelamar");
            var lokasi = $(this).data("lokasi");
            var catatan = $(this).data("catatan");
            var status = $(this).data("status");
            var tanggal = $(this).data("tanggal");
            var jam = $(this).data("jam");

            $("#modalTitle").text("Edit Jadwal Wawancara");
            $("#formAction").val("update");
            $("#formIdWawancara").val(id_wawancara);
            $("#formIdLamaran").hide().val(id_lamaran);
            $("#formNamaPelamarEdit").show().val(nama_pelamar + " (ID: " + id_lamaran + ")");
            $("#formLokasi").val(lokasi);
            $("#formCatatan").val(catatan);
            $("#formStatus").val(status);
            $("#formTanggal").val(tanggal);
            $("#formJam").val(jam);

            $("#modalWawancara").css("display", "block");
        });

        // --- Tombol Batal & Close (X) ---
        $(".modal-close, .btn-batal").click(function () {
            $("#modalWawancara").css("display", "none");
        });

        // --- Klik di luar modal untuk menutup ---
        $(window).click(function (event) {
            if (event.target == $("#modalWawancara")[0]) {
                $("#modalWawancara").css("display", "none");
            }
        });
    });
</script>

<?php
// 8. Panggil Footer
include '../templates/hrd_footer.php';

// 9. Tutup koneksi database
$koneksi->close();
?>