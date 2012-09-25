var validateFormAntecedentes = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("id"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        antecedentes:{required:true}
    },
    messages:{
        antecedentes: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormAntecedentes = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-antecedentes?formAntecedentes=1',
    beforeSerialize:function(){        
        if($("#formGuardarAntecedentes").valid() == true){
            $('#msg_form_guardarAntecedentes').hide();
            $('#msg_form_guardarAntecedentes').removeClass("correcto").removeClass("error");
            $('#msg_form_guardarAntecedentes .msg').html("");
            setWaitingStatus('tabsFormAntecedentes', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('tabsFormAntecedentes', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_guardarAntecedentes .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_guardarAntecedentes .msg').html(data.mensaje);
            }
            $('#msg_form_guardarAntecedentes').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_guardarAntecedentes .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_guardarAntecedentes .msg').html(data.mensaje);
            }
            $('#msg_form_guardarAntecedentes').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventFormAgregarArchivo(iSeguimientoId)
{
    if($('#antecedentesUpload').length){
        new Ajax_upload('#antecedentesUpload', {
            action:'seguimientos/procesar-antecedentes',
            data:{
                fileAntecedentesUpload:"1",
                iSeguimientoId:iSeguimientoId
            },
            name:'archivoAntecedentes',
            onChange:function(file , ext){
                if(confirm("Se eliminara el archivo anterior, desea realizar esta operacion?")){
                    return true;
                }else{
                    return false;
                }
            },
            onSubmit:function(file , ext){
                $('#msg_form_antecedentes').hide();
                $('#msg_form_antecedentes').removeClass("correcto").removeClass("error");
                $('#msg_form_antecedentes .msg').html("");
                setWaitingStatus('tabsFormAntecedentes', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                
                setWaitingStatus('tabsFormAntecedentes', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_antecedentes .msg').html(lang['error procesar']);
                    $('#msg_form_antecedentes').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';;');
                var resultado = dataInfo[0]; 
                var html = dataInfo[1]; 

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_antecedentes .msg').html(lang['error permiso']);
                    $('#msg_form_antecedentes').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_antecedentes .msg').html(html);
                    $('#msg_form_antecedentes').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_antecedentes .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_antecedentes').addClass("correcto").fadeIn('slow');

                    $('#wrapAntActual').html(html);
                }
                return;
            }
        });
    }    
}

$(document).ready(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

    //menu derecha
    $("#pageRightInnerContNav li").mouseenter(function(){
        if(!$(this).hasClass("selected")){
            $(this).children("ul").fadeIn('slow');
        }
    });
    $("#pageRightInnerContNav li").mouseleave(function(){
        if(!$(this).hasClass("selected")){
            $(this).children("ul").fadeOut('slow');
        }
    });

    $("#tabsFormAntecedentes").tabs();
    $("#antecedentes").maxlength({maxCharacters:1000});

    $("#formGuardarAntecedentes").validate(validateFormAntecedentes);
    $("#formGuardarAntecedentes").ajaxForm(optionsAjaxFormAntecedentes);

    bindEventFormAgregarArchivo($('#idSeguimiento').val());

    $(".verPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='"+$(this).html()+"'></div>").appendTo('body');

        setWaitingStatus('fichaPersonaMenu', true, "16");

        dialog.load(
            "seguimientos/ver-persona?personaId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                setWaitingStatus('fichaPersonaMenu', false, "16");
                dialog.dialog({
                    position:['center', '20'],
                    width:450,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });
                bindEventsPersonaVerFicha(); //la funcion esta en personas.js
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });
});