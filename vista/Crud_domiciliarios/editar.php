<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil de Domiciliario - <?php echo S_NAME; ?></title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 form-container">
                <h1 class="text-center form-title">Editar Perfil de Domiciliario</h1>

                <?php if (isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_mensaje'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error_mensaje']; unset($_SESSION['error_mensaje']); ?></div>
                <?php endif; ?>

                <?php
                // Verificar si hay un mensaje de error
                if (isset($error)) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
                ?>

                <form action="<?php echo C_URL; ?>editar_domiciliario" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $domiciliario['id']; ?>">
                    <input type="hidden" name="foto_actual" value="<?php echo htmlspecialchars($domiciliario['dom_foto']); ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($domiciliario['nombre']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="apellido" class="form-label">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($domiciliario['apellido']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($domiciliario['telefono']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($domiciliario['direccion']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="vehiculo" class="form-label">Vehículo</label>
                        <input type="text" class="form-control" id="vehiculo" name="vehiculo" value="<?php echo htmlspecialchars($domiciliario['vehiculo']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="nueva_foto" class="form-label">Nueva Foto de Perfil</label>
                        <input type="file" class="form-control" id="nueva_foto" name="nueva_foto" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Actualizar Domiciliario</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var img = document.querySelector('.current-photo');
        img.onerror = function() {
            this.src = '<?php echo IMG_URL; ?>domiciliarios/defaultDomiciliario.png';
            this.onerror = null;
        };
    });
    </script>
</body>
</html>
