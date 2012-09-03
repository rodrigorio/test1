function crearParametro()
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            crearParametro:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:800,
                resizable:false,
                draggable:true,
                modal:false,
                closeOnEscape:true
            });

            bindEventsFormParametro();
        }
    );
}

function editarParametro(iParametroId)
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            editarParametro:"1",
            iParametroId:iParametroId
        },
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:800,
                resizable:false,
                draggable:true,
                modal:false,
                closeOnEscape:true
            });

            bindEventsFormParametro();
        }
    );    
}

jQuery.validator.addMethod("nowhitespace", function(value, element) {
	return this.optional(element) || /^\S+$/i.test(value);
}, "Sin espacios en blanco. Utilice guiones bajos.");

var validateFormParametro = {
    errorElement: "span",
    validClass: "valid-side-note",
    errorClass: "invalid-side-note",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    highlight: function(element, errorClass, validClass){
        $(element).addClass("invalid");
    },
    unhighlight: function(element, errorClass, validClass){
        $(element).removeClass("invalid");
    },
    rules:{
        namespace:{required:true, nowhitespace:true},
        tipo:{required:true},
        descripcion:{required:true}
    },
    messages:{
        namespace:{required: mensajeValidacion("requerido")},
        tipo: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormParametro = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formParametro").valid() == true){
            $('#msg_form_parametro').hide();
            $('#msg_form_parametro').removeClass("success").removeClass("error2");
            $('#msg_form_parametro .msg').html("");
            setWaitingStatus('formParametro', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formParametro', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_parametro .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_parametro .msg').html(data.mensaje);
            }
            $('#msg_form_parametro').addClass("error2").fadeIn('slow');
        }else{
            if(data.crearParametro != undefined){
                if(data.mensaje == undefined){
                    $('#msg_form_parametro .msg').html("El parametro fue agregado exitosamente.");
                }else{
                    $('#msg_form_parametro .msg').html(data.mensaje);
                }
            }else{
                if(data.mensaje == undefined){
                    $('#msg_form_parametro .msg').html("El parametro fue modificado exitosamente");
                }else{
                    $('#msg_form_parametro .msg').html(data.mensaje);
                }
            }
            $('#msg_form_parametro').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsFormParametro()
{
    $("#formParametro").validate(validateFormParametro);
    $("#formParametro").ajaxForm(optionsAjaxFormParametro);
}

$(document).ready(function(){

    $(".crearParametro").live('click', function(){
        crearParametro();
        return false;
    });
    
    $(".asociarParametroSistema").live('click', function(){
        asociarParametroSistema();
        return false;
    });

    $(".asociarParametroControlador").live('click', function(){
        asociarParametroControlador();
        return false;
    });
    
    $(".asociarParametroUsuario").live('click', function(){
        asociarParametroUsuario();
        return false;
    });
    
    $(".eliminarParametro").live("click", function(){
        var iParametroId = $(this).attr("rel");
        eliminarParametro(iParametroId);
    });

    $(".editarParametro").live("click", function(){
        var iParametroId = $(this).attr("rel");
        editarParametro(iParametroId);
    });

    $(".modificarValorParametro").live("click", function(){
        var rel = $(this).attr("rel").split('_');
        
        var tipoAsociacion = rel[0];
        var iParametroId = rel[1];
        var iGrupoId = rel[2];

        switch(tipoAsociacion){
            case 'ParametroSistema':
                modificarValorParametroSistema(iParametroId);
                break;
            case 'ParametroControlador':
                modificarValorParametroControlador(iParametroId, iGrupoId);
                break;
            case 'ParametroUsuario':
                modificarValorParametroUsuario(iParametroId, iGrupoId);
                break;
        }      
    });

    $(".eliminarAsociacionParametro").live("click", function(){
        var rel = $(this).attr("rel").split('_');

        var tipoAsociacion = rel[0];
        var iParametroId = rel[1];
        var iGrupoId = rel[2];

        switch(tipoAsociacion){
            case 'ParametroSistema':
                eliminarAsociacionParametroSistema(iParametroId);
                break;
            case 'ParametroControlador':
                eliminarAsociacionParametroControlador(iParametroId, iGrupoId);
                break;
            case 'ParametroUsuario':
                eliminarAsociacionParametroUsuario(iParametroId, iGrupoId);
                break;
        }              
    });
});