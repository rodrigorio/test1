function listaProvinciasByPais(idPais, idSelectProvincia, idSelectCiudad, idContenedor){
    //si el valor elegido es '' entonces marco como disabled
    if(idPais == ''){
        $('#'+idSelectProvincia).addClass("disabled");
    }else{
        $('#'+idSelectProvincia).removeClass("disabled");
    }
    $('#'+idSelectCiudad).addClass("disabled");

    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#'+idSelectProvincia).html("");
            //dejo vacio el de ciudad si cambio de pais hasta que elija una provincia
            $('#'+idSelectCiudad).html("");
            $('#'+idSelectCiudad).html(new Option('Elija Ciudad:', '',true));
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectProvincia).append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectProvincia).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectProvincia).html(new Option('Elija Provincia:', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia, idSelectCiudad, idContenedor){
    if(idProvincia == ''){
        $('#'+idSelectCiudad).addClass("disabled");
    }else{
        $('#'+idSelectCiudad).removeClass("disabled");
    }
    $.ajax({
        type: "POST",
        url: "ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#'+idSelectCiudad).html("");
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectCiudad).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
}

//validacion y submit
var validateFormInstitucion = {
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
        nombre:{required:true},
        descripcion:{required:true},
        tipo:{required:true},
        cargo:{required:true},
        direccion:{required:true},
        pais:{required:true},
        provincia:{required:true},
        ciudad:{required:true},
        email:{required:true, email:true},
        telefono:{required:true},
        sitioWeb:{url:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        tipo: mensajeValidacion("requerido"),
        cargo: mensajeValidacion("requerido"),
        direccion: mensajeValidacion("requerido"),
        pais: mensajeValidacion("requerido"),
        provincia: mensajeValidacion("requerido"),
        ciudad: mensajeValidacion("requerido"),
        email:{
            required: mensajeValidacion("requerido"),
            email: mensajeValidacion("email")
        },
        telefono: mensajeValidacion("requerido"),
        sitioWeb:mensajeValidacion("url")
    }
};

var optionsAjaxFormInstitucion = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/instituciones-procesar',
    beforeSerialize:function(){

        if($("#formInstitucion").valid() == true){

            $('#msg_form_institucion').hide();
            $('#msg_form_institucion').removeClass("success").removeClass("error2");
            $('#msg_form_institucion .msg').html("");
            setWaitingStatus('formInstitucion', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInstitucion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_institucion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_institucion .msg').html(data.mensaje);
            }
            $('#msg_form_institucion').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_institucion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_institucion .msg').html(data.mensaje);
            }
            $('#msg_form_institucion').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsInstitucionForm(){
    $("#formInstitucion").validate(validateFormInstitucion);
    $("#formInstitucion").ajaxForm(optionsAjaxFormInstitucion);

    $("#pais").change(function(){
        listaProvinciasByPais($("#pais option:selected").val(), 'provincia', 'ciudad', 'selectsUbicacion');
    });
    $("#provincia").change(function(){
        listaCiudadesByProvincia($("#provincia option:selected").val(), 'ciudad', 'selectsUbicacion');
    });

    $("textarea.maxlength").maxlength();
}

function editarInstitucion(iInstitucionId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Modificar Institución"></div>').appendTo('body');

    dialog.load(
        "admin/instituciones-form",
        {
            iInstitucionId:iInstitucionId
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

            bindEventsInstitucionForm();
        }
    );
}

function ampliarInstitucion(iInstitucionId){

    var dialog = $("#dialog");
    if ($("#dialog").length != 0){
        dialog.hide("slow");
        dialog.remove();
    }
    dialog = $('<div id="dialog" title="Ficha Institucion ID: '+iInstitucionId+'"></div>').appendTo('body');

    dialog.load(
        "admin/instituciones-procesar",
        {
            ampliarInstitucion:"1",
            iInstitucionId:iInstitucionId
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

function borrarInstitucion(iInstitucionId){

    if(confirm("Se borrara la persona del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/instituciones-procesar",
            data:{
                iInstitucionId:iInstitucionId,
                eliminar:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iInstitucionId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Institucion");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Institucion"></div>').appendTo('body');
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

function masModeraciones(){
    $.ajax({
        type:"POST",
        url:"admin/instituciones-procesar",
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

function destituirIntegrante(iInstitucionId){
    if(confirm("Se quitara al integrante como administrador del contenido de la institucion, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/instituciones-procesar",
            data:{
                iInstitucionId:iInstitucionId,
                destituirIntegrante:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#integranteAdministradorCont").hide("slow", function(){
                        $("#integranteAdministradorCont").remove();
                    });
                }
            }
        });
    }
}

function solicitarAdministrarContenido(iInstitucionId){
    $.ajax({
        type:"post",
        dataType:"jsonp",
        url:"admin/instituciones-procesar",
        data:{
            iInstitucionId:iInstitucionId,
            solicitarAdministrarContenido:"1"
        },
        success:function(data){
            if(data.success != undefined && data.success == 1){
                $("#solicitudAdministradorCont").html("Su usario ha sido asignado a la institución");
            }
        }
    });
}

$(document).ready(function(){
    
    $(".borrarInstitucion").click(function(){
        var iInstitucionId = $(this).attr("rel");
        borrarInstitucion(iInstitucionId);
    });

    $(".editarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        editarInstitucion(iInstitucionId);
        return false;
    });

    $(".ampliarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        ampliarInstitucion(iInstitucionId);
        return false;
    });

    $(".destituirIntegrante").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        destituirIntegrante(iInstitucionId);
    });

    $(".solicitarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        solicitarAdministrarContenido(iInstitucionId);
        return false;
    });

    //estos formularios los valido a mano sin plugin porque se repiten por cada fila y hay problemas
    $('.moderarSubmit').live('click', function()
    {
        var mensajeValidacion = "Todos los campos son obligatorios";
        var iInstitucionId = $(this).attr('rel');
        var cartel = $('#msg_form_moderacion_'+iInstitucionId);
        var mensajeCont = $('#msg_form_moderacion_'+iInstitucionId+" .msg");

        cartel.hide();
        cartel.removeClass("correcto").removeClass("error2");
        mensajeCont.html("");

        if(!$('#aprobar_'+iInstitucionId).is(':checked') &&
           !$('#rechazar_'+iInstitucionId).is(':checked')){

            mensajeCont.html(mensajeValidacion);
            cartel.addClass("error").fadeIn('slow');
            return false;
        }


        if($('#mensaje_'+iInstitucionId).val() == ""){

            mensajeCont.html(mensajeValidacion);
            cartel.addClass("error").fadeIn('slow');
            return false;
        }

        var estado;
        if($('#aprobar_'+iInstitucionId).is(':checked')){
            estado = $('#aprobar_'+iInstitucionId).val();
        }else{
            estado = $('#rechazar_'+iInstitucionId).val();
        }

        $.ajax({
            type: "POST",
            url: "admin/instituciones-procesar",
            data:{
                iModeracionId: function(){return $('#moderacionId_'+iInstitucionId).val(); },
                moderarInstitucion: "1",
                estado: estado,
                mensaje: function(){return $('#mensaje_'+iInstitucionId).val(); }
            },
            beforeSend: function(){
                setWaitingStatus('listadoModeraciones', true);
            },
            success: function(data){
                setWaitingStatus('listadoModeraciones', false);

                if(data.success != undefined && data.success == 1){
                    $("."+iInstitucionId).hide("slow", function(){
                        $("."+iInstitucionId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title", "Moderar Publicación ID: "+iInstitucionId);
                }else{
                    dialog = $('<div id="dialog" title="Moderar Publicación ID: '+iInstitucionId+'"></div>').appendTo('body');
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