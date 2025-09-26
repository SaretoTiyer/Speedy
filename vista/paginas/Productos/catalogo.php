<div class="container mt-5">
    <h1 class="text-center mb-5 display-4 fw-bold text-primary">Catálogo de Productos</h1>
    
    <div class="row mb-4 justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="<?php echo S_URL_P ?>catalogo" method="get" class="row g-3">
                        <input type="hidden" name="controlador" value="productos">
                        <input type="hidden" name="accion" value="catalogo">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="busqueda" class="form-control" placeholder="Buscar productos" value="<?php echo htmlspecialchars($datos['busqueda']); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="categoria" class="form-select">
                                <option value="">Todas las categorías</option>
                                <?php foreach ($datos['categorias'] as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>" <?php echo ($datos['categoria_seleccionada'] == $categoria['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($productos as $producto): ?>
            <div class="col">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="<?php echo $producto['imagen']; ?>" class="card-img-top product-image" alt="<?php echo $producto['nombre']; ?>">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text text-muted"><?php echo substr($producto['descripcion'], 0, 100) . '...'; ?></p>
                        <p class="card-text fs-4 fw-bold text-primary">$<?php echo number_format($producto['precio'], 2); ?></p>
                        <p class="card-text"><small class="text-muted"><i class="fas fa-store me-2"></i><?php echo $producto['nombre_tienda']; ?></small></p>
                        <p class="card-text"><small class="text-muted"><i class="fas fa-tag me-2"></i><?php echo $producto['nombre_categoria']; ?></small></p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <form action="<?php echo S_URL_PED; ?>agregarAlCarrito" method="post" class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">-</button>
                                <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['cantidad']; ?>" class="form-control text-center">
                                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-shopping-cart me-2"></i>Comprar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .product-image {
        height: 200px;
        object-fit: cover;
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

    // Tooltip para el botón de compra
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
