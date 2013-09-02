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
        descripcion:{required:true},
        estimacion:{required:true}
    },
    messages:{
        objetivoAprendizaje: mensajeValidacion("requerido"),
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
        listaAreasByCiclo($("#ciclo option:selected").val());
    });
    $("#area").change(function(){
        listaEjesTematicosByArea($("#area option:selected").val());
    });
    $("#objetivoAprendizaje").change(function(){
        listaObjetivosAprendizajeByEjeTematico($("#objetivoAprendizaje option:selected").val());
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

//combos para el formulario de objetivo de aprendizaje
function listaCiclosByNivel(idNivel){

    //si el valor elegido es '' entonces marco como disabled
    if(idNivel == ''){
        $('#ciclo').addClass("disabled");
    }else{
        $('#ciclo').removeClass("disabled");
    }

    if($('#area').length){
        $('#area').addClass("disabled");
    }
    if($('#ejeTematico').length){
        $('#ejeTematico').addClass("disabled");
    }
    if($('#objetivoAprendizaje').length){
        $('#objetivoAprendizaje').addClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ciclos-por-niveles",
        data:{iNivelId:idNivel},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success:function(lista){
            $('#ciclo').html("");

            //los demas van vacios si es que estan en el formulario, se completan a medida que se seleccionan
            if($('#area').length){
                $('#area').html("");
                $('#area').html(new Option('Área:', '',true));
            }
            if($('#ejeTematico').length){
                $('#ejeTematico').html("");
                $('#ejeTematico').html(new Option('Eje:', '',true));
            }
            if($('#objetivoAprendizaje').length){
                $('#objetivoAprendizaje').html("");
                $('#objetivoAprendizaje').html(new Option('Objetivo Aprendizaje:', '',true));
            }

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

function listaAreasByCiclo(idCiclo){

    if(idCiclo == ''){
        $('#area').addClass("disabled");
    }else{
        $('#area').removeClass("disabled");
    }

    if($('#ejeTematico').length){
        $('#ejeTematico').addClass("disabled");
    }
    if($('#objetivoAprendizaje').length){
        $('#objetivoAprendizaje').addClass("disabled");
    }    
    
    $.ajax({
        type: "POST",
        url: "seguimientos/listar-areas-por-ciclos",
        data:{iCicloId:idCiclo},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){

            $('#area').html("");

            if($('#ejeTematico').length){
                $('#ejeTematico').html("");
                $('#ejeTematico').html(new Option('Eje:', '',true));
            }
            if($('#objetivoAprendizaje').length){
                $('#objetivoAprendizaje').html("");
                $('#objetivoAprendizaje').html(new Option('Objetivo Aprendizaje:', '',true));
            }

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
    if(idArea == ''){
        $('#ejeTematico').addClass("disabled");
    }else{
        $('#ejeTematico').removeClass("disabled");
    }

    if($('#objetivoAprendizaje').length){
        $('#objetivoAprendizaje').addClass("disabled");
    } 

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ejes-por-area",
        data:{iAreaId:idArea},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){

            $('#ejeTematico').html("");

            if($('#objetivoAprendizaje').length){
                $('#objetivoAprendizaje').html("");
                $('#objetivoAprendizaje').html(new Option('Objetivo Aprendizaje:', '',true));
            }

            if(lista.length != undefined && lista.length > 0){
                $('#ejeTematico').append(new Option('Eje:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ejeTematico').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#ejeTematico').append(new Option('No hay ejes cargados', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
        }
    });
}

function listaObjetivosAprendizajeByEjeTematico(idEje){
    if(idEje == ''){
        $('#objetivoAprendizaje').addClass("disabled");
    }else{
        $('#objetivoAprendizaje').removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-objetivos-aprendizaje-por-eje",
        data:{iEjeId:idEje},
        beforeSend: function(){
            setWaitingStatus("objetivoAprendizajeCont", true);
        },
        success: function(lista){

            $('#objetivoAprendizaje').html("");

            if(lista.length != undefined && lista.length > 0){
                $('#objetivoAprendizaje').append(new Option('Objetivo Aprendizaje:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#objetivoAprendizaje').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#objetivoAprendizaje').append(new Option('No hay objetivos cargados', '',true));
            }
            setWaitingStatus("objetivoAprendizajeCont", false);
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

    $("#asociarObjetivoAprendizaje").click(function(){
        var iSeguimientoId = $(this).attr('rel');
        var dialog = setWaitingStatusDialog(550, "Crear Objetivo");
        dialog.load(
            "seguimientos/form-objetivo",
            {"iSeguimientoId":iSeguimientoId,
             "objetivoAprendizaje":"1"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsFormObjetivoAprendizaje();
            }
        );
    });  
});