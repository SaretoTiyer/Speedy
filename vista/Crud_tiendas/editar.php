<h2>Editar Tienda</h2>
<form action="<?php echo C_URL ?>editar_tienda" method="POST">
    <input type="hidden" name="id" value="<?php echo $tienda['id']; ?>">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $tienda['nombre']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="direccion" class="form-label">Dirección</label>
        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $tienda['direccion']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $tienda['telefono']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $tienda['correo']; ?>" required>
    </div>
    <div class="mb-3">
        <label for="tienda_foto" class="form-label">Foto de la Tienda</label>
        <input type="file" class="form-control" id="tienda_foto" name="tienda_foto">
    </div>
    <button type="submit" class="btn btn-primary">Actualizar Tienda</button>
</form>