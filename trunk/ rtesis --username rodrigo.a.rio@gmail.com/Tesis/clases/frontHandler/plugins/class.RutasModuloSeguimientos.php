<?php

class RutasModuloSeguimientos
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
        $router = FrontController::getInstance()->getRouter();

        $route = new RegexRoute('seguimientos/home',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'index',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosIndexIndex', $route);
        $route = new RegexRoute('seguimientos/nuevo-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'nuevoSeguimiento'
                                ));
        $router->addRoute('seguimientosSeguimientosNuevoSeguimiento', $route);
        $route = new RegexRoute('seguimientos/seguimientos-eliminar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosSeguimientosEliminar', $route);
        $route = new RegexRoute('seguimientos/buscar-discapacitados',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'index',
                                        'action'     => 'buscarDiscapacitados'
                                ));
        $router->addRoute('seguimientosIndexBuscarDiscapacitados', $route);
        $route = new RegexRoute('seguimientos/procesar-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesarSeguimiento'
                                ));
        $router->addRoute('seguimientosSeguimientosProcesarSeguimiento', $route);
        $route = new RegexRoute('seguimientos/buscar-seguimientos',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'buscarSeguimientos'
                                ));
        $router->addRoute('seguimientosSeguimientosBuscarSeguimiento', $route);
        $route = new RegexRoute('seguimientos/cambiar-estado-seguimientos',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'cambiarEstadoSeguimientos'
                                ));
        $router->addRoute('seguimientosSeguimientosCambiarEstadoSeguimiento', $route);
        $route = new RegexRoute('seguimientos/editar-antecedentes',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'editarAntecedentes'
                                ));
        $router->addRoute('seguimientosSeguimientosEditarSeguimientos', $route);
        $route = new RegexRoute('seguimientos/procesar-antecedentes',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesarAntecedentes'
                                ));
        $router->addRoute('seguimientosSeguimientosProcesarAntecedentes', $route);
        $route = new RegexRoute('seguimientos/ver',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'ver'
                                ));
        $router->addRoute('seguimientosSeguimientosVer', $route);


        //personas
        $route = new RegexRoute('seguimientos/personas',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'personas',
                                        'action'     => 'index' //listar
                                ));
        $router->addRoute('seguimientosPersonasIndex', $route);
        $route = new RegexRoute('seguimientos/agregar-persona',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'personas',
                                        'action'     => 'agregar'
                                ));
        $router->addRoute('seguimientosPersonasAgregar', $route);
        $route = new RegexRoute('seguimientos/modificar-persona',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'personas',
                                        'action'     => 'modificar'
                                ));
        $router->addRoute('seguimientosPersonasModificar', $route);
        $route = new RegexRoute('seguimientos/personas-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'personas',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosPersonasProcesar', $route);
        $route = new RegexRoute('seguimientos/ver-persona',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'personas',
                                        'action'     => 'ver'
                                ));
        $router->addRoute('seguimientosPersonasVer', $route);
    }
}