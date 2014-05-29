<?php
/**
 * Este archivo es conocido en la jerga zend como "Bootstrap file"
 */
require_once 'includes/includePath.php';
require_once 'includes/autoload.php';

FrontController::getInstance()->throwExceptions(true) //poner en true cuando se testea, las excepciones se imprimen y cortan la ejecucion del sistema.
                              ->setBaseUrl('/')
                              ->registerPlugin(new PluginSession())                       // routeStartup  preDispatch
                              ->registerPlugin(new PluginRedireccion404())                //               preDispatch   postDispatch
                              ->registerPlugin(new PluginPermisos())                      //               preDispatch   postDispatch
                              ->registerPlugin(new PluginRedireccionAccionDesactivada())  //               preDispatch                   dispatchLoopShutdown
                              ->registerPlugin(new PluginCache())                         //               preDispatch
                              ->registerPlugin(new PluginParametros())                    // routeStartup  preDispatch
                              ->registerPlugin(new PluginConexionDataBase())              // routeStartup  preDispatch
                              ->registerPlugin(new PluginRouteSchema())                   // routeStartup
                              ->registerPlugin(new PluginSeguridad())                     // routeStartup
                              ->dispatch();

//El unico plug-in que se "cruza" es el de conexion a DB en preDispatch, no importa porque si no hay conexion a DB se muestra pantalla
//Que indica que no se puede utilizar el sistema. (se quitan todos los plugins y ya la ejecucion es totalmente fuera de lo normal).
