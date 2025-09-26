<?php
if (!isset($pedido) || empty($pedido) || !isset($detallePedido) || empty($detallePedido)) {
    echo "<div class='alert alert-danger'>No se pudo cargar la información del pedido.</div>";
    exit;
}
if (isset($_SESSION['warning'])) {
    echo "<div class='alert alert-warning'>" . $_SESSION['warning'] . "</div>";
    unset($_SESSION['warning']);
}
if (isset($_SESSION['info'])) {
    echo "<div class='alert alert-info'>" . $_SESSION['info'] . "</div>";
    unset($_SESSION['info']);
}
?>

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-gradient-primary text-white">
            <h2 class="mb-0 font-weight-bold">Pedido #<?php echo htmlspecialchars($pedido['id'] ?? 'N/A'); ?></h2>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4">Información del Pedido</h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <span class="fw-bold">Fecha</span>
                                    <span class="badge bg-secondary rounded-pill"><?php echo isset($pedido['fecha_hora_pedido']) ? date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])) : 'N/A'; ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <span class="fw-bold">Estado</span>
                                    <span class="badge bg-<?php echo getStatusColor($pedido['estado'] ?? ''); ?> rounded-pill"><?php echo ucfirst(htmlspecialchars($pedido['estado'] ?? 'N/A')); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                    <span class="fw-bold">Valor Total</span>
                                    <span class="badge bg-success rounded-pill">$<?php echo number_format($pedido['valor_pedido'] ?? 0, 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title text-primary mb-4">Detalles de Entrega</h4>
                            <p><i class="fas fa-map-marker-alt text-muted me-2"></i> <strong>Dirección:</strong> <?php echo htmlspecialchars($pedido['direccion_entrega'] ?? 'N/A'); ?></p>
                            <p><i class="fas fa-comment-alt text-muted me-2"></i> <strong>Comentarios:</strong> <?php echo htmlspecialchars($pedido['comentarios'] ?? 'N/A'); ?></p>
                            <?php if (isset($pedido['fecha_entrega']) && $pedido['fecha_entrega']): ?>
                                <p><i class="fas fa-calendar-check text-muted me-2"></i> <strong>Fecha de Entrega:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_entrega'])); ?></p>
                            <?php endif; ?>
                            <?php if (isset($pedido['calificacion'])): ?>
                                <p><i class="fas fa-star text-warning me-2"></i> <strong>Calificación:</strong> 
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $pedido['calificacion'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-primary mb-4">Productos del Pedido</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($detallePedido as $item): 
                                    $subtotal = $item['cantidad'] * $item['precio_unitario'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                                        <td><?php echo $item['cantidad']; ?></td>
                                        <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>$<?php echo number_format($total, 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer bg-light">
            <a href="<?php echo S_URL_PED ?>pedidosTienda" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a la lista de pedidos
            </a>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .table {
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }
    .table tbody tr {
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border-radius: 0.25rem;
    }
    .table tbody tr:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
