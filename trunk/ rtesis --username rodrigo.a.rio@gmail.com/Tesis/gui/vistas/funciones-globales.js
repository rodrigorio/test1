/**
 * Funciones que se compartan entre todos los .js de las vistas.
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
    messages['numerico'] = "{0} requiere dato numerico";
    messages['positivos'] = "requiere numero positivo";
    messages['contrasenia'] = "La contraseña es muy simple";
    messages['contraseniaIguales'] = "Las contraseñas no coinciden";
    if (value == null) {
        return messages[template];
    } else {
        return jQuery.validator.format(messages[template], value);
    }
}

/*
 * Funciones para utilizar en peticiones Ajax.
 */
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