<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 12/02/2018
 * Time: 9:40
 */

class UsuarioBD {


    public static function obtenerAllClientes(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM clientes");
        ManejoBBDD::ejecutar(array());
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerAllClientesActivos(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM clientes WHERE activo = 'SI'");
        ManejoBBDD::ejecutar(array());
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerAllSolicitudes(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM solicitudes");
        ManejoBBDD::ejecutar(array());
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerCliente($cod){
        ManejoBBDD::conectar_root();
        ManejoBBDD::preparar("SELECT * FROM clientes WHERE cod_cliente = ?;");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerGestor($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM gestor WHERE cod_gestor = ?;");
        ManejoBBDD::ejecutar(array($cod));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerSolicitud($id){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT * FROM solicitudes WHERE id_solicitud = ?");
        ManejoBBDD::ejecutar(array($id));
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos();
    }

    public static function obtenerCodCliente($nick){
        ManejoBBDD::conectar_root();
        ManejoBBDD::preparar("SELECT cod_cliente FROM clientes WHERE nick = ?;");
        ManejoBBDD::ejecutar(array($nick));
        if(ManejoBBDD::filasAfectadas() == 0){
            ManejoBBDD::desconectar();
            return null;
        }
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos()[0]['cod_cliente'];
    }

    public static function obtenerCodGestor($nick){
        ManejoBBDD::conectar_root();
        ManejoBBDD::preparar("SELECT cod_gestor FROM gestor WHERE nick = ?;");
        ManejoBBDD::ejecutar(array($nick));
        if(ManejoBBDD::filasAfectadas() == 0){
            ManejoBBDD::desconectar();
            return null;
        }
        ManejoBBDD::desconectar();
        return ManejoBBDD::getDatos()[0]['cod_gestor'];
    }

    public static function loginCliente($pass){
        ManejoBBDD::conectar_root();
        ManejoBBDD::preparar("SELECT cod_cliente FROM clientes WHERE pass = SHA(?);");
        ManejoBBDD::ejecutar(array($pass));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function loginGestor($pass){
        ManejoBBDD::conectar_root();
        ManejoBBDD::preparar("SELECT cod_gestor FROM gestor WHERE pass = SHA(?);");
        ManejoBBDD::ejecutar(array($pass));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function registraCliente($nick,$pass,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono){
        $cod_cliente = substr($nick, 0,1)."_".self::obtieneIdDisponibleCliente();
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("INSERT INTO clientes VALUES(?,?,?,?,?,?,?,?,SHA(?),?);");
        ManejoBBDD::ejecutar(
            array($cod_cliente,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono,$nick,$pass,'SI')
        );
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;

    }
    /*
     * La única diferencia entre ambos registraCliente, es que "registraClienteSol" no codifica la contraseña
     * Debido a que la contraseña ya viene codificada de 'solicitudes'
     */
    public static function registraClienteSol($nick,$pass,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono){
        $cod_cliente = substr($nick, 0,1)."_".self::obtieneIdDisponibleCliente();
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("INSERT INTO clientes VALUES(?,?,?,?,?,?,?,?,?,?)");
        ManejoBBDD::ejecutar(
            array($cod_cliente,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono,$nick,$pass,'SI')
        );
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;

    }
    public static function realizaSolicitud($nick,$pass,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono){
        $cod_cliente = self::obtieneIdDisponibleSolicitud();
        ManejoBBDD::conectar_limitado();
        ManejoBBDD::preparar("INSERT INTO solicitudes VALUES(?,?,?,?,?,?,?,?,SHA(?));");
        ManejoBBDD::ejecutar(
            array($cod_cliente,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono,$nick,$pass)
        );
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }

    public static function obtieneIdDisponibleCliente(){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT cod_cliente FROM clientes");
        ManejoBBDD::ejecutar(array());
        $clientes = ManejoBBDD::getDatos();
        $contador = 1;
        $e = true;
        while($e == true){
            $e = false;
            for($i = 0 ; $i < count($clientes) ; $i++){
                if(explode("_",$clientes[$i]['cod_cliente'])[1]*1 == $contador)
                    $e = true;
            }
            if($e == false)
                return $contador;
            $contador++;
        }
        ManejoBBDD::desconectar();
        return $contador;
    }
    public static function obtieneIdDisponibleSolicitud(){
        ManejoBBDD::conectar_limitado();
        ManejoBBDD::preparar("SELECT id_solicitud FROM solicitudes");
        ManejoBBDD::ejecutar(array());
        $solicitudes = ManejoBBDD::getDatos();
        $contador = 1;
        $e = true;
        while($e == true){
            $e = false;
            for($i = 0 ; $i < count($solicitudes) ; $i++){
                if($solicitudes[$i]['id_solicitud']*1 == $contador)
                    $e = true;
            }
            if($e == false)
                return $contador;
            $contador++;
        }
        ManejoBBDD::desconectar();
        return $contador;
    }

    public static function existeNickCliente($nick){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT nick FROM clientes WHERE nick = ?");
        ManejoBBDD::ejecutar(array($nick));
        $filas = ManejoBBDD::filasAfectadas();
        ManejoBBDD::desconectar();
        return $filas > 0;
    }
    public static function existeNickSolicitud($nick){
        ManejoBBDD::conectar_limitado();
        ManejoBBDD::preparar("SELECT nick FROM solicitudes WHERE nick = ?");
        ManejoBBDD::ejecutar(array($nick));
        $filas = ManejoBBDD::filasAfectadas();
        ManejoBBDD::desconectar();
        return $filas > 0;
    }
    public static function existeNickGestor($nick){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("SELECT nick FROM gestor WHERE nick = ?");
        ManejoBBDD::ejecutar(array($nick));
        $filas = ManejoBBDD::filasAfectadas();
        ManejoBBDD::desconectar();
        return $filas > 0;
    }
    public static function eliminaSolicitud($id){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("DELETE FROM solicitudes WHERE id_solicitud = ?");
        ManejoBBDD::ejecutar(array($id));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }
    public static function desactivaCliente($cod){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("UPDATE clientes SET activo = 'NO' WHERE cod_cliente = ?");
        ManejoBBDD::ejecutar(array($cod));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }
    public static function modificaCliente($cod,$nick,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono){
        ManejoBBDD::conectar_gestor();
        ManejoBBDD::preparar("UPDATE clientes SET
                                      nick = ?,
                                      cif_dni = ?,
                                      razon_social = ?,
                                      domicilio_social = ?,
                                      ciudad = ?,
                                      email = ?,
                                      telefono = ?
                                    WHERE cod_cliente = ?
                                    ");
        ManejoBBDD::ejecutar(array($nick,$dni,$razon_social,$domicilio_social,$ciudad,$email,$telefono,$cod));
        if(ManejoBBDD::filasAfectadas() > 0){
            ManejoBBDD::desconectar();
            return true;
        }
        ManejoBBDD::desconectar();
        return false;
    }
}