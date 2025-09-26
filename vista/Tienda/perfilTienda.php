<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($datos['tienda']['nombre']); ?> - <?php echo S_NAME; ?></title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .tienda-header {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .tienda-logo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .tienda-info {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .product-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-img {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="tienda-header">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo BASE_URL . htmlspecialchars($datos['tienda']['tienda_foto']); ?>" class="tienda-logo rounded-circle mb-3" alt="Logo de <?php echo htmlspecialchars($datos['tienda']['nombre']); ?>">
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?php echo htmlspecialchars($datos['tienda']['nombre']); ?></h1>
                    <p><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($datos['tienda']['direccion']); ?></p>
                    <p><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($datos['tienda']['telefono']); ?></p>
                    <p><i class="fas fa-clock me-2"></i><?php echo htmlspecialchars($datos['tienda']['horario_apertura']); ?> - <?php echo htmlspecialchars($datos['tienda']['horario_cierre']); ?></p>
                    
                    <?php if ($datos['esPropioDueno']): ?>
                        <a href="<?php echo S_URL_T; ?>editarPerfil" class="btn btn-primary mt-2">Editar Perfil</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Estadísticas de la Tienda</h2>
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stat-card text-center">
                    <i class="fas fa-box fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Total Productos</h3>
                    <p class="card-text display-4"><?php echo $datos['estadisticas']['totalProductos']; ?></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card text-center">
                    <i class="fas fa-star fa-3x mb-3 text-warning"></i>
                    <h3 class="card-title">Calificación</h3>
                    <p class="card-text display-4"><?php echo number_format($datos['estadisticas']['calificacionPromedio'], 1); ?><small>/5</small></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card text-center">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-success"></i>
                    <h3 class="card-title">Total Pedidos</h3>
                    <p class="card-text display-4"><?php echo $datos['estadisticas']['totalPedidos']; ?></p>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Productos Destacados</h2>
        <div class="row">
            <?php foreach ($datos['productos'] as $producto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card h-100">
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="card-text"><strong>Precio:</strong> $<?php echo number_format($producto['precio'], 2); ?></p>
                            <a href="<?php echo S_URL_P; ?>verProducto&id=<?php echo $producto['id']; ?>" class="btn btn-outline-primary mt-auto">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
