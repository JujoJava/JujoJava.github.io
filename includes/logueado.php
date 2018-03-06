<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 08/01/2018
 * Time: 9:33
 */

if(isset($_GET['page'])){
    if($_GET['page'] == "login"){
        if($usuario instanceof Gestor){
            AccesosBD::salidaGestor($usuario->getCod());
        }
        session_destroy();
        $usuario = null;
    }
}

?>