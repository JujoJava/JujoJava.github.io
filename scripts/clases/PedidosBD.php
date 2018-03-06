<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 27/02/2018
 * Time: 16:41
 */

require_once "ArticulosBD.php";

class PedidosBD {


    public static function insertarPedido($cod_cliente){
        $cod_pedido = self::obtieneIdDisponiblePedido();
        $date = date('Y-m-d');
        ManejoBBDD::conectar_cliente();
        ManejoBBDD::preparar("INSERT INTO pedidos VALUES(?,?,?)");
        ManejoBBDD::ejecutar(array($cod_pedido,$date,$cod_cliente));
        ManejoBBDD::desconectar();
        return $cod_pedido;
    }

    public static function insertarLineas($cod_pedido,$cod_gestor,$carrito){
        $num_linea = 0;
        ManejoBBDD::conectar_cliente();
        ManejoBBDD::preparar("INSERT INTO lineas_pedidos VALUES(?,?,?,?,?,?)");
        ManejoBBDD::iniTransaction();
        try{
            foreach($carrito as $articulo){
                if($articulo instanceof Articulo){
                    ManejoBBDD::ejecutar(array(
                        $num_linea,
                        $cod_pedido,
                        $articulo->getCodArticulo(),
                        $cod_gestor,
                        $articulo->getPrecioTotal(),
                        $articulo->getCantidad()
                    ));
                    $num_linea++;
                }
            }
            ManejoBBDD::commit();
            ManejoBBDD::desconectar();
            return true;
        } catch(PDOException $e){
            ManejoBBDD::rollback();
            ManejoBBDD::desconectar();
        }
        return false;
    }

