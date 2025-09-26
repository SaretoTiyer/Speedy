document.addEventListener('DOMContentLoaded', function () {
    // Inicializar el carrusel
    var heroCarousel = new bootstrap.Carousel(document.getElementById('heroCarousel'), {
        interval: 5000,
        pause: false
    });

    // Animación del texto del héroe
    gsap.from(".hero-title", {duration: 1, y: 50, opacity: 0, delay: 0.5});
    gsap.from(".hero-text", {duration: 1, y: 50, opacity: 0, delay: 0.8});
    gsap.from(".hero-button", {duration: 1, y: 50, opacity: 0, delay: 1.1});

    // Animación suave del scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});
