var validateFormCrearSeguimiento = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("name"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        tipoSeguimiento:{required:true},
        personaId:{required:true},
        practica:{required:true}
    },
    messages:{
        tipoSeguimiento: mensajeValidacion("requerido"),
        personaId: mensajeValidacion("requerido"),
        practica: mensajeValidacion("requerido")
    }
}

var optionsAjaxFormCrearSeguimiento = {
    dataType: 'jsonp',
    resetForm: false,
    url: "seguimientos/procesar-seguimiento",
    beforeSerialize: function($form, options){

        if($("#formCrearSeguimiento").valid() == true){
            $('#msg_form_crearSeguimiento').hide();
            $('#msg_form_crearSeguimiento').removeClass("correcto").removeClass("error");
            $('#msg_form_crearSeguimiento .msg').html("");

            setWaitingStatus('formCrearSeguimiento', true);
        }else{
            return false;
        }
    },

    success:function(data){

        setWaitingStatus('formCrearSeguimiento', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_crearSeguimiento .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_crearSeguimiento .msg').html(data.mensaje);
            }
            $('#msg_form_crearSeguimiento').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_crearSeguimiento .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_crearSeguimiento .msg').html(data.mensaje);
            }
            $('#msg_form_crearSeguimiento').addClass("correcto").fadeIn('slow');

            $("#persona").removeClass("selected");
            $("#persona").removeAttr("readonly");
            ocultarElemento($('#persona_clean'));
            $("#personaId").val("");
            $("#persona").val("");
            $("#frecuencias").val("");
            $("#diaHorario").val("");
            $("#diaHorario").val("");
            $("#practica").val("");            
        }      
    }
};

function masSeguimientos(){

    var filtroEstado = $('#filtroEstado option:selected').val();
    var filtroTipoSeguimiento = $('#filtroTipoSeguimiento option:selected').val();
    var filtroApellidoPersona = $('#filtroApellidoPersona').val();
    var filtroFechaDesde = $('#filtroFechaDesde').val();
    var filtroFechaHasta = $('#filtroFechaHasta').val();
    var filtroDni = $('#filtroDni').val();

    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();
    
    $.ajax({
        url: "seguimientos/buscar-seguimientos",
        type: "POST",
        data:{
            filtroEstado:filtroEstado,
            filtroApellidoPersona:filtroApellidoPersona,
            filtroFechaDesde:filtroFechaDesde,
            filtroFechaHasta:filtroFechaHasta,
            filtroDni:filtroDni,
            filtroTipoSeguimiento:filtroTipoSeguimiento,
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoSeguimientos', true);
        },
        success:function(data){
            setWaitingStatus('listadoSeguimientos', false);
            $("#listadoSeguimientosResult").html(data);
        }
    });
}

function cambiarEstadoSeguimiento(iSeguimientoId, valor){
    $.ajax({
        type: "POST",
        url: "seguimientos/cambiar-estado-seguimientos",
        data:{
            iSeguimientoId:iSeguimientoId,
            estadoSeguimiento:valor
        },
        beforeSend: function(){
            setWaitingStatus('listadoSeguimientos', true);
        },
        success:function(data){
            if(valor == "Detenido"){
               $("."+iSeguimientoId).addClass("disabled");
            }else{
               $("."+iSeguimientoId).removeClass("disabled");
            }
            setWaitingStatus('listadoSeguimientos', false);
        }
    });
}

function borrarSeguimiento(iSeguimientoId)
{
    if(confirm("Se eliminara el seguimiento con todo el material adjunto, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"seguimientos/seguimientos-eliminar",
            data:{iSeguimientoId:iSeguimientoId},
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+iSeguimientoId).remove();
                }

                var dialog = $("#dialog");
                if ($("#dialog").length != 0){
                    dialog.hide("slow");
                    dialog.remove();
                }
                dialog = $('<div id="dialog" title="Eliminar Seguimiento"></div>').appendTo('body');
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

function bindEventsFormAgregarVideo()
{
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
    
    $("#formAgregarVideo").validate(validateFormAgregarVideo);
    $("#formAgregarVideo").ajaxForm(optionsAjaxFormAgregarVideo);    
}

function bindEventsFormAgregarArchivo(iSeguimientoId)
{
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

function bindEventsFormAgregarFoto(iSeguimientoId)
{
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

function bindEventsFotoForm(){
    $("#formFoto").validate(validateFormFoto);
    $("#formFoto").ajaxForm(optionsAjaxFormFoto);
}

function bindEventsArchivoForm(){
    $("#formArchivo").validate(validateFormArchivo);
    $("#formArchivo").ajaxForm(optionsAjaxFormArchivo);
}

function bindEventsEditarVideoForm(){
    $("#formEditarVideo").validate(validateFormEditarVideo);
    $("#formEditarVideo").ajaxForm(optionsAjaxFormEditarVideo);
}

function editarFoto(iFotoId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Editar Foto"></div>').appendTo('body');

    dialog.load(
        "comunidad/publicaciones/galeria-fotos/form?iFotoId="+iFotoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:550,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:true
            });

            bindEventsFotoForm();
        }
    );
}

function editarVideo(iEmbedVideoId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Editar Video"></div>').appendTo('body');

    dialog.load(
        "comunidad/publicaciones/galeria-videos/form?iEmbedVideoId="+iEmbedVideoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:550,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:true
            });

            bindEventsEditarVideoForm();
        }
    );
}

