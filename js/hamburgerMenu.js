//hamburger menu funcionality

document.addEventListener("DOMContentLoaded", function() {
    const hamburgerMenu = document.querySelector(".hamburger-menu");
    const menuIcon = document.querySelector(".menu-icon");
    const fullscreenMenu = document.querySelector(".fullscreen-menu");

    hamburgerMenu.addEventListener("click", function() {
        this.classList.toggle("open");
        fullscreenMenu.classList.toggle("open");
    });
});



