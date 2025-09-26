<?php
// controladores/controlador_notificaciones.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once './modelos/Notificacion.php';
require_once './modelos/Tienda.php';
require_once './modelos/Domiciliario.php';
require_once './modelos/usuarios.php';

class ControladorNotificaciones {
    private $db;
    private $modelo;
    private $modeloTienda;
    private $modeloDomiciliario;
    private $modeloNotificaciones;
    private $modeloUsuarios;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
        $this->modelo = new Notificaciones();
        $this->modeloTienda = new Tienda();
        $this->modeloDomiciliario = new Domiciliario();
        $this->modeloNotificaciones = new Notificaciones();
    }

    public function mostrarNotificaciones() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $tipoUsuario = $_SESSION['usuario']['id_rol'];
        $idUsuario = $_SESSION['usuario']['id'];
        $tipoNotificacionSeleccionado = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

        // Obtener todas las notificaciones
        $todasLasNotificaciones = $this->obtenerNotificacionesSegunTipoUsuario($tipoUsuario, $idUsuario, 'todas');

        // Obtener las notificaciones filtradas según el tipo seleccionado
        $notificacionesFiltradas = $this->obtenerNotificacionesSegunTipoUsuario($tipoUsuario, $idUsuario, $tipoNotificacionSeleccionado);

        // Calcular el conteo de notificaciones por tipo
        $conteoNotificaciones = $this->contarNotificacionesPorTipo($todasLasNotificaciones);

        $datos = [
            'tipoNotificacion' => $tipoNotificacionSeleccionado,
            'notificaciones' => $notificacionesFiltradas,
            'conteoNotificaciones' => $conteoNotificaciones,
            'tipoUsuario' => $this->obtenerTipoUsuario($tipoUsuario)
        ];

        require_once 'vista/modulos/notificaciones.php';
    }

    private function obtenerNotificacionesSegunTipoUsuario($tipoUsuario, $idUsuario, $tipoNotificacion) {
        switch ($tipoUsuario) {
            case 1: // Administrador
                return $this->modeloNotificaciones->obtenerNotificacionesAdmin($tipoNotificacion);
            case 2: // Usuario normal
                return $this->modeloNotificaciones->obtenerNotificacionesPorUsuario($idUsuario, $tipoNotificacion);
            case 3: // Tienda
                $correoTienda = $_SESSION['usuario']['correo'];
                return $this->modeloNotificaciones->obtenerNotificacionesPorTienda($correoTienda, $tipoNotificacion);
            case 4: // Domiciliario
                $correoDomiciliario = $_SESSION['usuario']['correo'];
                return $this->modeloNotificaciones->obtenerNotificacionesPorDomiciliario($correoDomiciliario, $tipoNotificacion);
            default:
                return [];
        }
    }

    private function contarNotificacionesPorTipo($notificaciones) {
        $conteo = ['todas' => count($notificaciones)];
        foreach ($notificaciones as $notificacion) {
            if (!isset($conteo[$notificacion['tipo']])) {
                $conteo[$notificacion['tipo']] = 0;
            }
            $conteo[$notificacion['tipo']]++;
        }
        return $conteo;
    }

    private function obtenerTipoUsuario($idRol) {
        switch ($idRol) {
            case 1: return 'administrador';
            case 2: return 'usuario';
            case 3: return 'tienda';
            case 4: return 'domiciliario';
            default: return 'visitante';
        }
    }

    public function obtenerContadorNotificaciones() {
    if (!isset($_SESSION['usuario'])) {
        return 0;
    }

        $idUsuario = $_SESSION['usuario']['id'];
        return $this->modelo->contarNotificaciones($idUsuario);
    }

    public function marcarComoLeido() {
        if (!isset($_GET['id'])) {
            header('Location: ' . N_URL . 'mostrarNotificaciones');
            exit;
        }
        $id = $_GET['id'];
        $this->modelo->marcarComoLeida($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function borrarNotificacion() {
        if (!isset($_GET['id'])) {
            header('Location: ' . N_URL . 'mostrarNotificaciones');
            exit;
        }
        $id = $_GET['id'];
        $this->modelo->eliminarNotificacion($id);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function marcarTodasComoLeidas() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
        $idUsuario = $_SESSION['usuario']['id'];
        $this->modelo->marcarTodasComoLeidas($idUsuario);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function eliminarTodasLasNotificaciones() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . S_URL . 'login');
            exit;
        }
        $idUsuario = $_SESSION['usuario']['id'];
        $this->modelo->eliminarTodasLasNotificaciones($idUsuario);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    public function crearNotificacion($tipo, $mensaje, $id_usuario, $esAdmin = false) {
        // Verificar si ya existe una notificación similar
        $notificacionExistente = $this->modelo->obtenerNotificacionPorMensaje($mensaje, $id_usuario);
        if (!$notificacionExistente) {
            return $this->modelo->crearNotificacion($tipo, $mensaje, $id_usuario, $esAdmin);
        }
        return false; // No crear si ya existe una similar
    }

    public function crearNotificacionSolicitud($id_usuario) {
        $tipo = 'solicitud';
        $mensaje = 'Se ha registrado una nueva solicitud';
        $this->modelo->crearNotificacion($tipo, $mensaje, null, true); // Pasamos true para esAdmin
    }

    public function aceptarSolicitud($id) {
        try {
            $notificacion = $this->modelo->obtenerNotificacionPorId($id);
            if (!$notificacion) {
                throw new Exception("No se encontró la notificación.");
            }

            // Extraer el correo y el nombre del domiciliario/tienda del mensaje de la notificación
            preg_match('/Correo:\s*([^\s,]+)/', $notificacion['mensaje'], $matches);
            $correo = isset($matches[1]) ? $matches[1] : null;
            preg_match('/domiciliario:\s*([^,]+)/', $notificacion['mensaje'], $matches);
            $nombreDomiciliario = isset($matches[1]) ? trim($matches[1]) : null;
            preg_match('/tienda:\s*([^,]+)/', $notificacion['mensaje'], $matches);
            $nombreTienda = isset($matches[1]) ? trim($matches[1]) : null;

            if (!$correo) {
                throw new Exception("No se pudo extraer el correo de la notificación.");
            }

            // Buscar la notificación original enviada al usuario
            $mensajeBusqueda = $nombreDomiciliario 
                ? "Tu solicitud para registrar el domiciliario '$nombreDomiciliario' ha sido enviada con éxito."
                : "Tu solicitud para registrar la tienda '$nombreTienda' ha sido enviada con éxito.";
            $notificacionOriginal = $this->modelo->obtenerNotificacionPorMensaje($mensajeBusqueda);
            if (!$notificacionOriginal) {
                throw new Exception("No se encontró la notificación original para el usuario.");
            }
            $id_usuario_original = $notificacionOriginal['usuario_id'];

            // Procesar la solicitud de domiciliario o tienda
            if ($nombreDomiciliario) {
                $datosDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correo);
                if (!$datosDomiciliario) {
                    throw new Exception("No se encontraron datos para el domiciliario.");
                }
                $id_usuario_nuevo = $this->procesarSolicitudDomiciliario($datosDomiciliario);
                $mensaje = "Tu solicitud como domiciliario '$nombreDomiciliario' ha sido aprobada. Tu nombre de usuario es: $correo.";
            } else {
                $datosTienda = $this->modeloTienda->obtenerDatosTienda($correo);
                if (!$datosTienda) {
                    throw new Exception("No se encontraron datos para la tienda.");
                }
                $id_usuario_nuevo = $this->procesarSolicitudTienda($datosTienda);
                $mensaje = "Tu solicitud para la tienda '$nombreTienda' ha sido aprobada. Tu nombre de usuario es: $correo.";
            }

            // Crear notificación para el usuario original
            $this->modelo->crearNotificacion('informacion', $mensaje, $id_usuario_original);

            // Crear notificación para el administrador
            $mensajeAdmin = "La solicitud de " . ($nombreDomiciliario ? "el domiciliario '$nombreDomiciliario'" : "la tienda '$nombreTienda'") . " ha sido aprobada.";
            $this->modelo->crearNotificacion('informacion', $mensajeAdmin, null, true);

            // Marcar la notificación original como leída
            $this->modelo->marcarComoLeida($id);

            header("Location: " . N_URL . "mostrarNotificaciones&tipo=solicitud");
            exit();

        } catch (Exception $e) {
            error_log("Error en aceptarSolicitud: " . $e->getMessage());
            header("Location: " . N_URL . "mostrarNotificaciones&tipo=solicitud&error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    private function procesarSolicitudTienda($datosTienda) {
        error_log("Procesando solicitud de tienda para: " . $datosTienda['nombre']);
        
        $resultadoCreacion = Usuario::CrearUsuario(
            $datosTienda['nombre'],
            $datosTienda['correo'],
            $datosTienda['password'],
            $datosTienda['direccion'],
            $datosTienda['telefono'],
            'tienda'
        );

        error_log("Resultado de la creación del usuario: " . ($resultadoCreacion ? "Éxito" : "Fallo"));

        if (!$resultadoCreacion) {
            error_log("Error al crear usuario para la tienda: " . $datosTienda['nombre']);
            error_log("Último error de PDO: " . print_r(Usuario::getLastError(), true));
            throw new Exception("Error al crear usuario para la tienda.");
        }

        // Activar la tienda
        $resultadoActivacion = $this->modeloTienda->activarTienda($datosTienda['correo']);
        error_log("Resultado de la activación de la tienda: " . ($resultadoActivacion ? "Éxito" : "Fallo"));

        if (!$resultadoActivacion) {
            error_log("Error al activar la tienda: " . $datosTienda['nombre']);
            throw new Exception("Error al activar la tienda.");
        }

        // Obtener el ID del usuario recién creado
        $id_usuario = $this->obtenerIdUsuarioPorCorreo($datosTienda['correo']);
        if (!$id_usuario) {
            throw new Exception("No se pudo obtener el ID del usuario recién creado.");
        }

        return $id_usuario;
    }

    private function procesarSolicitudDomiciliario($datosDomiciliario) {
        error_log("Procesando solicitud de domiciliario para: " . $datosDomiciliario['nombre']);
        
        $resultadoCreacion = Usuario::CrearUsuario(
            $datosDomiciliario['nombre'],
            $datosDomiciliario['correo'],
            $datosDomiciliario['password'],
            $datosDomiciliario['direccion'],
            $datosDomiciliario['telefono'],
            'domiciliario'
        );

        if (!$resultadoCreacion) {
            error_log("Error al crear usuario para el domiciliario: " . $datosDomiciliario['nombre']);
            throw new Exception("Error al crear usuario para el domiciliario.");
        }

        // Activar el domiciliario
        $resultadoActivacion = $this->modeloDomiciliario->activarDomiciliario($datosDomiciliario['correo']);
        if (!$resultadoActivacion) {
            error_log("Error al activar el domiciliario: " . $datosDomiciliario['nombre']);
            throw new Exception("Error al activar el domiciliario.");
        }

        // Obtener el ID del usuario recién creado
        $id_usuario = $this->obtenerIdUsuarioPorCorreo($datosDomiciliario['correo']);
        if (!$id_usuario) {
            throw new Exception("No se pudo obtener el ID del usuario recién creado.");
        }

        return $id_usuario;
    }

    public function rechazarSolicitud($id) {
        $notificacion = $this->modelo->obtenerNotificacionPorId($id);
        if (!$notificacion) {
            throw new Exception("No se encontró la notificación.");
        }

        // Extraer el correo del mensaje de la notificación
        preg_match('/Correo:\s*([^\s,]+)/', $notificacion['mensaje'], $matches);
        $correo = isset($matches[1]) ? $matches[1] : null;

        if ($correo) {
            // Eliminar la tienda o domiciliario de la base de datos
            $this->modeloTienda->eliminarTiendaPorCorreo($correo);
            $this->modeloDomiciliario->eliminarDomiciliarioPorCorreo($correo);

            // Crear una notificación de rechazo
            $mensaje = "Lo sentimos, tu solicitud ha sido rechazada.";
            $this->modelo->crearNotificacion('informacion', $mensaje, $notificacion['usuario_id']);
        }

        // Eliminar la notificación original
        $this->modelo->eliminarNotificacion($id);

        header("Location: " . N_URL . "mostrarNotificaciones&tipo=solicitud");
        exit();
    }

    public function mostrarNotificacionesPorTienda() {
        // Verificar si el usuario ha iniciado sesión y es una tienda
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $correoTienda = $_SESSION['usuario']['correo'];
        $tienda = $this->modeloTienda->obtenerTiendaPorUsuario($_SESSION['usuario']['id']);

        if (!$tienda) {
            error_log("No se encontró la tienda para el usuario con correo: $correoTienda");
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $id_tienda = $tienda['id'];
        $notificaciones = $this->modelo->obtenerNotificacionesPorTienda($id_tienda);
        
        // Depuración
        error_log("ID de la tienda: " . $id_tienda);
        error_log("Notificaciones obtenidas: " . print_r($notificaciones, true));

        // Preparar los datos para la vista
        $tipoNotificacion = isset($_GET['tipo']) ? $_GET['tipo'] : 'pedido'; // Valor por defecto: 'pedido'

        $datos = [
            'tipoNotificacion' => $tipoNotificacion,
            'notificaciones' => $notificaciones,
            'tipoUsuario' => 'tienda'
        ];

        require_once 'vista/modulos/lista_notificaciones.php';
    }

    public function mostrarNotificacionesPorUsuario() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idUsuario = $_SESSION['usuario']['id'];
        $rolUsuario = $_SESSION['usuario']['id_rol'];

        // Determinar el tipo de usuario
        $tipoUsuario = ($rolUsuario == 4) ? 'domiciliario' : 'usuario';

        // Obtener notificaciones según el tipo de usuario
        if ($tipoUsuario == 'domiciliario') {
            $correoDomiciliario = $_SESSION['usuario']['correo'];
            $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorCorreo($correoDomiciliario);

            if (!$domiciliario) {
                error_log("No se encontró el domiciliario para el usuario con correo: $correoDomiciliario");
                $notificaciones = [];
            } else {
                $id_domiciliario = $domiciliario['id'];
                $notificaciones = $this->modelo->obtenerNotificacionesPorUsuario($id_domiciliario);
            }
        } else {
            $notificaciones = $this->modelo->obtenerNotificacionesPorUsuario($idUsuario);
        }

        // Preparar los datos para la vista
        $tipoNotificacion = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

        $datos = [
            'tipoNotificacion' => $tipoNotificacion,
            'notificaciones' => $notificaciones,
            'tipoUsuario' => $tipoUsuario
        ];

        require_once 'vista/modulos/lista_notificaciones.php';
    }

    public function mostrarNotificacionesPorDomiciliario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $correoDomiciliario = $_SESSION['usuario']['email'];
        $tipoNotificacion = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

        $notificaciones = $this->modelo->obtenerNotificacionesPorDomiciliario($correoDomiciliario, $tipoNotificacion);

        // Depuración
        error_log("Correo del domiciliario: " . $correoDomiciliario);
        error_log("Tipo de notificación: " . $tipoNotificacion);
        error_log("Notificaciones obtenidas: " . print_r($notificaciones, true));

        $datos = [
            'tipoNotificacion' => $tipoNotificacion,
            'notificaciones' => $notificaciones,
            'tipoUsuario' => 'domiciliario'
        ];

        require_once 'vista/modulos/lista_notificaciones.php';
    }

    public function obtenerNotificacionesActualizadas() {
        if (!isset($_SESSION['usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            return;
        }

        $idUsuario = $_SESSION['usuario']['id'];
        $rolUsuario = $_SESSION['usuario']['id_rol'];

        try {
            switch ($rolUsuario) {
                case 1: // Admin
                    $notificaciones = $this->modelo->obtenerNotificacionesAdmin();
                    break;
                case 3: // Tienda
                    $notificaciones = $this->modelo->obtenerNotificacionesPorTienda($_SESSION['usuario']['correo']);
                    break;
                case 4: // Domiciliario
                    $notificaciones = $this->modelo->obtenerNotificacionesPorDomiciliario($_SESSION['usuario']['correo']);
                    break;
                default: // Usuario normal
                    $notificaciones = $this->modelo->obtenerNotificacionesPorUsuario($idUsuario);
            }

            // Generar el HTML para las notificaciones
            ob_start();
            include 'vista/modulos/notificaciones_contenido.php';
            $notificacionesHtml = ob_get_clean();

            echo json_encode(['success' => true, 'notificaciones' => $notificacionesHtml]);
        } catch (Exception $e) {
            error_log("Error en obtenerNotificacionesActualizadas: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al obtener notificaciones']);
        }
    }

    private function obtenerIdUsuarioPorCorreo($correo) {
        $sql = "SELECT id FROM usuarios WHERE email = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['id'] : null;
    }

    public function eliminarNotificacion() {
        try {
            if (!isset($_GET['id'])) {
                throw new Exception("No se proporcionó el ID de la notificación.");
            }
            $id = intval($_GET['id']);

            // Verificar si el usuario tiene permiso para eliminar esta notificación
            $notificacion = $this->modelo->obtenerNotificacionPorId($id);
            if (!$notificacion) {
                throw new Exception("No se encontró la notificación.");
            }

            // Verificar si el usuario actual es el propietario de la notificación o es un administrador
            if ($_SESSION['usuario']['id'] != $notificacion['usuario_id'] && $_SESSION['usuario']['id_rol'] != 1) {
                throw new Exception("No tienes permiso para eliminar esta notificación.");
            }

            $resultado = $this->modelo->eliminarNotificacion($id);
            if (!$resultado) {
                throw new Exception("No se pudo eliminar la notificación.");
            }

            // Redirigir de vuelta a la página de notificaciones
            header("Location: " . N_URL . "mostrarNotificaciones&tipo=todas");
            exit();

        } catch (Exception $e) {
            error_log("Error en eliminarNotificacion: " . $e->getMessage());
            header("Location: " . N_URL . "mostrarNotificaciones&tipo=todas&error=" . urlencode($e->getMessage()));
            exit();
        }
    }

}












