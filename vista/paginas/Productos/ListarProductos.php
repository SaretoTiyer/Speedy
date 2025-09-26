<div class="container mt-5">
    <h2 class="text-center mb-5 display-4 fw-bold text-primary">Catálogo de Productos</h2>
    
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i>No hay productos para mostrar. ¡Comienza agregando tu primer producto!
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($productos as $producto): ?>
                <div class="col">
                    <div class="card h-100 shadow product-card">
                        <div class="position-relative">
                            <img src="<?php echo BASE_URL . $producto['imagen']; ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <span class="position-absolute top-0 end-0 badge bg-primary m-2">
                                <?php echo $producto['cantidad']; ?> en stock
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="text-primary fs-4 fw-bold">$<?php echo number_format($producto['precio'], 2); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo S_URL_P; ?>editarProducto&id=<?php echo $producto['id']; ?>" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo S_URL_P; ?>eliminar&id=<?php echo $producto['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');" data-bs-toggle="tooltip" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="<?php echo S_URL_P; ?>agregar" class="btn btn-primary btn-lg rounded-circle btn-floating" data-bs-toggle="tooltip" title="Agregar nuevo producto">
        <i class="fas fa-plus"></i>
    </a>
</div>

<style>
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 15px;
        overflow: hidden;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .product-image {
        height: 250px;
        object-fit: cover;
    }
    .btn-floating {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1030;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .btn-floating:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para las tarjetas de productos
    gsap.from('.col', {
        duration: 0.6,
        y: 50,
        opacity: 0,
        stagger: 0.1,
        ease: "power3.out"
    });

    // Tooltip para el botón flotante
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Efecto hover para las tarjetas de productos
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            gsap.to(card, {duration: 0.3, y: -10, boxShadow: '0 10px 20px rgba(0,0,0,0.1)'});
        });
        card.addEventListener('mouseleave', () => {
            gsap.to(card, {duration: 0.3, y: 0, boxShadow: '0 4px 10px rgba(0,0,0,0.1)'});
        });
    });
});
</script>