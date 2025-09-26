<?php
    session_start();
    include_once "./conexion.php"; 
    require_once './controladores/controlador_notificaciones.php';

    $usr = "visitante";
    $contadorNotificaciones = 0;

    if (isset($_SESSION['usuario']) && is_array($_SESSION["usuario"]) && isset($_SESSION["usuario"]['id_rol'])) {
        $idRol = $_SESSION["usuario"]['id_rol'];

        $pdo = BaseDatos::crearInstancia();


        $stmt = $pdo->prepare ("SELECT nombre FROM rol WHERE id = :id_rol");
        $stmt->bindParam(':id_rol', $idRol, PDO::PARAM_INT);
        $stmt->execute();

        $rol = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rol) {
            $usr = $rol['nombre'];
        }

        if ($usr == "administrador") {
            $controladorNotificaciones = new ControladorNotificaciones();
            $contadorNotificaciones = $controladorNotificaciones->obtenerContadorNotificaciones();
        }

        if ($usr == "usuario") {
            $controladorNotificaciones = new ControladorNotificaciones();
            $contadorNotificaciones = $controladorNotificaciones->obtenerContadorNotificaciones();
        }

        if ($usr == "tienda") {
            $controladorNotificaciones = new ControladorNotificaciones();
            $contadorNotificaciones = $controladorNotificaciones->obtenerContadorNotificaciones();
        }

        if ($usr == "domiciliario") {
            $controladorNotificaciones = new ControladorNotificaciones();
            $contadorNotificaciones = $controladorNotificaciones->obtenerContadorNotificaciones();
        }
    }
