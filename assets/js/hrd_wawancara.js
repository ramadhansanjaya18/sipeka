$(document).ready(function () {
  // --- TOMBOL TAMBAH ---
  $("#btnTambahWawancara").click(function () {
    $("#formWawancara")[0].reset();
    $("#modalTitle").text("Jadwal Wawancara Baru");
    $("#formAction").val("create");
    $("#formIdWawancara").val("");

    // Mode Tambah: Tampilkan Dropdown Pelamar
    $("#divSelectPelamar").show();
    $("#formIdLamaran").prop("required", true);

    // Sembunyikan Input Nama Readonly
    $("#divNamaPelamar").hide();

    $("#modalWawancara").css("display", "block");
  });

  // --- TOMBOL EDIT ---
  $(".btn-edit").click(function () {
    $("#modalTitle").text("Edit Jadwal Wawancara");
    $("#formAction").val("update");
    $("#formIdWawancara").val($(this).data("id_wawancara"));

    // Mode Edit: Sembunyikan Dropdown (Tidak bisa ganti orang)
    $("#divSelectPelamar").hide();
    $("#formIdLamaran").prop("required", false);

    // Tampilkan Nama Pelamar
    $("#divNamaPelamar").show();
    $("#formNamaPelamarEdit").val($(this).data("nama_pelamar"));

    // Isi Data
    $("#formLokasi").val($(this).data("lokasi"));
    $("#formCatatan").val($(this).data("catatan"));
    $("#formStatus").val($(this).data("status"));
    $("#formTanggal").val($(this).data("tanggal"));
    $("#formJam").val($(this).data("jam"));

    $("#modalWawancara").css("display", "block");
  });

  // --- BATAL & CLOSE ---
  $(".modal-close, .btn-batal").click(function () {
    $("#modalWawancara").css("display", "none");
  });

  $(window).click(function (event) {
    if (event.target == $("#modalWawancara")[0]) {
      $("#modalWawancara").css("display", "none");
    }
  });
});
