<?php
// controladores/controlador_domiciliario.php
require_once './modelos/Domiciliario.php';
require_once './modelos/Notificacion.php';
require_once './modelos/Pedidos.php';

class ControladorDomiciliario {
    private $modeloDomiciliario;
    private $modeloNotificacion;
    private $modeloPedidos;

    public function __construct() {
        $this->modeloDomiciliario = new Domiciliario();
        $this->modeloNotificacion = new Notificaciones();
        $this->modeloPedidos = new Pedidos();
    }

    public function registrarDomi() {
        error_log("Iniciando registrarDomi");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            error_log("Método POST detectado");
            try {
                $rutaFoto = null;
                if (isset($_FILES['dom_foto']) && $_FILES['dom_foto']['error'] == 0) {
                    error_log("Archivo detectado, iniciando manejo de subida");
                    $fotoResult = $this->manejarSubidaFotoDomiciliario($_FILES['dom_foto']);
                    if (!$fotoResult['exito']) {
                        throw new Exception($fotoResult['mensaje']);
                    }
                    $rutaFoto = $fotoResult['ruta'];
                    error_log("Ruta de foto establecida: " . $rutaFoto);
                } else {
                    error_log("No se detectó archivo o hubo un error: " . (isset($_FILES['dom_foto']) ? $_FILES['dom_foto']['error'] : 'No hay archivo'));
                    throw new Exception("No se ha subido ninguna foto o ha ocurrido un error en la subida.");
                }

                $datosDomiciliario = [
                    'nombre' => $_POST['nombre'],
                    'apellido' => $_POST['apellido'],
                    'telefono' => $_POST['telefono'],
                    'correo' => $_POST['correo'],
                    'password' => $_POST['password'],
                    'direccion' => $_POST['direccion'],
                    'vehiculo' => $_POST['vehiculo'],
                    'dom_foto' => $rutaFoto
                ];

                error_log("Intentando registrar domiciliario");
                $registroExitoso = $this->modeloDomiciliario->registrarDomiciliario($datosDomiciliario);

                $id_usuario = isset($_SESSION['usuario']['id']) ? $_SESSION['usuario']['id'] : null;

                if ($registroExitoso) {
                    // Crear notificación para el administrador
                    $mensajeNotificacion = "Nueva solicitud de registro de domiciliario: {$datosDomiciliario['nombre']}, Correo: {$datosDomiciliario['correo']}, Dirección: {$datosDomiciliario['direccion']}";
                    $this->modeloNotificacion->crearNotificacion('solicitud', $mensajeNotificacion, $id_usuario, true); // Asumiendo que 1 es el ID del administrador

                    // Crear notificación para el usuario
                    $mensajeUsuario = "Tu solicitud para registrar el domiciliario '{$datosDomiciliario['nombre']}' ha sido enviada con éxito. Te notificaremos cuando sea revisada.";
                    $this->modeloNotificacion->crearNotificacion('informacion', $mensajeUsuario, $id_usuario);

                    echo "<script>
                        Swal.fire({
                            title: '¡Solicitud enviada!',
                            text: 'Solicitud de domiciliario enviada con éxito',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '" . S_URL . "FormDomi';
                            }
                        });
                        </script>";
                    exit(); // Agregamos un exit() aquí para detener la ejecución del script
                } else {
                    throw new Exception("Error al registrar el domiciliario.");
                }
            } catch (Exception $e) {
                error_log("Error en registrarDomi: " . $e->getMessage());
                echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: '" . $e->getMessage() . "',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    </script>";
            }
        }
        error_log("Cargando formulario de domiciliario");
        require_once './vista/usuarios/formDomiciliario.php';
    }

    public function panel() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
        
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        error_log("Correo del domiciliario obtenido de la sesión: " . $correoDomiciliario);
        
        // Obtener datos del domiciliario
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        
        if (!$datosDomiciliario) {
            error_log("No se encontraron datos para el domiciliario con correo: " . $correoDomiciliario);
            $_SESSION['error'] = "Error: No se han recibido los datos del domiciliario.";
            header('Location: ' . S_URL . 'inicio');
            exit;
        }
        
        // Obtener pedidos asignados al domiciliario
        $pedidos = $this->modeloPedidos->obtenerPedidosPorDomiciliario($datosDomiciliario['id']);
        
        // Asegúrate de que $pedidos sea un array, incluso si está vacío
        $pedidos = $pedidos ?: [];
        
        // Pasar los datos a la vista
        $datos = [
            'datosDomiciliario' => $datosDomiciliario,
            'pedidos' => $pedidos
        ];

        require_once 'vista/Domiciliario/panel_D.php';
    }

    public function perfil() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header("Location: index.php?controlador=usuarios&accion=login");
            exit();
        }
        
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        
        if ($datosDomiciliario === null) {
            header("Location: index.php?controlador=paginas&accion=error&mensaje=" . urlencode("No se encontraron datos para el domiciliario."));
            exit();
        }
        
        $estadisticas = [
            'totalEntregas' => $this->modeloPedidos->contarPedidosPorDomiciliario($datosDomiciliario['id']),
            'calificacionPromedio' => $this->modeloDomiciliario->obtenerCalificacionPromedio($datosDomiciliario['id']),
            'tiempoPromedioEntrega' => $this->modeloPedidos->calcularTiempoPromedioEntrega($datosDomiciliario['id'])
        ];
        
        $datos = [
            'domiciliario' => $datosDomiciliario,
            'estadisticas' => $estadisticas,
            'esPropioDomiciliario' => true
        ];
        
        require_once 'vista/Domiciliario/perfilDomiciliario.php';
    }

    public function editarPerfil() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correoDomiciliario = $_SESSION['usuario']['correo'];
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);

            // Manejar la subida de la nueva foto
            if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
                $directorioDestino = './vista/recursos/img/domiciliarios/';
                $nombreArchivo = uniqid() . '_' . basename($_FILES['nueva_foto']['name']);
                $rutaCompleta = $directorioDestino . $nombreArchivo;

                if (move_uploaded_file($_FILES['nueva_foto']['tmp_name'], $rutaCompleta)) {
                    $datosDomiciliario['dom_foto'] = $rutaCompleta;
                } else {
                    $_SESSION['error_mensaje'] = "Error al subir la nueva foto.";
                    header("Location: index.php?controlador=domiciliario&accion=editarPerfil");
                    return;
                }
            }

            // Agregar el ID del domiciliario a los datos
            $datosDomiciliario['id'] = $datosDomiciliario['id'];

            try {
                $resultado = $this->modeloDomiciliario->actualizarPerfilDomiciliario($datosDomiciliario);
                if ($resultado) {
                    $_SESSION['mensaje'] = "Perfil actualizado con éxito.";
                } else {
                    $_SESSION['error_mensaje'] = "Error al actualizar el perfil.";
                }
            } catch (Exception $e) {
                $_SESSION['error_mensaje'] = "Error: " . $e->getMessage();
            }

            header("Location: index.php?controlador=domiciliario&accion=editarPerfil");
            return;
        }

        // Si no es POST, mostrar el formulario de edición
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        include_once "vista/Domiciliario/editarPerfil.php";
    }

    public function perfilDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header("Location: index.php?controlador=usuarios&accion=login");
            exit();
        }

        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);

        if (!$datosDomiciliario) {
            // Manejar el error si no se encuentra el domiciliario
            $_SESSION['error_mensaje'] = "No se encontró la información del domiciliario.";
            header("Location: index.php?controlador=paginas&accion=inicio");
            exit();
        }

        if (!isset($datosDomiciliario['dom_foto']) || empty($datosDomiciliario['dom_foto'])) {
            $datosDomiciliario['dom_foto'] = IMG_URL . 'domiciliarios/defaultDomiciliario.png';
        } else {
            $datosDomiciliario['dom_foto'] = BASE_URL . ltrim($datosDomiciliario['dom_foto'], '/');
        }

        $estadisticas = [
            'totalEntregas' => $this->modeloPedidos->contarPedidosPorDomiciliario($datosDomiciliario['id']),
            'calificacionPromedio' => $this->modeloDomiciliario->obtenerCalificacionPromedio($datosDomiciliario['id']),
            'tiempoPromedioEntrega' => $this->modeloPedidos->calcularTiempoPromedioEntrega($datosDomiciliario['id'])
        ];

        $datos = [
            'domiciliario' => $datosDomiciliario,
            'estadisticas' => $estadisticas,
            'esPropioDomiciliario' => true
        ];

        require_once 'vista/Domiciliario/perfilDomiciliario.php';
    }

    private function manejarSubidaFotoDomiciliario($archivo) {
        $directorioDestino = './vista/recursos/img/domiciliarios/';
        $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        error_log("Iniciando subida de archivo: " . $archivo['name']);

        if (!file_exists($directorioDestino)) {
            if (!mkdir($directorioDestino, 0777, true)) {
                error_log("Error: No se pudo crear el directorio " . $directorioDestino);
                return ['exito' => false, 'mensaje' => 'No se pudo crear el directorio para las imágenes.'];
            }
        }

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            error_log("Error: Tipo de archivo no permitido - " . $archivo['type']);
            return ['exito' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF.'];
        }

        if ($archivo['size'] > 5000000) {
            error_log("Error: Archivo demasiado grande - " . $archivo['size'] . " bytes");
            return ['exito' => false, 'mensaje' => 'El archivo es demasiado grande. El tamaño máximo es 5MB.'];
        }

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            error_log("Archivo subido exitosamente: " . $rutaCompleta);
            return ['exito' => true, 'ruta' => $rutaCompleta];
        } else {
            $errorMsg = error_get_last() ? error_get_last()['message'] : "Error desconocido";
            error_log("Error al mover el archivo: " . $errorMsg);
            return ['exito' => false, 'mensaje' => 'Error al subir el archivo. Por favor, inténtelo de nuevo.'];
        }
    }

    public function activarDomiciliario($id) {
        if ($this->modeloDomiciliario->activarDomiciliario($id)) {
            $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($id);
            $mensaje = "Tu cuenta de domiciliario ha sido activada. Ya puedes iniciar sesión con tu correo: {$domiciliario['correo']}.";
            $this->modeloNotificacion->crearNotificacion('informacion', $mensaje, $domiciliario['id_usuario']);
            return true;
        }
        return false;
    }

    public function desactivarDomiciliario($id) {
        if ($this->modeloDomiciliario->desactivarDomiciliario($id)) {
            $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($id);
            $mensaje = "Tu cuenta de domiciliario ha sido desactivada. Por favor, contacta con el administrador para más información.";
            $this->modeloNotificacion->crearNotificacion('informacion', $mensaje, $domiciliario['id_usuario']);
            return true;
        }
        return false;
    }

    public function mostrarNotificacionesPorDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);

        if (!$domiciliario) {
            error_log("No se encontró el domiciliario para el usuario con correo: $correoDomiciliario");
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $id_domiciliario = $domiciliario['id'];

        $notificaciones = $this->modeloNotificacion->obtenerNotificacionesPorUsuario($id_domiciliario);

        // Depuración
        error_log("ID del domiciliario: " . $id_domiciliario);
        error_log("Notificaciones obtenidas: " . print_r($notificaciones, true));

        // Preparar los datos para la vista
        $tipoNotificacion = isset($_GET['tipo']) ? $_GET['tipo'] : 'pedido'; // Valor por defecto: 'pedido'

        $datos = [
            'tipoNotificacion' => $tipoNotificacion,
            'notificaciones' => $notificaciones,
            'tipoUsuario' => 'domiciliario'
        ];

        require_once 'vista/Domiciliario/notificaciones_domiciliario.php';
    }

    public function mostrarEstadisticas() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header("Location: index.php?controlador=usuarios&accion=login");
            exit();
        }

        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);

        if (!$datosDomiciliario) {
            $_SESSION['error_mensaje'] = "No se encontró la información del domiciliario.";
            header("Location: index.php?controlador=paginas&accion=inicio");
            exit();
        }

        $estadisticas = [
            'totalEntregas' => $this->modeloPedidos->contarPedidosPorDomiciliario($datosDomiciliario['id']),
            'calificacionPromedio' => $this->modeloDomiciliario->obtenerCalificacionPromedio($datosDomiciliario['id']),
            'tiempoPromedioEntrega' => $this->modeloPedidos->calcularTiempoPromedioEntrega($datosDomiciliario['id']),
            'pedidosPorMes' => $this->modeloPedidos->obtenerPedidosPorMes($datosDomiciliario['id']),
            'pedidosRecientes' => $this->modeloPedidos->obtenerPedidosRecientesDomiciliario($datosDomiciliario['id'], 10),
            'ingresosTotales' => $this->modeloPedidos->calcularIngresosTotalesDomiciliario($datosDomiciliario['id']),
            'pedidosPendientes' => $this->modeloPedidos->contarPedidosPorEstadoDomiciliario($datosDomiciliario['id'], 'pendiente'),
            'pedidosEntregados' => $this->modeloPedidos->contarPedidosPorEstadoDomiciliario($datosDomiciliario['id'], 'entregado')
        ];

        $datos = [
            'domiciliario' => $datosDomiciliario,
            'estadisticas' => $estadisticas
        ];

        require_once 'vista/Domiciliario/estadisticas.php';
    }

    public function pedidosDisponibles() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $pedidosDisponibles = $this->modeloPedidos->obtenerPedidosListosParaEntrega();
    
        // Extraer la variable del array $datos
        extract(['pedidosDisponibles' => $pedidosDisponibles]);
    
        require_once 'vista/paginas/Domiciliarios/pedidosDisponibles.php';
    }

    public function misPedidos() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        $idDomiciliario = $domiciliario['id'];
    
        $pedidosDisponibles = $this->modeloPedidos->obtenerPedidosListosParaEntrega();
        $pedidosAsignados = $this->modeloPedidos->obtenerPedidosEnCaminoPorDomiciliario($idDomiciliario);
    
        // Extraer las variables del array $datos
        extract([
            'domiciliario' => $domiciliario,
            'pedidosDisponibles' => $pedidosDisponibles,
            'pedidosAsignados' => $pedidosAsignados
        ]);
    
        require_once 'vista/Domiciliario/pedidos_domiciliario.php';
    }

    public function gestionPedidos() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
    
        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        $idDomiciliario = $domiciliario['id'];
    
        $pedidosDisponibles = $this->modeloPedidos->obtenerPedidosListosParaEntrega();
        $pedidosAsignados = $this->modeloPedidos->obtenerPedidosEnCaminoPorDomiciliario($idDomiciliario);
    
        // Depuración
        error_log("Pedidos Disponibles: " . print_r($pedidosDisponibles, true));
        error_log("Pedidos Asignados: " . print_r($pedidosAsignados, true));
    
        // Extraer las variables del array $datos
        extract([
            'domiciliario' => $domiciliario,
            'pedidosDisponibles' => $pedidosDisponibles,
            'pedidosAsignados' => $pedidosAsignados
        ]);
    
        require_once 'vista/Domiciliario/pedidos_domiciliario.php';
    }

    public function historialPedidos() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $correoDomiciliario = $_SESSION['usuario']['correo'];
        $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);
        $idDomiciliario = $domiciliario['id'];

        $historialPedidos = $this->modeloDomiciliario->obtenerHistorialPedidos($idDomiciliario);

        $datos = [
            'domiciliario' => $domiciliario,
            'historialPedidos' => $historialPedidos
        ];

        require_once 'vista/Domiciliario/historial_pedidos.php';
    }
}
?>