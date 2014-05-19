var validateFormEntrevista = {
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
        descripcion:{required:true}
    },
    messages:{
        descripcion:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormEntrevista = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/guardar-entrevista',
    beforeSerialize:function(){

        if($("#formEntrevista").valid() == true){

            $('#msg_form_entrevista').hide();
            $('#msg_form_entrevista').removeClass("correcto").removeClass("error");
            $('#msg_form_entrevista .msg').html("");
            setWaitingStatus('formEntrevista', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formEntrevista', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_entrevista .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_entrevista .msg').html(data.mensaje);
            }
            $('#msg_form_entrevista').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_entrevista .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_entrevista .msg').html(data.mensaje);
            }
            if(data.agregarEntrevista != undefined){
                $('#formEntrevista').each(function(){
                  this.reset();
                });

                //refresco el formulario de busqueda pero elimino el filtro
                $("#limpiarFiltro").click();
            }

            //refresco el listado actual
            $("#buscarEntrevistas").click();
            $('#msg_form_entrevista').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventsEntrevistaForm(){
    $("#formEntrevista").validate(validateFormEntrevista);
    $("#formEntrevista").ajaxForm(optionsAjaxFormEntrevista);
}

function masEntrevistas(){

    var filtroDescripcionEntrevista = $('#filtroDescripcionEntrevista').val();

    $.ajax({
        type:"POST",
        url:"seguimientos/entrevistas-procesar",
        data:{
            masEntrevistas:"1",
            filtroDescripcionEntrevista: filtroDescripcionEntrevista
        },
        beforeSend: function(){
            setWaitingStatus('listadoEntrevistas', true);
        },
        success:function(data){
            setWaitingStatus('listadoEntrevistas', false);
            $("#listadoEntrevistasResult").html(data);
        }
    });
}

function eliminarEntrevista(iEntrevistaId)
{
    var buttons = {
        "Confirmar": function(){
            //este es el dialog que confirma que la entrevista fue eliminada del sistema
            var buttonAceptar = { "Aceptar": function(){ $(this).dialog("close"); } }
            dialog = setWaitingStatusDialog(500, "Borrar Entrevista", buttonAceptar);
            $.ajax({
                type:"post",
                dataType:'jsonp',
                url:"seguimientos/borrar-entrevista",
                data:{
                    iEntrevistaId:iEntrevistaId
                },
                success:function(data){
                    dialog.html(data.html);
                    if(data.success != undefined && data.success == 1){
                        $(".ui-dialog-buttonset .ui-button").click(function(){
                            $("#buscarEntrevistas").click();
                        });
                    }
                }
            });
        },
        "Cancelar": function() {
            $(this).dialog( "close" );
        }
    }

    var dialog = setWaitingStatusDialog(550, 'Borrar Entrevista', buttons);
    dialog.load(
        "seguimientos/borrar-entrevista",
        {mostrarDialogConfirmar:"1"},
        function(){}
    );
}

$(document).ready(function(){
    $("#buscarEntrevistas").live('click', function(){
        masEntrevistas();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarEntrevistas').each(function(){
          this.reset();
        });
        return false;
    });

    $("#crearEntrevista").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear Entrevista");
        dialog.load(
            "seguimientos/form-crear-entrevista",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsEntrevistaForm();
            }
        );
        return false;
    });

    $(".editarEntrevista").live('click', function(){
        var iEntrevistaId = $(this).attr("rel");
        var dialog = setWaitingStatusDialog(550, 'Editar Entrevista');

        dialog.load(
            "seguimientos/form-editar-entrevista?entrevistaId="+iEntrevistaId,
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsEntrevistaForm();
            }
        );
    });

    $(".verSeguimientosEntrevista").live('click', function(){
        var iEntrevistaId = $(this).attr("rel");
        var dialog = setWaitingStatusDialog(550, 'Seguimientos asociados');
        dialog.load(
            "seguimientos/entrevistas-procesar",
            {
                verSeguimientos:"1",
                iEntrevistaId:iEntrevistaId
            },
            function(responseText, textStatus, XMLHttpRequest){}
        );
    });

    $(".borrarEntrevista").live('click', function(){
        var iEntrevistaId = $(this).attr("rel");
        eliminarEntrevista(iEntrevistaId);
    });
});
