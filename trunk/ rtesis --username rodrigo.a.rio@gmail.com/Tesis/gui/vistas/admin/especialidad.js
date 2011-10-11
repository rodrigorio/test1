function verificarUsoEspecialidad(id){
	$.ajax({
		type: "POST",
		url: "admin/verfificar-uso-especialidad",
		data: "id="+id,
		success: function(data){
			if(data==1){
				alert("Prohibido eliminar. El item elegido esta siendo utilizado por uno o varios usuarios.");
			}else{
				eliminarEspecialidad(id);
			}
		}
	});
}
function eliminarEspecialidad(id){
   if(confirm("Está seguro que desea eliminar esta especialidad?")){
		$.ajax({
			type: "POST",
		   	url: "admin/eliminar-especialidad",
		   	data: "id="+id,
		   	success: function(data){
		   		$("#especialidades").html(data);
		   	}
		});
	}
}
function editarEspecialidad(id){
   $("#iEspecialidadId").val(id);
   var form = document.forms["editar"];
   form.submit();
}