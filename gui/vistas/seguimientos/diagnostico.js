var validateFormDiagnosticoPersonalizado = {
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
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormDiagnosticoPersonalizado = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-diagnostico',
    beforeSerialize:function(){

        if($("#formDiagnosticoPersonalizado").valid() == true){
            $('#msg_form_diagnosticoPersonalizado').hide();
            $('#msg_form_diagnosticoPersonalizado').removeClass("correcto").removeClass("error");
            $('#msg_form_diagnosticoPersonalizado .msg').html("");
            setWaitingStatus('formDiagnosticoPersonalizado', true);
        }else{
            return false;
        }
    },
    success:function(data){
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_diagnosticoPersonalizado .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_diagnosticoPersonalizado .msg').html(data.mensaje);
            }
            $('#msg_form_diagnosticoPersonalizado').addClass("error");
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_diagnosticoPersonalizado .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_diagnosticoPersonalizado .msg').html(data.mensaje);
            }
            $('#msg_form_diagnosticoPersonalizado').addClass("correcto");
        }

        setWaitingStatus('formDiagnosticoPersonalizado', false);
        $('#msg_form_diagnosticoPersonalizado').fadeIn('slow');
    }
};

/**
 * La validacion de los estados iniciales se hacen del lado del server y se devuelve por ajax.
 */
var validateFormDiagnosticoSCC = {
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
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxDiagnosticoSCC = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-diagnostico',
    beforeSerialize:function(){

        if($("#formDiagnosticoSCC").valid() == true){

            $('#msg_form_diagnosticoSCC').hide();
            $('#msg_form_diagnosticoSCC').removeClass("correcto").removeClass("error");
            $('#msg_form_diagnosticoSCC .msg').html("");
            setWaitingStatus('formDiagnosticoSCC', true);

        }else{
            return false;
        }
    },

    success:function(data){
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_diagnosticoSCC .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_diagnosticoSCC .msg').html(data.mensaje);
            }
            $('#msg_form_diagnosticoSCC').addClass("error");
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_diagnosticoSCC .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_diagnosticoSCC .msg').html(data.mensaje);
            }

            //refresco el listado de estados iniciales, necesario por ids
            if(data.modificarDiagnosticoSCC != undefined){
                $("#grillaEstadosInicialesWrapper").html(data.html);
                $("textarea.maxlength").maxlength();
            }

            $('#msg_form_diagnosticoSCC').addClass("correcto");
        }

        setWaitingStatus('formDiagnosticoSCC', false);
        $('#msg_form_diagnosticoSCC').fadeIn('slow');
    }
};

function bindEventsFormDiagnosticoPersonalizado(){
    $("#formDiagnosticoPersonalizado").validate(validateFormDiagnosticoPersonalizado);
    $("#formDiagnosticoPersonalizado").ajaxForm(optionsAjaxFormDiagnosticoPersonalizado);
}

function bindEventsFormDiagnosticoSCC(){
    $("#formDiagnosticoSCC").validate(validateFormDiagnosticoSCC);
    $("#formDiagnosticoSCC").ajaxForm(optionsAjaxDiagnosticoSCC);

    //bindeo eventos en los selects de los estados actuales que ya estaban cargados (fila por fila)
    if($('.estadoInicial').length){
        $('.estadoInicial').each(function(){
            var estadoInicialHtmlId = $(this).attr("id");

            $("#nivel_"+estadoInicialHtmlId).change(function(){
                listaCiclosByNivel($("#nivel_"+estadoInicialHtmlId+" option:selected").val(), estadoInicialHtmlId);
            });

            $("#ciclo_"+estadoInicialHtmlId).change(function(){
                listaAreasByCiclo($("#ciclo_"+estadoInicialHtmlId+" option:selected").val(), estadoInicialHtmlId);
            });

            $("#area_"+estadoInicialHtmlId).change(function(){
                listaEjesTematicosByArea($("#area_"+estadoInicialHtmlId+" option:selected").val(), estadoInicialHtmlId);
            });
        });
    }
}

//combos con ajax formularios. le paso el id del select porque hay varias filas de estados iniciales
function listaCiclosByNivel(idNivel, estadoInicialHtmlId){

    //si el valor elegido es '' entonces marco como disabled
    if(idNivel == ''){
        $('#ciclo_'+estadoInicialHtmlId).addClass("disabled");
    }else{
        $('#ciclo_'+estadoInicialHtmlId).removeClass("disabled");
    }

    if($('#area_'+estadoInicialHtmlId).length){
        $('#area_'+estadoInicialHtmlId).addClass("disabled");
    }
    if($('#ejeTematico_'+estadoInicialHtmlId).length){
        $('#ejeTematico_'+estadoInicialHtmlId).addClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ciclos-por-niveles",
        data:{iNivelId:idNivel},
        beforeSend: function(){
            setWaitingStatus(estadoInicialHtmlId, true);
        },
        success:function(lista){
            $('#ciclo_'+estadoInicialHtmlId).html("");

            //los demas van vacios si es que estan en el formulario, se completan a medida que se seleccionan
            if($('#area_'+estadoInicialHtmlId).length){
                $('#area_'+estadoInicialHtmlId).html("");
                $('#area_'+estadoInicialHtmlId).html(new Option('Área:', '',true));
            }
            if($('#ejeTematico_'+estadoInicialHtmlId).length){
                $('#ejeTematico_'+estadoInicialHtmlId).html("");
                $('#ejeTematico_'+estadoInicialHtmlId).html(new Option('Eje:', '',true));
            }

            if(lista.length != undefined && lista.length > 0){
                $('#ciclo_'+estadoInicialHtmlId).append(new Option('Ciclo:', '',true));
                for(var i=0; i<lista.length; i++){
                    $('#ciclo_'+estadoInicialHtmlId).append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
                $('#ciclo_'+estadoInicialHtmlId).removeClass("disabled");
            }else{
                $('#ciclo_'+estadoInicialHtmlId).html(new Option('No hay ciclos cargados', '',true));
            }

            setWaitingStatus(estadoInicialHtmlId, false);
        }
    });
 }

