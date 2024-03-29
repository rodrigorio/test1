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

var validateFormObjetivoAprendizaje = {
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
        objetivoAprendizaje:{required:true},
        relevancia:{required:true},
        estimacion:{required:true}
    },
    messages:{
        objetivoAprendizaje: mensajeValidacion("requerido"),
        relevancia: mensajeValidacion("requerido"),
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
                resetObjetivosAprendizaje();
            }
            
            //refresco el listado actual
            $("#orderByRelevancia").click();
            $('#msg_form_objetivo').addClass("correcto").fadeIn('slow');
        }
    }
};

var validateFormRecronograma = {
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
        estimacion:{required:true}
    },
    messages:{
        estimacion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormRecronograma = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/objetivos-procesar',
    beforeSerialize:function(){

        if($("#formRecronograma").valid() == true){

            $('#msg_form_recronograma').hide();
            $('#msg_form_recronograma').removeClass("correcto").removeClass("error");
            $('#msg_form_recronograma .msg').html("");
            setWaitingStatus('formRecronograma', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formRecronograma', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_recronograma .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_recronograma .msg').html(data.mensaje);
            }
            $('#msg_form_recronograma').addClass("error").fadeIn('slow');
        }else{
            $("#dialog").dialog("close");
            //refresco el listado actual
            $("#orderByRelevancia").click();            
        }
    }
};

function bindEventsFormObjetivoPersonalizado(){
    $("#formObjetivo").validate(validateFormObjetivoPersonalizado);
    $("#formObjetivo").ajaxForm(optionsAjaxFormObjetivo);
        
    $("#descripcion").maxlength();

    //solo permitido fechas a partir del dia posterior al actual
    var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(today.getDate()+1);
    $("#estimacion").datepicker({
        minDate: tomorrow
    });
}

function bindEventsFormObjetivoAprendizaje(){
    $("#formObjetivo").validate(validateFormObjetivoAprendizaje);
    $("#formObjetivo").ajaxForm(optionsAjaxFormObjetivo);

    //selects objetivo aprendizaje
    $("#nivel").change(function(){
        listaCiclosByNivel($("#nivel option:selected").val());
    });
    $("#ciclo").change(function(){
        listaAniosByCiclo($("#ciclo option:selected").val());
    });
    $("#anio").change(function(){
        listaAreasByAnio($("#anio option:selected").val());
    });
    $("#area").change(function(){
        listaEjesTematicosByArea($("#area option:selected").val());
    });
    $("#eje").change(function(){
        listaObjetivosAprendizajeByEjeTematico($("#eje option:selected").val());
    });

    $("#descripcion").maxlength();

    //solo permitido fechas a partir del dia posterior al actual
    var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(today.getDate()+1);
    $("#estimacion").datepicker({
        minDate: tomorrow
    });
}

function bindEventsFormCalendarEdit()
{
    $("#formRecronograma").validate(validateFormRecronograma);
    $("#formRecronograma").ajaxForm(optionsAjaxFormRecronograma);

    var today = new Date();
    var tomorrow = new Date();
    tomorrow.setDate(today.getDate()+1);
    $("#estimacion").datepicker({
        minDate: tomorrow
    });
}

function resetSelect(select, defaultOpt){
    if(select.length){
        select.addClass("disabled");
        select.html("");
        select.append(new Option(defaultOpt, '',true));
    }
}
function resetObjetivosAprendizaje(){
    $('#objetivosAprendizajeCont').html("");
    $('#objetivoAprendizaje').val("");
}

