<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 27/02/2018
 * Time: 11:27
 */

class Articulo {

    private $cod_articulo;
    private $precio;
    private $cantidad;
    public function __construct($cod_articulo){
        $this->cod_articulo = $cod_articulo;
        $this->precio = ArticulosBD::obtenerArticulo($cod_articulo)[0]['precio'];
        $this->cantidad = 1;
    }
    public function getCodArticulo(){
        return $this->cod_articulo;
    }
    public function getPrecio(){
        return $this->precio;
    }
    public function getPrecioTotal(){
        return ($this->precio * $this->cantidad);
    }
    public function getCantidad(){
        return $this->cantidad;
    }
    public function sumaCantidad(){
        $this->cantidad++;
    }
    public function restaCantidad(){
        $this->cantidad--;
    }

}