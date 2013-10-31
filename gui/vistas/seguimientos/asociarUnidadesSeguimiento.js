function asociarUnidad(unidad){
    moverUnidad(unidad, "asociarUnidadSeguimiento");
}

function desasociarUnidad(unidad)
{
    var buttons = {
        "Confirmar": function(){
            moverUnidad(unidad, "desasociarUnidadSeguimiento");
            $(this).dialog( "close" );
        },
        "Cancelar": function(){
            //volver a posicion inicial
            var rel = unidad.attr("rel").split('_');
            var iUnidadId = rel[1];
            $("#unidad_"+iUnidadId).appendTo('#unidadesAsociadas');
            $(this).dialog( "close" );
        }
    }

    //este es el dialog que pide confirmar la accion
    var dialog = setWaitingStatusDialog(500, "Asociar Unidad", buttons);
    dialog.load(
        "seguimientos/unidades-seguimiento-procesar",
        {dialogConfirmar:"1"},
        function(){}
    );
}

function moverUnidad(unidad, accion)
{
    var rel = unidad.attr("rel").split('_');
    var iSeguimientoId = rel[0];
    var iUnidadId = rel[1];

    $.ajax({
        type:"post",
        dataType:'jsonp',
        url:"seguimientos/unidades-seguimiento-procesar",
        data:{
            moverUnidad:accion,
            iSeguimientoId:iSeguimientoId,
            iUnidadId:iUnidadId
        },
        beforeSend: function(){
            setWaitingStatus('unidadesWrapper', true);
        },
        success:function(data){
            setWaitingStatus('unidadesWrapper', false);
            if(data.success == undefined || data.success == 0){
                //volver a posicion inicial el li
                $("#unidad_"+iUnidadId).appendTo('#unidadesAsociadas');
            }else{
                if(accion == "asociarUnidadSeguimiento"){
                    $("#noRecordsAsociadas").remove();
                }
                if(accion == "desasociarUnidadSeguimiento"){
                    $("#noRecordsDesasociadas").remove();
                }
            }
        }
    });
}

$(document).ready(function(){
            
    $(".ampliarUnidad").live('click', function(){
        var iUnidadId = $(this).attr("rel");

        var dialog = setWaitingStatusDialog(550, "Detalles Unidad");
        dialog.load(
            "seguimientos/unidades-seguimiento-procesar",
            {iUnidadId:iUnidadId,
             ampliarUnidad:"1"},
            function(responseText, textStatus, XMLHttpRequest){
                $(".tooltip").tooltip();
            }
        );
    });
    
    var itemclone, idx;
    $("#unidadesSinAsociar, #unidadesAsociadas").sortable({
        start: function(event, ui){
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
            var unidad = $(this).find("li:eq(" + idx + ")");

            if($(this).attr("id") == 'unidadesSinAsociar'){
                asociarUnidad(unidad);
            }
            if($(this).attr("id") == 'unidadesAsociadas'){
                desasociarUnidad(unidad);
            }

            //Once Finish Sort, remove Clone Li from current list
            unidad.remove();
        },
        connectWith: ".connectedSortable"
    }).disableSelection();    
});