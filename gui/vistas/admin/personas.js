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
                
                //devuelve un msg_top que lo agrego en el body
                msg_top = $(data.html).appendTo('body');

                msg_top.show('drop', {direction: "down"}, 1000);
                setTimeout(function(){
                    msg_top.hide('drop', {direction: "up"}, 1000)
                    msg_top.remove();
                }, 5000);
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