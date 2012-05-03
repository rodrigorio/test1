/**
 * Para ayuda acerca de form plugin: http://jquery.malsup.com/form/#html
 * Para ayuda acerca de validate plugin: http://docs.jquery.com/Plugins/Validation/validate#options
 */

// HINTS FORMULARIO LOGIN//
$("#contrasenia").live("focus", function(){
    revelarElemento($("#hintContrasenia"));
});
$("#contrasenia").live("blur", function(){
    ocultarElemento($("#hintContrasenia"));
});

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
$("#formLogin").validate(validateFormLogin);

// SUBMIT FORMULARIO LOGIN//
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

function recuperarPass(){
    var nombreUsuario = $("#rec_nombre_usuario").val();
    var email = $("#rec_email").val();
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    var fields = $('#recuperarContrasenia input[type=text]');
    var error = 0;
    fields.each(function(){
        var value = $(this).val();
        if( value == "" || value.length<1 || ( $(this).attr('id')=='rec_email' && !emailPattern.test(value) ) ) {
            $(this).addClass('rec_error');
            $("#hint"+$(this).attr('id')).show();
            error++;
        } else {
        	$("#hint"+$(this).attr('id')).hide();
            $(this).removeClass('rec_error');
        }
    });
    if(!error) {
    	var fields = "email="+email+"&nombreUsuario="+nombreUsuario;
    	$.ajax({
			type:	"POST",
			url: 	"recuperarContrasenia",
			data: 	fields,
			beforeSend: function() {
				$("#ajax_loading").show();
			},
			success: function(data){
				$("#ajax_loading").hide();
				if(data.success==1){
					
				}else{
					alert(data.mensaje);
				}
			}
    	});
    }else{
    	return false;
    }
}