<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 19/02/2018
 * Time: 8:18
 */

require_once "clases/ManejoBBDD.php";
require_once "clases/UsuarioBD.php";

$param = array();
$param['correcto'] = false;
$param['texto'] = "";
session_start();

if(isset($_POST['sol_nom'])){
    $nombre = $_POST['sol_nom'];
    $password = $_POST['sol_pass'];
    if(!UsuarioBD::existeNickCliente($nombre) && !UsuarioBD::existeNickSolicitud($nombre)){
        if(UsuarioBD::registraCliente(
            $nombre,
            $password,
            $_POST['sol_dni'],
            $_POST['sol_razon_social'],
            $_POST['sol_domicilio_social'],
            $_POST['sol_ciudad'],
            $_POST['sol_email'],
            $_POST['sol_telefono'])
        ){
            $param['correcto'] = true;
            $param['texto'] = "Se ha registrado el usuario";
        }
        else{
            $param['texto'] = "Ha habido un error al registrar el usuario";
        }
    }
    else $param['texto'] = "El nombre de usuario está en uso";

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}

?>