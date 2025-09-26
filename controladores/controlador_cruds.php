<?php
    include_once("./conexion.php");
    include_once("./modelos/usuarios.php");
    include_once("./modelos/tienda.php");
    include_once("./modelos/domiciliario.php");

    class ControladorCruds{
        private $modeloTienda;
        private $modeloDomiciliario;

        public function __construct() {
            $this->modeloTienda = new Tienda();
            $this->modeloDomiciliario = new Domiciliario();
        }

        public function mostrar_usuarios (){
            $datosUsuarios = Usuario::ConsultarUsuario();
            include_once "vista/Crud_usuarios/mostrar.php";
        }

        // Métodos para gestionar tiendas
        public function mostrar_tiendas() {
            $tiendas = $this->modeloTienda->obtenerTodasLasTiendas();
            include_once "vista/Crud_tiendas/mostrar.php";
        }

        public function crear_tienda() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datosTienda = $_POST;

                // Manejar la carga de la imagen
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $resultadoSubida = $this->manejarSubidaFoto($_FILES['foto']);
                    if ($resultadoSubida['exito']) {
                        $datosTienda['tienda_foto'] = $resultadoSubida['ruta'];
                    } else {
                        $error = $resultadoSubida['mensaje'];
                        include_once "vista/Crud_tiendas/crear.php";
                        return;
                    }
                } else {
                    // Si no se subió una imagen, usar una por defecto
                    $datosTienda['tienda_foto'] = 'vista/recursos/img/tiendas/default.jpg';
                }

                try {
                    $resultadoTienda = $this->modeloTienda->crearTienda($datosTienda);
                    if ($resultadoTienda) {
                        // Crear usuario correspondiente
                        $resultadoUsuario = Usuario::CrearUsuario(
                            $datosTienda['nombre'],
                            $datosTienda['correo'],
                            $datosTienda['password'],
                            $datosTienda['direccion'],
                            $datosTienda['telefono'],
                            'tienda'
                        );

                        if (!$resultadoUsuario) {
                            // Manejar el error si no se pudo crear el usuario
                            $error = "Se creó la tienda pero no se pudo crear el usuario correspondiente.";
                            // Considerar revertir la creación de la tienda
                        }

                        header("Location: " . C_URL . "mostrar_tiendas");
                    } else {
                        $error = "Error al crear la tienda";
                        include_once "vista/Crud_tiendas/crear.php";
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    include_once "vista/Crud_tiendas/crear.php";
                }
            } else {
                include_once "vista/Crud_tiendas/crear.php";
            }
        }

        public function editar_tienda() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $datosTienda = [
                    'id' => $_POST['id'],
                    'nombre' => $_POST['nombre'],
                    'direccion' => $_POST['direccion'],
                    'telefono' => $_POST['telefono'],
                    'correo' => $_POST['correo'],
                    'tienda_foto' => $_POST['tienda_foto'] ?? null
                ];
                $resultado = $this->modeloTienda->actualizarTienda($datosTienda);
                if ($resultado) {
                    header("Location: " . C_URL . "mostrar_tiendas");
                } else {
                    $error = "Error al editar la tienda";
                    $tienda = $this->modeloTienda->obtenerTiendaPorId($_POST['id']);
                    include_once "vista/Crud_tiendas/editar.php";
                }
            } else {
                $id = $_GET['id'];
                $tienda = $this->modeloTienda->obtenerTiendaPorId($id);
                include_once "vista/Crud_tiendas/editar.php";
            }
        }

        public function eliminar_tienda() {
            $id = $_GET['id'];
            $resultado = $this->modeloTienda->eliminarTienda($id);
            header("Location: " . C_URL . "mostrar_tiendas");
        }

        // Métodos para gestionar domiciliarios
        public function mostrar_domiciliarios() {
            $domiciliarios = $this->modeloDomiciliario->obtenerTodosDomiciliarios();
            include_once "vista/Crud_domiciliarios/mostrar.php";
        }

        public function crear_domiciliario() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $datosDomiciliario = $_POST;

                // Manejar la carga de la imagen
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $resultadoSubida = $this->manejarSubidaFotoDomiciliario($_FILES['foto']);
                    if ($resultadoSubida['exito']) {
                        $datosDomiciliario['dom_foto'] = $resultadoSubida['ruta'];
                    } else {
                        $error = $resultadoSubida['mensaje'];
                        include_once "vista/Crud_domiciliarios/crear.php";
                        return;
                    }
                } else {
                    // Si no se subió una imagen, usar una por defecto
                    $datosDomiciliario['dom_foto'] = 'vista/recursos/img/domiciliarios/default.jpg';
                }

                try {
                    $resultadoDomiciliario = $this->modeloDomiciliario->registrarDomiciliario($datosDomiciliario);
                    if ($resultadoDomiciliario) {
                        // Crear usuario correspondiente
                        $resultadoUsuario = Usuario::CrearUsuario(
                            $datosDomiciliario['nombre'],
                            $datosDomiciliario['correo'],
                            $datosDomiciliario['password'],
                            $datosDomiciliario['direccion'],
                            $datosDomiciliario['telefono'],
                            'domiciliario'
                        );

                        if (!$resultadoUsuario) {
                            // Manejar el error si no se pudo crear el usuario
                            $error = "Se creó el domiciliario pero no se pudo crear el usuario correspondiente.";
                            // Considerar revertir la creación del domiciliario
                        }

                        header("Location: " . C_URL . "mostrar_domiciliarios");
                    } else {
                        $error = "Error al crear el domiciliario";
                        include_once "vista/Crud_domiciliarios/crear.php";
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                    include_once "vista/Crud_domiciliarios/crear.php";
                }
            } else {
                include_once "vista/Crud_domiciliarios/crear.php";
            }
        }

        public function editar_domiciliario() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $datosDomiciliario = [
                    'id' => $_POST['id'],
                    'nombre' => $_POST['nombre'],
                    'apellido' => $_POST['apellido'],
                    'telefono' => $_POST['telefono'],
                    'direccion' => $_POST['direccion'],
                    'vehiculo' => $_POST['vehiculo']
                ];

                // Manejar la carga de la nueva imagen
                if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] == 0) {
                    $resultadoSubida = $this->manejarSubidaFotoDomiciliario($_FILES['nueva_foto']);
                    if ($resultadoSubida['exito']) {
                        $datosDomiciliario['dom_foto'] = $resultadoSubida['ruta'];
                    } else {
                        $error = $resultadoSubida['mensaje'];
                        $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($_POST['id']);
                        include_once "vista/Crud_domiciliarios/editar.php";
                        return;
                    }
                } else {
                    // Si no se subió una nueva imagen, mantener la foto actual
                    $datosDomiciliario['dom_foto'] = $_POST['foto_actual'];
                }

                $resultado = $this->modeloDomiciliario->actualizarPerfilDomiciliario($datosDomiciliario);
                if ($resultado) {
                    $_SESSION['mensaje'] = "Domiciliario actualizado con éxito.";
                    header("Location: " . C_URL . "mostrar_domiciliarios");
                    exit();
                } else {
                    $error = "Error al editar el domiciliario";
                    $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($_POST['id']);
                    include_once "vista/Crud_domiciliarios/editar.php";
                }
            } else {
                $id = $_GET['id'] ?? null;
                if ($id === null) {
                    // Manejar el caso en que no se proporciona un ID
                    $_SESSION['error'] = "No se proporcionó un ID de domiciliario válido.";
                    header("Location: " . C_URL . "mostrar_domiciliarios");
                    exit();
                }
                $domiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($id);
                if ($domiciliario === false) {
                    // Manejar el caso en que no se encuentra el domiciliario
                    $_SESSION['error'] = "No se encontró el domiciliario especificado.";
                    header("Location: " . C_URL . "mostrar_domiciliarios");
                    exit();
                }
                include_once "vista/Crud_domiciliarios/editar.php";
            }
        }

        public function eliminar_domiciliario() {
            $id = $_GET['id'];
            $resultado = $this->modeloDomiciliario->eliminarDomiciliario($id);
            header("Location: " . C_URL . "mostrar_domiciliarios");
        }

        private function manejarSubidaFoto($archivo) {
            $directorioDestino = "vista/recursos/img/tiendas/";
            $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
            $rutaCompleta = $directorioDestino . $nombreArchivo;

            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return ['exito' => true, 'ruta' => $rutaCompleta];
            } else {
                return ['exito' => false, 'mensaje' => 'No se pudo subir la imagen.'];
            }
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
        }
    }

?>