?>
<?php
    if ($usr == "administrador"){
?>
<!-- Navbar de Administrador -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(135deg, #5EFCE8 10%, #736EFE 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo S_URL ?>inicio">
            <img src="vista/recursos/img/logo.png" width="55" height="55" alt="Logo" class="d-inline-block align-text-top">
            SPEEDY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin" aria-controls="navbarAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo C_URL ?>mostrar_usuarios">
                        <i class="fas fa-users"></i> Gestionar Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo C_URL ?>mostrar_tiendas">
                        <i class="fas fa-store"></i> Gestionar Tiendas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo C_URL ?>mostrar_domiciliarios">
                        <i class="fas fa-motorcycle"></i> Gestionar Domiciliarios
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo N_URL ?>mostrarNotificaciones">
                        <i class="fas fa-bell"></i>
                        <?php if ($contadorNotificaciones > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $contadorNotificaciones; ?>
                                <span class="visually-hidden">notificaciones no leídas</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-shield"></i> <?php echo $_SESSION["usuario"]['nombre']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="./vista/modulos/salir.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
    }
?>
<!-- Fin del navbar de Administrador -->

<?php 
    if ($usr == "usuario"){
?>
<!-- Navbar de Usuario -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(135deg, #5EFCE8 10%, #736EFE 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo S_URL ?>inicio">
            <img src="vista/recursos/img/logo.png" width="55" height="55" alt="Logo" class="d-inline-block align-text-top">
            SPEEDY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUsuario" aria-controls="navbarUsuario" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarUsuario">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL ?>inicio">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_P ?>catalogo">
                        <i class="fas fa-store"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_T ?>listarTiendas">
                        <i class="fas fa-store"></i> Tiendas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_PED ?>verCarrito">
                        <i class="fas fa-shopping-cart"></i> Carrito
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_PED ?>verPedidos">
                        <i class="fas fa-list"></i> Mis Pedidos
                    </a>
                </li>
                
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo N_URL ?>mostrarNotificaciones">
                        <i class="fas fa-bell"></i>
                        <?php if (isset($contadorNotificaciones) && $contadorNotificaciones > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $contadorNotificaciones; ?>
                                <span class="visually-hidden">notificaciones no leídas</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo $_SESSION["usuario"]['nombre']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="./vista/modulos/salir">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Fin del navbar de Usuario -->
<?php 
    }
?>
<?php 
    if ($usr == "visitante"){
?>
<!-- Navbar de Visitante -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(135deg, #5EFCE8 10%, #736EFE 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo S_URL ?>inicio">
            <img src="vista/recursos/img/logo.png" width="55" height="55" alt="Logo" class="d-inline-block align-text-top">
            SPEEDY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVisitante" aria-controls="navbarVisitante" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarVisitante">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item separated-link">
                    <a class="nav-link d-flex flex-column align-items-center" href="<?php echo S_URL ?>inicio">
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="nav-item separated-link">
                    <a class="nav-link d-flex flex-column align-items-center" href="<?php echo S_URL ?>nosotros">
                        <i class="fas fa-users"></i>
                        <span>Nosotros</span>
                    </a>
                </li>
                <li class="nav-item separated-link">
                    <a class="nav-link d-flex flex-column align-items-center" href="<?php echo S_URL ?>contacto">
                        <i class="fas fa-envelope"></i>
                        <span>Contacto</span>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownVisitante" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> Usuario
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownVisitante">
                        <li><a class="dropdown-item" href="<?php echo S_URL ?>login">Iniciar Sesión</a></li>
                        <li><a class="dropdown-item" href="<?php echo S_URL ?>registro">Registrarse</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Fin del navbar de Visitante -->
<?php 
    }
?>
<?php 
    if ($usr == "tienda"){
?>
<!-- Navbar de Tienda -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(135deg, #5EFCE8 10%, #736EFE 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo S_URL ?>inicio">
            <img src="vista/recursos/img/logo.png" width="55" height="55" alt="Logo" class="d-inline-block align-text-top">
            SPEEDY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTienda" aria-controls="navbarTienda" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTienda">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_T ?>panel">
                        <i class="fas fa-tachometer-alt"></i> Panel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_P ?>agregarProducto">
                        <i class="fas fa-plus-circle"></i> Agregar Producto
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_P ?>listar">
                        <i class="fas fa-list"></i> Mis Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_PED ?>pedidosTienda">
                        <i class="fas fa-clipboard-list"></i> Pedidos
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo N_URL ?>mostrarNotificaciones">
                        <i class="fas fa-bell"></i>
                        <?php if ($contadorNotificaciones > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $contadorNotificaciones; ?>
                                <span class="visually-hidden">notificaciones no leídas</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo $_SESSION["usuario"]['nombre']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="<?php echo S_URL_T ?>perfilTienda">Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="./vista/modulos/salir">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Fin del navbar de Tienda -->
<?php
    }
?>
<?php 
    if ($usr == "domiciliario"){
?>
<!-- Navbar de Domiciliario -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-image: linear-gradient(135deg, #5EFCE8 10%, #736EFE 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo S_URL ?>inicio">
            <img src="vista/recursos/img/logo.png" width="55" height="55" alt="Logo" class="d-inline-block align-text-top">
            SPEEDY
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarDomiciliario" aria-controls="navbarDomiciliario" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarDomiciliario">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_D ?>panel">
                        <i class="fas fa-tachometer-alt"></i> Panel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_D ?>misPedidos">
                        <i class="fas fa-motorcycle"></i> Mis Pedidos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo S_URL_D ?>historialPedidos">
                        <i class="fas fa-history"></i> Historial de Pedidos
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo N_URL ?>mostrarNotificaciones">
                        <i class="fas fa-bell"></i>
                        <?php if (isset($contadorNotificaciones) && $contadorNotificaciones > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $contadorNotificaciones; ?>
                                <span class="visually-hidden">notificaciones no leídas</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo $_SESSION["usuario"]['nombre']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                        <li><a class="dropdown-item" href="<?php echo S_URL_D ?>perfilDomiciliario">Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="./vista/modulos/salir">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Fin del navbar de Domiciliario -->
<?php
    }
?>