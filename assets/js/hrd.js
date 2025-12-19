$(document).ready(function () {
  // --- Mobile Sidebar Logic ---
  // Pastikan element sidebar di hrd_sidebar.php memiliki class "sidebar"
  // atau sesuaikan selector ini dengan ".hrd-wrapper" jika menggunakan layout sebelumnya.
  const $sidebar = $(".hrd-wrapper, .sidebar");
  const $overlay = $(".sidebar-overlay");
  const $toggleBtn = $(".mobile-menu-toggle");

  // 1. Buka/Tutup Sidebar saat tombol menu diklik
  $toggleBtn.click(function () {
    $sidebar.toggleClass("sidebar-open active"); // Support kedua class naming convention
    $overlay.toggleClass("active");
  });

  // 2. Tutup Sidebar saat area overlay (background gelap) diklik
  $overlay.click(function () {
    $sidebar.removeClass("sidebar-open active");
    $overlay.removeClass("active");
  });

  // --- Alert/Message Handling ---
  // Selector ini cocok dengan output dari displayHrdMessage() di PHP
  const $alerts = $(".message.animated");

  if ($alerts.length) {
    // Auto-hide after 3 seconds for better user experience
    const timeout = setTimeout(() => {
      $alerts.addClass("hide");
    }, 3000);

    // Remove from DOM after transition
    $alerts.on("transitionend", function (e) {
      if (
        e.originalEvent.propertyName === "opacity" &&
        $(this).hasClass("hide")
      ) {
        // Hapus container induknya juga agar margin/padding hilang
        $(this).closest(".message-container").remove();
        clearTimeout(timeout);
      }
    });

    // Handle manual close
    $alerts.find(".close-btn").on("click", function (e) {
      e.preventDefault();
      // When the close button is clicked, hide its parent message
      $(this).closest(".message").addClass("hide");
    });
  }
});
