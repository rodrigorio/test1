var validateFormLogin = {
    errorElement: "div",
    validClass: "correcto",

    //algunos parametros los seteamos para que no haya incompatibilidades con navegadores viejos
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,

    //ubico la etiqueta con el resultado y el mensaje en el contenedor de form_linea del campo
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("id"));
    },

    //no quiero que el plugin agregue ni saque clases me gusta todo como esta
    highlight: function(element){},
    unhighlight: function(element){},

    //reglas que tiene que verificar en los campos
    rules:{
        tipoDocumento:{required:true},
        nroDocumento:{required:true, ignorarDefault:true, digits:true},
        contrasenia:{required:true}
    },

    //mensajes que devuelve si campo es invalido (ver la funcion en el archivo funciones-globales.js)
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
                        required: "Debe ingresar su numero de documento",
                        ignorarDefault: "Debe ingresar su numero de documento",
                        digits: mensajeValidacion("digitos")
                      },
        contrasenia: mensajeValidacion("requerido")
    }
}

var optionsAjaxFormLogin = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'login-procesar',
    forceSync: true,

    beforeSerialize: function($form, options){
        if($("#formLogin").valid() == true){

            //calculo MD5
            hashPassword("contrasenia", "contraseniaMD5");
            $("#contrasenia").val("");

            //reseteo el contenedor del mensaje de resultado
            $('#msg_form_login').removeClass("correcto").removeClass("error");
            $('#msg_form_login .msg').html("");

            //marco el contenedor en espera de una respuesta
            setWaitingStatus('formLogin', true);
        }else{
            //cancelo el submit
            return false;
        }
    },

    success:function(data){ 
        if(data.success == undefined || data.success == 0){
            //si se produjo error muestro el mensaje
            var mensaje = data.mensaje;
            if(mensaje == undefined){
                mensaje = lang['error procesar'];
            }
            $('#msg_form_login .msg').html(mensaje);
            $('#msg_form_login').addClass("error").fadeIn('slow');
            setWaitingStatus('formLogin', false);
        }else{
            //si se logueo de forma satisfactoria redirecciono
            location = data.redirect;
        }
    }

};

var validateFormRecuperarContrasenia = {
    errorElement: "div",
    validClass: "correcto",

    //algunos parametros los seteamos para que no haya incompatibilidades con navegadores viejos
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,

    //ubico la etiqueta con el resultado y el mensaje en el contenedor de form_linea del campo
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("id"));
    },

    //no quiero que el plugin agregue ni saque clases me gusta todo como esta
    highlight: function(element){},
    unhighlight: function(element){},

    //reglas que tiene que verificar en los campos
    rules:{
        tipoDocumentoRecuperarPass:{required:true},
        nroDocumentoRecuperarPass:{required:true, ignorarDefault:true, digits:true},
        emailRecuperarPass:{required:true, email:true}
    },

    //mensajes que devuelve si campo es invalido (ver la funcion en el archivo funciones-globales.js)
    messages:{
        tipoDocumentoRecuperarPass: "Debe especificar tipo de documento",
        nroDocumentoRecuperarPass:{
                        required: "Debe ingresar su numero de documento",
                        ignorarDefault: "Debe ingresar su numero de documento",
                        digits: mensajeValidacion("digitos")
                      },
        emailRecuperarPass:{
            required: mensajeValidacion("requerido"),
            email: mensajeValidacion("email")
        }
    }
}

var optionsAjaxFormRecuperarContrasenia = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'recuperar-contrasenia-procesar',
    forceSync: true,

    beforeSerialize: function($form, options){
        if($("#formRecuperarContrasenia").valid() == true){

            //reseteo el contenedor del mensaje de resultado
            $('#msg_form_recuperarContrasenia').removeClass("correcto").removeClass("error");
            $('#msg_form_recuperarContrasenia .msg').html("");

            //marco el contenedor en espera de una respuesta
            setWaitingStatus('formRecuperarContrasenia', true);
        }else{
            //cancelo el submit
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formRecuperarContrasenia', false);
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_recuperarContrasenia .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_recuperarContrasenia .msg').html(data.mensaje);
            }
            $('#msg_form_recuperarContrasenia').addClass("error").fadeIn('slow');
        }else{
            //si se envio el mail con exito muestro un dialog con el cartel 
            var dialog = $("#dialog");
            if($("#dialog").length){
                dialog.attr("title", "Recuperar Contraseña");
            }else{
                dialog = $('<div id="dialog" title="Recuperar Contraseña"></div>').appendTo('body');
            }

            dialog.html(data.html);

            dialog.dialog({
                position:['center', 'center'],
                width:600,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:false,
                buttons:{
                    "Aceptar": function() {
                        $(this).dialog( "close" );
                    }
                }
            });
        }
    }
};

function bindEventsFormLogin()
{
    $("#formLogin").validate(validateFormLogin);
    $("#formLogin").ajaxForm(optionsAjaxFormLogin);

    //hints
    $("#contrasenia").live("focus", function(){
        revelarElemento($("#hintContrasenia"));
    });
    $("#contrasenia").live("blur", function(){
        ocultarElemento($("#hintContrasenia"));
    });
}

function bindEventsRecuperarContrasenia()
{
    $("#formRecuperarContrasenia").validate(validateFormRecuperarContrasenia);
    $("#formRecuperarContrasenia").ajaxForm(optionsAjaxFormRecuperarContrasenia);
}

function recuperarContrasenia()
{
    var dialog = setWaitingStatusDialog(550, "Recuperar Contraseña");
    dialog.load(
        "recuperar-contrasenia",
        {},
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsRecuperarContrasenia();
        }
    );
}

function login()
{   
    var dialog = setWaitingStatusDialog(550, "Acceder");
    dialog.load(
        pathUrlBase+"login?popUp=1",
        {},
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormLogin();
        }
    );
}

$(document).ready(function(){    
    $("#recuperarContrasenia").live('click', function(){
        recuperarContrasenia();
        return false;
    });

    if($("#formLogin").length){
        bindEventsFormLogin();
    }
});