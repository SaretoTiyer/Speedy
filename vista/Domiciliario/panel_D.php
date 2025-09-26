<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
    header("Location: index.php?controlador=usuarios&accion=login");
    exit();
}

if (!isset($datosDomiciliario) || empty($datosDomiciliario)) {
    echo "<div class='alert alert-danger'>Error: No se han recibido los datos del domiciliario.</div>";
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Domiciliario - <?php echo htmlspecialchars($datosDomiciliario['nombre']); ?></title>
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Arial', sans-serif;
        }
        .container {
            padding-top: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #007bff;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
        }
        .btn-custom {
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .card-title {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <?php
                        $fotoUrl = isset($datosDomiciliario['dom_foto']) && !empty($datosDomiciliario['dom_foto']) 
                            ? $datosDomiciliario['dom_foto'] 
                            : IMG_URL . 'domiciliarios/defaultDomiciliario.png';
                        ?>
                        <img src="<?php echo htmlspecialchars($fotoUrl); ?>" 
                             alt="Foto de perfil de <?php echo htmlspecialchars($datosDomiciliario['nombre']); ?>" 
                             class="profile-image"
                             onerror="this.onerror=null; this.src='<?php echo IMG_URL; ?>domiciliarios/defaultDomiciliario.png'; this.classList.add('default-image');">
                        <h4 class="card-title mb-3"><?php echo htmlspecialchars($datosDomiciliario['nombre'] . ' ' . $datosDomiciliario['apellido']); ?></h4>
                        <p class="card-text"><i class="fas fa-envelope text-primary"></i> <?php echo htmlspecialchars($datosDomiciliario['correo']); ?></p>
                        <p class="card-text"><i class="fas fa-phone text-success"></i> <?php echo htmlspecialchars($datosDomiciliario['telefono']); ?></p>
                        <p class="card-text"><i class="fas fa-map-marker-alt text-danger"></i> <?php echo htmlspecialchars($datosDomiciliario['direccion']); ?></p>
                        <p class="card-text"><i class="fas fa-motorcycle text-info"></i> <?php echo htmlspecialchars($datosDomiciliario['vehiculo']); ?></p>
                        <a href="<?php echo S_URL_D; ?>editarPerfil" class="btn btn-primary btn-custom mt-3">Editar Perfil</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Panel de Control</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo S_URL_D; ?>gestionPedidos" class="btn btn-primary btn-custom btn-block">
                                    <i class="fas fa-tasks me-2"></i> Gestión de Pedidos
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo S_URL_D; ?>mostrarEstadisticas" class="btn btn-warning btn-custom btn-block">
                                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo N_URL; ?>mostrarNotificaciones" class="btn btn-danger btn-custom btn-block">
                                    <i class="fas fa-bell me-2"></i> Notificaciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Últimos Pedidos Entregados</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($pedidos)): ?>
                                        <?php foreach ($pedidos as $pedido): ?>
                                            <tr>
                                                <td><?php echo $pedido['id']; ?></td>
                                                <td><?php echo $pedido['cliente'] ?? 'N/A'; ?></td>
                                                <td><?php echo $pedido['fecha'] ?? 'N/A'; ?></td>
                                                <td><span class="badge bg-<?php echo $pedido['estado'] == 'entregado' ? 'success' : 'warning'; ?>"><?php echo $pedido['estado']; ?></span></td>
                                                <td>
                                                    <a href="<?php echo S_URL_PED; ?>verDetallePedidoDomiciliario&id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm btn-custom">Ver Detalles</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No hay pedidos asignados actualmente.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>
