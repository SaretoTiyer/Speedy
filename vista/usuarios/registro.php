<?php 
    include_once("./modelos/usuarios.php");
?>  

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?php echo BASE_URL; ?>vista/recursos/img/logo.png" alt="SPEEDY Logo" class="img-fluid mb-3" style="max-width: 150px;">
                        <h2 class="text-primary">Registro en SPEEDY</h2>
                        <p class="text-muted">Fast and Secure</p>
                    </div>
                    
                    <form action="<?php echo S_URL_U ?>crear" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="ej: 1234567890" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="ej: Barrio Santa margarita, CR107 60 C10">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email" id="email" placeholder="abc@mail.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="********" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="mb-0">¿Ya tienes una cuenta? <a href="<?php echo S_URL?>login" class="text-primary">Inicia sesión aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 1rem;
        border: none;
    }
    .card-body {
        padding: 3rem;
    }
    .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
    }
    .btn-primary:hover {
        background-color: #3a7bc8;
        border-color: #3a7bc8;
    }
</style>
