<?php
class baseDatos{
    public static $instancia=NULL;
    public static function crearInstancia() {
            if(!isset(self::$instancia)){
                $opcionesPDO[PDO::ATTR_ERRMODE] =PDO::ERRMODE_EXCEPTION;
                self::$instancia =new PDO ('mysql:host=localhost;dbname=bdspeedy','root','12345',$opcionesPDO);
                //echo "la conexion se realizo exitosamente";
            }
            return self ::$instancia;
        }
    }
?>
