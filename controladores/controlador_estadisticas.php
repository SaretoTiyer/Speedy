<?php
require_once './modelos/Tienda.php';
require_once './modelos/Pedidos.php';
require_once './modelos/Producto.php';
require_once './modelos/Domiciliario.php';
class ControladorEstadisticas {
    private $modeloTienda;
    private $modeloPedidos;
    private $modeloProductos;
    private $modeloDomiciliario;

    public function __construct() {
        $this->modeloTienda = new Tienda();
        $this->modeloPedidos = new Pedidos();
        $this->modeloProductos = new Producto();
        $this->modeloDomiciliario = new Domiciliario();
    }

    public function mostrar() {
        // Verificar si el usuario ha iniciado sesión y es una tienda
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 3) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idTienda = $this->modeloTienda->obtenerTiendaPorUsuario($_SESSION['usuario']['id']);

        // Verificar si $idTienda es un array y obtener el ID
        if (is_array($idTienda) && isset($idTienda['id'])) {
            $idTienda = $idTienda['id'];
        }

        // Obtener estadísticas
        $datos = [
            'totalPedidos' => $this->modeloPedidos->contarPedidosPorTienda($idTienda),
            'ingresosTotales' => $this->modeloPedidos->calcularIngresosTotales($idTienda),
            'productosMasVendidos' => $this->modeloProductos->obtenerProductosMasVendidos($idTienda, 5),
            'pedidosRecientes' => $this->modeloPedidos->obtenerPedidosRecientes($idTienda, 10),
            'ventasPorMes' => $this->modeloPedidos->obtenerVentasPorMes($idTienda)
        ];

        // Obtener estadísticas inmediatas
        $estadisticasInmediatas = $this->obtenerEstadisticasInmediatas($idTienda);

        // Cargar la vista
        require_once 'vista/Tienda/estadisticas.php';
    }

    private function obtenerEstadisticasInmediatas($idTienda) {
        return [
            'pedidosHoy' => $this->modeloPedidos->contarPedidosHoy($idTienda),
            'ingresosHoy' => $this->modeloPedidos->calcularIngresosHoy($idTienda),
            'productosVendidosHoy' => $this->modeloPedidos->contarProductosVendidosHoy($idTienda)
        ];
    }

    public function mostrarDomiciliario() {
        // Verificar si el usuario ha iniciado sesión y es un domiciliario
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['id_rol'] != 4) {
            header('Location: ' . S_URL . 'login');
            exit;
        }

        $idDomiciliario = $this->modeloDomiciliario->obtenerDomiciliarioPorId($_SESSION['usuario']['id']);

        // Verificar si $idDomiciliario es un array y obtener el ID
        if (is_array($idDomiciliario) && isset($idDomiciliario['id'])) {
            $idDomiciliario = $idDomiciliario['id'];
        }

        // Obtener estadísticas
        $datos = [
            'totalPedidos' => $this->modeloPedidos->contarPedidosPorDomiciliario($idDomiciliario),
            'ingresosTotales' => $this->modeloPedidos->calcularIngresosTotalesDomiciliario($idDomiciliario),
            'pedidosActivos' => $this->modeloPedidos->contarPedidosPorEstadoDomiciliario($idDomiciliario, 'en_proceso'),
            'pedidosEntregados' => $this->modeloPedidos->contarPedidosPorEstadoDomiciliario($idDomiciliario, 'entregado'),
            'pedidosRecientes' => $this->modeloPedidos->obtenerPedidosRecientesDomiciliario($idDomiciliario, 10),
            'tiempoPromedioEntrega' => $this->modeloPedidos->calcularTiempoPromedioEntrega($idDomiciliario),
            'pedidosPorMes' => $this->modeloPedidos->obtenerPedidosPorMes($idDomiciliario)
        ];

        // Cargar la vista
        require_once 'vista/Domiciliario/estadisticas.php';
    }
}
?>
