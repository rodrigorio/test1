//validacion y submit
var validateFormSoftware = {
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
        titulo:{required:true},
        categoria:{required:true},
        descripcionBreve:{required:true},
        activo:{required:true},
        publico:{required:true},
        activoComentarios:{required:true},
        descripcion:{required:true}        
    },
    messages:{
        titulo: mensajeValidacion("requerido"),
        categoria: mensajeValidacion("requerido"),
        descripcionBreve: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        activo: mensajeValidacion("requerido"),
        publico: mensajeValidacion("requerido"),
        activoComentarios: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormSoftware = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/software-procesar',
    beforeSerialize:function(){
        if($("#formSoftware").valid() == true){

            $('#msg_form_software').hide();
            $('#msg_form_software').removeClass("success").removeClass("error2");
            $('#msg_form_software .msg').html("");
            setWaitingStatus('formSoftware', true);

        }else{
            return false;
        }
    },
    success:function(data){
        setWaitingStatus('formSoftware', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_software .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_software .msg').html(data.mensaje);
            }
            $('#msg_form_software').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_software .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_software .msg').html(data.mensaje);
            }
            $('#msg_form_software').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsSoftwareForm(){
    $("#formSoftware").validate(validateFormSoftware);
    $("#formSoftware").ajaxForm(optionsAjaxFormSoftware);

    //agregar y limpiar enlaces
    $("#limpiarEnlaces").click(function(){
        $("#enlaces").html("");
        $("#enlacesHtml").html("");
        return false;
    });

    $("#enlace").click(function(){ $(this).removeClass('error'); });
    $("#agregarEnlace").click(function(){

        $('#enlace').removeClass('error2');
        var url = $('#enlace').val();

        if(url == $('#enlace').attr('title')){
            return false;
        }

        if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url)){
            var contenidoActual = $("#enlaces").html();
            var contenidoActualHtml = $("#enlacesHtml").html();
            var nuevoEnlace = "<a target='_blank' href='" + url + "'>" + url + "</a><br>";
            $("#enlaces").html(contenidoActual + nuevoEnlace);
            $("#enlacesHtml").html(contenidoActualHtml + nuevoEnlace);
            $('#enlace').val('');
        }else{
            $('#enlace').addClass('error');
        }
    });
}

var validateFormFoto = {
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
        orden:{digits:true, range:[1, 9999]}
    },
    messages:{
        orden:{
            digits:mensajeValidacion("digitos"),
            range:"El numero de orden debe ser un numero positivo mayor a 1."
        }
    }
};

var optionsAjaxFormFoto = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/software-procesar?guardarFoto=1',
    beforeSerialize:function(){
        if($("#formFoto").valid() == true){
            $('#msg_form_foto').hide();
            $('#msg_form_foto').removeClass("success").removeClass("error2");
            $('#msg_form_foto .msg').html("");
            setWaitingStatus('formFoto', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formFoto', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_foto .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_foto .msg').html(data.mensaje);
            }
            $('#msg_form_foto').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_foto .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_foto .msg').html(data.mensaje);
            }
            $('#msg_form_foto').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormArchivo = {
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
        orden:{digits:true, range:[1, 9999]}
    },
    messages:{
        orden:{
            digits:mensajeValidacion("digitos"),
            range:"El numero de orden debe ser un numero positivo mayor a 1."
        }
    }
};

var optionsAjaxFormArchivo = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/software-procesar?guardarArchivo=1',
    beforeSerialize:function(){
        if($("#formArchivo").valid() == true){
            $('#msg_form_archivo').hide();
            $('#msg_form_archivo').removeClass("success").removeClass("error2");
            $('#msg_form_archivo .msg').html("");
            setWaitingStatus('formArchivo', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formArchivo', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_archivo .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_archivo .msg').html(data.mensaje);
            }
            $('#msg_form_archivo').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_archivo .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_archivo .msg').html(data.mensaje);
            }
            $('#msg_form_archivo').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsFotoForm(){
    $("#formFoto").validate(validateFormFoto);
    $("#formFoto").ajaxForm(optionsAjaxFormFoto);
}

function bindEventsArchivoForm(){
    $("#formArchivo").validate(validateFormArchivo);
    $("#formArchivo").ajaxForm(optionsAjaxFormArchivo);
}

function cambiarEstadoSoftware(iSoftwareId, valor){
    $.ajax({
        type: "POST",
        url: "admin/software-procesar",
        data:{
            iSoftwareId:iSoftwareId,
            estadoSoftware:valor,
            cambiarEstado:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoSoftware', true);
        },
        success: function(data){
            setWaitingStatus('listadoSoftware', false);
        }
    });
}

