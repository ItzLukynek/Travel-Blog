document.addEventListener("DOMContentLoaded", function () {

    // setTimeout(showgallery, 1500);

    // Number of galleries to initially show and increment on each button click
    var initialGalleriesToShow = 3;
    var galleriesIncrement = 2;
  
    var galleryItems = document.querySelectorAll('.galleries .gallery-item');
  
    function toggleGalleries(start, end) {
      galleryItems.forEach(function (gallery, index) {
        gallery.style.display = index >= start && index < end ? 'block' : 'none';
      });
    }
  
    toggleGalleries(0, initialGalleriesToShow);
  
    var loadMoreBtn = document.getElementById('loadMoreBtn');
    var visibleGalleries = initialGalleriesToShow;
  
    loadMoreBtn.addEventListener('click', function () {
      visibleGalleries += galleriesIncrement;
  
      
      toggleGalleries(0, visibleGalleries);
  
       if (visibleGalleries >= galleryItems.length) {
        loadMoreBtn.style.display = 'none';
      }
    });
  });
  
  // function showgallery(){
  //   document.querySelector(".galleries").classList.remove("d-none");
  // }