$(function(){
	$('#formEnviarInvitacion').submit(function(){ return false; });
	$('#enviarInvitacion').click(function(){
		var fields = $('#formEnviarInvitacion input[type=text]');
	    var error = 0;
	    fields.each(function(){
	        var value = $(this).val();
	        if( value == "" || value == $(this).attr("title") ) {
	            $(this).addClass('inv_error');
	            error++;
	        } else {
	            $(this).removeClass('inv_error');
	        }
	    });
	    if(!error) {
		    var fields = "nombre="+$('#nombre').val()+
			"&apellido="+$('#apellido').val()+
			"&email="+$('#email').val()+
			"&relacion="+$('#relacion').val();
			$.ajax({
				type:	"POST",
				url: 	"comunidad/invitacion-procesar",
				data: 	fields,
				beforeSend: function() {
					$("#ajax_loading").show();
				},
				success: function(data){
					$("#ajax_loading").hide();
					var resp = $.parseJSON(data);
					if(data.success==1){
						$("#msg_conf").addClass("correcto");
						$("#msg_conf").html("Su invitacion se ha enviado correctamente");
					}else{
						$("#msg_conf").addClass("error");
						$("#msg_conf").html("Su invitacion no se ha podido enviar correctamente");
					}
					$("#formEnviarInvitacion").hide();
					$("#msg_conf").show();
					
				}
			});
	    }else{
	    	return false;
	    }
	});
});