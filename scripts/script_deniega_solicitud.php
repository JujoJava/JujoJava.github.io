<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 26/02/2018
 * Time: 16:36
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
session_start();

if(isset($_POST['id_solicitud'])){
    $id_solicitud = $_POST['id_solicitud'];
    if(UsuarioBD::eliminaSolicitud($id_solicitud)) {
        $param['correcto'] = true;
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param);
}

?>