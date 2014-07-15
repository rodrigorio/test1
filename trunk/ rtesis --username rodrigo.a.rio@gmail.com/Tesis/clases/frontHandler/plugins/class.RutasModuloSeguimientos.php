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
        $route = new RegexRoute('seguimientos/procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosSeguimientosProcesar', $route);

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

        //diagnostico
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

        //pronostico
        $route = new RegexRoute('seguimientos/editar-pronostico',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'editarPronostico'
                                ));
        $router->addRoute('seguimientosEditarPronostico', $route);
        $route = new RegexRoute('seguimientos/procesar-pronostico',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesarPronostico'
                                ));
        $router->addRoute('seguimientosProcesarPronostico', $route);

        //objetivo aprendizaje
        $route = new RegexRoute('seguimientos/listar-ciclos-por-nivel',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarCiclosPorNivel'
                                ));
        $router->addRoute('seguimientosListarCiclosPorNivel', $route);
        $route = new RegexRoute('seguimientos/listar-anios-por-ciclo',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarAniosPorCiclo'
                                ));
        $router->addRoute('seguimientosListarAniosPorCiclo', $route);
        $route = new RegexRoute('seguimientos/listar-areas-por-anio',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarAreasPorAnio'
                                ));
        $router->addRoute('seguimientosListarAreasPorAnio', $route);
        $route = new RegexRoute('seguimientos/listar-ejes-por-area',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarEjesPorArea'
                                ));
        $router->addRoute('seguimientosListarEjesPorArea', $route);
        $route = new RegexRoute('seguimientos/listar-objetivos-aprendizaje-por-eje',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'listarObjetivosAprendizajePorEje'
                                ));
        $router->addRoute('seguimientosListarObjetivosAprendizajePorEje', $route);

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
        $route = new RegexRoute('seguimientos/unidades-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'listarUnidadesPorSeguimiento'
                                ));
        $router->addRoute('seguimientosUnidadesListarUnidadesPorSeguimiento', $route);
        $route = new RegexRoute('seguimientos/unidades-seguimiento-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'unidadesPorSeguimientoProcesar'
                                ));
        $router->addRoute('seguimientosUnidadesUnidadesPorSeguimientoProcesar', $route);
        $route = new RegexRoute('seguimientos/ampliar-unidad-esporadica',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'unidades',
                                        'action'     => 'ampliarEsporadica'
                                ));
        $router->addRoute('seguimientosUnidadesAmpliarEsporadica', $route);

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
                                        'controller' => 'variables',
                                        'action'     => 'formCrearVariable'
                                ));
        $router->addRoute('seguimientosVariablesFormCrearVariable', $route);
        $route = new RegexRoute('seguimientos/form-editar-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'formEditarVariable'
                                ));
        $router->addRoute('seguimientosVariablesFormEditarVariable', $route);
        $route = new RegexRoute('seguimientos/variables-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosVariablesProcesar', $route);
        $route = new RegexRoute('seguimientos/guardar-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'guardar'
                                ));
        $router->addRoute('seguimientosVariablesGuardar', $route);
        $route = new RegexRoute('seguimientos/borrar-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosVariablesBorrar', $route);
        $route = new RegexRoute('seguimientos/borrar-modalidad-variable',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'variables',
                                        'action'     => 'eliminarModalidad'
                                ));
        $router->addRoute('seguimientosVariablesBorrarModalidad', $route);

        //entrevistas
        $route = new RegexRoute('seguimientos/listar-entrevistas',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosEntrevistasIndex', $route);
        $route = new RegexRoute('seguimientos/form-crear-entrevista',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'formCrearEntrevista'
                                ));
        $router->addRoute('seguimientosEntrevistasFormCrearEntrevista', $route);
        $route = new RegexRoute('seguimientos/form-editar-entrevista',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'formEditarEntrevista'
                                ));
        $router->addRoute('seguimientosEntrevistasFormEditarEntrevista', $route);
        $route = new RegexRoute('seguimientos/guardar-entrevista',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'guardarEntrevista'
                                ));
        $router->addRoute('seguimientosEntrevistasGuardarEntrevista', $route);
        $route = new RegexRoute('seguimientos/entrevistas-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosEntrevistasProcesar', $route);
        $route = new RegexRoute('seguimientos/borrar-entrevista',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosEntrevistasBorrar', $route);
        $route = new RegexRoute('seguimientos/entrevistas-seguimiento',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'listarEntrevistasPorSeguimiento'
                                ));
        $router->addRoute('seguimientosEntrevistasListarEntrevistasPorSeguimiento', $route);
        $route = new RegexRoute('seguimientos/entrevistas-seguimiento-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'entrevistasPorSeguimientoProcesar'
                                ));
        $router->addRoute('seguimientosEntrevistasEntrevistasPorSeguimientoProcesar', $route);
        $route = new RegexRoute('seguimientos/ampliar-entrevista',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'ampliar'
                                ));
        $router->addRoute('seguimientosEntrevistasAmpliar', $route);
        $route = new RegexRoute('seguimientos/form-entrevista-respuestas',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'formEntrevistaRespuestas'
                                ));
        $router->addRoute('seguimientosEntrevistasFormEntrevistaRespuestas', $route);
        $route = new RegexRoute('seguimientos/form-entrevista-guardar-respuestas',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entrevistas',
                                        'action'     => 'formEntrevistaGuardarRespuestas'
                                ));
        $router->addRoute('seguimientosEntrevistasFormEntrevistaGuardarRespuestas', $route);

        //preguntas
        $route = new RegexRoute('seguimientos/listar-preguntas',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosPreguntasIndex', $route);
        $route = new RegexRoute('seguimientos/form-crear-pregunta',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'formCrearPregunta'
                                ));
        $router->addRoute('seguimientosPreguntasFormCrearPregunta', $route);
        $route = new RegexRoute('seguimientos/form-editar-pregunta',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'formEditarPregunta'
                                ));
        $router->addRoute('seguimientosPreguntasFormEditarPregunta', $route);
        $route = new RegexRoute('seguimientos/preguntas-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosPreguntasProcesar', $route);
        $route = new RegexRoute('seguimientos/guardar-pregunta',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'guardar'
                                ));
        $router->addRoute('seguimientosPreguntasGuardar', $route);
        $route = new RegexRoute('seguimientos/borrar-pregunta',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosPreguntasBorrar', $route);
        $route = new RegexRoute('seguimientos/borrar-opcion-pregunta',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'preguntas',
                                        'action'     => 'eliminarOpcion'
                                ));
        $router->addRoute('seguimientosPreguntasBorrarOpcion', $route);

        //objetivos
        $route = new RegexRoute('seguimientos/administrar-objetivos',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'administrarObjetivos'
                                ));
        $router->addRoute('seguimientosSeguimientosAdministrarObjetivos', $route);
        $route = new RegexRoute('seguimientos/objetivos-procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'procesarObjetivos'
                                ));
        $router->addRoute('seguimientosSeguimientosProcesarObjetivos', $route);
        $route = new RegexRoute('seguimientos/form-objetivo',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'formObjetivo'
                                ));
        $router->addRoute('seguimientosSeguimientosFormObjetivo', $route);
        $route = new RegexRoute('seguimientos/guardar-objetivo',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'guardarObjetivo'
                                ));
        $router->addRoute('seguimientosSeguimientosGuardarObjetivo', $route);
        $route = new RegexRoute('seguimientos/ver-objetivo',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'seguimientos',
                                        'action'     => 'verObjetivo'
                                ));
        $router->addRoute('seguimientosSeguimientosVerObjetivo', $route);

        //entradas
        $route = new RegexRoute('seguimientos/entradas/calendario',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosEntradasIndex', $route);
        $route = new RegexRoute('seguimientos/entradas/(\d+)-(.+)',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'ampliar'
                                ),
                                array(
                                    1 => 'iSeguimientoId',
                                    2 => 'sDate'
                                ),
                                '');
        $router->addRoute('seguimientosEntradasAmpliar', $route);
        $route = new RegexRoute('seguimientos/entradas/crear',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'crear'
                                ));
        $router->addRoute('seguimientosEntradasCrear', $route);
        $route = new RegexRoute('seguimientos/entradas/procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosEntradasProcesar', $route);
        $route = new RegexRoute('seguimientos/entradas/eliminar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'eliminar'
                                ));
        $router->addRoute('seguimientosEntradasEliminar', $route);
        $route = new RegexRoute('seguimientos/entradas/editar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'editar'
                                ));
        $router->addRoute('seguimientosEntradasEditar', $route);
        $route = new RegexRoute('seguimientos/entradas/guardar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'guardar'
                                ));
        $router->addRoute('seguimientosEntradasGuardar', $route);
        $route = new RegexRoute('seguimientos/entradas/entradas-unidad-esporadica',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'entradas',
                                        'action'     => 'entradasUnidadEsporadica'
                                ));
        $router->addRoute('seguimientosEntradasEntradasUnidadEsporadica', $route);

        //informes
        $route = new RegexRoute('seguimientos/informes',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'index'
                                ));
        $router->addRoute('seguimientosInformesIndex', $route);
        $route = new RegexRoute('seguimientos/informes/configuracion',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'configuracion'
                                ));
        $router->addRoute('seguimientosInformesConfiguracion', $route);
        $route = new RegexRoute('seguimientos/informes/configuracion-guardar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'guardarConfiguracion'
                                ));
        $router->addRoute('seguimientosInformesGuardarConfiguracion', $route);
        $route = new RegexRoute('seguimientos/informes/procesar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('seguimientosInformesProcesar', $route);
        $route = new RegexRoute('seguimientos/informes/confeccionar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'confeccionar'
                                ));
        $router->addRoute('seguimientosInformesConfeccionar', $route);
        $route = new RegexRoute('seguimientos/informes/generar',
                                array(
                                        'module' => 'seguimientos',
                                        'controller' => 'informes',
                                        'action'     => 'generar'
                                ));
        $router->addRoute('seguimientosInformesGenerar', $route);
    }
}

