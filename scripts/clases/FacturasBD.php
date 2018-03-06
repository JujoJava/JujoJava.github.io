<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 03/03/2018
 * Time: 15:01
 */

class FacturasBD {
    public static function existeAlbaran($cod_albaran){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_facturas WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod_albaran));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }
    public static function procesoAlbaranesFactura($albaranes,$cod_cliente,$concepto,$descuento_factura){
        session_start();

        $date = date('Y-m-d');
        $cod_gestor = null;
        $lineas_albaran = array();

        if($_SESSION['login'] instanceof Gestor) $cod_gestor = $_SESSION['login']->getCod();

        $cod_factura = self::obtieneIdDisponibleFactura();

        ManejoBBDD::conectar_gestor();
        foreach($albaranes as $cod_albaran) {
            ManejoBBDD::preparar("SELECT * FROM lineas_albaran WHERE cod_albaran = ?");
            ManejoBBDD::ejecutar(array($cod_albaran));
            $datos = ManejoBBDD::getDatos();
            foreach($datos as $dato){
                $lineas_albaran[] = $dato;
            }
        }
        try {
            ManejoBBDD::iniTransaction();
            ManejoBBDD::preparar("INSERT INTO facturas VALUES (?,?,?,?,?)");
            ManejoBBDD::ejecutar(array($cod_factura,$date,$descuento_factura,$concepto,$cod_cliente));
            if(ManejoBBDD::filasAfectadas() > 0){
                ManejoBBDD::preparar("INSERT INTO lineas_facturas VALUES (?,?,?,?,?,?,?,?,?,?)");
                $linea_factura = 0;
                foreach($lineas_albaran as $linea){
                    ManejoBBDD::ejecutar(array(
                        $linea_factura,
                        $cod_factura,
                        $linea['cod_articulo'],
                        $linea['num_linea_albaran'],
                        $linea['cod_albaran'],
                        $cod_gestor,
                        $linea['precio'],
                        $linea['cantidad'],
                        $linea['descuento'],
                        $linea['iva']
                    ));
                    $linea_factura++;
                }
                ManejoBBDD::commit();
                ManejoBBDD::desconectar();
                return true;
            }
            else{
                ManejoBBDD::rollback();
                ManejoBBDD::desconectar();
                return false;
            }
        } catch(PDOException $e){
            ManejoBBDD::rollback();
            ManejoBBDD::desconectar();
            return false;
        }
    }

    public static function obtenerFacturas(){
        ManejoBBDD::conectar_gestor();
        if($_SESSION['login'] instanceof Cliente) {
            $cod_cliente = $_SESSION['login']->getCod();
            ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                              INNER JOIN clientes ON clientes.cod_cliente = facturas.cod_cliente
                              WHERE clientes.activo = 'SI' and clientes.cod_cliente = ?");
            ManejoBBDD::ejecutar(array($cod_cliente));
        }
        else{
            ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                              INNER JOIN clientes ON clientes.cod_cliente = facturas.cod_cliente
                              WHERE clientes.activo = 'SI'");
            ManejoBBDD::ejecutar(array());
        }
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerLineas($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM lineas_facturas WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function cantidadLineasAlbaran($cod_albaran){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT count(cod_albaran) as resul FROM lineas_facturas WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod_albaran));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function desfacturarAlbaran($cod_albaran){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_facturas WHERE cod_albaran = ?");
        ManejoBBDD::ejecutar(array($cod_albaran));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function tieneLineas($cod_factura){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT count(*) as resul FROM lineas_facturas WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($cod_factura));
        if(ManejoBBDD::getDatos()[0]['resul'] > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function obtenerFacturasBusqueda($texto,$modo,$campo){
        ManejoBBDD::conectar_gestor();
        $client = "";
        if($_SESSION['login'] instanceof Cliente)
            $client = "and clientes.cod_cliente = '".$_SESSION['login']->getCod()."'";
        if ($campo == "precio") {
            switch ($modo) {
                case "menor":
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join lineas_facturas on lineas_facturas.cod_factura = facturas.cod_factura
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_facturas.cod_factura having sum(lineas_facturas.precio) < ?");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join lineas_facturas on lineas_facturas.cod_factura = facturas.cod_factura
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_facturas.cod_factura having sum(lineas_facturas.precio) > ?");
                    break;
                case "igual":
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join lineas_facturas on lineas_facturas.cod_factura = facturas.cod_factura
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                          where clientes.activo = 'SI' $client
                                          group by lineas_facturas.cod_factura having sum(lineas_facturas.precio) = ?");
                    break;
            }
        } else if ($campo == "nick") {
            switch ($modo) {
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%" . $texto;
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%" . $texto . "%";
                    ManejoBBDD::preparar("select facturas.* from facturas 
                                          inner join clientes on clientes.cod_cliente = facturas.cod_cliente 
                                          where clientes.nick like ? and clientes.activo = 'SI' $client");
                    break;
            }
        } else if ($campo == "articulo") {
            switch ($modo) {
                case "empieza":
                    $texto = $texto . "%";
                    break;
                case "acaba":
                    $texto = "%" . $texto;
                    break;
                case "contiene":
                    $texto = "%" . $texto . "%";
                    break;
            }
            ManejoBBDD::preparar("select distinct facturas.* from facturas inner join lineas_facturas
                                                on facturas.cod_factura = lineas_facturas.cod_factura
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente   
                                                inner join articulos on articulos.cod_articulo = lineas_facturas.cod_articulo 
                                                where articulos.nombre LIKE ? and clientes.activo = 'SI' $client");
        } else {
            switch ($modo) {
                case "empieza":
                    $texto = $texto . "%";
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "acaba":
                    $texto = "%" . $texto;
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "contiene":
                    $texto = "%" . $texto . "%";
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo LIKE ? and clientes.activo = 'SI' $client");
                    break;
                case "menor":
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo < ? and clientes.activo = 'SI' $client");
                    break;
                case "mayor":
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo > ? and clientes.activo = 'SI' $client");
                    break;
                case "igual":
                    ManejoBBDD::preparar("SELECT facturas.* FROM facturas
                                                inner join clientes on clientes.cod_cliente = facturas.cod_cliente
                                                where $campo = ? and clientes.activo = 'SI' $client");
                    break;
            }
        }
        ManejoBBDD::ejecutar(array($texto));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function borrarFactura($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM lineas_facturas WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($cod));

        ManejoBBDD::preparar("DELETE FROM facturas WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($cod));

        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        return false;
    }

    public static function modificarDescuentoFactura($cod,$descuento){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("UPDATE facturas SET descuento_factura = ? WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($descuento,$cod));
        return true;
    }

    public static function obtenerFactura($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM facturas WHERE cod_factura = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtieneIdDisponibleFactura(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT cod_factura FROM facturas");
        ManejoBBDD::ejecutar(array());
        $facturas = ManejoBBDD::getDatos();
        $contador = 1;
        $e = true;
        while($e == true){
            $e = false;
            for($i = 0 ; $i < count($facturas) ; $i++){
                if(explode("_",$facturas[$i]['cod_factura'])[1]*1 == $contador)
                    $e = true;
            }
            if($e == false)
                return "fac_".$contador;
            $contador++;
        }
        ManejoBBDD::desconectar();
        return "fac_".$contador;
    }
}