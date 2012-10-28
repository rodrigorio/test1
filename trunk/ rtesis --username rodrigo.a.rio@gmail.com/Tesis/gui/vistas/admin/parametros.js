function eliminarParametro(iParametroId){
    if(confirm("Se borrara el parametro dinamico del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/parametros-procesar",
            data:{
                iParametroId:iParametroId,
                eliminarParametro:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iParametroId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Parametro Dinamico");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Parametro Dinamico"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function crearParametro()
{
    var dialog = setWaitingStatusDialog(800, "FORMULARIO PARAMETRO");
    dialog.load(
        "admin/parametros-form",
        {
            crearParametro:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametro();
        }
    );
}

function editarParametro(iParametroId)
{
    var dialog = setWaitingStatusDialog(800, "FORMULARIO PARAMETRO");
    dialog.load(
        "admin/parametros-form",
        {
            editarParametro:"1",
            iParametroId:iParametroId
        },
        function(responseText, textStatus, XMLHttpRequest){
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
                //limpio el formulario
                $('#formParametro').each(function(){ this.reset() });                
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

var validateFormParametroSistema = {
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
        iParametroIdForm:{required:true},
        valor:{required:true}
    },
    messages:{
        iParametroIdForm:mensajeValidacion("requerido"),
        valor:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormParametroSistema = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formParametroSistema").valid() == true){
            $('#msg_form_parametroSistema').hide();
            $('#msg_form_parametroSistema').removeClass("success").removeClass("error2");
            $('#msg_form_parametroSistema .msg').html("");
            setWaitingStatus('formParametroSistema', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formParametroSistema', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_parametroSistema .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_parametroSistema .msg').html(data.mensaje);
            }
            $('#msg_form_parametroSistema').addClass("error2").fadeIn('slow');
        }else{
            if(data.asociarParametroSistema != undefined){
                if(data.mensaje == undefined){
                    $('#msg_form_parametroSistema .msg').html("El parametro fue asociado exitosamente al sistema.");
                }else{
                    $('#msg_form_parametroSistema .msg').html(data.mensaje);
                }
                //limpio el formulario
                $('#formParametroSistema').each(function(){ this.reset() });
            }else{
                if(data.mensaje == undefined){
                    $('#msg_form_parametroSistema .msg').html("El valor del parametro fue modificado exitosamente para el sistema");
                }else{
                    $('#msg_form_parametroSistema .msg').html(data.mensaje);
                }
            }
            $('#msg_form_parametroSistema').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormParametroControlador = {
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
        iParametroIdForm:{required:true},
        iControladorIdForm:{required:true},
        valor:{required:true}
    },
    messages:{
        iParametroIdForm:mensajeValidacion("requerido"),
        iControladorIdForm:mensajeValidacion("requerido"),
        valor:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormParametroControlador = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formParametroControlador").valid() == true){
            $('#msg_form_parametroControlador').hide();
            $('#msg_form_parametroControlador').removeClass("success").removeClass("error2");
            $('#msg_form_parametroControlador .msg').html("");
            setWaitingStatus('formParametroControlador', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formParametroControlador', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_parametroControlador .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_parametroControlador .msg').html(data.mensaje);
            }
            $('#msg_form_parametroControlador').addClass("error2").fadeIn('slow');
        }else{
            if(data.asociarParametroControlador != undefined){
                if(data.mensaje == undefined){
                    $('#msg_form_parametroControlador .msg').html("El parametro fue asociado al controlador exitosamente.");
                }else{
                    $('#msg_form_parametroControlador .msg').html(data.mensaje);
                }
                //limpio el formulario
                $('#formParametroControlador').each(function(){ this.reset() });
            }else{
                if(data.mensaje == undefined){
                    $('#msg_form_parametroControlador .msg').html("El valor del parametro para el controlador fue modificado exitosamente");
                }else{
                    $('#msg_form_parametroControlador .msg').html(data.mensaje);
                }
            }
            $('#msg_form_parametroControlador').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormParametroUsuarios = {
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
        iParametroIdForm:{required:true},
        valor:{required:true}
    },
    messages:{
        iParametroIdForm:mensajeValidacion("requerido"),
        valor:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormParametroUsuarios = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formParametroUsuarios").valid() == true){
            $('#msg_form_parametroUsuarios').hide();
            $('#msg_form_parametroUsuarios').removeClass("success").removeClass("error2");
            $('#msg_form_parametroUsuarios .msg').html("");
            setWaitingStatus('formParametroUsuarios', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formParametroUsuarios', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_parametroUsuarios .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_parametroUsuarios .msg').html(data.mensaje);
            }
            $('#msg_form_parametroUsuarios').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_parametroUsuarios .msg').html("El parametro fue asociado a todos los usuarios del sistema con éxito.");
            }else{
                $('#msg_form_parametroUsuarios .msg').html(data.mensaje);
            }

            //limpio el formulario
            $('#formParametroUsuarios').each(function(){ this.reset() });
            
            $('#msg_form_parametroUsuarios').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormModificarParametroUsuario = {
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
        iParametroIdForm:{required:true},
        iUsuarioIdForm:{required:true},
        valor:{required:true}
    },
    messages:{
        iParametroIdForm:mensajeValidacion("requerido"),
        iUsuarioIdForm:mensajeValidacion("requerido"),
        valor:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormModificarParametroUsuario = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formModificarParametroUsuario").valid() == true){
            $('#msg_form_modificarParametroUsuario').hide();
            $('#msg_form_modificarParametroUsuario').removeClass("success").removeClass("error2");
            $('#msg_form_modificarParametroUsuario .msg').html("");
            setWaitingStatus('formModificarParametroUsuario', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formModificarParametroUsuario', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_modificarParametroUsuario .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_modificarParametroUsuario .msg').html(data.mensaje);
            }
            $('#msg_form_modificarParametroUsuario').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_modificarParametroUsuario .msg').html("El valor del parametro para el usuario fue modificado exitosamente");
            }else{
                $('#msg_form_modificarParametroUsuario .msg').html(data.mensaje);
            }
            $('#msg_form_modificarParametroUsuario').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsFormParametro()
{
    $("#formParametro").validate(validateFormParametro);
    $("#formParametro").ajaxForm(optionsAjaxFormParametro);
}

function bindEventsFormParametroSistema()
{
    $("#formParametroSistema").validate(validateFormParametroSistema);
    $("#formParametroSistema").ajaxForm(optionsAjaxFormParametroSistema);
}

function bindEventsFormParametroControlador()
{
    $("#formParametroControlador").validate(validateFormParametroControlador);
    $("#formParametroControlador").ajaxForm(optionsAjaxFormParametroControlador);
}

function bindEventsFormParametroUsuarios()
{
    $("#formParametroUsuarios").validate(validateFormParametroUsuarios);
    $("#formParametroUsuarios").ajaxForm(optionsAjaxFormParametroUsuarios);
}

function bindEventsFormModificarParametroUsuario()
{
    $("#formModificarParametroUsuario").validate(validateFormModificarParametroUsuario);
    $("#formModificarParametroUsuario").ajaxForm(optionsAjaxFormModificarParametroUsuario);
}

function asociarParametroSistema()
{
    var dialog = setWaitingStatusDialog(800, "FORMULARIO PARAMETRO SISTEMA");
    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroSistema:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametroSistema();
        }
    );
}

function asociarParametroControlador()
{
    var dialog = setWaitingStatusDialog(800, "FORMULARIO PARAMETRO CONTROLADOR");
    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroControlador:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametroControlador();
        }
    );
}

function asociarParametroUsuarios()
{
    var dialog = setWaitingStatusDialog(800, "FORMULARIO PARAMETRO USUARIOS");
    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroUsuarios:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametroUsuarios();
        }
    );
}

