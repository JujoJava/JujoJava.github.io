<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 12/02/2018
 * Time: 9:32
 */

require_once "Usuario.php";

class Cliente extends Usuario{
    private $dni;
    private $razon_social;
    private $domicilio_social;
    private $ciudad;
    private $email;
    private $telefono;
    private $activo;
    public function __construct($cod,$nick){
        parent::__construct($cod,$nick);
        $datos = UsuarioBD::obtenerCliente($cod);
        if($datos != null){
            $this->dni = $datos[0]['cif_dni'];
            $this->razon_social = $datos[0]['razon_social'];
            $this->domicilio_social = $datos[0]['domicilio_social'];
            $this->ciudad = $datos[0]['ciudad'];
            $this->email = $datos[0]['email'];
            $this->telefono = $datos[0]['telefono'];
            if($datos[0]['activo'] == "NO")
                $this->activo = false;
            else
                $this->activo = true;
        }
    }
    public function getDni(){
        return $this->dni;
    }
    public function getRazonSocial(){
        return $this->razon_social;
    }
    public function getCiudad(){
        return $this->ciudad;
    }
    public function getEmail(){
        return $this->email;
    }
    public function getTelefono(){
        return $this->telefono;
    }
    public function getActivo(){
        return $this->activo;
    }
    public function procesarCarrito(){
        $cod_pedido = PedidosBD::insertarPedido($this->getCod());
        if(PedidosBD::insertarLineas($cod_pedido,NULL,$this->getCarrito())){
            $this->carrito = array();
            return true;
        }
        return false;
    }
}