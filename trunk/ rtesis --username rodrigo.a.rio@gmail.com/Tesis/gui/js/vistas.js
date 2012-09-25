/*
 * JAVASCRIPT CON VARIABLES Y FUNCIONES GLOBALES A UTILIZAR EN TODAS LAS VISTAS
 */



//////////////////////////////////////////////////////////
//////////////VARIABLES Y FUNCIONES GLOBALES//////////////
//////////////////////////////////////////////////////////

//Clases para asignar a los mensajes y determinar tipo
var MSG_ERROR = "error";
var MSG_CORRECTO = "correcto";
var MSG_INFO = "info";
var MSG_HINT = "hint";

/* mensajes globales disponibles para utilizar en funciones ajax, etc */
if(!lang)
var lang = Array();
lang['error procesar'] = 'Error al intentar procesar lo solicitado';
lang['error permiso'] = 'La accion se encuentra desactivada o no tienes permiso para realizarla';
lang['exito procesar'] = 'Los datos se han procesado con éxito';
lang['exito procesar archivo'] = 'El archivo se logro subir con exito';
lang['error procesar archivo'] = 'Hubo un error al tratar de procesar el archivo';

/* util para cuando el javascript cambia dependiendo el navegador */
var clientPC = navigator.userAgent.toLowerCase();
var clientVer = parseInt(navigator.appVersion);
var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1) && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1) && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);
var is_moz = 0;

// VALIDATOR //

/**
 * Textos genericos para mensajes de validacion de campos en formularios.
 * Actuan en conjunto con el plugin validator de jQuery
 */
function mensajeValidacion(template, value)
{
    var mensajes = new Array();
    mensajes['requerido'] = "Este campo es obligatorio";
    mensajes['requerido2'] = "Debe especificar un {0}";
    mensajes['email'] = "Dirección de correo inválida";
    mensajes['email2'] = "La cuenta ya se encuentra registrada en el sistema";
    mensajes['url'] = "La URL es inválida o innaccesible.<br>Recuerde ingresar la url completa de la forma 'http://www.ejemplo.com'";
    mensajes['fecha'] = "La fecha no es válida";
    mensajes['fechaISO'] = "La fecha no es (ISO) válida";
    mensajes['numerico'] = "Se requiere número entero válido";
    mensajes['positivo'] = "Solo números positivos";
    mensajes['digitos'] = "Unicamente digitos";
    mensajes['iguales'] = "Escribe el mismo valor de nuevo";
    mensajes['extension'] = "No es una extensión aceptada";
    mensajes['simple'] = "La contraseña es muy simple";
    mensajes['maxlength'] = "No más de {0} caracteres";
    mensajes['minlength'] = "No menos de {0} caracteres";
    mensajes['max'] = "Requiere valor menor o igual a {0}";
    mensajes['min'] = "Requiere valor mayor o igual a {0}";

    if (value == null) {
        return mensajes[template];
    } else {
        return jQuery.validator.format(mensajes[template], value);
    }
}

/**
 * Este es un metodo de validacion que se tiene que llamar en inputs y textarea que tengan valor por defecto.
 * Se debe agregar en las 'rules' del 'validate()' para que no tenga en cuenta el valor por defecto.
 *
 * Ejemplo: rules:{ nombreUsuario: "required ignorarDefault" }
 */
if(jQuery.validator != null){
    jQuery.validator.addMethod("ignorarDefault", function(value, element){
        return element.title != value;
    });
}

// DATEPICKER EN ESP //

$.datepicker.regional['es'] = {clearText: 'Limpiar', clearStatus: '',
    closeText: 'Cerrar', closeStatus: '',
    prevText: '&lt;Ant', prevStatus: '',
    nextText: 'Sig&gt;', nextStatus: '',
    currentText: 'Hoy', currentStatus: '',
    monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
    'Jul','Ago','Sep','Oct','Nov','Dic'],
    monthStatus: '', yearStatus: '',
    weekHeader: 'Sm', weekStatus: '',
    dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;dabo'],
    dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
    dayStatus: 'DD', dateStatus: 'D, M d',
    dateFormat: 'dd/mm/yy', firstDay: 0,
    initStatus: '', isRTL: false};
$.datepicker.setDefaults($.datepicker.regional['es']);

// FUNCIONES PARA MANIPULAR VISTAS //

function valueToggleFocus(elemento){
    if(elemento.attr("title") == elemento.val()){
        elemento.val("")
                .addClass("co");
    }
}

function valueToggleBlur(elemento, textarea){
    if(elemento.val() == ""){

        /*contar caracteres*/
        if(textarea){elemento.next().hide();}

        elemento.val(elemento.attr("title"))
                .removeClass("co");
    }else{
        if(elemento.val() != elemento.attr("title")){
            elemento.addClass("co");
        }
    }
}

