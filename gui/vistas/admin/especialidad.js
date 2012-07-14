//existe ya una institucion con ese nombre?
jQuery.validator.addMethod("nombreEspecialidadDb", function(value, element){
    var result = true;
    if($("#nombre").val() != ""){
        
        var data;
        if($("#iEspecialidadId").length && $("#iEspecialidadId").val() != ""){
            data = {
                sNombre:function(){return $("#nombre").val();},
                iEspecialidadId: function(){return $("#iEspecialidadId").val();}
            }
        }else{
            data = {sNombre:function(){return $("#nombre").val();}}
        }
        
        $.ajax({
            url:"admin/verfificar-uso-especialidad",
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

var validateFormEspecialidad = {
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
        nombre:{required:true, nombreEspecialidadDb:true},
        descripcion:{required:true}
    },
    messages:{
        nombre:{
            required: mensajeValidacion("requerido"),
            nombreEspecialidadDb: "Ya existe una especialidad con ese nombre en el sistema"
        },
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormEspecialidad = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-especialidad',
    beforeSerialize:function(){

        if($("#formEspecialidad").valid() == true){

            $('#msg_form_especialidad').hide();
            $('#msg_form_especialidad').removeClass("success").removeClass("error2");
            $('#msg_form_especialidad .msg').html("");
            setWaitingStatus('formEspecialidad', true);

        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formEspecialidad', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_especialidad .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_especialidad .msg').html(data.mensaje);
            }
            $('#msg_form_especialidad').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_especialidad .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_especialidad .msg').html(data.mensaje);
            }
            if(data.agregarEspecialidad != undefined){
                $('#formEspecialidad').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_especialidad').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsEspecialidadForm(){
    $("#formEspecialidad").validate(validateFormEspecialidad);
    $("#formEspecialidad").ajaxForm(optionsAjaxFormEspecialidad);
}

function borrarEspecialidad(iEspecialidadId){

    if(confirm("Se borrara la especialidad del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/eliminar-especialidad",
            data:{
                iEspecialidadId:iEspecialidadId
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iEspecialidadId).hide("slow", function(){
                        $("."+iEspecialidadId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Especialidad");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Especialidad"></div>').appendTo('body');
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

$(document).ready(function(){

    if($("#formEspecialidad").length){
        bindEventsEspecialidadForm();
    }
    
    $(".borrarEspecialidad").live('click', function(){
        var iEspecialidadId = $(this).attr("rel");
        borrarEspecialidad(iEspecialidadId);
    });

});