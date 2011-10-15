//modificar privacidad (con ajax)
function cambiarPrivacidad(campo, valor){
    var fields = "nombreCampo="+campo+"&valorPrivacidad="+valor;
    $.ajax({
        type:	"POST",
        url: 	"comunidad/modificarPrivacidadCampo",
        data: 	fields,
        beforeSend: function(){
            setWaitingStatus('pageRightInnerCont', true);
        },
        success:function(data){
            setWaitingStatus('pageRightInnerCont', false);
        }
    });
}

$("#privacidadEmail").change(function(){ cambiarPrivacidad('email', $("#privacidadEmail option:selected").val()); });
$("#privacidadTelefonoContacto").change(function(){ cambiarPrivacidad('telefono', $("#privacidadTelefonoContacto option:selected").val()); });
$("#privacidadMovil").change(function(){ cambiarPrivacidad('celular', $("#privacidadMovil option:selected").val()); });
$("#privacidadFax").change(function(){ cambiarPrivacidad('fax', $("#privacidadFax option:selected").val()); });
$("#privacidadCurriculum").change(function(){ cambiarPrivacidad('curriculum', $("#privacidadCurriculum option:selected").val()); });

//toggle modificar contrasenia
$("#toggleContrasenia").click(function(){
    revelarElemento($("#contModificarPassword"));
    return false;
});

// hints formularios
$("#contraseniaNueva").live("focus", function(){
    $("#hintContraseniaNueva").show();
});
$("#contraseniaNueva").live("blur", function(){
    $("#hintContraseniaNueva").hide();
});

//submit form info basica

//regla especial para verificar por ajax que la contrasenia coincide con la actual efectivamente
jQuery.validator.addMethod("ignorarDefault", function(value, element){
    var fields = "nombreCampo="+campo+"&valorPrivacidad="+valor;
    $.ajax({
        type:	"POST",
        url: 	"comunidad/modificarPrivacidadCampo",
        data: 	fields,
        beforeSend: function(){
            setWaitingStatus('pageRightInnerCont', true);
        },
        success:function(data){
            setWaitingStatus('pageRightInnerCont', false);
        }
    });
});

var validateFormInfoBasica = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){
        error.appendTo("#msg_"+element.attr("id"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        tipoDocumento:{required:true},
        nroDocumento:{required:true, digits:true, ignorarDefault},
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true, email:true},
        contraseniaActual:{/* validacion por ajax si != vacio, hay que crear regla */ },
        contraseniaNueva:{required:true},                
        contraseniaConfirmar:{required:true, equalTo:'#contraseniaNueva'},
        sexo:{required:true},                                
        fechaNacimientoDia:{required:true},                                
        fechaNacimientoMes:{required:true},                                
        fechaNacimientoAnio:{required:true}
    },
    messages:{
        tipoDocumento: mensajeValidacion("requerido"),
        nroDocumento: mensajeValidacion("requerido"),
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email: mensajeValidacion("requerido"),
        contraseniaActual: mensajeValidacion("requerido"),
        contraseniaNueva: mensajeValidacion("requerido"),
        contraseniaConfirmar: mensajeValidacion("requerido"),
        sexo: mensajeValidacion("requerido"),
        fechaNacimientoDia: mensajeValidacion("requerido"),
        fechaNacimientoMes: mensajeValidacion("requerido"),
        fechaNacimientoAnio: mensajeValidacion("requerido")
    }
}
$("#formInfoBasica").validate(validateFormInfoBasica);

var optionsAjaxFormInfoBasica = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/datos-personales-procesar',

    beforeSerialize: function($form, options){
        if($("#formInfoBasica").valid() == true){
            hashPassword("contrasenia", "contraseniaMD5");
            $("#contrasenia").val("");
            $('#msg_form_infoBasica').removeClass("correcto").removeClass("error");
            $('#msg_form_infoBasica .msg').html("");
            setWaitingStatus('formInfoBasica', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInfoBasica', false);
        if(data.success == undefined || data.success == 0){
            var mensaje = data.mensaje;
            if(mensaje == undefined){
                mensaje = lang['error procesar'];
            }
            $('#msg_form_infoBasica .msg').html(mensaje);
            $('#msg_form_infoBasica').addClass("error").fadeIn('slow');
        }else{
            location = data.redirect;
        }
    }
};
$("#formInfoBasica").ajaxForm(optionsAjaxFormInfoBasica);


