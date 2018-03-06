<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 01/03/2018
 * Time: 9:26
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;

if(isset($_POST['cod_pedido'])){
    $modo = $_POST['modo'];
    switch($modo){
        case "borrar":
            $param['correcto'] = PedidosBD::borrarPedido($_POST['cod_pedido']);
            break;
        case "borrar_linea":
            $param['correcto'] = PedidosBD::borrarLinea($_POST['num_linea'],$_POST['cod_pedido']);
            $param['precio_pedido'] = 0;
            $lineas = PedidosBD::obtenerLineas($_POST['cod_pedido']);
            foreach($lineas as $linea){
                $param['precio_pedido'] += $linea['precio'];
            }
            break;
        case "resta":
            $param['correcto'] = PedidosBD::restarLinea($_POST['num_linea'],$_POST['cod_pedido']);
            $l = PedidosBD::obtenerLinea($_POST['num_linea'],$_POST['cod_pedido']);
            if(count($l) > 0) {
                $param['precio_linea'] = $l[0]['precio'];
                $param['cantidad'] = $l[0]['cantidad'];
                $param['gestor'] = UsuarioBD::obtenerGestor($l[0]['cod_gestor'])[0]['nombre'];
            }
            $lineas = PedidosBD::obtenerLineas($_POST['cod_pedido']);
            $param['precio_pedido'] = 0;
            foreach($lineas as $linea){
                $param['precio_pedido'] += $linea['precio'];
            }
            break;
        case "suma":
            $param['correcto'] = PedidosBD::sumarLinea($_POST['num_linea'],$_POST['cod_pedido']);
            $l = PedidosBD::obtenerLinea($_POST['num_linea'],$_POST['cod_pedido']);
            $param['precio_linea'] = $l[0]['precio'];
            $param['cantidad'] = $l[0]['cantidad'];
            $param['gestor'] = UsuarioBD::obtenerGestor($l[0]['cod_gestor'])[0]['nombre'];
            $lineas = PedidosBD::obtenerLineas($_POST['cod_pedido']);
            $param['precio_pedido'] = 0;
            foreach($lineas as $linea){
                $param['precio_pedido'] += $linea['precio'];
            }
            break;
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}


?>