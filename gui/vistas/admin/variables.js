var validateFormVariableTexto = {
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
        descripcion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormVariableTexto = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/guardar-variable',
    beforeSerialize:function(){

        if($("#formVariableTexto").valid() == true){

            $('#msg_form_variable').hide();
            $('#msg_form_variable').removeClass("success").removeClass("error2");
            $('#msg_form_variable .msg').html("");
            setWaitingStatus('formVariableTexto', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formVariableTexto', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            $('#msg_form_variable').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            if(data.agregarVariable != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formVariableTexto').each(function(){
                  this.reset();
                });
            }
            
            //refresco el listado actual
            masVariables();
            $('#msg_form_variable').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormVariableNumerica = {
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
        descripcion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormVariableNumerica = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/guardar-variable',
    beforeSerialize:function(){

        if($("#formVariableNumerica").valid() == true){

            $('#msg_form_variable').hide();
            $('#msg_form_variable').removeClass("success").removeClass("error2");
            $('#msg_form_variable .msg').html("");
            setWaitingStatus('formVariableNumerica', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formVariableNumerica', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            $('#msg_form_variable').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            if(data.agregarVariable != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formVariableNumerica').each(function(){
                  this.reset();
                });
            }

            //refresco el listado actual
            masVariables();
            $('#msg_form_variable').addClass("success").fadeIn('slow');
        }
    }
};

/**
 * La validacion de las modalidades se hacen del lado del server y se devuelve por ajax.
 */
var validateFormVariableCualitativa = {
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
        descripcion:{required:true}
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormVariableCualitativa = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/guardar-variable',
    beforeSerialize:function(){

        if($("#formVariableCualitativa").valid() == true){

            $('#msg_form_variable').hide();
            $('#msg_form_variable').removeClass("success").removeClass("error2");
            $('#msg_form_variable .msg').html("");
            setWaitingStatus('formVariableCualitativa', true);

        }else{
            return false;
        }
    },

    success:function(data){        
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            $('#msg_form_variable').addClass("error2");
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_variable .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_variable .msg').html(data.mensaje);
            }
            if(data.agregarVariable != undefined){
                //el submit fue para agregar una nueva publicacion. limpio el form
                $('#formVariableCualitativa').each(function(){
                  this.reset();                  
                });
                
                $('.modalidad').remove();
            }

            //si estoy editando una variable cualitativa refresco el listado de modalidades. necesario por ids
            if(data.modificarVariable != undefined){
                $("#grillaModalidadesWrapper").html(data.grillaModalidades);                    
            }

            //refresco el listado actual
            masVariables();
            $('#msg_form_variable').addClass("success");
        }
        
        setWaitingStatus('formVariableCualitativa', false);
        $('#msg_form_variable').fadeIn('slow');
    }
};

function bindEventsVariableTextoForm(){
    $("#formVariableTexto").validate(validateFormVariableTexto);
    $("#formVariableTexto").ajaxForm(optionsAjaxFormVariableTexto);
}

function bindEventsVariableNumericaForm(){
    $("#formVariableNumerica").validate(validateFormVariableNumerica);
    $("#formVariableNumerica").ajaxForm(optionsAjaxFormVariableNumerica);
}

function bindEventsVariableCualitativaForm(){
    $("#formVariableCualitativa").validate(validateFormVariableCualitativa);
    $("#formVariableCualitativa").ajaxForm(optionsAjaxFormVariableCualitativa);
}

function masVariables(){
    var unidadId = $('#unidadId').val();

    $.ajax({
        type:"POST",
        url:"admin/variables-procesar",
        data:{
            masVariables:"1",
            id: unidadId
        },
        beforeSend: function(){
            setWaitingStatus('listadoVariables', true);
        },
        success:function(data){
            $("#listadoVariablesResult").html(data);
            //bindeo js del listado
            $('.datatable').dataTable();
            setWaitingStatus('listadoVariables', false);
        }
    });
}

