//existe ya una institucion con ese nombre?
jQuery.validator.addMethod("nombreCategoriaDb", function(value, element){
    var result = true;
    if($("#nombre").val() != ""){

        var data;
        if($("#iCategoriaId").length && $("#iCategoriaId").val() != ""){
            data = {
                sNombre:function(){return $("#nombre").val();},
                iCategoriaId: function(){return $("#iCategoriaId").val();}
            }
        }else{
            data = {sNombre:function(){return $("#nombre").val();}}
        }

        $.ajax({
            url:"admin/verfificar-uso-categoria",
            type:"post",
            async:false,
            data:data,
            success:function(data){
                //si el nombre existe tira el cartel
                if(data == '1'){result = false;}
            }
        });
    }
    return result;
});

var validateFormCategoria = {
    errorElement: "span",
    validClass: "valid-side-note",
    errorClass: "invalid-side-note",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    highlight: function(element, errorClass, validClass){
        $(element).addClass("invalid");
    },
    unhighlight: function(element, errorClass, validClass){
        $(element).removeClass("invalid");
    },
    rules:{
        nombre:{required:true, nombreCategoriaDb:true},
        descripcion:{required:true}
    },
    messages:{
        nombre:{
            required: mensajeValidacion("requerido"),
            nombreCategoriaDb: "Ya existe una categoria con ese nombre en el sistema"
        },
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormCategoria = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-categoria',
    beforeSerialize:function(){

        if($("#formCategoria").valid() == true){

            $('#msg_form_categoria').hide();
            $('#msg_form_categoria').removeClass("success").removeClass("error2");
            $('#msg_form_categoria .msg').html("");
            setWaitingStatus('formCategoria', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCategoria', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_categoria .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_categoria .msg').html(data.mensaje);
            }
            $('#msg_form_categoria').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_categoria .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_categoria .msg').html(data.mensaje);
            }
            if(data.agregarCategoria != undefined){
                $('#formCategoria').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_categoria').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsCategoriaForm(){
    $("#formCategoria").validate(validateFormCategoria);
    $("#formCategoria").ajaxForm(optionsAjaxFormCategoria);
}

function borrarCategoria(iCategoriaId){

    if(confirm("Se borrara la categoria del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/eliminar-categoria",
            data:{
                iCategoriaId:iCategoriaId
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iCategoriaId).hide("slow", function(){
                        $("."+iCategoriaId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Categoría");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Categoría"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

function uploaderFoto(iCategoriaId){
    if($('#fotoUpload').length){
        new Ajax_upload('#fotoUpload', {
            action:'admin/procesar-categoria',
            data: {
                procesarFoto:"1",
                iCategoriaId:iCategoriaId
            },
            name:'fotoUpload',
            onSubmit:function(file , ext){
                $('#msg_form_foto').hide();
                $('#msg_form_foto').removeClass("success").removeClass("error2");
                $('#msg_form_foto .msg').html("");
                setWaitingStatus('formFotoCont', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('formFotoCont', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_foto .msg').html(lang['error procesar']);
                    $('#msg_form_foto').addClass("error2").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si se proceso bien aca queda el bloque del html con el nuevo thumbnail

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_foto .msg').html(lang['error permiso']);
                    $('#msg_form_foto').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_foto .msg').html(html);
                    $('#msg_form_foto').addClass("error2").fadeIn('slow');
                }else{
                    $('#msg_form_foto .msg').html(lang['exito procesar archivo']);
                    $('#contFotoActual').html(html).show();
                    $("a[rel^='prettyphoto']").prettyphoto();
                    $('.image-frame').hover(
                        function() { $(this).find('.image-actions').css('display', 'none').fadeIn('fast').css('display', 'block'); }, // Show actions menu
                        function() { $(this).find('.image-actions').fadeOut(100); } // Hide actions menu
                    );
                    $('#msg_form_foto').addClass("success").fadeIn('slow');
                }
                return;
            }
        });
    }
}

function borrarFoto(iCategoriaId){
    if(confirm("Se borrara la foto de la categoria, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/procesar-categoria",
            data:{
                iCategoriaId:iCategoriaId,
                borrarFoto:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("#contFotoActual").hide("slow", function(){
                        $("#contFotoActual").html("");
                    });
                }
            }
        });
    }
}

$(document).ready(function(){

    $("a[rel^='prettyphoto']").prettyphoto();

    if($("#formCategoria").length){
        bindEventsCategoriaForm();
    }

    $(".borrarCategoria").live('click', function(){
        var iCategoriaId = $(this).attr("rel");
        borrarCategoria(iCategoriaId);
    });

    if($("#fotoUpload").length){
        uploaderFoto($("#iCategoriaId").val());
    }

    $("#fotoBorrar").live('click', function(){
        var iCategoriaId = $(this).attr("rel");
        borrarFoto(iCategoriaId);
        return false;
    });
});
