var validateFormUnidad = {
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
        nombre:{required:true},
        descripcion:{required:true},
        tipoEdicion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        tipoEdicion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormUnidad = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/guardar-unidad',
    beforeSerialize:function(){

        if($("#formUnidad").valid() == true){

            $('#msg_form_unidad').hide();
            $('#msg_form_unidad').removeClass("correcto").removeClass("error");
            $('#msg_form_unidad .msg').html("");
            setWaitingStatus('formUnidad', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formUnidad', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_unidad .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_unidad .msg').html(data.mensaje);
            }
            $('#msg_form_unidad').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_unidad .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_unidad .msg').html(data.mensaje);
            }
            if(data.agregarUnidad != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formUnidad').each(function(){
                  this.reset();
                });
            }

            //refresco el listado actual
            masUnidades();
            $('#msg_form_unidad').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsUnidadForm(){
    $("#formUnidad").validate(validateFormUnidad);
    $("#formUnidad").ajaxForm(optionsAjaxFormUnidad);
}

function masUnidades(){   
    $.ajax({
        type:"POST",
        url:"admin/unidades-procesar",
        data:{
            masUnidades:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoUnidades', true);
        },
        success:function(data){            
            $("#listadoUnidadesResult").html(data);
            //bindeo js del listado
            $('.datatable').dataTable();
            setWaitingStatus('listadoUnidades', false);
        }
    });
}

function eliminarUnidad(iUnidadId)
{
    var buttons = {
        "Confirmar": function(){
            //este es el dialog que confirma que la unidad fue eliminada del sistema
            var buttonAceptar = { "Aceptar": function(){ $(this).dialog("close"); } }
            dialog = setWaitingStatusDialog(500, "Borrar Unidad", buttonAceptar);
            $.ajax({
                type:"post",
                dataType:'jsonp',
                url:"admin/borrar-unidad",
                data:{
                    iUnidadId:iUnidadId
                },
                success:function(data){
                    dialog.html(data.html);
                    if(data.success != undefined && data.success == 1){
                        $(".ui-dialog-buttonset .ui-button").click(function(){
                            masUnidades();
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
    var dialog = setWaitingStatusDialog(500, 'Borrar Unidad', buttons);
    dialog.load(
        "admin/borrar-unidad",
        {mostrarDialogConfirmar:"1"},
        function(){}
    );
}

$(document).ready(function(){

    $("#crearUnidad").click(function(){
        var dialog = setWaitingStatusDialog(660, "Crear Unidad");
        dialog.load(
            "admin/form-crear-unidad",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsUnidadForm();
            }
        );
        return false;
    });

    $(".editarUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");
        var dialog = setWaitingStatusDialog(660, 'Editar Unidad');

        dialog.load(
            "admin/form-editar-unidad?unidadId="+iUnidadId,
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsUnidadForm();
            }
        );
    });
    
    $(".borrarUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");
        eliminarUnidad(iUnidadId);
    });
});