<?php
function ruteador($getcontrolador, $getaccion) {
    include_once "controladores/controlador_$getcontrolador.php";
    $controlador = "Controlador".ucfirst($getcontrolador);
    $objcontrolador = new $controlador();
    
    // Mapeo de acciones a métodos del controlador
    $accionesMap = [
        'agregar' => 'agregarProducto',
        // Agrega aquí otros mapeos si es necesario
    ];

    // Verifica si hay un mapeo para la acción
    if (isset($accionesMap[$getaccion])) {
        $metodo = $accionesMap[$getaccion];
    } else {
        $metodo = $getaccion;
    }

    // Verifica si el método del controlador requiere un parámetro
    if ($metodo === 'aceptarSolicitud' || $metodo === 'rechazarSolicitud') {
        if (isset($_GET['id'])) {
            $objcontrolador->$metodo($_GET['id']);
        } else {
            // Redirigir a una página de error o mostrar un mensaje
            header("Location: index.php?controlador=notificaciones&accion=mostrarNotificaciones&error=id_no_proporcionado");
            exit;
        }
    } else {
        // Verifica si el método existe antes de llamarlo
        if (method_exists($objcontrolador, $metodo)) {
            $objcontrolador->$metodo();
        } else {
            // Método no encontrado, redirigir a una página de error
            header("Location: index.php?controlador=paginas&accion=error&mensaje=metodo_no_encontrado");
            exit;
        }
    }
}
?>
