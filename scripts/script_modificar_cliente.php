<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 26/02/2018
 * Time: 20:02
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
$param['texto'] = "";
session_start();

if(isset($_POST['cod_cliente'])){
    $cod_cliente = $_POST['cod_cliente'];
    $nick = $_POST['mod_nick'];
    if(UsuarioBD::obtenerCliente($cod_cliente)[0]['nick'] == $nick || (!UsuarioBD::existeNickCliente($nick) && !UsuarioBD::existeNickSolicitud($nick))){
        if(UsuarioBD::modificaCliente(
            $cod_cliente,
            $nick,
            $_POST['mod_dni'],
            $_POST['mod_razon_social'],
            $_POST['mod_domicilio_social'],
            $_POST['mod_ciudad'],
            $_POST['mod_email'],
            $_POST['mod_telefono']
        )) $param['correcto'] = true;
        else $param['texto'] = "No se ha modificado el cliente";
    }
    else $param['texto'] = "El nombre de usuario está en uso";

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}

?>