<?php
//modelos/usuarios.php
class Usuario{
    private $db;
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $direccion;
    private $telefono;
    private $id_rol;

    public function __construct($id = null, $nombre = null, $email = null, $password = null, $direccion = null, $telefono = null, $id_rol = null) {
        $this->db = baseDatos::crearInstancia();
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->id_rol = $id_rol;
    }

    public static function crearInstancia() {
        if (!self::$db) {
            self::$db = baseDatos::crearInstancia();
        }
        return self::$db;
    }

    public static function CrearUsuario($nombre, $correo, $password, $direccion, $telefono, $rolNombre){
        $conexionBaseDatos = baseDatos::crearInstancia();

        error_log("Creando usuario con los siguientes datos:");
        error_log("Nombre: $nombre, Correo: $correo, Dirección: $direccion, Teléfono: $telefono, Rol: $rolNombre");

        // Obtener el ID del rol
        $consultaRol = $conexionBaseDatos->prepare("SELECT id FROM rol WHERE nombre = ?");
        $consultaRol->execute(array($rolNombre));
        $rol = $consultaRol->fetch(PDO::FETCH_ASSOC);

        if (!$rol) {
            error_log("Error: No se encontró el rol '$rolNombre'");
            return false;
        }

        $idRol = $rol['id'];
        error_log("ID del rol obtenido: $idRol");

        // Verificar si la contraseña ya está hasheada
        if (!preg_match('/^\$2y\$/', $password)) {
            // Si no está hasheada, hashearla
            $password = password_hash($password, PASSWORD_DEFAULT);
        }

        // Inserción del usuario
        $sql = $conexionBaseDatos->prepare("INSERT INTO usuarios (nombre, email, password, direccion, telefono, id_rol)
                                                    VALUES (?,?,?,?,?,?)");
        $resultado = $sql->execute(array($nombre, $correo, $password, $direccion, $telefono, $idRol));

        if (!$resultado) {
            error_log("Error al insertar usuario: " . print_r($sql->errorInfo(), true));
        }

        return $resultado;
    }

    // leer o consultar los usuarios
    public static function ConsultarUsuario(){
        $listaUsuarios = [];
        $dbconexion = baseDatos::crearInstancia();

        $consulta = $dbconexion->prepare("
            SELECT usuarios.id, usuarios.nombre, usuarios.email, usuarios.direccion, usuarios.telefono, rol.nombre AS rolNombre 
            FROM usuarios 
            INNER JOIN rol ON usuarios.id_rol = rol.id
        ");
        $consulta->execute();

        foreach ($consulta->fetchAll(PDO::FETCH_ASSOC) as $usuario) {
            $listaUsuarios[] = new Usuario(
                $usuario['id'],
                $usuario['nombre'],
                $usuario['email'],
                null, // password no se incluye por seguridad
                $usuario['direccion'],
                $usuario['telefono'],
                $usuario['rolNombre']
            );
        }

        return $listaUsuarios;
    }

    public static function BorrarUsuario($idUsuario){
        $dbconexion = baseDatos::crearInstancia();
        $consulta = $dbconexion->prepare("DELETE FROM usuarios WHERE id = ?");
        $consulta->execute(array($idUsuario));
    }

    public static function editarUsuario($id, $nombre, $correo, $direccion, $telefono, $rolNombre) {
        $db = baseDatos::crearInstancia();
        $idRol = self::obtenerIdRol($rolNombre);
        
        $query = "UPDATE usuarios SET nombre = ?, email = ?, direccion = ?, telefono = ?, id_rol = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        try {
            $result = $stmt->execute([$nombre, $correo, $direccion, $telefono, $idRol, $id]);
            if (!$result) {
                error_log("Error al actualizar usuario: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Excepción al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    private static function obtenerIdRol($rolNombre) {
        $db = baseDatos::crearInstancia();
        $query = "SELECT id FROM rol WHERE nombre = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$rolNombre]);
        $result = $stmt->fetchColumn();
        
        if ($result === false) {
            // Si no se encuentra el rol, devolver un valor por defecto (por ejemplo, el rol de usuario)
            return 2; // Asumiendo que 2 es el ID para el rol de usuario normal
        }
        
        return $result;
    }

    public static function BuscarUsuario($idUsuario){
        $dbconexion = baseDatos::crearInstancia();

        $consulta = $dbconexion->prepare("
        SELECT usuarios.id, usuarios.nombre, usuarios.email, usuarios.direccion, usuarios.telefono, rol.id AS id_rol, rol.nombre AS rolNombre
        FROM usuarios
        INNER JOIN rol ON usuarios.id_rol = rol.id
        WHERE usuarios.id = ?
        ");
        $consulta->execute(array($idUsuario));
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            return new Usuario(
                $usuario['id'],
                $usuario['nombre'],
                $usuario['email'],
                null, // password no se incluye por seguridad
                $usuario['direccion'],
                $usuario['telefono'],
                $usuario['id_rol']
            );
        }
        return null;
    }

    public static function BuscarPorCorreo($correo) {
        $db = baseDatos::crearInstancia();
        
        error_log("Buscando usuario con correo: $correo");
        
        // Buscar en la tabla de usuarios
        $sql = "SELECT id, nombre, email as correo, password, direccion, telefono, id_rol 
            FROM usuarios WHERE email = :correo";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            error_log("Usuario encontrado en tabla usuarios: " . print_r($usuario, true));
            return $usuario;
        }
        
        // Buscar en la tabla de tienda
        $sql = "SELECT id, nombre, correo, password, direccion, telefono, 3 as id_rol 
            FROM tienda WHERE correo = :correo";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        $tienda = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tienda) {
            error_log("Usuario encontrado en tabla tienda: " . print_r($tienda, true));
            return $tienda;
        }
        
        // Buscar en la tabla de domiciliario
        $sql = "SELECT id, nombre, correo, password, direccion, telefono, 4 as id_rol 
            FROM domiciliario WHERE correo = :correo";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        $domiciliario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($domiciliario) {
            error_log("Usuario encontrado en tabla domiciliario: " . print_r($domiciliario, true));
            return $domiciliario;
        }
        
        error_log("Usuario no encontrado en ninguna tabla: $correo");
        return null;
    }

    public static function obtenerCuentasUsuario($correo) {
        $db = self::crearInstancia();
        $cuentas = [];

        // Buscar en la tabla de usuarios
        $sql = "SELECT id, nombre, email as correo, id_rol FROM usuarios WHERE email = :correo";
        $stmt = $db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $cuentas = array_merge($cuentas, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Buscar en la tabla de tienda
        $sql = "SELECT id, nombre, correo, 3 as id_rol FROM tienda WHERE correo = :correo";
        $stmt = $db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $cuentas = array_merge($cuentas, $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Buscar en la tabla de domiciliario
        $sql = "SELECT id, nombre, correo, 4 as id_rol FROM domiciliario WHERE correo = :correo";
        $stmt = $db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        $cuentas = array_merge($cuentas, $stmt->fetchAll(PDO::FETCH_ASSOC));

        return $cuentas;
    }

    public static function obtenerCuentaPorId($id) {
        $db = self::crearInstancia();

        // Buscar en la tabla de usuarios
        $sql = "SELECT id, nombre, email as correo, id_rol FROM usuarios WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cuenta) return $cuenta;

        // Buscar en la tabla de tienda
        $sql = "SELECT id, nombre, correo, 3 as id_rol FROM tienda WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cuenta) return $cuenta;

        // Buscar en la tabla de domiciliario
        $sql = "SELECT id, nombre, correo, 4 as id_rol FROM domiciliario WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        return $cuenta;
    }

    public function obtenerIdPorCorreo($correo) {
        $sql = "SELECT id FROM usuarios WHERE email = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function getLastError() {
        $conexionBaseDatos = baseDatos::crearInstancia();
        return $conexionBaseDatos->errorInfo();
    }

    // Agregar métodos getter para acceder a las propiedades privadas
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getIdRol() {
        return $this->id_rol;
    }
}

?>
