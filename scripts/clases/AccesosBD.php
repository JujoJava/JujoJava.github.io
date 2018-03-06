<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 23/02/2018
 * Time: 12:27
 */

class AccesosBD {

    public static function accesoGestor($cod){
        ManejoBBDD::conectar_gestor();
        $date = date('Y-m-d H:i:s');
        ManejoBBDD::preparar("INSERT INTO accesos(fecha_hora_acceso,fecha_hora_salida,cod_gestor) 
                                    VALUES (?,null,?)");
        ManejoBBDD::ejecutar(array($date,$cod));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function salidaGestor($cod){
        ManejoBBDD::conectar_gestor();
        $date = date('Y-m-d H:i:s');
        ManejoBBDD::preparar("UPDATE accesos SET fecha_hora_salida = ? WHERE cod_gestor = ?");
        ManejoBBDD::ejecutar(array($date,$cod));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function obtieneAccesos(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM accesos");
        ManejoBBDD::ejecutar(array());
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

}