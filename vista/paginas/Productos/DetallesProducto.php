<div class="container mt-4">
    <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="img-fluid">
        </div>
        <div class="col-md-6">
            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
            <p>Precio: $<?php echo number_format($producto['precio'], 2); ?></p>
            <form action="<?php echo S_URL_P ?>agregarAlCarrito" method="post">
                <input type="hidden" name="id_producto" value="<?php echo $producto['id']; ?>">
                <div class="form-group">
                    <label for="cantidad">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $producto['cantidad']; ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Agregar al Carrito</button>
            </form>
        </div>
    </div>
</div>

