<?php
if (!isset($datos)) {
    $datos = [
        'tipoNotificacion' => 'todas',
        'notificaciones' => [],
        'tipoUsuario' => 'visitante',
        'conteoNotificaciones' => []
    ];
}

$tipoNotificacion = $datos['tipoNotificacion'];
$notificaciones = $datos['notificaciones'];
$tipoUsuario = $datos['tipoUsuario'];
$conteoNotificaciones = $datos['conteoNotificaciones'];
$esAdmin = ($tipoUsuario === 'administrador');
$esTienda = ($tipoUsuario === 'tienda');
$esDomiciliario = ($tipoUsuario === 'domiciliario');
$esUsuario = ($tipoUsuario === 'usuario');
?>

<div class="container-fluid py-4">
    <h1 class="mb-4 text-primary">
        <?php
        if ($esAdmin) echo 'Centro de Notificaciones del Administrador';
        elseif ($esTienda) echo 'Centro de Notificaciones de la Tienda';
        elseif ($esDomiciliario) echo 'Centro de Notificaciones del Domiciliario';
        else echo 'Mis Notificaciones';
        ?>
    </h1>
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Tipos de Notificaciones</h5>
                    <div class="list-group">
                        <?php
                        $tiposNotificacion = [
                            'todas' => ['Todas', 'fas fa-bell'],
                            'pedido' => ['Pedidos', 'fas fa-shopping-cart'],
                            'informacion' => ['Información', 'fas fa-info-circle']
                        ];

                        if ($esAdmin) {
                            $tiposNotificacion['solicitud'] = ['Solicitudes', 'fas fa-envelope'];
                        }
                        if ($esTienda) {
                            $tiposNotificacion['inventario'] = ['Inventario', 'fas fa-box'];
                        }
                        if ($esDomiciliario) {
                            $tiposNotificacion['entrega'] = ['Entregas', 'fas fa-truck'];
                        }

                        foreach ($tiposNotificacion as $tipo => $info):
                            $conteo = isset($conteoNotificaciones[$tipo]) ? $conteoNotificaciones[$tipo] : 0;
                        ?>
                            <a href="<?php echo N_URL; ?>mostrarNotificaciones&tipo=<?php echo $tipo; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $tipoNotificacion == $tipo ? 'active' : ''; ?>">
                                <span><i class="<?php echo $info[1]; ?> me-2"></i><?php echo $info[0]; ?></span>
                                <span class="badge bg-primary rounded-pill"><?php echo $conteo; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Notificaciones de <?php echo ucfirst($tipoNotificacion); ?></h5>
                        <a href="<?php echo N_URL; ?>mostrarNotificaciones" class="btn btn-primary btn-sm rounded-pill">
                            <i class="fas fa-sync-alt me-1"></i>Actualizar
                        </a>
                    </div>
                    <div class="notification-content" style="max-height: 600px; overflow-y: auto;">
                        <?php if (empty($notificaciones)): ?>
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>No hay notificaciones en este momento.
                            </div>
                        <?php else: ?>
                            <?php foreach ($notificaciones as $notificacion): ?>
                                <?php if ($esAdmin || $notificacion['tipo'] !== 'solicitud'): ?>
                                    <div class="card mb-3 notification-card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-subtitle text-muted">
                                                    <i class="far fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($notificacion['fecha'])); ?>
                                                </h6>
                                                <span class="badge bg-<?php echo $notificacion['tipo'] == 'solicitud' ? 'warning' : 'info'; ?>">
                                                    <?php echo ucfirst($notificacion['tipo']); ?>
                                                </span>
                                            </div>
                                            <p class="card-text">
                                                <?php echo htmlspecialchars($notificacion['mensaje']); ?>
                                            </p>
                                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                                <?php if ($esAdmin && $notificacion['tipo'] == 'solicitud'): ?>
                                                    <div>
                                                        <a href="<?php echo N_URL; ?>aceptarSolicitud&id=<?php echo $notificacion['id']; ?>" class="btn btn-success btn-sm rounded-pill">
                                                            <i class="fas fa-check me-1"></i>Aceptar
                                                        </a>
                                                        <a href="<?php echo N_URL; ?>rechazarSolicitud&id=<?php echo $notificacion['id']; ?>" class="btn btn-danger btn-sm rounded-pill">
                                                            <i class="fas fa-times me-1"></i>Rechazar
                                                        </a>
                                                    </div>
                                                <?php elseif ($esDomiciliario && $notificacion['tipo'] == 'entrega'): ?>
                                                    <a href="<?php echo N_URL; ?>aceptarEntrega&id=<?php echo $notificacion['id']; ?>" class="btn btn-success btn-sm rounded-pill">
                                                        <i class="fas fa-check me-1"></i>Aceptar Entrega
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo N_URL; ?>marcarComoLeido&id=<?php echo $notificacion['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                                                        <i class="fas fa-check me-1"></i>Marcar como leído
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?php echo N_URL; ?>eliminarNotificacion&id=<?php echo $notificacion['id']; ?>" class="btn btn-danger btn-sm rounded-pill">
                                                    <i class="fas fa-trash me-1"></i>Eliminar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
                <div class="mt-4 d-flex gap-2">
                    <a href="<?php echo N_URL; ?>marcarTodasComoLeidas" class="btn btn-primary rounded-pill">
                        <i class="fas fa-check-double me-1"></i>Marcar todas como leídas
                    </a>
                    <a href="<?php echo N_URL; ?>eliminarTodasLasNotificaciones" class="btn btn-danger rounded-pill">
                        <i class="fas fa-trash me-1"></i>Eliminar todas las notificaciones
                    </a>
                </div>
            
        </div>
    </div>
</div>

<style>
.notification-card {
    transition: all 0.3s ease;
}
.notification-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.list-group-item {
    transition: all 0.2s ease;
}
.list-group-item:hover {
    background-color: #f8f9fa;
}
.list-group-item.active:hover {
    background-color: #0d6efd;
}
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
.rounded-pill {
    border-radius: 50rem !important;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>

