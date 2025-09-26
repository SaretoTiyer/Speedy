<?php

// Al principio del archivo
/*error_log("Iniciando carga del carrito");
error_log("Contenido del carrito: " . print_r($_SESSION['carrito'], true));

// Función para verificar si un archivo existe y tiene permisos de lectura
function verificarArchivo($ruta) {
    error_log("Verificando archivo: " . $ruta);
    if (file_exists($ruta)) {
        error_log("El archivo existe: " . $ruta);
        if (is_readable($ruta)) {
            error_log("El archivo tiene permisos de lectura: " . $ruta);
            return true;
        } else {
            error_log("El archivo NO tiene permisos de lectura: " . $ruta);
            return false;
        }
    } else {
        error_log("El archivo NO existe: " . $ruta);
        return false;
    }
}*/

// Asegúrate de que $carrito esté definido
if (!isset($carrito)) {
    $carrito = [];
    error_log("Carrito no definido, se ha inicializado como un array vacío");
}
?>

<style>
    .product-image {
        width: 60px;
        height: 60px;
        overflow: hidden;
        border-radius: 5px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .no-image {
        width: 100%;
        height: 100%;
        background-color: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .no-image::after {
        content: 'No image';
        color: #6c757d;
        font-size: 0.75rem;
    }

    .table td {
        vertical-align: middle;
    }
</style>
<div class="container mt-5">
    <h2 class="text-center mb-4">Tu Carrito de Compras</h2>
    
    <?php if (empty($carrito)): ?>
        <?php error_log("El carrito está vacío"); ?>
        <div class="text-center">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
            <p class="lead">Tu carrito está vacío.</p>
            <a href="<?php echo S_URL_P ?>catalogo" class="btn btn-primary">Ir a Comprar</a>
        </div>
    <?php else: ?>
        <?php error_log("El carrito tiene " . count($carrito) . " item(s)"); ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Producto</th>
                                <th style="width: 20%;">Cantidad</th>
                                <th style="width: 15%;">Precio Unitario</th>
                                <th style="width: 15%;">Subtotal</th>
                                <th style="width: 10%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carrito as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (isset($item['imagen']) && !empty($item['imagen'])): ?>
                                                <img src="<?php echo $item['imagen']; ?>" 
                                                     alt="<?php echo htmlspecialchars($item['nombre']); ?>" 
                                                     class="img-thumbnail mr-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-secondary mr-3" style="width: 50px; height: 50px;"></div>
                                            <?php endif; ?>
                                            <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="<?php echo S_URL_PED ?>actualizarCantidad" method="post" class="d-flex align-items-center">
                                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" max="99" class="form-control form-control-sm" style="width: 60px;">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary ml-2">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                    <td>$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></td>
                                    <td>
                                        <a href="<?php echo S_URL_PED ?>eliminarDelCarrito&id=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="<?php echo S_URL_P ?>catalogo" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left mr-2"></i>Seguir Comprando
                        </a>
                    </div>
                    <div class="col-md-6 text-right">
                        <h4 class="mb-3">Total: $<?php echo number_format(array_sum(array_map(function($item) { return $item['precio'] * $item['cantidad']; }, $carrito)), 2); ?></h4>
                        <a href="<?php echo S_URL_PED ?>crearPedido" class="btn btn-success btn-lg">
                            Realizar Pedido <i class="fas fa-check ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const errorMsg = document.createElement('small');
            errorMsg.textContent = 'Error cargando imagen';
            errorMsg.className = 'text-danger';
            this.parentNode.appendChild(errorMsg);
        });
    });
});
</script>

<?php
// Al final del archivo
/*error_log("Finalizada la carga del carrito");*/
?>
