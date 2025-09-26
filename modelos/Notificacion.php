<?php
// Notificacion.php
require_once './conexion.php';

class Notificaciones {
    private $db;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
    }

    public function obtenerNotificaciones($tipo, $esAdmin = false) {
        $sql = "SELECT * FROM notificaciones WHERE tipo = ? AND es_admin = ? ORDER BY fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo, $esAdmin ? 1 : 0]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionPorId($id) {
        $sql = "SELECT * FROM notificaciones WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearNotificacion($tipo, $mensaje, $id_usuario = null, $esAdmin = false, $correoTienda = null, $correoDomiciliario = null) {
        // Verificar si ya existe una notificación similar
        if ($this->existeNotificacionSimilar($tipo, $mensaje, $id_usuario)) {
            return false; // No crear notificación si ya existe una similar
        }

        // Si es una notificación de administrador y no se proporciona un id_usuario
        if ($esAdmin && $id_usuario === null) {
            // Obtener el ID del usuario administrador (asumiendo que tiene id_rol = 1)
            $sqlAdmin = "SELECT id FROM usuarios WHERE id_rol = 1 LIMIT 1";
            $stmtAdmin = $this->db->prepare($sqlAdmin);
            $stmtAdmin->execute();
            $resultadoAdmin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
            if ($resultadoAdmin) {
                $id_usuario = $resultadoAdmin['id'];
            }
        }

        if ($correoTienda) {
            // Obtener el id de usuario correspondiente a la tienda
            $sqlUsuario = "SELECT u.id FROM usuarios u JOIN tienda t ON u.email = t.correo WHERE t.correo = :correo";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':correo', $correoTienda);
            $stmtUsuario->execute();
            $resultado = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                $id_usuario = $resultado['id'];
            } else {
                return false; // No se encontró el usuario correspondiente a la tienda
            }
        }

        if ($correoDomiciliario) {
            // Obtener el id de usuario correspondiente al domiciliario
            $sqlUsuario = "SELECT u.id FROM usuarios u JOIN domiciliario d ON u.email = d.correo WHERE d.correo = :correo";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':correo', $correoDomiciliario);
            $stmtUsuario->execute();
            $resultado = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
            if ($resultado) {
                $id_usuario = $resultado['id'];
            } else {
                return false; // No se encontró el usuario correspondiente al domiciliario
            }
        }
    

    
        $sql = "INSERT INTO notificaciones (tipo, mensaje, usuario_id, es_admin) VALUES (:tipo, :mensaje, :usuario_id, :es_admin)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':mensaje', $mensaje);
        $stmt->bindParam(':usuario_id', $id_usuario, PDO::PARAM_INT);
        $esAdminInt = $esAdmin ? 1 : 0;
        $stmt->bindParam(':es_admin', $esAdminInt, PDO::PARAM_INT);
    
        return $stmt->execute();
    }

    public function marcarComoLeida($id) {
        $sql = "UPDATE notificaciones SET estado = 'leida' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarNotificacion($id) {
        $sql = "DELETE FROM notificaciones WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function contarNotificaciones($idUsuario) {
        $sql = "SELECT COUNT(*) AS total FROM notificaciones 
                WHERE usuario_id = :idUsuario AND estado = 'no_leida'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function obtenerNotificacionPorMensaje($mensaje) {
        $sql = "SELECT * FROM notificaciones WHERE mensaje LIKE :mensaje LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $mensajeLike = '%' . $mensaje . '%';
        $stmt->bindParam(':mensaje', $mensajeLike);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionesNoLeidasPorTienda($correoTienda) {
        $sql = "SELECT n.* FROM notificaciones n
                JOIN usuarios u ON n.usuario_id = u.id
                JOIN tienda t ON u.email = t.correo
                WHERE t.correo = :correoTienda AND n.estado = 'no_leida'
                ORDER BY n.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correoTienda', $correoTienda, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionesPorTienda($correoTienda) {
        $sql = "SELECT n.* FROM notificaciones n 
                JOIN usuarios u ON n.usuario_id = u.id
                JOIN tienda t ON u.email = t.correo
                WHERE t.correo = :correoTienda
                ORDER BY n.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correoTienda', $correoTienda, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionesPorDomiciliario($correoDomiciliario, $tipo = 'todas') {
        $sql = "SELECT n.* FROM notificaciones n
                JOIN usuarios u ON n.usuario_id = u.id
                JOIN domiciliario d ON u.email = d.correo
                WHERE d.correo = :correoDomiciliario";
        
        if ($tipo !== 'todas') {
            $sql .= " AND n.tipo = :tipo";
        }
        
        $sql .= " ORDER BY n.fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correoDomiciliario', $correoDomiciliario, PDO::PARAM_STR);
        
        if ($tipo !== 'todas') {
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionesNoLeidasPorDomiciliario($correoDomiciliario) {
        $sql = "SELECT n.* FROM notificaciones n
                JOIN usuarios u ON n.usuario_id = u.id
                JOIN domiciliario d ON u.email = d.correo
                WHERE d.correo = :correoDomiciliario AND n.estado = 'no_leida'
                ORDER BY n.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correoDomiciliario', $correoDomiciliario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerNotificacionesPorUsuario($idUsuario, $tipo = 'todas') {
        $sql = "SELECT * FROM notificaciones WHERE usuario_id = :idUsuario";
        
        if ($tipo !== 'todas') {
            $sql .= " AND tipo = :tipo";
        }
        
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        
        if ($tipo !== 'todas') {
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodasLasNotificaciones() {
        $sql = "SELECT * FROM notificaciones ORDER BY fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarTodasComoLeidas($idUsuario) {
        $sql = "UPDATE notificaciones SET estado = 'leida' WHERE usuario_id = :idUsuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarTodasLasNotificaciones($idUsuario) {
        $sql = "DELETE FROM notificaciones WHERE usuario_id = :idUsuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerTodasLasNotificacionesAdmin() {
        $sql = "SELECT * FROM notificaciones WHERE es_admin = 1 ORDER BY fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeNotificacionSimilar($tipo, $mensaje, $usuario_id, $tiempo_limite = 300) {
        $sql = "SELECT * FROM notificaciones 
                WHERE tipo = :tipo 
                AND usuario_id = :usuario_id 
                AND mensaje LIKE :mensaje 
                AND fecha > DATE_SUB(NOW(), INTERVAL :tiempo_limite SECOND)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $mensajeParcial = '%' . substr($mensaje, 0, 50) . '%';
        $stmt->bindParam(':mensaje', $mensajeParcial);
        $stmt->bindParam(':tiempo_limite', $tiempo_limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function obtenerNotificacionesAdmin($tipo = 'todas') {
        $sql = "SELECT * FROM notificaciones WHERE es_admin = 1";
        
        if ($tipo !== 'todas') {
            $sql .= " AND tipo = :tipo";
        }
        
        $sql .= " ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($tipo !== 'todas') {
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
