<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
    header("Location: index.php?controlador=usuarios&accion=login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas del Domiciliario - <?php echo htmlspecialchars($datos['domiciliario']['nombre']); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Estadísticas de <?php echo htmlspecialchars($datos['domiciliario']['nombre'] . ' ' . $datos['domiciliario']['apellido']); ?></h1>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total de Entregas</h5>
                        <p class="card-text display-4"><?php echo $datos['estadisticas']['totalEntregas']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Calificación Promedio</h5>
                        <p class="card-text display-4"><?php echo number_format($datos['estadisticas']['calificacionPromedio'], 1); ?> <small>/5</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Tiempo Promedio de Entrega</h5>
                        <p class="card-text display-4"><?php echo $datos['estadisticas']['tiempoPromedioEntrega']; ?> <small>min</small></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Ingresos Totales</h5>
                        <p class="card-text display-4">$<?php echo number_format($datos['estadisticas']['ingresosTotales'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pedidos por Mes</h5>
                        <canvas id="pedidosPorMes"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Estado de Pedidos</h5>
                        <canvas id="estadoPedidos"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pedidos Recientes</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos['estadisticas']['pedidosRecientes'] as $pedido): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['fecha_hora_pedido']); ?></td>
                                        <td><?php echo htmlspecialchars($pedido['estado']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Pedidos por Mes
        var ctxPedidosPorMes = document.getElementById('pedidosPorMes').getContext('2d');
        var chartPedidosPorMes = new Chart(ctxPedidosPorMes, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($datos['estadisticas']['pedidosPorMes'], 'mes')); ?>,
                datasets: [{
                    label: 'Pedidos por Mes',
                    data: <?php echo json_encode(array_column($datos['estadisticas']['pedidosPorMes'], 'total')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Estado de Pedidos
        var ctxEstadoPedidos = document.getElementById('estadoPedidos').getContext('2d');
        var chartEstadoPedidos = new Chart(ctxEstadoPedidos, {
            type: 'pie',
            data: {
                labels: ['Pendientes', 'Entregados'],
                datasets: [{
                    data: [<?php echo $datos['estadisticas']['pedidosPendientes']; ?>, <?php echo $datos['estadisticas']['pedidosEntregados']; ?>],
                    backgroundColor: ['rgb(255, 205, 86)', 'rgb(75, 192, 192)']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>