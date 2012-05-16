<?php

class RutasModuloComunidad
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
        $route = new RegexRoute('comunidad/modificarPrivacidadCampo',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "datosPersonales",
                                        'action'     => "modificarPrivacidadCampo"
                                ));
        $router->addRoute('comunidadModificarPrivacidadCampo', $route);
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
        $route = new RegexRoute('comunidad/buscar-instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "buscarInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesBuscarInstituciones', $route);
        $route = new RegexRoute('comunidad/cerrar-cuenta',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "datosPersonales",
                                    'action'     => "cerrarCuenta"
                                ));
        $router->addRoute('comunidadDatosPersonalesCerrarCuenta', $route);
    }
}