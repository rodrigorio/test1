/**
 * Este archivo tiene eventos para ver ficha de persona y para acceder a entradas por fecha, etc.
 *
 * Es codigo factorizado que se usa en antecedentes, diagnostico, objetivos, entradas por fecha, pronostico
 */

function checkSeguimientoEntradasOK(iSeguimientoId)
{
    $.ajax({
        type:"POST",
        url:"seguimientos/procesar",
        data:{
            checkEntradasOK:"1",
            iSeguimientoId:iSeguimientoId
        },
        beforeSend: function(){
            setWaitingStatus('pageRightInnerContNav', true);
        },
        success:function(data){
            setWaitingStatus('pageRightInnerContNav', false);

            if(data.success == undefined || data.success == 0){
                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title","Editar Entradas");
                }else{
                    dialog = $('<div id="dialog" title="Editar Entradas"></div>').appendTo('body');
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

            if(data.success == 1){
                location = data.redirect;
            }
        }
    });
}

$(document).ready(function(){
    $("a[rel^='prettyphoto']").prettyphoto();

    //menu derecha
    if($("#pageRightInnerContNav").length){
        $("#pageRightInnerContNav li").mouseenter(function(){
            if(!$(this).hasClass("selected")){
                $(this).children("ul").fadeIn('slow');
            }
        });
        $("#pageRightInnerContNav li").mouseleave(function(){
            if(!$(this).hasClass("selected")){
                $(this).children("ul").fadeOut('slow');
            }
        });
    }

    $(".verPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");

        setWaitingStatus('fichaPersonaMenu', true, "16");
        var dialog = setWaitingStatusDialog(450, $(this).html());
        dialog.load(
            "seguimientos/ver-persona?personaId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                setWaitingStatus('fichaPersonaMenu', false, "16");
                bindEventsPersonaVerFicha(); //la funcion esta en personas.js
                $("a[rel^='prettyphoto']").prettyphoto();
            }
        );
        return false;
    });

    /* me fijo que el seguimiento tenga antecedentes, diagnostico y al menos un objetivo asociado */
    if($("#checkSeguimientoEntradasOK").length){
        $("#checkSeguimientoEntradasOK").live('click', function(){
            var iSeguimientoId = $(this).attr("rel");
            checkSeguimientoEntradasOK(iSeguimientoId);
            return false;
        });
    }
});
