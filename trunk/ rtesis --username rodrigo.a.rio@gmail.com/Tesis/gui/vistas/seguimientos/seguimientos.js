
$(document).ready(function(){

    $("#agregarPersona").live('click',function(){
        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");
        var dialog = $("#dialog");
        if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Agregar Persona"></div>').appendTo('body'); }
        dialog.load(
            "seguimientos/agregar-persona?popUp=1",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:650,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });
                $("#tabsFormPersona" ).tabs();
            }            
        );
        return false;
    });
    
    $("#persona").autocomplete({
        source:function(request, response){
            $.ajax({
                url: "seguimientos/buscar-usuarios",
                dataType: "jsonp",
                data:{
                    limit:12,
                    str:request.term
                },
                beforeSend: function(){
                   revelarElemento($("#persona_loading"));
                },
                success: function(data){
                   ocultarElemento($("#persona_loading"));
                    response( $.map(data.usuarios, function(usuarios){
                        return{
                            //lo que aparece en el input
                            value:usuarios.sNombre,
                            //lo que aparece en la lista generada para elegir
                            label:usuarios.sNombre,
                            //valor extra que se devuelve para completar el hidden
                            id:usuarios.iId
                        }
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui){
            if(ui.item){
                $("#personaId").val(ui.item.id);
            }else{
                $("#personaId").val("");
            }
        },
        close: function(){
            if($("#personaId").val() != ""){
                $(this).addClass("selected");
                $(this).attr('readonly', 'readonly');
                revelarElemento($('#persona_clean'));
            }
        }
    });
});
//para borrar la institucion seleccionada con el autocomplete
$('#persona_clean').click(function(){
    $("#persona").removeClass("selected");
    $("#persona").removeAttr("readonly");
    $("#persona").val("");
    $("#personaId").val("");
    ocultarElemento($(this));
});