var mostrando_error_pass = false;
var mostrando_error_dni = false;
var mostrando_error_telefono = false;
var mostrando_error_email = false;

function checkPass(){
    var pat = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
    var err_pass = false;
    $('.pass').each(function(i, elem){
        if($(elem).val() != "") {
            if (!pat.test($(elem).val())) {
                $(elem).css({'border': '1px solid red'});
                err_pass = true;
            }
            else {
                $(elem).css({'border': 'none'});
            }
        }
    });
    if(err_pass && !mostrando_error_pass){
        //event.preventDefault();
        mostrando_error_pass = true;
        $('#er_2').append( "<span id='erPass'>La contraseña debe tener mínimo 8 caracteres, un número, y una letra</span>" );
    }
    else if(!err_pass){
        $("span#erPass").remove();
        mostrando_error_pass = false;
    }
}

function checkDNI(){
    var pat = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/;
    var letras = "TRWAGMYFPDXBNJZSQVHLCKE";
    var err_dni = false;
    $('.dni').each(function(i, elem){
        var elemento = $(elem).val().toUpperCase();
        if(elemento != "") {
            if (!pat.test(elemento)) {
                $(elem).css({'border': '1px solid red'});
                err_dni = true;
            }
            else {
                var letra = elemento.substr(8,9);
                var numero = parseInt(elemento.substr(0,8)) % 23;
                if(letras.charAt(numero) === letra)
                    $(elem).css({'border': 'none'});
                else{
                    $(elem).css({'border': '1px solid red'});
                    err_dni = true;
                }
            }
        }
    });
    if(err_dni && !mostrando_error_dni){
        //event.preventDefault();
        mostrando_error_dni = true;
        $('#er_2').append( "<span id='erDNI'>DNI incorrecto.</span>" );
    }
    else if(!err_dni){
        $("span#erDNI").remove();
        mostrando_error_dni = false;
    }

}

function checkTelefono(){
    var pat = /[0-9]{9}/;
    var err_tlf = false;
    $('.telefono').each(function(i,elem){
        if($(elem).val() != "") {
            if (!pat.test($(elem).val())) {
                $(elem).css({'border': '1px solid red'});
                err_tlf = true;
            }
            else {
                $(elem).css({'border': 'none'});
            }
        }
    });
    if(err_tlf && !mostrando_error_telefono){
        mostrando_error_telefono = true;
        $('#er_2').append("<span id='erTlf'>Teléfono incorrecto</span>");
    }
    else if(!err_tlf){
        $("span#erTlf").remove();
        mostrando_error_telefono = false;
    }
}

function checkEmail(){
    var pat = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/g;
    var err_email = false;
    $('.email').each(function(i,elem){
        if($(elem).val() != ""){
            if(!pat.test($(elem).val())){
                $(elem).css({'border':'1px solid red'});
                err_email = true;
            }
            else{
                $(elem).css({'border':'none'});
            }
        }
    });
    if(err_email && !mostrando_error_email){
        mostrando_error_email = true;
        $('#er_2').append("<span id='erEmail'>Formato del email incorrecto</span>");
    }
    else if(!err_email){
        $("span#erEmail").remove();
        mostrando_error_email = false;
    }
}