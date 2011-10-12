function verificarUsoCategoria(id){
	$.ajax({
		type: "POST",
		url: "admin/verfificar-uso-categoria",
		data: "id="+id,
		success: function(data){
			if(data==1){
				alert("Prohibido eliminar. El item elegido esta siendo utilizado por uno o varios usuarios.");
			}else{
				eliminarCategoria(id);
			}
		}
	});
}
function eliminarCategoria(id){
   if(confirm("Está seguro que desea eliminar esta categoria?")){
		$.ajax({
			type: "POST",
		   	url: "admin/eliminar-categoria",
		   	data: "id="+id,
		   	success: function(data){
		   		$("#categorias").html(data);
		   	}
		});
	}
}
function editarCategoria(id){
   $("#iCategoriaId").val(id);
   var form = document.forms["editar"];
   form.submit();
}