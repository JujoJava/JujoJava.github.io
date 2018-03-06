<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 08/02/2018
 * Time: 14:14
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;
$param['texto_error'] = "";
session_start();

if(isset($_POST['check_nom'])){
    $nombre = $_POST['check_nom'];
    $pass = $_POST['check_pass'];
    switch($_POST['tipo_login']){
        case "cliente":
            if(UsuarioBD::existeNickCliente($nombre)){
                if(UsuarioBD::loginCliente($pass)){
                    $param['correcto'] = true;
                    $param['texto_error'] = "Todo bien";
                    $_SESSION['login'] = new Cliente(UsuarioBD::obtenerCodCliente($nombre),$nombre);
                }
                else{
                    $param['texto_error'] = "La contraseña no es correcta";
                }
            }
            else{
                $param['texto_error'] = "El nombre de usuario no existe";
            }
            break;
        case "gestor":
            if(UsuarioBD::existeNickGestor($nombre)){ //comprueba el nick
                if(UsuarioBD::loginGestor($pass)){ //comprueba la contraseña
                    $cod = UsuarioBD::obtenerCodGestor($nombre);
                    if(AccesosBD::accesoGestor($cod)){ //crea el acceso
                        $param['correcto'] = true;
                        $param['texto_error'] = "Todo bien";
                        $_SESSION['login'] = new Gestor($cod,$nombre);
                    }
                    else{
                        $param['texto_error'] = "Ha habido un error a la hora de acceder";
                    }
                }
                else{
                    $param['texto_error'] = "La contraseña no es correcta";
                }
            }
            else{
                $param['texto_error'] = "El nombre de usuario no existe";
            }
            break;
    }

    ManejoBBDD::desconectar();

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}

?>