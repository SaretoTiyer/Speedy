<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
    header("Location: " . S_URL . "login");
    exit();
}
?>

<div class="container mt-5">
    <h1 class="mb-4 text-primary">Historial de Pedidos</h1>

    <div class="card shadow">
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="buscarPedido" class="form-control" placeholder="Buscar pedido...">
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="pedidosTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID Pedido</th>
                            <th>Fecha y Hora</th>
                            <th>Cliente</th>
                            <th>Dirección de Entrega</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historialPedidos)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay pedidos en el historial.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historialPedidos as $pedido): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['fecha_hora_pedido']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['direccion_entrega']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $pedido['estado'] == 'entregado' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst(htmlspecialchars($pedido['estado'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo S_URL_PED; ?>verDetallePedidoDomiciliario&id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm btn-custom">
                                            Ver Detalles</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles del pedido -->
<div class="modal fade" id="pedidoDetallesModal" tabindex="-1" aria-labelledby="pedidoDetallesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pedidoDetallesModalLabel">Detalles del Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="pedidoDetallesContent">
                <!-- El contenido se cargará dinámicamente aquí -->
            </div>
        </div>
    </div>
</div>

<style>
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    .table thead th {
        background-color: #f8f9fa;
        border-top: none;
    }
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('pedidosTable');
    const modal = new bootstrap.Modal(document.getElementById('pedidoDetallesModal'));
    const buscarInput = document.getElementById('buscarPedido');

    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-details') || e.target.closest('.view-details')) {
            const button = e.target.classList.contains('view-details') ? e.target : e.target.closest('.view-details');
            const pedidoId = button.getAttribute('data-id');
            
            // Aquí deberías hacer una llamada AJAX para obtener los detalles del pedido
            // Por ahora, simularemos con un contenido estático
            document.getElementById('pedidoDetallesContent').innerHTML = `
                <p>Cargando detalles del pedido ${pedidoId}...</p>
            `;
            
            modal.show();
        }
    });

    buscarInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Animación suave al cargar la página
    gsap.from('.card', {duration: 1, y: 50, opacity: 0, ease: 'power3.out'});
});
</script>
