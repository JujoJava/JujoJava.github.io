<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 06/03/2018
 * Time: 10:17
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;

session_start();

if(isset($_POST['modo'])) {
    $modo = $_POST['modo'];
    switch ($modo) {
        case "es_gestor":
            if($_SESSION['login'] instanceof Gestor)
                $param['correcto'] = true;
            break;
    }
}
header("Content-type: application/json; charset=utf-8");
echo json_encode($param,JSON_FORCE_OBJECT);