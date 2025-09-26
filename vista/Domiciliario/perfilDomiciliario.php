<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($datos['domiciliario']['nombre']); ?> - <?php echo S_NAME; ?></title>
    <style>
        body { background-color: #f8f9fa; }
        .domiciliario-header {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .domiciliario-foto {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="domiciliario-header">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="<?php echo htmlspecialchars($datos['domiciliario']['dom_foto']); ?>" class="domiciliario-foto rounded-circle mb-3" alt="Foto de <?php echo htmlspecialchars($datos['domiciliario']['nombre']); ?>">
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?php echo htmlspecialchars($datos['domiciliario']['nombre'] . ' ' . $datos['domiciliario']['apellido']); ?></h1>
                    <p><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($datos['domiciliario']['correo']); ?></p>
                    <p><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($datos['domiciliario']['telefono']); ?></p>
                    <p><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($datos['domiciliario']['direccion']); ?></p>
                    <p><i class="fas fa-motorcycle me-2"></i><?php echo htmlspecialchars($datos['domiciliario']['vehiculo']); ?></p>
                    
                    <?php if ($datos['esPropioDomiciliario']): ?>
                        <a href="<?php echo S_URL_D; ?>editarPerfil" class="btn btn-primary mt-2">Editar Perfil</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h2 class="mb-4">Estadísticas del Domiciliario</h2>
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="stat-card text-center">
                    <i class="fas fa-route fa-3x mb-3 text-primary"></i>
                    <h3 class="card-title">Total Entregas</h3>
                    <p class="card-text display-4"><?php echo $datos['estadisticas']['totalEntregas']; ?></p>
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
                    <i class="fas fa-clock fa-3x mb-3 text-success"></i>
                    <h3 class="card-title">Tiempo Promedio</h3>
                    <p class="card-text display-4"><?php echo $datos['estadisticas']['tiempoPromedioEntrega']; ?> min</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
