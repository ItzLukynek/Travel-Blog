let currentIndex = 0;
let imagecount = document.querySelectorAll('.gallery-item').length;

function showSlide(index) {
  const gallery = document.querySelector('.gallery');
  const itemWidth = document.querySelector('.gallery-item').offsetWidth;
  gallery.style.transform = `translateX(${-index * itemWidth}px)`;
  updateDots(index);
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + imagecount) % imagecount;
  showSlide(currentIndex);
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % imagecount;
  showSlide(currentIndex);
}

function currentSlide(event) {
  if (event.target.classList.contains('dot')) {
    const dots = document.querySelectorAll('.dot');
    const index = Array.from(dots).indexOf(event.target);
    currentIndex = index;
    showSlide(currentIndex);
  }
}

function updateDots(index) {
  const dots = document.querySelectorAll('.dot');
  dots.forEach((dot, i) => {
    dot.style.backgroundColor = i === index ? 'rgba(226, 174, 29, 1)' : 'rgba(226, 174, 29, 0.6)';
  });
}