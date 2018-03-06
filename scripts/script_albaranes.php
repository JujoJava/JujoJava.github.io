<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 02/03/2018
 * Time: 9:40
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;

if(isset($_POST['lineas_a_albaran'])){
    $cod = $_POST['cod_pedido'];
    $lineas = json_decode($_POST['lineas_a_albaran']);
    $param['correcto'] = AlbaranesBD::procesoLinpedAlbaran($cod,$lineas,NULL);
}
else if(isset($_POST['cod_albaran'])){
    $modo = $_POST['modo'];
    switch($modo) {
        case "borrar":
            $param['correcto'] = AlbaranesBD::borrarAlbaran($_POST['cod_albaran']);
            break;
        case "borrar_linea":
            $param['correcto'] = AlbaranesBD::borrarLinea($_POST['num_linea'], $_POST['cod_albaran']);
            $param['precio_albaran'] = 0;
            $lineas = AlbaranesBD::obtenerLineas($_POST['cod_albaran']);
            foreach ($lineas as $linea) {
                $param['precio_albaran'] += $linea['precio'];
            }
            break;
        case "modificar_linea":
            session_start();
            if($_SESSION['login'] instanceof Gestor) $param['gestor'] = $_SESSION['login']->getNombre();
            $param['correcto']  = AlbaranesBD::modificarLinea($_POST['cod_albaran'],$_POST['num_linea'],
                                                $_POST['precio'],$_POST['iva'],$_POST['descuento']);
            if($param['correcto']){
                $param['precio_albaran'] = 0;
                $lineas = AlbaranesBD::obtenerLineas($_POST['cod_albaran']);
                foreach ($lineas as $linea) {
                    $param['precio_albaran'] += $linea['precio'];
                }
            }
            break;
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($param,JSON_FORCE_OBJECT);

?>