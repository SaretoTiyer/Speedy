<?php if (empty($notificaciones)): ?>
    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>No hay notificaciones en este momento.
    </div>
<?php else: ?>
    <?php foreach ($notificaciones as $notificacion): ?>
        <?php if ($tipoUsuario === 'administrador' || $notificacion['tipo'] !== 'solicitud'): ?>
            <?php if ($tipoNotificacion === 'todas' || $notificacion['tipo'] === $tipoNotificacion): ?>
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
                            <?php if ($tipoUsuario == 'domiciliario' && in_array($notificacion['tipo'], ['pedido', 'entrega'])): ?>
                                <button onclick="aceptarEntrega(<?php echo $notificacion['id']; ?>)" class="btn btn-success btn-sm rounded-pill">
                                    <i class="fas fa-check me-1"></i>Aceptar Entrega
                                </button>
                            <?php else: ?>
                                <button onclick="marcarComoLeido(<?php echo $notificacion['id']; ?>)" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="fas fa-check me-1"></i>Marcar como leído
                                </button>
                            <?php endif; ?>
                            <button onclick="eliminarNotificacion(<?php echo $notificacion['id']; ?>)" class="btn btn-danger btn-sm rounded-pill">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
