function showDialogIntegranteActivo()
{
    var dialog = setWaitingStatusDialog(650, "Cambio de Perfil");    
    dialog.load(
        "comunidad/datos-personales-procesar",
        {seccion:'dialogIntegranteActivo'},
        function(data){}
    );
}

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
                email:function(){return $("#email").val();}
            },
            success:function(data){
                //si el mail existe tira el cartel
                if(data == '1'){result = false;}
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
                contraseniaActual:function(){return $("#contraseniaActualMD5").val();}
            },
            success:function(data){
                if(data == '0'){result = false;}
            }
        });
    }
    return result;
});

//Existe un discapacitado con el numero de documento ingresado?
jQuery.validator.addMethod("existeNumeroDocumento", function(value, element){
    var result = true;
    if($("#nroDocumento").val() != ""){
        $.ajax({
            url:"comunidad/datos-personales-procesar",
            type:"post",
            async:false,
            data:{
                seccion:"check-numeroDocumento-existe",
                numeroDocumento:function(){return $("#nroDocumento").val();}
            },
            success:function(data){
                //si el mail existe tira el cartel
                if(data == '1'){result = false;}
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
        nroDocumento:{required:true, ignorarDefault:true, digits:true, maxlength:8, existeNumeroDocumento:true},
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
                        digits: mensajeValidacion("digitos"),
                        maxlength:mensajeValidacion("maxlength", '8'),
                        existeNumeroDocumento: "El numero de documento ya existe para una persona cargada en el sistema."
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

var optionsAjaxFormInfoBasica = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'comunidad/datos-personales-procesar',

    beforeSerialize: function($form, options){
        if($("#formInfoBasica").valid() == true){            
            $('#msg_form_infoBasica').hide();
            $('#msg_form_infoBasica').removeClass("correcto").removeClass("error");
            $('#msg_form_infoBasica .msg').html("");

            //si ingreso contrasenia nueva la convierto y limpio los campos
            if( $("#contraseniaNueva").val() != "" ){
                hashPassword("contraseniaNueva", "contraseniaNuevaMD5");
                $("#contraseniaNueva").val("");
                $("#contraseniaConfirmar").val("");
                $("#contraseniaActual").val("");
                $("#contraseniaActualMD5").val("");
            }

            verificarValorDefecto("nroDocumento");

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
            if(data.integranteActivo == "1"){
                showDialogIntegranteActivo();
            }
            $('#msg_form_infoBasica .msg').html(lang['exito procesar']);
            $('#msg_form_infoBasica').addClass("correcto").fadeIn('slow');
        }
    }
};

function resetSelect(select, defaultOpt){
    if(select.length){
        select.addClass("disabled");
        select.html("");
        select.append(new Option(defaultOpt, '',true));
    }
}

//combo pais provincia ciudad form info contacto
function listaProvinciasByPais(idPais){
    resetSelect($('#ciudad'), 'Elija Ciudad:');
    if(idPais == ''){ 
        resetSelect($('#provincia'), 'Elija Provincia:');
        return;
    }else{
        $('#provincia').removeClass("disabled");
    }
    
    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(lista){
            $('#provincia').html("");

            if(lista.length != undefined && lista.length > 0){
                $('#provincia').append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#provincia').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#provincia').html(new Option('No hay provincias cargadas', '',true));
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia){
    if(idProvincia == ''){
        resetSelect($('#ciudad'), 'Elija Ciudad:');
        return;
    }else{
        $('#ciudad').removeClass("disabled");
    }
    $.ajax({
        type: "POST",
        url: "ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(lista){
            $('#ciudad').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#ciudad').append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ciudad').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#ciudad').append(new Option('No hay ciudades cargadas', '',true));
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
}

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
            if(data.integranteActivo == "1"){
                showDialogIntegranteActivo();
            }
            $('#msg_form_infoContacto .msg').html(lang['exito procesar']);
            $('#msg_form_infoContacto').addClass("correcto").fadeIn('slow');
        }
    }
};

//validacion y submit
var validateFormInfoProfesional = {
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
        cargoInstitucion:{required:function(element){
                                     return $("#institucionId").val() != "";
                                   }
                          },
        secundaria:{required:true},
        universidadCarrera:{required:function(element){
                                return $("#universidad").val() != "";
                           }},
        carreraFinalizada:{required:function(element){
                                return $("#universidad").val() != "";
                          }},
        sitioWeb:{url:true},
        especialidad:{required:true}
    },
    messages:{
        cargoInstitucion:mensajeValidacion("requerido"),
        secundaria:mensajeValidacion("requerido"),
        universidadCarrera:mensajeValidacion("requerido"),
        carreraFinalizada:mensajeValidacion("requerido"),
        sitioWeb:mensajeValidacion("url"),
        especialidad:mensajeValidacion("requerido")
    }
}

var optionsAjaxFormInfoProfesional = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'comunidad/datos-personales-procesar',

    beforeSerialize: function($form, options){
        if($("#formInfoProfesional").valid() == true){
            $('#msg_form_infoProfesional').hide();
            $('#msg_form_infoProfesional').removeClass("correcto").removeClass("error");
            $('#msg_form_infoProfesional .msg').html("");
            verificarValorDefecto("biografia"); //no envia el msg de ayuda en gris
            setWaitingStatus('formInfoProfesional', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInfoProfesional', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_infoProfesional .msg').html(lang['error procesar']);
            $('#msg_form_infoProfesional').addClass("error").fadeIn('slow');
        }else{
            if(data.integranteActivo == "1"){
                showDialogIntegranteActivo();
            }
            $('#msg_form_infoProfesional .msg').html(lang['exito procesar']);
            $('#msg_form_infoProfesional').addClass("correcto").fadeIn('slow');
        }
    }
};

function showDialogConfirmCerrarCuenta()
{
    var buttons = {
        "Confirmar": function(){
            //este es el dialog que confirma que la cuenta fue eliminada del sistema
            var buttonAceptar = { "Aceptar": function(){ $(this).dialog("close"); } }
            dialog = setWaitingStatusDialog(500, "Cerrar Cuenta", buttonAceptar);
            $.ajax({
                type:"post",
                dataType:'jsonp',
                url:"comunidad/cerrar-cuenta",
                success:function(data){
                    dialog.html(data.html);
                    if(data.success != undefined && data.success == 1){
                        $(".ui-dialog-buttonset .ui-button").click(function(){
                            //tendria que ser la url de logout para eliminar el usuario en sesion
                            location = data.redirect;
                        });
                    }
                }
            });
        },
        "Cancelar": function() {
            $(this).dialog( "close" );
        }
    }

    //este es el dialog que pide confirmar la accion
    var dialog = setWaitingStatusDialog(500, "Cerrar Cuenta", buttons);
    dialog.load(
        "comunidad/cerrar-cuenta",
        {confirmar:"1"},
        function(){}
    );
}

function cvUpload()
{
    if($('#cvUpload').length){
        //plugin ajax upload
        new Ajax_upload('#cvUpload',{
            action: 'comunidad/datos-personales-procesar',
            data: {seccion:'curriculum'},
            name: 'curriculum',
            onSubmit : function(file , ext){
                $('#msg_form_curriculum').hide();
                $('#msg_form_curriculum').removeClass("correcto").removeClass("error");
                $('#msg_form_curriculum .msg').html("");
                setWaitingStatus('formCurriculum', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete : function(file, response){
                setWaitingStatus('formCurriculum', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_curriculum .msg').html(lang['error procesar']);
                    $('#msg_form_curriculum').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si es satisfactorio el html devuelve el bloque de descarga

                //si rebota por accion desactivada o alguna de esas no tiene el formato de "0; mensaje mensaje mensaje"
                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_curriculum .msg').html(lang['error permiso']);
                    $('#msg_form_curriculum').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_curriculum .msg').html(html);
                    $('#msg_form_curriculum').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_curriculum .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_curriculum').addClass("correcto").fadeIn('slow');
                    $('#wrapCvActual').html(html);
                }
                return;
            }
        });
    }
}

function fotoUpload()
{    
    if($('#fotoUpload').length){
        new Ajax_upload('#fotoUpload', {
            action: 'comunidad/datos-personales-procesar',
            data: {seccion:'foto'},
            name: 'fotoPerfil',
            onSubmit:function(file , ext){
                $('#msg_form_fotoPerfil').hide();
                $('#msg_form_fotoPerfil').removeClass("correcto").removeClass("error");
                $('#msg_form_fotoPerfil .msg').html("");
                setWaitingStatus('formFotoPerfil', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('formFotoPerfil', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_fotoPerfil .msg').html(lang['error procesar']);
                    $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria, 2 = satisfactorio, paso a ser integrante activo
                var html = dataInfo[1]; //si se proceso bien aca queda el bloque del html con el nuevo thumbnail

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_fotoPerfil .msg').html(lang['error permiso']);
                    $('#msg_form_fotoPerfil').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_fotoPerfil .msg').html(html);
                    $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_fotoPerfil .msg').html(lang['exito procesar archivo']);
                    $('#contFotoPerfilActual').html(html);
                    $("a[rel^='prettyPhoto']").prettyPhoto(); //asocio el evento al html nuevo
                    $('#msg_form_fotoPerfil').addClass("correcto").fadeIn('slow');
                }
                return;
            }
        });
    }
}

function bindEventsFormInfoBasica()
{
    $("#formInfoBasica").validate(validateFormInfoBasica);
    $("#formInfoBasica").ajaxForm(optionsAjaxFormInfoBasica);

    //toggle modificar contrasenia
    $("#toggleContrasenia").click(function(){
        revelarElemento($("#contModificarPassword"));
    });
}

function bindEventsFormContacto()
{    
    $("#formInfoContacto").validate(validateFormInfoContacto);
    $("#formInfoContacto").ajaxForm(optionsAjaxFormInfoContacto);
        
    $("#pais").change(function(){
        listaProvinciasByPais($("#pais option:selected").val());
    });

    $("#provincia").change(function(){
        listaCiudadesByProvincia($("#provincia option:selected").val());
    });
}

function bindEventsFormProfesional()
{
    $("#biografia").maxlength();

    $("#formInfoProfesional").validate(validateFormInfoProfesional);
    $("#formInfoProfesional").ajaxForm(optionsAjaxFormInfoProfesional);
    
    if($("#institucionId").val() == ""){
        $('#contCargoInstitucion').addClass("disabled");
        $('#cargoInstitucion').attr('readonly', 'readonly');
    }else{
        $("#institucion").addClass("selected");
        $("#institucion").attr("readonly", "readonly");
        revelarElemento($('#institucion_clean'));
    }

    if($("#universidad").val() == ""){
        $('#universidadInfo').addClass("disabled");
        $('#universidadCarrera').attr('readonly', 'readonly');
        $('#carreraFinalizada').attr('readonly', 'readonly');
    }

    //para borrar la institucion seleccionada con el autocomplete
    $('#institucion_clean').click(function(){
        $("#institucion").removeClass("selected");
        $("#institucion").removeAttr("readonly");
        $("#institucion").val("");
        $("#institucionId").val("");
        $('#contCargoInstitucion').addClass("disabled");
        $('#cargoInstitucion').val("");
        ocultarElemento($(this));
    });

    $('#institucion').blur(function(){
        if($("#institucionId").val() == ""){
            $("#institucion").val("");
        }
        if($("#institucion").val() == ""){
            $("#institucionId").val("");
            $('#contCargoInstitucion').addClass("disabled");
            $('#cargoInstitucion').val("");
            $('#cargoInstitucion').attr('readonly', 'readonly');
        }
    });

    $('#universidad').blur(function(){
        if($("#universidad").val() == ""){
            $("#universidadCarrera").val("");
            $("#carreraFinalizada").val("");
            $('#universidadInfo').addClass("disabled");
            $('#universidadCarrera').attr('readonly', 'readonly');
            $('#carreraFinalizada').attr('readonly', 'readonly');
        }
    });

    $('#universidad').focus(function(){
        $('#universidadInfo').removeClass("disabled");
        $('#universidadCarrera').removeAttr('readonly');
        $('#carreraFinalizada').removeAttr('readonly');
    });

    $("#institucion").autocomplete({
        source:function(request, response){
            $.ajax({
                url: "comunidad/buscar-instituciones",
                dataType: "jsonp",
                data:{
                    limit:12,
                    str:request.term
                },
                beforeSend: function(){
                    revelarElemento($("#institucion_loading"));
                },
                success: function(data){
                    ocultarElemento($("#institucion_loading"));
                    response( $.map(data.instituciones, function(institucion){
                        return{
                            //lo que aparece en el input
                            value:institucion.nombre,
                            //lo que aparece en la lista generada para elegir
                            label:institucion.nombre,
                            //valor extra que se devuelve para completar el hidden
                            id:institucion.id
                        }
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui){
            if(ui.item){
                $("#institucionId").val(ui.item.id);
            }else{
                $("#institucionId").val("");
            }
        },
        close: function(){
            if($("#institucionId").val() != ""){
                $(this).addClass("selected");
                $(this).attr('readonly', 'readonly');
                revelarElemento($('#institucion_clean'));
                $('#contCargoInstitucion').removeClass("disabled");
                $('#cargoInstitucion').removeAttr('readonly');
            }
        }
    });
   
    cvUpload();
}
function bindEventsFormFoto()
{
    $("a[rel^='prettyPhoto']").prettyPhoto();
    fotoUpload();
}

$(document).ready(function(){

    if($("#formInfoBasica").length){
        bindEventsFormInfoBasica();
    }

    $("#privacidadEmail").change(function(){cambiarPrivacidad('email', $("#privacidadEmail option:selected").val());});
    $("#privacidadTelefonoContacto").change(function(){cambiarPrivacidad('telefono', $("#privacidadTelefonoContacto option:selected").val());});
    $("#privacidadMovil").change(function(){cambiarPrivacidad('celular', $("#privacidadMovil option:selected").val());});
    $("#privacidadFax").change(function(){cambiarPrivacidad('fax', $("#privacidadFax option:selected").val());});
    $("#privacidadCurriculum").change(function(){cambiarPrivacidad('curriculum', $("#privacidadCurriculum option:selected").val());});
    
    $("a[rel^='prettyPhoto']").prettyPhoto();

    $("#formMenu li a").live('click', function(){
        if($(this).hasClass("active")){return false}

        $("#formMenu li a.active").removeClass("active");
        $(this).addClass("active");
        
        var seccion = $(this).attr("rel");
        
        $.ajax({
            type: "POST",
            url: "comunidad/datos-personales",
            data:{
                seccion:seccion
            },
            beforeSend: function(){
                setWaitingStatus('formMenu', true);
                $("#formCont").html("");
            },
            success:function(data){
                setWaitingStatus('formMenu', false);

                $("#formCont").append(data);
                
                switch(seccion){
                case "basica":
                    bindEventsFormInfoBasica();
                    break;
                case "contacto":
                    bindEventsFormContacto();
                    break;
                case "profesional":
                    bindEventsFormProfesional();
                    break;
                case "foto":
                    bindEventsFormFoto();
                    break;
                }                
            }
        });
    });

    $("#cerrarCuenta").click(function(){
        showDialogConfirmCerrarCuenta();
        return false;
    });
});