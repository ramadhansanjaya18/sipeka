<?php
/**
 * Halaman Manajemen Lowongan untuk HRD.
 *
 * Fitur:
 * - Menampilkan daftar semua lowongan dengan status (Aktif/Tutup) yang dihitung real-time.
 * - Pencarian lowongan berdasarkan posisi atau status.
 * - CRUD (Create, Read, Update, Delete) lowongan melalui modal form.
 * - [BARU] Menambahkan input untuk Deskripsi Singkat.
 */

$page = 'lowongan';
// 1. Panggil Header
include '../templates/hrd_header.php'; 

// 3. --- LOGIKA CRUD (CREATE, UPDATE, DELETE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    // Ambil ID HRD dari session untuk pencatatan
    $id_hrd = $_SESSION['id_user'];

    // AKSI: CREATE (Tambah Lowongan)
    if ($_POST['action'] == 'create') {
        $judul = $_POST['judul'];
        $posisi = $_POST['posisi_lowongan'];
        $deskripsi_singkat = $_POST['deskripsi_singkat']; // [BARU] Ambil deskripsi singkat
        $deskripsi = $_POST['deskripsi'];
        $persyaratan = $_POST['persyaratan'];
        $tgl_buka = $_POST['tanggal_buka'];
        $tgl_tutup = $_POST['tanggal_tutup'];

        // [BARU] Tambahkan deskripsi_singkat ke query INSERT
        $query = "INSERT INTO lowongan (id_hrd, judul, posisi_lowongan, deskripsi_singkat, deskripsi, persyaratan, tanggal_buka, tanggal_tutup) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        // [BARU] Update tipe data bind_param (tambah satu 's' untuk deskripsi_singkat)
        $stmt->bind_param("isssssss", $id_hrd, $judul, $posisi, $deskripsi_singkat, $deskripsi, $persyaratan, $tgl_buka, $tgl_tutup);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan baru berhasil ditambahkan.'];
        } else {
            $error_message = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal menambahkan lowongan: {$error_message}"];
        }
        $stmt->close();
        header("Location: lowongan.php");
        exit();
    }

    // AKSI: UPDATE (Perbarui Lowongan)
    if ($_POST['action'] == 'update') {
        $id_lowongan = $_POST['id_lowongan'];
        $judul = $_POST['judul'];
        $posisi = $_POST['posisi_lowongan'];
        $deskripsi_singkat = $_POST['deskripsi_singkat']; // [BARU] Ambil deskripsi singkat
        $deskripsi = $_POST['deskripsi'];
        $persyaratan = $_POST['persyaratan'];
        $tgl_buka = $_POST['tanggal_buka'];
        $tgl_tutup = $_POST['tanggal_tutup'];

        // [BARU] Tambahkan deskripsi_singkat ke query UPDATE
        $query = "UPDATE lowongan SET 
                    judul = ?, 
                    posisi_lowongan = ?, 
                    deskripsi_singkat = ?,
                    deskripsi = ?, 
                    persyaratan = ?, 
                    tanggal_buka = ?, 
                    tanggal_tutup = ? 
                  WHERE id_lowongan = ?";
        $stmt = $koneksi->prepare($query);
        // [BARU] Update tipe data bind_param
        $stmt->bind_param("sssssssi", $judul, $posisi, $deskripsi_singkat, $deskripsi, $persyaratan, $tgl_buka, $tgl_tutup, $id_lowongan);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan berhasil diperbarui.'];
        } else {
            $error_message = htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal memperbarui lowongan: {$error_message}"];
        }
        $stmt->close();
        header("Location: lowongan.php");
        exit();
    }

    // AKSI: DELETE (Hapus Lowongan)
    if ($_POST['action'] == 'delete') {
        $id_lowongan = $_POST['id_lowongan'];

        $koneksi->begin_transaction();
        try {
            // ... (Logika hapus sama seperti sebelumnya) ...
            $stmt_get_lamaran = $koneksi->prepare("SELECT id_lamaran FROM lamaran WHERE id_lowongan = ?");
            $stmt_get_lamaran->bind_param("i", $id_lowongan);
            $stmt_get_lamaran->execute();
            $result_lamaran = $stmt_get_lamaran->get_result();
            $lamaran_ids = [];
            while ($row = $result_lamaran->fetch_assoc()) {
                $lamaran_ids[] = $row['id_lamaran'];
            }
            $stmt_get_lamaran->close();

            if (!empty($lamaran_ids)) {
                $id_placeholders = implode(',', array_fill(0, count($lamaran_ids), '?'));
                $stmt_delete_wawancara = $koneksi->prepare("DELETE FROM wawancara WHERE id_lamaran IN ({$id_placeholders})");
                $stmt_delete_wawancara->bind_param(str_repeat('i', count($lamaran_ids)), ...$lamaran_ids);
                $stmt_delete_wawancara->execute();
                $stmt_delete_wawancara->close();

                $stmt_delete_lamaran = $koneksi->prepare("DELETE FROM lamaran WHERE id_lowongan = ?");
                $stmt_delete_lamaran->bind_param("i", $id_lowongan);
                $stmt_delete_lamaran->execute();
                $stmt_delete_lamaran->close();
            }

            $stmt_delete_lowongan = $koneksi->prepare("DELETE FROM lowongan WHERE id_lowongan = ?");
            $stmt_delete_lowongan->bind_param("i", $id_lowongan);
            $stmt_delete_lowongan->execute();

            if ($stmt_delete_lowongan->affected_rows > 0) {
                $koneksi->commit();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Lowongan dan data terkait berhasil dihapus.'];
            } else {
                throw new Exception("Lowongan tidak ditemukan atau sudah dihapus.");
            }
            $stmt_delete_lowongan->close();
        } catch (Exception $e) {
            $koneksi->rollback();
            $error_message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            $_SESSION['message'] = ['type' => 'error', 'text' => "Gagal menghapus lowongan: {$error_message}"];
        }
        header("Location: lowongan.php");
        exit();
    }
}

