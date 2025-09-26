<?php
require_once 'modelos/Producto.php';
require_once 'modelos/Tienda.php';

class ControladorProductos {
    private $modeloProducto;
    private $modeloTienda;

    public function __construct() {
        $this->modeloProducto = new Producto();
        $this->modeloTienda = new Tienda();
    }

    public function agregarProducto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Evitar procesamiento duplicado
            if (isset($_SESSION['last_product_submission']) && $_SESSION['last_product_submission'] == $_POST) {
                error_log("Detección de sumisión duplicada. Ignorando.");
                header("Location: " . S_URL_P . "listar");
                return;
            }
            $_SESSION['last_product_submission'] = $_POST;

            error_log("Iniciando proceso de agregar producto");
            error_log("Datos recibidos: " . print_r($_POST, true));
            error_log("Archivos recibidos: " . print_r($_FILES, true));

            // Verificar si se ha subido una imagen
            if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != UPLOAD_ERR_OK) {
                error_log("Error en la subida de la imagen: " . $_FILES['imagen']['error']);
                $error = "Hubo un problema con la imagen del producto.";
                include_once "vista/paginas/Productos/AgregarProducto.php";
                return;
            }

            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio = $_POST['precio'];
            $cantidad = $_POST['cantidad'];
            $id_categoria = $_POST['categoria'];

            // Manejar la subida de la imagen
            $imagen = $this->subirImagen($_FILES['imagen']);
            if (!$imagen) {
                error_log("Error al subir la imagen");
                $error = "No se pudo subir la imagen del producto.";
                include_once "vista/paginas/Productos/AgregarProducto.php";
                return;
            }

            // Obtener el ID de la tienda
            $id_tienda = $this->modeloProducto->obtenerIdTiendaPorCorreo($_SESSION['usuario']['correo']);
            if (!$id_tienda) {
                error_log("No se pudo obtener el ID de la tienda para el correo: " . $_SESSION['usuario']['correo']);
                $error = "Error al identificar la tienda.";
                include_once "vista/paginas/Productos/AgregarProducto.php";
                return;
            }

            error_log("Intentando agregar producto con los siguientes datos:");
            error_log("Nombre: $nombre, Descripcion: $descripcion, Precio: $precio, Cantidad: $cantidad, Imagen: $imagen, ID Tienda: $id_tienda, ID Categoria: $id_categoria");

            $resultado = $this->modeloProducto->agregarProducto($nombre, $descripcion, $precio, $cantidad, $imagen, $id_tienda, $id_categoria);

            if ($resultado === false) {
                error_log("Error al agregar el producto en la base de datos");
                $error = "No se pudo agregar el producto. Por favor, inténtelo de nuevo.";
                include_once "vista/paginas/Productos/AgregarProducto.php";
            } else {
                error_log("Producto agregado exitosamente");
                header("Location: " . S_URL_P . "listar");
                return;
            }
        } else {
            $categorias = $this->modeloProducto->obtenerCategorias();
            include_once "vista/paginas/Productos/AgregarProducto.php";
        }
    }

    private function subirImagen($archivo) {
        $directorioDestino = 'vista/recursos/img/productos/';
        $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            error_log("Imagen subida exitosamente a: $rutaCompleta");
            return $rutaCompleta;
        } else {
            error_log("Error al mover el archivo subido");
            return false;
        }
    }

    public function listar() {
        // Verificar si el usuario ha iniciado sesión y es una tienda
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            // Redirigir si no es una tienda
            header('Location: ' . S_URL . 'login');
            exit;
        }

        // Obtener el ID de la tienda basado en el ID del usuario
        $id_usuario = $_SESSION['usuario']['id'];
        $tienda = $this->modeloTienda->obtenerTiendaPorUsuario($id_usuario);

        if (!$tienda) {
            error_log("No se encontró la tienda para el usuario con ID: $id_usuario");
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $id_tienda = $tienda['id'];
        $productos = $this->modeloProducto->obtenerProductos($id_tienda);
        
        // Depuración
        error_log("ID de la tienda: " . $id_tienda);
        error_log("Productos obtenidos: " . print_r($productos, true));

        require_once 'vista/paginas/Productos/ListarProductos.php';
    }

    public function editarProducto() {
        if (!isset($_GET['id'])) {
            header("Location: " . S_URL_P . "listar");
            return;
        }

        $id_producto = $_GET['id'];
        $producto = $this->modeloProducto->obtenerProductoPorId($id_producto);

        if (!$producto) {
            // Producto no encontrado, redirigir a la lista de productos
            header("Location: " . S_URL_P . "listar");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $precio = $_POST['precio'];
            $cantidad = $_POST['cantidad'];
            $id_categoria = $_POST['categoria'];

            // Manejar la actualización de la imagen si se proporciona una nueva
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen = $this->subirImagen($_FILES['imagen']);
                if (!$imagen) {
                    $error = "No se pudo subir la nueva imagen del producto.";
                    $categorias = $this->modeloProducto->obtenerCategorias();
                    include_once "vista/paginas/Productos/EditarProducto.php";
                    return;
                }
            } else {
                // Si no se proporciona una nueva imagen, mantener la existente
                $imagen = $producto['imagen'];
            }

            $resultado = $this->modeloProducto->actualizarProducto($id_producto, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_categoria);

            if ($resultado) {
                header("Location: " . S_URL_P . "listar");
                return;
            } else {
                $error = "No se pudo actualizar el producto. Por favor, inténtelo de nuevo.";
            }
        }

        $categorias = $this->modeloProducto->obtenerCategorias();
        include_once "vista/paginas/Productos/EditarProducto.php";
    }

    public function eliminar() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $id_producto = isset($_GET['id']) ? $_GET['id'] : null;
        if (!$id_producto) {
            header('Location: ' . S_URL_P . 'listar');
            exit;
        }

        $tienda = $this->modeloTienda->obtenerTiendaPorUsuario($_SESSION['usuario']['id']);
        if (!$tienda) {
            header('Location: ' . S_URL . 'error');
            exit;
        }

        $producto = $this->modeloProducto->obtenerProductoPorId($id_producto);
        if (!$producto || $producto['id_tienda'] != $tienda['id']) {
            header('Location: ' . S_URL_P . 'listar');
            exit;
        }

        $resultado = $this->modeloProducto->eliminarProducto($id_producto);

        if ($resultado) {
            header('Location: ' . S_URL_P . 'listar&mensaje=Producto eliminado con éxito');
        } else {
            header('Location: ' . S_URL_P . 'listar&error=Error al eliminar el producto');
        }
        exit;
    }

    public function verProducto() {
        if (!isset($_GET['id'])) {
            header('Location: ' . S_URL . 'inicio');
            exit;
        }

        $id_producto = $_GET['id'];
        $producto = $this->modeloProducto->obtenerProductoPorId($id_producto);

        if (!$producto) {
            header('Location: ' . S_URL . 'inicio');
            exit;
        }

        require_once 'vista/paginas/Productos/VerProducto.php';
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

            // Verificar si el producto ya está en el carrito
            $productoEnCarrito = false;
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['id'] == $producto['id']) {
                    $item['cantidad'] += $cantidad;
                    $productoEnCarrito = true;
                    break;
                }
            }

            if (!$productoEnCarrito) {
                $item = [
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad
                ];
                $_SESSION['carrito'][] = $item;
            }

            header('Location: ' . S_URL_PED . 'verCarrito');
            exit;
        }
    }

    public function catalogo() {
        $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
        $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
        
        $productos = $this->modeloProducto->buscarProductos($busqueda, $categoria);
        $categorias = $this->modeloProducto->obtenerCategorias();
        
        $datos = [
            'productos' => $productos,
            'categorias' => $categorias,
            'busqueda' => $busqueda,
            'categoria_seleccionada' => $categoria
        ];
        
        require_once 'vista/paginas/Productos/catalogo.php';
    }

    // Otros métodos según sea necesario...
}
