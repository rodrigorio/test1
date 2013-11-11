var validateFormUnidad = {
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
        descripcion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormUnidad = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/guardar-unidad',
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
            $('#msg_form_unidad').addClass("error").fadeIn('slow');
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

                //refresco el formulario de busqueda pero elimino el filtro
                $("#limpiarFiltro").click();
            }
            //refresco el listado actual
            $("#buscarUnidades").click();
            $('#msg_form_unidad').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventsUnidadForm(){
    $("#formUnidad").validate(validateFormUnidad);
    $("#formUnidad").ajaxForm(optionsAjaxFormUnidad);
}

function masUnidades(){

    var filtroNombreUnidad = $('#filtroNombreUnidad').val();

    $.ajax({
        type:"POST",
        url:"seguimientos/unidades-procesar",
        data:{
            masUnidades:"1",
            filtroNombreUnidad: filtroNombreUnidad
        },
        beforeSend: function(){
            setWaitingStatus('listadoUnidades', true);
        },
        success:function(data){
            setWaitingStatus('listadoUnidades', false);
            $("#listadoUnidadesResult").html(data);
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
                url:"seguimientos/borrar-unidad",
                data:{
                    iUnidadId:iUnidadId
                },
                success:function(data){
                    dialog.html(data.html);
                    if(data.success != undefined && data.success == 1){
                        $(".ui-dialog-buttonset .ui-button").click(function(){
                            //se borro la unidad, refresco el listado con el filtro actual como esta
                            $("#buscarUnidades").click();
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
    var dialog = setWaitingStatusDialog(550, 'Borrar Unidad', buttons);
    dialog.load(
        "seguimientos/borrar-unidad",
        {mostrarDialogConfirmar:"1"},
        function(){}
    );
}

var validateFormCrearEntradaUnidadEsporadica = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){},
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        dFecha:{required:true}
    }
};

var optionsAjaxFormCrearEntradaUnidadEsporadica = {
    dataType: 'jsonp',
    resetForm: false,
    url:'seguimientos/entradas/crear',
    beforeSerialize:function(){        
        if($("#formCrearEntradaUnidadEsporadica").valid() == true){
            $('#msg_form_crearEntradaUnidadEsporadica').hide();
            $('#msg_form_crearEntradaUnidadEsporadica').removeClass("correcto").removeClass("error");
            $('#msg_form_crearEntradaUnidadEsporadica .msg').html("");
            setWaitingStatus('formCrearEntradaUnidadEsporadica', true, "16");
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCrearEntradaUnidadEsporadica', false, "16");
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_crearEntradaUnidadEsporadica .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_crearEntradaUnidadEsporadica .msg').html(data.mensaje);
            }
            $('#msg_form_crearEntradaUnidadEsporadica').addClass("error").fadeIn('slow');
        }else{
            //cierro el dialog actual, abro otro con el mensaje de confirmacion y en el aceptar redirecciono
            //al form de edicion de unidad
            var buttonAceptar = {"Aceptar": function(){$(this).dialog("close");}}
            dialog = setWaitingStatusDialog(500, "Crear Entrada", buttonAceptar);
            dialog.html(data.html);
            if(data.success != undefined && data.success == 1){
                $(".ui-dialog-buttonset .ui-button").click(function(){
                    //ampliar entrada creada para editar por primera vez.
                    location = data.redirect;
                });
            }
        }
    }
};

function bindEventsCrearEntradaUnidadEsporadicaForm(){
    var ultimaEntrada = $("#fechaUltimaEntrada").html();
    if(ultimaEntrada != undefined && ultimaEntrada != ""){
        ultimaEntrada = new Date(ultimaEntrada);
        //le sumo 1 dia
        var fromDate = new Date(ultimaEntrada.getFullYear(), ultimaEntrada.getMonth(), ultimaEntrada.getDate()+1);
    }    
    $("#fechaFormUnidadEsporadica").datepicker({
        minDate:fromDate,
        maxDate:new Date
    });

    $(".tooltip").tooltip();

    $("#formCrearEntradaUnidadEsporadica").validate(validateFormCrearEntradaUnidadEsporadica);
    $("#formCrearEntradaUnidadEsporadica").ajaxForm(optionsAjaxFormCrearEntradaUnidadEsporadica);
}

$(document).ready(function(){
    $("#buscarUnidades").live('click', function(){
        masUnidades();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarUnidades').each(function(){
          this.reset();
        });
        return false;
    });

    $("#crearUnidad").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear Unidad");
        dialog.load(
            "seguimientos/form-crear-unidad",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsUnidadForm();
            }
        );
        return false;
    });

    $(".editarUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");
        var dialog = setWaitingStatusDialog(550, 'Editar Unidad');

        dialog.load(
            "seguimientos/form-editar-unidad?unidadId="+iUnidadId,
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsUnidadForm();
            }
        );
    });
    
    $(".verSeguimientosUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");
        var dialog = setWaitingStatusDialog(550, 'Seguimientos asociados');
        dialog.load(
            "seguimientos/unidades-procesar",
            {
                verSeguimientos:"1",
                iUnidadId:iUnidadId
            },
            function(responseText, textStatus, XMLHttpRequest){}
        );
    });

    $(".borrarUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");
        eliminarUnidad(iUnidadId);
    });
});