function eliminarVariable(iVariableId){
    if(confirm("Se borrara la variable de la unidad de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/borrar-variable",
            data:{
                iVariableId:iVariableId
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha
                    $("."+iVariableId).hide("slow", function(){
                        $("."+iVariableId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title","Borrar Variable");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Variable"></div>').appendTo('body');
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

    $("#crearVariableTexto").click(function(){
        var unidadId = $('#unidadId').val();
        var dialog = setWaitingStatusDialog(650, "Crear variable de Texto");
        dialog.load(
            "admin/form-crear-variable",
            {"formTexto":"1", "unidadId":unidadId},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsVariableTextoForm();
            }
        );
        return false;
    });

    $("#crearVariableNumerica").click(function(){
        var unidadId = $('#unidadId').val();
        var dialog = setWaitingStatusDialog(650, "Crear variable Numérica");
        dialog.load(
            "admin/form-crear-variable",
            {"formNumerica":"1", "unidadId":unidadId},
            function(responseText, textStatus, XMLHttpRequest){                
                bindEventsVariableNumericaForm();
            }
        );
        return false;
    });

    $("#crearVariableCualitativa").click(function(){
        var unidadId = $('#unidadId').val();
        var dialog = setWaitingStatusDialog(550, "Crear variable Cualitativa");
        dialog.load(
            "admin/form-crear-variable",
            {"formCualitativa":"1", "unidadId":unidadId},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsVariableCualitativaForm();
            }
        );
        return false;
    });

    $(".editarVariable").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iVariableId = rel[1];
        var unidadId = $('#unidadId').val();

        var titulo = "";
        switch(tipo){
            case "VariableTexto": titulo = "Editar variable de Texto"; break;
            case "VariableCualitativa": titulo = "Editar variable Cualitativa"; break;
            case "VariableNumerica": titulo = "Editar variable Numérica"; break;
        }
        
        var dialog = setWaitingStatusDialog(650, titulo);

        //desde el page controller me doy cuenta como muestro el formulario por la clase del objeto variable
        //aca la unica condicion que me fijo es para bindear el javascript segun el tipo de formulario, la url es la misma.
        dialog.load(
            "admin/form-editar-variable",
            {"iVariableId":iVariableId, "unidadId":unidadId},
            function(responseText, textStatus, XMLHttpRequest){
                switch(tipo){
                case "VariableTexto":
                  bindEventsVariableTextoForm();
                  break;
                case "VariableNumerica":
                  bindEventsVariableNumericaForm();
                  break;
                case "VariableCualitativa":
                  bindEventsVariableCualitativaForm();
                  break;
                }
            }
        );
    });

    $(".borrarVariable").live('click', function(){
        var iVariableId = $(this).attr("rel");
        eliminarVariable(iVariableId);
    });

    $("#agregarModalidad").live('click', function(){
        $.ajax({
            type:"POST",
            url:"admin/variables-procesar",
            data:{
                agregarModalidad:"1"
            },
            beforeSend: function(){
                setWaitingStatus('listadoModalidades', true);
            },
            success:function(data){
                if($("#noRecordsModalidades").length){
                    $("#noRecordsModalidades").hide("slow", function(){
                        $("#noRecordsModalidades").remove();
                    });
                }
                setWaitingStatus('listadoModalidades', false);                
                $('#grillaModalidades').append(data);                
            }
        });
    });

    $(".borrarModalidad").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var modalidadHtmlId = rel[0];
        var iModalidadId = rel[1];

        //solo si la modalidad estaba guardada en db
        if(iModalidadId != ""){
            if(confirm("Se borrara la modalidad seleccionada en la variable, desea continuar?")){
                $.ajax({
                    type:"post",
                    dataType:"jsonp",
                    url:"admin/borrar-modalidad-variable",
                    data:{
                        iModalidadId:iModalidadId                        
                    },
                    success:function(data){
                        if(data.success != undefined && data.success == 1){
                            $("."+modalidadHtmlId).hide("slow", function(){
                                $("."+modalidadHtmlId).remove();
                            });
                        }
                    }
                });
            }
        //repito el codigo en el else por el asincronismo del ajax si es que necesitas ejecutarse.
        }else{
            $("."+modalidadHtmlId).hide("slow", function(){
                $("."+modalidadHtmlId).remove();
            });
        }
    });
});