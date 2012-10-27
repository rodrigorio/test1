//validacion y submit
var validateFormPublicacion = {
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
    url: 'comunidad/publicaciones/guardar-publicacion',
    beforeSerialize:function(){

        if($("#formPublicacion").valid() == true){

            $('#msg_form_publicacion').hide();
            $('#msg_form_publicacion').removeClass("correcto").removeClass("error");
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
            $('#msg_form_publicacion').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){            
                $('#msg_form_publicacion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_publicacion .msg').html(data.mensaje);
            }
            if(data.agregarPublicacion != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formPublicacion').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_publicacion').addClass("correcto").fadeIn('slow');
        }
    }
};

var validateFormFoto = {
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
    url: 'comunidad/publicaciones/galeria-fotos/procesar?guardarFoto=1',
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
            //si guardo bien directamente cierro el dialog
            if($("#dialog").length != 0){
                $("#dialog").hide("slow").remove();
            }            
        }
    }
};

var validateFormAgregarVideo = {
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
        codigo:{required:true, url:true}
    },
    messages:{
        codigo:{
            required: mensajeValidacion("requerido"),
            url: mensajeValidacion("url")
        }
    }
};

var optionsAjaxFormAgregarVideo = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/publicaciones/galeria-videos/procesar?agregarVideo=1',
    data:{
        iPublicacionId: function(){return $("#iItemIdForm").val()},
        objType: function(){return $("#sTipoItemForm").val()}
    },
    beforeSerialize:function(){
        if($("#formAgregarVideo").valid() == true){
            $('#msg_form_agregar_video').hide();
            $('#msg_form_agregar_video').removeClass("correcto").removeClass("error");
            $('#msg_form_agregar_video .msg').html("");
            setWaitingStatus('formAgregarVideo', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formAgregarVideo', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_agregar_video .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_agregar_video .msg').html(data.mensaje);
            }
            $('#msg_form_agregar_video').addClass("error").fadeIn('slow');
        }else{
            $('#msg_form_agregar_video .msg').html("Se agrego el video con exito");
            $('#msg_form_agregar_video').addClass("correcto").fadeIn('slow');

            $('#Thumbnails').append(data.html);
            $("a[rel^='prettyPhoto']").prettyPhoto();
            if($('#msgNoRecord').length){ $('#msgNoRecord').hide(); }
        }
    }
};

var validateFormEditarVideo = {
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
    url: 'comunidad/publicaciones/galeria-videos/procesar?guardarVideo=1',
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
            //si guardo bien directamente cierro el dialog
            if($("#dialog").length != 0){
                $("#dialog").hide("slow").remove();
            }
        }
    }
};

var validateFormArchivo = {
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
    url: 'comunidad/publicaciones/galeria-archivos/procesar?guardarArchivo=1',
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
            //si guardo bien directamente cierro el dialog
            if($("#dialog").length != 0){
                $("#dialog").hide("slow").remove();
            }
        }
    }
};

