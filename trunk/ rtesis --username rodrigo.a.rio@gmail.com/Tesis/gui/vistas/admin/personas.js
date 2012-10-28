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

function borrarDiscapacitado(personaId){
    if(confirm("Se borrara la persona del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/personas-procesar",
            data:{
                personaId:personaId,
                eliminar:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+personaId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Persona");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Persona"></div>').appendTo('body');
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

function ampliarPersona(iPersonaId)
{
    var dialog = setWaitingStatusDialog(600, "Ficha Persona ID: "+iPersonaId);
    dialog.load(
        "admin/personas-procesar",
        {
            ampliarPersona:"1",
            iPersonaId:iPersonaId
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsAdmin();
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    );
}

function ampliarInstitucion(iInstitucionId)
{
    var dialog = setWaitingStatusDialog(700, "Ficha Institucion ID: "+iInstitucionId);
    dialog.load(
        "admin/instituciones-procesar",
        {
            ampliarInstitucion:"1",
            iInstitucionId:iInstitucionId
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsAdmin();
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    );
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

    $(".borrarDiscapacitado").click(function(){
        var personaId = $(this).attr("rel");
        borrarDiscapacitado(personaId);
    });

    $(".ampliarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        ampliarInstitucion(iInstitucionId);
        return false;
    });

    $(".ampliarPersona").live('click', function(){
        var iPersonaId = $(this).attr("rel");
        ampliarPersona(iPersonaId);        
    });
});