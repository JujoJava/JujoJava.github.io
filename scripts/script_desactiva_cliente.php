<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 26/02/2018
 * Time: 16:49
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
session_start();

if(isset($_POST['cod_cliente'])){
    $cod_cliente = $_POST['cod_cliente'];
    if(UsuarioBD::desactivaCliente($cod_cliente)) {
        $param['correcto'] = true;
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param);
}

?>