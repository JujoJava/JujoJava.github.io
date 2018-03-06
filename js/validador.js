"use strict";

$(document).ready(function(){
    var formulario = $("#form_valido");
    formulario.on("blur",".dni",checkDNI);
    formulario.on("blur",".pass",checkPass);
    formulario.on("blur",".telefono",checkTelefono);
    formulario.on("blur",".email",checkEmail);

    formulario.on("click","#boton_envio",function(event){
       var error = 0;
       $('.requerido').each(function(i, elem){
         if($(elem).val() == ''){
             $(elem).css({'border':'1px solid red'});
             error++;
         }
         else{
             $(elem).css("border","none");
         }
       });
       checkPass();
       checkDNI();
       checkEmail();
       checkTelefono();
       $('.cadena').each(function(i, elem){
          if(!isNaN($(elem).val())){
             $(elem).css({'border':'1px solid red'});
             $(elem).parent().append("<span class='error'>Debe tener letras</span>");
             error++;
          }
       });
       $('.numero').each(function(i, elem){
         if(isNaN($(elem).val())){
             $(elem).css({'border':'1px solid red'});
             $(elem).parent().append("<span class='error'>Debe ser un número</span>");
             error++;
         }
       });
       if(error > 0 || mostrando_error_pass || mostrando_error_dni || mostrando_error_email || mostrando_error_telefono) {
           event.preventDefault();
           $('#er_1').html('<span>Debe rellenar los campos requeridos</span>');
       }
       else{
           $("#er_1").html("");
            switch($(this).attr("name")){
                case "logueo":
                    comprobarLogin($('[name=login_nom]').val(),$('[name=login_pass]').val(),$('input[name=tipoLogin]:checked').val());
                    break;
                case "solicitar":
                    registrarUsuario($('[name=solicitud_nick]').val(),$('[name=solicitud_pass]').val());
                    break;
                case "gestor_registra":
                    registrarCliente($('[name=solicitud_nick]').val(),$('[name=solicitud_pass]').val());
                    break;
            }
       }
    });
});