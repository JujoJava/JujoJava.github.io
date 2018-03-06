<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 25/01/2018
 * Time: 19:14
 */

//header("Content-type: text/html; charset=utf-8");

require_once "scripts/includes_clases.php";

$usuario = null;

session_start();

if(isset($_SESSION['login'])){
    $usuario = $_SESSION['login'];
}
else if(isset($_GET['page'])){
    if($_GET['page'] != "login" && $_GET['page'] != "registro") {
        header("location:index.php?page=login");
    }
}

?>