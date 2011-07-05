$(document).ready(function(){
   
    $("#menuPpalAcceder").live('click',function(){

        $.getScript(pathUrlBase+"utilidades/js-scripts/md5.js");
        $.getScript(pathUrlBase+"gui/vistas/index/login.js");

        //nosotros laburamos con los dialogs pelados, porque usamos nuestro propio frame html
        var dialog = $("#dialog");
        if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Acceder"></div>').appendTo('body'); }

        //para ver las opciones: http://www.phpeveryday.com/articles/jQuery-UI-Dialog-Options-P1002.html
        dialog.load(
            pathUrlBase+"login?popUp=1",
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