/* para revelar elementos dentro de una ficha o contenedor que tengan display none */
function revelarElementos(object){
    object.children(".di_no").addClass("di_bl").removeClass("di_no");
}
function ocultarElementos(object){
    object.children(".di_bl").addClass("di_no").removeClass("di_bl");
}

/* lo mismo que pero solo con this */
function revelarElemento(object){
    object.addClass("di_bl").removeClass("di_no");
}
function ocultarElemento(object){
    object.addClass("di_no").removeClass("di_bl");
}

/**
 * Para setear un contenedor en estado de espera a un request ajax
 * Se pasa el id del contenedor y el estado: true->en espera, false->se finalizo el envio/devolucion
 */
function setWaitingStatus(contenedorId, show, ajaxLoadingSize)
{
    //ajaxLoading tiene valor por defecto
    ajaxLoadingSize = ajaxLoadingSize || '30';
    
    var contenedor = $("#" + contenedorId);

    var ajaxLoading
    if(ajaxLoadingSize == '30'){
        ajaxLoading = $("#" + contenedorId + " .ajax_wait30");
    }else{
        if(ajaxLoadingSize == '16'){
            ajaxLoading = $("#" + contenedorId + " .ajax_wait16");
        }
    }

    if(show){
        contenedor.addClass("ajaxdelay");
        //si es que existe, tambien muestro el gif de waiting
        if(ajaxLoading.length){
            revelarElemento(ajaxLoading);
        }
    }else{
        contenedor.removeClass("ajaxdelay");
        if(ajaxLoading.length){
            ocultarElemento(ajaxLoading)
        }
    }
}

/**
 * Activa un mensaje (el div tiene que existir en el arbol DOM por lo general display = none)
 * - sea un mensaje de campo de formulario (validacion de formulario),
 * - o mensaje de un formulario (el que se puede mostrar luego de un pedido ajax),
 * - o un mensaje que pertenezca a cualquier otro contenedor.
 *
 * Se tiene que proveer el id del contenedor del mensaje (contenedorId),
 * si se desea ocultar/mostrar (show),
 * el tipo de mensaje (ver constantes globales)
 * y el mensaje (que puede tener tags de html no es necesariamente string simple, por ejemplo un link, etc).
 *
 * si show = false (se oculta el mensaje) los parametros tipoMsg y mensaje no son necesarios
 *
 * @param contenedorId string
 * @param show boolean
 * @param tipoMsg string
 * @param mensaje string
 */
function setMsgResultado(contenedorId, show, tipoMsg, mensaje)
{
    var contenedor = $("#" + contenedorId);
    var contenedorMensaje = $("#" + contenedorId + " .msg");
    if(show){        
        contenedor.addClass(tipoMsg);
        if(mensaje != null){
            contenedorMensaje.html(mensaje);
        }
        revelarElemento(contenedor);
    }else{
        ocultarElemento(contenedor);
        contenedorMensaje.html("");
        contenedor.removeClass(MSG_ERROR)
                  .removeClass(MSG_CORRECTO)
                  .removeClass(MSG_INFO)
                  .removeClass(MSG_HINT);
    }
}

/**
 * En el before serialize o bien antes del submit verificar que el valor que se envia por post
 * para un input/textarea que tiene valor por defecto es un valor ingresado por el usuario
 * y no el texto en gris que aparece de referencia antes de completar el campo del formulario.
 *
 * en esta funcion, si el elemento no fue modificado por el usuario limpia el contenido
 */
function verificarValorDefecto(elementId)
{
    var campo = $("#" + elementId);    
    //puede ser textarea o input
    if(campo.attr("title") == campo.attr("value") || campo.attr("title") == campo.html()){
        campo.attr("value", "");
        campo.html("");
    }
}

/**
 * Lo mismo pero no toca los inputs devuelve boolean
 *
 * @return true si el campo posee valor por defecto
 */
function verificarValorDefectoBool(elementId)
{
    var campo = $("#" + elementId);
    if(campo.attr("title") == campo.attr("value") || campo.attr("title") == campo.html()){
        return true;
    }

    return false;
}

