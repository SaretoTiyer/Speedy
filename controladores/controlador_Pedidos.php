<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function logError($message) {
    error_log("Error en ControladorPedidos: " . $message);
}

require_once 'modelos/Pedidos.php';
require_once 'modelos/Producto.php';
require_once 'modelos/Tienda.php';
require_once 'modelos/Domiciliario.php';
require_once 'modelos/usuarios.php';
require_once 'controladores/controlador_notificaciones.php';
require_once 'modelos/Calificacion.php';

class ControladorPedidos {
    private $modeloPedidos;
    private $modeloProducto;
    private $modeloTienda;
    private $modeloDomiciliario;
    private $modeloCalificacion;
    private $modeloUsuarios;
    private $controladorNotificaciones;
    
    public function __construct() {
        $this->modeloPedidos = new Pedidos();
        $this->modeloProducto = new Producto();
        $this->modeloTienda = new Tienda();
        $this->modeloUsuarios = new Usuario(null, null, null, null, null, null);
        $this->modeloDomiciliario = new Domiciliario();
        $this->modeloCalificacion = new Calificacion();
        $this->controladorNotificaciones = new ControladorNotificaciones();
    }

    public function agregarAlCarrito() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id_producto = $_POST['id_producto'];
            $cantidad = $_POST['cantidad'];

            $producto = $this->modeloProducto->obtenerProductoPorId($id_producto);

            if (!$producto) {
                header('Location: ' . S_URL . 'error');
                exit;
            }

            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            $item = [
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto['imagen'] // Asegúrate de que esto ya contenga la ruta completa
            ];

            $_SESSION['carrito'][] = $item;

