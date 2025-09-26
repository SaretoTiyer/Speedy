<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Agregar nuevo usuario</h3>
                    <form action="<?php echo S_URL_U ?>insertar" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Usuario</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-4">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="usuario">Usuario</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                        <button type="submit" name="insertar" class="btn btn-primary w-100">Agregar Usuario</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Listado de Usuarios</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($datosUsuarios): ?>
                                    <?php foreach($datosUsuarios as $u): ?>
                                        <tr>
                                            <td><?php echo $u->getId(); ?></td>
                                            <td><?php echo $u->getNombre(); ?></td>
                                            <td><?php echo $u->getEmail(); ?></td>
                                            <td><?php echo $u->getDireccion(); ?></td>
                                            <td><?php echo $u->getTelefono(); ?></td>
                                            <td><span class="badge bg-<?php echo $u->getIdRol() == 1 ? 'primary' : 'secondary'; ?>"><?php echo $u->getIdRol(); ?></span></td>
                                            <td>
                                                <a href="?controlador=usuarios&accion=actualizarU&id=<?php echo $u->getId(); ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                                                <a href="?controlador=usuarios&accion=borrarU&id=<?php echo $u->getId(); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de querer eliminar este usuario?');">Borrar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No hay usuarios registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
