<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 27/02/2018
 * Time: 10:27
 */

class ArticulosBD {
    public static function obtenerArticulos(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM articulos");
        ManejoBBDD::ejecutar(array());
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }
    public static function obtenerArticulosBusqueda($texto,$modo,$campo){
        ManejoBBDD::conectar_gestor();
        switch($modo){
            case "empieza":
                $texto = $texto."%";
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo LIKE ?");
                break;
            case "acaba":
                $texto = "%".$texto;
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo LIKE ?");
                break;
            case "contiene":
                $texto = "%".$texto."%";
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo LIKE ?");
                break;
            case "menor":
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo < ?");
                break;
            case "mayor":
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo > ?");
                break;
            case "igual":
                ManejoBBDD::preparar("SELECT * FROM articulos WHERE $campo = ?");
                break;
        }
        ManejoBBDD::ejecutar(array($texto));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }
    public static function obtenerArticulo($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM articulos WHERE cod_articulo = ?");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

}