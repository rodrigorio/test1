function aprobarModeracion(personaId){
    if(confirm("Se aprobaran las modificaciones realizadas, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/personas-moderacion-procesar",
            data:{
                personaId:personaId,
                aprobar:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+personaId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Aprobar Moderacion");
                }else{
                    dialog = $('<div id="dialog" title="Aprobar Moderacion"></div>').appendTo('body');
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

function rechazarModeracion(personaId){
    if(confirm("Se descartaran las modificaciones realizadas, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/personas-moderacion-procesar",
            data:{
                personaId:personaId,
                rechazar:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+personaId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Rechazar Moderacion");
                }else{
                    dialog = $('<div id="dialog" title="Rechazar Moderacion"></div>').appendTo('body');
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
    $("a[rel^='prettyPhoto']").prettyPhoto();

    $(".aprobarModeracion").click(function(){
        var personaId = $(this).attr("rel");
        aprobarModeracion(personaId);
    });

    $(".rechazarModeracion").click(function(){
        var personaId = $(this).attr("rel");
        rechazarModeracion(personaId);
    });
});