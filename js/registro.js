"use strict";

var dni = "";
var razon_social = "";
var domicilio_social = "";
var ciudad = "";
var telefono = "";
var email = "";

$(document).ready(function(){
    $("#datos_logueo").hide();
    $("#solicitar").on("click","button[type=button]#continuar_solicitud",function(){
        var error = 0;
        $("#solicitud_datos_personales .requerido").each(function(i, elem){
            if($(elem).val() == ''){
                $(elem).css({'border':'1px solid red'});
                error++;
            }
            else{
                $(elem).css("border","none");
            }
        });
        checkDNI();
        checkTelefono();
        checkEmail();
        if(error <= 0 && !mostrando_error_dni && !mostrando_error_email && !mostrando_error_telefono){

            dni = $("#dni").val();
            razon_social = $("#razon_social").val();
            domicilio_social = $("#domicilio_social").val();
            ciudad = $("#ciudad").val();
            telefono = $("#telefono").val();
            email = $("#email").val();

            $('#er_1').html('');
            $("#datos_logueo").show(1000,function(){
                $("#continuar_solicitud").hide(300);
                $("#solicitud_datos_personales input[type=text]").each(function(i,elem){
                    $(elem).prop("disabled",true);
                });
            });
        }
        else{
            $('#er_1').html('<span>Debe rellenar los campos requeridos</span>');
        }
    });
    $("#solicitar").on("click","button[type=button]#boton_reinicio",function(){
        $("#datos_logueo").hide(1000,function(){
            $("#continuar_solicitud").show(300);
            $("#solicitud_datos_personales input[type=text]").each(function(i,elem){
                $(elem).prop("disabled",false);
            });
        });
    });
});