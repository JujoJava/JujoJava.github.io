<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 12/02/2018
 * Time: 9:29
 */

abstract class Usuario {
    protected $cod;
    protected $nick;
    protected $carrito;
    public function __construct($cod,$nick){
        $this->cod = $cod;
        $this->nick = $nick;
        $this->carrito = array();
    }
    public function getCod(){
        return $this->cod;
    }
    public function getNick(){
        return $this->nick;
    }
    public function getCarrito(){
        return $this->carrito;
    }
    public function getArticulo($cod_articulo){
        foreach($this->carrito as $articulo){
            if($articulo instanceof Articulo){
                if($articulo->getCodArticulo() == $cod_articulo){
                    return $articulo;
                }
            }
        }
        return false;
    }
    public function addArticulo($cod_articulo){
        $articulo = $this->getArticulo($cod_articulo);
        if($articulo instanceof Articulo){
            $articulo->sumaCantidad();
        }
        else $this->carrito[] = new Articulo($cod_articulo);
    }
    public function delArticulo($cod_articulo){
        $nuevo = array();
        foreach($this->carrito as $articulo){
            if($articulo instanceof Articulo){
                if($articulo->getCodArticulo() != $cod_articulo){
                    $nuevo[] = $articulo;
                }
            }
        }
        $this->carrito = $nuevo;
    }
}