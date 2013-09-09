var validateFormPronostico = {
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
        pronostico:{required:true}
    },
    messages:{
        pronostico:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormPronostico = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-pronostico',
    beforeSerialize:function(){

        if($("#formPronostico").valid() == true){
            $('#msg_form_pronostico').hide();
            $('#msg_form_pronostico').removeClass("correcto").removeClass("error");
            $('#msg_form_pronostico .msg').html("");
            setWaitingStatus('formPronostico', true);
        }else{
            return false;
        }
    },
    success:function(data){
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_pronostico .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_pronostico .msg').html(data.mensaje);
            }
            $('#msg_form_pronostico').addClass("error");
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_pronostico .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_pronostico .msg').html(data.mensaje);
            }
            $('#msg_form_pronostico').addClass("correcto");
        }

        setWaitingStatus('formPronostico', false);
        $('#msg_form_pronostico').fadeIn('slow');
    }
};

function bindEventsFormPronostico(){
    $("#formPronostico").validate(validateFormPronostico);
    $("#formPronostico").ajaxForm(optionsAjaxFormPronostico);
}

$(document).ready(function(){
    
    if($("#formPronostico").length){
        bindEventsFormPronostico();
    }
    
});
