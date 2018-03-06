<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 06/03/2018
 * Time: 0:55
 */

require_once "ezpdf/Cezpdf.php";
require_once "includes_clases.php";

if(isset($_POST['imprimir_factura'])) {

    $cod_factura = $_POST['imprimir_factura'];

    $pdf = new Cezpdf("a3");

    ManejoBBDD::conectar_gestor();
    ManejoBBDD::preparar("SELECT * FROM facturas WHERE cod_factura = ?");
    ManejoBBDD::ejecutar(array($cod_factura));
    $factura = ManejoBBDD::getDatos();
    ManejoBBDD::preparar("SELECT * FROM lineas_facturas WHERE cod_factura = ?");
    ManejoBBDD::ejecutar(array($cod_factura));
    $lineas_factura = ManejoBBDD::getDatos();
    $pdf->selectFont('ezpdf/fonts/Helvetica.afm');
    $pdf->ezText("Factura #".$factura[0]['cod_factura']."\n\n", 25);

    $cliente = UsuarioBD::obtenerCliente($factura[0]['cod_cliente']);

    $tabla_factura[0] = array(
        "fecha" => $factura[0]['fecha'],
        "descuento_factura" => ($factura[0]['descuento_factura']*100)."%",
        "concepto" => $factura[0]['concepto'],
        "cliente" => $cliente[0]['nick']
    );

    $titulos_factura = array(
        "fecha" => "Fecha",
        "descuento_factura" => "Descuento",
        "concepto" => "Concepto",
        "cliente" => "Nick del cliente"
    );

    $tabla_lineas = array();
    $precio_total = 0;
    $precio_total_iva_desc = 0;
    foreach ($lineas_factura as $linea) {
        $tabla_lineas[] = array(
            "num_linea" => $linea['num_linea_factura'],
            "articulo" => ArticulosBD::obtenerArticulo($linea['cod_articulo'])[0]['nombre'],
            "gestor" => UsuarioBD::obtenerGestor($linea['cod_gestor'])[0]['nombre'],
            "precio" => $linea['precio']." €",
            "cantidad" => $linea['cantidad'],
            "descuento" => ($linea['descuento']*100)."%",
            "iva" => ($linea['iva']*100)."%",
            "precio_total" => $linea['precio'] - ($linea['precio']*$linea['descuento']) + ($linea['precio']*$linea['iva'])." €"
        );
        $precio_total += $linea['precio'];
        $precio_total_iva_desc += $linea['precio'] - ($linea['precio']*$linea['descuento']) + ($linea['precio']*$linea['iva']);
    }

    $titulos_lineas = array(
        "num_linea" => "Número de línea",
        "articulo" => "Artículo",
        "gestor" => "Gestor",
        "precio" => "Precio",
        "cantidad" => "Cantidad",
        "descuento" => "Descuento",
        "iva" => "IVA",
        "precio_total" => "Precio total"
    );

    $tabla_tot[0] = array(
        "precio_base" => $precio_total." €",
        "con_iva_desc" => $precio_total_iva_desc." €",
        "con_desc_fact" => $precio_total_iva_desc - ($precio_total_iva_desc*$factura[0]['descuento_factura'])." €"
    );

    $tabla_tot_tit = array(
        "precio_base" => "Precio base",
        "con_iva_desc" => "Aplicando IVA y Descuentos",
        "con_desc_fact" => "Aplicando descuento factura"
    );

    $pdf->ezTable($tabla_factura, $titulos_factura);
    $pdf->ezText("\n");
    $pdf->ezTable($tabla_lineas, $titulos_lineas);
    $pdf->ezText("\n");
    $options = array('fontSize' => 13);
    $pdf->ezTable($tabla_tot, $tabla_tot_tit,"",$options);

    ob_end_clean();
    $pdf->ezStream();
}