//combos para el formulario de objetivo de aprendizaje
function listaCiclosByNivel(idNivel){
    resetSelect($('#anio'), 'Año:');
    resetSelect($('#area'), 'Área:');
    resetSelect($('#eje'), 'Eje:');
    resetObjetivosAprendizaje();

    //si el valor elegido es '' entonces marco como disabled
    if(idNivel == ''){
        resetSelect($('#ciclo'), 'Ciclo:');
        return;
    }else{
        $('#ciclo').removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ciclos-por-nivel",
        data:{iNivelId:idNivel},
        beforeSend:function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success:function(lista){
            $('#ciclo').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#ciclo').append(new Option('Ciclo:', '',true));
                for(var i=0; i<lista.length; i++){
                    $('#ciclo').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
                $('#ciclo').removeClass("disabled");
            }else{
                $('#ciclo').html(new Option('No hay ciclos cargados', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
        }
    });
}

function listaAniosByCiclo(idCiclo){
    resetSelect($('#area'), 'Área:');
    resetSelect($('#eje'), 'Eje:');
    resetObjetivosAprendizaje();
    if(idCiclo == ''){
        resetSelect($('#anio'), 'Año:');
        return;
    }else{
        $('#anio').removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-anios-por-ciclo",
        data:{iCicloId:idCiclo},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){
            $('#anio').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#anio').append(new Option('Año:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#anio').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#anio').append(new Option('No hay años cargados', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
        }
    });
}

function listaAreasByAnio(idAnio){
    resetSelect($('#eje'), 'Eje:');
    resetObjetivosAprendizaje();
    if(idAnio == ''){
        resetSelect($('#area'), 'Área:');
        return;
    }else{
        $('#area').removeClass("disabled");
    }
    
    $.ajax({
        type: "POST",
        url: "seguimientos/listar-areas-por-anio",
        data:{idAnio:idAnio},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){
            $('#area').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#area').append(new Option('Área:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#area').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#area').append(new Option('No hay áreas cargadas', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
        }
    });
}

function listaEjesTematicosByArea(idArea){
    resetObjetivosAprendizaje();
    if(idArea == ''){
        resetSelect($('#eje'), 'Eje:');
        return;
    }else{
        $('#eje').removeClass("disabled");
    }
    
    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ejes-por-area",
        data:{iAreaId:idArea},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){
            $('#eje').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#eje').append(new Option('Eje:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#eje').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#eje').append(new Option('No hay ejes cargados', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
        }
    });
}

function listaObjetivosAprendizajeByEjeTematico(idEje){
    resetObjetivosAprendizaje();
    if(idEje == ''){
        return;
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-objetivos-aprendizaje-por-eje",
        data:{iEjeId:idEje},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){
            $('#objetivosAprendizajeCont').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#objetivosAprendizajeCont').html(lista);
            }else{
                $('#objetivosAprendizajeCont').html('No hay objetivos cargados');
            }
            setWaitingStatus("objetivoAprendizajeCont", false);

            $("#objetivosAprendizajeCont .obj_aprendizaje").each(function(i, el) {
                $(el).css("cursor", "pointer");
                $(el).live('click',function(ev) {
                    $("#objetivosAprendizajeCont .celdaActiva").removeClass('celdaActiva');
                    $(this).toggleClass('celdaActiva');
                    $('#objetivoAprendizaje').val( $(this).attr('id'));
                    $('#formObjetivo [for="objetivoAprendizaje"]').css("display","none");
            });
    });
        }
    });
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

