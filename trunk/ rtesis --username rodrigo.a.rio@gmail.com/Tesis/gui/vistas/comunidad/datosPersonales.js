//funcion para el dialog si paso a ser integrante activo
function showDialogIntegranteActivo(){
    var dialog = $("#dialog");
    if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Comunidad SGPAPD"></div>').appendTo('body'); }

    dialog.load(
        "comunidad/datos-personales-procesar",
        {seccion:'dialogIntegranteActivo'},
        function(data){
            dialog.dialog({
                position:['center',5],
                width:650,
                resizable:false,
                draggable:false,
                modal:true,
                closeOnEscape:true
            });
        }
    );
}

////////////////////////////////////
// MODIFICAR PRIVACIDAD
//////////////////////////////////

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

$("#privacidadEmail").change(function(){cambiarPrivacidad('email', $("#privacidadEmail option:selected").val());});
$("#privacidadTelefonoContacto").change(function(){cambiarPrivacidad('telefono', $("#privacidadTelefonoContacto option:selected").val());});
$("#privacidadMovil").change(function(){cambiarPrivacidad('celular', $("#privacidadMovil option:selected").val());});
$("#privacidadFax").change(function(){cambiarPrivacidad('fax', $("#privacidadFax option:selected").val());});
$("#privacidadCurriculum").change(function(){cambiarPrivacidad('curriculum', $("#privacidadCurriculum option:selected").val());});

//////////////////////////////////
// FORM INFO BASICA
//////////////////////////////////

//toggle modificar contrasenia
$("#toggleContrasenia").click(function(){
    revelarElemento($("#contModificarPassword"));
    return false;
});

// hints formularios
$("#contraseniaNueva").live("focus", function(){
    revelarElemento($("#hintContraseniaNueva"));
});
$("#contraseniaNueva").live("blur", function(){
    ocultarElemento($("#hintContraseniaNueva"));
});

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
$("#formInfoBasica").ajaxForm(optionsAjaxFormInfoBasica);


//////////////////////////////////
// FORM INFO CONTACTO
//////////////////////////////////

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

$("#pais").change(function(){listaProvinciasByPais($("#pais option:selected").val());});
$("#provincia").change(function(){listaCiudadesByProvincia($("#provincia option:selected").val());});

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
            if(data.integranteActivo == "1"){
                showDialogIntegranteActivo();
            }
            $('#msg_form_infoContacto .msg').html(lang['exito procesar']);
            $('#msg_form_infoContacto').addClass("correcto").fadeIn('slow');
        }
    }
};
$("#formInfoContacto").ajaxForm(optionsAjaxFormInfoContacto);



//////////////////////////////////
// FORM INFO PROFESIONAL
//////////////////////////////////

//para el estado inicial del formulario
$(document).ready(function(){
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
        cargoInstitucion:{required:true},
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
$("#formInfoProfesional").validate(validateFormInfoProfesional);

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
$("#formInfoProfesional").ajaxForm(optionsAjaxFormInfoProfesional);


$(document).ready(function(){

    //////////////////////////////////
    // FORM CURRICULUM VITAE /////////
    //////////////////////////////////

    //esto es porque depende la subseccion puede tirar error si el boton no esta
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
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria, 2 = satisfactorio, paso a ser integrante activo
                var html = dataInfo[1]; //si es satisfactorio el html devuelve el bloque de descarga

                //si rebota por accion desactivada o alguna de esas no tiene el formato de "0; mensaje mensaje mensaje"
                if(resultado != "0" && resultado != "1" && resultado != "2"){
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
                    
                    if(resultado == "2"){
                        alert("Pasaste a ser Integrante activo");
                        //redireccionar a pagina de bienvenida para integrantes activos
                        //en esa pagina tirar bleble y link a pagina de manual de usuario
                    }
                }
                return;
            }
        });
    }
   
    //////////////////////////////////
    // FORM FOTO PERFIL    ///////////
    //////////////////////////////////

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
});

function cerrarCuenta(){
    alert("asldkjaskldjaslkd");
}
function showDialogConfirmCerrarCuenta(){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $("<div id='dialog' title='Cerrar Cuenta'></div>").appendTo('body');
    
    dialog.load(
        "comunidad/cerrar-cuenta",
        {step:"confirmar"},
        function(data){
            dialog.dialog({
                position:['center',5],
                width:400,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:true,
                buttons:{
                    "Confirmar": function() {
                        cerrarCuenta();
                    },
                    "Cancelar": function() {
                        $(this).dialog( "close" );
                    }
                }
            });
        }
    );
}

$(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto();

    $("#cerrarCuenta").click(function(){
        showDialogCerrarCuenta("1");
    });
});