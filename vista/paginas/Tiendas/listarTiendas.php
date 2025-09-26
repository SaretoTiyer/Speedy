<div class="container mt-5">
    <h2 class="text-center mb-4">Descubre Nuestras Tiendas</h2>
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($tiendas as $tienda): ?>
            <div class="col">
                <div class="card h-100 shadow-sm hover-card">
                    <img src="<?php echo BASE_URL . $tienda['tienda_foto']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($tienda['nombre']); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($tienda['nombre']); ?></h5>
                        <p class="card-text">
                            <i class="fas fa-map-marker-alt text-primary"></i> 
                            <?php echo htmlspecialchars($tienda['direccion']); ?>
                        </p>
                        <p class="card-text">
                            <i class="far fa-clock text-primary"></i> 
                            <?php echo date('h:i A', strtotime($tienda['horario_apertura'])); ?> - <?php echo date('h:i A', strtotime($tienda['horario_cierre'])); ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="<?php echo S_URL_T; ?>verTienda&id=<?php echo $tienda['id']; ?>" class="btn btn-primary btn-block">Ver Productos</a>
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
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.card-img-top {
    transition: opacity 0.3s ease-in-out;
}

.card:hover .card-img-top {
    opacity: 0.9;
}

.btn-primary {
    background-color: #4a90e2;
    border-color: #4a90e2;
    transition: background-color 0.3s ease-in-out;
}

.btn-primary:hover {
    background-color: #3a7bc8;
    border-color: #3a7bc8;
}
</style>
