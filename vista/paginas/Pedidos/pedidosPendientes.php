<div class="container mt-5">
    <h2 class="mb-4">Pedidos Pendientes</h2>
    <?php if (empty($datos['pedidosPendientes'])): ?>
        <div class="alert alert-info">No hay pedidos pendientes en este momento.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['pedidosPendientes'] as $pedido): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['fecha_hora_pedido']); ?></td>
                            <td>$<?php echo isset($pedido['valor_pedido']) ? number_format($pedido['valor_pedido'], 2) : 'N/A'; ?></td>
                            <td>
                                <a href="<?php echo S_URL_PED; ?>detallePedido&id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm">Ver Detalles</a>
                                <a href="<?php echo S_URL_PED; ?>aceptarPedido&id=<?php echo $pedido['id']; ?>" class="btn btn-success btn-sm">Aceptar</a>
                                <a href="<?php echo S_URL_PED; ?>rechazarPedido&id=<?php echo $pedido['id']; ?>" class="btn btn-danger btn-sm">Rechazar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
