document.addEventListener('DOMContentLoaded', function() {
    const starContainers = document.querySelectorAll('.star-rating');
    
    starContainers.forEach(container => {
        const stars = container.querySelectorAll('i');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const rating = star.getAttribute('data-rating');
                highlightStars(stars, rating);
            });
            
            star.addEventListener('mouseout', () => {
                const currentRating = container.getAttribute('data-rating');
                highlightStars(stars, currentRating);
            });
            
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-rating');
                container.setAttribute('data-rating', rating);
                highlightStars(stars, rating);
                console.log(`${container.id} calificado con ${rating} estrellas`);
            });
        });
    });
    
    function highlightStars(stars, rating) {
        stars.forEach(star => {
            const starRating = star.getAttribute('data-rating');
            if (starRating <= rating) {
                star.classList.remove('far');
                star.classList.add('fas', 'active');
            } else {
                star.classList.remove('fas', 'active');
                star.classList.add('far');
            }
        });
    }
    
    const form = document.getElementById('ratingForm');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (validateForm()) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Una vez enviada, no podrás modificar tu calificación.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Agregar los valores de calificación al formulario antes de enviarlo
                    const tiendaRating = document.getElementById('tienda-rating').getAttribute('data-rating');
                    const domiciliarioRating = document.getElementById('domiciliario-rating').getAttribute('data-rating');
                    
                    const tiendaInput = document.createElement('input');
                    tiendaInput.type = 'hidden';
                    tiendaInput.name = 'calificacion_tienda';
                    tiendaInput.value = tiendaRating;
                    form.appendChild(tiendaInput);
                    
                    const domiciliarioInput = document.createElement('input');
                    domiciliarioInput.type = 'hidden';
                    domiciliarioInput.name = 'calificacion_domiciliario';
                    domiciliarioInput.value = domiciliarioRating;
                    form.appendChild(domiciliarioInput);
                    
                    form.submit();
                }
            });
        }
    });
    
    function validateForm() {
        const tiendaRating = document.getElementById('tienda-rating').getAttribute('data-rating');
        const domiciliarioRating = document.getElementById('domiciliario-rating').getAttribute('data-rating');
        
        console.log('Calificación tienda:', tiendaRating);
        console.log('Calificación domiciliario:', domiciliarioRating);
        
        if (tiendaRating === '0' || domiciliarioRating === '0') {
            Swal.fire('Error', 'Por favor, califica tanto a la tienda como al domiciliario.', 'error');
            return false;
        }
        return true;
    }
});
