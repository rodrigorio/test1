function resetSelect(select, defaultOpt){
    if(select.length){
        select.addClass("disabled");
        select.html("");
        select.append(new Option(defaultOpt, '',true));
    }
}

function listaProvinciasByPais(idPais, idSelectProvincia, idSelectCiudad, idContenedor){
    resetSelect($('#'+idSelectCiudad), 'Elija Ciudad:');

    if(idPais == ''){
        resetSelect($('#'+idSelectProvincia), 'Elija Provincia:');
        return;
    }else{
        $('#'+idSelectProvincia).removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(lista){
            $('#'+idSelectProvincia).html("");
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectProvincia).append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectProvincia).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectProvincia).html(new Option('No hay provincias cargadas', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia, idSelectCiudad, idContenedor){
    if(idProvincia == ''){
        resetSelect($('#'+idSelectCiudad), 'Elija Ciudad:');
        return;
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
        success: function(lista){
            $('#'+idSelectCiudad).html("");
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectCiudad).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectCiudad).append(new Option('No hay ciudades cargadas', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
}

var validateFormInstitucion = {
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
    url:"comunidad/guardar-institucion",
    
    beforeSerialize:function($form, options){
        if($("#formInstitucion").valid() == true){
            $('#msg_form_institucion').hide();
            $('#msg_form_institucion').removeClass("correcto").removeClass("error");
            $('#msg_form_institucion .msg').html("");

            verificarValorDefecto("descripcion");
            verificarValorDefecto("sedes");
            verificarValorDefecto("autoridades");
            verificarValorDefecto("actividadesMes");
            
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
            $('#msg_form_institucion').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_institucion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_institucion .msg').html(data.mensaje);
            }
            $('#msg_form_institucion').addClass("correcto").fadeIn('slow');
        }

        if(data.agregarInstitucion != undefined){
            //limpio el form
            $('#formInstitucion').each(function(){
                this.reset();
            });
        }
    }
};

var validateFormSolicitarInstitucion = {
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
        mensaje:{required:true}
    },
    messages:{
        mensaje: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormSolicitarInstitucion = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/instituciones/procesar',
    data:{
        solicitarInstitucionProcesar:"1"
    },
    beforeSerialize:function(){
        if($("#formSolicitarInstitucion").valid() == true){
            $('#msg_form_solicitarInstitucion').hide();
            $('#msg_form_solicitarInstitucion').removeClass("correcto").removeClass("error");
            $('#msg_form_solicitarInstitucion .msg').html("");
            setWaitingStatus('formSolicitarInstitucion', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formSolicitarInstitucion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_solicitarInstitucion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_solicitarInstitucion .msg').html(data.mensaje);
            }
            $('#msg_form_solicitarInstitucion').addClass("error").fadeIn('slow');
        }else{
            $('#solicitarInstitucionCont').html("Solicitud de administración enviada.");

            $("#dialog").hide("slow", function(){
                $("#dialog").remove();
            });
        }
    }
};

var validateFormDenunciarInstitucion = {
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

var optionsAjaxFormDenunciarInstitucion = {
    dataType: 'jsonp',
    resetForm: true,
    url: 'comunidad/denunciar-institucion',
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
            dialog.attr("title", "Denunciar Institución");
        }else{
            dialog = $('<div id="dialog" title="Denunciar Institución"></div>').appendTo('body');
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

function bindEventsFormSolicitarInstitucion()
{
    $("#formSolicitarInstitucion").validate(validateFormSolicitarInstitucion);
    $("#formSolicitarInstitucion").ajaxForm(optionsAjaxFormSolicitarInstitucion);
    
    $("textarea.maxlength").maxlength();
}

function bindEventsFormDenunciarInstitucion()
{
    $("#formDenunciar").validate(validateFormDenunciarInstitucion);
    $("#formDenunciar").ajaxForm(optionsAjaxFormDenunciarInstitucion);
    
    $("textarea.maxlength").maxlength();
}

function masInstituciones(){

    var filtroNombre = $('#filtroNombre').val();
    var filtroTipoInstitucion = $('#filtroTipoInstitucion option:selected').val();
    var filtroPais = $('#filtroPais option:selected').val();
    var filtroProvincia = $('#filtroProvincia option:selected').val();
    var filtroCiudad = $('#filtroCiudad option:selected').val();

    $.ajax({
        type:"POST",
        url:"comunidad/masInstituciones",
        data:{
            filtroNombre: filtroNombre,
            filtroTipoInstitucion: filtroTipoInstitucion,
            filtroPais: filtroPais,
            filtroProvincia: filtroProvincia,
            filtroCiudad: filtroCiudad
        },
        beforeSend: function(){
            setWaitingStatus('listadoInstituciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoInstituciones', false);
            $("#listadoInstitucionesResult").html(data);
        }
    });
}

function masMisInstituciones(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"comunidad/instituciones/mas-mis-instituciones",
        data:{
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisInstituciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoMisInstituciones', false);
            $("#listadoMisInstitucionesResult").html(data);
        }
    });
}

function borrarInstitucion(iInstitucionId){
    if(confirm("Se borrara la institucion del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"comunidad/instituciones/procesar",
            data:{
                iInstitucionId:iInstitucionId,
                borrarInstitucion:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iInstitucionId).hide("slow", function(){
                        $("."+iInstitucionId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title","Borrar Institución");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Institución"></div>').appendTo('body');
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

function solicitarInstitucion(iInstitucionId)
{
    var dialog = setWaitingStatusDialog(500, 'Solicitar Institución');
    $.ajax({
        type:"post",
        url:"comunidad/instituciones/procesar",
        data:{
            iInstitucionId:iInstitucionId,
            solicitarInstitucionForm:"1"
        },
        success:function(data){
            dialog.html(data);
            bindEventsFormSolicitarInstitucion();           
        }
    });
}

function reportarInstitucion(iInstitucionId)
{
    var dialog = setWaitingStatusDialog(500, 'Denunciar Institución');
    $.ajax({
        type:"post",
        url:"comunidad/denunciar-institucion",
        data:{
            iInstitucionId:iInstitucionId
        },
        success:function(data){
            dialog.html(data);
            bindEventsFormDenunciarInstitucion();
        }
    });
}

$(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

    if($("#formInstitucion").length){
        $("#formInstitucion").validate(validateFormInstitucion);
        $("#formInstitucion").ajaxForm(optionsAjaxFormInstitucion);

        $("#pais").change(function(){
            listaProvinciasByPais($("#pais option:selected").val(), 'provincia', 'ciudad', 'selectsUbicacion');
        });
        $("#provincia").change(function(){
            listaCiudadesByProvincia($("#provincia option:selected").val(), 'ciudad', 'selectsUbicacion');
        });
    }

    $("#filtroPais").change(function(){
        listaProvinciasByPais($("#filtroPais option:selected").val(), 'filtroProvincia', 'filtroCiudad', 'formFiltrarInstituciones');
    });
    $("#filtroProvincia").change(function(){
        listaCiudadesByProvincia($("#filtroProvincia option:selected").val(), 'filtroCiudad', 'formFiltrarInstituciones');
    });

    $(".solicitarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        solicitarInstitucion(iInstitucionId);
        return false;
    });

    $(".reportarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        reportarInstitucion(iInstitucionId);
        return false;
    });

    //listado instituciones comunidad
    $("#BuscarInstituciones").live('click', function(){
        masInstituciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarInstituciones').each(function(){
          this.reset();
        });
        return false;
    });
    ///////////////////////////////

    //Listado Mis Instituciones
    $(".borrarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        borrarInstitucion(iInstitucionId);
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masMisInstituciones();
    });    
});

$(window).load(function(){
    if($("#mapaInstitucion").length){
        mapaSimple("mapaInstitucion");
    }

    if($("#mapaSeleccionarCoordenadas").length){
        //automaticamente rellena los inputs con name 'latitud' y 'longitud'
        mapaSeleccionCoordenadas("mapaSeleccionarCoordenadas");
    }
});