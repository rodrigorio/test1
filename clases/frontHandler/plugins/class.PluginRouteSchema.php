<?php
/*
 * http://framework.zend.com/manual/en/zend.controller.router.html (ver la parte de RegexRouter)
 *
 * URL Detection is Case Sensitive
 *
 * Note: Reverse Matching
 * Routes are matched in reverse order so make sure your most generic routes are defined first.
 *
 * Esquema GENERAL de Url's
 *
 * - /
 * - /MODULO
 * - /PAGECONTROLLER
 * - /PAGECONTROLLER/PARAMETRO1-PARAMETRO2
 * - /MODULO/PAGECONTROLLER
 * - /PAGECONTROLLER/PAGEACTION
 * - /MODULO/PAGECONTROLLER/PAGEACTION
 * - /PAGECONTROLLER/PAGEACTION/PARAMETRO
 * - /PAGECONTROLLER/PAGEACTION/PARAMETRO-EXTRA
 * - /MODULO/PAGECONTROLLER/PAGEACTION/PARAMETRO
 * - /MODULO/PAGECONTROLLER/PAGEACTION/PARAMETRO-EXTRA
 *
 * Ejemplo:
 *
 * /PAGECONTROLLER/PARAMETRO1-PARAMETRO2
 * http://www.infobae.com/notas/579620-Intel-logra-otro-hito-en-la-industria-de-los-microprocesadores.html
 * - Esto es bueno porque no conviene crear un subdirectorio con 579620
 * - Este es un ejemplo en el que el modulo se asigna en valor por defecto -> si no se especifica es index
 *
 * NOTA:
 * Se pueden ir agregando casos especiales pero tener en cuenta que hay que ir de lo mas complicado a lo mas simple.
 * Además tener en cuenta que si hay mas de un parametro, (como en el ejemplo de infobae que ademas del id esta el nombre de la publicacion)
 * se debe reconocer antes del dispatch si es que algun parametro falta y RECARGAR LA PAGINA con la url completa.
 * Sino es como que por mas de una url se puede acceder al mismo contenido y es perjudicial para el SEO.
 *
 * Por cuestiones de asimilar un estandard, no se van a usar valores por defecto en el objeto Request,
 * cada ruta aqui seteada debe devolver modulo, page, action y los parametros si es que existen.
 */

class PluginRouteSchema extends PluginAbstract
{

    /**
     * Agrega todas las rutas del sistema al router.
     * Si alguno de los plugins seteo el flag 'noRutear' en routeStartup entonces no se cargan las rutas
     * (El router no tendra rutas para matchear y devolvera 404 instantaneamente)
     * Por lo general el propio plugin redirecciona a alguna vista dependiendo en excepciones de sistema.
     */
    public function routeStartup(Request $request)
    {
        //esta clarito, si hubo error en la sesion o si no se pudo conectar a la DB no rutea, la vista la determinan los respectivos plugins
        if(Session::isDestroyed() || !PluginConexionDataBase::isConnected()){
            return;
        }

        RutasModuloIndex::getInstance()->cargarRutas();
        RutasModuloComunidad::getInstance()->cargarRutas();
        RutasModuloSeguimientos::getInstance()->cargarRutas();
        RutasModuloAdmin::getInstance()->cargarRutas();
    }
}
