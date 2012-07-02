//validacion y submit
var validateFormPublicacion = {
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
        descripcionBreve:{required:true},
        descripcion:{required:true},
        keywords:{required:true},
        activo:{required:true},
        publico:{required:true},
        activoComentarios:{required:true}
    },
    messages:{
        titulo: mensajeValidacion("requerido"),
        descripcionBreve: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        keywords: mensajeValidacion("requerido"),
        activo: mensajeValidacion("requerido"),
        publico: mensajeValidacion("requerido"),
        activoComentarios: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormPublicacion = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/publicaciones-procesar',
    beforeSerialize:function(){

        if($("#formPublicacion").valid() == true){

            $('#msg_form_publicacion').hide();
            $('#msg_form_publicacion').removeClass("success").removeClass("error2");
            $('#msg_form_publicacion .msg').html("");
            setWaitingStatus('formPublicacion', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formPublicacion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_publicacion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_publicacion .msg').html(data.mensaje);
            }
            $('#msg_form_publicacion').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_publicacion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_publicacion .msg').html(data.mensaje);
            }            
            $('#msg_form_publicacion').addClass("success").fadeIn('slow');
        }
    }
};

//validacion y submit
var validateFormReview = {
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
        itemEventSummary:{required:function(element){
                            return $("#itemType option:selected").val() == "event";
                         }},
        item:{required:true},
        titulo:{required:true},
        descripcionBreve:{required:true},
        descripcion:{required:true},
        keywords:{required:true},
        activo:{required:true},
        publico:{required:true},
        activoComentarios:{required:true},
        itemUrl:{url:true},
        fuenteOriginal:{url:true}
    },
    messages:{
        itemEventSummary: mensajeValidacion("requerido"),
        item: mensajeValidacion("requerido"),
        titulo: mensajeValidacion("requerido"),
        descripcionBreve: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        keywords: mensajeValidacion("requerido"),
        activo: mensajeValidacion("requerido"),
        publico: mensajeValidacion("requerido"),
        activoComentarios: mensajeValidacion("requerido"),
        itemUrl: mensajeValidacion("url"),
        fuenteOriginal: mensajeValidacion("url")
    }
};

