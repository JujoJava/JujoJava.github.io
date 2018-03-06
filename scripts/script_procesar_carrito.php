<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 27/02/2018
 * Time: 16:30
 */

require_once "includes_clases.php";
$param = array();
$param['correcto'] = false;
$param['texto'] = "";
session_start();
if(isset($_POST['a_nombre_cliente'])) {
    if ($_SESSION['login'] instanceof Gestor) {
        $cod_cliente = $_POST['a_nombre_cliente'];
        if (empty($cod_cliente)) {
            $param['texto'] = "No hay ningún cliente seleccionado";
        } else {
            $usuario = $_SESSION['login'];
            $carrito = $usuario->getCarrito();
            if (count($carrito) == 0) {
                $param['texto'] = "No hay ningún artículo en el carrito";
            } else {
                if($usuario->procesarCarrito($cod_cliente)) {
                    $param['texto'] = "Pedido realizado correctamente";
                    $param['correcto'] = true;
                }
                else{
                    $param['texto'] = "No se ha podido realizar el pedido";
                }
            }
        }
    }
    else if($_SESSION['login'] instanceof Cliente){
        $usuario = $_SESSION['login'];
        $carrito = $usuario->getCarrito();
        if(count($carrito) == 0){
            $param['texto'] = "No hay ningún artículo en el carrito";
        } else{
            if($usuario->procesarCarrito()) {
                $param['texto'] = "Pedido realizado correctamente";
                $param['correcto'] = true;
            }
            else{
                $param['texto'] = "No se ha podido realizar el pedido";
            }
        }
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}
?>