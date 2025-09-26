<!doctype html>
<html lang="es">
    <head>
        <title>SPEEDY</title>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Estilos personalizados -->
        <link rel="stylesheet" href="vista/recursos/css/estilos.css" />
        <link rel="stylesheet" href="vista/recursos/css/footer.css" />
        <!-- Google Fonts - Rubik -->
        <link href="https://fonts.googleapis.com/css?family=Rubik" rel="stylesheet">
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Leaflet -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <!-- GSAP -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
        <!-- Calificacion -->
        <script src="vista/recursos/js/calificacion.js"></script>

    </head>

    <body class="d-flex flex-column min-vh-100">
        <header>
            <?php
                include_once("modulos/navbar.php");
            ?>
        </header>
        <main class="flex-grow-1">
                <?php 
                    include_once("./ruteador.php");
                    $controlador = isset($_GET['controlador']) ? $_GET['controlador'] : 'paginas';
                    $accion = isset($_GET['accion']) ? $_GET['accion'] : 'inicio';
                    ruteador($controlador, $accion);
                ?>
        </main>

        <?php include_once("modulos/footer.php"); ?>

        <!-- Bootstrap 5 Bundle con Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inicialización automática de todos los componentes de Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })

                // Inicialización de dropdowns
                var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl)
                })
            });
        </script>
        <!-- Script personalizado -->
        <script src="vista/recursos/js/main.js"></script>
    </body>
</html>
