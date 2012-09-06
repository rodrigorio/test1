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

var validateFormParametroUsuario = {
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

var optionsAjaxFormParametroUsuario = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/parametros-procesar',

    beforeSerialize: function($form, options){
        if($("#formParametroUsuario").valid() == true){
            $('#msg_form_parametroUsuario').hide();
            $('#msg_form_parametroUsuario').removeClass("success").removeClass("error2");
            $('#msg_form_parametroUsuario .msg').html("");
            setWaitingStatus('formParametroUsuario', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formParametroUsuario', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_parametroUsuario .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_parametroUsuario .msg').html(data.mensaje);
            }
            $('#msg_form_parametroUsuario').addClass("error2").fadeIn('slow');
        }else{
            if(data.asociarParametroUsuario != undefined){
                if(data.mensaje == undefined){
                    $('#msg_form_parametroUsuario .msg').html("El parametro fue asociado al usuario exitosamente.");
                }else{
                    $('#msg_form_parametroUsuario .msg').html(data.mensaje);
                }

                //limpio el formulario
                $('#usuario_clean').click();
                $('#formParametroUsuario').each(function(){ this.reset() });
            }else{
                if(data.mensaje == undefined){
                    $('#msg_form_parametroUsuario .msg').html("El valor del parametro para el usuario fue modificado exitosamente");
                }else{
                    $('#msg_form_parametroUsuario .msg').html(data.mensaje);
                }
            }
            $('#msg_form_parametroUsuario').addClass("success").fadeIn('slow');
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

function bindEventsFormParametroUsuario()
{
    //solo para crear la asociacion
    if($("#crearAsociacionUsuario").length){
        if($("#usuarioId").val() != ""){
            $("#usuario").addClass("selected");
            $("#usuario").attr("readonly", "readonly");
            revelarElemento($('#usuario_clean'));
        }
        
        //Para el autocomplete de usuarios
        $("#usuario").autocomplete({
            source:function(request, response){
                $.ajax({
                    url:"admin/usuarios-procesar",
                    dataType:"jsonp",
                    data:{
                        usuariosAutocomplete:'1',
                        limit:12,
                        str:request.term
                    },
                    beforeSend: function(){
                        revelarElemento($("#usuario_loading"));
                    },
                    success: function(data){
                        ocultarElemento($("#usuario_loading"));
                        response($.map(data.usuarios, function(usuarios){
                            return{
                                //lo que aparece en el input
                                value:usuarios.nombre,
                                //lo que aparece en la lista generada para elegir
                                label:usuarios.nombre+' - ID: '+usuarios.id,
                                //valor extra que se devuelve para completar el hidden
                                id:usuarios.id
                            }
                        }));
                    }
                });
            },
            minLength: 1,
            select: function(event, ui){
                if(ui.item){
                    $("#usuarioId").val(ui.item.id);
                }else{
                    $("#usuarioId").val("");
                }
            },
            close: function(){
                if($("#usuarioId").val() != ""){
                    $(this).addClass("selected");
                    $(this).attr('readonly', 'readonly');
                    revelarElemento($('#usuario_clean'));
                }
            }
        });

        //para borrar la institucion seleccionada con el autocomplete
        $('#usuario_clean').click(function(){
            $("#usuario").removeClass("selected");
            $("#usuario").removeAttr("readonly");
            $("#usuario").val("");
            $("#usuarioId").val("");
            ocultarElemento($(this));
        });

        $('#usuario').blur(function(){
            if($("#usuarioId").val() == ""){
                $("#usuario").val("");
            }
            if($("#usuario").val() == ""){
                $("#usuarioId").val("");
            }
        });
    }

    $("#formParametroUsuario").validate(validateFormParametroUsuario);
    $("#formParametroUsuario").ajaxForm(optionsAjaxFormParametroUsuario);
}

function asociarParametroSistema()
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO SISTEMA"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroSistema:"1"
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

            bindEventsFormParametroSistema();
        }
    );
}

function asociarParametroControlador()
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO CONTROLADOR"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroControlador:"1"
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

            bindEventsFormParametroControlador();
        }
    );
}

function asociarParametroUsuario()
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO USUARIO"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            asociarParametroUsuario:"1"
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

            bindEventsFormParametroUsuario();
        }
    );
}

function modificarValorParametroSistema(iParametroId)
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO SISTEMA"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroSistema:"1",
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

            bindEventsFormParametroSistema();
        }
    );
}

function modificarValorParametroControlador(iParametroId, iGrupoId)
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO CONTROLADOR"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroControlador:"1",
            iParametroId:iParametroId,
            iControladorId:iGrupoId
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

            bindEventsFormParametroControlador();
        }
    );
}

function modificarValorParametroUsuario(iParametroId, iGrupoId)
{
    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="FORMULARIO PARAMETRO USUARIO"></div>').appendTo('body');

    dialog.load(
        "admin/parametros-form",
        {
            modificarValorParametroUsuario:"1",
            iParametroId:iParametroId,
            iUsuarioId:iGrupoId
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

            bindEventsFormParametroUsuario();
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

function eliminarAsociacionParametroUsuario(iParametroId, iGrupoId){
    if(confirm("Se eliminara la asociacion entre el parametro dinamico y el usuario, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/parametros-procesar",
            data:{
                iParametroId:iParametroId,
                iUsuarioId:iGrupoId,
                eliminarAsociacionUsuario:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iParametroId+"."+iGrupoId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Asociacion Usuario");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Asociacion Usuario"></div>').appendTo('body');
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