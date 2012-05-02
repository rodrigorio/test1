function cerrarCuentaUsuario(iUsuarioId){
    if(confirm("Se borrara el integrante del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"admin/usuarios-cerrar-cuenta",
            data:{
                iUsuarioId:iUsuarioId
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    //remuevo la fila y la ficha de la persona que se aprobo.
                    $("."+iUsuarioId).remove();
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Cerrar cuenta integrante");
                }else{
                    dialog = $('<div id="dialog" title="Cerrar cuenta integrante"></div>').appendTo('body');
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

function cambiarEstadoUsuario(iUsuarioId, valor){
    $.ajax({
        type: "POST",
        url: "admin/usuarios-procesar",
        data:{
            iUsuarioId:iUsuarioId,
            estadoUsuario:valor,
            cambiarEstado:"1"
        },
        beforeSend: function(){
            setWaitingStatus('listadoUsuarios', true);
        },
        success:function(data){
            setWaitingStatus('listadoUsuarios', false);
        }
    });
}

function borrarFotoPerfil(iUsuarioId){
    if(confirm("Se borrara la foto de perfil del usuario, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:"jsonp",
            url:"admin/usuarios-procesar",
            data:{
                iUsuarioId:iUsuarioId,
                borrarFotoPerfil:"1"
            },            
            success:function(data){
                if(data.success != undefined && data.success == 1){                    
                    $("#fotoPerfilActualCont").remove();
                    $("#fotoPerfilActual_"+iUsuarioId).remove();                    
                }
            }
        });
    }
}

//ya esta el mail registrado?
jQuery.validator.addMethod("mailDb", function(value, element){
    var result = true;
    if($("#email").val() != ""){
        $.ajax({
            url:"comunidad/datos-personales-procesar",
            type:"post",
            async:false,
            data:{
                seccion:"check-mail-existe",
                email:function(){return $("#email").val();}
            },
            success:function(data){
                //si el mail existe tira el cartel
                if(data == '1'){result = false;}
            }
        });
    }
    return result;
});
//el nombre de usuario ya esta siendo utilizado?
jQuery.validator.addMethod("nombreUsuarioDb", function(value, element){
    var result = true;
    if($("#nombreUsuario").val() != ""){
        $.ajax({
            url:"comunidad/datos-personales-procesar",
            type:"post",
            async:false,
            data:{
                seccion:"check-nombreUsuario-existe",
                nombreUsuario:function(){return $("#nombreUsuario").val();}
            },
            success:function(data){
                //si el usuario existe tira el cartel
                if(data == '1'){result = false;}
            }
        });
    }
    return result;
});

var validateFormCrearUsuario = {
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
        tipoDocumento:{required:true},
        numeroDocumento:{required:true, digits:true},
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true, email:true, mailDb:true},
        nombreUsuario:{required:true, minlength:4, nombreUsuarioDb:true},
        contrasenia:{required:true, minlength:5},
        reContrasenia:{required:function(element){
                            return $("#contrasenia").val() != "";
                       }, equalTo:'#contrasenia'},
        sexo:{required:true},
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true}        
    },
    messages:{
        tipoDocumento:"Debe especificar tipo de documento",
        numeroDocumento:{required: "Debe ingresar su numero de documento",
                      digits:mensajeValidacion("digitos")
                      },
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email:{
                required: mensajeValidacion("requerido"),
                email: mensajeValidacion("email"),
                mailDb: mensajeValidacion("email2")
        },
        nombreUsuario:{
                required: mensajeValidacion("requerido"),
                minlength: mensajeValidacion("minlength", '4'),
                nombreUsuarioDb: "El nombre de usuario ya existe"
        },
        contrasenia:{
                required: mensajeValidacion("requerido"),
                minlength: mensajeValidacion("minlength", '5')
        },
        reContrasenia:{
                required: mensajeValidacion("requerido"),
                equalTo: mensajeValidacion("iguales")
        },
        sexo: mensajeValidacion("requerido"),
        fechaNacimientoDia:{
                required: mensajeValidacion("requerido", 'día'),
                digits: mensajeValidacion("digitos")
        },
        fechaNacimientoMes:{
                required: mensajeValidacion("requerido", 'mes'),
                digits: mensajeValidacion("digitos")
        },
        fechaNacimientoAnio:{
                required: mensajeValidacion("requerido", 'año'),
                digits: mensajeValidacion("digitos")
        }        
    }
};

var optionsAjaxFormCrearUsuario = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/usuarios-crear',

    beforeSerialize: function($form, options){
        if($("#formCrearUsuario").valid() == true){

            hashPassword("contrasenia", "contraseniaMD5");
            $("#contrasenia").val("");
            
            $('#msg_form_crearUsuario').hide();
            $('#msg_form_crearUsuario').removeClass("success").removeClass("error2");
            $('#msg_form_crearUsuario .msg').html("");
            setWaitingStatus('formCrearUsuario', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCrearUsuario', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_crearUsuario .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_crearUsuario .msg').html(data.mensaje);
            }
            $('#msg_form_crearUsuario').addClass("error2").fadeIn('slow');
        }else{            
            if(data.mensaje == undefined){
                $('#msg_form_crearUsuario .msg').html("El usuario fue agregado exitosamente al sistema");
            }else{
                $('#msg_form_crearUsuario .msg').html(data.mensaje);
            }
            $('#msg_form_crearUsuario').addClass("success").fadeIn('slow');
        }
    }
};

$(document).ready(function(){
    
    $("a[rel^='prettyPhoto']").prettyPhoto();

    $(".cerrarCuentaUsuario").live('click', function(){
        var iUsuarioId = $(this).attr("rel");
        cerrarCuentaUsuario(iUsuarioId);
    });

    $(".cambiarEstadoUsuario").live("change", function(){
        var iUsuarioId = $(this).attr("rel");
        cambiarEstadoUsuario(iUsuarioId, $("#estadoUsuario_"+iUsuarioId+" option:selected").val());
    });

    $("#fotoPerfilBorrar").live('click', function(){
        var iUsuarioId = $(this).attr("rel");
        borrarFotoPerfil(iUsuarioId);
        return false; //porq es un <a>
    });

    $(".verFichaUsuario").live('click',function(){
        
        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $("<div id='dialog' title='"+$(this).html()+"'></div>").appendTo('body');

        dialog.load(
            "admin/usuarios-procesar?ver=1&iUsuarioId="+$(this).attr('rel'),
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
                bindEventsAdmin();
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });

    $("#formCrearUsuario").validate(validateFormCrearUsuario);
    $("#formCrearUsuario").ajaxForm(optionsAjaxFormCrearUsuario);
});