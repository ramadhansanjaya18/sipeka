/**
 * Main Script SIPEKA
 * Menangani interaksi global seperti navigasi mobile.
 */

document.addEventListener("DOMContentLoaded", function () {
  // Logika Hamburger Menu
  const hamburger = document.getElementById("hamburgerMenu");
  const nav = document.getElementById("mainNav");

  if (hamburger && nav) {
    hamburger.addEventListener("click", function () {
      // Toggle class untuk animasi icon
      this.classList.toggle("active");
      // Toggle class untuk menampilkan menu
      nav.classList.toggle("open");
    });
  }
});
