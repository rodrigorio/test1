var validateFormDiagnostico = {
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
        diagnostico:{required:true}
    },
    messages:{
        diagnostico: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormDiagnostico = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-diagnostico?formDiagnostico=1',
    beforeSerialize:function(){        
        if($("#formGuardarDiagnostico").valid() == true){
            $('#msg_form_guardarDiagnostico').hide();
            $('#msg_form_guardarDiagnostico').removeClass("correcto").removeClass("error");
            $('#msg_form_guardarDiagnostico .msg').html("");
            setWaitingStatus('tabsFormDiagnostico', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('tabsFormDiagnostico', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_guardarDiagnostico .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_guardarDiagnostico .msg').html(data.mensaje);
            }
            $('#msg_form_guardarDiagnostico').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_guardarDiagnostico .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_guardarDiagnostico .msg').html(data.mensaje);
            }
            $('#msg_form_guardarDiagnostico').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventFormAgregarArchivo(iSeguimientoId)
{
    if($('#diagnosticoUpload').length){
        new Ajax_upload('#diagnosticoUpload', {
            action:'seguimientos/procesar-diagnostico',
            data:{
                fileDiagnosticoUpload:"1",
                iSeguimientoId:iSeguimientoId
            },
            name:'archivoDiagnostico',
            onChange:function(file , ext){
                if(confirm("Se eliminara el archivo anterior, desea realizar esta operacion?")){
                    return true;
                }else{
                    return false;
                }
            },
            onSubmit:function(file , ext){
                $('#msg_form_diagnostico').hide();
                $('#msg_form_diagnostico').removeClass("correcto").removeClass("error");
                $('#msg_form_diagnostico .msg').html("");
                setWaitingStatus('tabsFormDiagnostico', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                
                setWaitingStatus('tabsFormDiagnostico', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_diagnostico .msg').html(lang['error procesar']);
                    $('#msg_form_diagnostico').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';;');
                var resultado = dataInfo[0]; 
                var html = dataInfo[1]; 

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_diagnostico .msg').html(lang['error permiso']);
                    $('#msg_form_diagnostico').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_diagnostico .msg').html(html);
                    $('#msg_form_diagnostico').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_diagnostico .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_diagnostico').addClass("correcto").fadeIn('slow');

                    $('#wrapAntActual').html(html);
                }
                return;
            }
        });
    }    
}

$(document).ready(function(){

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

    $("#tabsFormDiagnostico").tabs();
    $("#diagnostico").maxlength({maxCharacters:1000});

    $("#formGuardarDiagnostico").validate(validateFormDiagnostico);
    $("#formGuardarDiagnostico").ajaxForm(optionsAjaxFormDiagnostico);

    bindEventFormAgregarArchivo($('#idSeguimiento').val());
});