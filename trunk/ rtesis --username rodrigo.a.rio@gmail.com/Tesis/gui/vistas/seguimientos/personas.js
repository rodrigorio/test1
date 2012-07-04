//Existe un discapacitado con el numero de documento ingresado? 
jQuery.validator.addMethod("existeNumeroDocumento", function(value, element){
    var result = true;
    if($("#nroDocumento").val() != ""){
        $.ajax({
            url:"seguimientos/personas-procesar",
            type:"post",
            async:false,
            data:{
                checkNumeroDocumento:"1",
                numeroDocumento:function(){return $("#nroDocumento").val();},
                //porque si es modificar te tiene que dejar guardar el numero que ya estaba
                personaId:function(){
                    //porque sino devuelve undefined
                    if($('#personaIdForm').length){
                        return $("#personaIdForm").val();
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

/*
 * En este caso el boton del upload lo creo despues de que se crea satisfactoriamente la persona.
 */
function uploader(idPersona){
    if($('#fotoUpload').length){
        new Ajax_upload('#fotoUpload', {
            action: 'seguimientos/personas-procesar',
            data:{
                fotoUpload:'1',
                personaIdFoto:idPersona
            },
            name: 'fotoPerfil',
            onSubmit:function(file , ext){
                $('#msg_form_fotoPerfil').hide();
                $('#msg_form_fotoPerfil').removeClass("correcto").removeClass("error");
                $('#msg_form_fotoPerfil .msg').html("");
                setWaitingStatus('tabsFormPersona', true);
                this.disable(); //solo un archivo a la vez
            },
            onComplete:function(file, response){
                setWaitingStatus('tabsFormPersona', false);
                this.enable();

                if(response == undefined){
                    $('#msg_form_fotoPerfil .msg').html(lang['error procesar']);
                    $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
                    return;
                }

                var dataInfo = response.split(';');
                var resultado = dataInfo[0]; //0 = error, 1 = actualizacion satisfactoria, 2 = satisfactorio, pendiente de moderacion
                var html = dataInfo[1]; //aca queda el bloque del html que acompaña el resultado

                if(resultado != "0" && resultado != "1" && resultado != "2"){
                    $('#msg_form_fotoPerfil .msg').html(lang['error permiso']);
                    $('#msg_form_fotoPerfil').addClass("info").fadeIn('slow');
                    return;
                }

                if(resultado == '0'){
                    $('#msg_form_fotoPerfil .msg').html(html);
                    $('#msg_form_fotoPerfil').addClass("error").fadeIn('slow');
                }else{
                    if(resultado == '1'){
                        $('#msg_form_fotoPerfil .msg').html(lang['exito procesar archivo']);
                        $('#contFotoPerfilActual').html(html);
                        $("a[rel^='prettyPhoto']").prettyPhoto(); //asocio el evento al html nuevo
                        $('#msg_form_fotoPerfil').addClass("correcto").fadeIn('slow');
                    }else{
                        $('#msg_form_fotoPerfil .msg').html(html);
                        $('#msg_form_fotoPerfil').addClass("correcto").fadeIn('slow');
                    }
                }
                return;
            }
        });
    }
}

//validacion y submit
var validateFormPersona = {
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
        sexo:{required:true},
        nombre:{required:true},
        apellido:{required:true},
        fechaNacimientoDia:{required:true, digits: true},
        fechaNacimientoMes:{required:true, digits: true},
        fechaNacimientoAnio:{required:true, digits: true},
        pais:{required:true, digits: true},
        provincia:{required:function(element){
                            return $("#pais option:selected").val() != "";
                  }, digits: true},
        ciudad:{required:function(element){
                            return $("#provincia option:selected").val() != "";
               }, digits: true},
        tipoDocumento:{required:true},
        nroDocumento:{required:true, ignorarDefault:true, digits:true, maxlength:8, existeNumeroDocumento:true},
        telefono:{required:true}
    },
    messages:{
        tipoDocumento: "Debe especificar tipo de documento",
        nroDocumento:{
                        required: "Debe ingresar numero de documento",
                        ignorarDefault: "Debe ingresar numero de documento",
                        digits: mensajeValidacion("digitos"),
                        maxlength:mensajeValidacion("maxlength", '8'),
                        existeNumeroDocumento: "El numero de documento ya existe para una persona cargada en el sistema."
                      },
        sexo: mensajeValidacion("requerido"),
        nombre: mensajeValidacion("requerido"),
        apellido: mensajeValidacion("requerido"),
        telefono: mensajeValidacion("requerido"),
        fechaNacimientoDia:{
                            required: mensajeValidacion("requerido2", 'día'),
                            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoMes:{
                            required: mensajeValidacion("requerido2", 'mes'),
                            digits: mensajeValidacion("digitos")
        },
        fechaNacimientoAnio:{
                            required: mensajeValidacion("requerido2", 'año'),
                            digits: mensajeValidacion("digitos")
        },
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
        }
    }
};

var optionsAjaxFormPersona = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'seguimientos/personas-procesar',

    beforeSerialize: function($form, options){
        if($("#formPersona").valid() == true){
            $('#msg_form_persona').hide();
            $('#msg_form_persona').removeClass("correcto").removeClass("error");
            $('#msg_form_persona .msg').html("");
            verificarValorDefecto("ocupacionPadre");
            verificarValorDefecto("ocupacionMadre");
            verificarValorDefecto("nombreHermanos");
            setWaitingStatus('tabsFormPersona', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('tabsFormPersona', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_persona .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_persona .msg').html(data.mensaje);
            }
            $('#msg_form_persona').addClass("error").fadeIn('slow');
        }else{
            if(data.agregarPersona != undefined){
                //si el submit fue para agregar una nueva persona al sistema...
                $('#msg_form_fotoPerfil .msg').html("La persona ha sido creada exitosamente. Puede agregar una foto de perfil si lo desea.");
                $('#msg_form_fotoPerfil').addClass("correcto").show();
                $('#tabFormPersonaContent').html("");
                $('#tabFotoPerfil').click();
                $('#tabFormPersona').addClass("disabled");
                uploader(data.personaId);
            }else{
                //el submit fue la modificacion de una persona
                $('#msg_form_persona .msg').html(data.mensaje);
                $('#msg_form_persona').addClass("correcto").fadeIn('slow');
            }
        }
    }
};

function bindEventsPersonaForm(){
   
    //si entra al form de foto por 'modificar' entonces creo el boton uploader de entrada
    var personaIdFoto = $("#personaIdFoto").val();
    if(personaIdFoto != undefined && personaIdFoto != ""){
        uploader(personaIdFoto);
    }

    $("#tabsFormPersona").tabs();

    $("textarea.maxlength").maxlength();

    $("#toggleContInfoExtra").click(function(){
        if($(this).attr("rel") == "open"){
            revelarElemento($("#contInfoExtra"));
            $(this).attr("rel", "close");
        }else{
            ocultarElemento($("#contInfoExtra"));
            $(this).attr("rel", "open");
        }
        return false;
    });

    if($("#institucionId").val() != ""){
        $("#institucion").addClass("selected");
        $("#institucion").attr("readonly", "readonly");
        revelarElemento($('#institucion_clean'));
    }

    $('#institucion').blur(function(){
        if($("#institucionId").val() == ""){
            $("#institucion").val("");
        }
        if($("#institucion").val() == ""){
            $("#institucionId").val("");
        }
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
            }
        }
    });

    //pais provincia ciudad
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
            url: "provinciasByPais",
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
            url: "ciudadesByProvincia",
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

    //para borrar la institucion seleccionada con el autocomplete
    $('#institucion_clean').click(function(){
        $("#institucion").removeClass("selected");
        $("#institucion").removeAttr("readonly");
        $("#institucion").val("");
        $("#institucionId").val("");
        ocultarElemento($(this));
    });
    
    $("#formPersona").validate(validateFormPersona);
    $("#formPersona").ajaxForm(optionsAjaxFormPersona);
}

function bindEventsPersonaVerFicha()
{
    $("#modificarPersona").click(function(){

        $.getScript(pathUrlBase+"utilidades/jquery/ajaxupload.3.6.js");

        var dialog = $("#dialog");
        if ($("#dialog").length != 0){
            dialog.hide("slow");
            dialog.remove();
        }
        dialog = $('<div id="dialog" title="Modificar Persona"></div>').appendTo('body');

        dialog.load(
            "seguimientos/modificar-persona?popUp=1&personaId="+$(this).attr('rel'),
            {},
            function(responseText, textStatus, XMLHttpRequest){
                dialog.dialog({
                    position:['center', '20'],
                    width:650,
                    resizable:false,
                    draggable:true,
                    modal:false,
                    closeOnEscape:true
                });
                bindEventsPersonaForm();
                $("a[rel^='prettyPhoto']").prettyPhoto();
            }
        );
        return false;
    });
}