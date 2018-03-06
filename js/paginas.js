"use strict";

var cliente_pedido = "";
var modo_busqueda = "";
var campo_busqueda = "";
var en_albaran = [];
var en_factura = [];
var cliente_factura = "";

function cancelarModificadoCliente(e){
    var boton = $(e);
    $(boton).attr("class","borrar_cliente btn btn-danger");
    $(boton).html("Borrar");
    $(boton).siblings().attr("class","modificar_cliente btn btn-primary");
    botonNormal($(boton).siblings(),"Modificar");
    var elementos = $(boton).parent().siblings();
    var valor = "";
    elementos.each(function(i, elem){
        valor = $(elem).children().attr("name");
        $(elem).html(valor);
    });
}

function cancelarModificadoLinea(e){
    var boton = $(e);
    $(boton).attr("name","borrar_linea");
    $(boton).html("Borrar línea");
    $(boton).siblings().attr("name","modificar_linea");
    $(boton).siblings().removeClass("btn-success");
    $(boton).siblings().addClass("btn-primary");
    botonNormal($(boton).siblings(),"Modificar línea");
    var elementos = $(boton).parent().siblings();
    var valor = "";
    elementos.each(function(i,elem){
        valor = $(elem).children().attr("name");
        $(elem).html(valor);
    });
}

