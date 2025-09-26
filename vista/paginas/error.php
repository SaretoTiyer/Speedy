    <div class="container mt-5">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error</h4>
            <p><?php echo isset($error) ? $error : "Ha ocurrido un error inesperado."; ?></p>
            <hr>
            <p class="mb-0">Por favor, contacta al administrador si el problema persiste.</p>
        </div>
        <a href="<?php echo S_URL; ?>inicio" class="btn btn-primary">Volver al inicio</a>