//validacion y submit
var validateFormReview = {
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
    url: 'comunidad/publicaciones/guardar-review',
    beforeSerialize:function(){
        if($("#formReview").valid() == true){

            $('#msg_form_review').hide();
            $('#msg_form_review').removeClass("correcto").removeClass("error");
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
            $('#msg_form_review').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_review .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_review .msg').html(data.mensaje);
            }
            if(data.agregarReview != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formReview').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_review').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventsComentarForm(iPublicacionId, sTipoItemForm){
    
    var validateFormComentar = {
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
            comentario:{ignorarDefault:true, required:true}
        },
        messages:{
            comentario:mensajeValidacion("requerido")
        }
    };

    var optionsAjaxFormComentar = {
        dataType: 'jsonp',
        resetForm: true,
        url: 'comunidad/publicaciones/procesar?comentar=1',
        data:{
            iPublicacionId:iPublicacionId,
            objType:sTipoItemForm
        },
        beforeSerialize:function(){
            if($("#formComentar").valid() == true){
                $('#msg_form_comentar').hide();
                $('#msg_form_comentar').removeClass("correcto").removeClass("error");
                $('#msg_form_comentar .msg').html("");
                setWaitingStatus('formComentar', true);
            }else{
                return false;
            }
        },

        success:function(data){
            setWaitingStatus('formComentar', false);

            if(data.success == undefined || data.success == 0){
                if(data.mensaje == undefined){
                    $('#msg_form_comentar .msg').html(lang['error procesar']);
                }else{
                    $('#msg_form_comentar .msg').html(data.mensaje);
                }
                $('#msg_form_comentar').addClass("error").fadeIn('slow');
            }else{
                if(data.mensaje == undefined){
                    $('#msg_form_comentar .msg').html(lang['exito procesar']);
                }else{
                    $('#msg_form_comentar .msg').html(data.mensaje);
                }
                $('#msg_form_comentar').addClass("correcto").fadeIn('slow');

                $("#comentario").val("");
                $("#comentarios").append(data.html);
            }
        }
    };
    
    $("#formComentar").validate(validateFormComentar);
    $("#formComentar").ajaxForm(optionsAjaxFormComentar);
}

var validateFormDenunciarPublicacion = {
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
        razon:{required:true},
        mensaje:{required:true}
    },
    messages:{
        razon: mensajeValidacion("requerido"),
        mensaje: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormDenunciarPublicacion = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/denunciar-publicacion',
    beforeSerialize:function(){
        if($("#formDenunciar").valid() == true){
            setWaitingStatus('formDenunciar', true);
        }else{
            return false;
        }
    },
    success:function(data){
        setWaitingStatus('formDenunciar', false);

        var dialog = $("#dialog");
        if($("#dialog").length){
            dialog.attr("title", "Denunciar Aplicación");
        }else{
            dialog = $('<div id="dialog" title="Denunciar Aplicación"></div>').appendTo('body');
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
};

function bindEventsFormDenunciarPublicacion()
{
    $("#formDenunciar").validate(validateFormDenunciarPublicacion);
    $("#formDenunciar").ajaxForm(optionsAjaxFormDenunciarPublicacion);

    $("textarea.maxlength").maxlength();
}

function bindEventsPublicacionForm(){
    $("#formPublicacion").validate(validateFormPublicacion);
    $("#formPublicacion").ajaxForm(optionsAjaxFormPublicacion);
}

function bindEventsReviewForm(){
    $("#formReview").validate(validateFormReview);
    $("#formReview").ajaxForm(optionsAjaxFormReview);
    selectItemTypeReviewEvent();
}

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

function cambiarEstadoPublicacion(iPublicacionId, valor, tipo){
    $.ajax({
        type: "POST",
        url: "comunidad/publicaciones/procesar",
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

    var dialog = setWaitingStatusDialog(550, 'Modificar '+tipo);

    var url = "";
    switch(tipo){
        case "publicacion": url = "comunidad/publicaciones/form-modificar-publicacion"; break;
        case "review": url = "comunidad/publicaciones/form-modificar-review"; break;
    }

    dialog.load(
        url+"?publicacionId="+iPublicacionId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            if(tipo == "publicacion"){
                bindEventsPublicacionForm();
            }else{
                bindEventsReviewForm();
            }
        }
    );
}

function editarFoto(iFotoId){
    var dialog = setWaitingStatusDialog(550, "Editar Foto");
    dialog.load(
        "comunidad/publicaciones/galeria-fotos/form?iFotoId="+iFotoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsFotoForm();
        }
    );
}

function editarVideo(iEmbedVideoId){
    var dialog = setWaitingStatusDialog(550, "Editar Video");
    dialog.load(
        "comunidad/publicaciones/galeria-videos/form?iEmbedVideoId="+iEmbedVideoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsEditarVideoForm();
        }
    );
}

function editarArchivo(iArchivoId){
    var dialog = setWaitingStatusDialog(550, "Editar Archivo");
    dialog.load(
        "comunidad/publicaciones/galeria-archivos/form?iArchivoId="+iArchivoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            bindEventsArchivoForm();
        }
    );
}

function masMisPublicaciones(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"comunidad/publicaciones/procesar",
        data:{
            masMisPublicaciones:"1",
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisPublicaciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoMisPublicaciones', false);
            $("#listadoMisPublicacionesResult").html(data);
        }
    });
}

