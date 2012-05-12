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
                if($("#dialog").length){
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

//para pasar de activo a suspendido con el select del listado
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

//para cambiar el tipo de perfil de un usuario de la comunidad
function cambiarPerfilUsuario(iUsuarioId, valor){    
    $.ajax({
        type: "POST",
        url: "admin/usuarios-cambiar-perfil",
        data:{
            iUsuarioId:iUsuarioId,
            perfil:valor
        },
        beforeSend: function(){
            setWaitingStatus('panelAdminFormBlock', true);
        },
        success:function(data){
            setWaitingStatus('panelAdminFormBlock', false);
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

/////////////////////////
// METODOS PARA LAS VALIDACIONES DE FORMULARIOS
/////////////////////////

//Existe un usuario con el numero de documento ingresado?
jQuery.validator.addMethod("mailDb", function(value, element){
    var result = true;
    if($("#email").val() != ""){
        $.ajax({
            url:"admin/usuarios-procesar",
            type:"post",
            async:false,
            data:{
                checkMailExiste:"1",
                email:function(){return $("#email").val();},
                //porque si es modificar te tiene que dejar guardar el mail que ya estaba
                iUsuarioId:function(){
                    //porque sino devuelve undefined
                    if($('#iUsuarioId').length){
                        return $("#iUsuarioId").val();
                    }else{
                        return "";
                    }
                }
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

//Existe un usuario con el numero de documento ingresado?
jQuery.validator.addMethod("existeNumeroDocumento", function(value, element){
    var result = true;
    if($("#nroDocumento").val() != ""){
        $.ajax({
            url:"admin/usuarios-procesar",
            type:"post",
            async:false,
            data:{
                checkNumeroDocumento:"1",
                numeroDocumento:function(){return $("#nroDocumento").val();},
                //porque si es modificar te tiene que dejar guardar el numero que ya estaba
                iUsuarioId:function(){
                    //porque sino devuelve undefined
                    if($('#iUsuarioId').length){
                        return $("#iUsuarioId").val();
                    }else{
                        return "";
                    }
                }
            },
            success:function(data){                
                if(data == '1'){result = false;}
            }
        });
    }
    return result;
});

/////////////////////////
// FORM CREAR USUARIO
/////////////////////////

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
        nroDocumento:{required:true, digits:true, maxlength:8, existeNumeroDocumento:true},
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
        nroDocumento:{required: "Debe ingresar su numero de documento",
                      digits:mensajeValidacion("digitos"),
                      maxlength:mensajeValidacion("maxlength", '8'),
                      existeNumeroDocumento:"El numero de documento ya existe para una persona cargada en el sistema."
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
    resetForm: true,
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

/////////////////////////
// MODIFICAR USUARIO FORM INFO BASICA
/////////////////////////

var validateFormInfoBasica = {
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
        nroDocumento:{required:true, digits:true, maxlength:8, existeNumeroDocumento:true},
        nombre:{required:true},
        apellido:{required:true},
        email:{required:true, email:true, mailDb:true},
        contraseniaNueva:{required:function(element){
                            return $("#contraseniaConfirmar").val() != "";
                         }, minlength:5},
        contraseniaConfirmar:{required:function(element){
                                return $("#contraseniaNueva").val() != "";
                              }, equalTo:'#contraseniaNueva'},
        sexo:{required:true},
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true}
    },
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
                        required: "Debe ingresar su numero de documento",                        
                        digits: mensajeValidacion("digitos"),
                        maxlength: mensajeValidacion("maxlength", '8'),
                        existeNumeroDocumento: "El numero de documento ya existe para una persona cargada en el sistema."
                      },
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        email:{
                required: mensajeValidacion("requerido"),
                email: mensajeValidacion("email"),
                mailDb: mensajeValidacion("email2")
        },
        contraseniaNueva:{
                            required: mensajeValidacion("requerido"),
                            minlength: mensajeValidacion("minlength", '5')
        },
        contraseniaConfirmar:{
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

var optionsAjaxFormInfoBasica = {
    dataType: 'jsonp',
    resetForm: false,
    url: "admin/usuarios-procesar",
    beforeSerialize: function($form, options){
        
        if($("#formInfoBasica").valid() == true){            
            
            $('#msg_form_usuario').hide();
            $('#msg_form_usuario').removeClass("success").removeClass("error2");
            $('#msg_form_usuario .msg').html("");

            //si ingreso contrasenia nueva la convierto y limpio los campos
            if($("#contraseniaNueva").val() != ""){
                hashPassword("contraseniaNueva", "contraseniaNuevaMD5");
                $("#contraseniaNueva").val("");
                $("#contraseniaConfirmar").val("");
            }

            setWaitingStatus('formInfoBasica', true);
        }else{
            return false;
        }
    },
    success:function(data){
        setWaitingStatus('formInfoBasica', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_usuario .msg').html(lang['error procesar']);
            $('#msg_form_usuario').addClass("error2").fadeIn('slow');
        }else{
            $('#msg_form_usuario .msg').html(lang['exito procesar']);
            $('#msg_form_usuario').addClass("success").fadeIn('slow');
        }
    }
};

/////////////////////////
// MODIFICAR USUARIO FORM INFO CONTACTO
/////////////////////////

function listaProvinciasByPais(idPais){
    //si el valor elegido es '' entonces marco como disabled
    if(idPais == ''){
        $('#provincia').addClass("disabled");
    }else{
        $('#provincia').removeClass("disabled");
    }
    $('#ciudad').addClass("disabled");

    $.ajax({
        type: "POST",
        url: "comunidad/provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#provincia').html("");
            //dejo vacio el de ciudad si cambio de pais hasta que elija una provincia
            $('#ciudad').html("");
            $('#ciudad').html(new Option('Elija Ciudad:', '',true));
            if(lista.length != undefined && lista.length > 0){
                $('#provincia').append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#provincia').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#provincia').html(new Option('Elija Provincia:', '',true));
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia){
    if(idProvincia == ''){
        $('#ciudad').addClass("disabled");
    }else{
        $('#ciudad').removeClass("disabled");
    }
    $.ajax({
        type: "POST",
        url: "comunidad/ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus('selectsUbicacion', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#ciudad').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#ciudad').append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ciudad').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#ciudad').append(new Option('Elija Ciudad:', '',true));
            }
            setWaitingStatus('selectsUbicacion', false);
        }
    });
}

$("#pais").change(function(){listaProvinciasByPais($("#pais option:selected").val());});
$("#provincia").change(function(){listaCiudadesByProvincia($("#provincia option:selected").val());});

var validateFormInfoContacto = {
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
        pais:{required:true, digits: true},
        provincia:{required:function(element){
                            return $("#pais option:selected").val() != "";
                  }, digits: true},
        ciudad:{required:function(element){
                            return $("#provincia option:selected").val() != "";
               }, digits: true},
        codigoPostal:{required:true},
        direccion:{required:true},
        telefono:{required:true}
    },
    messages:{
        pais:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")
        },
        provincia:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")
        },
        ciudad:{
            required: mensajeValidacion("requerido"),
            digits: mensajeValidacion("digitos")
        },
        codigoPostal: mensajeValidacion("requerido"),
        direccion: mensajeValidacion("requerido"),
        telefono: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormInfoContacto = {
    dataType: 'jsonp',
    resetForm: false,
    url: "admin/usuarios-procesar",
    beforeSerialize: function($form, options){
        if($("#formInfoContacto").valid() == true){
            $('#msg_form_usuario').hide();
            $('#msg_form_usuario').removeClass("success").removeClass("error2");
            $('#msg_form_usuario .msg').html("");
            setWaitingStatus('formInfoContacto', true);
        }else{
            return false;
        }
    },
    success:function(data){
        setWaitingStatus('formInfoContacto', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_usuario .msg').html(lang['error procesar']);
            $('#msg_form_usuario').addClass("error2").fadeIn('slow');
        }else{
            $('#msg_form_usuario .msg').html(lang['exito procesar']);
            $('#msg_form_usuario').addClass("success").fadeIn('slow');
        }
    }    
}

/////////////////////////
// MODIFICAR USUARIO FORM INFO PROFESIONAL
/////////////////////////

//para el estado inicial del formulario
$(document).ready(function(){
    if($("#institucionId").val() == ""){
        $('#contCargoInstitucion').addClass("disabled");
        $('#cargoInstitucion').attr('readonly', 'readonly');
    }else{
        $("#institucion").addClass("selected");
        $("#institucion").attr("readonly", "readonly");
        revelarElemento($('#institucion_clean'));
    }

    if($("#universidad").val() == ""){
        $('#universidadInfo').addClass("disabled");
        $('#universidadCarrera').attr('readonly', 'readonly');
        $('#carreraFinalizada').attr('readonly', 'readonly');
    }
});

$('#institucion').blur(function(){
    if($("#institucionId").val() == ""){
        $("#institucion").val("");
    }
    if($("#institucion").val() == ""){
        $("#institucionId").val("");
        $('#contCargoInstitucion').addClass("disabled");
        $('#cargoInstitucion').val("");
        $('#cargoInstitucion').attr('readonly', 'readonly');
    }
});

$('#universidad').blur(function(){
    if($("#universidad").val() == ""){
        $("#universidadCarrera").val("");
        $("#carreraFinalizada").val("");
        $('#universidadInfo').addClass("disabled");
        $('#universidadCarrera').attr('readonly', 'readonly');
        $('#carreraFinalizada').attr('readonly', 'readonly');
    }
});

$('#universidad').focus(function(){
    $('#universidadInfo').removeClass("disabled");
    $('#universidadCarrera').removeAttr('readonly');
    $('#carreraFinalizada').removeAttr('readonly');
});

$("#institucion").autocomplete({
    source:function(request, response){
        $.ajax({
            url: "comunidad/buscar-instituciones",
            dataType: "jsonp",
            data:{
                limit:12,
                str:request.term
            },
            beforeSend: function(){
                revelarElemento($("#institucion_loading"));
            },
            success: function(data){
                ocultarElemento($("#institucion_loading"));
                response( $.map(data.instituciones, function(institucion){
                    return{
                        //lo que aparece en el input
                        value:institucion.nombre,
                        //lo que aparece en la lista generada para elegir
                        label:institucion.nombre,
                        //valor extra que se devuelve para completar el hidden
                        id:institucion.id
                    }
                }));
            }
        });
    },
    minLength: 1,
    select: function(event, ui){
        if(ui.item){
            $("#institucionId").val(ui.item.id);
        }else{
            $("#institucionId").val("");
        }
    },
    close: function(){
        if($("#institucionId").val() != ""){
            $(this).addClass("selected");
            $(this).attr('readonly', 'readonly');
            revelarElemento($('#institucion_clean'));
            $('#contCargoInstitucion').removeClass("disabled");
            $('#cargoInstitucion').removeAttr('readonly');
        }
    }
});

//para borrar la institucion seleccionada con el autocomplete
$('#institucion_clean').click(function(){
    $("#institucion").removeClass("selected");
    $("#institucion").removeAttr("readonly");
    $("#institucion").val("");
    $("#institucionId").val("");
    $('#contCargoInstitucion').addClass("disabled");
    $('#cargoInstitucion').val("");
    ocultarElemento($(this));
});

var validateFormInfoProfesional = {
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
        cargoInstitucion:{required:true},
        secundaria:{required:true},
        universidadCarrera:{required:function(element){
                                return $("#universidad").val() != "";
                           }},
        carreraFinalizada:{required:function(element){
                                return $("#universidad").val() != "";
                          }},
        sitioWeb:{url:true},
        especialidad:{required:true}
    },
    messages:{
        cargoInstitucion:mensajeValidacion("requerido"),
        secundaria:mensajeValidacion("requerido"),
        universidadCarrera:mensajeValidacion("requerido"),
        carreraFinalizada:mensajeValidacion("requerido"),
        sitioWeb:mensajeValidacion("url"),
        especialidad:mensajeValidacion("requerido")
    }
};

var optionsAjaxFormInfoProfesional = {
    dataType: 'jsonp',
    resetForm: false,
    url: "admin/usuarios-procesar",

    beforeSerialize: function($form, options){
        if($("#formInfoProfesional").valid() == true){
            $('#msg_form_usuario').hide();
            $('#msg_form_usuario').removeClass("success").removeClass("error2");
            $('#msg_form_usuario .msg').html("");
            setWaitingStatus('formInfoProfesional', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInfoProfesional', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_usuario .msg').html(lang['error procesar']);
            $('#msg_form_usuario').addClass("error2").fadeIn('slow');
        }else{
            $('#msg_form_usuario .msg').html(lang['exito procesar']);
            $('#msg_form_usuario').addClass("success").fadeIn('slow');
        }
    }
};

//////////////////////////////////
// FORM FOTO PERFIL    ///////////
//////////////////////////////////

function uploaderFotoPerfil(iUsuarioId){
    if($('#fotoUpload').length){
        new Ajax_upload('#fotoUpload', {
            action: 'admin/usuarios-procesar',
            data: {
                modificarUsuario:"1",
                fotoPerfil:"1",
                iUsuarioId:iUsuarioId
            },
            name: 'fotoPerfil',
            onSubmit:function(file , ext){
                $('#msg_form_usuario').hide();
                $('#msg_form_usuario').removeClass("success").removeClass("error2");
                $('#msg_form_usuario .msg').html("");
                setWaitingStatus('tabFoto', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('tabFoto', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_usuario .msg').html(lang['error procesar']);
                    $('#msg_form_usuario').addClass("error2").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si se proceso bien aca queda el bloque del html con el nuevo thumbnail

                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_usuario .msg').html(lang['error permiso']);
                    $('#msg_form_usuario').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_usuario .msg').html(html);
                    $('#msg_form_usuario').addClass("error2").fadeIn('slow');
                }else{
                    $('#msg_form_usuario .msg').html(lang['exito procesar archivo']);
                    $('#contFotoPerfilActual').html(html);
                    $("a[rel^='prettyPhoto']").prettyPhoto(); //asocio el evento al html nuevo
                    $('#msg_form_usuario').addClass("success").fadeIn('slow');
                }
                return;
            }
        });
    }
}

//////////////////////////////////
// FORM CURRICULUM VITAE /////////
//////////////////////////////////

function uploaderCurriculumVitae(iUsuarioId){
    if($('#cvUpload').length){        
        new Ajax_upload('#cvUpload',{
            action: 'admin/usuarios-procesar',
            data: {
                modificarUsuario:"1",
                curriculum:"1",
                iUsuarioId:iUsuarioId
            },
            name: 'curriculum',
            onSubmit : function(file , ext){
                $('#msg_form_usuario').hide();
                $('#msg_form_usuario').removeClass("success").removeClass("error2");
                $('#msg_form_usuario .msg').html("");
                setWaitingStatus('tabCurriculum', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete : function(file, response){
                setWaitingStatus('tabCurriculum', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_usuario .msg').html(lang['error procesar']);
                    $('#msg_form_usuario').addClass("error2").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria
                var html = dataInfo[1]; //si es satisfactorio el html devuelve el bloque de descarga
                
                //si rebota por accion desactivada o alguna de esas no tiene el formato de "0; mensaje mensaje mensaje"
                if(resultado != "0" && resultado != "1"){
                    $('#msg_form_usuario .msg').html(lang['error permiso']);
                    $('#msg_form_usuario').addClass("attention").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_usuario .msg').html(html);
                    $('#msg_form_usuario').addClass("error2").fadeIn('slow');
                }else{
                    $('#msg_form_usuario .msg').html(lang['exito procesar archivo']);
                    $('#msg_form_usuario').addClass("success").fadeIn('slow');
                    $('#wrapCvActual').html(html);
                }
                return;
            }
        });
    }
}

/**
 * Para la paginacion de resultados de filtro con ajax en el listado principal.
 */
function buscarUsuarios(){    
    var filtroApellido = $('#filtroApellido').val();
    var filtroNumeroDocumento = $('#filtroNumeroDocumento').val();
    var filtroInstitucion = $('#filtroInstitucion').val();
    var filtroCiudad = $('#filtroCiudad').val();        
    var filtroEspecialidad = $('#filtroEspecialidad option:selected').val();
    var filtroPerfil = $('#filtroPerfil option:selected').val();
    var filtroSuspendido = $('#filtroSuspendido option:selected').val();    
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"admin/usuarios-procesar",
        data:{
            masUsuarios:"1",
            filtroApellido: filtroApellido,
            filtroNumeroDocumento: filtroNumeroDocumento,
            filtroInstitucion: filtroInstitucion,
            filtroCiudad: filtroCiudad,
            filtroEspecialidad: filtroEspecialidad,
            filtroPerfil: filtroPerfil,
            filtroSuspendido: filtroSuspendido,
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoUsuariosResult', true);
        },
        success:function(data){
            setWaitingStatus('listadoUsuariosResult', false);
            $("#listadoUsuariosResult").html(data);
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    });
}

function exportarUsuarios(){
    var filtroApellido = $('#filtroApellido').val();
    var filtroNumeroDocumento = $('#filtroNumeroDocumento').val();
    var filtroInstitucion = $('#filtroInstitucion').val();
    var filtroCiudad = $('#filtroCiudad').val();
    var filtroEspecialidad = $('#filtroEspecialidad option:selected').val();
    var filtroPerfil = $('#filtroPerfil option:selected').val();
    var filtroSuspendido = $('#filtroSuspendido option:selected').val();
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"admin/usuarios-exportar",
        data:{
            filtroApellido: filtroApellido,
            filtroNumeroDocumento: filtroNumeroDocumento,
            filtroInstitucion: filtroInstitucion,
            filtroCiudad: filtroCiudad,
            filtroEspecialidad: filtroEspecialidad,
            filtroPerfil: filtroPerfil,
            filtroSuspendido: filtroSuspendido,
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('subHeader', true);
        },
        success:function(data){
            setWaitingStatus('subHeader', false);
        }
    });
}

$(document).ready(function(){
    
    $("a[rel^='prettyPhoto']").prettyPhoto();

    $("#exportarFiltroActual").live('click', function(){
        exportarUsuarios();
    });

    $("#filtrarUsuarios").live('click', function(){
        buscarUsuarios();
        return false;
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        buscarUsuarios();        
    });
 
    //para limpiar el filtro del listado de usuarios.
    $("#limpiarFiltro").live('click',function(){        
        $('#formFiltrarUsuarios').each(function(){
          this.reset();
        });
        return false;
    });

    $(".cerrarCuentaUsuario").live('click', function(){
        var iUsuarioId = $(this).attr("rel");
        cerrarCuentaUsuario(iUsuarioId);
    });

    $(".cambiarEstadoUsuario").live("change", function(){
        var iUsuarioId = $(this).attr("rel");
        cambiarEstadoUsuario(iUsuarioId, $("#estadoUsuario_"+iUsuarioId+" option:selected").val());
    });

    $("#modificarPerfil").live("change", function(){
        var iUsuarioId = $(this).attr("rel");
        cambiarPerfilUsuario(iUsuarioId, $("#modificarPerfil option:selected").val());
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

    if($("#formCrearUsuario").length){
        $("#formCrearUsuario").validate(validateFormCrearUsuario);
        $("#formCrearUsuario").ajaxForm(optionsAjaxFormCrearUsuario);
    }

    if($("#formInfoBasica").length){
        $("#formInfoBasica").validate(validateFormInfoBasica);
        $("#formInfoBasica").ajaxForm(optionsAjaxFormInfoBasica);
    }

    if($("#formInfoContacto").length){
        $("#formInfoContacto").validate(validateFormInfoContacto);
        $("#formInfoContacto").ajaxForm(optionsAjaxFormInfoContacto);
    }
    
    if($("#formInfoProfesional").length){
        $("#formInfoProfesional").validate(validateFormInfoProfesional);
        $("#formInfoProfesional").ajaxForm(optionsAjaxFormInfoProfesional);
    }

    var iUsuarioId = $("#iUsuarioId").val();
    if(iUsuarioId != undefined && iUsuarioId != ""){
        uploaderFotoPerfil(iUsuarioId);
        uploaderCurriculumVitae(iUsuarioId);
    }
});