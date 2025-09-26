<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de la Tienda - <?php echo S_NAME; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Estadísticas de la Tienda</h1>

        <!-- Sección de estadísticas inmediatas -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pedidos Hoy</h5>
                        <p class="card-text display-4"><?php echo $estadisticasInmediatas['pedidosHoy']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ingresos Hoy</h5>
                        <p class="card-text display-4">$<?php echo number_format($estadisticasInmediatas['ingresosHoy'], 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Productos Vendidos Hoy</h5>
                        <p class="card-text display-4"><?php echo $estadisticasInmediatas['productosVendidosHoy']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total de Pedidos</h5>
                        <p class="card-text display-4"><?php echo $datos['totalPedidos']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ingresos Totales</h5>
                        <p class="card-text display-4">$<?php echo number_format($datos['ingresosTotales'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ventas por Mes</h5>
                        <canvas id="ventasPorMes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pedidos Recientes</h5>
                        <ul class="list-group">
                            <?php foreach ($datos['pedidosRecientes'] as $pedido): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Pedido #<?php echo $pedido['id']; ?>
                                    <span class="badge bg-primary rounded-pill">$<?php echo number_format($pedido['valor_total'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Productos Más Vendidos</h5>
                        <ul class="list-group">
                            <?php foreach ($datos['productosMasVendidos'] as $producto): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $producto['nombre']; ?>
                                    <span class="badge bg-success rounded-pill"><?php echo $producto['cantidad_vendida']; ?> vendidos</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de ventas por mes
        var ctx = document.getElementById('ventasPorMes').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($datos['ventasPorMes'], 'mes')); ?>,
                datasets: [{
                    label: 'Ventas por Mes',
                    data: <?php echo json_encode(array_column($datos['ventasPorMes'], 'total')); ?>,
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
    </script>
</body>
</html>
