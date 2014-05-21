var validateFormPreguntaAbierta = {
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
        descripcion:{required:true},
        orden:{required:true, digits:true, range:[1, 99]}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        orden:{
            required:mensajeValidacion("requerido"),
            digits:mensajeValidacion("digitos"),
            range:"El numero de orden debe ser un numero positivo mayor a 1."
        }
    }
};

var optionsAjaxFormPreguntaAbierta = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/guardar-pregunta',
    beforeSerialize:function(){

        if($("#formPreguntaAbierta").valid() == true){

            $('#msg_form_pregunta').hide();
            $('#msg_form_pregunta').removeClass("correcto").removeClass("error");
            $('#msg_form_pregunta .msg').html("");
            setWaitingStatus('formPreguntaAbierta', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formPreguntaAbierta', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_pregunta .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_pregunta .msg').html(data.mensaje);
            }
            $('#msg_form_pregunta').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_pregunta .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_pregunta .msg').html(data.mensaje);
            }
            if(data.agregarPregunta != undefined){
                $('#formPreguntaAbierta').each(function(){
                  this.reset();
                });
            }

            //refresco el listado actual
            masPreguntas();
            $('#msg_form_pregunta').addClass("correcto").fadeIn('slow');
        }
    }
};

/**
 * La validacion de las opciones se hacen del lado del server directamente y se devuelve por ajax.
 */
var validateFormPreguntaMC = {
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
        descripcion:{required:true},
        orden:{required:true, digits:true, range:[1, 99]}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        orden:{
            required:mensajeValidacion("requerido"),
            digits:mensajeValidacion("digitos"),
            range:"El numero de orden debe ser un numero positivo mayor a 1."
        }
    }
};

var optionsAjaxFormPreguntaMC = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/guardar-pregunta',
    beforeSerialize:function(){

        if($("#formPreguntaMC").valid() == true){

            $('#msg_form_pregunta').hide();
            $('#msg_form_pregunta').removeClass("correcto").removeClass("error");
            $('#msg_form_pregunta .msg').html("");
            setWaitingStatus('formPreguntaMC', true);

        }else{
            return false;
        }
    },

    success:function(data){
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_pregunta .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_pregunta .msg').html(data.mensaje);
            }
            $('#msg_form_pregunta').addClass("error");
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_pregunta .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_pregunta .msg').html(data.mensaje);
            }
            if(data.agregarPregunta != undefined){
                $('#formPreguntaMC').each(function(){
                  this.reset();
                });

                $('.opcion').remove();
            }

            //si estoy editando una pregunta multiple choise refresco el listado de opciones. necesario por ids
            if(data.modificarPregunta != undefined){
                $("#grillaOpcionesWrapper").html(data.grillaOpciones);
            }

            //refresco el listado actual
            masPreguntas();
            $('#msg_form_pregunta').addClass("correcto");
        }

        setWaitingStatus('formPreguntaMC', false);
        $('#msg_form_pregunta').fadeIn('slow');
    }
};

function bindEventsPreguntaAbiertaForm(){
    $("#formPreguntaAbierta").validate(validateFormPreguntaAbierta);
    $("#formPreguntaAbierta").ajaxForm(optionsAjaxFormPreguntaAbierta);
}

function bindEventsPreguntaMCForm(){
    $("#formPreguntaMC").validate(validateFormPreguntaMC);
    $("#formPreguntaMC").ajaxForm(optionsAjaxFormPreguntaMC);
}

function masPreguntas(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();
    var entrevistaId = $('#entrevistaId').val();

    $.ajax({
        type:"POST",
        url:"seguimientos/preguntas-procesar",
        data:{
            masPreguntas: "1",
            sOrderBy: sOrderBy,
            sOrder: sOrder,
            id: entrevistaId
        },
        beforeSend:function(){
            setWaitingStatus('listadoPreguntas', true);
        },
        success:function(data){
            setWaitingStatus('listadoPreguntas', false);
            $("#listadoPreguntasResult").html(data);
        }
    });
}

