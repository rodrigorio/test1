$(document).ready(function(){

   
    var validateFormSeguimiento = {
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
                personaId:{required:true},
                tipoPractica:{required:true},
                tipoSeguimiento:{required:true}
            },
            messages:{
                personaId:{
                        required: mensajeValidacion("requerido")
                },
                tipoPractica:{
                        required: mensajeValidacion("requerido")
                },
                tipoSeguimiento:{
                        required: mensajeValidacion("requerido")
                }
            }
        }
        $("#formCrearSeguimiento").validate(validateFormSeguimiento);
    
	var optionsAjaxFormSeguimiento = {
                dataType: 'jsonp',
                resetForm: false,
                url: 	"seguimientos/procesar-seguimiento",
                beforeSerialize: function($form, options){
                    if($("#formCrearSeguimiento").valid() == true){
                        $('#msg_form_crearSeguimiento').hide();
                        $('#msg_form_crearSeguimiento').removeClass("correcto").removeClass("error");
                        $('#msg_form_crearSeguimiento .msg').html("");
                        //setWaitingStatus('formInfoBasica', true);
                    }else{
                        return false;
                    }
                },

                success:function(data){
                    setWaitingStatus('formCrearSeguimiento', false);
                    if(data.success == undefined || data.success == 0){
                        $('#msg_form_crearSeguimiento .msg').html(lang['error procesar']);
                        $('#msg_form_crearSeguimiento').addClass("error").fadeIn('slow');
                    }else{
                        $('#msg_form_crearSeguimiento .msg').html(lang['exito procesar']);
                        $('#msg_form_crearSeguimiento').addClass("correcto").fadeIn('slow');
                        $('#formCrearSeguimiento input[type=text],#formCrearSeguimiento select,#formCrearSeguimiento textarea').val("");
                        $("#persona").removeClass("selected");
                        $("#persona").removeAttr("readonly");
                        $("#persona").val("");
                        $("#personaId").val("");
                        ocultarElemento($('#persona_clean'));
                    }
                }
            };
            
	$("#formCrearSeguimiento").ajaxForm(optionsAjaxFormSeguimiento);

    $("#agregarPersona").live('click',function(){
        $.getScript(pathUrlBase+"gui/vistas/seguimientos/personas.js");
        var dialog = $("#dialog");
        if ($("#dialog").length == 0){ dialog = $('<div id="dialog" title="Agregar Persona"></div>').appendTo('body'); }
        dialog.load(
            "seguimientos/agregar-persona?popUp=1",
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:650,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true
                });
                $("#tabsFormPersona" ).tabs();
            }            
        );
        return false;
    });
    
    $("#persona").autocomplete({
        source:function(request, response){
            $.ajax({
                url: "seguimientos/buscar-usuarios",
                dataType: "jsonp",
                data:{
                    limit:12,
                    str:request.term
                },
                beforeSend: function(){
                   revelarElemento($("#persona_loading"));
                },
                success: function(data){
                   ocultarElemento($("#persona_loading"));
                    response( $.map(data.usuarios, function(usuarios){
                        return{
                            //lo que aparece en el input
                            value:usuarios.sNombre,
                            //lo que aparece en la lista generada para elegir
                            label:usuarios.sNombre,
                            //valor extra que se devuelve para completar el hidden
                            id:usuarios.iId
                        }
                    }));
                }
            });
        },
        minLength: 8,
        select: function(event, ui){
            if(ui.item){
                $("#personaId").val(ui.item.id);
            }else{
                $("#personaId").val("");
            }
        },
        close: function(){
            if($("#personaId").val() != ""){
                $(this).addClass("selected");
                $(this).attr('readonly', 'readonly');
                revelarElemento($('#persona_clean'));
            }
        }
    });
});
//para borrar la institucion seleccionada con el autocomplete
$('#persona_clean').click(function(){
    $("#persona").removeClass("selected");
    $("#persona").removeAttr("readonly");
    $("#persona").val("");
    $("#personaId").val("");
    ocultarElemento($(this));
});