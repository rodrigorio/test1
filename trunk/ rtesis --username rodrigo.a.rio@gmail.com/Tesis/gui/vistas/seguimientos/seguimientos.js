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
            $(this).children("ul").fadeIn();
        }
    });
    $("#pageRightInnerContNav li").mouseleave(function(){
        if(!$(this).hasClass("selected")){
            $(this).children("ul").fadeOut();
        }
    });
});