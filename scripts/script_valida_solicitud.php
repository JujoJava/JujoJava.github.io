<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 26/02/2018
 * Time: 14:19
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
session_start();

if(isset($_POST['id_solicitud'])){
    $id_solicitud = $_POST['id_solicitud'];
    $solicitud = UsuarioBD::obtenerSolicitud($id_solicitud);
    if(UsuarioBD::eliminaSolicitud($id_solicitud)) {
        $param['correcto'] = UsuarioBD::registraClienteSol(
            $solicitud[0]['nick'],
            $solicitud[0]['pass'],
            $solicitud[0]['cif_dni'],
            $solicitud[0]['razon_social'],
            $solicitud[0]['domicilio_social'],
            $solicitud[0]['ciudad'],
            $solicitud[0]['email'],
            $solicitud[0]['telefono']
        );
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param);
}

?>