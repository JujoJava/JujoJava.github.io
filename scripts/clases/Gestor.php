<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 21/02/2018
 * Time: 18:53
 */

require_once "Usuario.php";

class Gestor extends Usuario {
    private $nombre;
    public function __construct($cod,$nick){
        parent::__construct($cod,$nick);
        $datos = UsuarioBD::obtenerGestor($cod);
        if($datos != null){
            $this->nombre = $datos[0]['nombre'];
        }
    }
    public function getNombre(){
        return $this->nombre;
    }
    public function procesarCarrito($cod_cliente){
        $cod_pedido = PedidosBD::insertarPedido($cod_cliente);
        if(PedidosBD::insertarLineas($cod_pedido,$this->getCod(),$this->getCarrito())){
            $this->carrito = array();
            return true;
        }
        return false;
    }
}