function editarSoftware(iSoftwareId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Modificar Software"></div>').appendTo('body');

    dialog.load(
        "admin/software-form",
        {
            iSoftwareId:iSoftwareId
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

            bindEventsSoftwareForm();
        }
    );
}

function ampliarSoftware(iSoftwareId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Ficha Software ID: '+iSoftwareId+'"></div>').appendTo('body');

    dialog.load(
        "admin/software-procesar",
        {
            ampliarSoftware:"1",
            iSoftwareId:iSoftwareId
        },
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:700,
                resizable:false,
                draggable:true,
                modal:false,
                closeOnEscape:true
            });

            bindEventsAdmin();
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    );
}

function editarFoto(iFotoId){

    var dialog = $("#dialog2");
    if ($("#dialog2").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div class="zin2" id="dialog2" title="Editar Foto"></div>').appendTo('body');

    dialog.load(
        "admin/software-procesar",
        {
            iFotoId:iFotoId,
            formFoto:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:700,
                resizable:false,
                draggable:true,
                modal:false,
                closeOnEscape:true
            });

            bindEventsFotoForm();
        }
    );
}

function editarArchivo(iArchivoId){

    var dialog = $("#dialog2");
    if ($("#dialog2").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div class="zin2" id="dialog" title="Editar Archivo"></div>').appendTo('body');

    dialog.load(
        "admin/software-procesar",
        {
            iArchivoId:iArchivoId,
            formArchivo:"1"
        },
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:700,
                resizable:false,
                draggable:true,
                modal:false,
                closeOnEscape:true
            });

            bindEventsArchivoForm();
        }
    );
}

function masSoftware(){

    var filtroTitulo = $('#filtroTitulo').val();
    var filtroApellidoAutor = $('#filtroApellidoAutor').val();
    var filtroFechaDesde = $('#filtroFechaDesde').val();
    var filtroFechaHasta = $('#filtroFechaHasta').val();
    var filtroCategoria = $('#filtroCategoria').val();

    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"admin/software-procesar",
        data:{
            masSoftware:"1",
            filtroTitulo: filtroTitulo,
            filtroApellidoAutor: filtroApellidoAutor,
            filtroFechaDesde: filtroFechaDesde,
            filtroFechaHasta: filtroFechaHasta,
            filtroCategoria: filtroCategoria,
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoSoftware', true);
        },
        success:function(data){
            setWaitingStatus('listadoSoftware', false);
            $("#listadoSoftwareResult").html(data);
        }
    });
}

function masModeraciones(){

    $.ajax({
        type:"POST",
        url:"admin/software-procesar",
        data:{
            masModeraciones:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoModeraciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoModeraciones', false);
            $("#listadoModeracionesResult").html(data);
        }
    });
}

function borrarSoftware(iSoftwareId){
    if(confirm("Se borrara la aplicacion del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/software-procesar",
            data:{
                iSoftwareId:iSoftwareId,
                borrarSoftware:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iSoftwareId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title", "Borrar Software");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Software"></div>').appendTo('body');
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

function borrarComentario(iComentarioId){
    if(confirm("Se borrara el comentario de la aplicacion, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/software-procesar",
            data:{
                iComentarioId:iComentarioId,
                eliminarComentario:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#comentario_"+iComentarioId).hide("slow", function(){
                        $("#comentario_"+iComentarioId).remove();
                    });
                }
            }
        });
    }
}

function borrarFoto(iFotoId){
    if(confirm("Se borrara la foto de la aplicacion, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/software-procesar",
            data:{
                iFotoId:iFotoId,
                eliminarFoto:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#foto_"+iFotoId).hide("slow", function(){
                        $("#foto_"+iFotoId).remove();
                    });
                }
            }
        });
    }
}

function borrarArchivo(iArchivoId){
    if(confirm("Se borrara el archivo de la aplicacion, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/software-procesar",
            data:{
                iArchivoId:iArchivoId,
                eliminarArchivo:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#archivo_"+iArchivoId).hide("slow", function(){
                        $("#archivo_"+iArchivoId).remove();
                    });                    
                }
            }
        });
    }
}

function borrarFotoPerfil(iUsuarioId){
    if(confirm("Se borrara la foto de perfil del usuario, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/usuarios-procesar",
            data:{
                iUsuarioId:iUsuarioId,
                borrarFotoPerfil:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#fotoPerfilActualCont").hide("slow", function(){
                        $("#fotoPerfilActualCont").remove();
                    });
                }
            }
        });
    }
}

