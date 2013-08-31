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

function bindEventsObjetivoPersonalizadoForm(){
    $("#formUnidad").validate(validateFormUnidad);
    $("#formUnidad").ajaxForm(optionsAjaxFormUnidad);
}

function masObjetivos(iSeguimientoId){

    var sOrderBy = $('#sOrderBy').html();
    var sOrder = $('#sOrder').html();

    $.ajax({
        url: "seguimientos/objetivos-procesar",
        type: "POST",
        data:{
            iSeguimientoId:iSeguimientoId,
            masObjetivos:"1",
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoObjetivos', true);
        },
        success:function(data){
            setWaitingStatus('listadoObjetivos', false);
            $("#listadoObjetivosResult").html(data);
        }
    });
}

$(document).ready(function(){

    $(".orderLink").live('click', function(){
        $('#sOrderBy').html($(this).attr('orderBy'));
        $('#sOrder').html($(this).attr('order'));
        var iSeguimientoId = $(this).attr('rel');
        masObjetivos(iSeguimientoId);
    });
    
});