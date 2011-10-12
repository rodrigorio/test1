//modificar privacidad (con ajax)
function cambiarPrivacidad(campo, valor){
    var fields = "nombreCampo="+campo+"&valorPrivacidad="+valor;
    $.ajax({
        type:	"POST",
        url: 	"comunidad/modificarPrivacidadCampo",
        data: 	fields,
        beforeSend: function(){
            setWaitingStatus('pageRightInnerCont', true);
        },
        success:function(data){
            setWaitingStatus('pageRightInnerCont', false);
        }
    });
}

$("#privacidadEmail").change(function(){ cambiarPrivacidad('email', $("#privacidadEmail option:selected").val()); });
$("#privacidadTelefonoContacto").change(function(){ cambiarPrivacidad('telefono', $("#privacidadTelefonoContacto option:selected").val()); });
$("#privacidadMovil").change(function(){ cambiarPrivacidad('celular', $("#privacidadMovil option:selected").val()); });
$("#privacidadFax").change(function(){ cambiarPrivacidad('fax', $("#privacidadFax option:selected").val()); });
$("#privacidadCurriculum").change(function(){ cambiarPrivacidad('curriculum', $("#privacidadCurriculum option:selected").val()); });