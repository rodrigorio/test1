//modificar privacidad (con ajax)
function cambiarPrivacidad(element){
    var fields = "email="+email+"&nombreUsuario="+nombreUsuario;
    $.ajax({
                    type:	"POST",
                    url: 	"recuperarContrasenia",
                    data: 	fields,
                    beforeSend: function() {
                            $("#ajax_loading").show();
                    },
                    success: function(data){
                            $("#ajax_loading").hide();
                            if(data.success==1){

                            }else{
                                    alert(data.mensaje);
                            }
                    }
    });
}

$("#privacidadEmail").change(function(){ cambiarPrivacidad($(this)); });
$("#privacidadTelefonoContacto").change(function(){ cambiarPrivacidad($(this)); });
$("#privacidadMovil").change(function(){ cambiarPrivacidad($(this)); });
$("#privacidadFax").change(function(){ cambiarPrivacidad($(this)); });
$("#privacidadCurriculum").change(function(){ cambiarPrivacidad($(this)); });