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
 * Por cuestiones de asimilar un estandard, no se van a usar valores por defecto en el objeto HttpRequest,
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
    public function routeStartup(HttpRequest $request)
    {
        //esta clarito, si hubo error en la sesion o si no se pudo conectar a la DB no rutea, la vista la determinan los respectivos plugins
        if(Session::isDestroyed() || !PluginConexionDataBase::isConnected()){
            return;
        }
        
        //obtengo la home del sitio, es decir, modulo_controlador_accion inicial cuando se entra a la pagina.
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        $homeSitioModulo = $parametros->obtener('HOME_SITIO_MODULO');
        $homeSitioControlador = $parametros->obtener('HOME_SITIO_CONTROLADOR');
        $homeSitioAccion = $parametros->obtener('HOME_SITIO_ACCION');

        $router = FrontController::getInstance()->getRouter();

        ////////////////////////////////
        ////// RUTAS MODULO INDEX //////
        ////////////////////////////////
        
        $route = new RegexRoute('',
                                array(
                                        'module' => $homeSitioModulo,
                                        'controller' => $homeSitioControlador,
                                        'action'     => $homeSitioAccion
                                ));
        $router->addRoute('defaultDefaultDefault', $route);
        
        $route = new RegexRoute('recuperarContrasenia',
                                array(
                                        'module' => "index",
                                        'controller' => "login",
                                        'action'     => "recuperarContrasenia"
                                ));
        $router->addRoute('indexLoginFormRecuperarContrasenia', $route);
        $route = new RegexRoute('confirmarContrasenia',
                                array(
                                        'module' => "index",
                                        'controller' => "login",
                                        'action'     => "confirmarContrasenia"
                                ));
        $router->addRoute('indexLoginFormConfirmarContrasenia', $route);
        
        $route = new RegexRoute('registracion',
                                array(
                                        'module' => "index",
                                        'controller' => "registracion",
                                        'action'     => "formulario"
                                ));
        $router->addRoute('indexRegistracionFormulario', $route);
        
        $route = new RegexRoute('registracion-procesar',
                                array(
                                        'module' => "index",
                                        'controller' => "registracion",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('indexRegistracionProcesar', $route);
        
        $route = new RegexRoute('login',
                                array(
                                        'module' => 'index',
                                        'controller' => 'login',
                                        'action'     => 'index'
                                ));
        $router->addRoute('indexLoginIndex', $route);

        $route = new RegexRoute('login-procesar',
                                array(
                                        'module' => 'index',
                                        'controller' => 'login',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('indexLoginProcesar', $route);

        $route = new RegexRoute('logout',
                                array(
                                        'module' => 'index',
                                        'controller' => 'login',
                                        'action'     => 'logout'
                                ));
        $router->addRoute('indexLoginLogout', $route);
               
        ////////////////////////////////////
        ////// RUTAS MODULO COMUNIDAD //////
        ////////////////////////////////////

        $route = new RegexRoute('comunidad/home',
                                array(
                                        'module' => 'comunidad',
                                        'controller' => 'index',
                                        'action'     => 'index'
                                ));
        $router->addRoute('comunidadIndexIndex', $route);
        

        $route = new RegexRoute('comunidad/invitaciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "invitaciones",
                                        'action'     => "index"
                                ));
        $router->addRoute('comunidadInvitacionesIndex', $route);

        $route = new RegexRoute('comunidad/invitaciones-listado',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "invitaciones",
                                        'action'     => "listado"
                                ));
        $router->addRoute('comunidadInvitacionesListado', $route);

        $route = new RegexRoute('comunidad/nueva-invitacion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "invitaciones",
                                        'action'     => "formulario"
                                ));
        $router->addRoute('comunidadInvitacionesFormulario', $route);

        $route = new RegexRoute('comunidad/invitacion-procesar',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "invitaciones",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('comunidadInvitacionesProcesar', $route);

        $route = new RegexRoute('comunidad/datos-personales',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "datosPersonales",
                                        'action'     => "formulario"
                                ));
        $router->addRoute('comunidadDatosPersonalesFormulario', $route);

        $route = new RegexRoute('comunidad/datos-personales-procesar',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "datosPersonales",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('comunidadDatosPersonalesProcesar', $route);

        //INSITUCIONES//
        $route = new RegexRoute('comunidad/editar-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "editarInstitucion"
                                ));
        $router->addRoute('comunidadInstitucionesEditarInstitucion', $route);
        $route = new RegexRoute('comunidad/ampliar-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "ampliarInstitucion"
                                ));
        $router->addRoute('comunidadInstitucionesAmpliarInstitucion', $route);
        $route = new RegexRoute('comunidad/masInstituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "masInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesMasInstituciones', $route);
        
        $route = new RegexRoute('comunidad/provinciasByPais',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "provinciasByPais"
                                ));
        $router->addRoute('comunidadInstitucionesProvinciasByPais', $route);
        
        $route = new RegexRoute('comunidad/ciudadesByProvincia',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "ciudadesByProvincia"
                                ));
        $router->addRoute('comunidadInstitucionesCiudadesByProvincia', $route);
        
        $route = new RegexRoute('comunidad/nueva-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "nuevaInstitucion"
                                ));
        $router->addRoute('comunidadInstitucionesNueva', $route);

        $route = new RegexRoute('comunidad/institucion-procesar',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('comunidadInstitucionesProcesar', $route);
        
        $route = new RegexRoute('comunidad/instituciones-listado',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "listadoInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesListado', $route);
        
        $route = new RegexRoute('comunidad/instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "index"
                                ));
        $router->addRoute('comunidadInstitucionesIndex', $route);
        
        ///////////////////////////////////////////
        ////// RUTAS MODULO SEGUIMIENTOS //////////
        ///////////////////////////////////////////
        
        $route = new RegexRoute('seguimientos/home',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'index',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosIndexIndex', $route);

        ////////////////////////////////////
        ////// RUTAS MODULO ADMIN //////////
        ////////////////////////////////////

        $route = new RegexRoute('admin/home',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'index',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminIndexIndex', $route);

        $route = new RegexRoute('admin/procesar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'procesarEspecialidad'
                                ));
        $router->addRoute('adminIndexProcesarEspecialidad', $route);
        
        $route = new RegexRoute('admin/listar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'listarEspecialidades'
                                ));
        $router->addRoute('adminIndexListarEspecialidades', $route);
        
        $route = new RegexRoute('admin/nueva-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'nuevaEspecialidad'
                                ));
        $router->addRoute('adminIndexNuevaEspecialidad', $route);
       
        $route = new RegexRoute('admin/administrar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminIndexAdministrarEspecialidad', $route);
    }
}