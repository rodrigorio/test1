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
        $route = new RegexRoute('seguimientos/form-modificar-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formModificarSeguimiento'
                                ));
        $router->addRoute('seguimientosSeguimientosFormModificarSeguimiento', $route);
        $route = new RegexRoute('seguimientos/guardar-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'guardarSeguimiento'
                                ));
        $router->addRoute('seguimientosSeguimientosGuardarSeguimiento', $route);
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
        $router->addRoute('seguimientosSeguimientosEditarAntecedentes', $route);
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

        //galeria adjuntos
        $route = new RegexRoute('seguimientos/ver-adjuntos',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'verAdjuntos'
                                ));
        $router->addRoute('seguimientosSeguimientosAdjuntos', $route);
        $route = new RegexRoute('seguimientos/form-adjuntar-foto',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formAdjuntarFoto'
                                ));
        $router->addRoute('seguimientosSeguimientosFormAdjuntarFoto', $route);
        $route = new RegexRoute('seguimientos/form-adjuntar-video',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formAdjuntarVideo'
                                ));
        $router->addRoute('seguimientosSeguimientosFormAdjuntarVideo', $route);
        $route = new RegexRoute('seguimientos/form-adjuntar-archivo',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formAdjuntarArchivo'
                                ));
        $router->addRoute('seguimientosSeguimientosFormAdjuntarArchivo', $route);
        $route = new RegexRoute('seguimientos/form-editar-adjunto',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formEditarAdjunto'
                                ));
        $router->addRoute('seguimientosSeguimientosFormEditarAdjunto', $route);
        $route = new RegexRoute('seguimientos/procesar-adjunto',
                                array(
                                    'module' => 'seguimientos',
                                    'controller' => 'seguimientos',
                                    'action'     => 'procesarAdjunto'
                                ));
        $router->addRoute('seguimientosSeguimientosProcesarAdjunto', $route);

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
        $route = new RegexRoute('seguimientos/editar-diagnostico',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'editarDiagnostico'
                                ));
        $router->addRoute('seguimientosEditarDiagnostico', $route);             
        $route = new RegexRoute('seguimientos/procesar-diagnostico',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesarDiagnostico'
                                ));
        $router->addRoute('seguimientosProcesarDiagnostico', $route);          
        $route = new RegexRoute('seguimientos/listar-ciclos-por-niveles',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarCiclosPorNiveles'
                                ));
        $router->addRoute('seguimientosListarCiclosPorNivel', $route);  
        $route = new RegexRoute('seguimientos/listar-areas-por-ciclos',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarAreasPorCiclos'
                                ));
        $router->addRoute('seguimientosListarAreasPorCiclo', $route);  
        $route = new RegexRoute('seguimientos/listar-ejes-por-area',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarEjesPorArea'
                                ));
        $router->addRoute('seguimientosListarEjePorArea', $route);

        //unidades
        $route = new RegexRoute('seguimientos/listar-unidades',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosUnidadesIndex', $route);
        $route = new RegexRoute('seguimientos/form-crear-unidad',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'formCrearUnidad'
                                ));
        $router->addRoute('seguimientosUnidadesFormCrearUnidad', $route);
        $route = new RegexRoute('seguimientos/form-editar-unidad',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'formEditarUnidad'
                                ));
        $router->addRoute('seguimientosUnidadesFormEditarUnidad', $route);
        $route = new RegexRoute('seguimientos/guardar-unidad',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'guardarUnidad'
                                ));
        $router->addRoute('seguimientosUnidadesGuardarUnidad', $route);
        $route = new RegexRoute('seguimientos/unidades-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosUnidadesProcesar', $route);
        $route = new RegexRoute('seguimientos/borrar-unidad',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosUnidadesBorrar', $route);

        //variables
        $route = new RegexRoute('seguimientos/listar-variables',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosVariablesIndex', $route);
        $route = new RegexRoute('seguimientos/form-crear-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'formCrearVariable'
                                ));
        $router->addRoute('seguimientosUnidadesFormCrearVariable', $route);
        $route = new RegexRoute('seguimientos/form-editar-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'formEditarVariable'
                                ));
        $router->addRoute('seguimientosUnidadesFormEditarVariable', $route);
    }
}