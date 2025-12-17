$(document).ready(function () {
  // --- Mobile Sidebar Logic ---
  const $sidebar = $(".sidebar");
  const $overlay = $(".sidebar-overlay");
  const $toggleBtn = $(".mobile-menu-toggle");

  // 1. Buka/Tutup Sidebar saat tombol menu diklik
  $toggleBtn.click(function () {
    $sidebar.toggleClass("active");
    $overlay.toggleClass("active"); // Penting: Overlay juga harus ditoggle agar muncul
  });

  // 2. Tutup Sidebar saat area overlay (background gelap) diklik
  $overlay.click(function () {
    $sidebar.removeClass("active");
    $overlay.removeClass("active"); // Sembunyikan overlay juga
  });

  // --- Alert/Message Handling ---
  const $alerts = $(".message.animated");

  if ($alerts.length) {
    // Auto-hide after 3 seconds for better user experience
    const timeout = setTimeout(() => {
      $alerts.addClass("hide");
    }, 3000);

    // Remove from DOM after transition
    $alerts.on("transitionend", function (e) {
      // Ensure we are targeting the correct event
      if (
        e.originalEvent.propertyName === "opacity" &&
        $(this).hasClass("hide")
      ) {
        $(this).remove();
        clearTimeout(timeout); // Clear timeout if closed manually
      }
    });

    // Handle manual close
    $alerts.find(".close-btn").on("click", function () {
      // When the close button is clicked, hide its parent message
      $(this).closest(".message").addClass("hide");
    });
  }
});
