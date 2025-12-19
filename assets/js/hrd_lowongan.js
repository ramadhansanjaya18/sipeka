/**
 * Script untuk Halaman Manajemen Lowongan (HRD)
 */

$(document).ready(function () {
  // --- Tombol Tambah Lowongan ---
  $("#btnTambahLowongan").click(function () {
    $("#formLowongan")[0].reset(); // Reset form
    $("#modalTitle").text("Tambah Lowongan Baru");
    $("#formAction").val("create");
    $("#formIdLowongan").val("");

    // Tampilkan Modal
    $("#modalLowongan").css("display", "block");
  });

  // --- Tombol Edit di Tabel ---
  $(".btn-edit").click(function () {
    // Ambil data dari atribut data-* tombol
    var id = $(this).data("id");
    var judul = $(this).data("judul");
    var posisi = $(this).data("posisi");
    var deskripsi_singkat = $(this).data("deskripsi_singkat");
    var deskripsi = $(this).data("deskripsi");
    var persyaratan = $(this).data("persyaratan");
    var tgl_buka = $(this).data("tgl_buka");
    var tgl_tutup = $(this).data("tgl_tutup");

    // Isi ke dalam Form Modal
    $("#modalTitle").text("Edit Lowongan");
    $("#formAction").val("update");
    $("#formIdLowongan").val(id);

    $("#formJudul").val(judul);
    $("#formPosisi").val(posisi);
    $("#formDeskripsiSingkat").val(deskripsi_singkat);
    $("#formDeskripsi").val(deskripsi);
    $("#formPersyaratan").val(persyaratan);
    $("#formTglBuka").val(tgl_buka);
    $("#formTglTutup").val(tgl_tutup);

    // Tampilkan Modal
    $("#modalLowongan").css("display", "block");
  });

  // --- Tombol Batal & Close (X) ---
  $(".modal-close, .btn-batal").click(function () {
    $("#modalLowongan").css("display", "none");
  });

  // --- Klik di luar modal untuk menutup ---
  $(window).click(function (event) {
    if (event.target == $("#modalLowongan")[0]) {
      $("#modalLowongan").css("display", "none");
    }
  });
});
