<?php
// controladores/controlador_tienda.php
require_once './modelos/Tienda.php';
require_once './modelos/Notificacion.php';
require_once './modelos/Pedidos.php';
require_once './modelos/Producto.php';
require_once './controladores/controlador_notificaciones.php';
require_once './modelos/Domiciliario.php';

class ControladorTienda {
    private $modeloTienda;
    private $modeloPedidos;
    private $modeloNotificaciones;
    private $modeloProductos;
    private $controladorNotificaciones;
    private $modeloDomiciliario;
    public function __construct() {
        $this->modeloTienda = new Tienda();
        $this->modeloPedidos = new Pedidos();
        $this->modeloNotificaciones = new Notificaciones();
        $this->modeloProductos = new Producto();
        $this->controladorNotificaciones = new ControladorNotificaciones();
    }

    public function registrarTienda() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?controlador=usuarios&accion=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Preparar los datos de la tienda
                $datosTienda = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'correo' => $_POST['correo'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'direccion' => $_POST['direccion'] ?? '',
                    'telefono' => $_POST['telefono'] ?? '',
                    'apertura' => $_POST['apertura'] ?? '',
                    'cierre' => $_POST['cierre'] ?? '',
                    'fecha_apertura' => $_POST['fecha_apertura'] ?? date('Y-m-d'),
                    'fecha_registro' => date('Y-m-d')
                ];