function masPublicaciones(){
    
    var filtroTitulo = $('#filtroTitulo').val();
    var filtroApellidoAutor = $('#filtroApellidoAutor').val();
    var filtroFechaDesde = $('#filtroFechaDesde').val();
    var filtroFechaHasta = $('#filtroFechaHasta').val();

    $.ajax({
        type:"POST",
        url:"comunidad/publicaciones/procesar",
        data:{
            masPublicaciones:"1",
            filtroTitulo: filtroTitulo,
            filtroApellidoAutor: filtroApellidoAutor,
            filtroFechaDesde: filtroFechaDesde,
            filtroFechaHasta: filtroFechaHasta
        },
        beforeSend: function(){
            setWaitingStatus('listadoPublicaciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoPublicaciones', false);
            $("#listadoPublicacionesResult").html(data);
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    });
}

function borrarPublicacion(iPublicacionId, tipo){
    if(confirm("Se borrara la publicacion del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"comunidad/publicaciones/procesar",
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
                    dialog.attr("title","Borrar Publicación");
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

function uploaderFotoGaleria(iPublicacionId, sTipoItemForm){
    //galeria de fotos
    if($('#fotoUploadGaleria').length){
        new Ajax_upload('#fotoUploadGaleria', {
            action:'comunidad/publicaciones/galeria-fotos/procesar',
            data:{
                agregarFoto:"1",
                iPublicacionId:iPublicacionId,
                objType: sTipoItemForm
            },
            name:'fotoGaleria',
            onSubmit:function(file , ext){
                $('#msg_form_fotoGaleria').hide();
                $('#msg_form_fotoGaleria').removeClass("correcto").removeClass("error");
                $('#msg_form_fotoGaleria .msg').html("");
                setWaitingStatus('formFotoGaleria', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('formFotoGaleria', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_fotoGaleria .msg').html(lang['error procesar']);
                    $('#msg_form_fotoGaleria').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si se proceso bien aca queda el bloque del html con el nuevo thumbnail

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_fotoGaleria .msg').html(lang['error permiso']);
                    $('#msg_form_fotoGaleria').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_fotoGaleria .msg').html(html);
                    $('#msg_form_fotoGaleria').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_fotoGaleria .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_fotoGaleria').addClass("correcto").fadeIn('slow');
                    
                    $('#Thumbnails').append(html);
                    $("a[rel^='prettyPhoto']").prettyPhoto();
                    if($('#msgNoRecord').length){ $('#msgNoRecord').hide(); }
                }
                return;
            }
        });
    }
}

function uploaderArchivoGaleria(iPublicacionId, sTipoItemForm){
    if($('#archivoUploadGaleria').length){
        new Ajax_upload('#archivoUploadGaleria', {
            action:'comunidad/publicaciones/galeria-archivos/procesar',
            data:{
                agregarArchivo:"1",
                iPublicacionId:iPublicacionId,
                objType: sTipoItemForm
            },
            name:'archivoGaleria',
            onSubmit:function(file , ext){
                $('#msg_form_archivoGaleria').hide();
                $('#msg_form_archivoGaleria').removeClass("correcto").removeClass("error");
                $('#msg_form_archivoGaleria .msg').html("");
                setWaitingStatus('formArchivoGaleria', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('formArchivoGaleria', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_archivoGaleria .msg').html(lang['error procesar']);
                    $('#msg_form_archivoGaleria').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';;');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si se proceso bien aca queda el bloque del html con el nuevo thumbnail

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_archivoGaleria .msg').html(lang['error permiso']);
                    $('#msg_form_archivoGaleria').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_archivoGaleria .msg').html(html);
                    $('#msg_form_archivoGaleria').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_archivoGaleria .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_archivoGaleria').addClass("correcto").fadeIn('slow');
                    
                    $('#Rows').append(html);
                    if($('#msgNoRecord').length){ $('#msgNoRecord').hide(); }
                }
                return;
            }
        });
    }
}

function borrarFoto(iFotoId){
    if(confirm("Se borrara la foto de la publicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"comunidad/publicaciones/galeria-fotos/procesar",
            data:{
                iFotoId:iFotoId,
                eliminarFoto:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#foto_"+iFotoId).remove();
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
            url:"comunidad/publicaciones/galeria-videos/procesar",
            data:{
                iEmbedVideoId:iEmbedVideoId,
                eliminarVideo:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#video_"+iEmbedVideoId).remove();
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
            url:"comunidad/publicaciones/galeria-archivos/procesar",
            data:{
                iArchivoId:iArchivoId,
                eliminarArchivo:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#archivo_"+iArchivoId).remove();
                }
            }
        });
    }
}

function reportarPublicacion(iPublicacionId, sTipoItemForm)
{
    var dialog = setWaitingStatusDialog(500, 'Denunciar Publicación');
    $.ajax({
        type:"post",
        url:"comunidad/denunciar-publicacion",
        data:{
            iPublicacionId:iPublicacionId,
            objType: sTipoItemForm
        },
        success:function(data){
            dialog.html(data);
            bindEventsFormDenunciarPublicacion();
        }
    });
}

$(document).ready(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();
    
    $(".reportarPublicacion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];  
        reportarPublicacion(iPublicacionId, tipo);
        return false;
    });

    //Publicaciones Comunidad
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
    ////////////////

    //Mis publicaciones
    $(".close.ihover").live("click", function(){
        var id = $(this).attr("rel");
        $("#desplegable_" + id).hide();
    });

    $(".editarPublicacion").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iPublicacionId = rel[1];
        editarPublicacion(iPublicacionId, tipo);
        return false;
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masMisPublicaciones();
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
    })
    ////////////////
    
    $("#crearPublicacion").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear Publicacion");
        dialog.load(
            "comunidad/publicaciones/form-nueva-publicacion",
            {},
            function(responseText, textStatus, XMLHttpRequest){                
                bindEventsPublicacionForm();
            }
        );
        return false;
    });

    $("#crearReview").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear Review");
        dialog.load(
            "comunidad/publicaciones/form-crear-review",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsReviewForm();
            }
        );
        return false;
    });

    var iPublicacionId = $("#iItemIdForm").val();
    var sTipoItemForm = $("#sTipoItemForm").val();

    //Galeria de fotos
    if(iPublicacionId != undefined && iPublicacionId != "" &&
       sTipoItemForm != undefined && sTipoItemForm != ""){
        uploaderFotoGaleria(iPublicacionId, sTipoItemForm);
    }

    $(".borrarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        borrarFoto(iFotoId);
    })

    $(".editarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        editarFoto(iFotoId);
    });

    //Galeria de videos
    if($('#formAgregarVideo').length){
        bindEventsAgregarVideoForm();
    } 
    
    $(".editarVideo").live('click', function(){
        var iEmbedVideoId = $(this).attr("rel");
        editarVideo(iEmbedVideoId);
    });

    $(".borrarVideo").live('click', function(){
        var iEmbedVideoId = $(this).attr("rel");
        borrarVideo(iEmbedVideoId);
    })

    //Galeria de archivos
    if(iPublicacionId != undefined && iPublicacionId != "" &&
       sTipoItemForm != undefined && sTipoItemForm != ""){
        uploaderArchivoGaleria(iPublicacionId, sTipoItemForm);
    }

    $(".borrarArchivo").live('click', function(){
        var iArchivoId = $(this).attr("rel");
        borrarArchivo(iArchivoId);
    })

    $(".editarArchivo").live('click', function(){
        var iArchivoId = $(this).attr("rel");
        editarArchivo(iArchivoId);
    });

    //publicacion ampliada.
    if($("#formComentar").length){
        bindEventsComentarForm(iPublicacionId, sTipoItemForm);
    }
});