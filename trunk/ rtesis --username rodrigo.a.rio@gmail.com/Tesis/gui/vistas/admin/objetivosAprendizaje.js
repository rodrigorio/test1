var validateFormNivel = {
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
        descripcion:{required:true}
    },
    messages:{
        descripcion: mensajeValidacion("requerido")
    }
};

var optionsAjaxFormNivel = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-nivel',
    beforeSerialize:function(){

        if($("#formNivel").valid() == true){
            $('#msg_form_nivel').hide();
            $('#msg_form_nivel').removeClass("success").removeClass("error2");
            $('#msg_form_nivel .msg').html("");
            setWaitingStatus('formNivel', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formNivel', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_nivel .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_nivel .msg').html(data.mensaje);
            }
            $('#msg_form_nivel').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_nivel .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_nivel .msg').html(data.mensaje);
            }
            if(data.accion == 'crearNivel'){
                $('#formNivel').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_nivel').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormCiclo = {
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
        descripcion:{required:true},
        nivel:{required:true}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        nivel :mensajeValidacion("requerido")
    }
};

var optionsAjaxFormCiclo = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-ciclo',
    beforeSerialize:function(){

        if($("#formCiclo").valid() == true){
            $('#msg_form_ciclo').hide();
            $('#msg_form_ciclo').removeClass("success").removeClass("error2");
            $('#msg_form_ciclo .msg').html("");
            setWaitingStatus('formCiclo', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCiclo', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_ciclo .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_ciclo .msg').html(data.mensaje);
            }
            $('#msg_form_ciclo').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_ciclo .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_ciclo .msg').html(data.mensaje);
            }
            if(data.accion == 'crearCiclo'){
                $('#formCiclo').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_ciclo').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormArea = {
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
        descripcion:{required:true},
        nivel:{required:true},
        ciclo:{required:true}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        nivel :mensajeValidacion("requerido"),
        ciclo :mensajeValidacion("requerido")
    }
};

var optionsAjaxFormArea = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-area',
    beforeSerialize:function(){

        if($("#formArea").valid() == true){
            $('#msg_form_area').hide();
            $('#msg_form_area').removeClass("success").removeClass("error2");
            $('#msg_form_area .msg').html("");
            setWaitingStatus('formArea', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formArea', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_area .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_area .msg').html(data.mensaje);
            }
            $('#msg_form_area').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_area .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_area .msg').html(data.mensaje);
            }
            if(data.accion == 'crearArea'){
                $('#formArea').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_area').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormEje = {
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
        descripcion:{required:true},
        nivel:{required:true},
        ciclo:{required:true},
        area:{required:true},
        contenidos:{required:true}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        nivel :mensajeValidacion("requerido"),
        ciclo :mensajeValidacion("requerido"),
        area :mensajeValidacion("requerido"),
        contenidos :mensajeValidacion("requerido")
    }
};

var optionsAjaxFormEje = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-eje',
    beforeSerialize:function(){

        if($("#formEje").valid() == true){
            $('#msg_form_eje').hide();
            $('#msg_form_eje').removeClass("success").removeClass("error2");
            $('#msg_form_eje .msg').html("");
            setWaitingStatus('formEje', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formEje', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_eje .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_eje .msg').html(data.mensaje);
            }
            $('#msg_form_eje').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_eje .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_eje .msg').html(data.mensaje);
            }
            if(data.accion == 'crearEje'){
                $('#formEje').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_eje').addClass("success").fadeIn('slow');
        }
    }
};

var validateFormObjetivoAprendizaje = {
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
        descripcion:{required:true},
        nivel:{required:true},
        ciclo:{required:true},
        area:{required:true},
        ejeTematico:{required:true}
    },
    messages:{
        descripcion: mensajeValidacion("requerido"),
        nivel :mensajeValidacion("requerido"),
        ciclo :mensajeValidacion("requerido"),
        area :mensajeValidacion("requerido"),
        ejeTematico :mensajeValidacion("requerido")
    }
};

