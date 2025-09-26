<?php 
    if (!isset($_SESSION['usuario'])) {
        header("Location: index.php?controlador=paginas&accion=login");
        exit();
    }

    // include_once "./controladores/controlador_tienda.php";
    // $objIngresoTienda = new ControladorTienda();
    // $respuesta = $objIngresoTienda->registrarTienda();
?> 

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Registro de Tienda en SPEEDY</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo S_URL_T ?>registrarTienda" method="post" enctype="multipart/form-data" onsubmit="this.querySelector('input[type=submit]').disabled=true;">

                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label">Nombre de la Tienda</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese el nombre de la tienda" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingrese la dirección de la tienda" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="ej: 1234567890" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo" placeholder="abc@mail.com" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="********" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="foto" class="form-label">Foto de la Tienda</label>
                            <input type="file" class="form-control" name="foto" id="foto" accept="image/*" required>
                            <small class="form-text text-muted">Suba una imagen de la tienda</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="apertura" class="form-label">Hora de Apertura</label>
                            <input type="time" class="form-control" name="apertura" id="apertura" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="cierre" class="form-label">Hora de Cierre</label>
                            <input type="time" class="form-control" name="cierre" id="cierre" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="fecha_apertura" class="form-label">Fecha de Apertura</label>
                            <input type="date" class="form-control" name="fecha_apertura" id="fecha_apertura" required>
                        </div>

                        <div class="d-grid">
                            <input name="login" class="btn btn-primary btn-block" type="submit" value="Registrar Tienda">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
