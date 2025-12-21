$(document).ready(function () {
  $("#btnTambahWawancara").click(function () {
    $("#formWawancara")[0].reset();
    $("#modalTitle").text("Jadwal Wawancara Baru");
    $("#formAction").val("create");
    $("#formIdWawancara").val("");
    $("#divSelectPelamar").show();
    $("#formIdLamaran").prop("required", true);
    $("#divNamaPelamar").hide();
    $("#modalWawancara").css("display", "block");
  });

  $(".btn-edit").click(function () {
    $("#modalTitle").text("Edit Jadwal Wawancara");
    $("#formAction").val("update");
    $("#formIdWawancara").val($(this).data("id_wawancara"));
    $("#divSelectPelamar").hide();
    $("#formIdLamaran").prop("required", false);
    $("#divNamaPelamar").show();
    $("#formNamaPelamarEdit").val($(this).data("nama_pelamar"));
    $("#formLokasi").val($(this).data("lokasi"));
    $("#formCatatan").val($(this).data("catatan"));
    $("#formStatus").val($(this).data("status"));
    $("#formTanggal").val($(this).data("tanggal"));
    $("#formJam").val($(this).data("jam"));
    $("#modalWawancara").css("display", "block");
  });

  $(".modal-close, .btn-batal").click(function () {
    $("#modalWawancara").css("display", "none");
  });

  $(window).click(function (event) {
    if (event.target == $("#modalWawancara")[0]) {
      $("#modalWawancara").css("display", "none");
    }
  });
});
