<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center font-weight-light my-4">Detalles del Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="mb-3">Información del Pedido:</h6>
                            <div><strong>Fecha:</strong> <?php echo htmlspecialchars($pedido['fecha_hora_pedido']); ?></div>
                            <div><strong>Estado:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($pedido['estado']); ?></span></div>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="mb-3">Total del Pedido:</h6>
                            <div class="display-4 text-primary">$<?php echo number_format($pedido['valor_pedido'], 2); ?></div>
                        </div>
                    </div>

                    <h5 class="mb-3">Productos</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles_pedido as $detalle): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                                    <td class="text-end">$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                    <td class="text-end">$<?php echo number_format($detalle['cantidad'] * $detalle['precio_unitario'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">$<?php echo number_format($pedido['valor_pedido'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center py-3">
                    <a href="<?php echo S_URL_PED; ?>verPedidos" class="btn btn-primary">Volver a Mis Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
