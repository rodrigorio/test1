var validateFormEnviarInvitacion = {
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
        email:{required:true, email:true},
        relacion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email:{
            required: mensajeValidacion("requerido"),
            email: mensajeValidacion("email")
        },
        relacion:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormEnviarInvitacion = {
    dataType: 'jsonp',
    resetForm: false,
    url:"comunidad/invitacion-procesar",

    beforeSerialize:function($form, options){
        if($("#formEnviarInvitacion").valid() == true){
            $('#msg_form_enviarInvitacion').hide();
            $('#msg_form_enviarInvitacion').removeClass("correcto").removeClass("error");
            $('#msg_form_enviarInvitacion .msg').html("");

            setWaitingStatus('formEnviarInvitacion', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formEnviarInvitacion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_enviarInvitacion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_enviarInvitacion .msg').html(data.mensaje);
            }
            $('#msg_form_enviarInvitacion').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_enviarInvitacion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_enviarInvitacion .msg').html(data.mensaje);
            }
            $('#msg_form_enviarInvitacion').addClass("correcto").fadeIn('slow');

            //actualizo cant invitaciones disponibles
            $('#invitacionesDisponibles').html(data.cantidadInvitaciones);
        }

        //limpio el form
        $('#formEnviarInvitacion').each(function(){
            this.reset();
        });
    }
};

function bindEventsFormEnviarInvitacion()
{
    $("#formEnviarInvitacion").validate(validateFormEnviarInvitacion);
    $("#formEnviarInvitacion").ajaxForm(optionsAjaxFormEnviarInvitacion);

    $("textarea.maxlength").maxlength();
}

function enviarInvitacion(){
    $.ajax({
        type:"post",
        url:"comunidad/nueva-invitacion",
        success:function(data){

            var dialog = $("#dialog");
            if($("#dialog").length){
                dialog.attr("title", "Enviar invitación");
            }else{
                dialog = $('<div id="dialog" title="Enviar invitación"></div>').appendTo('body');
            }
            dialog.html(data);

            dialog.dialog({
                position:['center', 'center'],
                width:500,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:true
            });

            bindEventsFormEnviarInvitacion();
        }
    });
}

$(function(){
    $(".enviarInvitacion").live('click', function(){
        enviarInvitacion();
        return false;
    });
});