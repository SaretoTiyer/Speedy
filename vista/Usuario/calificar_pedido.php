<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
    header("Location: " . S_URL . "login");
    exit();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Calificar Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo S_URL_PED; ?>calificarPedido&id=<?php echo htmlspecialchars($pedido['id']); ?>" method="POST" id="ratingForm">
                        <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($pedido['id']); ?>">

                        <div class="mb-4">
                            <label for="calificacion_tienda" class="form-label fw-bold">Calificación de la Tienda</label>
                            <div class="star-rating" id="tienda-rating">
                                <input type="hidden" name="calificacion_tienda" id="calificacion_tienda" value="0">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" data-rating="<?php echo $i; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="calificacion_domiciliario" class="form-label fw-bold">Calificación del Domiciliario</label>
                            <div class="star-rating" id="domiciliario-rating">
                                <input type="hidden" name="calificacion_domiciliario" id="calificacion_domiciliario" value="0">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" data-rating="<?php echo $i; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comentarios" class="form-label fw-bold">Comentarios</label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3" placeholder="Cuéntanos tu experiencia..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Enviar Calificación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        background-color: #4a90e2;
        padding: 20px;
    }
    .card-body {
        padding: 30px;
    }
    .star-rating {
        font-size: 30px;
        display: flex;
        justify-content: center;
    }
    .star-rating .fas {
        cursor: pointer;
        transition: color 0.2s ease, transform 0.2s ease;
        margin: 0 5px;
        color: #c0c0c0; /* Color gris para estrellas no seleccionadas */
    }
    .star-rating .fas:hover {
        transform: scale(1.1);
    }
    .star-rating .fas.active {
        color: #ffd700; /* Color dorado para estrellas seleccionadas */
    }
    .btn-primary {
        background-color: #4a90e2;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #3a7bc8;
    }
    textarea.form-control {
        border: 1px solid #ced4da;
        border-radius: 10px;
    }
    textarea.form-control:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const starContainers = document.querySelectorAll('.star-rating');
    
    starContainers.forEach(container => {
        const stars = container.querySelectorAll('i');
        const input = container.querySelector('input[type="hidden"]');
        
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const rating = star.getAttribute('data-rating');
                highlightStars(stars, rating);
            });
            
            star.addEventListener('mouseout', () => {
                highlightStars(stars, input.value);
            });
            
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-rating');
                input.value = rating;
                highlightStars(stars, rating);
            });
        });
    });
    
    function highlightStars(stars, rating) {
        stars.forEach(star => {
            const starRating = star.getAttribute('data-rating');
            if (starRating <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
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
                confirmButtonColor: '#4a90e2',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Gracias!',
                                text: 'Tu calificación ha sido enviada con éxito.',
                                icon: 'success',
                                confirmButtonColor: '#4a90e2'
                            }).then(() => {
                                window.location.href = '<?php echo S_URL_PED; ?>verPedidos';
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Hubo un problema al enviar tu calificación.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Hubo un problema al comunicarse con el servidor.', 'error');
                    });
                }
            });
        }
    });

    function validateForm() {
        const tiendaRating = document.getElementById('calificacion_tienda').value;
        const domiciliarioRating = document.getElementById('calificacion_domiciliario').value;
        
        if (tiendaRating === '0' || domiciliarioRating === '0') {
            Swal.fire('Error', 'Por favor, califica tanto a la tienda como al domiciliario.', 'error');
            return false;
        }
        return true;
    }
});
</script>
