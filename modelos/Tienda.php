<?php
// modelos/Tienda.php
require_once './conexion.php';

class Tienda   {
    private $db;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
    }

    public function correoExiste($correo) {
        $sql = "SELECT COUNT(*) FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Devuelve true si existe
    }

    public function registrarTienda($datosTienda) {
        // Verificar que $datosTienda no sea null
        if ($datosTienda === null) {
            error_log("Error: datosTienda es null");
            return false;
        }

        // Verificar que todas las claves necesarias existan
        $camposRequeridos = ['nombre', 'correo', 'password', 'direccion', 'telefono', 'tienda_foto', 'apertura', 'cierre', 'fecha_apertura', 'fecha_registro'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datosTienda[$campo])) {
                error_log("Error: Falta el campo $campo en datosTienda");
                return false;
            }
        }

        // Hashear la contraseña
        $datosTienda['password'] = password_hash($datosTienda['password'], PASSWORD_DEFAULT);
        
        error_log("Iniciando registro de tienda con datos: " . print_r($datosTienda, true));

        // Verificar si el correo ya existe
        if ($this->correoExiste($datosTienda['correo'])) {
            error_log("El correo '{$datosTienda['correo']}' ya está registrado.");
            throw new Exception("El correo '{$datosTienda['correo']}' ya está registrado.");
        }

        // Lógica para insertar los datos de la tienda en la base de datos
        $sql = "INSERT INTO tienda (nombre, correo, direccion, telefono, password, tienda_foto, horario_apertura, horario_cierre, fecha_apertura, fecha_registro, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute(array(
            $datosTienda['nombre'],
            $datosTienda['correo'],
            $datosTienda['direccion'],
            $datosTienda['telefono'],
            $datosTienda['password'],
            $datosTienda['tienda_foto'],
            $datosTienda['apertura'],
            $datosTienda['cierre'],
            $datosTienda['fecha_apertura'],
            $datosTienda['fecha_registro']
        ));

        error_log("Resultado de la inserción en la base de datos: " . ($resultado ? "Éxito" : "Fallo"));

        return $resultado;
    }

    public function obtenerDatosTienda($correo) {
        $sql = "SELECT * FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            // Si no hay foto, establecer una por defecto
            if (empty($resultado['tienda_foto'])) {
                $resultado['tienda_foto'] = '/vista/recursos/img/tiendas/default.jpg';
            } else {
                // Elimina './' del principio y asegúrate de que la ruta comience con una barra
                $resultado['tienda_foto'] = '/' . ltrim($resultado['tienda_foto'], './');
            }
        }
        
        error_log("Datos obtenidos de la tienda: " . print_r($resultado, true));
        
        return $resultado;
    }

    public function obtenerDatosTiendaPorNotificacion($datosNotificacion) {
        // Asumimos que la notificación contiene el correo electrónico de la tienda
        $sql = "SELECT nombre, correo AS correo, direccion, telefono, password 
                FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $datosNotificacion['correo_tienda'], PDO::PARAM_STR);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar si se encontró un resultado
        if ($resultado === false) {
            return []; // O lanzar una excepción
        }
        
        return $resultado;
    }

    public function activarTienda($correo) {
        $sql = "UPDATE tienda SET activo = 1 WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado = $stmt->execute();
        
        if ($resultado) {
            error_log("Tienda activada con éxito: " . $correo);
        } else {
            error_log("Error al activar la tienda: " . $correo);
            error_log("Error de base de datos: " . print_r($stmt->errorInfo(), true));
        }
        
        return $resultado;
    }

    public function BuscarTiendaPorCorreo($correo) {
        $sql = "SELECT * FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDatosTiendaPorCorreo($correo) {
        $sql = "SELECT id, nombre, correo, direccion, telefono, tienda_foto, horario_apertura, horario_cierre FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Datos obtenidos de la base de datos para la tienda con correo $correo: " . print_r($resultado, true));
        
        if ($resultado === false) {
            error_log("No se encontraron datos para la tienda con correo $correo");
            return null;
        }
        
        // Si no hay foto, establecer una por defecto
        if (!isset($resultado['tienda_foto']) || empty($resultado['tienda_foto'])) {
            $resultado['tienda_foto'] = '/vista/recursos/img/tiendas/default.jpg';
        } else {
            // Asegúrate de que la ruta comience con una barra
            $resultado['tienda_foto'] = '/' . ltrim($resultado['tienda_foto'], '/');
        }
        
        error_log("Datos de la tienda procesados: " . print_r($resultado, true));
        
        return $resultado;
    }

    public function existeTienda($id_tienda) {
        $sql = "SELECT id FROM tienda WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tienda]);
        return $stmt->fetch() !== false;
    }

    public function obtenerTiendaPorUsuario($id_usuario) {
        $sql = "SELECT t.* FROM tienda t 
                JOIN usuarios u ON t.correo = u.email 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDatosTiendaPorId($idTienda) {
        $sql = "SELECT t.*, u.id as id_usuario FROM tienda t 
                JOIN usuarios u ON t.correo = u.email 
                WHERE t.id = :idTienda";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCalificacionPromedio($idTienda) {
        $sql = "SELECT AVG(puntuacion_tienda) as promedio 
                FROM calificacion 
                WHERE id_tienda = :idTienda";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? round($resultado['promedio'], 1) : 0;
    }

    public function actualizarPerfilTiendaYUsuario($datos, $correoUsuario) {
        try {
            $this->db->beginTransaction();

            // Actualizar tabla tienda
            $sqlTienda = "UPDATE tienda SET nombre = :nombre, direccion = :direccion, telefono = :telefono, 
                          horario_apertura = :horario_apertura, horario_cierre = :horario_cierre";
            $paramsTienda = [
                ':nombre' => $datos['nombre'],
                ':direccion' => $datos['direccion'],
                ':telefono' => $datos['telefono'],
                ':horario_apertura' => $datos['horario_apertura'],
                ':horario_cierre' => $datos['horario_cierre']
            ];

            if (isset($datos['tienda_foto']) && !empty($datos['tienda_foto'])) {
                $sqlTienda .= ", tienda_foto = :tienda_foto";
                $paramsTienda[':tienda_foto'] = $datos['tienda_foto'];
            }

            $sqlTienda .= " WHERE id = :id";
            $paramsTienda[':id'] = $datos['id'];

            $stmtTienda = $this->db->prepare($sqlTienda);
            $stmtTienda->execute($paramsTienda);

            // Actualizar tabla usuarios
            $sqlUsuario = "UPDATE usuarios SET nombre = :nombre, telefono = :telefono WHERE email = :email";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->execute([
                ':nombre' => $datos['nombre'],
                ':telefono' => $datos['telefono'],
                ':email' => $correoUsuario
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al actualizar perfil de tienda y usuario: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTiendasActivas() {
        $sql = "SELECT * FROM tienda WHERE activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTiendaPorId($id) {
        $sql = "SELECT * FROM tienda WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminarTiendaPorCorreo($correo) {
        $sql = "DELETE FROM tienda WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function obtenerIdTiendaPorUsuario($idUsuario) {
        try {
            // Primero, obtenemos el correo del usuario
            $sqlUsuario = "SELECT email FROM usuarios WHERE id = :idUsuario";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $stmtUsuario->execute();
            $correoUsuario = $stmtUsuario->fetchColumn();
    
            if (!$correoUsuario) {
                return null; // No se encontró el usuario
            }
    
            // Ahora, usamos el correo para obtener el id de la tienda
            $sqlTienda = "SELECT id FROM tienda WHERE correo = :correo";
            $stmtTienda = $this->db->prepare($sqlTienda);
            $stmtTienda->bindParam(':correo', $correoUsuario, PDO::PARAM_STR);
            $stmtTienda->execute();
            return $stmtTienda->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error en obtenerIdTiendaPorUsuario: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerIdUsuarioPorTienda($idTienda) {
        try {
            // Primero, obtenemos el correo asociado a la tienda
            $sqlTienda = "SELECT correo FROM tienda WHERE id = :idTienda";
            $stmtTienda = $this->db->prepare($sqlTienda);
            $stmtTienda->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
            $stmtTienda->execute();
            $resultadoTienda = $stmtTienda->fetch(PDO::FETCH_ASSOC);
    
            if (!$resultadoTienda) {
                return null; // No se encontró la tienda
            }
    
            $correoTienda = $resultadoTienda['correo'];
    
            // Ahora, usamos el correo para obtener el id de usuario
            $sqlUsuario = "SELECT id FROM usuario WHERE correo = :correo";
            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->bindParam(':correo', $correoTienda, PDO::PARAM_STR);
            $stmtUsuario->execute();
            $resultadoUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
    
            return $resultadoUsuario ? $resultadoUsuario['id'] : null;
        } catch (PDOException $e) {
            error_log("Error al obtener id de usuario por tienda: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerCorreoPorTienda($idTienda) {
    $sql = "SELECT correo FROM tienda WHERE id = :idTienda";
    $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerIdUsuarioPorCorreo($correoTienda) {
        try {
            // Primero, verificamos si existe una tienda con ese correo
            $query = "SELECT id FROM tienda WHERE correo = :correo";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':correo', $correoTienda, PDO::PARAM_STR);
            $stmt->execute();
            $resultadoTienda = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultadoTienda) {
                error_log("No se encontró una tienda con el correo: " . $correoTienda);
                return null;
            }

            // Ahora, buscamos el usuario correspondiente en la tabla usuarios
            $query = "SELECT id FROM usuarios WHERE email = :correo AND id_rol = 3"; // 3 es el rol de tienda
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':correo', $correoTienda, PDO::PARAM_STR);
            $stmt->execute();
            $resultadoUsuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultadoUsuario) {
                error_log("No se encontró un usuario con el correo de tienda: " . $correoTienda);
                return null;
            }

            return $resultadoUsuario['id'];
        } catch (PDOException $e) {
            error_log("Error en obtenerIdUsuarioPorCorreo: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerCorreoPorId($idTienda) {
    $sql = "SELECT correo FROM tienda WHERE id = :idTienda";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
    $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerTodasLasTiendas() {
        $sql = "SELECT * FROM tienda ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearTienda($datosTienda) {
        // Campos requeridos
        $camposRequeridos = ['nombre', 'direccion', 'telefono', 'correo', 'password'];
        
        // Verificar campos requeridos
        foreach ($camposRequeridos as $campo) {
            if (!isset($datosTienda[$campo]) || empty($datosTienda[$campo])) {
                throw new Exception("El campo $campo es requerido");
            }
        }

        // Establecer valores predeterminados para campos opcionales
        $datosTienda['tienda_foto'] = $datosTienda['tienda_foto'] ?? '/vista/recursos/img/tiendas/default.jpg';
        $datosTienda['horario_apertura'] = $datosTienda['apertura'] ?? '09:00:00';
        $datosTienda['horario_cierre'] = $datosTienda['cierre'] ?? '18:00:00';
        $datosTienda['fecha_apertura'] = $datosTienda['fecha_apertura'] ?? date('Y-m-d');

        // Hashear la contraseña
        $datosTienda['password'] = password_hash($datosTienda['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO tienda (nombre, direccion, telefono, correo, password, tienda_foto, horario_apertura, horario_cierre, fecha_apertura, fecha_registro, activo) 
                VALUES (:nombre, :direccion, :telefono, :correo, :password, :tienda_foto, :horario_apertura, :horario_cierre, :fecha_apertura, NOW(), 1)";
        
        $stmt = $this->db->prepare($sql);
        
        $params = [
            ':nombre' => $datosTienda['nombre'],
            ':direccion' => $datosTienda['direccion'],
            ':telefono' => $datosTienda['telefono'],
            ':correo' => $datosTienda['correo'],
            ':password' => $datosTienda['password'],
            ':tienda_foto' => $datosTienda['tienda_foto'],
            ':horario_apertura' => $datosTienda['horario_apertura'],
            ':horario_cierre' => $datosTienda['horario_cierre'],
            ':fecha_apertura' => $datosTienda['fecha_apertura']
        ];
        
        try {
            $resultado = $stmt->execute($params);
            if ($resultado) {
                return $this->db->lastInsertId();
            } else {
                throw new Exception("Error al crear la tienda");
            }
        } catch (PDOException $e) {
            error_log("Error en crearTienda: " . $e->getMessage());
            throw new Exception("Error al crear la tienda: " . $e->getMessage());
        }
    }

    public function actualizarTienda($datosTienda) {
        $sql = "UPDATE tienda SET 
                nombre = :nombre, 
                direccion = :direccion, 
                telefono = :telefono, 
                tienda_foto = :tienda_foto, 
                horario_apertura = :horario_apertura, 
                horario_cierre = :horario_cierre 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($datosTienda);
    }

    public function eliminarTienda($id) {
        $sql = "DELETE FROM tienda WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
