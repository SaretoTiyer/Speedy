<?php
session_start();
    isset($_SESSION['usuario']);
    session_destroy();
    header("Location: ../../index.php?controlador=paginas&accion=inicio");

?>