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
    }   
}