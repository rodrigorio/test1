var ejeEliminados = new Array();

function tieneEjes(){
	var ejes = $(".eje");
	return ejes.length > 0 ? false : true;
}

var validateFormDiagnostico = {
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
        diagnostico:{required:tieneEjes},
        nivel:{required:tieneEjes},
        ciclo:{required:tieneEjes},
        area:{required:tieneEjes},
        eje:{required:tieneEjes}
    },
    messages:{
        diagnostico: mensajeValidacion("requerido"),
        nivel: mensajeValidacion("requerido"),
        ciclo: mensajeValidacion("requerido"),
        area: mensajeValidacion("requerido"),
        eje: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormDiagnostico = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/procesar-diagnostico',
    beforeSerialize:function(){        
        if($("#formGuardarDiagnostico").valid() == true){
            $('#msg_form_guardarDiagnostico').hide();
            $('#msg_form_guardarDiagnostico').removeClass("correcto").removeClass("error");
            $('#msg_form_guardarDiagnostico .msg').html("");
            setWaitingStatus('tabsFormDiagnostico', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('tabsFormDiagnostico', false);
        
        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_guardarDiagnostico .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_guardarDiagnostico .msg').html(data.mensaje);
            }
            $('#msg_form_guardarDiagnostico').addClass("error").fadeIn('slow');
        }else{
        	ejeEliminados = new Array();
        	actualizarInputEjesEliminados();
        	//$('input[name="nivelHiddenNew[]"],input[name="cicloHiddenNew[]"],input[name="areaHiddenNew[]"],input[name="ejeHiddenNew[]"],input[name="estadoInicialHiddenNew[]"]').each(function() {
        	//	var name = $(this).attr( "name")
        	//	name = name.replace("New","");
        	//	$(this).attr( "name", name );
        	//});
        	
            if(data.mensaje == undefined){
                $('#msg_form_guardarDiagnostico .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_guardarDiagnostico .msg').html(data.mensaje);
            }
            $('#msg_form_guardarDiagnostico').addClass("correcto").fadeIn('slow');
        }
    }
};

function bindEventFormAgregarArchivo(iSeguimientoId)
{
    if($('#diagnosticoUpload').length){
        new Ajax_upload('#diagnosticoUpload', {
            action:'seguimientos/procesar-diagnostico',
            data:{
                fileDiagnosticoUpload:"1",
                iSeguimientoId:iSeguimientoId
            },
            name:'archivoDiagnostico',
            onChange:function(file , ext){
                if(confirm("Se eliminara el archivo anterior, desea realizar esta operacion?")){
                    return true;
                }else{
                    return false;
                }
            },
            onSubmit:function(file , ext){
                $('#msg_form_diagnostico').hide();
                $('#msg_form_diagnostico').removeClass("correcto").removeClass("error");
                $('#msg_form_diagnostico .msg').html("");
                setWaitingStatus('tabsFormDiagnostico', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                
                setWaitingStatus('tabsFormDiagnostico', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_diagnostico .msg').html(lang['error procesar']);
                    $('#msg_form_diagnostico').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';;');
                var resultado = dataInfo[0]; 
                var html = dataInfo[1]; 

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_diagnostico .msg').html(lang['error permiso']);
                    $('#msg_form_diagnostico').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_diagnostico .msg').html(html);
                    $('#msg_form_diagnostico').addClass("error").fadeIn('slow');
                }else{
                    $('#msg_form_diagnostico .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_diagnostico').addClass("correcto").fadeIn('slow');

                    $('#wrapAntActual').html(html);
                }
                return;
            }
        });
    }    
}

