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

//ya esta el mail registrado?
jQuery.validator.addMethod("mailDb", function(value, element){
    var result = true;
    if($("#email").val() != ""){
        $.ajax({
            url:"comunidad/datos-personales-procesar",
            type:"post",
            async:false,
            data:{
                seccion:"check-mail-existe",
                email:function(){ return $("#email").val(); }
            },
            success:function(data){
                //si el mail existe tira el cartel
                if(data == '1'){ result = false; }
            }
        });
    }
    return result;
});

//es la contrasenia actual del usuario?
jQuery.validator.addMethod("contraseniaActual", function(value, element){
    var result = true;
    if($("#contraseniaActual").val() != ""){
        hashPassword("contraseniaActual", "contraseniaActualMD5");
        $.ajax({
            url:"comunidad/datos-personales-procesar",
            type:"post",
            async:false,
            data:{
                seccion:"check-contrasenia-actual",
                contraseniaActual:function(){ return $("#contraseniaActualMD5").val(); }
            },
            success:function(data){
                if(data == '0'){ result = false; }
            }
        });
    }
    return result;
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
        error.appendTo(".msg_"+element.attr("id"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        tipoDocumento:{required:true},
        nroDocumento:{required:true, ignorarDefault:true, digits:true},
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true, email:true, mailDb:true},
        contraseniaActual:{required:function(element){
                                return ( ($("#contraseniaNueva").val() != "") || ($("#contraseniaConfirmar").val() != ""));
                          }, contraseniaActual:true},
        contraseniaNueva:{required:function(element){
                            return $("#contraseniaConfirmar").val() != "";
                         }, minlength:5},
        contraseniaConfirmar:{required:function(element){
                                return $("#contraseniaNueva").val() != "";
                              }, equalTo:'#contraseniaNueva'},
        sexo:{required:true},                                
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true}
    },
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
                        required: "Debe ingresar su numero de documento",
                        ignorarDefault: "Debe ingresar su numero de documento",
                        digits: mensajeValidacion("digitos")
                      },
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email:{
                required: mensajeValidacion("requerido"),
                email: mensajeValidacion("email"),
                mailDb: mensajeValidacion("email2")
        },
        contraseniaActual:{
                            required: mensajeValidacion("requerido"),
                            contraseniaActual: "La contraseña no coincide"
        },
        contraseniaNueva:{
                            required: mensajeValidacion("requerido"),
                            minlength: mensajeValidacion("minlength", '5')
        },
        contraseniaConfirmar:{
                                required: mensajeValidacion("requerido"),
                                equalTo: mensajeValidacion("iguales")
        },
        sexo: mensajeValidacion("requerido"),
        fechaNacimientoDia:{
                            required: mensajeValidacion("requerido", 'día'),
                            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoMes:{
                            required: mensajeValidacion("requerido", 'mes'),
                            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoAnio:{
                            required: mensajeValidacion("requerido", 'año'),
                            digits: mensajeValidacion("digitos")
        }
    }
}
$("#formInfoBasica").validate(validateFormInfoBasica);

var optionsAjaxFormInfoBasica = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'comunidad/datos-personales-procesar',

    beforeSerialize: function($form, options){
        if($("#formInfoBasica").valid() == true){
            
            //si ingreso contrasenia nueva la convierto y limpio los campos
            if( $("#contraseniaNueva").val() != "" ){
                hashPassword("contraseniaNueva", "contraseniaNuevaMD5");
                $("#contraseniaNueva").val("");
                $("#contraseniaConfirmar").val("");
                $("#contraseniaActual").val("");
                $("#contraseniaActualMD5").val("");
            }

            $('#msg_form_infoBasica').hide();
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
            $('#msg_form_infoBasica .msg').html(lang['error procesar']);
            $('#msg_form_infoBasica').addClass("error").fadeIn('slow');
        }else{
            $('#msg_form_infoBasica .msg').html(lang['exito procesar']);
            $('#msg_form_infoBasica').addClass("correcto").fadeIn('slow');
        }
    }
};
$("#formInfoBasica").ajaxForm(optionsAjaxFormInfoBasica);


//combo pais provincia ciudad form info contacto
function listaProvinciasByPais(idPais){
    //si el valor elegido es '' entonces marco como disabled
    if(idPais == ''){ 
        $('#provincia').addClass("disabled");        
    }else{
        $('#provincia').removeClass("disabled");
    }
    $('#ciudad').addClass("disabled");
    
    $.ajax({
        type: "POST",
        url: "comunidad/provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#provincia').html("");
            //dejo vacio el de ciudad si cambio de pais hasta que elija una provincia
            $('#ciudad').html("");
            $('#ciudad').html(new Option('Elija Ciudad:', '',true));
            if(lista.length != undefined && lista.length > 0){
                $('#provincia').append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#provincia').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#provincia').html(new Option('Elija Provincia:', '',true));                
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
 }
function listaCiudadesByProvincia(idProvincia){
    if(idProvincia == ''){
        $('#ciudad').addClass("disabled");
    }else{
        $('#ciudad').removeClass("disabled");
    }
    $.ajax({
        type: "POST",
        url: "comunidad/ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#ciudad').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#ciudad').append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ciudad').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#ciudad').append(new Option('Elija Ciudad:', '',true));
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
}
$("#pais").change(function(){ listaProvinciasByPais($("#pais option:selected").val()); });
$("#provincia").change(function(){ listaCiudadesByProvincia($("#provincia option:selected").val()); });

//validacion y submit form info contacto
var validateFormInfoContacto = {
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
        pais:{required:true, digits: true},
        provincia:{required:function(element){
                            return $("#pais option:selected").val() != "";
                  }, digits: true},
        ciudad:{required:function(element){
                            return $("#provincia option:selected").val() != "";
               }, digits: true},
        codigoPostal:{required:true},
        direccion:{required:true},
        telefono:{required:true}
    },
    messages:{
        pais:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")
        },
        provincia:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")
        },
        ciudad:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")            
        },
        codigoPostal: mensajeValidacion("requerido"),
        direccion: mensajeValidacion("requerido"),
        telefono: mensajeValidacion("requerido")
    }
}
$("#formInfoContacto").validate(validateFormInfoContacto);

var optionsAjaxFormInfoContacto = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'comunidad/datos-personales-procesar',

    beforeSerialize: function($form, options){
        if($("#formInfoContacto").valid() == true){
            $('#msg_form_infoContacto').hide();
            $('#msg_form_infoContacto').removeClass("correcto").removeClass("error");
            $('#msg_form_infoContacto .msg').html("");
            setWaitingStatus('formInfoContacto', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInfoContacto', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_infoContacto .msg').html(lang['error procesar']);
            $('#msg_form_infoContacto').addClass("error").fadeIn('slow');
        }else{
            $('#msg_form_infoContacto .msg').html(lang['exito procesar']);
            $('#msg_form_infoContacto').addClass("correcto").fadeIn('slow');
        }
    }
};
$("#formInfoContacto").ajaxForm(optionsAjaxFormInfoContacto);

//form informacion profesional

autoCompleteInput("institucion","comunidad/buscar-instituciones");