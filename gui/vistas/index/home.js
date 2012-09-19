$(document).ready(function(){
    $.getScript(pathUrlBase+"utilidades/js-scripts/md5.js");
    $.getScript(pathUrlBase+"gui/vistas/index/login.js");
   
    $("#menuPpalAcceder").live('click',function(){        
        login();
        return false;
    });
});