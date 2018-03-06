<?php

$selected = array(
    "accesos_gestores" => "",
    "gestion_usuarios" => "",
    "gestion_pedidos" => "",
    "gestion_albaranes" => "",
    "realizar_pedido" => "",
    "gestion_facturas" => "",
    "datos_cliente" => ""
);
if(isset($_GET['page']))
    $selected[$_GET['page']] = "active";
if(isset($_POST['page']))
    $selected[$_POST['page']] = "active";

$accesos_gestores = $selected['accesos_gestores'];
$gestion_usuarios = $selected['gestion_usuarios'];
$gestion_pedidos = $selected['gestion_pedidos'];
$gestion_albaranes = $selected['gestion_albaranes'];
$gestion_facturas = $selected['gestion_facturas'];
$realizar_pedido = $selected['realizar_pedido'];
$datos_cliente = $selected['datos_cliente'];

if(isset($_SESSION['login']) && $usuario != null){
    echo "<nav id='menu' class='navbar navbar-expand-lg navbar-dark bg-dark'>";
    echo "<a class='navbar-brand' href='#'>".$usuario->getNick()."</a>
            <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarColor02' aria-controls='navbarColor02' aria-expanded='false' aria-label='Toggle navigation'>
                <span class='navbar-toggler-icon'><span>
            </button>";
    echo "<div class='collapse navbar-collapse' id='navbarColor02'>";
    echo "<ul class='navbar-nav mr-auto'>";
    if($usuario instanceof Gestor){
        echo "<li class='nav-item'>
                <a class='nav-link $accesos_gestores' href='index.php?page=accesos_gestores'>Accesos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_usuarios' href='index.php?page=gestion_usuarios'>Usuarios</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $realizar_pedido' href='index.php?page=realizar_pedido'>Artículos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_pedidos' href='index.php?page=gestion_pedidos'>Pedidos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_albaranes' href='index.php?page=gestion_albaranes'>Albaranes</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_facturas' href='index.php?page=gestion_facturas'>Facturas</a></li>";
    }
    else if($usuario instanceof Cliente){
        echo "<li class='nav-item'>
                <a class='nav-link $datos_cliente' href='index.php?page=datos_cliente'>Ver mis Datos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $realizar_pedido' href='index.php?page=realizar_pedido'>Artículos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_pedidos' href='index.php?page=gestion_pedidos'>Pedidos</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_albaranes' href='index.php?page=gestion_albaranes'>Albaranes</a></li>";
        echo "<li class='nav-item'>
                <a class='nav-link $gestion_facturas' href='index.php?page=gestion_facturas'>Facturas</a></li>";
    }
    echo "</ul>";
    echo "<div id='cerrar_sesion' class='my-2 my-lg-0'>
            <a href='index.php?page=login'><img src='img/logout.png'></a></div>";
    echo "</div></nav>";
}

?>
