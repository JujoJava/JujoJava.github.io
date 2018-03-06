<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 28/02/2018
 * Time: 19:07
 */

require_once "Gestor.php";

class AlbaranesBD {

    public static function existeLineaPedido($cod_pedido,$num_linea_pedido){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_albaran WHERE cod_pedido = ? and num_linea_pedido = ?");
        ManejoBBDD::ejecutar(array($cod_pedido,$num_linea_pedido));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function procesoLinpedAlbaran($cod_pedido,$lineas,$concepto){
        session_start();

        $num_linea_albaran = 0;
        $l = array();
        $date = date('Y-m-d');
        $cod_gestor = null;
        ManejoBBDD::conectar_gestor();
        $cont = 0;
        foreach($lineas as $i => $linea) {
            ManejoBBDD::preparar("SELECT * FROM lineas_pedidos WHERE cod_pedido = ? and num_linea_pedido = ?");
            ManejoBBDD::ejecutar(array($cod_pedido,$linea));
            $l[$cont] = ManejoBBDD::getDatos()[0];
            ManejoBBDD::preparar("SELECT iva,descuento FROM articulos WHERE cod_articulo = ?");
            ManejoBBDD::ejecutar(array($l[$i]['cod_articulo']));
            $d = ManejoBBDD::getDatos();
            $l[$cont]['iva'] = $d[0]['iva'];
            $l[$cont]['descuento'] = $d[0]['descuento'];
            $cont++;
        }
        ManejoBBDD::preparar("SELECT cod_cliente FROM pedidos WHERE cod_pedido = ?");
        ManejoBBDD::ejecutar(array($cod_pedido));
        $cod_cliente = ManejoBBDD::getDatos()[0]['cod_cliente'];
        if($_SESSION['login'] instanceof Gestor) $cod_gestor = $_SESSION['login']->getCod();
        $cod_albaran = self::obtieneIdDisponibleAlbaran();
        ManejoBBDD::iniTransaction();
        try {
            ManejoBBDD::preparar("INSERT INTO albaranes VALUES (?,?,?,?)");
            ManejoBBDD::ejecutar(array($cod_albaran, $date, $concepto, $cod_cliente));
            if (ManejoBBDD::filasAfectadas() > 0) {
                foreach($l as $linped) {
                    ManejoBBDD::preparar("INSERT INTO lineas_albaran VALUES (?,?,?,?,?,?,?,?,?,?)");
                    ManejoBBDD::ejecutar(array(
                        $num_linea_albaran,
                        $cod_albaran,
                        $linped['cod_articulo'],
                        $linped['num_linea_pedido'],
                        $cod_pedido,
                        $cod_gestor,
                        $linped['precio'],
                        $linped['cantidad'],
                        $linped['descuento'],
                        $linped['iva']
                    ));
                    $num_linea_albaran++;
                }
                if(ManejoBBDD::filasAfectadas() > 0){
                    ManejoBBDD::commit();
                    return true;
                } else{
                    ManejoBBDD::rollback();
                    return false;
                }
            }
            else{
                ManejoBBDD::rollback();
                return false;
            }
        } catch(PDOException $e){
            ManejoBBDD::rollback();
            return false;
        }
    }

    public static function obtenerAlbaranes(){
        ManejoBBDD::conectar_gestor();
        if($_SESSION['login'] instanceof Cliente) {
            $cod_cliente = $_SESSION['login']->getCod();
            ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                              INNER JOIN clientes ON clientes.cod_cliente = albaranes.cod_cliente
                              WHERE clientes.activo = 'SI' and clientes.cod_cliente = ?");
            ManejoBBDD::ejecutar(array($cod_cliente));
        }
        else{
            ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                              INNER JOIN clientes ON clientes.cod_cliente = albaranes.cod_cliente
                              WHERE clientes.activo = 'SI'");
            ManejoBBDD::ejecutar(array());
        }
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerLineas($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_albaran WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function borrarAlbaran($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_albaran WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod));

        ManejoBBDD::preparar("DELETE FROM albaranes WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod));

        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        return false;
    }

    public static function borrarLinea($num,$cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_albaran WHERE cod_albaran = ? and num_linea_albaran = ?");
        ManejoBBDD::ejecutar(array($cod,$num));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function obtenerAlbaranesBusqueda($texto,$modo,$campo){
        ManejoBBDD::conectar_gestor();
        $client = "";
        if($_SESSION['login'] instanceof Cliente)
            $client = "and clientes.cod_cliente = '".$_SESSION['login']->getCod()."'";
        if($campo == "precio"){
            switch($modo){
                case "menor":
                    ManejoBBDD::preparar("select albaranes.* from albaranes 
                                          inner join lineas_albaran on lineas_albaran.cod_albaran = albaranes.cod_albaran
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_albaran.cod_albaran having sum(lineas_albaran.precio) < ?");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("select albaranes.* from albaranes 
                                          inner join lineas_albaran on lineas_albaran.cod_albaran = albaranes.cod_albaran 
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_albaran.cod_albaran having sum(lineas_albaran.precio) > ?");
                    break;
                case "igual":
                    ManejoBBDD::preparar("select albaranes.* from albaranes
                                          inner join lineas_albaran on lineas_albaran.cod_albaran = albaranes.cod_albaran
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_pedidos.cod_pedido having sum(lineas_albaran.precio) = ?");
                    break;
            }
        }
        else if($campo == "nick"){
            switch($modo){
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("select albaranes.* from albaranes 
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%".$texto;
                    ManejoBBDD::preparar("select albaranes.* from albaranes 
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%".$texto."%";
                    ManejoBBDD::preparar("select albaranes.* from albaranes 
                                          inner join clientes on clientes.cod_cliente = albaranes.cod_cliente 
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
            ManejoBBDD::preparar("select distinct albaranes.* from albaranes inner join lineas_albaran
                                                on albaranes.cod_albaran = lineas_albaran.cod_albaran
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente   
                                                inner join articulos on articulos.cod_articulo = lineas_albaran.cod_articulo 
                                                where articulos.nombre LIKE ? and clientes.activo = 'SI' $client");
        }
        else {
            switch ($modo) {
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%" . $texto;
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%" . $texto . "%";
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "menor":
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo < ? and clientes.activo = 'SI' $client");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo > ? and clientes.activo = 'SI' $client");
                    break;
                case "igual":
                    ManejoBBDD::preparar("SELECT albaranes.* FROM albaranes
                                                inner join clientes on clientes.cod_cliente = albaranes.cod_cliente
                                                where $campo = ? and clientes.activo = 'SI' $client");
                    break;
            }
        }
        ManejoBBDD::ejecutar(array($texto));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function modificarLinea($cod_albaran,$num_linea_albaran,$precio,$iva,$descuento){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("UPDATE lineas_albaran
                                    SET precio = ?, iva = ?, descuento = ?
                                    WHERE num_linea_albaran = ? and cod_albaran = ?");
        ManejoBBDD::ejecutar(array($precio,$iva,$descuento,$num_linea_albaran,$cod_albaran));
        return true;
    }

    public static function obtieneIdDisponibleAlbaran(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT cod_albaran FROM albaranes");
        ManejoBBDD::ejecutar(array());
        $albaranes = ManejoBBDD::getDatos();
        $contador = 1;
        $e = true;
        while($e == true){
            $e = false;
            for($i = 0 ; $i < count($albaranes) ; $i++){
                if(explode("_",$albaranes[$i]['cod_albaran'])[1]*1 == $contador)
                    $e = true;
            }
            if($e == false)
                return "alb_".$contador;
            $contador++;
        }
        ManejoBBDD::desconectar();
        return "alb_".$contador;
    }

}