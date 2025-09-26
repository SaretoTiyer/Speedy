<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
    header("Location: " . S_URL . "login");
    exit();
}
?>

<div class="container mt-4">
    <h1 class="mb-4 text-center">Gestión de Pedidos</h1>

    <!-- Pedidos Disponibles -->
    <!--<div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="h4 mb-0">Pedidos Disponibles</h2>
        </div>
        <div class="card-body">
            <?php //if (empty($pedidosDisponibles)): ?>
                <p class="text-center">No hay pedidos disponibles en este momento.</p>
            <?php //else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Dirección de Entrega</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php //foreach ($pedidosDisponibles as $pedido): ?>
                            <tr>
                                <td><?php //echo isset($pedido['id']) ? htmlspecialchars($pedido['id']) : 'N/A'; ?></td>
                                <td><?php //echo isset($pedido['cliente']) ? htmlspecialchars($pedido['cliente']) : 'No especificado'; ?></td>
                                <td><?php //echo isset($pedido['fecha']) ? htmlspecialchars($pedido['fecha']) : 'No especificada'; ?></td>
                                <td><?php //echo isset($pedido['direccion_entrega']) ? htmlspecialchars($pedido['direccion_entrega']) : 'No especificada'; ?></td>
                                <td>
                                    <span class="badge bg-<?php //cho $pedido['estado'] == 'pendiente' ? 'warning' : 'info'; ?>">
                                        <?php //echo isset($pedido['estado']) ? htmlspecialchars(ucfirst($pedido['estado'])) : 'Desconocido'; ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?php //echo S_URL_PED; ?>asignarPedidoDomiciliario" method="POST">
                                        <input type="hidden" name="id_pedido" value="<?php //echo isset($pedido['id']) ? $pedido['id'] : ''; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-hand-pointer me-1"></i> Asignarme
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php //endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php //endif; ?>
        </div>
    </div>-->

    <!-- Pedidos Asignados -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h2 class="h4 mb-0">Mis Pedidos Asignados</h2>
        </div>
        <div class="card-body">
            <?php if (empty($pedidosAsignados)): ?>
                <p class="text-center">No hay pedidos asignados en este momento.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pedido</th>
                                <th>Dirección de Entrega</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidosAsignados as $pedido): ?>
                            <tr>
                                <td><?php echo isset($pedido['id']) ? htmlspecialchars($pedido['id']) : 'N/A'; ?></td>
                                <td><?php echo isset($pedido['direccion_entrega']) ? htmlspecialchars($pedido['direccion_entrega']) : 'No especificada'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $pedido['estado'] == 'en_camino' ? 'info' : 'success'; ?>">
                                        <?php echo isset($pedido['estado']) ? htmlspecialchars(ucfirst(str_replace('_', ' ', $pedido['estado']))) : 'Desconocido'; ?>
                                    </span>
                                </td>
                                <td>
                                <a href="<?php echo S_URL_PED; ?>verDetallePedidoDomiciliario&id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm btn-custom">
                                        <i class="fas fa-eye me-1"></i> Ver Detalles
                                    </a>
                                    <?php if (isset($pedido['estado']) && $pedido['estado'] == 'en_camino'): ?>
                                    <a href="<?php echo S_URL_PED; ?>confirmarEntrega&id=<?php echo isset($pedido['id']) ? $pedido['id'] : ''; ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i> Confirmar Entrega
                                    </a>
                                    <?php endif; ?>
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

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .card-header {
        border-bottom: 0;
    }
    .table {
        margin-bottom: 0;
    }
    .btn {
        border-radius: 20px;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.7em;
    }
</style>