                // Manejar la subida de la foto
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $fotoResult = $this->manejarSubidaFoto($_FILES['foto']);
                    if (!$fotoResult['exito']) {
                        throw new Exception($fotoResult['mensaje']);
                    }
                    $datosTienda['tienda_foto'] = $fotoResult['ruta'];
                } else {
                    throw new Exception("No se ha subido ninguna foto o ha ocurrido un error en la subida.");
                }

                // Registrar la tienda en la base de datos
                $resultadoRegistro = $this->modeloTienda->registrarTienda($datosTienda);

                if ($resultadoRegistro) {
                    $id_usuario = $_SESSION['usuario']['id'];

                    // Crear notificación para el administrador
                    $mensajeNotificacion = "Nueva solicitud de registro de tienda: {$datosTienda['nombre']}, Correo: {$datosTienda['correo']}, Dirección: {$datosTienda['direccion']}";
                    $this->modeloNotificaciones->crearNotificacion('solicitud', $mensajeNotificacion, null, true);

                    // Crear notificación para el usuario
                    $mensajeUsuario = "Tu solicitud para registrar la tienda '{$datosTienda['nombre']}' ha sido enviada con éxito. Te notificaremos cuando sea revisada.";
                    $this->modeloNotificaciones->crearNotificacion('informacion', $mensajeUsuario, $id_usuario);

                    // Mensaje de éxito
                    $respuesta = "Solicitud enviada con éxito";
                    echo "<script>
                            Swal.fire({
                                title: '¡Solicitud enviada!',
                                text: '$respuesta',
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '" . S_URL . "FormTien';
                                }
                            });
                         </script>";
                    exit();
                }
            } catch (Exception $e) {
                error_log("Error en registrarTienda: " . $e->getMessage());
                echo "<script>
                        Swal.fire({
                            title: 'Error',
                            text: '{$e->getMessage()}',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                     </script>";
            }
        }
        // Cargar la vista si no es una solicitud POST
        require_once './vista/usuarios/formTienda.php';
    }

    private function manejarSubidaFoto($archivo) {
        $directorioDestino = 'vista/recursos/img/tiendas/';
        $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['exito' => true, 'ruta' => $rutaCompleta];
        } else {
            return ['exito' => false, 'mensaje' => 'No se pudo subir la imagen.'];
        }
    }

    public function panel() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $correoUsuario = $_SESSION['usuario']['correo'];
        $datosTienda = $this->modeloTienda->obtenerDatosTienda($correoUsuario);

        $tienda = $this->modeloTienda->obtenerTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$tienda || !isset($tienda['id'])) {
            // Manejar el caso en que no se encuentre la tienda o no tenga ID
            $_SESSION['error'] = "No se pudo encontrar la información de la tienda.";
            header('Location: ' . S_URL . 'error');
            exit;
        }

        // Asegurarse de que 'tienda_foto' siempre esté definida
        if (!isset($datosTienda['tienda_foto']) || empty($datosTienda['tienda_foto'])) {
            $datosTienda['tienda_foto'] = IMG_URL . 'tiendas/defaultTienda.png';
        } else {
            // Convertir la ruta del sistema de archivos a una URL relativa
            $datosTienda['tienda_foto'] = BASE_URL . ltrim($datosTienda['tienda_foto'], '/');
        }
        
        // Obtener los últimos 5 pedidos
        $ultimosPedidos = $this->modeloPedidos->obtenerPedidosPorTienda($tienda['id'], 5);
        
        // Cargar estadísticas básicas
        $estadisticas = [
            'totalPedidos' => $this->modeloPedidos->contarPedidosPorTienda($tienda['id']),
            'pedidosPendientes' => $this->modeloPedidos->contarPedidosPorEstado($tienda['id'], 'pendiente'),
            'ingresosTotales' => $this->modeloPedidos->calcularIngresosTotales($tienda['id'])
        ];
        
        // Cargar notificaciones no leídas
        $notificacionesNoLeidas = $this->modeloNotificaciones->obtenerNotificacionesNoLeidasPorTienda($tienda['id']);
        
        // Preparar los datos para la vista
        $datos = [
            'tienda' => $tienda,
            'ultimosPedidos' => $ultimosPedidos,
            'estadisticas' => $estadisticas,
            'notificaciones' => $notificacionesNoLeidas
        ];
        
        require_once 'vista/Tienda/panel_T.php';
    }
    
    public function editarPerfil() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: " . S_URL_U . "login");
            exit();
        }

        $correoUsuario = $_SESSION['usuario']['correo'];
        $datosTienda = $this->modeloTienda->obtenerDatosTienda($correoUsuario);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datosActualizados = [
                'id' => $datosTienda['id'],
                'nombre' => $_POST['nombre'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'horario_apertura' => $_POST['horario_apertura'],
                'horario_cierre' => $_POST['horario_cierre'],
                'tienda_foto' => $datosTienda['tienda_foto']  // Mantener la foto existente por defecto
            ];

            if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
                $fotoResult = $this->manejarSubidaFoto($_FILES['nueva_foto']);
                if ($fotoResult['exito']) {
                    $datosActualizados['tienda_foto'] = $fotoResult['ruta'];
                } else {
                    $_SESSION['error_mensaje'] = $fotoResult['mensaje'];
                }
            }

            $actualizacionExitosa = $this->modeloTienda->actualizarPerfilTiendaYUsuario($datosActualizados, $correoUsuario);

            if ($actualizacionExitosa) {
                $_SESSION['mensaje_exito'] = "Perfil actualizado con éxito.";
                // Recargar los datos de la tienda después de la actualización
                $datosTienda = $this->modeloTienda->obtenerDatosTienda($correoUsuario);
            } else {
                $_SESSION['error_mensaje'] = "Error al actualizar el perfil.";
            }
        }

        error_log("Datos de la tienda después de actualizar: " . print_r($datosTienda, true));

        require_once 'vista/Tienda/editarPerfil.php';
    }

    public function perfilTienda($idTienda = null) {
        if ($idTienda === null && isset($_SESSION['usuario']) && $_SESSION['usuario']['id_rol'] == 3) {
            // Si no se proporciona ID y el usuario es una tienda, mostrar su propio perfil
            $idTienda = $this->modeloTienda->obtenerTiendaPorUsuario($_SESSION['usuario']['id'])['id'];
        }

        if ($idTienda === null) {
            // Manejar el error si no se proporciona ID de tienda
            header("Location: index.php?controlador=paginas&accion=error&mensaje=" . urlencode("No se especificó una tienda."));
            exit();
        }

        $datosTienda = $this->modeloTienda->obtenerDatosTiendaPorId($idTienda);
        
        if ($datosTienda === null) {
            header("Location: index.php?controlador=paginas&accion=error&mensaje=" . urlencode("No se encontró la tienda especificada."));
            exit();
        }

        // Obtener productos de la tienda
        $productos = $this->modeloProductos->obtenerProductosPorTienda($idTienda);

        // Obtener estadísticas básicas
        $estadisticas = [
            'totalProductos' => count($productos),
            'calificacionPromedio' => $this->modeloTienda->obtenerCalificacionPromedio($idTienda),
            'totalPedidos' => $this->modeloPedidos->contarPedidosPorTienda($idTienda)
        ];

        $datos = [
            'tienda' => $datosTienda,
            'productos' => $productos,
            'estadisticas' => $estadisticas,
            'esPropioDueno' => isset($_SESSION['usuario']) && $_SESSION['usuario']['id_rol'] == 3 && $_SESSION['usuario']['id'] == $datosTienda['id_usuario']
        ];

        require_once 'vista/Tienda/perfilTienda.php';
    }
    
    public function listarTiendas() {
        $tiendas = $this->modeloTienda->obtenerTiendasActivas();
        require_once 'vista/paginas/Tiendas/listarTiendas.php';
    }

    public function verTienda() {
        if (!isset($_GET['id'])) {
            // Redirigir a una página de error o a la lista de tiendas
            header('Location: ' . S_URL_T . 'listarTiendas');
            exit;
        }

        $idTienda = $_GET['id'];
        $tienda = $this->modeloTienda->obtenerTiendaPorId($idTienda);
        $productos = $this->modeloProductos->obtenerProductosPorTienda($idTienda);

        if (!$tienda) {
            // Manejar el caso en que la tienda no existe
            $_SESSION['error'] = "La tienda solicitada no existe.";
            header('Location: ' . S_URL_T . 'listarTiendas');
            exit;
        }

        // Agregar logs para depuración
        error_log("Tienda: " . print_r($tienda, true));
        error_log("Productos: " . print_r($productos, true));

        require_once 'vista/paginas/Tiendas/verTienda.php';
    }

    // Otros métodos según sea necesario
}

?>
