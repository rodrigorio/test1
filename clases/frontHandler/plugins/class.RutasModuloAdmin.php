<?php

class RutasModuloAdmin
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
        
        $route = new RegexRoute('admin/home',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'index',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminIndexIndex', $route);

        //especialidades
        $route = new RegexRoute('admin/procesar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'procesarEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadProcesarEspecialidad', $route);
        $route = new RegexRoute('admin/listar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminEspecialidadListarEspecialidades', $route);
        $route = new RegexRoute('admin/nueva-especialidad',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'especialidad',
                                    'action'     => 'nuevaEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadNuevaEspecialidad', $route);
        $route = new RegexRoute('admin/editar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'editarEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadEditarEspecialidad', $route);
        $route = new RegexRoute('admin/administrar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminEspecialidadAdministrarEspecialidad', $route);
        $route = new RegexRoute('admin/eliminar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'eliminarEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadEliminarEspecialidad', $route);
        $route = new RegexRoute('admin/verfificar-uso-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'verificarUsoDeEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadVerificarUsoDeEspecialidad', $route);


        //categorias
        $route = new RegexRoute('admin/procesar-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'procesarCategoria'
                                ));
        $router->addRoute('adminCategoriaProcesarCategoria', $route);
        $route = new RegexRoute('admin/listar-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminCategoriaListarCategoriaes', $route);
        $route = new RegexRoute('admin/nueva-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'nuevaCategoria'
                                ));
        $router->addRoute('adminCategoriaNuevaCategoria', $route);
        $route = new RegexRoute('admin/editar-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'editarCategoria'
                                ));
        $router->addRoute('adminCategoriaEditarCategoria', $route);
        $route = new RegexRoute('admin/administrar-categorias',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminCategoriaAdministrarCategoria', $route);
        $route = new RegexRoute('admin/eliminar-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'eliminarCategoria'
                                ));
        $router->addRoute('adminCategoriaEliminarCategoria', $route);
        $route = new RegexRoute('admin/verfificar-uso-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'verificarUsoDeCategoria'
                                ));
        $router->addRoute('adminCategoriaVerificarUsoDeCategoria', $route);


        //personas
        $route = new RegexRoute('admin/personas-moderacion',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'personas',
                                        'action'     => 'listarModeracionesPendientes'
                                ));
        $router->addRoute('adminListarModeraciones', $route);
        $route = new RegexRoute('admin/personas-moderacion-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'personas',
                                        'action'     => 'procesarModeracion'
                                ));
        $router->addRoute('adminProcesarModeracion', $route);
        $route = new RegexRoute('admin/personas',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'personas',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminPersonasListar', $route);
        $route = new RegexRoute('admin/personas-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'personas',
                                        'action'     => 'procesarPersona'
                                ));
        $router->addRoute('adminPersonasProcesar', $route);



        //instituciones
        $route = new RegexRoute('admin/instituciones',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminInstitucionesListar', $route);
        $route = new RegexRoute('admin/instituciones-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminInstitucionesProcesar', $route);
        $route = new RegexRoute('admin/instituciones-moderacion',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'listarModeraciones'
                                ));
        $router->addRoute('adminInstitucionesModerar', $route);
        $route = new RegexRoute('admin/instituciones-denuncias',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'listarDenuncias'
                                ));
        $router->addRoute('adminInstitucionesDenuncias', $route);
        $route = new RegexRoute('admin/instituciones-denuncias-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'procesarDenuncias'
                                ));
        $router->addRoute('adminInstitucionesProcesarDenuncias', $route);
        $route = new RegexRoute('admin/instituciones-solicitudes',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'instituciones',
                                        'action'     => 'listarSolicitudes'
                                ));                        
        $router->addRoute('adminInstitucionesSolicitudes', $route);
        $route = new RegexRoute('admin/instituciones-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'instituciones',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminInstitucionesForm', $route);
        
        //acciones perfil
        $route = new RegexRoute('admin/acciones-perfil',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'accionesPerfil',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminAccionesPerfilIndex', $route);
        $route = new RegexRoute('admin/acciones-perfil-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'accionesPerfil',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminAccionesPerfilProcesar', $route);
        $route = new RegexRoute('admin/acciones-perfil-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'accionesPerfil',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminAccionesPerfilForm', $route);

        //usuarios
        $route = new RegexRoute('admin/usuarios',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminUsuariosIndex', $route);
        $route = new RegexRoute('admin/usuarios-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminUsuariosProcesar', $route);
        $route = new RegexRoute('admin/usuarios-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'usuarios',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminUsuariosForm', $route);
        $route = new RegexRoute('admin/usuarios-cambiar-perfil',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'cambiarPerfil'
                                ));
        $router->addRoute('adminUsuariosCambiarPerfil', $route);
        $route = new RegexRoute('admin/usuarios-cerrar-cuenta',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'cerrarCuenta'
                                ));
        $router->addRoute('adminUsuariosCerrarCuenta', $route);
        $route = new RegexRoute('admin/usuarios-crear',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'crear'
                                ));
        $router->addRoute('adminUsuariosCrear', $route);
        $route = new RegexRoute('admin/usuarios-vista-impresion',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'vistaImpresion'
                                ));
        $router->addRoute('adminUsuariosVistaImpresion', $route);
        $route = new RegexRoute('admin/usuarios-exportar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'exportar'
                                ));
        $router->addRoute('adminUsuariosExportar', $route);

        //publicaciones
        $route = new RegexRoute('admin/publicaciones',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'publicaciones',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminPublicacionesIndex', $route);
        $route = new RegexRoute('admin/publicaciones-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'publicaciones',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminPublicacionesProcesar', $route);
        $route = new RegexRoute('admin/publicaciones-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'publicaciones',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminPublicacionesForm', $route);
        $route = new RegexRoute('admin/publicaciones-moderacion',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'publicaciones',
                                    'action'     => 'listarModeraciones'
                                ));
        $router->addRoute('adminPublicacionesModerar', $route);
        $route = new RegexRoute('admin/publicaciones-denuncias',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'publicaciones',
                                        'action'     => 'listarDenuncias'
                                ));
        $router->addRoute('adminPublicacionesDenuncias', $route);
        $route = new RegexRoute('admin/publicaciones-denuncias-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'publicaciones',
                                        'action'     => 'procesarDenuncias'
                                ));
        $router->addRoute('adminPublicacionesProcesarDenuncias', $route);


        //software
        $route = new RegexRoute('admin/software',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'software',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminSoftwareIndex', $route);
        $route = new RegexRoute('admin/software-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'software',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminSoftwareProcesar', $route);
        $route = new RegexRoute('admin/software-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'software',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminSoftwareForm', $route);
        $route = new RegexRoute('admin/software-moderacion',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'software',
                                    'action'     => 'listarModeraciones'
                                ));
        $router->addRoute('adminSoftwareModerar', $route);
        $route = new RegexRoute('admin/software-denuncias',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'software',
                                        'action'     => 'listarDenuncias'
                                ));
        $router->addRoute('adminSoftwareDenuncias', $route);
        $route = new RegexRoute('admin/software-denuncias-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'software',
                                        'action'     => 'procesarDenuncias'
                                ));
        $router->addRoute('adminSoftwareProcesarDenuncias', $route);

        //parametros sistema
        $route = new RegexRoute('admin/parametros',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'parametros',
                                        'action'     => 'index'
                                ));
        $router->addRoute('adminParametrosIndex', $route);
        $route = new RegexRoute('admin/parametros-usuario',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'parametros',
                                        'action'     => 'listarParametrosUsuario'
                                ));
        $router->addRoute('adminParametrosListarParametrosUsuario', $route);
        $route = new RegexRoute('admin/parametros-procesar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'parametros',
                                        'action'     => 'procesar'
                                ));
        $router->addRoute('adminParametrosProcesar', $route);
        $route = new RegexRoute('admin/parametros-form',
                                array(
                                    'module' => 'admin',
                                    'controller' => 'parametros',
                                    'action'     => 'form'
                                ));
        $router->addRoute('adminParametrosForm', $route);


        //niveles
        $route = new RegexRoute('admin/procesar-nivel',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'procesarNivel'
                                ));
        $router->addRoute('adminObjetivosCurricularesProcesarNivel', $route);
        $route = new RegexRoute('admin/listar-niveles',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'listarNiveles'
                                ));
        $router->addRoute('adminObjetivosCurricularesListarNiveles', $route);
        $route = new RegexRoute('admin/form-nivel',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'formularioNivel'
                                ));
        $router->addRoute('adminObjetivosCurricularesFormularioNivel', $route);

        //ciclos
        $route = new RegexRoute('admin/procesar-ciclo',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'procesarCiclo'
                                ));
        $router->addRoute('adminObjetivosCurricularesProcesarCiclo', $route);
        $route = new RegexRoute('admin/listar-ciclos',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'listarCiclos'
                                ));
        $router->addRoute('adminObjetivosCurricularesListarCiclos', $route);
        $route = new RegexRoute('admin/form-ciclo',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'formularioCiclo'
                                ));
        $router->addRoute('adminObjetivosCurricularesFormularioCiclo', $route);

        //areas
        $route = new RegexRoute('admin/procesar-area',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'procesarArea'
                                ));
        $router->addRoute('adminObjetivosCurricularesProcesarArea', $route);
        $route = new RegexRoute('admin/listar-areas',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'listarAreas'
                                ));
        $router->addRoute('adminObjetivosCurricularesListarAreas', $route);
        $route = new RegexRoute('admin/form-area',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'objetivosCurriculares',
                                        'action'     => 'formularioArea'
                                ));
        $router->addRoute('adminObjetivosCurricularesFormularioArea', $route);
    }
}