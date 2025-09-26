<?php
error_log("Cargando vista PedidosTienda.php");
?>

<?php if(isset($_SESSION['exito'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['exito']; ?></div>
    <?php unset($_SESSION['exito']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="container mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="display-4 text-primary fw-bold">Pedidos de la Tienda</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="btn-group" role="group" aria-label="Filtros de pedidos">
                <a href="<?php echo S_URL_PED ?>pedidosTienda" class="btn btn-outline-primary"><i class="fas fa-list-ul me-2"></i>Todos</a>
                <a href="<?php echo S_URL_PED ?>pedidosTienda&estado=pendiente" class="btn btn-outline-warning"><i class="fas fa-clock me-2"></i>Pendientes</a>
                <a href="<?php echo S_URL_PED ?>pedidosTienda&estado=aceptado" class="btn btn-outline-info"><i class="fas fa-check-circle me-2"></i>Aceptados</a>
                <a href="<?php echo S_URL_PED ?>pedidosTienda&estado=en_preparacion" class="btn btn-outline-success"><i class="fas fa-cog me-2"></i>En Preparación</a>
                <a href="<?php echo S_URL_PED ?>pedidosTienda&estado=listo_para_entrega" class="btn btn-outline-secondary"><i class="fas fa-truck me-2"></i>Listos para Entrega</a>
            </div>
        </div>
    </div>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i>No hay pedidos para mostrar en este momento.
        </div>
    <?php else: ?>
        <div class="card shadow-lg border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">ID Pedido</th>
                                <th class="border-0">Fecha</th>
                                <th class="border-0">Estado</th>
                                <th class="border-0">Valor Total</th>
                                <th class="border-0">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr class="align-middle">
                                    <td class="fw-bold">#<?php echo $pedido['id']; ?></td>
                                    <td><i class="far fa-calendar-alt me-2 text-muted"></i><?php echo date('d/m/Y H:i', strtotime($pedido['fecha_hora_pedido'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getStatusColor($pedido['estado']); ?> rounded-pill px-3 py-2">
                                            <?php echo ucfirst($pedido['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">$<?php echo number_format($pedido['valor_pedido'], 2); ?></td>
                                    <td>
                                        <a href="<?php echo S_URL_PED . 'detallePedidoTienda&id=' . $pedido['id']; ?>" class="btn btn-sm btn-primary rounded-pill">
                                            <i class="fas fa-eye me-1"></i>Ver Detalle
                                        </a>
                                        <?php if ($pedido['estado'] == 'pendiente'): ?>
                                            <a href="<?php echo S_URL_PED . 'aceptarPedido&id=' . $pedido['id']; ?>" class="btn btn-sm btn-success rounded-pill">
                                                <i class="fas fa-check me-1"></i>Aceptar
                                            </a>
                                            <a href="<?php echo S_URL_PED . 'rechazarPedido&id=' . $pedido['id']; ?>" class="btn btn-sm btn-danger rounded-pill">
                                                <i class="fas fa-times me-1"></i>Rechazar
                                            </a>
                                        <?php elseif ($pedido['estado'] == 'aceptado'): ?>
                                            <a href="<?php echo S_URL_PED . 'prepararPedido&id=' . $pedido['id']; ?>" class="btn btn-sm btn-info rounded-pill">
                                                <i class="fas fa-utensils me-1"></i>Preparar
                                            </a>
                                        <?php elseif ($pedido['estado'] == 'en_preparacion'): ?>
                                            <a href="<?php echo S_URL_PED . 'finalizarPreparacion&id=' . $pedido['id']; ?>" class="btn btn-sm btn-warning rounded-pill">
                                                <i class="fas fa-check-double me-1"></i>Finalizar Preparación
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'pendiente':
            return 'warning';
        case 'aceptado':
            return 'primary';
        case 'en_preparacion':
            return 'info';
        case 'listo_para_entrega':
            return 'success';
        case 'entregado':
            return 'secondary';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        gsap.from('.card', {duration: 0.6, y: 50, opacity: 0, stagger: 0.2, ease: "power3.out"});
        gsap.from('tbody tr', {duration: 0.4, opacity: 0, y: 20, stagger: 0.1, ease: "power2.out", delay: 0.4});
    });
</script>

<style>
    body {
        background-color: #f8f9fa;
    }
    .container {
        max-width: 1200px;
    }
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
    }
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-weight: 500;
    }
    .btn-sm {
        padding: 0.4rem 1rem;
    }
    .btn-group .btn {
        border-radius: 30px !important;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    .btn-group .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .table tbody tr {
        transition: all 0.3s ease;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }
</style>
