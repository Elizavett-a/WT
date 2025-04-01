let currentIndex = 0;
const slides = document.querySelector('.slides');

function showSlides() {
    currentIndex = (currentIndex + 1) % document.querySelectorAll('.slide').length;
    const offset = -currentIndex * 100;
    slides.style.transform = `translateX(${offset}%)`;
}

setInterval(showSlides, 5000);

document.querySelectorAll('.transparent-button').forEach(button => {
    button.addEventListener('click', (e) => {
        if (e.target.tagName === 'A') return;
        window.location.href = `/books/category/${e.target.textContent.toLowerCase()}`;
    });
});