function modificarValorParametroSistema(iParametroId)
{
    var dialog = setWaitingStatusDialog(800, "MODIFICAR VALOR PARAMETRO SISTEMA");
    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroSistema:"1",
            iParametroId:iParametroId
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametroSistema();
        }
    );
}

function modificarValorParametroControlador(iParametroId, iGrupoId)
{
    var dialog = setWaitingStatusDialog(800, "MODIFICAR VALOR PARAMETRO CONTROLADOR");
    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroControlador:"1",
            iParametroId:iParametroId,
            iControladorId:iGrupoId
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormParametroControlador();
        }
    );
}

function modificarValorParametroUsuario(iParametroId, iGrupoId)
{
    var dialog = setWaitingStatusDialog(800, "MODIFICAR VALOR PARAMETRO USUARIO");
    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroUsuario:"1",
            iParametroId:iParametroId,
            iUsuarioId:iGrupoId
        },
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFormModificarParametroUsuario();
        }
    );
}

function eliminarAsociacionParametroSistema(iParametroId){
    if(confirm("Se eliminara la asociacion entre el parametro dinamico y el sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/parametros-procesar",
            data:{
                iParametroId:iParametroId,
                eliminarAsociacionSistema:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iParametroId+".null").remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Asociacion Sistema");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Asociacion Sistema"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function eliminarAsociacionParametroControlador(iParametroId, iGrupoId){
    if(confirm("Se eliminara la asociacion entre el parametro dinamico y el controlador, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/parametros-procesar",
            data:{
                iParametroId:iParametroId,
                iControladorId:iGrupoId,
                eliminarAsociacionControlador:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iParametroId+"."+iGrupoId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Asociacion Controlador");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Asociacion Controlador"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function eliminarParametroUsuarios(iParametroId){
    if(confirm("Se eliminara la asociacion entre el parametro dinamico y los usuarios del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/parametros-procesar",
            data:{
                iParametroId:iParametroId,
                eliminarAsociacionUsuarios:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $(".usuarios_"+iParametroId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar parametro usuarios");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar parametro usuarios"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function listarParametrosUsuarios()
{
    var dialog = setWaitingStatusDialog(800, "Parámetros asociados a los usuarios del sistema");
    dialog.load(
        "admin/parametros-procesar",
        {
            listarParametrosAsociadosUsuarios:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){}
    );
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
    
    $(".asociarParametroUsuarios").live('click', function(){
        asociarParametroUsuarios();
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
    
    $(".listarParametrosUsuarios").live("click", function(){
        listarParametrosUsuarios();
    });
    $(".eliminarParametroUsuarios").live("click", function(){
        var iParametroId = $(this).attr("rel");
        eliminarParametroUsuarios(iParametroId);
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
        }              
    });    
});