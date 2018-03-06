<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 27/02/2018
 * Time: 18:18
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
$param['texto_error'] = "No se ha podido añadir el articulo";
session_start();

if(isset($_POST['cod_articulo'])){
    if(isset($_SESSION['login'])){
        $usuario = $_SESSION['login'];
        if(isset($_POST['modo'])){
            switch($_POST['modo']){
                case "suma":
                    if($usuario instanceof Usuario){
                        $usuario->getArticulo($_POST['cod_articulo'])->sumaCantidad();
                        $param['correcto'] = true;
                    }
                    break;
                case "resta":
                    if($usuario instanceof Usuario){
                        $cod = $_POST['cod_articulo'];
                        $usuario->getArticulo($cod)->restaCantidad();
                        if($usuario->getArticulo($cod)->getCantidad() <= 0)
                            $usuario->delArticulo($cod);
                        $param['correcto'] = true;
                    }
                    break;
                case "quitar":
                    if($usuario instanceof Usuario){
                        $usuario->delArticulo($_POST['cod_articulo']);
                        $param['correcto'] = true;
                    }
                    break;

            }
        }
        else {
            if ($usuario instanceof Usuario) {
                $usuario->addArticulo($_POST['cod_articulo']);
                $param['correcto'] = true;
            }
        }
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}

?>
