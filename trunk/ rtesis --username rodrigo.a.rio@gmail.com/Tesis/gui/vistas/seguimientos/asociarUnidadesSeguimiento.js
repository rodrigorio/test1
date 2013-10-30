function asociarUnidad()
{

}

function desasociarUnidad()
{
    
}

function moverUnidad(){
    $.ajax({
        type:"post",
        dataType: 'jsonp',
        url:"seguimientos/entradas/eliminar",
        data:{
            confirmo:"0",
            iEntradaId:iEntradaId
        },
        beforeSend: function(){
            setWaitingStatus('menuEntrada', true, "16");
        },
        success:function(data){
            setWaitingStatus('menuEntrada', false, "16");

            var dialog = $("#dialog");
            if($("#dialog").length){dialog.remove();}
            dialog = $('<div id="dialog" title="Eliminar Entrada"></div>').appendTo('body');
            dialog.html(data.html);

            if(data.success != undefined && data.success == 1 && data.confirmar){
                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:buttons
                });
            }else{
                var buttonAceptar = {"Aceptar": function(){$(this).dialog("close");}}
                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:buttonAceptar
                });

                if(data.success != undefined && data.success == 1){
                    $(".ui-dialog-buttonset .ui-button").click(function(){
                        //redirecciona a ultima entrada
                        location = data.redirect;
                    });
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
            var result;

            if($(this).attr("id") == 'unidadesSinAsociar'){
                result = asociarUnidad(unidad);
            }
            if($(this).attr("id") == 'unidadesAsociadas'){
                result = desasociarUnidad(unidad);
            }

            //si el resultado fue false cancelo el movimiento de la unidad.

            //Once Finish Sort, remove Clone Li from current list
            unidad.remove();
        },
        connectWith: ".connectedSortable"
    }).disableSelection();    
});