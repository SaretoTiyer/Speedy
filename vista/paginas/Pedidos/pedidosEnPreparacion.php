<div class="container mt-5">
    <h2 class="mb-4">Pedidos en Preparación</h2>
    <?php if (empty($pedidosEnPreparacion)): ?>
        <div class="alert alert-info">No hay pedidos en preparación en este momento.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($pedidosEnPreparacion as $pedido): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            Pedido #<?php echo htmlspecialchars($pedido['id']); ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Cliente: <?php echo htmlspecialchars($pedido['nombre_cliente']); ?></h5>
                            <p class="card-text">Fecha: <?php echo htmlspecialchars($pedido['fecha_hora_pedido']); ?></p>
                            <p class="card-text">Total: $<?php echo number_format($pedido['total'], 2); ?></p>
                            <a href="<?php echo S_URL_PED; ?>verDetallePedido&id=<?php echo $pedido['id']; ?>" class="btn btn-info">Ver Detalles</a>
                            <a href="<?php echo S_URL_PED; ?>finalizarPreparacion&id=<?php echo $pedido['id']; ?>" class="btn btn-success">Finalizar Preparación</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

