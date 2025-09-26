<?php
function mostrarTablaPedidos($pedidos, $mostrarCalificacion = false) {
    ?>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-4">ID Pedido</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Tienda</th>
                    <th>Valor Total</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td class="px-4"><strong>#<?php echo htmlspecialchars($pedido['id']); ?></strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])); ?></td>
                        <td>
                            <span class="badge rounded-pill bg-<?php echo getStatusColor($pedido['estado']); ?>">
                                <?php echo ucfirst(htmlspecialchars($pedido['estado'])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($pedido['nombre_tienda']); ?></td>
                        <td>$<?php echo number_format($pedido['valor_pedido'], 2); ?></td>
                        <td class="text-center">
                            <a href="<?php echo S_URL_PED ?>ver_detalle_pedido&id=<?php echo $pedido['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver Detalles
                            </a>
                            <?php if ($mostrarCalificacion && $pedido['estado'] == 'entregado' && !$pedido['ya_calificado']): ?>
                                <a href="<?php echo S_URL_PED ?>calificarPedido&id=<?php echo $pedido['id']; ?>" class="btn btn-outline-success btn-sm ms-2">
                                    <i class="fas fa-star me-1"></i>Calificar
                                </a>
                            <?php elseif ($pedido['ya_calificado']): ?>
                                <span class="badge bg-secondary ms-2">Ya calificado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

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
<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-lg mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h2 class="mb-0"><i class="fas fa-list-alt me-2"></i>Mis Pedidos en Curso</h2>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pedidosEnCurso)): ?>
                <div class="text-center py-5">
                    <p class="lead">No tienes pedidos en curso en este momento.</p>
                </div>
            <?php else: ?>
                <?php mostrarTablaPedidos($pedidosEnCurso); ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-gradient-success text-white">
            <h2 class="mb-0"><i class="fas fa-check-circle me-2"></i>Mis Pedidos Entregados</h2>
        </div>
        <div class="card-body p-0">
            <?php if (empty($pedidosEntregados)): ?>
                <div class="text-center py-5">
                    <p class="lead">No tienes pedidos entregados para calificar en este momento.</p>
                </div>
            <?php else: ?>
                <?php mostrarTablaPedidos($pedidosEntregados, true); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        padding: 1.5rem;
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #007bff, #6610f2);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #28a745, #20c997);
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.75em;
    }
    .btn-outline-primary, .btn-outline-success {
        border-width: 2px;
    }
    .btn-outline-primary:hover, .btn-outline-success:hover {
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>