$(document).ready(function(){
	
    $("a[rel^='prettyPhoto']").prettyPhoto();

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

    $("#tabsFormDiagnostico").tabs();
    $("#diagnostico").maxlength({maxCharacters:1000});

    $("#formGuardarDiagnostico").validate(validateFormDiagnostico);
    $("#formGuardarDiagnostico").ajaxForm(optionsAjaxFormDiagnostico);

    bindEventFormAgregarArchivo($('#idSeguimiento').val());
    
    $("#nivel").live("change",function(){
    	me = this;
    	$.ajax({
            url: "seguimientos/listar-ciclos-por-niveles",
            type: "POST",
            data:{
                "nivelId":me.value
            },
            beforeSend: function(){
                setWaitingStatus('ciclo', true);
            },
            success:function(data){
                setWaitingStatus('ciclo', false);
                $("#ciclo").html(data);
                $("#area").html("<option value=''>Seleccione un area</option>");
                
            }
        });
    });
    
    $("#ciclo").live("change",function(){
    	me = this;
    	$.ajax({
            url: "seguimientos/listar-areas-por-ciclos",
            type: "POST",
            data:{
                "cicloId":me.value
            },
            beforeSend: function(){
                setWaitingStatus('area', true);
            },
            success:function(data){
                setWaitingStatus('area', false);
                $("#area").html(data);
            }
        });
    });
    
    $("#area").live("change",function(){
    	me = this;
    	$.ajax({
            url: "seguimientos/listar-ejes-por-area",
            type: "POST",
            data:{
                "areaId":me.value
            },
            beforeSend: function(){
                setWaitingStatus('eje', true);
            },
            success:function(data){
                setWaitingStatus('eje', false);
                $("#eje").html(data);
            }
        });
    });

    $(".verPersona").live('click',function(){

        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");

        var dialog = setWaitingStatusDialog(450, $(this).html());
        setWaitingStatus('fichaPersonaMenu', true, "16");
        dialog.load(
            "seguimientos/ver-persona?personaId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                setWaitingStatus('fichaPersonaMenu', false, "16");
                bindEventsPersonaVerFicha(); //la funcion esta en personas.js
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });
    
    $("#agregarEje").live('click',function(){
    	var ejes= $(".eje");
    	for (var i = 0; i < ejes.length; i++) {
    	    if (ejes[i].value == $('#eje option:selected').val()) {
    	    	alert("No puede agregar 2 veces el mismo eje.")
    	    	return false;
    	    }
    	}
    	$('#msg_form_guardarDiagnostico').hide();
    	if ($('#eje option:selected').val() == "") {
    		 $('#msg_form_guardarDiagnostico').show();
             $('#msg_form_guardarDiagnostico').removeClass("correcto").addClass("error");
             $('#msg_form_guardarDiagnostico .msg').html("Debe seleccionar un eje.");
             return false;
    	}
      
    	var html = 
			       " <tr id='"+$('#eje option:selected').val()+"'>"+
			       " 	<td>"+$('#nivel option:selected').text()+"<input type='hidden'  name='nivelHiddenNew[]' value='"+$('#nivel option:selected').val()+"'/></td>"+
			       "   	<td>"+$('#ciclo option:selected').text()+"<input type='hidden'  name='cicloHiddenNew[]' value='"+$('#ciclo option:selected').val()+"'/></td>"+
			       " 	<td>"+$('#area option:selected').text()+"<input type='hidden'  name='areaHiddenNew[]' value='"+$('#area option:selected').val()+"'/></td>"+
			       "  	<td>"+$('#eje option:selected').text()+"<input type='hidden'  class='eje' name='ejeHidden["+$('#eje option:selected').val()+"][id]' value='"+$('#eje option:selected').val()+"'/></td>"+
			       "	<td><div class='fwrap'><div class='inner_fwrap'><textarea rows='0' cols='0' class='textAreaLihe textareaAutoGrow defVal maxlength' onblur='editarEje(this);'>"+$('#estadoInicial').val()+"</textarea><input type='hidden'  name='ejeHidden["+$('#eje option:selected').val()+"][estadoInicial]' value='"+$('#estadoInicial').val()+"'/><input type='hidden'  name='ejeHidden["+$('#eje option:selected').val()+"][new]' value='si'/></div></div></td>"+
			       "	<td><span onclick='eliminarEje(this)' class='i bs delete ihover' rel='2' title='Eliminar eje'></span></td>"+
			       " </tr>";

    	$("#contentEjesResult").append(html);
    });
});

function eliminarEje(eje)
{
	ejeEliminados.push($(eje).parent().parent().attr("id"));
	actualizarInputEjesEliminados();
	$(eje).parent().parent().remove();
}

function actualizarInputEjesEliminados()
{
	$("#ejeEliminados").val(  ejeEliminados.toString() );	
}

function editarEje(eje)
{
	$(eje).parent().find("input[type=hidden]").val($(eje).val());
}