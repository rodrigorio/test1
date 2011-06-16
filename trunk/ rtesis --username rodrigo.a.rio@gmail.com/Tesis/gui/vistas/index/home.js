$(document).ready(function(){
   /* Ejemplo de dialog en menu01 con ajax despues borrar */
    $("#menu1").live('click',function(){

        $.getScript("http://localhost/tesis/gui/vistas/index/login.js");

        //nosotros laburamos con los dialogs pelados, porque usamos nuestro propio frame html
        var dialog = $("#dialog");
        if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Acceder"></div>').appendTo('body'); }

        //para ver las opciones: http://www.phpeveryday.com/articles/jQuery-UI-Dialog-Options-P1002.html
        dialog.load(
            'http://localhost/tesis/login',
            {},
            function(responseText, textStatus, XMLHttpRequest){
                    dialog.dialog({
                        width:550,
                        resizable:false,
                        draggable:false,
                        modal:true,
                        closeOnEscape:true
                    });
            }
        );
        return false;
    });
});