var optionsAjaxFormObjetivoAprendizaje = {
    dataType: 'jsonp',
    resetForm: false,
    url: 'admin/procesar-objetivo-aprendizaje',
    beforeSerialize:function(){

        if($("#formObjetivoAprendizaje").valid() == true){
            $('#msg_form_objetivoAprendizaje').hide();
            $('#msg_form_objetivoAprendizaje').removeClass("success").removeClass("error2");
            $('#msg_form_objetivoAprendizaje .msg').html("");
            setWaitingStatus('formObjetivoAprendizaje', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formObjetivoAprendizaje', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_objetivoAprendizaje .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_objetivoAprendizaje .msg').html(data.mensaje);
            }
            $('#msg_form_objetivoAprendizaje').addClass("error2").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_objetivoAprendizaje .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_objetivoAprendizaje .msg').html(data.mensaje);
            }
            if(data.accion == 'crearObjetivoAprendizaje'){
                $('#formObjetivoAprendizaje').each(function(){
                  this.reset();
                });
            }
            $('#msg_form_objetivoAprendizaje').addClass("success").fadeIn('slow');
        }
    }
};

function bindEventsFormNivel(){
    $("#formNivel").validate(validateFormNivel);
    $("#formNivel").ajaxForm(optionsAjaxFormNivel);
}

function bindEventsFormCiclo(){
    $("#formCiclo").validate(validateFormCiclo);
    $("#formCiclo").ajaxForm(optionsAjaxFormCiclo);
}

function bindEventsFormArea(){
    $("#formArea").validate(validateFormArea);
    $("#formArea").ajaxForm(optionsAjaxFormArea);
}

function bindEventsFormEje(){
    $("#formEje").validate(validateFormEje);
    $("#formEje").ajaxForm(optionsAjaxFormEje);
}

function bindEventsFormObjetivoAprendizaje(){
    $("#formObjetivoAprendizaje").validate(validateFormObjetivoAprendizaje);
    $("#formObjetivoAprendizaje").ajaxForm(optionsAjaxFormObjetivoAprendizaje);
}

