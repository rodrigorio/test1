function cerrarCuentaUsuario(usuarioId){
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
});