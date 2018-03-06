"use strict";

    function botonCargando(elem) {
        $(elem).html("<img width='25' height='25' src='img/ajax_loading.gif' /><span>Cargando...</span>");
        $(elem).addClass("disabled");
        $(elem).css({
            cursor: "default",
            display: "flex"
        });
        $(elem).children("span").css("padding-left", "8px");
    }

    function botonRueda(elem) {
        $(elem).html("<img width='25' height='25' src='img/ajax_loading.gif' />");
        $(elem).addClass("disabled");
        $(elem).css({
            cursor: "default"
        });
        $(elem).children("span").css("padding-left", "8px");
    }

    function botonNormal(elem,texto) {
        $(elem).html("<span>"+texto+"</span>");
        $(elem).css({
            cursor: "pointer",
            display: "inline"
        });
        $(elem).removeClass("disabled");
        $(elem).children("span").css("padding-left", "0");
    }

    function comprobarLogin(nombre, password, tipo) {
        $.ajax({
            data: {'check_nom': nombre, 'check_pass': password, 'tipo_login': tipo},
            type: "POST",
            dataType: "json",
            url: "scripts/script_logueo.php",
            success: function (data) {
                botonNormal("#boton_envio","Login");
                if (data.texto_error) {
                    if (!data.correcto) {
                        $("#er_1").html("<span>"+data.texto_error+"</span>");
                    }
                    else {
                        window.location = "index.php";
                    }
                }
                else {
                    $("#er_1").html("<span>Ha habido un error con la base de datos</span>");
                }
            },
            beforeSend: function () {
                botonCargando("#boton_envio");
            }
        });
    }

    function registrarUsuario(nombre,password){
        $.ajax({
            data: {
                'sol_nom':nombre,
                'sol_pass':password,
                'sol_dni':dni,
                'sol_razon_social':razon_social,
                'sol_domicilio_social':domicilio_social,
                'sol_ciudad':ciudad,
                'sol_telefono':telefono,
                'sol_email':email
            },
            type: "POST",
            dataType: "json",
            url: "scripts/script_solicitud.php",
            success: function(data){
                botonNormal("#boton_envio","Enviar solicitud de registro");
                if(data.texto){
                    if(!data.correcto){
                        $("#er_1").html("<span>"+data.texto+"</span>");
                    }
                    else{
                        $("#ex_1").html("<span>"+data.texto+"</span>");
                    }
                }
            },
            beforeSend: function(){
                botonCargando("#boton_envio");
            }
        });
    }

    function mostrarAccesos(){
        $.ajax({
            data: {'modo' : 'accesos'},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data) {
                $("#accesos").children("tbody").html("");
                if(data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        console.log("HOLA");
                        $("#accesos").children("tbody").append("<tr><td>"+data[i]['id_acceso']+"</td>" +
                            "<td>"+data[i]['fecha_hora_acceso']+"</td><td>"+data[i]['fecha_hora_salida']+"</td>" +
                            "<td>"+data[i]['gestor_accede']+"</td></tr>");
                    }
                }
                else{
                    $("#accesos").children("tbody").append("<tr><td colspan='4'>No hay ningún registro</td></tr>");
                }
            },
            beforeSend: function(){
                $("#accesos").children("tbody").append("<tr><td colspan='4'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function mostrarUsuarios(){
        $.ajax({
            data: {'modo' : 'usuarios'},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                $("#gestor_usuarios").children("tbody").html("");
                if(data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        $("#gestor_usuarios").children("tbody").append(data[i]['row']+"<td class='cif_dni'>" + data[i]['cif_dni'] + "</td>" +
                            "<td class='razon_social'>" + data[i]['razon_social'] + "</td><td class='domicilio_social'>" + data[i]['domicilio_social'] + "</td>" +
                            "<td class='ciudad'>" + data[i]['ciudad'] + "</td><td class='email'>" + data[i]['email'] + "</td><td class='telefono'>" + data[i]['telefono'] + "</td>" +
                            "<td class='nick'>" + data[i]['nick'] + "</td><td>" + data[i]['botones'] + "</td></tr>");
                    }
                }
                else{
                    $("#gestor_usuarios").children("tbody").append("<tr><td colspan='8'>No hay ningún usuario</td></tr>");
                }
            },
            beforeSend: function(){
                $("#gestor_usuarios").children("tbody").append("<tr><td colspan='8'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function registrarCliente(nombre,password){
        $.ajax({
            data: {
                'sol_nom':nombre,
                'sol_pass':password,
                'sol_dni':dni,
                'sol_razon_social':razon_social,
                'sol_domicilio_social':domicilio_social,
                'sol_ciudad':ciudad,
                'sol_telefono':telefono,
                'sol_email':email
            },
            type: "POST",
            dataType: "json",
            url: "scripts/script_registro.php",
            success: function(data){
                botonNormal("#boton_envio","Registrar");
                if(data.texto){
                    if(!data.correcto){
                        $("#er_1").html("<span>"+data.texto+"</span>");
                    }
                    else{
                        $("#ex_1").html("<span>"+data.texto+"</span>");
                        //limpiamos el formulario
                        $("#solicitar form")[0].reset();
                        //ocultamos nombre y contraseña para dejar los campos de datos personales abiertos
                        $("#datos_logueo").hide(1000,function(){
                            $("#continuar_solicitud").show(300);
                            $("#solicitud_datos_personales input[type=text]").each(function(i,elem){
                                $(elem).prop("disabled",false);
                            });
                        });
                        //ocultamos la ventana de registro
                        $("#solicitar").hide(300);
                        //mostramos los usuarios con los nuevos cambios
                        mostrarUsuarios();
                    }
                }
            },
            beforeSend: function(){
                botonCargando("#boton_envio");
            }
        });
    }

    function modificarCliente(cod_cliente,nick,boton){
        $.ajax({
            data: {
                'cod_cliente':cod_cliente,
                'mod_nick':nick,
                'mod_dni':dni,
                'mod_razon_social':razon_social,
                'mod_domicilio_social':domicilio_social,
                'mod_ciudad':ciudad,
                'mod_telefono':telefono,
                'mod_email':email
            },
            type: "POST",
            dataType: "json",
            url: "scripts/script_modificar_cliente.php",
            success: function(data){
                var elementos = "";
                var valor = "";
                $(boton).attr("class", "modificar_cliente btn btn-primary");
                botonNormal($(boton),"Modificar");
                $(boton).siblings().attr("class", "borrar_cliente btn btn-danger");
                $(boton).siblings().html("Borrar");
                if(!data.correcto) {
                    elementos = $(boton).parent().siblings();
                    valor = "";
                    elementos.each(function(i, elem){
                        valor = $(elem).children().attr("name");
                        $(elem).html(valor);
                    });
                    $("#er_1").html("<span>"+data.texto+"</span>");
                }
                else{
                    elementos = $(boton).parent().siblings();
                    valor = "";
                    elementos.each(function (i, elem) {
                        valor = $(elem).children().val();
                        $(elem).html(valor);
                    });
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function validarSolicitud(idSolicitud){
        var boton = "#"+idSolicitud+".validar_solicitud";
        $.ajax({
            data: {'id_solicitud' : idSolicitud},
            type: "POST",
            dataType: "json",
            url: "scripts/script_valida_solicitud.php",
            success: function(data){
                botonNormal(boton,"Validar");
                if(data.correcto === true)
                    mostrarUsuarios();
                else
                    $("#er_1").html("<span>No se ha podido validar la solicitud</span>");
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function denegarSolicitud(idSolicitud){
        var boton = "#"+idSolicitud+".denegar_solicitud";
        $.ajax({
            data: {'id_solicitud' : idSolicitud},
            type: "POST",
            dataType: "json",
            url: "scripts/script_deniega_solicitud.php",
            success: function(data){
                botonNormal(boton,"Denegar");
                if(data.correcto === true)
                    mostrarUsuarios();
                else
                    $("#er_1").html("<span>No se ha podido denegar la solicitud</span>");
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function borrarCliente(codCliente) {
        var boton = "#" + codCliente + ".borrar_cliente";
        $.ajax({
            data: {'cod_cliente': codCliente},
            type: "POST",
            dataType: "json",
            url: "scripts/script_desactiva_cliente.php",
            success: function (data) {
                botonNormal(boton, "Borrar");
                if (data.correcto === true)
                    mostrarUsuarios();
                else
                    $("#er_1").html("<span>No se ha podido borrar el cliente</span>");
            },
            beforeSend: function () {
                botonRueda(boton);
            }
        });
    }
    function mostrarArticulos(e){
        var contenedor = $(e);
        var busqueda = $("#texto_buscar").val();
        $.ajax({
            data: {'modo' : 'articulos',
                'texto_busqueda' : busqueda,
                'campo_busqueda' : campo_busqueda,
                'modo_busqueda' : modo_busqueda
            },
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var articulos = "";
                contenedor.html("");
                if(data.length > 0){
                    for(var i = 0 ; i < data.length ; i++){
                        articulos += data[i];
                    }
                    contenedor.append(articulos);
                }
            },
            beforeSend:function(){
                contenedor.append("<img width='25' height='25' src='img/ajax_loading.gif' /><span>Cargando...</span>");
            }
        });
    }

    function mostrarCarrito(e){
        var contenedor = $(e);
        $.ajax({
            data: {'modo' : 'carrito'},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var articulos = "";
                contenedor.children("tbody").html("");
                if(data.length > 0){
                    for(var i = 0 ; i < data.length ; i++){
                        articulos += data[i];
                    }
                    contenedor.children("tbody").append(articulos);
                }
                else contenedor.children("tbody").append("<tr><td colspan='4'>El carrito está vacío</td></tr>");
            },
            beforeSend:function(){
                contenedor.children("tbody").append("<tr><td colspan='4'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function gestorPedidoCliente(e){
        var contenedor = $(e);
        $.ajax({
            data: {'modo' : 'clientes_carrito'},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var articulos = "";
                if(data.length > 0){
                    articulos = "<div><label for='selector_cliente'>Pedido a nombre de </label>";
                    articulos += "<select class='form-control' name='selector_cliente' id='selector_cliente'>";
                    for(var i = 0 ; i < data.length ; i++){
                        articulos += data[i];
                    }
                    articulos += "</select></div>";
                    contenedor.append(articulos);
                    cliente_pedido = $("#selector_cliente").val();
                }
            }
        });
    }

    function procesaCarrito(e){
        var boton = $(e);
        $.ajax({
            data: {'a_nombre_cliente' : cliente_pedido},
            type: "POST",
            dataType: "json",
            url: "scripts/script_procesar_carrito.php",
            success: function(data){
                botonNormal(boton, "Realizar pedido");
                if(data.correcto){
                    $("#ex_1").html("<span>"+data.texto+"</span>");
                    mostrarCarrito("#ver_carrito table");
                }
                else{
                    $("#er_1").html("<span>"+data.texto+"</span>");
                }
            },
            beforeSend: function () {
                botonRueda(boton);
            }
        });
    }

    function anyadeArticulo(cod_articulo){
        var boton = $("#"+cod_articulo).children("[name=anyade_articulo]");
        $.ajax({
            data: {'cod_articulo' : cod_articulo},
            type: "POST",
            dataType: "json",
            url: "scripts/script_anyade_articulo.php",
            success: function(data){
                botonNormal(boton, "Añadir al carrito");
                if(data.correcto){
                    mostrarCarrito("#ver_carrito table");
                }
                else{
                    $("#er_1").html("<span>"+data.texto_error+"</span>");
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function restarArticulo(cod_articulo,elem){
        var elemento = elem.siblings("span");
        var numero = elemento.html();
        $.ajax({
            data: {'cod_articulo' : cod_articulo, 'modo' : 'resta'},
            type: "POST",
            dataType: "json",
            url: "scripts/script_anyade_articulo.php",
            success: function(data){
                botonNormal(elemento,numero);
                if(data.correcto === true){
                    mostrarCarrito("#ver_carrito table");
                }
            },
            beforeSend: function(){
                botonRueda(elemento);
            }
        });
    }

    function sumarArticulo(cod_articulo,elem){
        var elemento = elem.siblings("span");
        var numero = elemento.html();
        $.ajax({
            data: {'cod_articulo' : cod_articulo, 'modo' : 'suma'},
            type: "POST",
            dataType: "json",
            url: "scripts/script_anyade_articulo.php",
            success: function(data){
                botonNormal(elemento,numero);
                if(data.correcto === true){
                    mostrarCarrito("#ver_carrito table");
                }
            },
            beforeSend: function(){
                botonRueda(elemento);
            }
        });
    }

    function quitarArticulo(cod_articulo,elem){
        $.ajax({
            data: {'cod_articulo' : cod_articulo, 'modo' : 'quitar'},
            type: "POST",
            dataType: "json",
            url: "scripts/script_anyade_articulo.php",
            success: function(data){
                botonNormal(elem,"Quitar");
                if(data.correcto === true){
                    mostrarCarrito("#ver_carrito table");
                }
            },
            beforeSend: function(){
                botonRueda(elem);
            }
        });
    }

    function mostrarPedidos(e){
        var contenedor = $(e);
        var busqueda = $("#texto_buscar").val();
        $.ajax({
            data: {'modo' : 'pedidos',
                'texto_busqueda' : busqueda,
                'campo_busqueda' : campo_busqueda,
                'modo_busqueda' : modo_busqueda},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var pedidos = "";
                contenedor.children("tbody").html("");
                if(data.length > 0){
                    for(var i = 0 ; i < data.length ; i++){
                        pedidos += data[i];
                    }
                    contenedor.children("tbody").append(pedidos);
                    $("[name=tabla_secundaria]").each(function(i,elem){
                        $(elem).hide();
                    });
                    $("[name=procesar_albaran]").each(function(i,elem){
                        $(elem).css({'cursor' : 'default'});
                        $(elem).addClass("disabled");
                    });
                }
                else contenedor.children("tbody").append("<tr><td colspan='5'>No hay pedidos</td></tr>");
            },
            beforeSend:function(){
                contenedor.children("tbody").append("<tr><td colspan='5'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function borrarPedido(cod_pedido,boton){
        $.ajax({
            data:{'modo' : 'borrar','cod_pedido' : cod_pedido},
            type: "POST",
            dataType:"json",
            url: "scripts/script_pedidos.php",
            success: function(data){
                botonNormal(boton,"Borrar");
                if(data.correcto){
                    boton.parent().parent().remove();
                    $("[name=tabla_secundaria]."+cod_pedido).remove();
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function borrarLineaPedido(cod_pedido,num_linea,boton){
        $.ajax({
            data:{'modo' : 'borrar_linea','cod_pedido' : cod_pedido,'num_linea' : num_linea},
            type: "POST",
            dataType:"json",
            url: "scripts/script_pedidos.php",
            success: function(data){
                botonNormal(boton,"Borrar línea");
                if(data.correcto){
                    boton.parent().parent().remove();
                    $("."+cod_pedido+"[name=tabla_principal]").children(".precio_total").html(data.precio_pedido);

                    var indice = Number(en_albaran.indexOf(num_linea));
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
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function sumarLineaPedido(cod_pedido,num_linea,elem){
        var elemento = elem.siblings("span");
        var numero = elemento.html();
        $.ajax({
            data: {'cod_pedido' : cod_pedido, 'num_linea' : num_linea, 'modo' : 'suma'},
            type: "POST",
            dataType: "json",
            url: "scripts/script_pedidos.php",
            success: function(data){
                if(data.correcto){
                    elemento.parent().siblings("td[name=gestor]").html(""+data.gestor);
                    elemento.parent().siblings("td[name=precio_linea]").html(""+data.precio_linea);
                    $("."+cod_pedido+"[name=tabla_principal]").children(".precio_total").html(data.precio_pedido);
                    elemento.html(""+data.cantidad);
                }
                else{
                    botonNormal(elemento,numero);
                }
            },
            beforeSend: function(){
                botonRueda(elemento);
            }
        });
    }

    function restarLineaPedido(cod_pedido,num_linea,elem){
        var elemento = elem.siblings("span");
        var numero = elemento.html();
        $.ajax({
            data: {'cod_pedido' : cod_pedido, 'num_linea' : num_linea, 'modo' : 'resta'},
            type: "POST",
            dataType: "json",
            url: "scripts/script_pedidos.php",
            success: function(data){
                if(data.correcto){
                    if(numero === '1') {
                        elemento.parent().parent().remove();

                        var indice = Number(en_albaran.indexOf(num_linea));
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
                    }
                    else{
                        elemento.parent().siblings("td[name=gestor]").html(""+data.gestor);
                        elemento.parent().siblings("td[name=precio_linea]").html(""+data.precio_linea);
                        $("."+cod_pedido+"[name=tabla_principal]").children(".precio_total").html(data.precio_pedido);
                        elemento.html(""+data.cantidad);
                    }
                }
                else{
                    botonNormal(elemento,numero);
                }
            },
            beforeSend: function(){
                botonRueda(elemento);
            }
        });
    }

    function procesaAlbaran(elem,codigo_pedido){
        $.ajax({
            data : {'cod_pedido' : codigo_pedido, 'lineas_a_albaran' : JSON.stringify(en_albaran)},
            type : "POST",
            dataType : "json",
            url : "scripts/script_albaranes.php",
            success : function(data){
                $("#er_1").html("");
                if(data.correcto){
                    $("tr").each(function(i,elm){
                        var elemento = $(elm);
                        for(var j = 0 ; j < en_albaran.length ; j++){
                            if(elemento.hasClass(codigo_pedido) && elemento.hasClass(en_albaran[j])){
                                elemento.addClass("table-warning");
                                elemento.children("td").children("button").remove();

                                botonNormal(elem,"Procesar albarán");
                                elem.addClass("disabled");
                                elem.css({'cursor' : 'default'});
                            }
                        }
                    });
                    $("[name=tabla_principal]."+codigo_pedido).children("td").children("button[name=borrar_pedido]").remove();
                    en_albaran = [];
                }
                else{
                    botonNormal(elem,"Procesar albarán");
                    $("#er_1").append("<span>Ha habido un error al procesar el albarán");
                }
            },
            beforeSend : function(){
                botonRueda(elem);
            }
        });
    }

    function mostrarAlbaranes(e){
        var contenedor = $(e);
        var busqueda = $("#texto_buscar").val();
        $.ajax({
            data: {'modo' : 'albaranes',
                'texto_busqueda' : busqueda,
                'campo_busqueda' : campo_busqueda,
                'modo_busqueda' : modo_busqueda},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var albaranes = "";
                contenedor.children("tbody").html("");
                if(data.length > 0){
                    for(var i = 0 ; i < data.length ; i++){
                        albaranes += data[i];
                    }
                    contenedor.children("tbody").append(albaranes);
                    $("[name=tabla_secundaria]").each(function(i,elem){
                        $(elem).hide();
                    });
                    $("[name=procesar_albaran]").each(function(i,elem){
                        $(elem).css({'cursor' : 'default'});
                        $(elem).addClass("disabled");
                    });
                }
                else contenedor.children("tbody").append("<tr><td colspan='5'>No hay albaranes</td></tr>");
            },
            beforeSend:function(){
                contenedor.children("tbody").append("<tr><td colspan='5'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function mostrarBotonFactura(elem){
        $.ajax({
            data:{'modo' : 'es_gestor'},
            type:"POST",
            dataType:"json",
            url: "scripts/script_comp.php",
            success: function(data){
                if(data.correcto){
                    $(elem).show();
                }
            }
        });
    }

    function borrarAlbaran(cod_albaran,boton){
        $.ajax({
            data:{'modo' : 'borrar','cod_albaran' : cod_albaran},
            type: "POST",
            dataType:"json",
            url: "scripts/script_albaranes.php",
            success: function(data){
                botonNormal(boton,"Borrar");
                if(data.correcto){
                    boton.parent().parent().remove();
                    $("[name=tabla_secundaria]."+cod_albaran).remove();
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function borrarLineaAlbaran(cod_albaran,num_linea,boton) {
        $.ajax({
            data: {'modo': 'borrar_linea', 'cod_albaran': cod_albaran, 'num_linea': num_linea},
            type: "POST",
            dataType: "json",
            url: "scripts/script_albaranes.php",
            success: function (data) {
                botonNormal(boton, "Borrar línea");
                if (data.correcto) {
                    boton.parent().parent().remove();
                    if(data.precio_albaran > 0)
                        $("." + cod_albaran + "[name=tabla_principal]").children(".precio_total").html(data.precio_albaran);
                    else{
                        borrarAlbaran(cod_albaran,$("[name=tabla_principal]."+cod_albaran).children().children("[name=borrar_albaran]"));
                    }
                }
            },
            beforeSend: function () {
                botonRueda(boton);
            }
        });
    }

    function modificarLineaAlbaran(cod_albaran,num_linea_albaran,precio,iva,descuento,boton){
        if(precio >= 0 && iva >= 0 && iva <= 1 && descuento >= 0 && descuento <= 1) {
            $.ajax({
                data: {
                    'modo': 'modificar_linea',
                    'cod_albaran': cod_albaran,
                    'num_linea': num_linea_albaran,
                    'precio': precio,
                    'iva': iva,
                    'descuento': descuento
                },
                type: "POST",
                datgaType: "json",
                url: "scripts/script_albaranes.php",
                success: function (data) {
                    botonNormal(boton, "Guardar");
                    if (data.correcto) {
                        botonNormal(boton, "Modificar línea");
                        $(boton).siblings("[name=cancelar_modificado_linea]").html("Borrar línea");
                        $(boton).siblings("[name=cancelar_modificado_linea]").attr("name", "borrar_linea");
                        $(boton).attr("name", "modificar_linea");
                        $(boton).removeClass("btn-success");
                        $(boton).addClass("btn-primary");
                        $(boton).parent().siblings().each(function (i, elem) {
                            switch ($(elem).attr("name")) {
                                case "gestor":
                                    $(elem).html("");
                                    $(elem).html(data.gestor);
                                    break;
                                case "precio_linea":
                                    $(elem).html("");
                                    $(elem).html(precio);
                                    break;
                                case "descuento":
                                    $(elem).html("");
                                    $(elem).html(descuento);
                                    break;
                                case "iva":
                                    $(elem).html("");
                                    $(elem).html(iva);
                                    break;
                            }
                        });
                        $("." + cod_albaran + "[name=tabla_principal]").children(".precio_total").html(data.precio_albaran);
                    }
                },
                beforeSend: function () {
                    botonRueda(boton);
                }
            });
        }
        else{
            cancelarModificadoLinea($(boton).siblings("[name=cancelar_modificado_linea]"));
        }
    }

    function procesaFactura(elem){
        var descuento_factura = 0;
        var selecDescuento = $("#descuento_factura");
        if(selecDescuento.val() !== "" && selecDescuento.val() >= 0 && selecDescuento.val() <= 1)
            descuento_factura = selecDescuento.val();
        else
            selecDescuento.val("0");
        $.ajax({
            data : {'modo' : 'procesa_factura' ,
                    'albaranes' : JSON.stringify(en_factura),
                    'cod_cliente' : cliente_factura,
                    'descuento_factura' : descuento_factura
            },
            type : "POST",
            dataType : "json",
            url : "scripts/script_facturas.php",
            success : function(data){
                $("#er_1").html("");
                if(data.correcto){
                    $("tr[name=tabla_principal]").each(function(cont,elm){
                        var elemento = $(elm);
                        for(var i = 0 ; i < en_factura.length ; i++){
                            if(elemento.hasClass(""+en_factura[i])){
                                elemento.addClass("table-warning");
                                elemento.children("td").children("button[name=borrar_albaran]").remove();
                                elemento.children("td").children("button[name=a_factura]").remove();
                            }
                        }
                    });
                    for(var i = 0 ; i < en_factura.length ; i++){
                        $("tr[name=tabla_secundaria]."+en_factura[i]+" button").each(function(i,elm){
                            $(elm).remove();
                        });
                    }
                    botonNormal(elem,"Facturar");
                    elem.addClass("disabled");
                    elem.css({'cursor' : 'default'});
                    en_factura = [];
                }
                else{
                    botonNormal(elem,"Facturar");
                    $("#er_1").append("<span>Ha habido un error al facturar el albarán");
                }
            },
            beforeSend : function(){
                botonRueda(elem);
            }
        });
    }

    function mostrarFacturas(e){
        var contenedor = $(e);
        var busqueda = $("#texto_buscar").val();
        $.ajax({
            data: {'modo' : 'facturas',
                'texto_busqueda' : busqueda,
                'campo_busqueda' : campo_busqueda,
                'modo_busqueda' : modo_busqueda},
            type: "POST",
            dataType: "json",
            url: "scripts/obtencionDatos.php",
            success: function(data){
                var facturas = "";
                contenedor.children("tbody").html("");
                if(data.length > 0){
                    for(var i = 0 ; i < data.length ; i++){
                        facturas += data[i];
                    }
                    contenedor.children("tbody").append(facturas);
                    $("[name=tabla_secundaria]").each(function(i,elem){
                        $(elem).hide();
                    });
                }
                else contenedor.children("tbody").append("<tr><td colspan='7'>No hay facturas</td></tr>");
            },
            beforeSend:function(){
                contenedor.children("tbody").append("<tr><td colspan='7'><img width='25' height='25' src='img/ajax_loading.gif' /></td></tr>");
            }
        });
    }

    function borrarFactura(cod_factura,boton){
        $.ajax({
            data:{'modo' : 'borrar','cod_factura' : cod_factura},
            type: "POST",
            dataType:"json",
            url: "scripts/script_facturas.php",
            success: function(data){
                botonNormal(boton,"Borrar");
                if(data.correcto){
                    boton.parent().parent().parent().remove();
                    $("[name=tabla_secundaria]."+cod_factura).remove();
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function desfacturarAlbaran(cod_albaran,boton){
        var cod_factura = $(boton).parent().parent().attr("class").split(" ")[1];
        $.ajax({
            data:{'modo' : 'desfacturar','cod_albaran' : cod_albaran, 'cod_factura' : cod_factura},
            type: "POST",
            dataType:"json",
            url: "scripts/script_facturas.php",
            success:function(data){
                if(data.correcto){
                    $("tr[name=tabla_secundaria] tbody tr").each(function(i,elm){
                        console.log(elm);
                        if($(elm).attr("class").split(" ")[2] === cod_albaran){
                            $(elm).remove();
                        }
                    });
                    if(!data.lineas){
                        borrarFactura(cod_factura,$("[name=borrar_factura]."+cod_factura));
                    }
                }
                else{
                    botonNormal(boton,"Desfacturar albarán");
                }
            },
            beforeSend: function(){
                botonRueda(boton);
            }
        });
    }

    function modificarDescuentoFactura(elem,valor){
        var codigo_factura = $(elem).parent().attr("class");
        $.ajax({
            data: {'modo': 'modificar', 'descuento_factura': valor, 'cod_factura': codigo_factura},
            type: "POST",
            dataType: "json",
            url: "scripts/script_facturas.php",
            success: function (data) {
                $(elem).html("");
                $(elem).append(data.descuento_nuevo);
            },
            beforeSend: function () {
                $(elem).html("");
                $(elem).append("<img width='25' height='25' src='img/ajax_loading.gif' />");
            }
        });
    }
