$(document).ready(function(){

    //menu derecha
    $("#pageRightInnerContNav li").mouseenter(function(){
        if(!$(this).hasClass("selected")){
            $(this).children("ul").fadeIn('slow');
        }
    });
    $("#pageRightInnerContNav li").mouseleave(function(){
        if(!$(this).hasClass("selected")){
            $(this).children("ul").fadeOut('slow');
        }
    });

    $("#antecedentes").bind('keyup',function(e){
            /*var char = "";
            if(e.which == 13){
                    char = "<br>";
            }else{
                    char = String.fromCharCode(e.which);
            }*/
            $("#antecedentesTexto").html(this.value);
    });

    $("#guardarAntecedentes").live('click',function(){
         $("#msg").html("");
         $("#msg").removeClass("di_bl").addClass("di_no");
         var error = "";
         if($("#antecedentes").val()==""){
                 error += "Debe ingresar un antecedente.";
         }
         if(error!=""){
                 $("#msg").html(error);
                 $("#msg").removeClass("di_no").addClass("di_bl");
         }else{
                 $.ajax({
                    url: "seguimientos/procesar-antecedentes",
                    type: "POST",
                    data:{
                        "textoAntecedentes": "1",
                        "antecedentes": $("#antecedentes").val(),
                        "id": $("#idSeguimiento").val()
                    },
                    beforeSend: function(){
                        setWaitingStatus('listadoSeguimientos', true);
                    },
                    success: function(data){
                        if(data.success != undefined || data.success == 1){
                                $("#msg_form_Antecedentes .msg").html("Los datos se guardaron exitosamente.");
                                        $('#msg_form_Antecedentes').removeClass("error").addClass("correcto").fadeIn('slow');
                        }
                        setWaitingStatus('listadoSeguimientos', false);
                    }
                });
         }
    });
	 
    new Ajax_upload('#antUpload', {
        action: 'seguimientos/procesar-antecedentes',
        data:{
                fileAntecedentesUpload: "1",
            seguimientoId: $("#idSeguimiento").val(),
        },
        name: 'fileAntecedentes',
        onChange:function(file , ext){
                if(confirm("Se eliminara el archivo anterior, desea realizar esta operacion?")){
                        return true;
                }else{
                        return false;
                }
        },
        onSubmit:function(file , ext){
            this.disable(); //solo un archivo a la vez
            setWaitingStatus('formAntecedentes', true);
        },
        onProgress: function(id, fileName, loaded, total){

        },
        onComplete:function(file, response){
            setWaitingStatus('formAntecedentes', false);
        this.enable();

        if(response == undefined){
            $('#msg_form_Antecedentes .msg').html(lang['error procesar']);
            $('#msg_form_Antecedentes').addClass("error").fadeIn('slow');
            return;
        }

        var dataInfo = response.split(';');
        var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria, 2 = satisfactorio, paso a ser integrante activo
        var html = dataInfo[1]; //si es satisfactorio el html devuelve el bloque de descarga

        //si rebota por accion desactivada o alguna de esas no tiene el formato de "0; mensaje mensaje mensaje"
        if(resultado != "0" && resultado != "1" && resultado != "2"){
            $('#msg_form_Antecedentes .msg').html(lang['error permiso']);
            $('#msg_form_Antecedentes').addClass("info").fadeIn('slow');
            return;
        }

        if(resultado == '0'){
            $('#msg_form_Antecedentes .msg').html(html);
            $('#msg_form_Antecedentes').addClass("error").fadeIn('slow');
        }else{
            $('#msg_form_Antecedentes .msg').html(lang['exito procesar archivo']);
            $('#msg_form_Antecedentes').addClass("correcto").fadeIn('slow');
            $('#wrapAntActual').html(html);
        }
        return;
        }
    });
});