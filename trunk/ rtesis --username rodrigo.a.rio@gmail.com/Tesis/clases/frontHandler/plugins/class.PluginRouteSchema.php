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

        $route = new RegexRoute('comunidad/descargar',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "index",
                                        'action'     => "descargarArchivo"
                                ));
        $router->addRoute('comunidadIndexDescargarArchivo', $route);
        
        //ajax
        $route = new RegexRoute('comunidad/modificarPrivacidadCampo',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "datosPersonales",
                                        'action'     => "modificarPrivacidadCampo"
                                ));
        $router->addRoute('comunidadModificarPrivacidadCampo', $route);
        
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
        
        //para el autocomplete de elegir institucion
        $route = new RegexRoute('comunidad/buscar-instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "buscarInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesBuscarInstituciones', $route);
       
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



        //PERSONAS
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
        $route = new RegexRoute('admin/buscar-especialidad',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'especialidad',
                                        'action'     => 'buscarEspecialidad'
                                ));
        $router->addRoute('adminEspecialidadBuscar', $route);
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
        $route = new RegexRoute('admin/buscar-categoria',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'categoria',
                                        'action'     => 'buscarCategoria'
                                ));
        $router->addRoute('adminCategoriaBuscar', $route);

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
        $route = new RegexRoute('admin/usuarios-imprimir',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'imprimir'
                                ));
        $router->addRoute('adminUsuariosImprimir', $route);
        $route = new RegexRoute('admin/usuarios-exportar',
                                array(
                                        'module' => 'admin',
                                        'controller' => 'usuarios',
                                        'action'     => 'exportar'
                                ));
        $router->addRoute('adminUsuariosExportar', $route);
    }
}