$(document).ready(function() {

    if ($("#accesos").length) {
        mostrarAccesos();
    }

    else if ($("#gestor_usuarios").length) {
        mostrarUsuarios();
        $("#solicitar").hide();
        $(document).on("click",".agregar_cliente",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            $("#solicitar").toggle(1000);
        });
        $("#gestor_usuarios").on("click",".modificar_cliente",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            cancelarModificadoCliente(".cancelar_modificado_cliente");
            var boton = $(this);
            var id = boton.attr("id");
            $(boton).attr("class","modificar_cliente_envio btn btn-success");
            $(boton).html("Guardar");
            $(boton).siblings().attr("class","cancelar_modificado_cliente btn btn-danger");
            $(boton).siblings().html("Cancelar");
            var elementos = $(boton).parent().siblings();
            var valor = "";
            elementos.each(function(i, elem){
                valor = $(elem).html();
                switch($(elem).attr("class")){
                    case "cif_dni":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_dni' class='dni requerido'>");
                        break;
                    case "razon_social":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_razon_social' class='requerido'>");
                        break;
                    case "domicilio_social":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_domicilio_social' class='requerido'>");
                        break;
                    case "ciudad":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_ciudad' class='requerido'>");
                        break;
                    case "telefono":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_telefono' class='telefono requerido'>");
                        break;
                    case "email":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_email' class='email requerido'>");
                        break;
                    case "nick":
                        $(elem).html("");
                        $(elem).append("<input type='text' size='10' value='"+valor+"' name='"+valor+"' id='"+id+"_nick' class='requerido'>");
                        break;
                }
            });
        });
        $("#gestor_usuarios").on("click",".cancelar_modificado_cliente",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            cancelarModificadoCliente(this);
        });
        $("#gestor_usuarios").on("click",".modificar_cliente_envio",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            var error = 0;
            $(this).parent().siblings().children('.requerido').each(function(i, elem){
                if($(elem).val() == ''){
                    $(elem).css({'border':'1px solid red'});
                    error++;
                }
                else{
                    $(elem).css("border","none");
                }
            });
            checkDNI();
            checkEmail();
            checkTelefono();
            if(error > 0 || mostrando_error_dni || mostrando_error_email || mostrando_error_telefono) {
                $('#er_1').html('<span>Debe rellenar los campos requeridos</span>');
            }
            else {
                var boton = $(this);
                var cod_cliente = boton.attr("id");
                var nick = $("input[type=text]#" + cod_cliente + "_nick").val();
                dni = $("input[type=text]#" + cod_cliente + "_dni").val();
                razon_social = $("input[type=text]#" + cod_cliente + "_razon_social").val();
                domicilio_social = $("input[type=text]#" + cod_cliente + "_domicilio_social").val();
                ciudad = $("input[type=text]#" + cod_cliente + "_ciudad").val();
                email = $("input[type=text]#" + cod_cliente + "_email").val();
                telefono = $("input[type=text]#" + cod_cliente + "_telefono").val();
                modificarCliente(cod_cliente, nick, boton);
            }
        });
        $("#gestor_usuarios").on("click",".borrar_cliente",function(){
            borrarCliente($(this).attr("id"));
        });
        $("#gestor_usuarios").on("click",".validar_solicitud",function(){
            validarSolicitud($(this).attr("id"));
        });
        $("#gestor_usuarios").on("click",".denegar_solicitud",function(){
            denegarSolicitud($(this).attr("id"));
        });
    }
    else if($("#realizar_pedidos").length){

        mostrarCarrito("#ver_carrito table");
        mostrarArticulos("#articulos");
        gestorPedidoCliente("#ver_carrito #cliente_pedido");

        modo_busqueda = $("#modo").val();
        campo_busqueda = $("#campo").val();

        $("#cliente_pedido").on("change","#selector_cliente",function(){
            cliente_pedido = $(this).val();
        });
        $("#buscador").on("change","#modo",function(){
            modo_busqueda = $(this).val();
        });
        $("#buscador").on("change","#campo",function(){
            campo_busqueda = $(this).val();
            if(campo_busqueda === "precio"){
                $("#modo").html("");
                $("#modo").append("<option value=\"menor\">Menor que</option>\n" +
                    "            <option value=\"mayor\">Mayor que</option>\n" +
                    "            <option value=\"igual\">Igual a</option>");
            }
            else{
                $("#modo").html("");
                $("#modo").append("<option value=\"empieza\">Empieza por</option>\n" +
                    "            <option value=\"contiene\">Contiene</option>\n" +
                    "            <option value=\"acaba\">Acaba por</option>");
            }
            modo_busqueda = $("#modo").val();
        });
        $("#ver_carrito").on("click","[name=procesar_carrito]",function(){
            procesaCarrito("[name=procesar_carrito]");
        });

        $("#articulos").on("click","[name=anyade_articulo]",function(){
            anyadeArticulo($(this).parent().attr("id"));
        });

        $("#ver_carrito").on("click","[name=restar_articulo]",function(){
            restarArticulo($(this).parent().parent().attr("id"),$(this));
        });

        $("#ver_carrito").on("click","[name=sumar_articulo]",function(){
            sumarArticulo($(this).parent().parent().attr("id"),$(this));
        });

        $("#ver_carrito").on("click","[name=quitar_articulo]",function(){
            quitarArticulo($(this).parent().parent().attr("id"),$(this));
        });

        $("#buscador").on("click","#boton_buscar",function(){
            $("#articulos").html("");
            mostrarArticulos("#articulos");
        });
    }
    else if($("#gestion_pedido").length){
        var clase = "";
        var num_linea = "";
        var codigo_pedido = "";
        mostrarPedidos("#pedidos");

        modo_busqueda = $("#modo").val();
        campo_busqueda = $("#campo").val();

        $("#buscador").on("change","#campo",function(){
            campo_busqueda = $(this).val();
            $("#texto_buscar").attr("type","text");
            if(campo_busqueda === "precio" || campo_busqueda === "fecha"){
                if(campo_busqueda === "fecha") $("#texto_buscar").attr("type","date");
                $("#modo").html("");
                $("#modo").append("<option value=\"menor\">Menor que</option>\n" +
                    "            <option value=\"mayor\">Mayor que</option>\n" +
                    "            <option value=\"igual\">Igual a</option>");
            }
            else{
                $("#modo").html("");
                $("#modo").append("<option value=\"empieza\">Empieza por</option>\n" +
                    "            <option value=\"contiene\">Contiene</option>\n" +
                    "            <option value=\"acaba\">Acaba por</option>");
            }
            modo_busqueda = $("#modo").val();
        });

        $("#buscador").on("change","#modo",function(){
            modo_busqueda = $(this).val();
        });

        $("#pedidos").on("click","[name=ver_pedido]",function(){
            codigo_pedido = $(this).attr("class").split(" ")[0];
            //oculta todos
            $("[name=tabla_secundaria]").each(function(i,elem){
                if(!$(elem).hasClass(codigo_pedido))
                    $(elem).hide();
            });
            $("[name=a_albaran].en_albaran").each(function(i,elem){
                $(elem).html("A albaran");
                $(elem).removeClass("en_albaran");
                $(elem).removeClass("disabled");
                $(elem).css({'cursor' : 'pointer'});
                en_albaran = [];
            });
            $("[name=procesar_albaran]").addClass("disabled");
            $("[name=procesar_albaran]").css({'cursor' : 'default'});
            //muestra u oculta el seleccionado
            $("[name=tabla_secundaria]."+codigo_pedido).toggle(300);
        });

        $("#pedidos").on("click","[name=borrar_pedido]",function(){
            codigo_pedido = $(this).attr("class").split(" ")[0];
            borrarPedido(codigo_pedido,$(this));
        });

        $("#pedidos").on("click","[name=borrar_linea]",function(){
            clase = $(this).parent().parent().attr("class").split(" ");
            codigo_pedido = clase[1];
            num_linea = clase[0];

            borrarLineaPedido(codigo_pedido,num_linea,$(this));
        });

        $("#pedidos").on("click","[name=sumar_articulo]",function(){
            clase = $(this).parent().parent().attr("class").split(" ");
            codigo_pedido = clase[1];
            num_linea = clase[0];
            sumarLineaPedido(codigo_pedido,num_linea,$(this));
        });

        $("#pedidos").on("click","[name=restar_articulo]",function(){
            clase = $(this).parent().parent().attr("class").split(" ");
            codigo_pedido = clase[1];
            num_linea = clase[0];
            restarLineaPedido(codigo_pedido,num_linea,$(this));
        });

        $("#buscador").on("click","#boton_buscar",function(){
            $("#pedidos tbody").html("");
            mostrarPedidos("#pedidos");
        });

        $("#pedidos").on("click","[name=a_albaran]",function(){
            if(!$(this).hasClass("en_albaran")) {
                $(this).html("Añadido");
                $(this).addClass("disabled");
                $(this).addClass("en_albaran");
                $(this).css({'cursor': 'default'});

                en_albaran.push($(this).attr("class").split(" ")[0]);

                $("[name=procesar_albaran]").removeClass("disabled");
                $("[name=procesar_albaran]").css({'cursor' : 'pointer'});
            }
        });

        $("#pedidos").on("mouseenter","[name=a_albaran].en_albaran",function(){
            $(this).html("Quitar");
            $(this).removeClass("disabled");
        });
        $("#pedidos").on("mouseout","[name=a_albaran].en_albaran",function(){
            $(this).html("Añadido");
            $(this).addClass("disabled");
            $(this).css({'cursor' : 'pointer'});
        });
        $("#pedidos").on("click","[name=a_albaran].en_albaran",function(){
            $(this).html("A albaran");
            $(this).removeClass("en_albaran");
            $(this).removeClass("disabled");
            $(this).css({'cursor' : 'pointer'});
            var indice = Number(en_albaran.indexOf($(this).attr("class").split(" ")[0]));
            var aux = [];
            en_albaran[indice] = null;
            for(var i = 0 ; i < en_albaran.length ; i++)
                if(en_albaran[i] !== null)
                    aux.push(en_albaran[i]);
            en_albaran = aux;
            if(en_albaran.length === 0){
                $("[name=procesar_albaran]").addClass("disabled");
                $("[name=procesar_albaran]").css({'cursor' : 'default'});
            }
        });
        $("#pedidos").on("click","[name=procesar_albaran]",function(){
            if(!$(this).hasClass("disabled")){
                procesaAlbaran($(this),$(this).attr("class").split(" ")[0]);
            }
        });
    }
    else if($("#gestion_albaranes").length){
        mostrarAlbaranes("#albaranes");
        $("#form_factura").hide();

        $("#boton_facturar").addClass("disabled");
        $("#boton_facturar").css({'cursor' : 'default'});

        mostrarBotonFactura("#form_factura");

        var codigo_albaran = "";

        modo_busqueda = $("#modo").val();
        campo_busqueda = $("#campo").val();

        $("#buscador").on("change","#campo",function(){
            campo_busqueda = $(this).val();
            $("#texto_buscar").attr("type","text");
            if(campo_busqueda === "precio" || campo_busqueda === "fecha"){
                if(campo_busqueda === "fecha") $("#texto_buscar").attr("type","date");
                $("#modo").html("");
                $("#modo").append("<option value=\"menor\">Menor que</option>\n" +
                    "            <option value=\"mayor\">Mayor que</option>\n" +
                    "            <option value=\"igual\">Igual a</option>");
            }
            else{
                $("#modo").html("");
                $("#modo").append("<option value=\"empieza\">Empieza por</option>\n" +
                    "            <option value=\"contiene\">Contiene</option>\n" +
                    "            <option value=\"acaba\">Acaba por</option>");
            }
            modo_busqueda = $("#modo").val();
        });

        $("#buscador").on("change","#modo",function(){
            modo_busqueda = $(this).val();
        });

        $("#albaranes").on("click","[name=ver_albaran]",function(){
            codigo_albaran = $(this).attr("class").split(" ")[0];
            //oculta todos
            $("[name=tabla_secundaria]").each(function(i,elem){
                if(!$(elem).hasClass(codigo_albaran))
                    $(elem).hide();
            });
            $("[name=tabla_secundaria]."+codigo_albaran).toggle(300);
        });

        $("#albaranes").on("click","[name=borrar_albaran]",function(){
            codigo_albaran = $(this).attr("class").split(" ")[0];
            borrarAlbaran(codigo_albaran,$(this));
        });

        $("#albaranes").on("click","[name=borrar_linea]",function(){
            clase = $(this).parent().parent().attr("class").split(" ");
            codigo_albaran = clase[1];
            num_linea = clase[0];
            borrarLineaAlbaran(codigo_albaran,num_linea,$(this));
        });

        $("#buscador").on("click","#boton_buscar",function(){
            $("#albaranes tbody").html("");
            mostrarAlbaranes("#albaranes");
        });

        $("#albaranes").on("click","[name=modificar_linea]",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            cancelarModificadoLinea("[name=cancelar_modificado_linea]");
            var boton = $(this);
            var id = boton.attr("class").split(" ")[0];
            $(boton).attr("name","modificar_linea_envio");
            $(boton).removeClass("btn-primary");
            $(boton).addClass("btn-success");
            $(boton).html("Guardar");
            $(boton).siblings().attr("name","cancelar_modificado_linea");
            $(boton).siblings().html("Cancelar");
            var elementos = $(boton).parent().siblings();
            var valor = "";
            elementos.each(function(i, elem){
                valor = $(elem).html();
                switch($(elem).attr("name")){
                    case "precio_linea":
                        $(elem).html("");
                        $(elem).append("<input type='number' size='10' min='1' value='"+valor+"' name='"+valor+"' id='"+id+"_precio' class='requerido'>");
                        break;
                    case "iva":
                        $(elem).html("");
                        $(elem).append("<input type='number' min='0' max='1' step='0.01' value='"+valor+"' name='"+valor+"' id='"+id+"_iva' class='requerido'>");
                        break;
                    case "descuento":
                        $(elem).html("");
                        $(elem).append("<input type='number' min='0' max='1' step='0.01' value='"+valor+"' name='"+valor+"' id='"+id+"_descuento' class='requerido'>");
                        break;
                }
            });
        });

        $("#albaranes").on("click","[name=cancelar_modificado_linea]",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            cancelarModificadoLinea(this);
        });
        $("#albaranes").on("click","[name=modificar_linea_envio]",function(){
            $("#er_1").html("");
            $("#er_2").html("");
            var error = 0;
            $(this).parent().siblings().children('.requerido').each(function(i, elem){
                if($(elem).val() == ''){
                    $(elem).css({'border':'1px solid red'});
                    error++;
                }
                else{
                    $(elem).css("border","none");
                }
            });
            if(error > 0) {
                $('#er_1').html('<span>Debe rellenar los campos requeridos</span>');
            }
            else {
                var boton = $(this);
                var cod_albaran = boton.parent().parent().attr("class").split(" ")[1];
                var num_linea = boton.attr("class").split(" ")[0];
                var precio_linea = $("input[type=number]#" + num_linea + "_precio").val();
                var iva = $("input[type=number]#" + num_linea + "_iva").val();
                var descuento = $("input[type=number]#" + num_linea + "_descuento").val();
                modificarLineaAlbaran(cod_albaran,num_linea,precio_linea,iva,descuento,boton);
            }
        });

        $("#albaranes").on("click","[name=a_factura]",function(){
            if(!$(this).hasClass("en_factura")) {
                if (en_factura.length === 0) {
                    en_factura.push($(this).attr("class").split(" ")[0]);
                    cliente_factura = $(this).attr("class").split(" ")[1];

                    $(this).html("<span>Añadido</span>");
                    $(this).addClass("en_factura");
                    $(this).css({'cursor': 'default'});
                    $(this).addClass("disabled");

                    $("#boton_facturar").removeClass("disabled");
                    $("#boton_facturar").css({'cursor': 'pointer'});
                }
                else {
                    var clase = $(this).attr("class").split(" ");
                    if (clase[1] !== cliente_factura) {
                        $("[name=a_factura].en_factura").each(function (i, elem) {
                            $(elem).html("<span>A factura</span>");
                            $(elem).removeClass("en_factura");
                            $(elem).css({'cursor': 'pointer'});
                            $(elem).removeClass("disabled");
                        });
                        en_factura = [];
                    }
                    $(this).html("<span>Añadido</span>");
                    $(this).addClass("en_factura");
                    $(this).css({'cursor': 'default'});
                    $(this).addClass("disabled");
                    cliente_factura = clase[1];
                    en_factura.push(clase[0]);
                }
            }
        });

        $("#albaranes").on("mouseenter","[name=a_factura].en_factura",function(){
            $(this).html("<span>Quitar</span>");
            $(this).removeClass("disabled");
        });
        $("#albaranes").on("mouseout","[name=a_factura].en_factura",function(){
            $(this).html("<span>Añadido</span>");
            $(this).addClass("disabled");
            $(this).css({'cursor' : 'pointer'});
        });

        $("#albaranes").on("click","[name=a_factura].en_factura",function(){
            $(this).html("A factura");
            $(this).removeClass("en_factura");
            $(this).removeClass("disabled");
            $(this).css({'cursor' : 'pointer'});
            var indice = Number(en_factura.indexOf($(this).attr("class").split(" ")[0]));
            var aux = [];
            en_factura[indice] = null;
            for(var i = 0 ; i < en_factura.length ; i++)
                if(en_factura[i] !== null)
                    aux.push(en_factura[i]);
            en_factura = aux;
            if(en_factura.length === 0){
                cliente_factura = "";
                $("#boton_facturar").addClass("disabled");
                $("#boton_facturar").css({'cursor' : 'default'});
            }
        });

        $("#form_factura").on("click","#boton_facturar",function(){
            if(!$(this).hasClass("disabled")){
                procesaFactura($(this));
            }
        });

    }

    else if($("#gestion_facturas").length){
        mostrarFacturas("#facturas");

        var codigo_factura = "";
        var codigo_albaran_factura = "";

        modo_busqueda = $("#modo").val();
        campo_busqueda = $("#campo").val();

        $("#buscador").on("change","#campo",function(){
            campo_busqueda = $(this).val();
            $("#texto_buscar").attr("type","text");
            if(campo_busqueda === "precio" || campo_busqueda === "fecha"){
                if(campo_busqueda === "fecha") $("#texto_buscar").attr("type","date");
                $("#modo").html("");
                $("#modo").append("<option value=\"menor\">Menor que</option>\n" +
                    "            <option value=\"mayor\">Mayor que</option>\n" +
                    "            <option value=\"igual\">Igual a</option>");
            }
            else{
                $("#modo").html("");
                $("#modo").append("<option value=\"empieza\">Empieza por</option>\n" +
                    "            <option value=\"contiene\">Contiene</option>\n" +
                    "            <option value=\"acaba\">Acaba por</option>");
            }
            modo_busqueda = $("#modo").val();
        });

        $("#buscador").on("change","#modo",function(){
            modo_busqueda = $(this).val();
        });

        $("#facturas").on("click","[name=ver_factura]",function(){
            codigo_factura = $(this).attr("class").split(" ")[0];
            //oculta todos
            $("[name=tabla_secundaria]").each(function(i,elem){
                if(!$(elem).hasClass(codigo_factura))
                    $(elem).hide();
            });
            $("[name=tabla_secundaria]."+codigo_factura).toggle(300);
        });

        $("#facturas").on("click","[name=borrar_factura]",function(){
            codigo_factura = $(this).attr("class").split(" ")[0];
            borrarFactura(codigo_factura,$(this));
        });

        $("#buscador").on("click","#boton_buscar",function(){
            $("#facturas tbody").html("");
            mostrarFacturas("#facturas");
        });

        $("#facturas").on("click","[name=desfacturar_albaran]",function(){
            codigo_albaran_factura = $(this).attr("class").split(" ")[0];
            desfacturarAlbaran(codigo_albaran_factura,$(this));
        });

        $("#facturas").on("click","td[name=descuento_factura]",function(){
            if(!$(this).hasClass("modificando")) {
                var descuento_factura = $(this).html();
                $(this).addClass("modificando");
                $(this).html("");
                $(this).append("<input type='number' min='0' max='1' step='0.01' name='" + descuento_factura + "' value='" + descuento_factura + "'>");
            }
        });

        $(document).mouseup(function(e)
        {
            var container = $("td[name=descuento_factura].modificando");

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0)
            {
                var valor = container.children("input").val();
                modificarDescuentoFactura(container,valor);
                container.removeClass("modificando");
            }
        });

    }
});