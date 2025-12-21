document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.getElementById("hamburgerMenu");
  const nav = document.getElementById("mainNav");
  if (hamburger && nav) {
    hamburger.addEventListener("click", function () {
      this.classList.toggle("active");
      nav.classList.toggle("open");
    });
  }
});
