<?php
// Asegúrate de que el usuario ha iniciado sesión y es una tienda
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
    header("Location: index.php?controlador=usuarios&accion=login");
    exit();
}

// Asegúrate de que $datos está definido y contiene la información de la tienda
if (!isset($datos) || !isset($datos['tienda'])) {
    echo "<div class='alert alert-danger'>Error: No se han recibido los datos de la tienda.</div>";
    exit;
}

$tienda = $datos['tienda'];
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php
                    $fotoUrl = isset($tienda['tienda_foto']) && !empty($tienda['tienda_foto']) 
                        ? $tienda['tienda_foto'] 
                        : IMG_URL . 'tiendas/defaultTienda.png';
                    ?>
                    <img src="<?php echo BASE_URL . htmlspecialchars($fotoUrl); ?>" 
                         alt="Logo de <?php echo htmlspecialchars($tienda['nombre']); ?>" 
                         class="img-fluid rounded-circle mb-3 profile-image"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="card-title"><?php echo htmlspecialchars($tienda['nombre']); ?></h4>
                    <p class="card-text"><i class="fas fa-envelope text-primary"></i> <?php echo htmlspecialchars($tienda['correo']); ?></p>
                    <p class="card-text"><i class="fas fa-phone text-success"></i> <?php echo htmlspecialchars($tienda['telefono']); ?></p>
                    <p class="card-text"><i class="fas fa-map-marker-alt text-danger"></i> <?php echo htmlspecialchars($tienda['direccion']); ?></p>
                    <p class="card-text">
                        <i class="fas fa-clock text-info"></i> 
                        <?php 
                        if (isset($tienda['horario_apertura']) && isset($tienda['horario_cierre'])) {
                            $hora_apertura = date('h:i A', strtotime($tienda['horario_apertura']));
                            $hora_cierre = date('h:i A', strtotime($tienda['horario_cierre']));
                            echo htmlspecialchars($hora_apertura) . ' - ' . htmlspecialchars($hora_cierre);
                        } else {
                            echo 'Horario no definido';
                        }
                        ?>
                    </p>
                    <a href="<?php echo S_URL_T; ?>editarPerfil" class="btn btn-primary btn-sm mt-3">Editar Perfil</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Panel de Control</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="index.php?controlador=productos&accion=listar" class="btn btn-info btn-lg btn-block w-100">
                                <i class="fas fa-box-open me-2"></i> Gestionar Productos
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?php echo S_URL_PED; ?>pedidosTienda" class="btn btn-success btn-lg btn-block w-100">
                                <i class="fas fa-clipboard-list me-2"></i> Ver Pedidos
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?php echo E_URL; ?>mostrar" class="btn btn-warning btn-lg btn-block w-100">
                                <i class="fas fa-chart-bar me-2"></i> Estadísticas
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="<?php echo N_URL; ?>mostrarNotificaciones" class="btn btn-danger btn-lg btn-block w-100">
                                <i class="fas fa-bell me-2"></i> Notificaciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Últimos Pedidos</h5>
                    <?php if (empty($datos['ultimosPedidos'])): ?>
                        <p class="text-muted">No hay pedidos recientes.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Valor Total</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos['ultimosPedidos'] as $pedido): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusColor($pedido['estado']); ?>">
                                                    <?php echo ucfirst(htmlspecialchars($pedido['estado'])); ?>
                                                </span>
                                            </td>
                                            <td>$<?php echo number_format($pedido['valor_pedido'], 2); ?></td>
                                            <td>
                                                <a href="<?php echo S_URL_PED; ?>detallePedidoTienda&id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .btn {
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .table {
        border-radius: 10px;
        overflow: hidden;
    }
    .badge {
        padding: 8px 12px;
        font-weight: 500;
    }
</style>

<?php
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'pendiente':
            return 'warning';
        case 'en proceso':
            return 'info';
        case 'entregado':
            return 'success';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>