// 4. --- LOGIKA READ (Mengambil data lowongan) ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// [BARU] Tambahkan deskripsi_singkat ke SELECT
$query = "SELECT 
            id_lowongan, judul, posisi_lowongan, deskripsi_singkat, deskripsi, persyaratan, 
            DATE_FORMAT(tanggal_buka, '%d - %m - %Y') AS tgl_buka_formatted, 
            DATE_FORMAT(tanggal_tutup, '%d - %m - %Y') AS tgl_tutup_formatted,
            tanggal_buka, tanggal_tutup, 
            (CASE 
                WHEN CURDATE() >= tanggal_buka AND CURDATE() <= tanggal_tutup THEN 'Aktif'
                ELSE 'Tutup'
            END) AS status_lowongan_realtime
          FROM lowongan";

if (!empty($search)) {
    $search_param = "%{$search}%";
    $query .= " WHERE posisi_lowongan LIKE ? OR 
              (CASE WHEN CURDATE() >= tanggal_buka AND CURDATE() <= tanggal_tutup THEN 'Aktif' ELSE 'Tutup' END) LIKE ?";
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

<div class="page-title">
    <h1>Manajemen Data Lowongan Pekerjaan</h1>
    <p>Kelola lowongan pekerjaan yang tersedia di Syjura Coffee.</p>
</div>

<div class="page-actions">
    <button class="btn-primary" id="btnTambahLowongan">+ Tambahkan Lowongan</button>
    <div class="search-container">
        <form action="lowongan.php" method="GET">
            <input type="text" name="search" placeholder="Cari posisi atau status..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256">
                    <g fill="#6a4e3b" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path d="M21,3c-9.37891,0 -17,7.62109 -17,17c0,9.37891 7.62109,17 17,17c3.71094,0 7.14063,-1.19531 9.9375,-3.21875l13.15625,13.125l2.8125,-2.8125l-13,-13.03125c2.55469,-2.97656 4.09375,-6.83984 4.09375,-11.0625c0,-9.37891 -7.62109,-17 -17,-17zM21,5c8.29688,0 15,6.70313 15,15c0,8.29688 -6.70312,15 -15,15c-8.29687,0 -15,-6.70312 -15,-15c0,-8.29687 6.70313,-15 15,-15z"></path>
                        </g>
                    </g>
                </svg>
            </button>
        </form>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Posisi</th>
                <th class="col-status">Status (Real-time)</th> 
                <th>Tanggal Buka</th>
                <th>Tanggal Tutup</th>
                <th class="col-aksi">Aksi</th> 
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php
                    $status_class = $row['status_lowongan_realtime'] == 'Aktif' ? 'status-aktif' : 'status-ditutup';
                    ?>
                    <tr>
                        <td data-label="Posisi"><?php echo htmlspecialchars($row['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td data-label="Status" class="col-status">
                            <span class="<?php echo htmlspecialchars($status_class, ENT_QUOTES, 'UTF-8'); ?>" style="font-weight: bold;">
                                <?php echo htmlspecialchars($row['status_lowongan_realtime'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>

                        <td data-label="Tanggal Buka"><?php echo htmlspecialchars($row['tgl_buka_formatted'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td data-label="Tanggal Tutup"><?php echo htmlspecialchars($row['tgl_tutup_formatted'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td data-label="Aksi" class="col-aksi action-buttons">
                            <button class="btn-edit" 
                                data-id="<?php echo htmlspecialchars($row['id_lowongan'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-judul="<?php echo htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-posisi="<?php echo htmlspecialchars($row['posisi_lowongan'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-deskripsi_singkat="<?php echo htmlspecialchars($row['deskripsi_singkat'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-deskripsi="<?php echo htmlspecialchars($row['deskripsi'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-persyaratan="<?php echo htmlspecialchars($row['persyaratan'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-tgl_buka="<?php echo htmlspecialchars($row['tanggal_buka'], ENT_QUOTES, 'UTF-8'); ?>" 
                                data-tgl_tutup="<?php echo htmlspecialchars($row['tanggal_tutup'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="lowongan.php" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus lowongan juga akan menghapus SEMUA data lamaran dan jadwal wawancara yang terkait. Lanjutkan?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_lowongan" value="<?php echo htmlspecialchars($row['id_lowongan'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        <?php if (!empty($search)) : ?>
                            Tidak ada lowongan yang cocok dengan pencarian &quot;<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&quot;.
                        <?php else : ?>
                            Belum ada data lowongan.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="modalLowongan" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2 id="modalTitle">Tambah Lowongan Baru</h2>
            <span class="modal-close">&times;</span>
        </div>

        <form id="formLowongan" action="lowongan.php" method="POST">
            <div class="modal-body">

                <div class="modal-body-left">
                    <input type="hidden" name="action" id="formAction">
                    <input type="hidden" name="id_lowongan" id="formIdLowongan">

                    <div class="form-group">
                        <label for="formJudul">Judul Lowongan</label>
                        <input type="text" id="formJudul" name="judul" placeholder="Masukan Judul Lowongan Pekerjaan" required>
                    </div>

                    <div class="form-group">
                        <label for="formPosisi">Posisi Lowongan</label>
                        <input type="text" id="formPosisi" name="posisi_lowongan" placeholder="Masukan Posisi Yang Tersedia" required>
                    </div>

                    <div class="form-group">
                        <label for="formDeskripsiSingkat">Deskripsi Singkat</label>
                        <input type="text" id="formDeskripsiSingkat" name="deskripsi_singkat" placeholder="Ringkasan singkat untuk tampilan kartu (Max 255 char)" required>
                    </div>

                    <div class="form-group">
                        <label for="formDeskripsi">Deskripsi Lengkap</label>
                        <textarea id="formDeskripsi" name="deskripsi" placeholder="Deskripsikan Lowongan Yang Tersedia Secara Detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formPersyaratan">Persyaratan</label>
                        <textarea id="formPersyaratan" name="persyaratan" placeholder="Jelaskan Persyaratan Untuk Lowongan..."></textarea>
                    </div>
                </div>

                <div class="modal-body-right">
                    <div class="form-group">
                        <label for="formTglBuka">Tanggal Buka</label>
                        <input type="date" id="formTglBuka" name="tanggal_buka" required>
                    </div>

                    <div class="form-group">
                        <label for="formTglTutup">Tanggal Tutup</label>
                        <input type="date" id="formTglTutup" name="tanggal_tutup" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" value="Otomatis (berdasarkan tanggal)" disabled style="background-color: #eee;">
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
    $(document).ready(function() {

        // --- Tombol Tambah Lowongan ---
        $("#btnTambahLowongan").click(function() {
            $("#formLowongan")[0].reset();
            $("#modalTitle").text("Tambah Lowongan Baru");
            $("#formAction").val("create");
            $("#formIdLowongan").val("");
            $("#modalLowongan").css("display", "block");
        });

        // --- Tombol Edit di Tabel ---
        $(".btn-edit").click(function() {
            var id = $(this).data("id");
            var judul = $(this).data("judul");
            var posisi = $(this).data("posisi");
            var deskripsi_singkat = $(this).data("deskripsi_singkat"); // [BARU] Ambil data deskripsi singkat
            var deskripsi = $(this).data("deskripsi");
            var persyaratan = $(this).data("persyaratan");
            var tgl_buka = $(this).data("tgl_buka");
            var tgl_tutup = $(this).data("tgl_tutup");

            $("#modalTitle").text("Edit Lowongan");
            $("#formAction").val("update");
            $("#formIdLowongan").val(id);
            $("#formJudul").val(judul);
            $("#formPosisi").val(posisi);
            $("#formDeskripsiSingkat").val(deskripsi_singkat); // [BARU] Isi input deskripsi singkat
            $("#formDeskripsi").val(deskripsi);
            $("#formPersyaratan").val(persyaratan);
            $("#formTglBuka").val(tgl_buka);
            $("#formTglTutup").val(tgl_tutup);

            $("#modalLowongan").css("display", "block");
        });

        // --- Tombol Batal & Close (X) ---
        $(".modal-close, .btn-batal").click(function() {
            $("#modalLowongan").css("display", "none");
        });

        // --- Klik di luar modal untuk menutup ---
        $(window).click(function(event) {
            if (event.target == $("#modalLowongan")[0]) {
                $("#modalLowongan").css("display", "none");
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