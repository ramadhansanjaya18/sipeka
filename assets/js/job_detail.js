/**
 * Script khusus untuk halaman Detail Lowongan
 * Menangani interaksi tombol lamar jika profil belum lengkap.
 */

document.addEventListener("DOMContentLoaded", function () {
  // Cari tombol dengan ID khusus
  const incompleteBtn = document.getElementById("btn-incomplete-apply");

  if (incompleteBtn) {
    incompleteBtn.addEventListener("click", function (e) {
      // Mencegah aksi default tombol
      e.preventDefault();

      // Ambil data JSON dari atribut data-missing
      const rawData = this.getAttribute("data-missing");
      let missingItems = [];

      try {
        // Parsing JSON string menjadi array JavaScript
        missingItems = JSON.parse(rawData);
      } catch (error) {
        console.error("Gagal memproses data profil:", error);
        alert("Terjadi kesalahan sistem saat memvalidasi profil.");
        return;
      }

      // Susun pesan alert
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

      // Tampilkan konfirmasi
      if (confirm(message)) {
        // Redirect ke halaman profil
        window.location.href = "profil.php";
      }
    });
  }
});
