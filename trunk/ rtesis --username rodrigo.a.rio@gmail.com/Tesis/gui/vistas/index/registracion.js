jQuery.validator.addMethod("nowhitespace", function(value, element) {
	return this.optional(element) || /^\S+$/i.test(value);
}, "Sin espacios en blanco.");

var validateFormRegistracion = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("id"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        tipoDocumento:{required:true},
        nroDocumento:{required:true, digits:true, nowhitespace:true, maxlength:8},
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true, email:true},
        nombreUsuario:{minlength:5, nowhitespace:true},
        contrasenia:{required:function(element){
                                return $("#contraseniaConfirmar").val() != "";
                              },
                     minlength:5},
        contraseniaConfirmar:{required:function(element){
                                        return $("#contrasenia").val() != "";
                                        },
                              equalTo:'#contrasenia'},
        sexo:{required:true},
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true}
    },
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
            required: "Debe ingresar su número de documento",
            digits: mensajeValidacion("digitos"),
            maxlength:mensajeValidacion("maxlength", '8')
        },
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email:{
            required: mensajeValidacion("requerido"),
            email: mensajeValidacion("email")
        },
        nombreUsuario:{
            minlength:mensajeValidacion("minlength", '5')
        },
        contrasenia:{
            required: mensajeValidacion("requerido"),
            minlength: mensajeValidacion("minlength", '5')
        },
        contraseniaConfirmar:{
            required: mensajeValidacion("requerido"),
            equalTo: mensajeValidacion("iguales")
        },
        sexo: mensajeValidacion("requerido"),
        fechaNacimientoDia:{
            required: mensajeValidacion("requerido2", 'día'),
            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoMes:{
            required: mensajeValidacion("requerido2", 'mes'),
            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoAnio:{
            required: mensajeValidacion("requerido2", 'año'),
            digits: mensajeValidacion("digitos")
        }
    }
}

var optionsAjaxFormRegistracion = {
    dataType: 'jsonp',
    resetForm: false,
    forceSync: true,
    url: 'registracion-procesar',

    beforeSerialize: function($form, options){
        
        if($("#formRegistracion").valid() == true){
            $('#msg_form_registracion').hide();
            $('#msg_form_registracion').removeClass("correcto").removeClass("error");
            $('#msg_form_registracion .msg').html("");

            hashPassword("contrasenia", "contraseniaMD5");
            $("#contrasenia").val("");
            $("#contraseniaConfirmar").val("");
                        
            setWaitingStatus('formRegistracion', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formRegistracion', false);
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_registracion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_registracion .msg').html(data.mensaje);
            }
            $('#msg_form_registracion').addClass("error").fadeIn('slow');            
        }else{

            //se registro con exito, muestro un dialog y al aceptar
            //se produce una redireccion con los permisos reseteados
            var dialog = $("#dialog");
            if($("#dialog").length){
                dialog.attr("title", "Registración");
            }else{
                dialog = $('<div id="dialog" title="Registración"></div>').appendTo('body');
            }
                        
            dialog.html(data.html);

            dialog.dialog({
                position:['center', 'center'],
                width:600,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:false,
                beforeClose:function(event, ui){
                    location = data.redirect;
                },
                buttons:{
                    "Aceptar": function() {
                        location = data.redirect;
                    }
                }
            });                        
        }
    }
};

function bindEventsFormRegistracion()
{
    $("#formRegistracion").validate(validateFormRegistracion);
    $("#formRegistracion").ajaxForm(optionsAjaxFormRegistracion);

    //hints
    $("#nroDocumento").live("focus", function(){ revelarElemento($("#hintNroDocumento")); });
    $("#nroDocumento").live("blur", function(){ ocultarElemento($("#hintNroDocumento")); });
    $("#nombreUsuario").live("focus", function(){ revelarElemento($("#hintNombreUsuario")); });
    $("#nombreUsuario").live("blur", function(){ ocultarElemento($("#hintNombreUsuario")); });
    $("#contrasenia").live("focus", function(){ revelarElemento($("#hintContrasenia")); });
    $("#contrasenia").live("blur", function(){ ocultarElemento($("#hintContrasenia")); });
}

$(document).ready(function(){

    if($("#formRegistracion").length){
        bindEventsFormRegistracion();
    }
});



/*
$(function(){
	$('form').submit(function(){ return false; });
	$('#reg_button').click(function(){
	    //remove classes
	    $('#registracion input').removeClass('error').removeClass('valid');
	
	    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	    var fields = $('#registracion input[type=text], input[type=password],#registracion select');
	    var error = 0;
	    fields.each(function(){
	        var value = $(this).val();
	        if( value == -1 || value.length<1 || ( $(this).attr('id')=='reg_email' && !emailPattern.test(value) ) ) {
	            $(this).addClass('reg_error');
	            //$(this).effect("shake", { times:3,distance:0 }, 100);
	            error++;
	        } else {
	            $(this).removeClass('reg_error');
	        }
	    });
	    
	    if( $('#reg_pass').val() != $('#reg_pass_confirmacion').val() ) {
            $('#registracion input[type=password]').each(function(){
                $(this).addClass('error');
                error++;
            });
	    }
	    if(!error) {
	    	var fields = "tipoDni="+$('#reg_tipoDni').val()+
    		"&dni="+$('#reg_dni').val()+
    		"&username="+$('#reg_nombre_usuario').val()+
    		"&password="+$('#reg_pass').val()+
    		"&email="+$('#reg_email').val()+
    		"&firstname="+$('#reg_nombre').val()+
    		"&lastname="+$('#reg_apellido').val()+
    		"&sex="+$('#reg_sex').val()+
    		"&inv="+$('#inv').val()+
    		"&us="+$('#us').val()+
    		"&fechaNacimiento="+$('#reg_dia_fecha_de_nacimiento').val()+"/"+$('#reg_mes_fecha_de_nacimiento').val()+"/"+$('#reg_anio_fecha_de_nacimiento').val()+"";                       
			$.ajax({
				type:	"POST",
				url: 	"registracion-procesar",
				data: 	fields,
				beforeSend: function() {
					$("#registracion_msg").html("");
					$("#registracion_msg").hide();
					$("#registracion_msg_error").html("");
					$("#registracion_msg_error").hide();
					$("#loading").show();
				},
				success: function(data){
					$("#loading").hide();
					var resp = $.parseJSON(data);
					if(resp == false){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Se ha producido un error desconocido. Por favor intente mas tarde.");
						return false;						
					}
					if(resp == 10){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Nombre de usuario existente");
						$("#reg_nombre_usuario").addClass('reg_error');
						return false;						
					}else if(resp == 11){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Numero de documento existente");
						$("#reg_dni").addClass('reg_error');
						return false;						
					}else if(resp == 12){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Email existente");
						$("#reg_email").addClass('reg_error');
						return false;						
					}
					
					$("#registracion").hide();
					//$("#registracion_msg").html("Registracion existosa");
					//$("#registracion_msg").show();
					//enviar a la primer pagina del perfil
					location = data.redirect;
				}
			});
	    } else{
	    	return false;
	    }
	});
});
*/