<?php 
    if (!isset($_SESSION['usuario'])) {
        header("Location: index.php?controlador=paginas&accion=login");
        exit();
    }
    // Elimina estas líneas
    // include_once "./controladores/controlador_domiciliario.php";
    // $objIngresoDomiciliario = new ControladorDomiciliario();
    // $objIngresoDomiciliario->registrarDomi();
?> 

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Registro de Domiciliario en SPEEDY</h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo S_URL_D ?>registrarDomi" method="post" enctype="multipart/form-data" onsubmit="this.querySelector('input[type=submit]').disabled=true;">

                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese su nombre" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellido" id="apellido" placeholder="Ingrese su apellido" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="ej: 1234567890" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" id="correo" placeholder="domiciliario@mail.com" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="********" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="dom_foto" class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" name="dom_foto" id="dom_foto" accept="image/*" required>
                            <small class="form-text text-muted">Suba una imagen de perfil</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingrese su dirección" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="vehiculo" class="form-label">Vehículo</label>
                            <input type="text" class="form-control" name="vehiculo" id="vehiculo" placeholder="Ingrese su tipo de vehículo" required>
                        </div>

                        <div class="d-grid">
                            <input name="login" class="btn btn-primary btn-block" type="submit" value="Registrar Domiciliario">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>