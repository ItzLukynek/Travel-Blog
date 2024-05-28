

document.addEventListener("DOMContentLoaded", function () {
    const cookieBanner = document.getElementById('cookieBanner');
    const acceptButton = document.getElementById('acceptButton');
    const acceptAllButton = document.getElementById('acceptAllButton');

    acceptButton.addEventListener('click', function () {
        cookieBanner.classList.remove('show');
        // Set sessionStorage variable
        sessionStorage.setItem('cookieAccepted', 'true');
    });

    acceptAllButton.addEventListener('click', function () {
        cookieBanner.classList.remove('show');
        // Set sessionStorage variable
        sessionStorage.setItem('cookieAccepted', 'true');
    });

    // Check if sessionStorage variable is set
    const cookieAccepted = sessionStorage.getItem('cookieAccepted');
    if (!cookieAccepted) {
        // If not set, show the cookie banner after a delay
        setTimeout(function () {
            cookieBanner.classList.add('show');
        }, 3500);
    }
});