<?php
require_once 'modelos/Producto.php';

class ControladorPaginas{
            public function inicio() {
                $modeloProductos = new Producto();
                $productosRecientes = $modeloProductos->obtenerProductosRecientes();
                
                include_once("vista/paginas/inicio.php");
            }
            public function contacto(){
                include_once "vista/paginas/contacto.php";
                }               
            public function nosotros(){
                include_once "vista/paginas/nosotros.php";
                }
            public function registro (){
                include_once "vista/usuarios/registro.php";
            }
            public function unete (){
                include_once "vista/paginas/unete.php";
            }
            public function login (){
                include_once "vista/usuarios/login.php";
            }

            public function FormTien (){
                include_once "vista/usuarios/formTienda.php";
            }

            public function FormDomi (){
                include_once "vista/usuarios/formDomiciliario.php";
            }

            public function notificaciones() {
                // Obtener el tipo de usuario (asumimos que está almacenado en la sesión)
                $tipoUsuario = isset($_SESSION['usuario']['id_rol']) ? $_SESSION['usuario']['id_rol'] : 'visitante';
            
                // Obtener el tipo de notificación de la URL o establecer un valor por defecto
                $tipoNotificacion = isset($_GET['tipo']) ? $_GET['tipo'] : 'solicitud';
            
                // Aquí deberías obtener las notificaciones reales
                // Por ahora, usaremos un array vacío como ejemplo
                $notificaciones = [];
            
                $datos = [
                    'tipoNotificacion' => $tipoNotificacion,
                    'notificaciones' => $notificaciones,
                    'tipoUsuario' => $tipoUsuario
                ];
            
                // Cargar la vista y pasar los datos
                require_once './vista/modulos/notificaciones.php';
            }

            public function error($mensaje = null) {
                if ($mensaje === null) {
                    $mensaje = "Ha ocurrido un error inesperado.";
                }
                include_once "vista/paginas/error.php";
            }
        
        }


?>