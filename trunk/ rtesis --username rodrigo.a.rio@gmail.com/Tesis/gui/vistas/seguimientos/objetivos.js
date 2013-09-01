var validateFormObjetivoPersonalizado = {
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
        eje:{required:true},
        relevancia:{required:true},
        descripcion:{required:true},
        estimacion:{required:true}
    },
    messages:{
        eje: mensajeValidacion("requerido"),
        relevancia: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        estimacion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormObjetivo = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/guardar-objetivo',
    beforeSerialize:function(){

        if($("#formObjetivo").valid() == true){

            $('#msg_form_objetivo').hide();
            $('#msg_form_objetivo').removeClass("correcto").removeClass("error");
            $('#msg_form_objetivo .msg').html("");
            setWaitingStatus('formObjetivo', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formObjetivo', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_objetivo .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_objetivo .msg').html(data.mensaje);
            }
            $('#msg_form_objetivo').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_objetivo .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_objetivo .msg').html(data.mensaje);
            }
            if(data.agregarObjetivo != undefined){                
                $('#formObjetivo').each(function(){
                  this.reset();
                });
            }
            
            //refresco el listado actual
            $("#orderByRelevancia").click();
            $('#msg_form_objetivo').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventsFormObjetivoPersonalizado(){
    $("#formObjetivo").validate(validateFormObjetivoPersonalizado);
    $("#formObjetivo").ajaxForm(optionsAjaxFormObjetivo);
        
    $("#descripcion").maxlength();
    $("#estimacion").datepicker();
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

$(function(){

    $(".orderLink").live('click', function(){
        $('#sOrderBy').html($(this).attr('orderBy'));
        $('#sOrder').html($(this).attr('order'));
        var iSeguimientoId = $(this).attr('rel');
        masObjetivos(iSeguimientoId);
    });

    $("#crearObjetivoPersonalizado").click(function(){
        var iSeguimientoId = $(this).attr('rel');
        var dialog = setWaitingStatusDialog(550, "Crear Objetivo");
        dialog.load(
            "seguimientos/form-objetivo",
            {"iSeguimientoId":iSeguimientoId,
             "objetivoPersonalizado":"1"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsFormObjetivoPersonalizado();               
            }
        );
    });      
});