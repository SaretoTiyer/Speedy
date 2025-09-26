<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil de Tienda - <?php echo S_NAME; ?></title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .form-title {
            color: #007bff;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 10px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-primary {
            border-radius: 10px;
            padding: 0.5rem 2rem;
        }
        .current-photo-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 2rem;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .current-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .current-photo-container:hover .photo-overlay {
            opacity: 1;
        }
        .photo-text {
            color: white;
            font-size: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 form-container">
                <h1 class="text-center form-title">Editar Perfil de Tienda</h1>

                <?php if (isset($_SESSION['error_mensaje'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error_mensaje']; unset($_SESSION['error_mensaje']); ?></div>
                <?php endif; ?>

                <form action="index.php?controlador=tienda&accion=editarPerfil" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <div class="current-photo-container">
                            <img src="<?php echo htmlspecialchars(BASE_URL . ltrim($datosTienda['tienda_foto'], '/')); ?>" class="current-photo" alt="Foto de perfil actual de <?php echo htmlspecialchars($datosTienda['nombre']); ?>">
                            <div class="photo-overlay">
                                <span class="photo-text">Foto de Perfil Actual</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="nombre" class="form-label">Nombre de la Tienda</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datosTienda['nombre']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($datosTienda['direccion']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datosTienda['telefono']); ?>" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="horario_apertura" class="form-label">Horario de Apertura</label>
                            <input type="time" class="form-control" id="horario_apertura" name="horario_apertura" value="<?php echo htmlspecialchars($datosTienda['horario_apertura']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="horario_cierre" class="form-label">Horario de Cierre</label>
                            <input type="time" class="form-control" id="horario_cierre" name="horario_cierre" value="<?php echo htmlspecialchars($datosTienda['horario_cierre']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="nueva_foto" class="form-label">Nueva Foto de Perfil</label>
                        <input type="file" class="form-control" id="nueva_foto" name="nueva_foto" accept="image/*">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var img = document.querySelector('.current-photo');
        img.onerror = function() {
            this.src = '<?php echo IMG_URL; ?>tiendas/defaultTienda.png';
            this.onerror = null;
        };
    });
    </script>
</body>
</html>
