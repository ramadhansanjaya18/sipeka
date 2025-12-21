$(document).ready(function () {
  const $sidebar = $(".hrd-wrapper, .sidebar");
  const $overlay = $(".sidebar-overlay");
  const $toggleBtn = $(".mobile-menu-toggle");

  $toggleBtn.click(function () {
    $sidebar.toggleClass("sidebar-open active");
    $overlay.toggleClass("active");
  });

  $overlay.click(function () {
    $sidebar.removeClass("sidebar-open active");
    $overlay.removeClass("active");
  });

  const $alerts = $(".message.animated");

  if ($alerts.length) {
    const timeout = setTimeout(() => {
      $alerts.addClass("hide");
    }, 3000);

    $alerts.on("transitionend", function (e) {
      if (
        e.originalEvent.propertyName === "opacity" &&
        $(this).hasClass("hide")
      ) {
        $(this).closest(".message-container").remove();
        clearTimeout(timeout);
      }
    });

    $alerts.find(".close-btn").on("click", function (e) {
      e.preventDefault();
      $(this).closest(".message").addClass("hide");
    });
  }
});
