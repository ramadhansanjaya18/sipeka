document.addEventListener("DOMContentLoaded", function () {
  const incompleteBtn = document.getElementById("btn-incomplete-apply");

  if (incompleteBtn) {
    incompleteBtn.addEventListener("click", function (e) {
      e.preventDefault();

      const rawData = this.getAttribute("data-missing");
      let missingItems = [];

      try {
        missingItems = JSON.parse(rawData);
      } catch (error) {
        console.error("Gagal memproses data profil:", error);
        alert("Terjadi kesalahan sistem saat memvalidasi profil.");
        return;
      }

      let listText = "";
      if (Array.isArray(missingItems) && missingItems.length > 0) {
        missingItems.forEach(function (item) {
          listText += "- " + item + "\n";
        });
      }

      const message =
        "Maaf, Anda belum dapat melamar.\n\n" +
        "Mohon lengkapi data berikut di halaman Profil:\n" +
        listText +
        "\nKlik OK untuk menuju halaman Profil.";

      if (confirm(message)) {
        window.location.href = "profil.php";
      }
    });
  }
});
