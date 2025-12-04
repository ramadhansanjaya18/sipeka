document.addEventListener('DOMContentLoaded', function () {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const mainNav = document.querySelector('.main-nav');
    const body = document.querySelector('body');

    if (hamburgerMenu && mainNav) {
        hamburgerMenu.addEventListener('click', function () {
            mainNav.classList.toggle('mobile-menu-open');
            body.classList.toggle('mobile-menu-open');
        });
    }
});