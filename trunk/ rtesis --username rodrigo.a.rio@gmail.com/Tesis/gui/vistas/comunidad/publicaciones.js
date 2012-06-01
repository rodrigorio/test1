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

//validacion y submit
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
        success:function(data){
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

    var url = "";
    switch(tipo){
        case "publicacion": url = "comunidad/publicaciones/form-modificar-publicacion"; break;
        case "review": url = "comunidad/publicaciones/form-modificar-review"; break;
    }

    dialog.load(
        url+"?publicacionId="+iPublicacionId,
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

$(document).ready(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

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
        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $('<div id="dialog" title="Crear Publicacion"></div>').appendTo('body');

        dialog.load(
            "comunidad/publicaciones/form-nueva-publicacion",
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
                
                bindEventsPublicacionForm();
            }
        );
        return false;
    });

    $("#crearReview").click(function(){
        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $('<div id="dialog" title="Crear Review"></div>').appendTo('body');

        dialog.load(
            "comunidad/publicaciones/form-crear-review",
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

                bindEventsReviewForm();
            }
        );
        return false;
    });

    //Galeria de fotos
    var iPublicacionId = $("#iItemIdForm").val();
    var sTipoItemForm = $("#sTipoItemForm").val();
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
});