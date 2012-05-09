$(document).ready(function(){
	$("#antecedentes").bind('keyup',function(e){
		/*var char = "";
		if(e.which == 13){
			char = "<br>";
		}else{
			char = String.fromCharCode(e.which);
		}*/
		$("#antecedentesTexto").html(this.value);
	});
	 $("#guardarAntecedentes").live('click',function(){
		 $("#msg").html("");
		 $("#msg").removeClass("di_bl").addClass("di_no");
		 var error = "";
		 if($("#antecedentes").val()==""){
			 error += "Debe ingresar un antecedente.";
		 }
		 if(error!=""){
			 $("#msg").html(error);
			 $("#msg").removeClass("di_no").addClass("di_bl");
		 }else{
			 $.ajax({
		            url: "seguimientos/procesar-antecedentes",
		            type: "POST",
		            data:{
		            	"textoAntecedentes": "1",
		            	"antecedentes": $("#antecedentes").val(),
		            	"id": $("#idSeguimiento").val()
		            },
		            beforeSend: function(){
		                setWaitingStatus('listadoSeguimientos', true);
		            },
		            success: function(data){
		            	if(data.success != undefined || data.success == 1){
		            		$("#msg").html("Los datos se guardaron exitosamente.");
		       			 	$("#msg").removeClass("di_no").addClass("di_bl");  
		       			 	$("#msg").removeClass("error").addClass("correcto");  
		            	}
		                setWaitingStatus('listadoSeguimientos', false);
		            }
			});
		 }
	 });
	 
	  new Ajax_upload('#fileAntecedentes', {
	        action: 'seguimientos/procesar-antecedentes',
	        data:{
	        	fileAntecedentesUpload: "1",
	            seguimientoId: $("#idSeguimiento").val(),
	        },
	        name: 'fileAntecedentes',
	        onSubmit:function(file , ext){
	        	alert(1)
	            this.disable(); //solo un archivo a la vez
	        },
	        onProgress: function(id, fileName, loaded, total){
	        	alert(2)
	        },
	        onComplete:function(file, response){
	        	alert(3)
	            setWaitingStatus('tabsFormPersona', false);
	            this.enable();

	            if(response == undefined){
	                $('#msg_form_fotoPerfil .msg').html(lang['error procesar']);
	                $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
	                return;
	            }

	            var dataInfo = response.split(';');
	            var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria, 2 = satisfactorio, pendiente de moderacion
	            var html = dataInfo[1]; //aca queda el bloque del html que acompaña el resultado

	            if(resultado != "0" && resultado != "1" && resultado != "2"){
	                $('#msg_form_fotoPerfil .msg').html(lang['error permiso']);
	                $('#msg_form_fotoPerfil').addClass("info").fadeIn('slow');
	                return;
	            }

	            if(resultado == '0'){
	                $('#msg_form_fotoPerfil .msg').html(html);
	                $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
	            }else{
	                if(resultado == '1'){
	                    $('#msg_form_fotoPerfil .msg').html(lang['exito procesar archivo']);
	                    $('#contFotoPerfilActual').html(html);
	                    $("a[rel^='prettyPhoto']").prettyPhoto(); //asocio el evento al html nuevo
	                    $('#msg_form_fotoPerfil').addClass("correcto").fadeIn('slow');
	                }else{
	                    $('#msg_form_fotoPerfil .msg').html(html);
	                    $('#msg_form_fotoPerfil').addClass("correcto").fadeIn('slow');
	                }
	            }
	            return;
	        }
	    });
	
});

