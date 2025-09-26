<?php
require_once './conexion.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = BaseDatos::crearInstancia();
    }

    public function agregarProducto($nombre, $descripcion, $precio, $cantidad, $imagen, $id_tienda, $id_categoria) {
        try {
            $sql = "INSERT INTO productos (nombre, descripcion, precio, cantidad, imagen, id_tienda, id_categoria) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$nombre, $descripcion, $precio, $cantidad, $imagen, $id_tienda, $id_categoria]);
            if (!$result) {
                error_log("Error al insertar producto: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Excepción al insertar producto: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerCategorias() {
        $sql = "SELECT * FROM categorias ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Añade este nuevo método
    public function obtenerProductos($id_tienda) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                WHERE p.id_tienda = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tienda]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nuevo método para obtener el ID de la tienda por correo
    public function obtenerIdTiendaPorCorreo($correo) {
        $sql = "SELECT id FROM tienda WHERE correo = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$correo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['id'] : null;
    }

    // Nuevo método para obtener un producto por ID
    public function obtenerProductoPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Nuevo método para actualizar un producto
    public function actualizarProducto($id, $nombre, $descripcion, $precio, $cantidad, $imagen, $id_categoria) {
        try {
            $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, cantidad = ?, imagen = ?, id_categoria = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$nombre, $descripcion, $precio, $cantidad, $imagen, $id_categoria, $id]);
            if (!$result) {
                error_log("Error al actualizar producto: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            return true;
        } catch (PDOException $e) {
            error_log("Excepción al actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    // Nuevo método para eliminar un producto
    public function eliminarProducto($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Nuevo método para obtener los productos más vendidos
    public function obtenerProductosMasVendidos($idTienda, $limite) {
        $sql = "SELECT p.id, p.nombre, SUM(dp.cantidad) as cantidad_vendida 
                FROM productos p 
                JOIN detalle_pedido dp ON p.id = dp.id_producto 
                JOIN pedido pe ON dp.id_pedido = pe.id
                WHERE p.id_tienda = :idTienda 
                GROUP BY p.id 
                ORDER BY cantidad_vendida DESC 
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);   
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nuevo método para obtener productos por tienda
    public function obtenerProductosPorTienda($idTienda) {
        $sql = "SELECT * FROM productos WHERE id_tienda = :idTienda";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nuevo método para obtener productos recientes
    public function obtenerProductosRecientes($limite = 6) {
        $sql = "SELECT p.*, t.nombre as nombre_tienda 
                FROM productos p 
                JOIN tienda t ON p.id_tienda = t.id 
                ORDER BY p.fecha_registro DESC 
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosFiltrados($busqueda = '', $categoria = '') {
        $sql = "SELECT p.*, t.nombre as nombre_tienda, c.nombre as nombre_categoria 
                FROM productos p 
                JOIN tienda t ON p.id_tienda = t.id 
                LEFT JOIN categorias c ON p.id_categoria = c.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda)";
            $params[':busqueda'] = "%$busqueda%";
        }
        
        if (!empty($categoria)) {
            $sql .= " AND p.id_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }
        
        $sql .= " ORDER BY p.fecha_registro DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerIdTiendaPorProducto($idProducto) {
        $sql = "SELECT id_tienda FROM productos WHERE id = :idProducto";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function buscarProductos($busqueda = '', $categoria = '') {
        $sql = "SELECT p.*, t.nombre as nombre_tienda, c.nombre as nombre_categoria 
                FROM productos p 
                JOIN tienda t ON p.id_tienda = t.id 
                LEFT JOIN categorias c ON p.id_categoria = c.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($busqueda)) {
            $sql .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda)";
            $params[':busqueda'] = "%$busqueda%";
        }
        
        if (!empty($categoria)) {
            $sql .= " AND p.id_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }
        
        $sql .= " ORDER BY p.fecha_registro DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
