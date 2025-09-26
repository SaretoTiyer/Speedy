<?php
require_once './conexion.php';
class Calificacion {
    private $db;

    public function __construct() {
        $this->db = baseDatos::crearInstancia();
    }

    public function agregarCalificacion($idPedido, $idUsuario, $idTienda, $idDomiciliario, $puntuacionTienda, $puntuacionDomiciliario, $comentario) {
        $sql = "INSERT INTO calificacion (id_pedido, id_usuario, id_tienda, id_domiciliario, puntuacion_tienda, puntuacion_domiciliario, comentario) 
                VALUES (:idPedido, :idUsuario, :idTienda, :idDomiciliario, :puntuacionTienda, :puntuacionDomiciliario, :comentario)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
        $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':idTienda', $idTienda, PDO::PARAM_INT);
        $stmt->bindParam(':idDomiciliario', $idDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':puntuacionTienda', $puntuacionTienda, PDO::PARAM_INT);
        $stmt->bindParam(':puntuacionDomiciliario', $puntuacionDomiciliario, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function pedidoYaCalificado($idPedido) {
        $sql = "SELECT COUNT(*) FROM calificacion WHERE id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
?>
