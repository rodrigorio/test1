// SUBMIT FORMULARIO LOGIN//

//preparo el objeto de opciones para el submit
var optionsAjaxFormEnviarInvitacion = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/invitacion-procesar',

    beforeSerialize: function($form, options){
        if($("#formEnviarInvitacion").valid() == true){

            //reseteo el contenedor del mensaje de resultado
            $('#msg_form_enviarInvitacion').removeClass("correcto").removeClass("error");
            $('#msg_form_enviarInvitacion .msg').html("");

            //marco el contenedor en espera de una respuesta
            setWaitingStatus('formEnviarInvitacion', true);
        }else{
            //cancelo el submit
            return false;
        }
    },

    success:function(data){
        //quito el estado en espera del contenedor
        setWaitingStatus('formEnviarInvitacion', false);
        if(data.success == undefined || data.success == 0){
            //si se produjo error muestro el mensaje
            var mensaje = data.mensaje;
            if(mensaje == undefined){
                mensaje = lang['error procesar'];
            }
            $('#msg_form_enviarInvitacion .msg').html(mensaje);
            $('#msg_form_enviarInvitacion').addClass("error").fadeIn('slow');
        }else{
            //si se envio de forma satisfactoria muestro un mensaje de correcto.
            location = data.redirect;
        }
    }
};
$("#formEnviarInvitacion").ajaxForm(optionsAjaxFormEnviarInvitacion);

// VALIDACION FORMULARIO LOGIN//

var validateFormEnviarInvitacion = {
    errorElement:"div",
    validClass:"correcto",

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
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true},
        relacion:{required:true}
    },

    //mensajes que devuelve si campo es invalido (ver la funcion en el archivo funciones-globales.js)
    messages:{
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email: mensajeValidacion("requerido"),
        relacion: mensajeValidacion("requerido")
    }
}
$("#formEnviarInvitacion").validate(validateFormEnviarInvitacion);

// HINTS FORMULARIO LOGIN//
$("#email").live("focus", function(){
    $("#hintEmail").show();
});
$("#email").live("blur", function(){
    $("#hintEmail").hide();
});

$("#relacion").live("focus", function(){
    $("#hintRelacion").show();
});
$("#relacion").live("blur", function(){
    $("#hintRelacion").hide();
});