<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-success text-white">
            <h2 class="mb-0">¡Pedido Confirmado!</h2>
        </div>
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
            </div>
            <h3 class="card-title text-center mb-4">Gracias por tu compra</h3>
            <p class="lead text-center">Tu pedido ha sido realizado con éxito.</p>
            <?php if (isset($pedido) && !empty($pedido)): ?>
                <p class="text-center">Número de pedido: <strong><?php echo htmlspecialchars($pedido['id']); ?></strong></p>
                
                <div class="mt-5">
                    <h4 class="mb-3">Detalles del Pedido:</h4>
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
                                <?php foreach ($detallesPedido as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                        <td>$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                        <td>$<?php echo number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>$<?php echo number_format($pedido['valor_pedido'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-center text-danger">Lo sentimos, no se pudo cargar la información del pedido.</p>
            <?php endif; ?>
            
            <div class="mt-5 text-center">
                <a href="<?php echo S_URL ?>inicio" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
        <div class="card-footer text-muted text-center">
            <small>Guarda este número de pedido para futuras referencias.</small>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 15px;
    }
    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
