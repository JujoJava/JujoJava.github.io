<?php
/**
 * Created by PhpStorm.
 * User: Juanjo
 * Date: 23/02/2018
 * Time: 14:04
 */

require_once "includes_clases.php";

$datos = null;
$elementos = array();

session_start();

if(isset($_POST['modo'])){
    switch($_POST['modo']){
        case "accesos":
            $datos = AccesosBD::obtieneAccesos();
            foreach($datos as $i => $dato){
                $elementos[$i]['id_acceso'] = $dato['id_acceso'];
                $elementos[$i]['fecha_hora_acceso'] = $dato['fecha_hora_acceso'];
                if($dato['fecha_hora_salida'] == null) $elementos[$i]['fecha_hora_salida'] = "";
                else $elementos[$i]['fecha_hora_salida'] = $dato['fecha_hora_salida'];
                $elementos[$i]['gestor_accede'] = UsuarioBD::obtenerGestor($dato['cod_gestor'])[0]['nombre'];
            }
            break;
        case "usuarios":
            $contador = 0;
            $datos = UsuarioBD::obtenerAllClientes();
            foreach($datos as $i => $dato){
                if($dato['activo'] == 'SI'){
                    $cod = $dato['cod_cliente'];
                    $elementos[$contador]['row'] = "<tr>";
                    $elementos[$contador]['cif_dni'] = $dato['cif_dni'];
                    $elementos[$contador]['razon_social'] = $dato['razon_social'];
                    $elementos[$contador]['domicilio_social'] = $dato['domicilio_social'];
                    $elementos[$contador]['ciudad'] = $dato['ciudad'];
                    $elementos[$contador]['email'] = $dato['email'];
                    $elementos[$contador]['telefono'] = $dato['telefono'];
                    $elementos[$contador]['nick'] = $dato['nick'];
                    $elementos[$contador]['botones'] = "<button type='button' id=$cod class='modificar_cliente btn btn-primary'><span>Modificar</span></button>
                                            <button type='button' id=$cod class='borrar_cliente btn btn-danger'><span>Borrar</span></button>";
                    $contador++;
                }
            }
            $datos = UsuarioBD::obtenerAllSolicitudes();
            foreach($datos as $i => $dato){
                $id = $dato['id_solicitud'];
                $elementos[$contador]['row'] = "<tr class='table-warning'>";
                $elementos[$contador]['cif_dni'] = $dato['cif_dni'];
                $elementos[$contador]['razon_social'] = $dato['razon_social'];
                $elementos[$contador]['domicilio_social'] = $dato['domicilio_social'];
                $elementos[$contador]['ciudad'] = $dato['ciudad'];
                $elementos[$contador]['email'] = $dato['email'];
                $elementos[$contador]['telefono'] = $dato['telefono'];
                $elementos[$contador]['nick'] = $dato['nick'];
                $elementos[$contador]['botones'] = "<button type='button' id=$id class='validar_solicitud btn btn-success'><span>Validar</span></button>
                                            <button type='button' id=$id class='denegar_solicitud btn btn-danger'><span>Denegar</span></button>";
                $contador++;
            }
            break;
        case "articulos":
            if(empty($_POST['texto_busqueda'])){
                $datos = ArticulosBD::obtenerArticulos();
            }
            else{
                $texto_busqueda = $_POST['texto_busqueda'];
                $modo_busqueda = $_POST['modo_busqueda'];
                $campo_busqueda = $_POST['campo_busqueda'];
                $datos = ArticulosBD::obtenerArticulosBusqueda($texto_busqueda,$modo_busqueda,$campo_busqueda);
            }
            $contador = 0;
            foreach($datos as $i => $dato){
                $cod = $dato['cod_articulo'];
                $elementos[$contador] = "<div class='articulo' id=$cod>";
                $elementos[$contador] .= "<span class='neg cod'>$cod</span>";
                $elementos[$contador] .= "<div><span class='neg'>".$dato['nombre']."</span><span>".$dato['descripcion']."</span></div>";
                $elementos[$contador] .= "<div><span>Precio: ".$dato['precio']."</span><span>Descuento: ".$dato['descuento']."</span></div>";
                $elementos[$contador] .= "<button type='button' name='anyade_articulo' class='btn btn-primary'>Añadir al carrito</button></div>";
                $contador++;
            }
            break;
        case "carrito":
            if($_SESSION['login'] instanceof Usuario){
                $usuario = $_SESSION['login'];
                $carrito = $usuario->getCarrito();
                $contador = 0;
                foreach($carrito as $articulo){
                    if($articulo instanceof Articulo) {
                        $cod = $articulo->getCodArticulo();
                        $precio = $articulo->getPrecioTotal();
                        $cantidad = $articulo->getCantidad();
                        $elementos[$contador] = "<tr id=$cod>";
                        $elementos[$contador] .= "<td>$cod</td>";
                        $elementos[$contador] .= "<td>$precio</td>";
                        $elementos[$contador] .= "<td><button type='button' name='restar_articulo' class='btn btn-link'>-</button>";
                        $elementos[$contador] .= "<span>$cantidad</span>";
                        $elementos[$contador] .= "<button type='button' name='sumar_articulo' class='btn btn-link'>+</button></td>";
                        $elementos[$contador] .= "<td><button type='button' name='quitar_articulo' class='btn btn-danger'>Quitar</button></td></tr>";
                        $contador++;
                    }
                }
            }
            break;
        case "clientes_carrito":
            if($_SESSION['login'] instanceof Gestor){
                $clientes = UsuarioBD::obtenerAllClientesActivos();
                $contador = 0;
                foreach($clientes as $cliente){
                    $cod = $cliente['cod_cliente'];
                    $nick = $cliente['nick'];
                    $elementos[$contador] = "<option value=$cod>$nick</option>";
                    $contador++;
                }
            }
            break;
        case "pedidos":
            if(empty($_POST['texto_busqueda'])){
                $pedidos = PedidosBD::obtenerPedidos();
            }
            else{
                $texto_busqueda = $_POST['texto_busqueda'];
                $modo_busqueda = $_POST['modo_busqueda'];
                $campo_busqueda = $_POST['campo_busqueda'];
                $pedidos = PedidosBD::obtenerPedidosBusqueda($texto_busqueda,$modo_busqueda,$campo_busqueda);
            }
            $contador = 0;
            foreach($pedidos as $pedido){
                $cod = $pedido['cod_pedido'];
                $fecha = $pedido['fecha'];
                $nick_cliente = UsuarioBD::obtenerCliente($pedido['cod_cliente'])[0]['nick'];
                $lineas = PedidosBD::obtenerLineas($cod);
                $precio_total = 0;
                $en_albaran = false;
                foreach($lineas as $linea){
                    $precio_total += $linea['precio'];
                    if($en_albaran == false)
                        $en_albaran = AlbaranesBD::existeLineaPedido($cod,$linea['num_linea_pedido']);
                }
                $elementos[$contador] = "<tr class=$cod name='tabla_principal' ><td>$cod</td><td>$fecha</td><td>$nick_cliente</td><td class='precio_total' >$precio_total</td>";
                $elementos[$contador] .= "<td><button type='button' class='$cod btn btn-primary' name='ver_pedido'><span>Ver</span></button>";
                if(!$en_albaran)
                    $elementos[$contador] .= "<button type='button' class='$cod btn btn-danger' name='borrar_pedido'><span>Borrar</span></button>";
                $elementos[$contador] .= "</td></tr>";
                $elementos[$contador] .= "<tr class=$cod name='tabla_secundaria' ><td colspan='5'><table class='table table-hover'>
                                            <thead>
                                                <th scope='col'>Artículo</th>
                                                <th scope='col'>Gestor</th>
                                                <th scope='col'>Precio</th>
                                                <th scope='col'>Cantidad</th>
                                                <th scope='col'>Acciones</th>
                                            </thead><tbody>";
                $hay_lineas_libres = false;
                foreach($lineas as $linea){
                    $articulo = ArticulosBD::obtenerArticulo($linea['cod_articulo'])[0]['nombre'];
                    if($linea['cod_gestor'] != null)
                        $gestor = UsuarioBD::obtenerGestor($linea['cod_gestor'])[0]['nombre'];
                    else
                        $gestor = "";
                    $precio = $linea['precio'];
                    $cantidad = $linea['cantidad'];
                    $num = $linea['num_linea_pedido'];
                    $en_albaran = AlbaranesBD::existeLineaPedido($cod,$num);
                    if(!$en_albaran) $hay_lineas_libres = true;

                    if($en_albaran)
                        $elementos[$contador] .= "<tr class='$num $cod table table-warning'><td>$articulo</td><td>$gestor</td><td>$precio</td><td>";
                    else
                        $elementos[$contador] .= "<tr class='$num $cod'><td>$articulo</td><td name='gestor'>$gestor</td><td name='precio_linea'>$precio</td><td>";

                    if(!$en_albaran)
                        $elementos[$contador] .= "<button type='button' name='restar_articulo' class='btn btn-link'>-</button>";
                    $elementos[$contador] .= "<span>$cantidad</span>";
                    if(!$en_albaran)
                        $elementos[$contador] .= "<button type='button' name='sumar_articulo' class='btn btn-link'>+</button>";
                    $elementos[$contador] .= "</td><td>";
                    if(!$en_albaran) {
                        if($_SESSION['login'] instanceof Gestor)
                            $elementos[$contador] .= "<button type='button' class='$num btn btn-warning' name='a_albaran'><span>A albarán</span></button>";
                        $elementos[$contador] .= "<button type='button' class='$num btn btn-danger' name='borrar_linea'><span>Borrar línea</span></button>";
                    }
                        $elementos[$contador] .= "</td></tr>";
                }
                if($hay_lineas_libres && $_SESSION['login'] instanceof Gestor)
                    $elementos[$contador] .= "<tr><td colspan='5'><button type='button' class='$cod btn btn-secondary' name='procesar_albaran'><span>Procesar albarán</span></button></td></tr>";
                $elementos[$contador] .= "</tbody></table></td></tr>";
                $contador++;
            }
            break;
        case "albaranes":
            if(empty($_POST['texto_busqueda'])){
                $albaranes = AlbaranesBD::obtenerAlbaranes();
            }
            else{
                $texto_busqueda = $_POST['texto_busqueda'];
                $modo_busqueda = $_POST['modo_busqueda'];
                $campo_busqueda = $_POST['campo_busqueda'];
                $albaranes = AlbaranesBD::obtenerAlbaranesBusqueda($texto_busqueda,$modo_busqueda,$campo_busqueda);
            }
            $contador = 0;
            foreach($albaranes as $albaran){
                $cod = $albaran['cod_albaran'];
                $fecha = $albaran['fecha'];
                $concepto = $albaran['concepto'];
                $cod_cliente = $albaran['cod_cliente'];
                $nick_cliente = UsuarioBD::obtenerCliente($cod_cliente)[0]['nick'];
                $lineas = AlbaranesBD::obtenerLineas($cod);
                $en_factura = FacturasBD::existeAlbaran($cod);
                $precio_total = 0;
                foreach($lineas as $linea){
                    $precio_total += $linea['precio'];
                }
                if(!$en_factura)
                    $elementos[$contador] = "<tr class=$cod name='tabla_principal' ><td>$cod</td><td>$fecha</td><td>$concepto</td><td>$nick_cliente</td><td class='precio_total' >$precio_total</td>";
                else
                    $elementos[$contador] = "<tr class='$cod table-warning' name='tabla_principal'><td>$cod</td><td>$fecha</td><td>$concepto</td><td>$nick_cliente</td><td class='precio_total' >$precio_total</td>";
                $elementos[$contador] .= "<td><button type='button' class='$cod btn btn-primary' name='ver_albaran'><span>Ver</span></button>";
                if(!$en_factura && $_SESSION['login'] instanceof Gestor) {
                    $elementos[$contador] .= "<button type='button' class='$cod $cod_cliente btn btn-success' name='a_factura'><span>A factura</span></button>";
                    $elementos[$contador] .= "<button type='button' class='$cod btn btn-danger' name='borrar_albaran'><span>Borrar</span></button>";
                }
                $elementos[$contador] .= "</td></tr>";
                $elementos[$contador] .= "<tr class=$cod name='tabla_secundaria' ><td colspan='6'><table class='table table-hover'>
                                            <thead>
                                                <th scope='col'>Artículo</th>
                                                <th scope='col'>Gestor</th>
                                                <th scope='col'>Precio</th>
                                                <th scope='col'>Cantidad</th>
                                                <th scope='col'>Descuento</th>
                                                <th scope='col'>IVA</th>
                                                <th scope='col'>Acciones</th>
                                            </thead><tbody>";
                foreach($lineas as $linea){
                    $articulo = ArticulosBD::obtenerArticulo($linea['cod_articulo'])[0]['nombre'];
                    if($linea['cod_gestor'] != NULL)
                        $gestor = UsuarioBD::obtenerGestor($linea['cod_gestor'])[0]['nombre'];
                    else $gestor = "";
                    $precio = $linea['precio'];
                    $cantidad = $linea['cantidad'];
                    $iva = $linea['iva'];
                    $descuento = $linea['descuento'];
                    $num = $linea['num_linea_albaran'];
                    $elementos[$contador] .= "<tr class='$num $cod'><td>$articulo</td><td name='gestor'>$gestor</td><td name='precio_linea'>$precio</td><td>$cantidad</td><td name='descuento'>$descuento</td><td name='iva'>$iva</td>";
                    if(!$en_factura && $_SESSION['login'] instanceof Gestor) {
                        $elementos[$contador] .= "<td><button type='button' class='$num btn btn-primary' name='modificar_linea'><span>Modificar línea</span></button>";
                        $elementos[$contador] .= "<button type='button' class='$num btn btn-danger' name='borrar_linea'><span>Borrar línea</span></button>";
                        $elementos[$contador] .= "</td>";
                    }
                    else $elementos[$contador] .= "<td></td>";
                    $elementos[$contador] .= "</tr>";
                }
                $elementos[$contador] .= "</tbody></table></td></tr>";
                $contador++;
            }
            break;
        case "facturas":
            if(empty($_POST['texto_busqueda'])){
                $facturas = FacturasBD::obtenerFacturas();
            }
            else{
                $texto_busqueda = $_POST['texto_busqueda'];
                $modo_busqueda = $_POST['modo_busqueda'];
                $campo_busqueda = $_POST['campo_busqueda'];
                $facturas = FacturasBD::obtenerFacturasBusqueda($texto_busqueda,$modo_busqueda,$campo_busqueda);
            }
            $contador = 0;
            foreach($facturas as $factura){
                $cod = $factura['cod_factura'];
                $fecha = $factura['fecha'];
                $concepto = $factura['concepto'];
                $descuento_factura = $factura['descuento_factura'];
                $cod_cliente = $factura['cod_cliente'];
                $nick_cliente = UsuarioBD::obtenerCliente($cod_cliente)[0]['nick'];
                $lineas = FacturasBD::obtenerLineas($cod);
                $precio_total = 0;
                foreach($lineas as $linea){
                    $precio_total += $linea['precio'];
                }
                $elementos[$contador] = "<tr class=$cod name='tabla_principal' ><td>$cod</td><td>$fecha</td><td>$concepto</td>";
                if($_SESSION['login'] instanceof Gestor)
                    $elementos[$contador] .= "<td name='descuento_factura'>$descuento_factura</td>";
                else
                    $elementos[$contador] .= "<td>$descuento_factura</td>";
                $elementos[$contador] .= "<td>$nick_cliente</td><td class='precio_total' >$precio_total</td>";
                $elementos[$contador] .= "<td><form action='scripts/imprimir_factura.php' target='_blank' method='post'><button type='button' class='$cod btn btn-primary' name='ver_factura'><span>Ver</span></button>";
                $elementos[$contador] .= "<button type='submit' class='$cod btn btn-success' name='imprimir_factura' value='$cod'><span>Imprimir</span></button>";
                if($_SESSION['login'] instanceof Gestor)
                    $elementos[$contador] .= "<button type='button' class='$cod btn btn-danger' name='borrar_factura'><span>Borrar</span></button>";
                $elementos[$contador] .= "</form></td></tr>";
                $elementos[$contador] .= "<tr class=$cod name='tabla_secundaria' ><td colspan='7'><table class='table'>
                                            <thead>
                                                <th scope='col'>Artículo</th>
                                                <th scope='col'>Gestor</th>
                                                <th scope='col'>Precio</th>
                                                <th scope='col'>Cantidad</th>
                                                <th scope='col'>Descuento</th>
                                                <th scope='col'>IVA</th>
                                                <th scope='col'>Acciones</th>
                                            </thead><tbody>";
                $cod_albaran = "";
                foreach($lineas as $linea){
                    if($cod_albaran != $linea['cod_albaran']) {
                        $cod_albaran = $linea['cod_albaran'];
                        $rowspan = FacturasBD::cantidadLineasAlbaran($cod_albaran)[0]['resul'];
                    }
                    else $rowspan = 0;
                    $articulo = ArticulosBD::obtenerArticulo($linea['cod_articulo'])[0]['nombre'];
                    if($linea['cod_gestor'] != null)
                        $gestor = UsuarioBD::obtenerGestor($linea['cod_gestor'])[0]['nombre'];
                    else
                        $gestor = "";
                    $precio = $linea['precio'];
                    $cantidad = $linea['cantidad'];
                    $iva = $linea['iva'];
                    $descuento = $linea['descuento'];
                    $num = $linea['num_linea_factura'];
                    $elementos[$contador] .= "<tr class='$num $cod $cod_albaran'><td>$articulo</td><td name='gestor'>$gestor</td><td name='precio_linea'>$precio</td><td>$cantidad</td><td name='descuento'>$descuento</td><td name='iva'>$iva</td>";
                    if($rowspan > 0 && $_SESSION['login'] instanceof Gestor)
                        $elementos[$contador] .= "<td class='columna-centrar' rowspan='$rowspan'><button type='button' class='$cod_albaran btn btn-warning' name='desfacturar_albaran'><span>Desfacturar albarán</span></button></td>";
                    $elementos[$contador] .= "</tr>";
                }
                $elementos[$contador] .= "</tbody></table></td></tr>";
                $contador++;
            }
            break;
    }
}

header("Content-type: application/json; charset=utf-8");
echo json_encode($elementos);

?>