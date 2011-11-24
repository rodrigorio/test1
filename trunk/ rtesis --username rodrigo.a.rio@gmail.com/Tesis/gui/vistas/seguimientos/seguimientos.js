autoCompleteInput("persona","seguimientos/buscar-usuarios");

$(document).ready(function(){

    $("#agregarPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");

        var dialog = $("#dialog");
        if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Agregar Persona"></div>').appendTo('body'); }

    dialog.load(
        "comunidad/datos-personales-procesar",
        {seccion:'dialogIntegranteActivo'},
        function(data){
            dialog.dialog({
                position:['center',5],
                width:650,
                resizable:false,
                draggable:false,
                modal:true,
                closeOnEscape:true
            });
        }
    );

        dialog.load(
            "seguimientos/personas-",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                    dialog.dialog({
                        width:550,
                        resizable:false,
                        draggable:false,
                        modal:true,
                        closeOnEscape:true
                    });
                    $("#formLogin").validate(validateFormLogin);
                    $("#formLogin").ajaxForm(optionsAjaxFormLogin);
                    $("#rec_pass").live('click',function(){
                    	$("#formLogin").hide();
                    	$("#recuperarContrasenia").show();
                    });
            }
        );
        return false;
    });
});