function paginar(iPage ,toUrl, div, params){
    $.ajax({
        type: "POST",
        url: toUrl,
        data: "iPage="+iPage+"&"+params,
        beforeSend:function(data){
            setWaitingStatus(div, true);
        },
        success: function(data){
            setWaitingStatus(div, false);
            $("#"+div).html(data);
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    });
 }

 function autoCompleteInput(div,action){
    $( "#"+div ).autocomplete({
        source: function( request, response ) {
                $.ajax({
                        url: action,
                        dataType: "jsonp",
                        data: {
                                limit: 12,
                                str: request.term
                        },
                        success: function( data ) {
                                response( $.map( data.usuarios, function( item ) {
                                        return {
                                                label: item.sNombre,
                                                value: item.sNombre
                                        }
                                }));
                        }
                });
            } ,
        minLength: 8
    });
 }



/////////////////////////////////////////////////////////////////////////////////////////////////
//////////////DOCUMENT READY (LAS FUNC DE VISTAS TIENEN QUE SER BINDEADS EN .live()//////////////
/////////////////////////////////////////////////////////////////////////////////////////////////

$(document).ready(function(){
	
    /* para ocultar msgTop 10 segundos despues que se termina de cargar la pagina */
    setTimeout(function(){
        $("#msg_top").hide('drop', {direction: "up"}, 1000)
    }, 5000);

    $(".msgTopWindow .cerrar").click(
        function(){
            $(this).parent().fadeTo(350, 0, function () {$(this).slideUp(600);});
        }
    );
    
    $("input.defVal").live("focus", function(){valueToggleFocus($(this));});
    $("input.defVal").live("blur", function(){valueToggleBlur($(this),false);});

    $("textarea.maxlength").maxlength();
    
    $("textarea.defVal").live("focus", function(){
        var elemento = $(this);
        valueToggleFocus(elemento);
        elemento.next().show(); /*contar caracteres*/
    });
    $("textarea.defVal").live("blur", function(){valueToggleBlur($(this),true);});
    $("textarea.textareaAutoGrow").live("focus", function(){
        $(this).addClass("textAreaExpanded").removeClass("textareaAutoGrow");
    });
    $("textarea.textAreaExpanded").live("blur", function(){
        $(this).addClass("textareaAutoGrow").removeClass("textAreaExpanded");
    });

    //Slides
    $(".slideExpanded").live("click", function(){
       $(this).removeClass("slideExpanded").addClass("slideCollapsed");
       $(this).next().hide();
    });
    $(".slideCollapsed").live("click", function(){
       $(this).removeClass("slideCollapsed").addClass("slideExpanded");
       $(this).next().show();
    });
    
    /* Comunidad/Seguimientos */
    $("#togleInnerCont").live("click", function(){
        var togleInnerCont = $("#togleInnerCont");
        var pageRightInnerCont = $("#pageRightInnerCont");
        var pageRightInnerMainCont = $("#pageRightInnerMainCont");
        if(pageRightInnerCont.hasClass("di_no")){
            pageRightInnerMainCont.removeClass("mari0");
            revelarElemento(pageRightInnerCont);
            togleInnerCont.removeClass("expand").addClass("collapse");
        }else{
            pageRightInnerMainCont.addClass("mari0");
            ocultarElemento(pageRightInnerCont);
            togleInnerCont.removeClass("collapse").addClass("expand");
        }       
    });

    //para las filas desplegables de una tabla.
    $(".desplegable").live("click", function(){
        var id = $(this).attr("rel");
        if($("#desplegable_" + id).css("display") == "none"){
            $("#desplegable_" + id).show();
        }else{
            $("#desplegable_" + id).hide();
        }
    });

    //SUPER FISH MENU
    $(function(){
        $('.menuList').superfish();
    });

    // PUNTUAR PUBLICACIONES, ETC //
    if($("#puntaje").length){
        var txts_puntaje = new Array();
        txts_puntaje['pun1'] = 'p\xe9simo';
        txts_puntaje['pun2'] = 'malo';
        txts_puntaje['pun3'] = 'normal';
        txts_puntaje['pun4'] = 'muy bueno';
        txts_puntaje['pun5'] = 'excelente';

        var puntaje = $("#puntaje");
        var txt_puntaje = $("#txt_puntaje");
        var valorPosicAnt = "pun0";
        $("#puntaje div").mouseover(function(){
            puntaje.removeClass("hold");
            $("#valoracion").val("");
            var valorPosic = $(this).attr("id"); /* El id del span coincide con la clase del CSS para asignar de una el valor ;) */
            if(valorPosic != valorPosicAnt){
                puntaje.addClass(valorPosic).removeClass(valorPosicAnt);
                txt_puntaje.html(txts_puntaje[valorPosic]);
                valorPosicAnt = valorPosic;
            }
        });
        puntaje.mouseout(function(){
            if(valorPosicAnt != "pun0" && !$(this).hasClass('hold')){ //si no marco ninguno vuelve a estado inicial
                puntaje.addClass("pun0").removeClass(valorPosicAnt);
                valorPosicAnt = "pun0";
                txt_puntaje.html("");
            }
        });
        $("#puntaje div").click(function(){
            $("#valoracion").val($(this).attr("rel"));
            puntaje.addClass("hold");
        });
    }
    // FIN PUNTUAR PUBLICACIONES, ETC //

    //editar foto thumbail galerias
    $(".thumbEdit").live("mouseenter", function(){
        $(this).children(".thumbEditMenu").show();
    });
    $(".thumbEdit").live("mouseleave", function(){
        $(this).children(".thumbEditMenu").hide();
    });
});