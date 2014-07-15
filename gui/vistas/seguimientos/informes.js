var validateFormCabecera = {
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
        titulo:{required:true}
    },
    messages:{
        titulo: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormCabecera = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/informes/configuracion-guardar',
    beforeSerialize:function(){
        if($("#formCabecera").valid() == true){
            $('#msg_form_cabecera').hide();
            $('#msg_form_cabecera').removeClass("correcto").removeClass("error");
            $('#msg_form_cabecera .msg').html("");
            setWaitingStatus('formCabecera', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCabecera', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_cabecera .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_cabecera .msg').html(data.mensaje);
            }
            $('#msg_form_cabecera').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_cabecera .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_cabecera .msg').html(data.mensaje);
            }

            //refresco la vista preliminar
            refreshVistaPreliminar();
            $('#msg_form_cabecera').addClass("correcto").fadeIn('slow');
        }
    }
};

var optionsAjaxFormPie = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/informes/configuracion-guardar',
    beforeSerialize:function(){
        $('#msg_form_pie').hide();
        $('#msg_form_pie').removeClass("correcto").removeClass("error");
        $('#msg_form_pie .msg').html("");
        setWaitingStatus('formPie', true);
    },

    success:function(data){
        setWaitingStatus('formPie', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_pie .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_pie .msg').html(data.mensaje);
            }
            $('#msg_form_pie').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_pie .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_pie .msg').html(data.mensaje);
            }

            //refresco la vista preliminar
            refreshVistaPreliminar();
            $('#msg_form_pie').addClass("correcto").fadeIn('slow');
        }
    }
};

function refreshVistaPreliminar()
{
    $.ajax({
        url:"seguimientos/informes/procesar",
        type:"POST",
        data:{
            refrescarVistaPreliminar:"1"
        },
        beforeSend: function(){
            setWaitingStatus('vistaPreliminar', true);
        },
        success:function(data){
            setWaitingStatus('vistaPreliminar', false);
            $("#ajaxContPreliminar").html(data);
        }
    });
}

$(function(){
    if($("#formCabecera").length){
        $("#formCabecera").validate(validateFormCabecera);
        $("#formCabecera").ajaxForm(optionsAjaxFormCabecera);
    }

    if($("#formPie").length){
        $("#formPie").ajaxForm(optionsAjaxFormPie);
    }
});


