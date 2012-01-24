//validacion y submit
var validateFormPersona = {
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
        nombre:{required:true},
        apellido:{required:true},
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true},
        pais:{required:true, digits: true},
        provincia:{required:function(element){
                            return $("#pais option:selected").val() != "";
                  }, digits: true},
        ciudad:{required:function(element){
                            return $("#provincia option:selected").val() != "";
               }, digits: true},
        tipoDocumento:{required:true},
        nroDocumento:{required:true, ignorarDefault:true, digits:true},
        telefono:{required:true}        
    },
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
                        required: "Debe ingresar numero de documento",
                        ignorarDefault: "Debe ingresar numero de documento",
                        digits: mensajeValidacion("digitos")
                      },
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        telefono: mensajeValidacion("requerido"),
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
        },
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
        }
    }
}

var optionsAjaxFormPersona = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/personas-procesar',

    beforeSerialize: function($form, options){
        if($("#formPersona").valid() == true){
            $('#msg_form_persona').hide();
            $('#msg_form_persona').removeClass("correcto").removeClass("error");
            $('#msg_form_persona .msg').html("");
            verificarValorDefecto("ocupacionPadre");
            verificarValorDefecto("ocupacionMadre");
            verificarValorDefecto("nombreHermanos");
            setWaitingStatus('formPersona', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formPersona', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_persona .msg').html(lang['error procesar']);
            $('#msg_form_persona').addClass("error").fadeIn('slow');
        }else{            
            if(data.agregarPersona == "1"){
                //si el submit fue para agregar una nueva persona al sistema...
                $('#msg_form_fotoPerfil .msg').html("La persona ha sido creada exitosamente. Puede agregar una foto de perfil si lo desea.");
                $('#msg_form_fotoPerfil').addClass("correcto").show();
                
                $('#tabFormularioPersona').html("");
                
            }else{
                //el submit fue la modificacion de una persona

            }

            $('#msg_form_persona').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventsPersonaForm(){

    $("#tabsFormPersona" ).tabs();
    
    $("#toggleContInfoExtra").click(function(){
        if($(this).attr("rel") == "open"){
            revelarElemento($("#contInfoExtra"));
            $(this).attr("rel", "close");
        }else{
            ocultarElemento($("#contInfoExtra"));
            $(this).attr("rel", "open");
        }
        return false;
    });

    if($("#institucionId").val() != ""){
        $("#institucion").addClass("selected");
        $("#institucion").attr("readonly", "readonly");
        revelarElemento($('#institucion_clean'));
    }

    $('#institucion').blur(function(){
        if($("#institucionId").val() == ""){
            $("#institucion").val("");
        }
        if($("#institucion").val() == ""){
            $("#institucionId").val("");
        }
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
            }
        }
    });

    //para borrar la institucion seleccionada con el autocomplete
    $('#institucion_clean').click(function(){
        $("#institucion").removeClass("selected");
        $("#institucion").removeAttr("readonly");
        $("#institucion").val("");
        $("#institucionId").val("");
        ocultarElemento($(this));
    });

    $("#formPersona").validate(validateFormPersona);
    $("#formPersona").ajaxForm(optionsAjaxFormPersona);
}



/*

//////////////////////////////////
// FORM FOTO PERFIL    ///////////
//////////////////////////////////

$(document).ready(function(){

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
*/