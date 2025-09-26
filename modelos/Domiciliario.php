<?php
// modelos/Domiciliario.php
require_once './conexion.php';

class Domiciliario {
    private $db;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
    }

    public function correoExiste($correo) {
        $sql = "SELECT COUNT(*) FROM domiciliario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function registrarDomiciliario($datosDomiciliario) {
        $datosDomiciliario['password'] = password_hash($datosDomiciliario['password'], PASSWORD_DEFAULT);
        
        if ($this->correoExiste($datosDomiciliario['correo'])) {
            throw new Exception("El correo '{$datosDomiciliario['correo']}' ya está registrado.");
        }

        // Asignar una imagen por defecto si no se proporciona una
        if (!isset($datosDomiciliario['dom_foto']) || empty($datosDomiciliario['dom_foto'])) {
            $datosDomiciliario['dom_foto'] = 'vista/recursos/img/domiciliarios/default.jpg';
        }

        $sql = "INSERT INTO domiciliario (nombre, apellido, telefono, correo, password, dom_foto, direccion, vehiculo, fecha_registro, activo) 
                VALUES (:nombre, :apellido, :telefono, :correo, :password, :dom_foto, :direccion, :vehiculo, NOW(), 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datosDomiciliario['nombre'],
            ':apellido' => $datosDomiciliario['apellido'],
            ':telefono' => $datosDomiciliario['telefono'],
            ':correo' => $datosDomiciliario['correo'],
            ':password' => $datosDomiciliario['password'],
            ':dom_foto' => $datosDomiciliario['dom_foto'],
            ':direccion' => $datosDomiciliario['direccion'],
            ':vehiculo' => $datosDomiciliario['vehiculo']
        ]);
    }

    public function obtenerDomiciliarioPorId($id) {
        $sql = "SELECT * FROM domiciliario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resultado) {
            error_log("No se encontró domiciliario con ID: " . $id);
        }
        
        return $resultado;
    }

    public function obtenerDomiciliarioPorCorreo($correo) {
        $sql = "SELECT * FROM domiciliario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resultado) {
            error_log("No se encontró domiciliario con correo: " . $correo);
        }
        
        return $resultado;
    }

    public function actualizarPerfilDomiciliario($datos) {
        $sql = "UPDATE domiciliario SET 
            nombre = :nombre, 
            apellido = :apellido, 
            telefono = :telefono, 
            direccion = :direccion, 
            vehiculo = :vehiculo";
        
        $params = [
            ':nombre' => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':telefono' => $datos['telefono'],
            ':direccion' => $datos['direccion'],
            ':vehiculo' => $datos['vehiculo'],
            ':id' => $datos['id']
        ];

        if (isset($datos['dom_foto'])) {
            $sql .= ", dom_foto = :dom_foto";
            $params[':dom_foto'] = $datos['dom_foto'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function obtenerTodosDomiciliarios() {
        $sql = "SELECT * FROM domiciliario ORDER BY fecha_registro DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activarDomiciliario($correo) {
        $sql = "UPDATE domiciliario SET activo = 1 WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function desactivarDomiciliario($correo) {
        $sql = "UPDATE domiciliario SET activo = 0 WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function obtenerUltimosPedidosPorDomiciliario($idDomiciliario, $limite) {
        $sql = "SELECT * FROM pedido WHERE id_domiciliario = :idDomiciliario ORDER BY fecha_hora_pedido DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerCalificacionPromedio($idDomiciliario) {
        $sql = "SELECT AVG(puntuacion_domiciliario) as promedio 
            FROM calificacion 
            WHERE id_domiciliario = :idDomiciliario";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? round($resultado['promedio'], 1) : 0;
    }

    public function obtenerDomiciliariosActivos() {
        $sql = "SELECT id, nombre, apellido, telefono, dom_foto, vehiculo, 
            (SELECT AVG(puntuacion_domiciliario) FROM calificacion WHERE id_domiciliario = domiciliario.id) AS calificacion
            FROM domiciliario 
            WHERE activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarDomiciliarioPorCorreo($correo) {
        $sql = "DELETE FROM domiciliario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function obtenerDomiciliariosDisponibles() {
        $sql = "SELECT d.id, d.nombre, d.apellido, d.correo
                FROM domiciliario d
                WHERE d.activo = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIdDomiciliarioPorUsuario($correoUsuario) {
        $sql = "SELECT id FROM domiciliario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correoUsuario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerCorreoPorDomiciliario($idDomiciliario) {
    $sql = "SELECT correo FROM domiciliario WHERE id = :idDomiciliario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerIdDomiciliarioPorCorreo($correoDomiciliario) {
        $sql = "SELECT id FROM domiciliario WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correoDomiciliario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerCorreoPorId($idDomiciliario) {
    $sql = "SELECT correo FROM domiciliario WHERE id = :idDomiciliario";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
    $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerHistorialPedidos($idDomiciliario, $limite = 50) {
        $sql = "SELECT p.id, p.fecha_hora_pedido, p.estado, p.direccion_entrega, u.nombre as nombre_cliente
            FROM pedido p
            JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.id_domiciliario = :idDomiciliario
            ORDER BY p.fecha_hora_pedido DESC
            LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarDomiciliario($id) {
        $sql = "DELETE FROM domiciliario WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