            header('Location: ' . S_URL_PED . 'verCarrito');
            exit;
        }
    }

    public function verCarrito() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
        require_once 'vista/paginas/Pedidos/Carrito.php';
    }

    public function crearPedido() {
        error_log("Iniciando crearPedido()");
        if (!isset($_SESSION['usuario'])) {
            error_log("Usuario no autenticado");
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idUsuario = $_SESSION['usuario']['id'];
        $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
        error_log("ID Usuario: " . $idUsuario);
        error_log("Carrito: " . print_r($carrito, true));
    
        if (empty($carrito)) {
            error_log("Carrito vacío");
            $_SESSION['error'] = "No hay productos en el carrito.";
            header('Location: ' . S_URL . 'carrito');
            exit;
        }
    
        // Calcular el valor total del pedido
        $valorTotal = 0;
        foreach ($carrito as $item) {
            $valorTotal += $item['precio'] * $item['cantidad'];
        }
        error_log("Valor total del pedido: " . $valorTotal);
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            error_log("Procesando formulario POST");
            // Procesar el formulario de pedido
            $direccionEntrega = $_POST['direccion_entrega'] ?? '';
            $comentarios = $_POST['comentarios'] ?? '';
            error_log("Dirección de entrega: " . $direccionEntrega);
            error_log("Comentarios: " . $comentarios);
    
            $idPedido = $this->modeloPedidos->crearPedido($idUsuario, $carrito, $valorTotal, $direccionEntrega, $comentarios);
            error_log("ID del pedido creado: " . ($idPedido ? $idPedido : "Error al crear pedido"));
    
            if ($idPedido) {
                error_log("Pedido creado exitosamente");
                // Notificación para el usuario
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Su pedido #' . $idPedido . ' ha sido creado con éxito.', $idUsuario);

            // Notificación para la tienda
            $idTienda = $this->obtenerIdTiendaDelPedido($carrito);
            $correoTienda = $this->modeloTienda->obtenerCorreoPorTienda($idTienda);
            $idUsuarioTienda = $this->modeloTienda->obtenerIdUsuarioPorCorreo($correoTienda);
            
            if ($idUsuarioTienda) {
                $this->controladorNotificaciones->crearNotificacion('pedido', 'Nuevo pedido #' . $idPedido . ' recibido.', $idUsuarioTienda, false, true);
            } else {
                error_log("No se pudo obtener el id de usuario de la tienda para el pedido #" . $idPedido);
            }
    
                unset($_SESSION['carrito']);
                $_SESSION['exito'] = "Pedido creado con éxito.";
                error_log("Redirigiendo a confirmación de pedido");
                header('Location: ' . S_URL_PED . 'confirmacionPedido&id=' . $idPedido);
            } else {
                error_log("Error al crear el pedido");
                $_SESSION['error'] = "Error al crear el pedido.";
                header('Location: ' . S_URL . 'carrito');
            }
            exit;
        } else {
            error_log("Mostrando formulario de pedido");
            // Mostrar el formulario de pedido
            require_once 'vista/paginas/Pedidos/RealizarPedido.php';
        }
    }

    private function obtenerIdTiendaDelPedido($carrito) {
        // Asumimos que todos los productos en el carrito son de la misma tienda
        $primerProducto = reset($carrito);
        if (isset($primerProducto['id'])) {
            // Necesitamos obtener el id_tienda a partir del id del producto
            return $this->modeloProducto->obtenerIdTiendaPorProducto($primerProducto['id']);
        }
        return null;
    }

    public function confirmacionPedido() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $id_pedido = $_GET['id'] ?? null;
        if (!$id_pedido) {
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $pedido = $this->modeloPedidos->obtenerPedidoPorId($id_pedido);
        $detallesPedido = $this->modeloPedidos->obtenerDetallePedido($id_pedido);
        
        if (!$pedido) {
            $_SESSION['error'] = "No se pudo encontrar el pedido especificado.";
            header('Location: ' . S_URL . 'error');
            exit;
        }

        require_once 'vista/paginas/Pedidos/ConfirmacionP.php';
    }


    public function verPedidos() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idUsuario = $_SESSION['usuario']['id'];

        // Obtener todos los pedidos del usuario
        $pedidos = $this->modeloPedidos->obtenerTodosPedidosUsuario($idUsuario);

        // Separar pedidos entregados y en curso
        $pedidosEntregados = array_filter($pedidos, function($pedido) {
            return $pedido['estado'] == 'entregado';
        });

        $pedidosEnCurso = array_filter($pedidos, function($pedido) {
            return $pedido['estado'] != 'entregado';
        });

        // Ordenar los pedidos por fecha, con los más recientes primero
        $ordenarPorFecha = function($a, $b) {
            return strtotime($b['fecha_hora_pedido']) - strtotime($a['fecha_hora_pedido']);
        };
        usort($pedidosEntregados, $ordenarPorFecha);
        usort($pedidosEnCurso, $ordenarPorFecha);

        // Verificar si los pedidos ya han sido calificados
        foreach ($pedidosEntregados as &$pedido) {
            $pedido['ya_calificado'] = $this->modeloCalificacion->pedidoYaCalificado($pedido['id']);
        }

        // Pasar los datos a la vista
        $datos = [
            'pedidosEntregados' => $pedidosEntregados,
            'pedidosEnCurso' => $pedidosEnCurso
        ];

        require_once 'vista/paginas/Pedidos/ListaPedidos.php';
    }
    public function eliminarDelCarrito() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        if (isset($_GET['id'])) {
            $id_producto = $_GET['id'];
            if (isset($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as $index => $item) {
                    if ($item['id'] == $id_producto) {  // Cambiado de 'id_producto' a 'id'
                        unset($_SESSION['carrito'][$index]);
                        break;
                    }
                }
                // Reindexar el array
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            }
        }
    
        // Redirigir de vuelta al carrito
        header('Location: ' . S_URL_PED . 'verCarrito');
        exit;
    }

    public function pedidosTienda() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idTienda = $this->modeloTienda->obtenerIdTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$idTienda) {
            $_SESSION['error'] = "No se encontró una tienda asociada a tu cuenta.";
            header('Location: ' . S_URL_T . 'panel');
            exit;
        }

        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
        $pedidos = $this->modeloPedidos->obtenerPedidosPorTiendaYEstado($idTienda, $estado);

        $datos = [
            'pedidos' => $pedidos,
            'estado_actual' => $estado
        ];

        require_once 'vista/paginas/Pedidos/PedidosTienda.php';
    }

    public function detallePedidoTienda() {
        error_log("Iniciando detallePedidoTienda");
        
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            error_log("Usuario no autorizado");
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idPedido = $_GET['id'] ?? null;
        $idTienda = $_SESSION['usuario']['id'];

        error_log("ID Pedido: " . $idPedido);
        error_log("ID Tienda: " . $idTienda);

        if (!$idPedido) {
            error_log("ID de pedido no proporcionado");
            header('Location: ' . S_URL_PED . 'pedidosTienda');
            exit;
        }

        error_log("Obteniendo datos del pedido");
        $pedido = $this->modeloPedidos->obtenerPedidoPorId($idPedido);
        $detallePedido = $this->modeloPedidos->obtenerDetallePedido($idPedido);
        
        // Obtener la lista de domiciliarios activos
        $domiciliarios = $this->modeloDomiciliario->obtenerDomiciliariosActivos();

        error_log("Pedido: " . print_r($pedido, true));
        error_log("Detalle Pedido: " . print_r($detallePedido, true));
        error_log("Domiciliarios: " . print_r($domiciliarios, true));

        if (empty($pedido)) {
            $_SESSION['error'] = "No se encontró el pedido.";
            header('Location: ' . S_URL_PED . 'pedidosTienda');
            exit;
        }


        if (empty($domiciliarios)) {
            $_SESSION['info'] = "No hay domiciliarios activos disponibles.";
        }

        require_once 'vista/paginas/Pedidos/DetallePedidoTienda.php';
    }

    public function actualizarEstadoPedido() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idPedido = $_POST['id_pedido'];
            $nuevoEstado = $_POST['nuevo_estado'];

            $resultado = $this->modeloPedidos->actualizarEstadoPedido($idPedido, $nuevoEstado);

            if ($resultado) {
                header('Location: ' . S_URL_PED . 'detallePedidoTienda&id=' . $idPedido . '&mensaje=Estado actualizado con éxito');
            } else {
                header('Location: ' . S_URL_PED . 'detallePedidoTienda&id=' . $idPedido . '&error=Error al actualizar el estado');
            }
            exit;
        }
    }

    public function listarPedidosDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $idDomiciliario = $this->modeloDomiciliario->obtenerIdDomiciliarioPorCorreo($correoDomiciliario);
        $pedidosEnCamino = $this->modeloPedidos->obtenerPedidosCompletosParaDomiciliario($idDomiciliario, 'en_camino');
        $pedidosEntregados = $this->modeloPedidos->obtenerPedidosCompletosParaDomiciliario($idDomiciliario, 'entregado');
        $pedidosDisponibles = $this->modeloPedidos->obtenerPedidosDisponiblesParaDomiciliario();
    
        $datos = [
            'pedidosEnCamino' => $pedidosEnCamino,
            'pedidosEntregados' => $pedidosEntregados,
            'pedidosDisponibles' => $pedidosDisponibles
        ];
    
        require_once 'vista/paginas/Domiciliarios/pedidos.php';
    }

    public function asignarPedidoDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idPedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : null;
        if (!$idPedido) {
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_D . 'listarPedidosDomiciliario');
            exit;
        }
    
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $idDomiciliario = $this->modeloDomiciliario->obtenerIdDomiciliarioPorCorreo($correoDomiciliario);
    
        if ($this->modeloPedidos->asignarDomiciliario($idPedido, $idDomiciliario)) {
            $this->modeloPedidos->actualizarEstadoPedido($idPedido, 'en_camino');
    
            // Notificación para el domiciliario
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Te has asignado el pedido #' . $idPedido . ' para entrega.', $correoDomiciliario);
    
            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' está en camino.', $idUsuario);
    
            $_SESSION['exito'] = "Te has asignado el pedido #$idPedido exitosamente.";
        } else {
            $_SESSION['error'] = "Error al asignar el pedido.";
        }
    
        header('Location: ' . S_URL_D . 'listarPedidosDomiciliario');
        exit;
    }

    public function verDetallePedidoDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idPedido = $_GET['id'] ?? null;
        if (!$idPedido) {
            header('Location: ' . S_URL_D . 'pedidosActivos');
            exit;
        }
    
        $pedido = $this->modeloPedidos->obtenerPedidoPorId($idPedido);
        $detallesPedido = $this->modeloPedidos->obtenerDetallePedido($idPedido);
    
        // Depuración
        error_log("Pedido: " . print_r($pedido, true));
        error_log("Detalles del Pedido: " . print_r($detallesPedido, true));
    
        require_once 'vista/Domiciliario/detalle_pedido.php';
    }

    public function actualizarEstadoPedidoDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idPedido = $_POST['id_pedido'];
            $nuevoEstado = $_POST['nuevo_estado'];

            $resultado = $this->modeloPedidos->actualizarEstadoPedido($idPedido, $nuevoEstado);

            if ($resultado) {
                header('Location: ' . S_URL_D . 'pedidosActivos&mensaje=Estado actualizado con éxito');
            } else {
                header('Location: ' . S_URL_D . 'pedidosActivos&error=Error al actualizar el estado');
            }
            exit;
        }
    }

    public function asignarDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) { // 3 es el rol de tienda
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idPedido = isset($_POST['id_pedido']) ? $_POST['id_pedido'] : null;
        $idDomiciliario = isset($_POST['id_domiciliario']) ? $_POST['id_domiciliario'] : null;
    
        if (!$idPedido || !$idDomiciliario) {
            $_SESSION['error'] = "Información incompleta para asignar el domiciliario.";
            header('Location: ' . S_URL_PED . 'pedidosEnPreparacion');
            exit;
        }
    
        if ($this->modeloPedidos->asignarDomiciliario($idPedido, $idDomiciliario)) {
            $this->modeloPedidos->actualizarEstadoPedido($idPedido, 'en_camino');
    
            // Notificación para la tienda
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Has asignado un domiciliario al pedido #' . $idPedido . '.', $_SESSION['usuario']['id'], false, true);
    
            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' está en camino.', $idUsuario);
    
            // Notificación para el domiciliario
            $correoDomiciliario = $this->modeloDomiciliario->obtenerCorreoPorId($idDomiciliario);
            if ($correoDomiciliario) {
                $idUsuarioDomiciliario = $this->modeloUsuarios->obtenerIdPorCorreo($correoDomiciliario);
                if ($idUsuarioDomiciliario) {
                    $this->controladorNotificaciones->crearNotificacion('pedido', 'Se te ha asignado el pedido #' . $idPedido . ' para entrega.', $idUsuarioDomiciliario);
                }
            }
    
            $_SESSION['exito'] = "Domiciliario asignado y pedido en camino.";
        } else {
            $_SESSION['error'] = "Error al asignar el domiciliario.";
        }
    
        header('Location: ' . S_URL_PED . 'pedidosTienda');
        exit;
    }

    public function ver_detalle_pedido() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $id_pedido = $_GET['id'] ?? null;
        if (!$id_pedido) {
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $pedido = $this->modeloPedidos->obtenerPedidoPorId($id_pedido);
        $detalles_pedido = $this->modeloPedidos->obtenerDetallePedido($id_pedido);
        
        if (!$pedido || !$detalles_pedido) {
            $_SESSION['error'] = "No se pudo encontrar el pedido especificado.";
            header('Location: ' . S_URL . 'error');
            exit;
        }

        require_once 'vista/paginas/Pedidos/DetallePedido.php';
    }

    public function aceptarPedido() {
        logError("Iniciando aceptarPedido");
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            logError("Usuario no autorizado intentando aceptar pedido");
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idPedido = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$idPedido) {
            logError("ID de pedido no especificado en aceptarPedido");
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_PED . 'pedidosPendientes');
            exit;
        }

        $idTienda = $this->modeloTienda->obtenerIdTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$this->modeloPedidos->perteneceALaTienda($idPedido, $idTienda)) {
            logError("Intento de aceptar pedido que no pertenece a la tienda. ID Pedido: $idPedido, ID Tienda: $idTienda");
            $_SESSION['error'] = "No tienes permiso para aceptar este pedido.";
            header('Location: ' . S_URL_PED . 'pedidosPendientes');
            exit;
        }

        if ($this->modeloPedidos->actualizarEstadoPedido($idPedido, 'aceptado')) {
            logError("Pedido aceptado con éxito. ID Pedido: $idPedido");
            // Notificación para la tienda
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Has aceptado el pedido #' . $idPedido . '.', $_SESSION['usuario']['id'], false, true);

            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' ha sido aceptado por la tienda.', $idUsuario);

            $_SESSION['exito'] = "Pedido aceptado con éxito.";
        } else {
            logError("Error al aceptar pedido. ID Pedido: $idPedido");
            $_SESSION['error'] = "Error al aceptar el pedido.";
        }

        error_log("Redirigiendo a pedidosPendientes");
        header('Location: ' . S_URL_PED . 'pedidosTienda');
        exit;
    }

    public function prepararPedido() {
        logError("Iniciando prepararPedido");
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            logError("Usuario no autorizado intentando preparar pedido");
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idPedido = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$idPedido) {
            logError("ID de pedido no especificado en prepararPedido");
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_PED . 'pedidosAceptados');
            exit;
        }

        $idTienda = $this->modeloTienda->obtenerIdTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$this->modeloPedidos->perteneceALaTienda($idPedido, $idTienda)) {
            logError("Intento de preparar pedido que no pertenece a la tienda. ID Pedido: $idPedido, ID Tienda: $idTienda");
            $_SESSION['error'] = "No tienes permiso para preparar este pedido.";
            header('Location: ' . S_URL_PED . 'pedidosAceptados');
            exit;
        }

        if ($this->modeloPedidos->actualizarEstadoPedido($idPedido, 'en_preparacion')) {
            logError("Pedido en preparación. ID Pedido: $idPedido");
            // Notificación para la tienda
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Has comenzado a preparar el pedido #' . $idPedido . '.', $_SESSION['usuario']['id'], false, true);

            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' está siendo preparado.', $idUsuario);

            $_SESSION['exito'] = "El pedido está ahora en preparación.";
        } else {
            logError("Error al actualizar el estado del pedido a en preparación. ID Pedido: $idPedido");
            $_SESSION['error'] = "Error al actualizar el estado del pedido.";
        }

        header('Location: ' . S_URL_PED . 'pedidosTienda');
        exit;
    }

    public function finalizarPreparacion() {
        logError("Iniciando finalizarPreparacion");
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) { // 3 es el rol de tienda
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idPedido = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$idPedido) {
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_PED . 'pedidosEnPreparacion');
            exit;
        }
    
        $idTienda = $this->modeloTienda->obtenerIdTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$this->modeloPedidos->perteneceALaTienda($idPedido, $idTienda)) {
            $_SESSION['error'] = "No tienes permiso para finalizar la preparación de este pedido.";
            header('Location: ' . S_URL_PED . 'pedidosEnPreparacion');
            exit;
        }

        $domiciliariosDisponibles = $this->modeloDomiciliario->obtenerDomiciliariosActivos();
    
        $datos = [
            'idPedido' => $idPedido,
            'domiciliariosDisponibles' => $domiciliariosDisponibles
        ];

        require_once 'vista/paginas/Pedidos/seleccionarDomiciliario.php';
    }

    public function confirmarEntrega() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $idPedido = $_GET['id'] ?? null;
        if (!$idPedido) {
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_D . 'misPedidos');
            exit;
        }
    
        // Verificar si el pedido pertenece al domiciliario actual
        $idDomiciliario = $this->modeloDomiciliario->obtenerIdDomiciliarioPorCorreo($_SESSION['usuario']['correo']);
        if (!$this->modeloPedidos->pedidoPerteneceADomiciliario($idPedido, $idDomiciliario)) {
            $_SESSION['error'] = "No tienes permiso para confirmar la entrega de este pedido.";
            header('Location: ' . S_URL_D . 'misPedidos');
            exit;
        }
    
        if ($this->modeloPedidos->confirmarEntregaPedido($idPedido, $idDomiciliario)) {
            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' ha sido entregado.', $idUsuario);
    
            // Notificación para la tienda
            $idTienda = $this->modeloPedidos->obtenerIdTiendaPorPedido($idPedido);
            $correoTienda = $this->modeloTienda->obtenerCorreoPorId($idTienda);
            if ($correoTienda) {
                $idUsuarioTienda = $this->modeloUsuarios->obtenerIdPorCorreo($correoTienda);
                if ($idUsuarioTienda) {
                    $this->controladorNotificaciones->crearNotificacion(
                        'pedido', 
                        'El usuario ' . $_SESSION['usuario']['nombre'] . ' ha confirmado la entrega del pedido #' . $idPedido . '.', 
                        $idUsuarioTienda
                    );
                }
            }
            // Notificación para el domiciliario
            $correoDomiciliario = $this->modeloDomiciliario->obtenerCorreoPorId($idDomiciliario);
            if ($correoDomiciliario) {
                $idUsuarioDomiciliario = $this->modeloUsuarios->obtenerIdPorCorreo($correoDomiciliario);
                if ($idUsuarioDomiciliario) {
                    $this->controladorNotificaciones->crearNotificacion('pedido', 'Has entregado el pedido #' . $idPedido . '.', $idUsuarioDomiciliario);
                }
            }
    
            $_SESSION['exito'] = "Entrega del pedido #$idPedido confirmada con éxito.";
        } else {
            $_SESSION['error'] = "Error al confirmar la entrega del pedido #$idPedido.";
        }
    
        header('Location: ' . S_URL_D . 'misPedidos');
        exit;
    }

    public function calificarPedido() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idPedido = isset($_GET['id']) ? $_GET['id'] : null;

        // Verificar si el pedido ya ha sido calificado
        if ($this->modeloCalificacion->pedidoYaCalificado($idPedido)) {
            $_SESSION['info'] = "Este pedido ya ha sido calificado.";
            header('Location: ' . S_URL_U . 'misPedidos');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Desactivar la salida de la plantilla
            ob_clean();
            header('Content-Type: application/json');

            $calificacionTienda = isset($_POST['calificacion_tienda']) ? $_POST['calificacion_tienda'] : null;
            $calificacionDomiciliario = isset($_POST['calificacion_domiciliario']) ? $_POST['calificacion_domiciliario'] : null;
            $comentarios = isset($_POST['comentarios']) ? $_POST['comentarios'] : '';

            if (!$idPedido || !$calificacionTienda || !$calificacionDomiciliario) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos de calificación son obligatorios.']);
                exit;
            }

            if (!$this->modeloPedidos->perteneceAlUsuario($idPedido, $_SESSION['usuario']['id'])) {
                echo json_encode(['success' => false, 'message' => 'No tienes permiso para calificar este pedido.']);
                exit;
            }

            $idTienda = $this->modeloPedidos->obtenerIdTiendaPorPedido($idPedido);
            $idDomiciliario = $this->modeloPedidos->obtenerIdDomiciliarioPorPedido($idPedido);

            $resultado = $this->modeloCalificacion->agregarCalificacion(
                $idPedido, 
                $_SESSION['usuario']['id'], 
                $idTienda,
                $idDomiciliario,
                $calificacionTienda,
                $calificacionDomiciliario, 
                $comentarios
            );

            if ($resultado) {
                // Notificación para la tienda
                $correoTienda = $this->modeloTienda->obtenerCorreoPorId($idTienda);
                if ($correoTienda) {
                    $idUsuarioTienda = $this->modeloUsuarios->obtenerIdPorCorreo($correoTienda);
                    if ($idUsuarioTienda) {
                        $this->controladorNotificaciones->crearNotificacion(
                            'pedido', 
                            'El usuario ' . $_SESSION['usuario']['nombre'] . ' ha calificado tu tienda con ' . $calificacionTienda . ' estrellas.', 
                            $idUsuarioTienda
                        );
                    }
                }

                 // Notificación para el domiciliario
                    $correoDomiciliario = $this->modeloDomiciliario->obtenerCorreoPorId($idDomiciliario);
                    if ($correoDomiciliario) {
                        $idUsuarioDomiciliario = $this->modeloUsuarios->obtenerIdPorCorreo($correoDomiciliario);
                        if ($idUsuarioDomiciliario) {
                            $this->controladorNotificaciones->crearNotificacion(
                                'pedido', 
                                'El usuario ' . $_SESSION['usuario']['nombre'] . ' ha calificado tu servicio con ' . $calificacionDomiciliario . ' estrellas.', 
                                $idUsuarioDomiciliario
                            );
                        }
                    }

                echo json_encode(['success' => true, 'message' => 'Calificación enviada con éxito']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al calificar el pedido']);
            }
            exit;
        }

        // Si no es POST, muestra el formulario
        $pedido = $this->modeloPedidos->obtenerPedidoPorId($idPedido);
        if (!$pedido || $pedido['id_usuario'] != $_SESSION['usuario']['id']) {
            $_SESSION['error'] = "No se encontró el pedido o no tienes permiso para calificarlo.";
            header('Location: ' . S_URL_U . 'misPedidos');
            exit;
        }
        require_once 'vista/Usuario/calificar_pedido.php';
    }

    private function enviarRespuestaJSON($success, $message) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }

    public function rechazarPedido() {
        logError("Iniciando rechazarPedido");
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            logError("Usuario no autorizado intentando rechazar pedido");
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idPedido = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$idPedido) {
            logError("ID de pedido no especificado en rechazarPedido");
            $_SESSION['error'] = "ID de pedido no especificado.";
            header('Location: ' . S_URL_PED . 'pedidosTienda');
            exit;
        }

        $idTienda = $this->modeloTienda->obtenerIdTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$this->modeloPedidos->perteneceALaTienda($idPedido, $idTienda)) {
            logError("Intento de rechazar pedido que no pertenece a la tienda. ID Pedido: $idPedido, ID Tienda: $idTienda");
            $_SESSION['error'] = "No tienes permiso para rechazar este pedido.";
            header('Location: ' . S_URL_PED . 'pedidosTienda');
            exit;
        }

        if ($this->modeloPedidos->actualizarEstadoPedido($idPedido, 'cancelado')) {
            logError("Pedido rechazado con éxito. ID Pedido: $idPedido");
            
            // Notificación para la tienda
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Has rechazado el pedido #' . $idPedido . '.', $_SESSION['usuario']['id'], false, true);

            // Notificación para el usuario
            $idUsuario = $this->modeloPedidos->obtenerIdUsuarioPorPedido($idPedido);
            $this->controladorNotificaciones->crearNotificacion('pedido', 'Tu pedido #' . $idPedido . ' ha sido rechazado por la tienda.', $idUsuario);

            // Eliminar el pedido y sus detalles
            if ($this->modeloPedidos->eliminarPedido($idPedido)) {
                $_SESSION['exito'] = "Pedido rechazado y eliminado con éxito.";
            } else {
                $_SESSION['error'] = "El pedido fue rechazado pero no se pudo eliminar completamente.";
            }
        } else {
            logError("Error al rechazar pedido. ID Pedido: $idPedido");
            $_SESSION['error'] = "Error al rechazar el pedido.";
        }

        header('Location: ' . S_URL_PED . 'pedidosTienda');
        exit;
    }

    public function actualizarCantidad() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 2) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $idProducto = isset($_POST['id']) ? $_POST['id'] : null;
            $nuevaCantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

            if (!$idProducto || $nuevaCantidad <= 0) {
                $_SESSION['error'] = "Datos inválidos para actualizar la cantidad.";
                header('Location: ' . S_URL_PED . 'verCarrito');
                exit;
            }

            if (isset($_SESSION['carrito'])) {
                foreach ($_SESSION['carrito'] as &$item) {
                    if ($item['id'] == $idProducto) {
                        $item['cantidad'] = $nuevaCantidad;
                        break;
                    }
                }
                unset($item); // Importante para evitar problemas con referencias
            }

            $_SESSION['exito'] = "Cantidad actualizada con éxito.";
        }

        header('Location: ' . S_URL_PED . 'verCarrito');
        exit;
    }

}





