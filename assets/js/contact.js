/**
 * Script untuk Halaman Kontak
 * Menangani animasi alert notifikasi.
 */

document.addEventListener("DOMContentLoaded", function () {
  var notification = document.getElementById("notification");

  // Cek apakah notifikasi muncul
  if (notification) {
    // Tunggu 5 detik (5000 ms) sebelum mulai menghilang
    setTimeout(function () {
      notification.style.opacity = "0"; // Mulai efek transisi CSS

      // Hapus elemen dari layout setelah transisi selesai (600ms)
      setTimeout(function () {
        notification.style.display = "none";
      }, 600);
    }, 5000);
  }
});