$(document).ready(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

    $("#filtroFechaDesde").datepicker();
    $("#filtroFechaHasta").datepicker();

    $("#buscarSoftware").live('click', function(){
        masSoftware();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarSoftware').each(function(){
          this.reset();
        });
        return false;
    });

    $(".editarSoftware").live('click', function(){
        var iSoftwareId = $(this).attr("rel");
        editarSoftware(iSoftwareId);
        return false;
    });

    $(".ampliarSoftware").live('click', function(){
        var iSoftwareId = $(this).attr("rel");
        ampliarSoftware(iSoftwareId);
        return false;
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masSoftware();
    });

    $(".cambiarEstadoSoftware").live("change", function(){
        var iSoftwareId = $(this).attr("rel");
        cambiarEstadoSoftware(iSoftwareId, $("#estadoSoftware_"+iSoftwareId+" option:selected").val());
    });

    $(".borrarSoftware").live('click', function(){
        var iSoftwareId = $(this).attr("rel");
        borrarSoftware(iSoftwareId);
    });

    $(".borrarComentario").live('click', function(){
        var iComentarioId = $(this).attr("rel");
        borrarComentario(iComentarioId);
    });

    var iSoftwareId = $("#iItemIdForm").val();
    var sTipoItemForm = $("#sTipoItemForm").val();

    $(".borrarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        borrarFoto(iFotoId);
        return false;
    });

    $(".editarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        editarFoto(iFotoId);
        return false;
    });

    $(".borrarArchivo").live('click', function(){
        var iArchivoId = $(this).attr("rel");
        borrarArchivo(iArchivoId);
        return false;
    });

    $(".editarArchivo").live('click', function(){
        var iArchivoId = $(this).attr("rel");
        editarArchivo(iArchivoId);
        return false;
    });

    $(".verFichaUsuario").live('click',function(){

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='"+$(this).html()+"'></div>").appendTo('body');

        dialog.load(
            "admin/usuarios-procesar?ver=1&iUsuarioId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:650,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });
                bindEventsAdmin();
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });

    //ficha usuario autor publicacion
    $("#fotoPerfilBorrar").live('click', function(){
        var iUsuarioId = $(this).attr("rel");
        borrarFotoPerfil(iUsuarioId);
        return false; //porq es un <a>
    });

    //estos formularios los valido a mano sin plugin porque se repiten por cada fila y hay problemas
    $('.moderarSubmit').live('click', function()
    {
        var mensajeValidacion = "Todos los campos son obligatorios";
        var iSoftwareId = $(this).attr('rel');
        var cartel = $('#msg_form_moderacion_'+iSoftwareId);
        var mensajeCont = $('#msg_form_moderacion_'+iSoftwareId+" .msg");

        cartel.hide();
        cartel.removeClass("correcto").removeClass("error2");
        mensajeCont.html("");
        
        if(!$('#aprobar_'+iSoftwareId).is(':checked') &&
           !$('#rechazar_'+iSoftwareId).is(':checked')){

            mensajeCont.html(mensajeValidacion);
            cartel.addClass("error").fadeIn('slow');
            return false;
        }

        
        if($('#mensaje_'+iSoftwareId).val() == ""){
            mensajeCont.html(mensajeValidacion);
            cartel.addClass("error").fadeIn('slow');
            return false;
        }
        
        var estado;
        if($('#aprobar_'+iSoftwareId).is(':checked')){
            estado = $('#aprobar_'+iSoftwareId).val();
        }else{
            estado = $('#rechazar_'+iSoftwareId).val();
        }

        $.ajax({
            type: "POST",
            url: "admin/software-procesar",
            data:{
                iModeracionId: function(){return $('#moderacionId_'+iSoftwareId).val(); },
                moderarSoftware: "1",
                estado: estado,
                mensaje: function(){return $('#mensaje_'+iSoftwareId).val(); }
            },
            beforeSend: function(){
                setWaitingStatus('listadoModeraciones', true);
            },
            success: function(data){
                setWaitingStatus('listadoModeraciones', false);

                if(data.success != undefined && data.success == 1){
                    $("."+iSoftwareId).hide("slow", function(){
                        $("."+iSoftwareId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title", "Moderar Aplicacion ID: "+iSoftwareId);
                }else{
                    dialog = $('<div id="dialog" title="Moderar Aplicacion ID: '+iSoftwareId+'"></div>').appendTo('body');
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
    });
    
});