/*
 * Javascript utilizado solamente para complementar el html y css. 
 * Los eventos que disparen funcionalidades de sistema van en los .js de cada vista en particular !!
 *
 */

$(document).ready(function(){					   		   
	
	// FUNCIONES //
	function valueToggleFocus(elemento){		
		if(elemento.attr("title") == elemento.val()){elemento.val("");}
	}	
	function valueToggleBlur(elemento, textarea){ 
		if(elemento.val() == ""){			
			if(textarea){
				elemento.addClass("tarea_ctrl");
				elemento.next().hide(); /*contar caracteres*/
			}		
			elemento.val(elemento.attr("title"));			
		}
	}	
	/* para revelar elementos dentro de una ficha o contenedor que tengan display none */
	function revelarElementos(bloque){
		bloque.children(".di_no").addClass("di_bl").removeClass("di_no");
	}

        /* para ocultar msgTop 10 segundos despues que se termina de cargar la pagina */
        setTimeout(function(){
            $("#msg_top").hide('drop', { direction: "up" }, 1000)
        }, 5000);
        
	// FIN FUNCIONES //
    		
			
	$("input").live("focus", function(){ valueToggleFocus($(this)); });		
	$("input").live("blur", function(){ valueToggleBlur($(this),false); });
	
	$("textarea").maxlength();
	$("textarea").live("focus", function(){		
		var elemento = $(this);
		valueToggleFocus(elemento);
		elemento.removeClass("tarea_ctrl");
		elemento.next().show(); /*contar caracteres*/		
	});		
	$("textarea").live("blur", function(){ valueToggleBlur($(this),true); });		
	
	$("#nuevoComentario").live("click", function(){ revelarElementos($(this)); });				
	
			
	// PUNTUAR PUBLICACIONES, ETC //
	var txts_puntaje = new Array();
	txts_puntaje['pun1'] = 'p\xe9simo';
	txts_puntaje['pun2'] = 'malo';
	txts_puntaje['pun3'] = 'normal';
	txts_puntaje['pun4'] = 'muy bueno';
	txts_puntaje['pun5'] = 'excelente';
	
	var puntaje = $("#puntaje");	
	var txt_puntaje = $("#txt_puntaje");
	var valorPosicAnt = "pun0";
	$("#puntaje div").mouseover(function(){
		var valorPosic = $(this).attr("id"); /* El id del span coincide con la clase del CSS para asignar de una el valor ;) */
		if(valorPosic != valorPosicAnt){
			puntaje.addClass(valorPosic).removeClass(valorPosicAnt);			
			txt_puntaje.html(txts_puntaje[valorPosic]);
			valorPosicAnt = valorPosic;
		}	
	});	
	puntaje.mouseout(function(){
		if(valorPosicAnt != "pun0"){ //si no marco ninguno vuelve a estado inicial
			puntaje.addClass("pun0").removeClass(valorPosicAnt);
			valorPosicAnt = "pun0";
			txt_puntaje.html("");
		}	
	});	
	// FIN PUNTUAR PUBLICACIONES, ETC //

        
        // AJAX //

        /* Para marcar el estado de una peticion ajax en un elemento de la vista */
        function showWaitingImage(show, message, error)
        {
            var $text = $("#ajaxWaitingText");
            var $image = $("#ajaxWaitingImage");
            $text.hide().html("").removeClass("ui-state-error");
            if (show) {
                $image.fadeIn("fast");
            } else {
                $image.fadeOut("slow", function(){
                    if (message) {
                        $text.html(message);
                        if (error) {
                            $text.addClass("ajaxFailedResult");
                        } else {
                            $text.addClass("ajaxSuccessResult");
                        }
                        $text.show();
                    }
                });
            }
        }
});