document.addEventListener("DOMContentLoaded", function() {
    const scrollernav = document.getElementById("scrollernav");
    const normalnav = document.getElementById("normalnav");
    let lastScrollPosition = 0;

    function updateNavigationVisibility() {
        const currentScrollPosition = window.scrollY;

        if (currentScrollPosition > 300 && lastScrollPosition <= 300) {
            // Scrolled down past 300px, switch to the scrollernav
            scrollernav.classList.remove("d-none");
            scrollernav.classList.add("reveal");
            normalnav.classList.add("d-none");
        } else if (currentScrollPosition <= 300 && lastScrollPosition > 300) {
            // Scrolled back to 300px or less, switch back to the normalnav
            scrollernav.classList.add("d-none");
            scrollernav.classList.remove("reveal");
            scrollernav.classList.remove("active");
            normalnav.classList.remove("d-none");
        }

        lastScrollPosition = currentScrollPosition;
    }

    // Attach the scroll event listener to trigger the function
    window.addEventListener("scroll", updateNavigationVisibility);
});