<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
    header("Location: " . S_URL . "login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-custom {
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Detalle del Pedido #<?php echo htmlspecialchars($pedido['id'] ?? 'N/A'); ?></h1>

        <div class="card">
            <div class="card-header">
                <h2>Información del Pedido</h2>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_usuario'] ?? 'No especificado'); ?></p>
                <p><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars($pedido['fecha_hora_pedido'] ?? 'No especificada'); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($pedido['estado'] ?? 'Desconocido'); ?></p>
                <p><strong>Dirección de Entrega:</strong> <?php echo htmlspecialchars($pedido['direccion_entrega'] ?? 'No especificada'); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($pedido['valor_pedido'] ?? 0, 2); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Detalles del Pedido</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($detallesPedido)): ?>
                    <table class="table">
                        <thead>
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
                                <td><?php echo htmlspecialchars($detalle['nombre_producto'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($detalle['cantidad'] ?? '0'); ?></td>
                                <td>$<?php echo number_format($detalle['precio_unitario'] ?? 0, 2); ?></td>
                                <td>$<?php echo number_format(($detalle['cantidad'] ?? 0) * ($detalle['precio_unitario'] ?? 0), 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay detalles disponibles para este pedido.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <?php if (isset($pedido['estado']) && $pedido['estado'] == 'en_camino'): ?>
            <a href="<?php echo S_URL_PED; ?>confirmarEntrega&id=<?php echo $pedido['id'] ?? ''; ?>" class="btn btn-success btn-lg btn-custom">Confirmar Entrega</a>
            <?php endif; ?>
            <a href="<?php echo S_URL_D; ?>misPedidos" class="btn btn-secondary btn-lg btn-custom">Volver a Mis Pedidos</a>
        </div>
    </div>
</body>
</html>
