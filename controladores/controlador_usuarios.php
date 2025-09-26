<?php 

    include_once "conexion.php";
    include_once "./modelos/usuarios.php";
    include_once "./modelos/Tienda.php"; 
    include_once "./modelos/Pedidos.php";
    class ControladorUsuarios {
        private $modeloTienda; 
        private $modeloPedidos; 
        public function __construct() {
            $this->modeloTienda = new Tienda();
            $this->modeloPedidos = new Pedidos(); // Inicializa el modelo Pedidos
        }

        public function crear() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['nombre'];
                $correo = $_POST['email'];  
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Cambiado a PASSWORD_DEFAULT
                $direccion = $_POST['direccion'];
                $telefono = $_POST['telefono'];
                $rol = "usuario";

                // Aquí no es necesario crear un objeto Usuario, solo llamamos a CrearUsuario
                Usuario::CrearUsuario($nombre, $correo, $password, $direccion, $telefono, $rol);
                
                header("Location: index.php?controlador=paginas&accion=login");
                exit();
            }
        }
        public function ingreso() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);

                error_log("Intento de inicio de sesión - Correo: $email, Contraseña: $password");

                $usuario = Usuario::BuscarPorCorreo($email);

                if ($usuario) {
                    error_log("Usuario encontrado - ID: {$usuario['id']}, Rol: {$usuario['id_rol']}");
                    error_log("Hash almacenado: " . $usuario['password']);
                    error_log("Longitud de la contraseña ingresada: " . strlen($password));
                    error_log("Primeros 3 caracteres de la contraseña ingresada: " . substr($password, 0, 3) . "***");

                    if (password_verify($password, $usuario['password'])) {
                        // Contraseña correcta, iniciar sesión
                        $_SESSION['usuario'] = $usuario;
                        error_log("Inicio de sesión exitoso para: $email");
                        
                        // Redirigir según el rol del usuario
                        switch ($usuario['id_rol']) {
                            case 1: // Administrador
                            case 2: // Usuario normal
                                header("Location: index.php?controlador=paginas&accion=inicio");
                                break;
                            case 3: // Tienda
                                header("Location: index.php?controlador=tienda&accion=panel");
                                break;
                            case 4: // Domiciliario
                                header("Location: index.php?controlador=domiciliario&accion=panel");
                                break;
                            default:
                                header("Location: index.php");
                        }
                        exit();
                    } else {
                        error_log("Contraseña incorrecta para: $email");
                        error_log("Resultado de password_verify: " . (password_verify($password, $usuario['password']) ? 'true' : 'false'));
                        $this->mostrarError('Contraseña incorrecta');
                    }
                } else {
                    error_log("Usuario no encontrado: $email");
                    $this->mostrarError('Usuario no encontrado');
                }
            }
            // Cargar la vista de login si no es una solicitud POST
            require_once './vista/usuarios/login.php';
        }

        private function mostrarError($mensaje) {
            echo "<script>
                    Swal.fire({
                        title: 'Error',
                        text: '$mensaje',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                  </script>";
        }

        public function insertar() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['name']; // Nombre del campo en el formulario
                $correo = $_POST['correo'];
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Cambiado a PASSWORD_DEFAULT
                $direccion = $_POST['direccion'];
                $telefono = $_POST['telefono'];
                $rol = $_POST['rol']; // Cambiado para usar el rol seleccionado

                // Crear un nuevo usuario
                Usuario::CrearUsuario($nombre, $correo, $password, $direccion, $telefono, $rol);

                // Redirigir a la vista de listado de usuarios
                header("Location: index.php?controlador=cruds&accion=mostrar_usuarios");
                exit();
            }
        }

        public function borrarU() {
            if($_GET['id']){
                $idUsuario = $_GET['id'];
                Usuario::BorrarUsuario($idUsuario);
                header("Location: index.php?controlador=cruds&accion=mostrar_usuarios");
                exit();
            }
        }

        public function actualizarU(){
            $idUsuario=$_GET['id'];
            $datosUsuario=Usuario::BuscarUsuario($idUsuario);
            //print_r($datosUsuario); 
            include_once "./vista/Crud_usuarios/actualizar.php";
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nombre = $_POST['name'];
                $correo = $_POST['correo'];
                $direccion = $_POST['direccion'];
                $telefono = $_POST['telefono'];
                $rol = $_POST['rol']; 

                Usuario::editarUsuario($idUsuario,$nombre, $correo, $direccion, $telefono, $rol);

                // Redirigir a la vista de listado de usuarios
                header("Location: index.php?controlador=cruds&accion=mostrar_usuarios");
                exit();
            }
        }
    }
?>