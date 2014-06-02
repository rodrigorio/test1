function asociarEntrevista(entrevista){
    moverEntrevista(entrevista, "asociarEntrevistaSeguimiento");
}

function desasociarEntrevista(entrevista)
{
    var buttons = {
        "Confirmar": function(){
            moverEntrevista(entrevista, "desasociarEntrevistaSeguimiento");
            $(this).dialog( "close" );
        },
        "Cancelar": function(){
            //volver a posicion inicial
            var rel = entrevista.attr("rel").split('_');
            var iEntrevistaId = rel[1];
            $("#entrevista_"+iEntrevistaId).appendTo('#entrevistasAsociadas');
            $(this).dialog("close");
        }
    }

    //este es el dialog que pide confirmar la accion
    var dialog = setWaitingStatusDialog(500, "Asociar Entrevista", buttons);
    dialog.load(
        "seguimientos/entrevistas-seguimiento-procesar",
        {dialogConfirmar:"1"},
        function(){}
    );
}

function moverEntrevista(entrevista, accion)
{
    var rel = entrevista.attr("rel").split('_');
    var iSeguimientoId = rel[0];
    var iEntrevistaId = rel[1];

    $.ajax({
        type:"post",
        dataType:'jsonp',
        url:"seguimientos/entrevistas-seguimiento-procesar",
        data:{
            moverEntrevista:accion,
            iSeguimientoId:iSeguimientoId,
            iEntrevistaId:iEntrevistaId
        },
        beforeSend: function(){
            setWaitingStatus('entrevistasWrapper', true);
        },
        success:function(data){
            setWaitingStatus('entrevistasWrapper', false);
            if(data.success == undefined || data.success == 0){
                //volver a posicion inicial el li
                $("#entrevista_"+iEntrevistaId).appendTo('#entrevistasAsociadas');
            }else{
                if(accion == "asociarEntrevistaSeguimiento"){
                    $("#noRecordsAsociadas").remove();
                }
                if(accion == "desasociarEntrevistaSeguimiento"){
                    $("#noRecordsDesasociadas").remove();
                }
            }
        }
    });
}

$(document).ready(function(){

    $(".ampliarEntrevista").live('click', function(){
        var iEntrevistaId = $(this).attr("rel");

        var dialog = setWaitingStatusDialog(550, "Detalle Entrevista");
        dialog.load(
            "seguimientos/entrevistas-seguimiento-procesar",
            {iEntrevistaId:iEntrevistaId,
             ampliarEntrevista:"1"},
            function(responseText, textStatus, XMLHttpRequest){
                $(".tooltip").tooltip();
            }
        );
    });

    var itemclone, idx;
    $("#entrevistasSinAsociar, #entrevistasAsociadas").sortable({
        items:"li:not(.expirada)",
        start: function(event, ui){

            //if($(ui.item).hasClass("expirada")){return false;}

            //create clone of current seletected li
            itemclone = $(ui.item).clone();
            //get current li index position in list
            idx = $(ui.item).index();
            //If first li then prepend clone on first position
            if (idx == 0) {
                itemclone.css('opacity', '0.5');
                $(this).prepend(itemclone);
            }
            //Else Append Clone on its original position
            else {
                itemclone.css('opacity', '0.7');
                $(this).find("li:eq(" + (idx - 1) + ")").after(itemclone);
            }
        },
        change: function(event, ui){
            //While Change event set clone position as relative
            $(this).find("li:eq(" + idx + ")").css('position', 'relative');
        },
        stop: function(){
            var entrevista = $(this).find("li:eq(" + idx + ")");

            if($(this).attr("id") == 'entrevistasSinAsociar'){
                asociarEntrevista(entrevista);
            }
            if($(this).attr("id") == 'entrevistasAsociadas'){
                desasociarEntrevista(entrevista);
            }

            //Once Finish Sort, remove Clone Li from current list
            entrevista.remove();
        },
        connectWith: ".connectedSortable"
    }).disableSelection();
});
