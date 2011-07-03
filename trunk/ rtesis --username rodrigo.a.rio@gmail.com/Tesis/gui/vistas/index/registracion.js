$(function(){
	$('form').submit(function(){ return false; });
	$('#reg_button').click(function(){
	    //remove classes
	    $('#registracion input').removeClass('error').removeClass('valid');
	
	    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
	    var fields = $('#registracion input[type=text], input[type=password],#registracion select');
	    var error = 0;
	    fields.each(function(){
	        var value = $(this).val();
	        if( value == -1 || value.length<1 || ( $(this).attr('id')=='reg_email' && !emailPattern.test(value) ) ) {
	            $(this).addClass('reg_error');
	            //$(this).effect("shake", { times:3,distance:0 }, 100);
	            error++;
	        } else {
	            $(this).removeClass('reg_error');
	        }
	    });
	    
	    if( $('#reg_pass').val() != $('#reg_pass_confirmacion').val() ) {
            $('#registracion input[type=password]').each(function(){
                $(this).addClass('error');
                error++;
            });
	    }
	    if(!error) {
	    	var fields = "tipoDni="+$('#reg_tipoDni').val()+
    		"&dni="+$('#reg_dni').val()+
    		"&username="+$('#reg_nombre_usuario').val()+
    		"&password="+$('#reg_pass').val()+
    		"&email="+$('#reg_email').val()+
    		"&firstname="+$('#reg_nombre').val()+
    		"&lastname="+$('#reg_apellido').val()+
    		"&sex="+$('#reg_sex').val()+
    		"&inv="+$('#inv').val()+
    		"&us="+$('#us').val()+
    		"&fechaNacimiento="+$('#reg_dia_fecha_de_nacimiento').val()+"/"+$('#reg_mes_fecha_de_nacimiento').val()+"/"+$('#reg_anio_fecha_de_nacimiento').val()+"";                       
			$.ajax({
				type:	"POST",
				url: 	"registracion-procesar",
				data: 	fields,
				beforeSend: function() {
					$("#registracion_msg").html("");
					$("#registracion_msg").hide();
					$("#registracion_msg_error").html("");
					$("#registracion_msg_error").hide();
					$("#loading").show();
				},
				success: function(data){
					$("#loading").hide();
					var resp = $.parseJSON(data);
					if(resp == false){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Se ha producido un error desconocido. Por favor intente mas tarde.");
						return false;						
					}
					if(resp == 10){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Nombre de usuario existente");
						$("#reg_nombre_usuario").addClass('reg_error');
						return false;						
					}else if(resp == 11){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Numero de documento existente");
						$("#reg_dni").addClass('reg_error');
						return false;						
					}else if(resp == 12){
						$("#registracion_msg_error").show();
						$("#registracion_msg_error").html("Email existente");
						$("#reg_email").addClass('reg_error');
						return false;						
					}
					
					$("#registracion").hide();
					//$("#registracion_msg").html("Registracion existosa");
					//$("#registracion_msg").show();
					//enviar a la primer pagina del perfil
					location = data.redirect;
				}
			});
	    } else{
	    	return false;
	    }
	});
});