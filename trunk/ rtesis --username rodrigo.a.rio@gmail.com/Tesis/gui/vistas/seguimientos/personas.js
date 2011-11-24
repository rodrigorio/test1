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
        especialidad:{required:true}
    },
    messages:{
        cargoInstitucion:mensajeValidacion("requerido"),
        secundaria:mensajeValidacion("requerido"),
        universidadCarrera:mensajeValidacion("requerido"),
        carreraFinalizada:mensajeValidacion("requerido"),
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

$(document).ready(function(){
    $("a[rel^='prettyPhoto']").prettyPhoto();
});