function borrarInstitucion(iInstitucionId){

    if(confirm("Se borrara la persona del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/instituciones-procesar",
            data:{
                iInstitucionId:iInstitucionId,
                eliminar:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iInstitucionId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Institucion");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Institucion"></div>').appendTo('body');
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
    $(".borrarInstitucion").click(function(){
        var iInstitucionId = $(this).attr("rel");
        borrarInstitucion(iInstitucionId);
    });
});