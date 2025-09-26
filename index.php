<?php


// Incluir archivos necesarios
include_once "config.php";
include_once "conexion.php";
BaseDatos::crearInstancia();

// Definir valores predeterminados para controlador y acción
$getcontrolador = isset($_GET['controlador']) ? $_GET['controlador'] : 'paginas';
$getaccion = isset($_GET['accion']) ? $_GET['accion'] : 'inicio';

// Iniciar el buffer de salida
ob_start();

// Incluir la plantilla principal
include_once("./vista/plantilla.php");

// Incluir el ruteador y ejecutar la acción correspondiente
include_once "ruteador.php";
ruteador($getcontrolador, $getaccion);

// Finalizar y enviar el buffer de salida
ob_end_flush();
?>
