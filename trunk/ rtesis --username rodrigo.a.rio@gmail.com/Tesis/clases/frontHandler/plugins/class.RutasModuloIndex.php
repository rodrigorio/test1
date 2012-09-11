<?php

class RutasModuloIndex
{
    private static $instance = null;

    /**
     * Singleton instance
     *
     * @return RutasModuloSeguimientos
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function cargarRutas()
    {
        //obtengo la home del sitio, es decir, modulo_controlador_accion inicial cuando se entra a la pagina.
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        $homeSitioModulo = $parametros->obtener('HOME_SITIO_MODULO');
        $homeSitioControlador = $parametros->obtener('HOME_SITIO_CONTROLADOR');
        $homeSitioAccion = $parametros->obtener('HOME_SITIO_ACCION');
        
        $router = FrontController::getInstance()->getRouter();

        $route = new RegexRoute('', array('module' => $homeSitioModulo,
                                          'controller' => $homeSitioControlador,
                                          'action'     => $homeSitioAccion));
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
        
        $route = new RegexRoute('video',
                                array(
                                    'module' => 'index',
                                    'controller' => 'index',
                                    'action'     => 'video'
                                ));
        $router->addRoute('indexIndexVideoAmpliar', $route);
        $route = new RegexRoute('provinciasByPais',
                                array(
                                    'module' => "index",
                                    'controller' => "index",
                                    'action' => "provinciasByPais"
                                ));
        $router->addRoute('provinciasByPais', $route);
        $route = new RegexRoute('ciudadesByProvincia',
                                array(
                                    'module' => "index",
                                    'controller' => "index",
                                    'action' => "ciudadesByProvincia"
                                ));
        $router->addRoute('ciudadesByProvincia', $route);

        $route = new RegexRoute('desactivar-notificaciones-mail',
                                array(
                                    'module' => "index",
                                    'controller' => "index",
                                    'action' => "desactivarNotificacionesMail"
                                ));
        $router->addRoute('desactivarNotificacionesMail', $route);

        //INSTITUCIONES
        $route = new RegexRoute('instituciones',
                                array(
                                        'module' => "index",
                                        'controller' => "instituciones",
                                        'action'     => "index"
                                ));
        $router->addRoute('indexInstitucionesIndex', $route);
        $route = new RegexRoute('instituciones/(\d+)-(.+)',
                                array(
                                    'module' => 'index',
                                    'controller' => 'instituciones',
                                    'action'     => 'ampliarInstitucion'
                                ),
                                array(
                                    1 => 'iInstitucionId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('indexInstitucionesAmpliarInstitucion', $route);
        $route = new RegexRoute('instituciones/procesar',
                                array(
                                        'module' => "index",
                                        'controller' => "instituciones",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('indexInstitucionesProcesar', $route);

        //PUBLICACIONES
        $route = new RegexRoute('publicaciones',
                                array(
                                        'module' => "index",
                                        'controller' => "publicaciones",
                                        'action'     => "index"
                                ));
        $router->addRoute('indexPublicacionesIndex', $route);
        $route = new RegexRoute('publicaciones/(\d+)-(.+)',
                                array(
                                    'module' => 'index',
                                    'controller' => 'publicaciones',
                                    'action'     => 'verPublicacion'
                                ),
                                array(
                                    1 => 'iPublicacionId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('indexPublicacionesVerPublicacion', $route);
        $route = new RegexRoute('reviews/(\d+)-(.+)',
                                array(
                                    'module' => 'index',
                                    'controller' => 'publicaciones',
                                    'action'     => 'verReview'
                                ),
                                array(
                                    1 => 'iReviewId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('indexPublicacionesVerReview', $route);
        $route = new RegexRoute('publicaciones/procesar',
                                array(
                                    'module' => "index",
                                    'controller' => "publicaciones",
                                    'action'     => "procesar"
                                ));
        $router->addRoute('indexPublicacionesProcesar', $route);

        //DESCARGAS
        $route = new RegexRoute('descargas',
                                array(
                                        'module' => "index",
                                        'controller' => "software",
                                        'action'     => "index"
                                ));
        $router->addRoute('indexDescargasIndex', $route);
        $route = new RegexRoute('descargas/(\S+)',
                                array(
                                    'module' => 'index',
                                    'controller' => 'software',
                                    'action'     => 'listarCategoria'
                                ),
                                array(
                                    1 => 'sUrlToken'
                                ),
                                '');
        $router->addRoute('indexSoftwareListarCategoria', $route);
        $route = new RegexRoute('descargas/(\S+)\/(\d+)-(.+)',
                                array(
                                    'module' => 'index',
                                    'controller' => 'software',
                                    'action'     => 'verSoftware'
                                ),
                                array(
                                    1 => 'sUrlToken',
                                    2 => 'iSoftwareId',
                                    3 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('indexSoftwareVerAplicacion', $route);
        $route = new RegexRoute('descargas/procesar',
                                array(
                                    'module' => "index",
                                    'controller' => "software",
                                    'action'     => "procesar"
                                ));
        $router->addRoute('indexSoftwareProcesar', $route);
    }   
}