<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h2 class="card-title mb-0">Listado de Tiendas</h2>
                    <a href="<?php echo C_URL ?>crear_tienda" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nueva Tienda
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
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tiendas as $tienda): ?>
                                <tr>
                                    <td><?php echo $tienda['id']; ?></td>
                                    <td><?php echo $tienda['nombre']; ?></td>
                                    <td><?php echo $tienda['direccion']; ?></td>
                                    <td><?php echo $tienda['telefono']; ?></td>
                                    <td><?php echo $tienda['correo']; ?></td>
                                    <td>
                                        <a href="<?php echo C_URL ?>editar_tienda&id=<?php echo $tienda['id']; ?>" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo C_URL ?>eliminar_tienda&id=<?php echo $tienda['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Está seguro de eliminar esta tienda?')" data-bs-toggle="tooltip" title="Eliminar">
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
