$(document).ready(function() {
    $(".menu-toggle").click(function() {
        $(".sidebar").toggleClass("active");
        $(".header").toggleClass("flex");
        document.querySelector(".closener").style.display = "flex";
    });
});
$(document).ready(function() {
    $(".close_button").click(function() {
        $(".sidebar").toggleClass("active");
        $(".header").toggleClass("flex");
        document.querySelector(".closener").style.display = "none";
    });
});
$(window).resize(function() {
if ($(window).width() > 996) {
    document.querySelector(".closener").style.display = "none";
}else{
    document.querySelector(".closener").style.display = "flex";
}

});