function editarArchivo(iArchivoId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Editar Archivo"></div>').appendTo('body');

    dialog.load(
        "comunidad/publicaciones/galeria-archivos/form?iArchivoId="+iArchivoId,
        {},
        function(responseText, textStatus, XMLHttpRequest){
            dialog.dialog({
                position:['center', '20'],
                width:550,
                resizable:false,
                draggable:false,
                modal:false,
                closeOnEscape:true
            });

            bindEventsArchivoForm();
        }
    );
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

$(document).ready(function(){
   
    //Listado Seguimientos
    $("#filtroFechaDesde").datepicker();
    $("#filtroFechaHasta").datepicker();

    $("#BuscarSeguimientos").live('click', function(){
        masSeguimientos();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarSeguimientos').each(function(){
          this.reset();
        });
        return false;
    });

    $(".close.ihover").live("click", function(){
        var id = $(this).attr("rel");
        $("#desplegable_" + id).hide();
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masSeguimientos();
    });

    $(".cambiarEstadoSeguimiento").live("change", function(){
        var iSeguimientoId = $(this).attr("rel");
        cambiarEstadoSeguimiento(iSeguimientoId, $("#estadoSeguimiento_"+iSeguimientoId+" option:selected").val());
    });

    $(".borrarSeguimiento").live('click', function(){
        var iSeguimientoId = $(this).attr("rel");
        borrarSeguimiento(iSeguimientoId);
    })
    ////////////////Listado Seguimientos

    if($("#formCrearSeguimiento").length){

        $("#formCrearSeguimiento").validate(validateFormCrearSeguimiento);
        $("#formCrearSeguimiento").ajaxForm(optionsAjaxFormCrearSeguimiento);

        $('#persona').blur(function(){
            if($("#personaId").val() == ""){
                $("#persona").val("");
            }
            if($("#persona").val() == ""){
                $("#personaId").val("");
            }
        });
        
        //para borrar la persona seleccionada con el autocomplete
        $('#persona_clean').click(function(){
            $("#persona").removeClass("selected");
            $("#persona").removeAttr("readonly");
            $("#persona").val("");
            $("#personaId").val("");
            ocultarElemento($(this));
        });

        $("#persona").autocomplete({
            source:function(request, response){
                $.ajax({
                    url: "seguimientos/buscar-discapacitados",
                    dataType:"jsonp",
                    data:{
                        limit:12,
                        str:request.term
                    },
                    beforeSend: function(){
                       revelarElemento($("#persona_loading"));
                    },
                    success: function(data){                        
                        ocultarElemento($("#persona_loading"));
                        response($.map(data.discapacitados, function(discapacitados){
                            return{
                                //lo que aparece en el input
                                value:discapacitados.sNombre,
                                //lo que aparece en la lista generada para elegir
                                label:discapacitados.sNombre,
                                //valor extra que se devuelve para completar el hidden
                                id:discapacitados.iId,
                                sRutaFoto:discapacitados.sRutaFoto,
                                dni:discapacitados.iNumeroDocumento
                            }
                        }));
                    }
                });
            },
            minLength:3,
            select: function(event, ui){
                if(ui.item){
                    $("#personaId").val(ui.item.id);
                }else{
                    $("#personaId").val("");
                }
            },
            close: function(){
                if($("#personaId").val() != ""){
                    $(this).addClass("selected");
                    $(this).attr('readonly', 'readonly');
                    revelarElemento($('#persona_clean'));
                }
            }
        })
        .data("autocomplete")._renderItem = function(ul, item){
            return $("<li></li>")
            .data("item.autocomplete", item)
            .append("<a class='bobo1'><div class='fl_le'><img src='"+ item.sRutaFoto +"' alt='foto' ></div>" +
                    "<div class='fl_le pa3'>" + item.label + "</div>" +
                    "<div class='cl_bo'>Numero de documento: " + item.dni + "</div></a>" )
            .appendTo(ul);
        };
    }

    $(".agregarPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");
        $.getScript(pathUrlBase+"utilidades/jquery/ajaxupload.3.6.js");

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $('<div id="dialog" title="Agregar Persona"></div>').appendTo('body');

        dialog.load(
            "seguimientos/agregar-persona?popUp=1",
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
                bindEventsPersonaForm(); //la funcion esta en personas.js
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });

    $(".verPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='"+$(this).html()+"'></div>").appendTo('body');

        dialog.load(
            "seguimientos/ver-persona?personaId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
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

    $("a[rel^='prettyPhoto']").prettyPhoto();

    //formulario adjuntar foto, video, archivo
    $(".agregarFotoSeguimiento").live('click',function(){
        
        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='Agregar Foto'></div>").appendTo('body');
               
        dialog.load(
            "seguimientos/form-adjuntar-foto?iSeguimientoId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){                
                dialog.dialog({
                    position:['center', '20'],
                    width:450,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });
                
                bindEventsFormAgregarFoto();                
            }
        );

        return false;
    });

    $(".agregarVideoSeguimiento").live('click',function(){

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='Agregar Video'></div>").appendTo('body');

        dialog.load(
            "seguimientos/form-adjuntar-video?iSeguimientoId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:450,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });

                bindEventsFormAgregarVideo();
            }
        );
        return false;
    });

    $(".agregarArchivoSeguimiento").live('click',function(){

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='Agregar Archivo'></div>").appendTo('body');

        dialog.load(
            "seguimientos/form-adjuntar-archivo?iSeguimientoId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:450,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });

                bindEventsFormAgregarArchivo();
            }
        );
        return false;
    });
    
});