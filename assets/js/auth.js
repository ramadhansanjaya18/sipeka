document.addEventListener("DOMContentLoaded", function () {
  const notifications = document.querySelectorAll(".notification");
  notifications.forEach(function (notification) {
    const closeBtn = notification.querySelector(".close-btn");
    if (closeBtn) {
      closeBtn.addEventListener("click", function () {
        notification.style.display = "none";
      });
    }
    setTimeout(function () {
      notification.style.display = "none";
    }, 3000);
  });
});
