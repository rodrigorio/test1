function masVariables(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"seguimientos/variables-procesar",
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

$(document).ready(function(){

    $(".close.ihover").live("click", function(){
        var id = $(this).attr("rel");
        $("#desplegable_" + id).hide();
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masVariables();
    });

    $("#crearVariableTexto").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear variable de Texto");
        dialog.load(
            "seguimientos/form-crear-variable",
            {"formTexto":"1"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsVariableForm();
            }
        );
        return false;
    });

    $("#crearVariableNumerica").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear variable Numérica");
        dialog.load(
            "seguimientos/form-crear-variable",
            {"formNumerica":"1"},
            function(responseText, textStatus, XMLHttpRequest){                
                bindEventsVariableForm();
            }
        );
        return false;
    });

    $("#crearVariableCualitativa").click(function(){
        var dialog = setWaitingStatusDialog(550, "Crear variable Cualitativa");
        dialog.load(
            "seguimientos/form-crear-variable",
            {"formCualitativa":"1"},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsVariableForm();
                //se agrega el javascript de la edicion de modalidades 
                bindEventsVariableCualitativaForm();
            }
        );
        return false;
    });

    $(".editarVariable").live('click', function(){
        var rel = $(this).attr("rel").split('_');
        var tipo = rel[0];
        var iVariableId = rel[1];

        var titulo = "";
        switch(tipo){
            case "VariableTexto": titulo = "Editar variable de Texto"; break;
            case "VariableCualitativa": titulo = "Editar variable Cualitativa"; break;
            case "VariableNumerica": titulo = "Editar variable Numérica"; break;
        }
        
        var dialog = setWaitingStatusDialog(550, titulo);

        //desde el page controller me doy cuenta como muestro el formulario por la clase del objeto variable
        //aca la unica condicion que me fijo es para bindear el javascript segun el tipo de formulario, la url es la misma.
        dialog.load(
            "seguimientos/form-editar-variable",
            {"iVariableId":iVariableId},
            function(responseText, textStatus, XMLHttpRequest){
                bindEventsVariableForm();
                if(tipo == "VariableCualitativa"){
                    bindEventsVariableCualitativaForm();
                }
            }
        );
    });

    $(".borrarVariable").live('click', function(){
        var iVariableId = $(this).attr("rel");
        eliminarVariable(iVariableId);
    });
});