function borrarNivel(iNivelId){
    if(confirm("Se borrara el nivel del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/procesar-nivel",
            data:{
                iNivelId:iNivelId,
                borrarNivel:'1'
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iNivelId).hide("slow", function(){
                        $("."+iNivelId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Nivel");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Nivel"></div>').appendTo('body');
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

function borrarCiclo(iCicloId){
    if(confirm("Se borrara el ciclo del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/procesar-ciclo",
            data:{
                iCicloId:iCicloId,
                borrarCiclo:'1'
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iCicloId).hide("slow", function(){
                        $("."+iCicloId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Ciclo");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Ciclo"></div>').appendTo('body');
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

function borrarArea(iAreaId){
    if(confirm("Se borrara el área del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/procesar-area",
            data:{
                iAreaId:iAreaId,
                borrarArea:'1'
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iAreaId).hide("slow", function(){
                        $("."+iAreaId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Área");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Área"></div>').appendTo('body');
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

function borrarEje(iEjeId){
    if(confirm("Se borrara el Eje Temático del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/procesar-eje",
            data:{
                iEjeId:iEjeId,
                borrarEje:'1'
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iEjeId).hide("slow", function(){
                        $("."+iEjeId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Eje Temático");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Eje Temático"></div>').appendTo('body');
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

function borrarObjetivoAprendizaje(iObjetivoAprendizajeId){
    if(confirm("Se borrara el Objetivo de Aprendizaje del sistema, desea continuar?")){
        $.ajax({
            type:"post",
            dataType:'jsonp',
            url:"admin/procesar-objetivo-aprendizaje",
            data:{
                iObjetivoAprendizajeId:iObjetivoAprendizajeId,
                borrarObjetivoAprendizaje:'1'
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iObjetivoAprendizajeId).hide("slow", function(){
                        $("."+iObjetivoAprendizajeId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length != 0){
                    dialog.attr("title","Eliminar Objetivo de Aprendizaje");
                }else{
                    dialog = $('<div id="dialog" title="Eliminar Objetivo de Aprendizaje"></div>').appendTo('body');
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

//combos con ajax formularios
function listaCiclosByNivel(idNivel, formId){

    //si el valor elegido es '' entonces marco como disabled
    if(idNivel == ''){
        $('#ciclo').addClass("disabled");
    }else{
        $('#ciclo').removeClass("disabled");
    }

    if($("#area").length){
        $('#area').addClass("disabled");
    }
    if($("#ejeTematico").length){
        $('#ejeTematico').addClass("disabled");
    }
    
    $.ajax({
        type: "POST",
        url: "admin/procesar-nivel",
        data:{iNivelId:idNivel, ciclosByNivel:"1"},
        beforeSend: function(){
            setWaitingStatus(formId, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#ciclo').html("");

            //los demas van vacios si es que estan en el formulario, se completan a medida que se seleccionan
            if($("#area").length){
                $('#area').html("");
                $('#area').html(new Option('Elija Área:', '',true));
            }
            if($("#ejeTematico").length){
                $('#ejeTematico').html("");
                $('#ejeTematico').html(new Option('Elija Eje Temático:', '',true));
            }

            if(lista.length != undefined && lista.length > 0){
                $('#ciclo').append(new Option('Elija Ciclo:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ciclo').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#ciclo').html(new Option('Elija Ciclo:', '',true));
            }
            setWaitingStatus(formId, false);
        }
    });
 }

function listaAreasByCiclo(idCiclo, formId){
    if(idCiclo == ''){
        $('#area').addClass("disabled");
    }else{
        $('#area').removeClass("disabled");
    }

    if($("#ejeTematico").length){
        $('#ejeTematico').addClass("disabled");
    }
    
    $.ajax({
        type: "POST",
        url: "admin/procesar-ciclo",
        data:{iCicloId:idCiclo, areasByCiclo:"1"},
        beforeSend: function(){
            setWaitingStatus(formId, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#area').html("");

            if($("#ejeTematico").length){
                $('#ejeTematico').html("");
                $('#ejeTematico').html(new Option('Elija Eje Temático:', '',true));
            }

            if(lista.length != undefined && lista.length > 0){
                $('#area').append(new Option('Elija Área:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#area').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#area').append(new Option('Elija Área:', '',true));
            }
            setWaitingStatus(formId, false);
        }
    });
}

function listaEjesTematicosByArea(idArea, formId){
    if(idArea == ''){
        $('#ejeTematico').addClass("disabled");
    }else{
        $('#ejeTematico').removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "admin/procesar-area",
        data:{iAreaId:idArea, ejesByArea:"1"},
        beforeSend: function(){
            setWaitingStatus(formId, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#ejeTematico').html("");

            if(lista.length != undefined && lista.length > 0){
                $('#ejeTematico').append(new Option('Elija Eje Temático:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#ejeTematico').append(new Option(lista[i].sDescripcion, lista[i].iId));
                }
            }else{
                $('#area').append(new Option('Elija Eje Temático:', '',true));
            }
            setWaitingStatus(formId, false);
        }
    });
}

$(document).ready(function(){

    if($("#formNivel").length){
        bindEventsFormNivel();
    }

    if($("#formCiclo").length){
        bindEventsFormCiclo();
    }

    if($("#formArea").length){
        bindEventsFormArea();
    }

    if($("#formEje").length){
        bindEventsFormEje();
    }

    if($("#formObjetivoAprendizaje").length){
        bindEventsFormObjetivoAprendizaje();
    }
    
    $(".borrarNivel").live('click', function(){
        var iNivelId = $(this).attr("rel");
        borrarNivel(iNivelId);
    });

    $(".borrarCiclo").live('click', function(){
        var iCicloId = $(this).attr("rel");
        borrarCiclo(iCicloId);
    });

    $(".borrarArea").live('click', function(){
        var iAreaId = $(this).attr("rel");
        borrarArea(iAreaId);
    });

    $(".borrarEje").live('click', function(){
        var iEjeId = $(this).attr("rel");
        borrarEje(iEjeId);
    });

    $(".borrarObjetivoAprendizaje").live('click', function(){
        var iObjetivoAprendizajeId = $(this).attr("rel");
        borrarObjetivoAprendizaje(iObjetivoAprendizajeId);
    });
});