    public static function obtenerPedidos(){
        ManejoBBDD::conectar_gestor();
        if($_SESSION['login'] instanceof Cliente) {
            $cod_cliente = $_SESSION['login']->getCod();
            ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                  INNER JOIN clientes ON clientes.cod_cliente = pedidos.cod_cliente
                                  WHERE clientes.activo = 'SI' and clientes.cod_cliente = ?");
            ManejoBBDD::ejecutar(array($cod_cliente));
        }
        else{
            ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                  INNER JOIN clientes ON clientes.cod_cliente = pedidos.cod_cliente
                                  WHERE clientes.activo = 'SI'");
            ManejoBBDD::ejecutar(array());
        }
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerPedido($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM pedidos WHERE cod_pedido = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerLineas($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_pedidos WHERE cod_pedido = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerLinea($num,$cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_pedidos WHERE cod_pedido = ? and num_linea_pedido = ?");
        ManejoBBDD::ejecutar(array($cod,$num));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function borrarPedido($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_pedidos WHERE cod_pedido = ?");
        ManejoBBDD::ejecutar(array($cod));

        ManejoBBDD::preparar("DELETE FROM pedidos WHERE cod_pedido = ?");
        ManejoBBDD::ejecutar(array($cod));

        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        return false;
    }

    public static function borrarLinea($num,$cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_pedidos WHERE cod_pedido = ? and num_linea_pedido = ?");
        ManejoBBDD::ejecutar(array($cod,$num));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function restarLinea($num,$cod){
        session_start();
        $cod_articulo = self::obtenerLinea($num,$cod)[0]['cod_articulo'];
        $precio_articulo = ArticulosBD::obtenerArticulo($cod_articulo)[0]['precio'];
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT cantidad FROM lineas_pedidos WHERE num_linea_pedido = ? and cod_pedido = ?");
        ManejoBBDD::ejecutar(array($num,$cod));
        $cantidad = ManejoBBDD::getDatos()[0]['cantidad'];
        if($cantidad > 1){
            if($_SESSION['login'] instanceof Gestor){
                ManejoBBDD::preparar("UPDATE lineas_pedidos 
                                    SET cantidad = cantidad - 1, precio = cantidad * ?, cod_gestor = ?
                                    WHERE num_linea_pedido = ? and cod_pedido = ?");
                ManejoBBDD::ejecutar(array($precio_articulo, $_SESSION['login']->getCod(), $num, $cod));
            }
            else {
                ManejoBBDD::preparar("UPDATE lineas_pedidos SET cantidad = cantidad - 1, precio = cantidad * ?
                                    WHERE num_linea_pedido = ? and cod_pedido = ?");
                ManejoBBDD::ejecutar(array($precio_articulo, $num, $cod));
            }
        }
        else{
            ManejoBBDD::preparar("DELETE FROM lineas_pedidos WHERE num_linea_pedido = ? and cod_pedido = ?");
            ManejoBBDD::ejecutar(array($num,$cod));
        }
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function sumarLinea($num,$cod){
        session_start();
        $cod_articulo = self::obtenerLinea($num,$cod)[0]['cod_articulo'];
        $precio_articulo = ArticulosBD::obtenerArticulo($cod_articulo)[0]['precio'];
        ManejoBBDD::conectar_gestor();
        if($_SESSION['login'] instanceof Gestor){
            ManejoBBDD::preparar("UPDATE lineas_pedidos 
                                    SET cantidad = cantidad + 1, precio = cantidad * ?, cod_gestor = ?
                                    WHERE num_linea_pedido = ? and cod_pedido = ?");
            ManejoBBDD::ejecutar(array($precio_articulo, $_SESSION['login']->getCod(), $num, $cod));
        }
        else {
            ManejoBBDD::preparar("UPDATE lineas_pedidos SET cantidad = cantidad + 1, precio = cantidad * ?
                                    WHERE num_linea_pedido = ? and cod_pedido = ?");
            ManejoBBDD::ejecutar(array($precio_articulo, $num, $cod));
        }
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function obtenerPedidosBusqueda($texto,$modo,$campo){
        ManejoBBDD::conectar_gestor();
        $client = "";
        if($_SESSION['login'] instanceof Cliente)
            $client = "and clientes.cod_cliente = '".$_SESSION['login']->getCod()."'";
        if($campo == "precio"){
            switch($modo){
                case "menor":
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join lineas_pedidos on lineas_pedidos.cod_pedido = pedidos.cod_pedido
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_pedidos.cod_pedido having sum(precio) < ?");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join lineas_pedidos on lineas_pedidos.cod_pedido = pedidos.cod_pedido
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_pedidos.cod_pedido having sum(precio) > ?");
                    break;
                case "igual":
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join lineas_pedidos on lineas_pedidos.cod_pedido = pedidos.cod_pedido
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_pedidos.cod_pedido having sum(precio) = ?");
                    break;
            }
        }
        else if($campo == "nick"){
            switch($modo){
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%".$texto;
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%".$texto."%";
                    ManejoBBDD::preparar("select pedidos.* from pedidos 
                                          inner join clientes on clientes.cod_cliente = pedidos.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
            }
        }
        else if($campo == "articulo"){
            switch($modo){
                case "empieza":
                    $texto = $texto . "%";
                    break;
                case "acaba":
                    $texto = "%".$texto;
                    break;
                case "contiene":
                    $texto = "%".$texto."%";
                    break;
            }
            ManejoBBDD::preparar("select distinct pedidos.* from pedidos inner join lineas_pedidos 
                                                on pedidos.cod_pedido = lineas_pedidos.cod_pedido
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente   
                                                inner join articulos on articulos.cod_articulo = lineas_pedidos.cod_articulo 
                                                where articulos.nombre LIKE ? and clientes.activo = 'SI' $client");
        }
        else {
            switch ($modo) {
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%" . $texto;
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%" . $texto . "%";
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "menor":
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo < ? and clientes.activo = 'SI' $client");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo > ? and clientes.activo = 'SI' $client");
                    break;
                case "igual":
                    ManejoBBDD::preparar("SELECT pedidos.* FROM pedidos
                                                inner join clientes on clientes.cod_cliente = pedidos.cod_cliente
                                                where $campo = ? and clientes.activo = 'SI' $client");
                    break;
            }
        }
        ManejoBBDD::ejecutar(array($texto));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtieneIdDisponiblePedido(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT cod_pedido FROM pedidos");
        ManejoBBDD::ejecutar(array());
        $pedidos = ManejoBBDD::getDatos();
        $contador = 1;
        $e = true;
        while($e == true){
            $e = false;
            for($i = 0 ; $i < count($pedidos) ; $i++){
                if(explode("_",$pedidos[$i]['cod_pedido'])[1]*1 == $contador)
                    $e = true;
            }
            if($e == false)
                return "ped_".$contador;
            $contador++;
        }
        ManejoBBDD::desconectar();
        return "ped_".$contador;
    }

}