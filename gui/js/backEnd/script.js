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
 *
 * @param contenedorId string
 * @param show boolean
 */
function setWaitingStatus(contenedorId, show)
{
    var contenedor = $("#" + contenedorId);
    var ajaxLoading = $("#ajax_loading");

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

/* mensajes globales disponibles para utilizar en funciones ajax, etc */
if(!lang)
var lang = Array();
lang['error procesar'] = 'Error al intentar procesar lo solicitado';
lang['error permiso'] = 'La accion se encuentra desactivada o no tienes permiso para realizarla';
lang['exito procesar'] = 'Los datos se han procesado con éxito';
lang['exito procesar archivo'] = 'El archivo se logro subir con exito';
lang['error procesar archivo'] = 'Hubo un error al tratar de procesar el archivo';

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
    mensajes['url'] = "La URL es inválida o innaccesible";
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
 * Lo paso a una funcion para bindear todo a lo que vuelve por ajax mas facil
 */
function bindEventsAdmin()
{
    /* para ocultar msgTop 10 segundos despues que se termina de cargar la pagina */
    setTimeout(function(){
        $("#msg_top").hide('drop', {direction: "up"}, 1000)
    }, 5000);
    
    // Notification Close Button
    $(".close-notification").click(
            function () {
                    $(this).parent().fadeTo(350, 0, function () {$(this).slideUp(600);});
                    return false;
            }
    );

    // jQuery Tipsy
    $('[rel=tooltip], #main-nav span, .loader').tipsy({gravity:'s', fade:true}); // Tooltip Gravity Orientation: n | w | e | s

    // jQuery Facebox Modal
    $('a[rel*=modal]').facebox();

    // jQuery jWYSIWYG Editor
    //$('.wysiwyg').wysiwyg({ iFrameClass:'wysiwyg-iframe' });

    // jQuery dataTables
    $('.datatable').dataTable();

    // Check all checkboxes
    $('.check-all').click(
            function(){
                    $(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
            }
    )

    // IE7 doesn't support :disabled
    $('.ie7').find(':disabled').addClass('disabled');

    // Widget Close Button
    $(".close-widget").click(
            function () {
                    $(this).parent().fadeTo(350, 0, function () {$(this).slideUp(600);});
                    return false;
            }
    );

    // Image actions
    $('.image-frame').hover(
            function() { $(this).find('.image-actions').css('display', 'none').fadeIn('fast').css('display', 'block'); }, // Show actions menu
            function() { $(this).find('.image-actions').fadeOut(100); } // Hide actions menu
    );

    // Content box tabs
    $('.tab').hide(); // Hide the content divs
    $('.default-tab').show(); // Show the div with class "default-tab"
    $('.tab-switch a.default-tab').addClass('current'); // Set the class of the default tab link to "current"

    $('.tab-switch a').click(
            function() {
                    var tab = $(this).attr('href'); // Set variable "tab" to the value of href of clicked tab
                    $(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
                    $(this).addClass('current'); // Add class "current" to clicked tab
                    $(tab).siblings('.tab').hide(); // Hide all content divs
                    $(tab).show(); // Show the content div with the id equal to the id of clicked tab
                    return false;
            }
    );

    // Content box side tabs
    $(".sidetab").hide();// Hide the content divs
    $('.default-sidetab').show(); // Show the div with class "default-sidetab"
    $('.sidetab-switch a.default-sidetab').addClass('current'); // Set the class of the default tab link to "current"

    $(".sidetab-switch a").click(
            function() {
                    var sidetab = $(this).attr('href'); // Set variable "sidetab" to the value of href of clicked sidetab
                    $(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all sidetabs
                    $(this).addClass('current'); // Add class "current" to clicked sidetab
                    $(sidetab).siblings('.sidetab').hide(); // Hide all content divs
                    $(sidetab).show(); // Show the content div with the id equal to the id of clicked tab
                    return false;
            }
    );

    //Minimize Content Article
    $("article header h2").css({ "cursor":"s-resize" }); // Minizmie is not available without javascript, so we don't change cursor style with CSS
    $("article header h2").click( // Toggle the Box Content
            function () {
                    $(this).parent().find("nav").toggle();
                    $(this).parent().parent().find("section, footer").toggle();
            }
    );
}

$(function(){
    $("textarea.maxlength").maxlength();

    // Menu Dropdown
    $("#main-nav li ul").hide(); //Hide all sub menus
    $("#main-nav li.current a").parent().find("ul").slideToggle("slow"); // Slide down the current sub menu
    $("#main-nav li a").click(
            function () {
                    $(this).parent().siblings().find("ul").slideUp("normal"); // Slide up all menus except the one clicked
                    $(this).parent().find("ul").slideToggle("normal"); // Slide down the clicked sub menu
                    return false;
            }
    );
    $("#main-nav li a.no-submenu").click(
            function () {
                    window.location.href=(this.href); // Open link instead of a sub menu
                    return false;
            }
    );

    //para las filas desplegables de una tabla.
    $(".desplegable").live("click", function(){
        var id = $(this).attr("rel");
        if($("#desplegable_" + id).css("display") == "none"){
            $("#desplegable_" + id).show("slow");
        }else{
            $("#desplegable_" + id).hide();
        }
    });

    bindEventsAdmin();
});