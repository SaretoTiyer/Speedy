<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Editar Producto</h2>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form action="index.php?controlador=productos&accion=editarProducto&id=<?php echo $producto['id']; ?>" method="POST" enctype="multipart/form-data" id="formEditarProducto">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="precio" class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" value="<?php echo $producto['cantidad']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria['id'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen del Producto</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                                <div id="imagenPreview" class="mt-2">
                                    <?php if (!empty($producto['imagen'])): ?>
                                        <img src="<?php echo BASE_URL . $producto['imagen']; ?>" alt="Imagen actual del producto" class="img-thumbnail" style="max-width: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                                <a href="<?php echo S_URL_P; ?>listar" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formEditarProducto');
        const imagenInput = document.getElementById('imagen');
        const imagenPreview = document.getElementById('imagenPreview');

        // Previsualización de la imagen
        imagenInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagenPreview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" alt="Vista previa" style="max-width: 200px;">`;
                }
                reader.readAsDataURL(file);
            }
        });

        // Validación del formulario
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);

        // Confirmación antes de salir si hay cambios
        let formChanged = false;
        form.addEventListener('change', () => formChanged = true);

        window.addEventListener('beforeunload', (event) => {
            if (formChanged) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    });
    </script>
</body>
</html>
