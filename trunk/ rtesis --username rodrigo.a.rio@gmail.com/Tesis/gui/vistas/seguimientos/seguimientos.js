autoCompleteInput("persona","seguimientos/buscar-usuarios");

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
});