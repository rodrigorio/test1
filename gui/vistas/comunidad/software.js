//validacion y submit
var validateFormSoftware = {
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
    url: 'comunidad/descargas/guardar-aplicacion',
    beforeSerialize:function(){

        if($("#formSoftware").valid() == true){

            $('#msg_form_software').hide();
            $('#msg_form_software').removeClass("correcto").removeClass("error");
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
            $('#msg_form_software').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){            
                $('#msg_form_software .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_software .msg').html(data.mensaje);
            }
            if(data.agregarSoftware != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formSoftware').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_software').addClass("correcto").fadeIn('slow');
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
    url: 'comunidad/descargas/galeria-fotos/procesar?guardarFoto=1',
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
    url: 'comunidad/descargas/galeria-archivos/procesar?guardarArchivo=1',
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

function bindEventsComentarForm(iSoftwareId){
    
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
        url: 'comunidad/descargas/procesar?comentar=1',
        data:{
            iSoftwareId:iSoftwareId
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

function bindEventsSoftwareForm(){
    $("#formSoftware").validate(validateFormSoftware);
    $("#formSoftware").ajaxForm(optionsAjaxFormSoftware);
}

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
        url: "comunidad/descargas/procesar",
        data:{
            iSoftwareId:iSoftwareId,
            estadoPublicacion:valor,
            cambiarEstado:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisAplicaciones', true);
        },
        success: function(data){
            setWaitingStatus('listadoMisAplicaciones', false);
        }
    });
}

function editarSoftware(iSoftwareId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Modificar Aplicación"></div>').appendTo('body');

    dialog.load(
        "comunidad/descargas/form-modificar-aplicacion?iSoftwareId="+iSoftwareId,
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

            bindEventsSoftwareForm();
        }
    );
}

function editarFoto(iFotoId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Editar Foto"></div>').appendTo('body');

    dialog.load(
        "comunidad/descargas/galeria-fotos/form?iFotoId="+iFotoId,
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

function editarArchivo(iArchivoId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Editar Archivo"></div>').appendTo('body');

    dialog.load(
        "comunidad/descargas/galeria-archivos/form?iArchivoId="+iArchivoId,
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

function masMisAplicaciones(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"comunidad/descargas/procesar",
        data:{
            masMisPublicaciones:"1",
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisAplicaciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoMisAplicaciones', false);
            $("#listadoMisAplicacionesResult").html(data);
        }
    });
}

function masAplicaciones(){
    
    var filtroTitulo = $('#filtroTitulo').val();
    var filtroCategoria = $('#filtroCategoria').val();

    $.ajax({
        type:"POST",
        url:"comunidad/descargas/procesar",
        data:{
            masAplicaciones:"1",
            filtroTitulo: filtroTitulo,
            filtroCategoria: filtroCategoria
        },
        beforeSend: function(){
            setWaitingStatus('listadoSoftware', true);
        },
        success:function(data){
            setWaitingStatus('listadoSoftware', false);
            $("#listadoSoftwareResult").html(data);
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    });
}

function borrarSoftware(iSoftwareId){
    if(confirm("Se borrara la aplicación del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"comunidad/descargas/procesar",
            data:{
                iSoftwareId:iSoftwareId,
                borrarSoftware:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha
                    $("."+iSoftwareId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title", "Borrar Aplicación");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Aplicación"></div>').appendTo('body');
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

function uploaderFotoGaleria(iSoftwareId){
    //galeria de fotos
    if($('#fotoUploadGaleria').length){
        new Ajax_upload('#fotoUploadGaleria', {
            action:'comunidad/descargas/galeria-fotos/procesar',
            data:{
                agregarFoto:"1",
                iSoftwareId:iSoftwareId
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

                var dataInfo = response.split(';;');
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

function uploaderArchivoGaleria(iSoftwareId){
    if($('#archivoUploadGaleria').length){
        new Ajax_upload('#archivoUploadGaleria', {
            action:'comunidad/descargas/galeria-archivos/procesar',
            data:{
                agregarArchivo:"1",
                iSoftwareId:iSoftwareId
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
    if(confirm("Se borrara la foto de la aplicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"comunidad/descargas/galeria-fotos/procesar",
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

function borrarArchivo(iArchivoId){
    if(confirm("Se borrara el archivo de la aplicación, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"comunidad/descargas/galeria-archivos/procesar",
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

    $("a[rel^='prettyPhoto']").prettyPhoto();

    //Software Comunidad
    $("#BuscarSoftware").live('click', function(){
        masAplicaciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarSoftware').each(function(){
          this.reset();
        });
        return false;
    });
    ////////////////

    //Mis aplicaciones
    $(".close.ihover").live("click", function(){
        var id = $(this).attr("rel");
        $("#desplegable_" + id).hide();
    });

    $(".editarSoftware").live('click', function(){
        var iSoftwareId = $(this).attr("rel");
        editarSoftware(iSoftwareId);
        return false;
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masMisAplicaciones();
    });

    $(".cambiarEstadoSoftware").live("change", function(){
        var iSoftwareId = $(this).attr("rel");
        cambiarEstadoSoftware(iSoftwareId, $("#estadoSoftware_"+iSoftwareId+" option:selected").val());
    });

    $(".borrarSoftware").live('click', function(){
        var iSoftwareId = $(this).attr("rel");
        borrarSoftware(iSoftwareId);
    })
    ////////////////
    
    $("#crearSoftware").click(function(){
        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $('<div id="dialog" title="Crear Aplicación"></div>').appendTo('body');

        dialog.load(
            "comunidad/descargas/form-nueva-aplicacion",
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
                
                bindEventsSoftwareForm();
            }
        );
        return false;
    });

    var iSoftwareId = $("#iItemIdForm").val();

    //Galeria de fotos
    if(iSoftwareId != undefined && iSoftwareId != ""){
        uploaderFotoGaleria(iSoftwareId);
    }

    $(".borrarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        borrarFoto(iFotoId);
    })

    $(".editarFoto").live('click', function(){
        var iFotoId = $(this).attr("rel");
        editarFoto(iFotoId);
    });

    //Galeria de archivos
    if(iSoftwareId != undefined && iSoftwareId != ""){
        uploaderArchivoGaleria(iSoftwareId);
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
        bindEventsComentarForm(iSoftwareId);
    }
});