function eliminarPregunta(iPreguntaId){
    if(confirm("Se borrara la pregunta de la entrevista de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"seguimientos/borrar-pregunta",
            data:{
                iPreguntaId:iPreguntaId
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha
                    $("."+iPreguntaId).hide("slow", function(){
                        $("."+iPreguntaId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title","Borrar Pregunta");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Pregunta"></div>').appendTo('body');
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

$(document).ready(function(){

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masPreguntas();
    });

    $("#crearPreguntaAbierta").click(function(){
        var entrevistaId = $('#entrevistaId').val();
        var dialog = setWaitingStatusDialog(550, "Crear pregunta abierta");
        dialog.load(
            "seguimientos/form-crear-pregunta",
            {"formAbierta":"1", "entrevistaId":entrevistaId},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsPreguntaAbiertaForm();
            }
        );
        return false;
    });

    $("#crearPreguntaMC").click(function(){
        var entrevistaId = $('#entrevistaId').val();
        var dialog = setWaitingStatusDialog(550, "Crear pregunta multiple choise");
        dialog.load(
            "seguimientos/form-crear-pregunta",
            {"formMC":"1", "entrevistaId":entrevistaId},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsPreguntaMCForm();
            }
        );
        return false;
    });

    $(".editarPregunta").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPreguntaId = rel[1];
        var entrevistaId = $('#entrevistaId').val();

        var titulo = "";
        switch(tipo){
            case "PreguntaAbierta": titulo = "Editar pregunta abierta"; break;
            case "PreguntaMC": titulo = "Editar pregunta multiple choise"; break;
        }

        var dialog = setWaitingStatusDialog(550, titulo);

        dialog.load(
            "seguimientos/form-editar-pregunta",
            {"iPreguntaId":iPreguntaId, "entrevistaId":entrevistaId},
            function(responseText, textStatus, XMLHttpRequest){
                switch(tipo){
                case "PreguntaAbierta":
                  bindEventsPreguntaAbiertaForm();
                  break;
                case "PreguntaMC":
                  bindEventsPreguntaMCForm();
                  break;
                }
            }
        );
    });

    $(".borrarPregunta").live('click', function(){
        var iPreguntaId = $(this).attr("rel");
        eliminarPregunta(iPreguntaId);
    });

    $("#agregarOpcion").live('click', function(){
        $.ajax({
            type:"POST",
            url:"seguimientos/preguntas-procesar",
            data:{
                agregarOpcion:"1"
            },
            beforeSend: function(){
                setWaitingStatus('listadoOpciones', true);
            },
            success:function(data){
                if($("#noRecordsOpciones").length){
                    $("#noRecordsOpciones").hide("slow", function(){
                        $("#noRecordsOpciones").remove();
                    });
                }
                setWaitingStatus('listadoOpciones', false);
                $('#grillaOpciones').append(data);
            }
        });
    });

    $(".borrarOpcion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var opcionHtmlId = rel[0];
        var iOpcionId = rel[1];

        //solo si la opcion estaba guardada en db
        if(iOpcionId != ""){
            if(confirm("Se borrara la opción seleccionada en la pregunta, desea continuar?")){
                $.ajax({
                    type:"post",
                    dataType:"jsonp",
                    url:"seguimientos/borrar-opcion-pregunta",
                    data:{
                        iOpcionId:iOpcionId
                    },
                    success:function(data){
                        if(data.success != undefined && data.success == 1){
                            $("."+opcionHtmlId).hide("slow", function(){
                                $("."+opcionHtmlId).remove();
                            });
                        }
                    }
                });
            }
        //repito el codigo en el else por el asincronismo del ajax si es que necesitas ejecutarse.
        }else{
            $("."+opcionHtmlId).hide("slow", function(){
                $("."+opcionHtmlId).remove();
            });
        }
    });
});
