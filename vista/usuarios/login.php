<?php 
    include_once "./controladores/controlador_usuarios.php";
    $objIngreso = new ControladorUsuarios();
    $objIngreso->ingreso();
?> 

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?php echo BASE_URL; ?>vista/recursos/img/logo.png" alt="SPEEDY Logo" class="img-fluid mb-3" style="max-width: 150px;">
                        <h2 class="text-primary">Ingreso a SPEEDY</h2>
                        <p class="text-muted">Fast and Secure</p>
                    </div>
                    
                    <form action="<?php echo S_URL_U ?>ingreso" method="post">
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
                            <button type="submit" name="login" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Ingresar
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 text-center">
                        <p class="mb-0">¿No tienes una cuenta? <a href="<?php echo S_URL ?>registro" class="text-primary">Regístrate aquí</a></p>
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
