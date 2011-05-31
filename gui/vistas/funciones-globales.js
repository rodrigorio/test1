/**
 * Funciones que se compartan entre todos los .js de las vistas.
 *
 *
 */

/**
 * Check if a given date is valid.
 * strDate: YYYY-MM-DD
 */
function isValidDate(strDate)
{
    var date = new Date(strDate);
    return !isNaN(date.getTime());
}

/**
 * Textos genericos para mensajes de validacion de campos en formularios.
 * Actuan en conjunto con el plugin validator de jQuery
 */
function mensajeValidacion(template, value)
{
    var mensajes = new Array();
    messages['requerido'] = "Por favor ingrese {0}";
    messages['valido'] = "Dato invalido en {0}";
    messages['numerico'] = "{0} requiere un dato numerico";
    messages['positivos'] = "Por favor ingrese solo numeros positivos";
    messages['contrasenia'] = "La contraseña es muy simple";
    messages['contraseniaIguales'] = "Las contraseñas no coinciden";
    if (value == null) {
        return messages[template];
    } else {
        return jQuery.validator.format(messages[template], value);
    }
}

/*
 * Funciones para operar con la vista
 *
 */
function showWaitingImage(show, message, error)
{
    var $text = $("#ajaxWaitingText");
    var $image = $("#ajaxWaitingImage");
    $text.hide().html("").removeClass("ui-state-error");
    if (show) {
        $image.fadeIn("fast");
    } else {
        $image.fadeOut("slow", function(){
            if (message) {
                $text.html(message);
                if (error) {
                    $text.addClass("ajaxFailedResult");
                } else {
                    $text.addClass("ajaxSuccessResult");
                }
                $text.show();
            }
        });
    }
}


function ajaxStartSaving()
{
    showWaitingImage(true);
}
function ajaxEndSaving(error)
{
    if (error) {
        showWaitingImage(false, 'Error while saving!', true);
    } else {
        showWaitingImage(false, 'Saved!', false);
    }
}

function ajaxStartSending()
{
    showWaitingImage(true);
}
function ajaxEndSending(error)
{
    if (error) {
        showWaitingImage(false, 'Error while sending!');
    } else {
        showWaitingImage(false, 'Sent!');
    }
}