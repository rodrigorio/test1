$(document).ready(function(){
	
	$("#puntaje div").click(function(){
		var valorMarcado = $(this);
		alert("has votado"); /* aca despues meter el codigo */
	});	
	
	/* Ejemplo de dialog en menu01 con ajax despues borrar */
	$("#menu1").live('click',function(){
		var dialog = $("#dialog");
		if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Titulo"></div>').appendTo('body'); }
		
		dialog.load(
			baseUrl+'gui/componentes/carteles.html',
			{},
			function(responseText, textStatus, XMLHttpRequest){ 
				dialog.dialog({
								width:500,
								resizable:false,
								modal:true,
								buttons:{Cerrar:function(){ $( this ).dialog("close");}}
							}); 
			}
		);

		return false;
	});

});
										 