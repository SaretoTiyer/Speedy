<?php
require_once './conexion.php';

class Pedidos {
    private $db;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
    }

    public function obtenerUltimosPedidosPorTienda($tiendaId, $limite) {
        $sql = "SELECT DISTINCT p.* FROM pedido p
                INNER JOIN detalle_pedido dp ON p.id = dp.id_pedido
                INNER JOIN productos pr ON dp.id_producto = pr.id
                WHERE pr.id_tienda = :tiendaId 
                ORDER BY p.fecha_hora_pedido DESC LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':tiendaId', $tiendaId, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPedidosPorTienda($idTienda) {
        $sql = "SELECT COUNT(*) as total FROM pedido p 
            JOIN detalle_pedido dp ON p.id = dp.id_pedido 
            JOIN productos pr ON dp.id_producto = pr.id 
            WHERE pr.id_tienda = :idTienda";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function calcularIngresosTotales($idTienda) {
        $sql = "SELECT SUM(dp.cantidad * dp.precio_unitario) as total 
            FROM pedido p 
            JOIN detalle_pedido dp ON p.id = dp.id_pedido 
            JOIN productos pr ON dp.id_producto = pr.id 
            WHERE pr.id_tienda = :idTienda";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    public function obtenerPedidosRecientes($idTienda, $limite) {
        $sql = "SELECT p.id, p.fecha_hora_pedido, p.estado, SUM(dp.cantidad * dp.precio_unitario) as valor_total 
            FROM pedido p 
            JOIN detalle_pedido dp ON p.id = dp.id_pedido 
            JOIN productos pr ON dp.id_producto = pr.id 
            WHERE pr.id_tienda = :idTienda 
            GROUP BY p.id 
            ORDER BY p.fecha_hora_pedido DESC 
            LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerVentasPorMes($idTienda) {
        $sql = "SELECT DATE_FORMAT(p.fecha_hora_pedido, '%Y-%m') as mes, 
            SUM(dp.cantidad * dp.precio_unitario) as total 
            FROM pedido p 
            JOIN detalle_pedido dp ON p.id = dp.id_pedido 
            JOIN productos pr ON dp.id_producto = pr.id 
            WHERE pr.id_tienda = :idTienda 
            GROUP BY DATE_FORMAT(p.fecha_hora_pedido, '%Y-%m') 
            ORDER BY mes DESC 
            LIMIT 12";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPedidosPorEstado($idTienda, $estado) {
        $sql = "SELECT COUNT(*) as total FROM pedido p 
                JOIN detalle_pedido dp ON p.id = dp.id_pedido 
                JOIN productos pr ON dp.id_producto = pr.id 
                WHERE pr.id_tienda = :idTienda AND p.estado = :estado";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function contarPedidosPorDomiciliario($idDomiciliario) {
        $sql = "SELECT COUNT(*) as total FROM pedido WHERE id_domiciliario = :idDomiciliario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function calcularIngresosTotalesDomiciliario($idDomiciliario) {
        $sql = "SELECT SUM(dp.cantidad * dp.precio_unitario) as total 
            FROM pedido p 
            JOIN detalle_pedido dp ON p.id = dp.id_pedido 
            WHERE p.id_domiciliario = :idDomiciliario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    public function contarPedidosPorEstadoDomiciliario($idDomiciliario, $estado) {
        $sql = "SELECT COUNT(*) as total FROM pedido WHERE id_domiciliario = :idDomiciliario AND estado = :estado";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }

    public function obtenerUltimosPedidosPorDomiciliario($idDomiciliario, $limite) {
        $sql = "SELECT * FROM pedido 
                WHERE id_domiciliario = :idDomiciliario 
                ORDER BY fecha_hora_pedido DESC 
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calcularTiempoPromedioEntrega($idDomiciliario) {
        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, fecha_hora_pedido, fecha_hora_entrega)) as promedio 
                FROM pedido 
                WHERE id_domiciliario = :idDomiciliario 
                AND estado = 'entregado' 
                AND fecha_hora_entrega IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? round($resultado['promedio']) : 0;
    }

    public function obtenerPedidosPorMes($idDomiciliario) {
        $sql = "SELECT DATE_FORMAT(fecha_hora_pedido, '%Y-%m') as mes, COUNT(*) as total 
                FROM pedido 
                WHERE id_domiciliario = :idDomiciliario 
                GROUP BY DATE_FORMAT(fecha_hora_pedido, '%Y-%m') 
                ORDER BY mes DESC 
                LIMIT 12";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidosRecientesDomiciliario($idDomiciliario, $limite) {
        $sql = "SELECT p.*, u.nombre as nombre_cliente 
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

    public function crearPedido($idUsuario, $carrito, $valorTotal, $direccionEntrega, $comentarios = null) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO pedido (id_usuario, valor_pedido, direccion_entrega, comentarios) VALUES (:id_usuario, :valor_pedido, :direccion_entrega, :comentarios)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(':valor_pedido', $valorTotal, PDO::PARAM_STR);
            $stmt->bindParam(':direccion_entrega', $direccionEntrega, PDO::PARAM_STR);
            $stmt->bindParam(':comentarios', $comentarios, PDO::PARAM_STR);
            $stmt->execute();

            $idPedido = $this->db->lastInsertId();

            foreach ($carrito as $item) {
                $query = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
                $stmt->bindParam(':id_producto', $item['id'], PDO::PARAM_INT);
                $stmt->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmt->bindParam(':precio_unitario', $item['precio'], PDO::PARAM_STR);
                $stmt->execute();
            }

            $this->db->commit();
            return $idPedido;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en crearPedido: " . $e->getMessage());
            return false;
        }
    }

    public function agregarDetallePedido($id_pedido, $id_producto, $cantidad, $precio_unitario) {
        $sql = "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario) 
                VALUES (:id_pedido, :id_producto, :cantidad, :precio_unitario)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
        $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindParam(':precio_unitario', $precio_unitario, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function obtenerPedidosUsuario($id_usuario) {
        $sql = "SELECT * FROM pedido WHERE id_usuario = :id_usuario ORDER BY fecha_hora_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidoPorId($id_pedido) {
        $sql = "SELECT p.*, u.nombre as nombre_usuario
                FROM pedido p
                JOIN usuarios u ON p.id_usuario = u.id
                WHERE p.id = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidosPorTienda($idTienda, $limite = null, $estado = null) {
        $sql = "SELECT DISTINCT p.* 
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE pr.id_tienda = :idTienda";
        
        if ($estado) {
            $sql .= " AND p.estado = :estado";
        }
        
        $sql .= " ORDER BY p.fecha_hora_pedido DESC";
        
        if ($limite) {
            $sql .= " LIMIT :limite";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        
        if ($estado) {
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        }
        
        if ($limite) {
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoPedido($idPedido, $nuevoEstado, $idDomiciliario = null) {
    try {
        $this->db->beginTransaction();

        $sql = "UPDATE pedido SET estado = :estado";
        $params = [':estado' => $nuevoEstado, ':id' => $idPedido];

        if ($idDomiciliario !== null) {
            $sql .= ", id_domiciliario = :id_domiciliario";
            $params[':id_domiciliario'] = $idDomiciliario;
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $result = $stmt->execute();

        if ($stmt->rowCount() == 0) {
            throw new Exception("No se pudo actualizar el pedido.");
        }

        $this->db->commit();
        return $result;
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Error en actualizarEstadoPedido: " . $e->getMessage());
        return false;
    }
}

    public function aceptarPedido($idPedido, $idDomiciliario = null) {
        return $this->actualizarEstadoPedido($idPedido, 'aceptado', $idDomiciliario);
    }

    public function prepararPedido($idPedido) {
        return $this->actualizarEstadoPedido($idPedido, 'en_preparacion');
    }

    public function finalizarPreparacion($idPedido) {
        return $this->actualizarEstadoPedido($idPedido, 'listo_para_entrega');
    }

    public function iniciarEntrega($idPedido) {
        return $this->actualizarEstadoPedido($idPedido, 'en_camino');
    }

    public function confirmarEntrega($idPedido) {
        return $this->actualizarEstadoPedido($idPedido, 'en_camino');
    }


    public function cancelarPedido($idPedido) {
        return $this->actualizarEstadoPedido($idPedido, 'cancelado');
    }

    public function obtenerPedidosPorDomiciliario($idDomiciliario) {
        $sql = "SELECT p.id, u.nombre AS cliente, p.fecha_hora_pedido AS fecha, p.estado
                FROM pedido p
                LEFT JOIN usuarios u ON p.id_usuario = u.id
                WHERE p.id_domiciliario = :idDomiciliario
                ORDER BY p.fecha_hora_pedido DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallePedido($idPedido) {
        $sql = "SELECT dp.*, p.nombre as nombre_producto 
                FROM detalle_pedido dp 
                JOIN productos p ON dp.id_producto = p.id 
                WHERE dp.id_pedido = :idPedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignarDomiciliario($idPedido, $idDomiciliario) {
        $sql = "UPDATE pedido SET id_domiciliario = :id_domiciliario WHERE id = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_domiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function perteneceALaTienda($idPedido, $idTienda) {
        $sql = "SELECT COUNT(*) FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE p.id = :idPedido AND pr.id_tienda = :idTienda";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerIdUsuarioPorPedido($idPedido) {
        $sql = "SELECT id_usuario FROM pedido WHERE id = :idPedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerPedidosPendientesPorTienda($idTienda) {
        try {
            $sql = "SELECT p.id, p.fecha_hora_pedido, p.estado, 
                           SUM(dp.cantidad * dp.precio_unitario) as valor_pedido
                    FROM pedido p
                    JOIN detalle_pedido dp ON p.id = dp.id_pedido
                    JOIN productos pr ON dp.id_producto = pr.id
                    WHERE pr.id_tienda = :idTienda AND p.estado = 'pendiente'
                    GROUP BY p.id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPedidosPendientesPorTienda: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPedidosAceptadosPorTienda($idTienda) {
        $sql = "SELECT DISTINCT p.id, p.fecha_hora_pedido, p.estado, u.nombre as nombre_cliente,
                (SELECT SUM(dp.cantidad * dp.precio_unitario) 
                 FROM detalle_pedido dp 
                 WHERE dp.id_pedido = p.id) as total
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                JOIN usuarios u ON p.id_usuario = u.id
                WHERE pr.id_tienda = :idTienda AND p.estado = 'aceptado'
                ORDER BY p.fecha_hora_pedido ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidosEnPreparacionPorTienda($idTienda) {
        $sql = "SELECT DISTINCT p.id, p.fecha_hora_pedido, p.estado, u.nombre as nombre_cliente,
                (SELECT SUM(dp.cantidad * dp.precio_unitario) 
                 FROM detalle_pedido dp 
                 WHERE dp.id_pedido = p.id) as total
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                JOIN usuarios u ON p.id_usuario = u.id
                WHERE pr.id_tienda = :idTienda AND p.estado = 'en_preparacion'
                ORDER BY p.fecha_hora_pedido ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignarDomiciliarioAPedido($idPedido, $idDomiciliario) {
        $sql = "UPDATE pedido SET id_domiciliario = :idDomiciliario WHERE id = :idPedido AND estado = 'listo_para_entrega'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerIdTiendaPorPedido($idPedido) {
        $sql = "SELECT DISTINCT pr.id_tienda
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE p.id = :idPedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerPedidosListosParaEntrega() {
        $sql = "SELECT p.id, p.fecha_hora_pedido as fecha, p.estado, u.nombre as cliente, p.direccion_entrega 
                FROM pedido p 
                JOIN usuarios u ON p.id_usuario = u.id 
                WHERE p.estado = 'listo_para_entrega'";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function confirmarEntregaPedido($idPedido) {
        $sql = "UPDATE pedido SET 
                estado = 'entregado', 
                fecha_hora_entrega = NOW() 
                WHERE id = :idPedido";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function perteneceAlDomiciliario($idPedido, $idDomiciliario) {
        $sql = "SELECT COUNT(*) FROM pedido WHERE id = :idPedido AND id_domiciliario = :idDomiciliario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerPedidosEnCaminoPorDomiciliario($idDomiciliario) {
    $sql = "SELECT p.id, p.fecha_hora_pedido as fecha, p.estado, u.nombre as cliente, p.direccion_entrega 
            FROM pedido p 
            JOIN usuarios u ON p.id_usuario = u.id 
            WHERE p.id_domiciliario = :idDomiciliario AND p.estado = 'en_camino'";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calificarPedido($idPedido, $calificacionTienda, $calificacionDomiciliario, $comentarios) {
        $sql = "UPDATE pedido SET 
                calificacion_tienda = :calificacionTienda, 
                calificacion_domiciliario = :calificacionDomiciliario, 
                comentarios_calificacion = :comentarios,
                fecha_calificacion = NOW()
                WHERE id = :idPedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':calificacionTienda', $calificacionTienda, PDO::PARAM_INT);
        $stmt->bindParam(':calificacionDomiciliario', $calificacionDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':comentarios', $comentarios, PDO::PARAM_STR);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function perteneceAlUsuario($idPedido, $idUsuario) {
        $sql = "SELECT COUNT(*) FROM pedido WHERE id = :idPedido AND id_usuario = :idUsuario";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function obtenerIdDomiciliarioPorPedido($idPedido) {
        $sql = "SELECT id_domiciliario FROM pedido WHERE id = :idPedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function obtenerPedidosEntregadosPorUsuario($idUsuario) {
        $sql = "SELECT p.id, p.fecha_hora_pedido, p.fecha_hora_entrega, t.nombre as nombre_tienda,
                (SELECT SUM(dp.cantidad * dp.precio_unitario) FROM detalle_pedido dp WHERE dp.id_pedido = p.id) as total
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                JOIN tienda t ON pr.id_tienda = t.id
                WHERE p.id_usuario = :idUsuario AND p.estado = 'entregado'
                GROUP BY p.id
                ORDER BY p.fecha_hora_entrega DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function rechazarPedido($idPedido, $motivo) {
        try {
            $this->db->beginTransaction();

            // Actualizar el estado del pedido a 'rechazado'
            $sql = "UPDATE pedidos SET estado = 'rechazado', motivo_rechazo = :motivo WHERE id = :id_pedido";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmt->bindParam(':motivo', $motivo, PDO::PARAM_STR);
            $stmt->execute();

            // Verificar si se actualizó correctamente
            if ($stmt->rowCount() == 0) {
                throw new Exception("No se pudo actualizar el pedido.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error en rechazarPedido: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPedidosPorTiendaYEstado($idTienda, $estado = 'todos') {
        try {
            $sql = "SELECT p.*, u.nombre as nombre_usuario 
                    FROM pedido p
                    JOIN detalle_pedido dp ON p.id = dp.id_pedido
                    JOIN productos pr ON dp.id_producto = pr.id
                    JOIN usuarios u ON p.id_usuario = u.id
                    WHERE pr.id_tienda = :idTienda";
            
            if ($estado !== 'todos') {
                $sql .= " AND p.estado = :estado";
            }
            
            $sql .= " GROUP BY p.id ORDER BY p.fecha_hora_pedido DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
            
            if ($estado !== 'todos') {
                $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerPedidosPorTiendaYEstado: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPedidosEnCursoPorUsuario($idUsuario) {
        $sql = "SELECT p.*, t.nombre as nombre_tienda 
                FROM pedido p 
                JOIN tienda t ON p.id_tienda = t.id 
                WHERE p.id_usuario = :idUsuario AND p.estado != 'entregado' AND p.estado != 'cancelado'
                ORDER BY p.fecha_hora_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosPedidosUsuario($idUsuario) {
        $sql = "SELECT DISTINCT p.*, t.nombre as nombre_tienda 
                FROM pedido p 
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                JOIN tienda t ON pr.id_tienda = t.id 
                WHERE p.id_usuario = :idUsuario 
                ORDER BY p.fecha_hora_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidosCompletosParaDomiciliario($correoDomiciliario) {
        $sql = "SELECT p.id, p.fecha_hora_pedido, p.estado, p.valor_pedido,
                u.nombre AS nombre_cliente,
                t.nombre AS nombre_tienda,
                p.direccion_entrega
            FROM pedido p
            JOIN usuarios u ON p.id_usuario = u.id
            JOIN domiciliario d ON p.id_domiciliario = d.id
            JOIN detalle_pedido dp ON p.id = dp.id_pedido
            JOIN productos pr ON dp.id_producto = pr.id
            JOIN tienda t ON pr.id_tienda = t.id
            WHERE d.correo = :correoDomiciliario AND p.estado IN ('en_camino', 'entregado')
            GROUP BY p.id, p.fecha_hora_pedido, p.estado, p.valor_pedido, u.nombre, t.nombre, p.direccion_entrega
            ORDER BY p.fecha_hora_pedido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correoDomiciliario', $correoDomiciliario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPedidosDisponiblesParaDomiciliario() {
        $sql = "SELECT p.id, p.fecha_hora_pedido, p.estado, p.valor_pedido,
                u.nombre AS nombre_cliente,
                t.nombre AS nombre_tienda,
                p.direccion_entrega
            FROM pedido p
            JOIN usuarios u ON p.id_usuario = u.id
            JOIN detalle_pedido dp ON p.id = dp.id_pedido
            JOIN productos pr ON dp.id_producto = pr.id
            JOIN tienda t ON pr.id_tienda = t.id
            WHERE p.id_domiciliario IS NULL AND p.estado = 'listo_para_entrega'
            GROUP BY p.id, p.fecha_hora_pedido, p.estado, p.valor_pedido, u.nombre, t.nombre, p.direccion_entrega
            ORDER BY p.fecha_hora_pedido ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pedidoPerteneceADomiciliario($idPedido, $idDomiciliario) {
        $sql = "SELECT COUNT(*) FROM pedido 
                WHERE id = :idPedido AND id_domiciliario = :idDomiciliario";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    public function eliminarPedido($idPedido) {
        try {
            $this->db->beginTransaction();

            // Primero, eliminar los detalles del pedido
            $stmtDetalles = $this->db->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $stmtDetalles->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmtDetalles->execute();

            // Luego, eliminar el pedido
            $stmtPedido = $this->db->prepare("DELETE FROM pedido WHERE id = :id_pedido");
            $stmtPedido->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
            $stmtPedido->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error al eliminar pedido: " . $e->getMessage());
            return false;
        }
    }

    public function contarPedidosHoy($idTienda) {
        $sql = "SELECT COUNT(DISTINCT p.id) as total 
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE pr.id_tienda = :idTienda 
                AND DATE(p.fecha_hora_pedido) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    public function calcularIngresosHoy($idTienda) {
        $sql = "SELECT SUM(dp.cantidad * dp.precio_unitario) as total 
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE pr.id_tienda = :idTienda 
                AND DATE(p.fecha_hora_pedido) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

    public function contarProductosVendidosHoy($idTienda) {
        $sql = "SELECT SUM(dp.cantidad) as total 
                FROM pedido p
                JOIN detalle_pedido dp ON p.id = dp.id_pedido
                JOIN productos pr ON dp.id_producto = pr.id
                WHERE pr.id_tienda = :idTienda 
                AND DATE(p.fecha_hora_pedido) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] ?? 0;
    }

}
?>
