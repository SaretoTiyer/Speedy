<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="card-title mb-0">Listado de Domiciliarios</h2>
                    <a href="<?php echo C_URL ?>crear_domiciliario" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Domiciliario
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Vehículo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($domiciliarios as $domiciliario): ?>
                                <tr>
                                    <td><?php echo $domiciliario['id']; ?></td>
                                    <td><?php echo $domiciliario['nombre']; ?></td>
                                    <td><?php echo $domiciliario['apellido']; ?></td>
                                    <td><?php echo $domiciliario['telefono']; ?></td>
                                    <td><?php echo $domiciliario['correo']; ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?php echo $domiciliario['vehiculo']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo C_URL ?>editar_domiciliario&id=<?php echo $domiciliario['id']; ?>" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo C_URL ?>eliminar_domiciliario&id=<?php echo $domiciliario['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Está seguro de eliminar este domiciliario?')" data-bs-toggle="tooltip" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
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
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
