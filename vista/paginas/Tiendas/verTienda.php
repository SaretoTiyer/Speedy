<div class="container-fluid mt-5 tienda-container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg mb-5">
                <div class="card-header bg-gradient-primary text-white text-center py-4">
                    <h2 class="font-weight-bold mb-0"><?php echo htmlspecialchars($tienda['nombre']); ?></h2>
                </div>
                <div class="card-body">
                    <!-- Botón para regresar a la lista de tiendas -->
                    <div class="text-start mb-4">
                        <a href="<?php echo S_URL_T; ?>listarTiendas" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Tiendas
                        </a>
                    </div>
                    
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <p class="lead mb-0"><i class="fas fa-map-marker-alt text-primary me-2"></i><?php echo htmlspecialchars($tienda['direccion']); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><i class="fas fa-clock text-primary me-2"></i><?php 
                            if (isset($tienda['horario_apertura']) && isset($tienda['horario_cierre'])) {
                                $hora_apertura = date('h:i A', strtotime($tienda['horario_apertura']));
                                $hora_cierre = date('h:i A', strtotime($tienda['horario_cierre']));
                                echo htmlspecialchars($hora_apertura) . ' - ' . htmlspecialchars($hora_cierre);
                            } else {
                                echo 'Horario no definido';
                            }
                            ?></p>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php if (empty($productos)): ?>
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <p class="mb-0">Esta tienda aún no tiene productos disponibles.</p>
                        </div>
                    <?php else: ?>
                        <h3 class="text-center mb-4">Nuestros Productos</h3>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 productos-container">
                            <?php foreach ($productos as $producto): ?>
                                <div class="col producto-item">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <img src="<?php echo BASE_URL . $producto['imagen']; ?>" class="card-img-top producto-imagen" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="precio-producto">$<?php echo number_format($producto['precio'], 2); ?></span>
                                                <form action="<?php echo S_URL_PED; ?>agregarAlCarrito" method="post" class="d-flex justify-content-between align-items-center">
                                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                                                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['cantidad']; ?>" class="form-control mb-2">
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-cart-plus me-2"></i>Agregar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tienda-container {
        background-color: #f8f9fa;
    }
    .card {
        transition: transform 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .bg-gradient-primary {
        background: linear-gradient(to right, #007bff, #0056b3);
    }
    .producto-imagen {
        height: 200px;
        object-fit: cover;
    }
    .precio-producto {
        font-size: 1.2rem;
        font-weight: bold;
        color: #28a745;
    }
    .agregar-carrito {
        transition: all 0.3s ease;
    }
    .agregar-carrito:hover {
        transform: scale(1.05);
    }
    .productos-container {
        overflow-x: auto;
        padding-bottom: 1rem;
    }
    .producto-item {
        min-width: 250px;
    }
    .btn-outline-primary {
        transition: all 0.3s ease;
    }
    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
        transform: translateY(-2px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para los productos
    gsap.from(".producto-item", {
        duration: 0.8,
        opacity: 0,
        y: 50,
        stagger: 0.2,
        ease: "power3.out"
    });

    // Efecto hover para los botones de agregar al carrito
    const botonesAgregar = document.querySelectorAll('.agregar-carrito');
    botonesAgregar.forEach(boton => {
        boton.addEventListener('mouseenter', () => {
            gsap.to(boton, {duration: 0.3, backgroundColor: "#0056b3", scale: 1.05});
        });
        boton.addEventListener('mouseleave', () => {
            gsap.to(boton, {duration: 0.3, backgroundColor: "#007bff", scale: 1});
        });
    });

    // SweetAlert para confirmación de agregar al carrito
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            Swal.fire({
                title: '¿Agregar al carrito?',
                text: "Este producto se añadirá a tu carrito de compras",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
});
</script>