function eliminarObjetivo(iObjetivoId, iSeguimientoId, tipoObjetivo){
    if(confirm("Se borrara el objetivo junto al historial de evolución de forma permanente, desea continuar?")){

        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"seguimientos/objetivos-procesar",
            data:{
                eliminarObjetivo:"1",
                iObjetivoId:iObjetivoId,
                iSeguimientoId:iSeguimientoId,
                tipoObjetivo:tipoObjetivo
            },
            beforeSend: function(){
                setWaitingStatus('objetivo_'+iObjetivoId+'_'+iSeguimientoId, true);
            },
            success:function(data){
                setWaitingStatus('objetivo_'+iObjetivoId+'_'+iSeguimientoId, false);

                if(data.success != undefined && data.success == 1){
                    //remuevo la ficha
                    $('#objetivo_'+iObjetivoId+'_'+iSeguimientoId).hide("slow", function(){
                        $('#objetivo_'+iObjetivoId+'_'+iSeguimientoId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){ dialog.remove(); }
                dialog = $('<div id="dialog" title="Borrar Objetivo"></div>').appendTo('body');
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

$(function(){
    
    $(".orderLink").live('click', function(){
        $('#sOrderBy').html($(this).attr('orderBy'));
        $('#sOrder').html($(this).attr('order'));
        var iSeguimientoId = $(this).attr('rel');
        masObjetivos(iSeguimientoId);
    });

    $(".borrarObjetivo").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var iObjetivoId = rel[0];
        var iSeguimientoId = rel[1];
        var tipoObjetivo = rel[2];

        eliminarObjetivo(iObjetivoId, iSeguimientoId, tipoObjetivo);
    });
    
    $(".verObjetivo").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var iObjetivoId = rel[0];
        var iSeguimientoId = rel[1];
        var tipoObjetivo = rel[2];

        var dialog = setWaitingStatusDialog(550, "Ver Objetivo");
        dialog.load(
            "seguimientos/ver-objetivo",
            {iSeguimientoId:iSeguimientoId,
             iObjetivoId:iObjetivoId,
             tipoObjetivo:tipoObjetivo},
            function(responseText, textStatus, XMLHttpRequest){
                $(".tooltip").tooltip();
            }
        );
    });
    
    $("#crearObjetivoPersonalizado").click(function(){
        var iSeguimientoId = $(this).attr('rel');
        var dialog = setWaitingStatusDialog(550, "Crear Objetivo");
        dialog.load(
            "seguimientos/form-objetivo",
            {iSeguimientoId:iSeguimientoId,
             tipoObjetivo:"ObjetivoPersonalizado"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsFormObjetivoPersonalizado();               
            }
        );
    });

    $("#asociarObjetivoAprendizaje").click(function(){
        var iSeguimientoId = $(this).attr('rel');
        var dialog = setWaitingStatusDialog(550, "Asociar nuevo Objetivo Aprendizaje");
        dialog.load(
            "seguimientos/form-objetivo",
            {iSeguimientoId:iSeguimientoId,
             tipoObjetivo:"ObjetivoAprendizaje"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsFormObjetivoAprendizaje();
            }
        );
    });

    $(".editarObjetivo").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var iObjetivoId = rel[0];
        var iSeguimientoId = rel[1];
        var tipoObjetivo = rel[2];
        
        var dialog = setWaitingStatusDialog(550, 'Editar Objetivo');

        dialog.load(
            "seguimientos/form-objetivo",
            {
                iObjetivoId:iObjetivoId,
                iSeguimientoId:iSeguimientoId,
                tipoObjetivo:tipoObjetivo
            },
            function(responseText, textStatus, XMLHttpRequest){
                if(tipoObjetivo == 'ObjetivoAprendizaje'){
                    bindEventsFormObjetivoPersonalizado();
                }
                if(tipoObjetivo == 'ObjetivoPersonalizado'){
                    bindEventsFormObjetivoAprendizaje();
                }
            }
        );
    });

    $(".toggleActivo").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var iObjetivoId = rel[0];
        var iSeguimientoId = rel[1];
        var tipoObjetivo = rel[2];
        var bActivo = rel[3];
        
        $.ajax({
            url: "seguimientos/objetivos-procesar",
            type: "POST",
            data:{
                iSeguimientoId:iSeguimientoId,
                iObjetivoId:iObjetivoId,
                tipoObjetivo:tipoObjetivo,
                bActivo:bActivo,
                toggleActivo:"1"
            },
            beforeSend: function(){
                setWaitingStatus('listadoObjetivos', true);
            },
            success:function(data){
                setWaitingStatus('listadoObjetivos', false);
                if(data.success != undefined && data.success == 1){
                    masObjetivos(iSeguimientoId);
                }
            }
        });
    });

    //para shortcut de edicion de fecha de estimacion haciendo click en calendar
    $(".calendarEdit").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var iObjetivoId = rel[0];
        var iSeguimientoId = rel[1];
        var tipoObjetivo = rel[2];
        
        var dialog = setWaitingStatusDialog(300, 'Recronograma');

        dialog.load(
            "seguimientos/objetivos-procesar",
            {
                iObjetivoId:iObjetivoId,
                iSeguimientoId:iSeguimientoId,
                tipoObjetivo:tipoObjetivo,
                recronogramaForm:"1"
            },
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsFormCalendarEdit();
            }
        );
    });
});