var optionsAjaxFormReview = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/publicaciones-procesar',
    beforeSerialize:function(){
        if($("#formReview").valid() == true){

            $('#msg_form_review').hide();
            $('#msg_form_review').removeClass("success").removeClass("error2");
            $('#msg_form_review .msg').html("");
            setWaitingStatus('formReview', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formReview', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_review .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_review .msg').html(data.mensaje);
            }
            $('#msg_form_review').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_review .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_review .msg').html(data.mensaje);
            }
            $('#msg_form_review').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormModeracion = {
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
        mensaje:{required:true}
    },
    messages:{
        mensaje: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormModeracion = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/publicaciones-procesar',
    beforeSerialize:function(){

        alert("entro entro");

        return false;
        
        if($("#formReview").valid() == true){

            $('#msg_form_review').hide();
            $('#msg_form_review').removeClass("success").removeClass("error2");
            $('#msg_form_review .msg').html("");
            setWaitingStatus('formReview', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formReview', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_review .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_review .msg').html(data.mensaje);
            }
            $('#msg_form_review').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_review .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_review .msg').html(data.mensaje);
            }
            $('#msg_form_review').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsPublicacionForm(){
    $("#formPublicacion").validate(validateFormPublicacion);
    $("#formPublicacion").ajaxForm(optionsAjaxFormPublicacion);
}

function bindEventsReviewForm(){
    $("#formReview").validate(validateFormReview);
    $("#formReview").ajaxForm(optionsAjaxFormReview);
    selectItemTypeReviewEvent();
}

function bindEventsModeracionForm(){
    $(".moderarPublicacion").validate(validateFormModeracion);
    $(".moderarPublicacion").ajaxForm(optionsAjaxFormModeracion);    
}

function selectItemTypeReviewEvent(){
    //el item event summary es visible solo si elige evento en el select de itemType
    if( $("#itemType option:selected").val() == "event" ){
        $("#itemEventSummaryFormLine").show();
    }

    $("#itemType").change(function(){
        if( $("#itemType option:selected").val() == "event" ){
            $("#itemEventSummaryFormLine").show();
        }else{
            $("#itemEventSummaryFormLine").hide();
            $("#itemEventSummaryFormLine").val("");
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
    url: 'admin/publicaciones-procesar?guardarFoto=1',
    beforeSerialize:function(){
        if($("#formFoto").valid() == true){
            $('#msg_form_foto').hide();
            $('#msg_form_foto').removeClass("correcto").removeClass("error");
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
            $('#msg_form_foto').addClass("error").fadeIn('slow');
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

var validateFormAgregarVideo = {
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
        codigo:{required:true, url:true}
    },
    messages:{
        codigo:{
            required: mensajeValidacion("requerido"),
            url: mensajeValidacion("url")
        }
    }
};

var validateFormEditarVideo = {
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

var optionsAjaxFormEditarVideo = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/publicaciones-procesar?guardarVideo=1',
    beforeSerialize:function(){
        if($("#formEditarVideo").valid() == true){
            $('#msg_form_editar_video').hide();
            $('#msg_form_editar_video').removeClass("correcto").removeClass("error");
            $('#msg_form_editar_video .msg').html("");
            setWaitingStatus('formEditarVideo', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formEditarVideo', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_editar_video .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_editar_video .msg').html(data.mensaje);
            }
            $('#msg_form_editar_video').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_editar_video .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_editar_video .msg').html(data.mensaje);
            }
            $('#msg_form_editar_video').addClass("success").fadeIn('slow');
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
    url: 'admin/publicaciones-procesar?guardarArchivo=1',
    beforeSerialize:function(){
        if($("#formArchivo").valid() == true){
            $('#msg_form_archivo').hide();
            $('#msg_form_archivo').removeClass("correcto").removeClass("error");
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
            $('#msg_form_archivo').addClass("error").fadeIn('slow');
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

function bindEventsAgregarVideoForm(){
    $("#formAgregarVideo").validate(validateFormAgregarVideo);
    $("#formAgregarVideo").ajaxForm(optionsAjaxFormAgregarVideo);
}

function bindEventsEditarVideoForm(){
    $("#formEditarVideo").validate(validateFormEditarVideo);
    $("#formEditarVideo").ajaxForm(optionsAjaxFormEditarVideo);
}

function cambiarEstadoPublicacion(iPublicacionId, valor, tipo){
    $.ajax({
        type: "POST",
        url: "admin/publicaciones-procesar",
        data:{
            iPublicacionId:iPublicacionId,
            estadoPublicacion:valor,
            cambiarEstado:"1",
            objType:tipo
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisPublicaciones', true);
        },
        success: function(data){
            setWaitingStatus('listadoMisPublicaciones', false);
        }
    });
}

/**
 * Tipo es Publicacion/Review
 */
function editarPublicacion(iPublicacionId, tipo){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Modificar '+tipo+'"></div>').appendTo('body');

    dialog.load(
        "admin/publicaciones-form",
        {
            iPublicacionId:iPublicacionId,
            objType:tipo
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

            if(tipo == "publicacion"){
                bindEventsPublicacionForm();
            }else{
                bindEventsReviewForm();
            }
        }
    );
}

/**
 * Tipo es Publicacion/Review
 */
function ampliarPublicacion(iPublicacionId, tipo){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Ficha '+tipo+' ID: '+iPublicacionId+'"></div>').appendTo('body');

    dialog.load(
        "admin/publicaciones-procesar",
        {
            ampliarPublicacion:"1",
            iPublicacionId:iPublicacionId,
            objType:tipo
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
        "admin/publicaciones-procesar",
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

function editarVideo(iEmbedVideoId){

    var dialog = $("#dialog2");
    if ($("#dialog2").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div class="zin2" id="dialog" title="Editar Video"></div>').appendTo('body');

    dialog.load(
        "admin/publicaciones-procesar",
        {
            iEmbedVideoId:iEmbedVideoId,
            formVideo:"1"
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

            bindEventsEditarVideoForm();
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
        "admin/publicaciones-procesar",
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

function masPublicaciones(){

    var filtroTitulo = $('#filtroTitulo').val();
    var filtroApellidoAutor = $('#filtroApellidoAutor').val();
    var filtroFechaDesde = $('#filtroFechaDesde').val();
    var filtroFechaHasta = $('#filtroFechaHasta').val();

    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"admin/publicaciones-procesar",
        data:{
            masPublicaciones:"1",
            filtroTitulo: filtroTitulo,
            filtroApellidoAutor: filtroApellidoAutor,
            filtroFechaDesde: filtroFechaDesde,
            filtroFechaHasta: filtroFechaHasta,
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoPublicaciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoPublicaciones', false);
            $("#listadoPublicacionesResult").html(data);
        }
    });
}

function borrarPublicacion(iPublicacionId, tipo){
    if(confirm("Se borrara la publicacion del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/publicaciones-procesar",
            data:{
                iPublicacionId:iPublicacionId,
                objType:tipo,
                borrarPublicacion:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha
                    $("."+iPublicacionId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title", "Borrar Publicación");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Publicación"></div>').appendTo('body');
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
    if(confirm("Se borrara el comentario de la publicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/publicaciones-procesar",
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
    if(confirm("Se borrara la foto de la publicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/publicaciones-procesar",
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

function borrarVideo(iEmbedVideoId){
    if(confirm("Se borrara el video de la publicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/publicaciones-procesar",
            data:{
                iEmbedVideoId:iEmbedVideoId,
                eliminarVideo:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#video_"+iEmbedVideoId).hide("slow", function(){
                        $("#video_"+iEmbedVideoId).remove();
                    });
                }
            }
        });
    }
}

function borrarArchivo(iArchivoId){
    if(confirm("Se borrara el archivo de la publicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/publicaciones-procesar",
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

    $("#BuscarPublicaciones").live('click', function(){
        masPublicaciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarPublicaciones').each(function(){
          this.reset();
        });
        return false;
    });

    $(".editarPublicacion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];
        editarPublicacion(iPublicacionId, tipo);
        return false;
    });

    $(".ampliarPublicacion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];
        ampliarPublicacion(iPublicacionId, tipo);
        return false;
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masPublicaciones();
    });

    $(".cambiarEstadoPublicacion").live("change", function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];
        cambiarEstadoPublicacion(iPublicacionId, $("#estadoPublicacion_"+iPublicacionId+" option:selected").val(), tipo);
    });

    $(".borrarPublicacion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];
        borrarPublicacion(iPublicacionId, tipo);
    });

    $(".borrarComentario").live('click', function(){
        var iComentarioId = $(this).attr("rel");
        borrarComentario(iComentarioId);
    });

    var iPublicacionId = $("#iItemIdForm").val();
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

    $(".editarVideo").live('click', function(){
        var iEmbedVideoId = $(this).attr("rel");
        editarVideo(iEmbedVideoId);
        return false;
    });

    $(".borrarVideo").live('click', function(){
        var iEmbedVideoId = $(this).attr("rel");
        borrarVideo(iEmbedVideoId);
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

    bindEventsModeracionForm();
});