function listaAreasByCiclo(idCiclo, estadoInicialHtmlId){

    if(idCiclo == ''){
        $('#area_'+estadoInicialHtmlId).addClass("disabled");
    }else{
        $('#area_'+estadoInicialHtmlId).removeClass("disabled");
    }

    if($('#ejeTematico_'+estadoInicialHtmlId).length){
        $('#ejeTematico_'+estadoInicialHtmlId).addClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-areas-por-ciclos",
        data:{iCicloId:idCiclo},
        beforeSend: function(){
            setWaitingStatus(estadoInicialHtmlId, true);
        },
        success: function(lista){

            $('#area_'+estadoInicialHtmlId).html("");

            if($('#ejeTematico_'+estadoInicialHtmlId).length){
                $('#ejeTematico_'+estadoInicialHtmlId).html("");
                $('#ejeTematico_'+estadoInicialHtmlId).html(new Option('Eje:', '',true));
            }

            if(lista.length != undefined && lista.length > 0){
                $('#area_'+estadoInicialHtmlId).append(new Option('Área:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#area_'+estadoInicialHtmlId).append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#area_'+estadoInicialHtmlId).append(new Option('No hay áreas cargadas', '',true));
            }
            setWaitingStatus(estadoInicialHtmlId, false);
        }
    });
}

function listaEjesTematicosByArea(idArea, estadoInicialHtmlId){
    if(idArea == ''){
        $('#ejeTematico_'+estadoInicialHtmlId).addClass("disabled");
    }else{
        $('#ejeTematico_'+estadoInicialHtmlId).removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "seguimientos/listar-ejes-por-area",
        data:{iAreaId:idArea},
        beforeSend: function(){
            setWaitingStatus(estadoInicialHtmlId, true);
        },
        success: function(lista){

            $('#ejeTematico_'+estadoInicialHtmlId).html("");

            if(lista.length != undefined && lista.length > 0){
                $('#ejeTematico_'+estadoInicialHtmlId).append(new Option('Eje:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ejeTematico_'+estadoInicialHtmlId).append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#ejeTematico_'+estadoInicialHtmlId).append(new Option('No hay ejes cargados', '',true));
            }
            setWaitingStatus(estadoInicialHtmlId, false);
        }
    });
}

$(document).ready(function(){
    
    if($("#formDiagnosticoPersonalizado").length){
        bindEventsFormDiagnosticoPersonalizado();
    }

    if($("#formDiagnosticoSCC").length){
        bindEventsFormDiagnosticoSCC();
    }
    
    $("#agregarEstadoInicial").click(function(){
        $.ajax({
            type:"post",
            dataType:"jsonp",            
            url:"seguimientos/procesar-diagnostico",
            data:{
                agregarEstadoInicial:"1"
            },
            beforeSend: function(){
                setWaitingStatus('listadoEstadosIniciales', true);
            },
            success:function(data){
                if($("#noRecordsEstadoInicial").length){
                    $("#noRecordsEstadoInicial").hide("slow", function(){
                        $("#noRecordsEstadoInicial").remove();
                    });
                }
                setWaitingStatus('listadoEstadosIniciales', false);
                $('#grillaEstadosIniciales').append(data.html);

                //bindeo eventos en los selects.
                $("#nivel_"+data.estadoInicialHtmlId).change(function(){
                    listaCiclosByNivel($("#nivel_"+data.estadoInicialHtmlId+" option:selected").val(), data.estadoInicialHtmlId);
                });

                $("#ciclo_"+data.estadoInicialHtmlId).change(function(){
                    listaAreasByCiclo($("#ciclo_"+data.estadoInicialHtmlId+" option:selected").val(), data.estadoInicialHtmlId);
                });

                $("#area_"+data.estadoInicialHtmlId).change(function(){
                    listaEjesTematicosByArea($("#area_"+data.estadoInicialHtmlId+" option:selected").val(), data.estadoInicialHtmlId);
                });

                $("#estadoInicial_"+data.estadoInicialHtmlId).maxlength();
            }
        });               
    });

    $(".borrarEstadoInicial").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var estadoInicialHtmlId = rel[0];
        var iEjeId = rel[1];
        var iDiagnosticoSCCId = rel[2];

        //solo si el estado inicial estaba guardado en db
        if(iEjeId != "" && iDiagnosticoSCCId != ""){
            if(confirm("Se borrara el estado inicial seleccionado, desea continuar?")){
                $.ajax({
                    type:"post",
                    dataType:"jsonp",
                    url:"seguimientos/procesar-diagnostico",
                    data:{
                        eliminarEstadoInicial:"1",
                        iEjeId:iEjeId,
                        iDiagnosticoSCCId:iDiagnosticoSCCId
                    },
                    success:function(data){
                        if(data.success != undefined && data.success == 1){
                            $("#"+estadoInicialHtmlId).hide("slow", function(){
                                $("#"+estadoInicialHtmlId).remove();
                            });
                        }
                    }
                });
            }
        //repito el codigo en el else por el asincronismo del ajax si es que necesitas ejecutarse.
        }else{
            $("#"+estadoInicialHtmlId).hide("slow", function(){
                $("#"+estadoInicialHtmlId).remove();
            });
        }
    });
});
