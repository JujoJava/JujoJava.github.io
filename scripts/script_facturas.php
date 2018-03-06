<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 05/03/2018
 * Time: 10:28
 */

require_once "includes_clases.php";

$param = array();
$param['correcto'] = false;

if(isset($_POST['modo'])){
    switch($_POST['modo']){
        case "procesa_factura":
            $cod_cliente = $_POST['cod_cliente'];
            $descuento_factura = $_POST['descuento_factura'];
            $albaranes = json_decode($_POST['albaranes']);
            $param['correcto'] = FacturasBD::procesoAlbaranesFactura($albaranes,$cod_cliente,NULL,$descuento_factura);
            break;
        case "borrar":
            $param['correcto'] = FacturasBD::borrarFactura($_POST['cod_factura']);
            break;
        case "desfacturar":
            $param['correcto'] = FacturasBD::desfacturarAlbaran($_POST['cod_albaran']);
            $param['lineas'] = FacturasBD::tieneLineas($_POST['cod_factura']);
            break;
        case "modificar":
            $valor = $_POST['descuento_factura'];
            if($valor >= 0 && $valor <= 1){
                FacturasBD::modificarDescuentoFactura($_POST['cod_factura'],$valor);
                $param['descuento_nuevo'] = $valor;
            }
            else{
                $param['descuento_nuevo'] = FacturasBD::obtenerFactura($_POST['cod_factura'])[0]['descuento_factura'];
            }
            break;
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($param,JSON_FORCE_OBJECT);
}

?>