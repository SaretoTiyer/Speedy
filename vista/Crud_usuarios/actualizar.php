<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Actualizar usuario</h3>
                    <form action="<?php echo S_URL_U ?>actualizarU&id=<?php echo $datosUsuario->getId(); ?>" method="POST">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID de usuario</label>
                            <input type="text" value="<?php echo $datosUsuario->getId(); ?>" class="form-control" id="id" readonly>
                            <input type="hidden" name="id" value="<?php echo $datosUsuario->getId(); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Usuario</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $datosUsuario->getNombre(); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $datosUsuario->getEmail(); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $datosUsuario->getDireccion(); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $datosUsuario->getTelefono(); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" name="rol" id="rol" required>
                                <option value="2" <?php echo $datosUsuario->getIdRol() == 2 ? 'selected' : ''; ?>>Usuario</option>
                                <option value="1" <?php echo $datosUsuario->getIdRol() == 1 ? 'selected' : ''; ?>>Administrador</option>
                                <option value="3" <?php echo $datosUsuario->getIdRol() == 3 ? 'selected' : ''; ?>>Tienda</option>
                                <option value="4" <?php echo $datosUsuario->getIdRol() == 4 ? 'selected' : ''; ?>>Domiciliario</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="actualizar" class="btn btn-primary">Actualizar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>

