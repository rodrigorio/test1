/**
 * Para ayuda acerca de form plugin: http://jquery.malsup.com/form/#html
 * Para ayuda acerca de validate plugin: http://docs.jquery.com/Plugins/Validation/validate#options
 */

// SUBMIT FORMULARIO LOGIN//

//preparo el objeto de opciones para el submit
var optionsAjaxFormLogin = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'login-procesar',

    beforeSerialize: function($form, options){
        if($("#formLogin").valid() == true){

            //calculo MD5
            hashPassword("contrasenia", "contraseniaMD5");
            //borro la contraseania asi no se envia
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
        //quito el estado en espera del contenedor
        setWaitingStatus('formLogin', false);
        if(data.success == undefined || data.success == 0){
            //si se produjo error muestro el mensaje
            var mensaje = data.mensaje;
            if(mensaje == undefined){
                mensaje = lang['error procesar'];
            }
            $('#msg_form_login .msg').html(mensaje);
            $('#msg_form_login').addClass("error").fadeIn('slow');
        }else{
            //si se logueo de forma satisfactoria redirecciono
            location = data.redirect;
        }
    }
};
$("#formLogin").ajaxForm(optionsAjaxFormLogin);

// VALIDACION FORMULARIO LOGIN//

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
        error.appendTo("#msg_"+element.attr("id"));
    },

    //no quiero que el plugin agregue ni saque clases me gusta todo como esta
    highlight: function(element){},
    unhighlight: function(element){},

    //reglas que tiene que verificar en los campos
    rules:{
        documento:{required:true},
        contrasenia:{required:true}
    },

    //mensajes que devuelve si campo es invalido (ver la funcion en el archivo funciones-globales.js)
    messages:{
        nombreUsuario: mensajeValidacion("requerido"),
        contrasenia: mensajeValidacion("requerido")
    }
}
$("#formLogin").validate(validateFormLogin);

// HINTS FORMULARIO LOGIN//
$("#contrasenia").live("focus", function(){
    $("#hintContrasenia").show();
});
$("#contrasenia").live("blur", function(){
    $("#hintContrasenia").hide();
});