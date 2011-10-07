
function eliminarEspecialidad(id){
   window.location = "admin/eliminar-especialidad?iEspecialidadId="+id;
}
function editarEspecialidad(id){
   $("#iEspecialidadId").val(id);
   var form = document.forms["editar"];
   form.submit();
}