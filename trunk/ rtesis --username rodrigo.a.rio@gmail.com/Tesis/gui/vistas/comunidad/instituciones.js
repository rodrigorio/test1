$(function(){
	$('#formCrearInstitucion').submit(function(){ return false; });
	$('#crearInstitucion').click(function(){
            var fields = $('#formCrearInstitucion input[type=text],#formCrearInstitucion select');
	    var error = 0;
	    fields.each(function(){
	        var value = $(this).val();
	        if( (value == "" || value == $(this).attr("title")) && $(this).hasClass("requerido") ) {
	            $(this).addClass('inst_error');
	            error++;
	        } else {
	            $(this).removeClass('inst_error');
	        }
	    });
	    if(!error) {
		    var fields = "nombre="+$('#nombre').val()+
			"&tipo="+$('#tipo').val()+
			"&email="+$('#email').val()+
			"&cargo="+$('#cargo').val()+
			"&personaJuridica="+$('#personaJuridica').val()+
			"&direccion="+$('#direccion').val()+
			"&ciudad="+$('#ciudad').val()+
			"&tel="+$('#tel').val()+
			"&web="+$('#web').val()+
			"&horarioAtencion="+$('horarioAtencion').val()+
			"&sedes="+$('#sedes').val()+
			"&autoridades="+$('#autoridades').val()+
			"&actividadesMes="+$('#actividadesMes').val()+
			"&descripcion="+$('#descripcion').val();
			$.ajax({
				type:	"POST",
				url: 	"comunidad/institucion-procesar",
				data: 	fields,
				beforeSend: function() {
					$("#ajax_loading").show();
				},
				success: function(data){
					$("#ajax_loading").hide();
					//var resp = $.parseJSON(data);
                                        if(data==1){
						$("#msg_conf").addClass("correcto");
						$("#msg_conf").html("Se ha creado la institucion correctamente");
					}else{
						$("#msg_conf").addClass("error");
						$("#msg_conf").html("No se ha creado la institucion correctamente");
					}
					$("#formCrearInstitucion").hide();
					$("#msg_conf").show();
				}
			});
	    }else{
	    	return false;
	    }
	});
});

function listaProvinciasByPais(me){
	$.ajax({
		type: "POST",
	   	url: "comunidad/provinciasByPais",
	   	data: "iPaisId="+me.value,
	   	success: function(data){
	   		var lista = $.parseJSON(data);
	   		$('#provincia').html("");
	   		if(lista.length != undefined && lista.length > 0){
	   			$('#provincia').append(new Option('Selecciones una provincia', '',true));
	   			for(var i=0;i<lista.length;i++){
	   				$('#provincia').append(new Option(lista[i].sNombre, lista[i].id));
				}
	   		}else{
	   			$('#provincia').append(new Option('Selecciones una provincia', '',true));
	   		}
	   	}
	});
 }
function listaCiudadesByProvincia(me){
	$.ajax({
		type: "POST",
		url: "comunidad/ciudadesByProvincia",
		data: "iProvinciaId="+me.value,
		success: function(data){
			var lista = $.parseJSON(data);
			$('#ciudad').html("");
			if(lista.length != undefined && lista.length > 0){
				$('#ciudad').append(new Option('Selecciones una ciudad', '',true));
				for(var i=0;i<lista.length;i++){
					$('#ciudad').append(new Option(lista[i].sNombre, lista[i].id));
				}
			}else{
				$('#ciudad').append(new Option('Selecciones una ciudad', '',true));
			}
		}
	});
}