function eliminarParametro(iParametroId){
    if(confirm("Se borrara la accion del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/acciones-perfil-procesar",
            data:{
                iAccionId:iAccionId,
                eliminarAccion:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+iAccionId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Accion");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Accion"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function cambiarEstadoAccion(iAccionId, valor){
    $.ajax({
        type: "POST",
        url: "admin/acciones-perfil-procesar",
        data:{
            iAccionId:iAccionId,
            estadoAccion:valor,
            cambiarEstadoAccion:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoAcciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoAcciones', false);
        }
    });
}

var validateFormAccion = {
    errorElement: "span",
    validClass: "valid-side-note",
    errorClass: "invalid-side-note",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    highlight: function(element, errorClass, validClass){
        $(element).addClass("invalid");
    },
    unhighlight: function(element, errorClass, validClass){
        $(element).removeClass("invalid");
    },
    rules:{
        modulo:{required:true},
        controlador:{required:true},
        accion:{required:true},
        perfil:{required:true},
        activo:{required:true}
    },
    messages:{
        modulo: mensajeValidacion("requerido"),
        controlador: mensajeValidacion("requerido"),
        accion: mensajeValidacion("requerido"),
        perfil: mensajeValidacion("requerido"),
        activo: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormAccion = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/acciones-perfil-procesar',

    beforeSerialize: function($form, options){
        if($("#formAccion").valid() == true){            
            $('#msg_form_accion').hide();
            $('#msg_form_accion').removeClass("success").removeClass("error2");
            $('#msg_form_accion .msg').html("");
            setWaitingStatus('formAccion', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formAccion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_accion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_accion .msg').html(data.mensaje);
            }
            $('#msg_form_accion').addClass("error2").fadeIn('slow');
        }else{
            if(data.crearAccion != undefined){
                //si el submit fue para agregar una nueva persona al sistema...
                if(data.mensaje == undefined){
                    $('#msg_form_accion .msg').html("La accion fue agregada exitosamente al sistema");
                }else{
                    $('#msg_form_accion .msg').html(data.mensaje);
                }
            }else{
                //el submit fue la modificacion de una persona
                if(data.mensaje == undefined){
                    $('#msg_form_accion .msg').html("La accion fue modificada exitosamente");
                }else{
                    $('#msg_form_accion .msg').html(data.mensaje);
                }                
            }
            $('#msg_form_accion').addClass("success").fadeIn('slow');
        }
    }
};

$(document).ready(function(){
    $(".eliminarAccion").live("click", function(){
        var iAccionId = $(this).attr("rel");
        eliminarAccion(iAccionId);
    });

    $(".cambiarEstadoAccion").live("change", function(){
        var iAccionId = $(this).attr("rel");
        cambiarEstadoAccion(iAccionId, $("#estadoAccion_"+iAccionId+" option:selected").val());
    });

    $("#formAccion").validate(validateFormAccion);
    $("#formAccion").ajaxForm(optionsAjaxFormAccion);
});