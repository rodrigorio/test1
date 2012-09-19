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