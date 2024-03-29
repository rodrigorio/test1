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
        $route = new RegexRoute('comunidad/cerrar-cuenta',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "datosPersonales",
                                    'action'     => "cerrarCuenta"
                                ));
        $router->addRoute('comunidadDatosPersonalesCerrarCuenta', $route);


        
        //////////INSTITUCIONES
        $route = new RegexRoute('comunidad/editar-institucion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "instituciones",
                                    'action'     => "editarInstitucion"
                                ));
        $router->addRoute('comunidadInstitucionesEditarInstitucion', $route);
        $route = new RegexRoute('comunidad/instituciones/(\d+)-(.+)',
                                array(
                                    'module' => 'comunidad',
                                    'controller' => 'instituciones',
                                    'action'     => 'ampliarInstitucion'
                                ),
                                array(
                                    1 => 'iInstitucionId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('comunidadInstitucionesAmpliarInstitucion', $route);        
        $route = new RegexRoute('comunidad/masInstituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "masInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesMasInstituciones', $route);                
        $route = new RegexRoute('comunidad/nueva-institucion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "instituciones",
                                    'action'     => "nuevaInstitucion"
                                ));
        $router->addRoute('comunidadInstitucionesNueva', $route);
        $route = new RegexRoute('comunidad/instituciones/procesar',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "procesar"
                                ));
        $router->addRoute('comunidadInstitucionesProcesar', $route);
        $route = new RegexRoute('comunidad/instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "index"
                                ));
        $router->addRoute('comunidadInstitucionesIndex', $route);
        $route = new RegexRoute('comunidad/instituciones/mis-instituciones',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "instituciones",
                                    'action'     => "misInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesMisInstituciones', $route);
        $route = new RegexRoute('comunidad/instituciones/mas-mis-instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "masMisInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesMasMisInstituciones', $route);
        $route = new RegexRoute('comunidad/buscar-instituciones',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "buscarInstituciones"
                                ));
        $router->addRoute('comunidadInstitucionesBuscarInstituciones', $route);
        $route = new RegexRoute('comunidad/guardar-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "guardar"
                                ));
        $router->addRoute('comunidadInstitucionesGuardar', $route);
        $route = new RegexRoute('comunidad/guardar-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "guardar"
                                ));
        $router->addRoute('comunidadInstitucionesGuardar', $route);        
        $route = new RegexRoute('comunidad/denunciar-institucion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "instituciones",
                                        'action'     => "denunciar"
                                ));
        $router->addRoute('comunidadInstitucionesDenunciar', $route);

        
        /////////PUBLICACIONES
        $route = new RegexRoute('comunidad/publicaciones',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "index"
                                ));
        $router->addRoute('comunidadPublicacionesIndex', $route);
        $route = new RegexRoute('comunidad/publicaciones/mis-publicaciones',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "misPublicaciones"
                                ));
        $router->addRoute('comunidadPublicacionesMisPublicaciones', $route);
        $route = new RegexRoute('comunidad/publicaciones/form-nueva-publicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "crearPublicacionForm"
                                ));
        $router->addRoute('comunidadPublicacionesCrearPublicacionForm', $route);
        $route = new RegexRoute('comunidad/publicaciones/form-modificar-publicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "modificarPublicacionForm"
                                ));
        $router->addRoute('comunidadPublicacionesModificarPublicacionForm', $route);
        $route = new RegexRoute('comunidad/publicaciones/form-crear-review',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "crearReviewForm"
                                ));
        $router->addRoute('comunidadPublicacionesCrearReviewForm', $route);
        $route = new RegexRoute('comunidad/publicaciones/form-modificar-review',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "modificarReviewForm"
                                ));
        $router->addRoute('comunidadPublicacionesModificarReviewForm', $route);
        $route = new RegexRoute('comunidad/publicaciones/guardar-publicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "guardarPublicacion"
                                ));
        $router->addRoute('comunidadPublicacionesGuardarPublicacion', $route);
        $route = new RegexRoute('comunidad/publicaciones/guardar-review',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "guardarReview"
                                ));
        $router->addRoute('comunidadPublicacionesGuardarReview', $route);
        $route = new RegexRoute('comunidad/publicaciones/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "procesar"
                                ));
        $router->addRoute('comunidadPublicacionesProcesar', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-fotos',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "galeriaFotos"
                                ));
        $router->addRoute('comunidadPublicacionesGaleriaFotos', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-fotos/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "fotosProcesar"
                                ));
        $router->addRoute('comunidadPublicacionesFotosProcesar', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-fotos/form',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "formFoto"
                                ));
        $router->addRoute('comunidadPublicacionesFormFoto', $route);        
        $route = new RegexRoute('comunidad/publicaciones/galeria-archivos',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "galeriaArchivos"
                                ));
        $router->addRoute('comunidadPublicacionesGaleriaArchivos', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-archivos/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "archivosProcesar"
                                ));
        $router->addRoute('comunidadPublicacionesArchivosProcesar', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-archivos/form',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "formArchivo"
                                ));
        $router->addRoute('comunidadPublicacionesFormArchivo', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-videos',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "galeriaVideos"
                                ));
        $router->addRoute('comunidadPublicacionesGaleriaVideos', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-videos/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "videosProcesar"
                                ));
        $router->addRoute('comunidadPublicacionesVideosProcesar', $route);
        $route = new RegexRoute('comunidad/publicaciones/galeria-videos/form',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "publicaciones",
                                    'action'     => "formVideo"
                                ));
        $router->addRoute('comunidadPublicacionesFormVideo', $route);
        
        //publicacion ampliada comunidad.
        //http://domain.com/comunidad/publicaciones/32-Nombre de la publicacion
        //ó http://domain.com/comunidad/reviews/32-Nombre del review
        $route = new RegexRoute('comunidad/publicaciones/(\d+)-(.+)',
                                array(
                                    'module' => 'comunidad',
                                    'controller' => 'publicaciones',
                                    'action'     => 'verPublicacion'
                                ),
                                array(
                                    1 => 'iPublicacionId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('comunidadPublicacionesVerPublicacion', $route);
        $route = new RegexRoute('comunidad/reviews/(\d+)-(.+)',
                                array(
                                    'module' => 'comunidad',
                                    'controller' => 'publicaciones',
                                    'action'     => 'verReview'
                                ),
                                array(
                                    1 => 'iReviewId',
                                    2 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('comunidadPublicacionesVerReview', $route);
        $route = new RegexRoute('comunidad/denunciar-publicacion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "publicaciones",
                                        'action'     => "denunciar"
                                ));
        $router->addRoute('comunidadPublicacionesDenunciar', $route);


        /////////CATALOGO DESCARGAS
        $route = new RegexRoute('comunidad/descargas',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "index"
                                ));
        $router->addRoute('comunidadSoftwareIndex', $route);
        
        //'comunidad/descargas/nombre-categoria/' MUESTRA TODO EL SOFTWARE PARA ESA CATEGORIA
        $route = new RegexRoute('comunidad/descargas/(\S+)',
                                array(
                                    'module' => 'comunidad',
                                    'controller' => 'software',
                                    'action'     => 'listarCategoria'
                                ),
                                array(
                                    1 => 'sUrlToken'
                                ),
                                '');
        $router->addRoute('comunidadSoftwareListarCategoria', $route);

        $route = new RegexRoute('comunidad/descargas/mis-aplicaciones',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "misAplicaciones"
                                ));
        $router->addRoute('comunidadSoftwareMisAplicaciones', $route);
        $route = new RegexRoute('comunidad/descargas/form-nueva-aplicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "crearSoftwareForm"
                                ));
        $router->addRoute('comunidadSoftwareCrearSoftwareForm', $route);
        $route = new RegexRoute('comunidad/descargas/form-modificar-aplicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "modificarSoftwareForm"
                                ));
        $router->addRoute('comunidadSoftwareModificarSoftwareForm', $route);
        $route = new RegexRoute('comunidad/descargas/guardar-aplicacion',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "guardarSoftware"
                                ));
        $router->addRoute('comunidadSoftwareGuardarSoftware', $route);
        $route = new RegexRoute('comunidad/descargas/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "procesar"
                                ));
        $router->addRoute('comunidadSoftwareProcesar', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-fotos',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "galeriaFotos"
                                ));
        $router->addRoute('comunidadSoftwareGaleriaFotos', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-fotos/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "fotosProcesar"
                                ));
        $router->addRoute('comunidadSoftwareFotosProcesar', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-fotos/form',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "formFoto"
                                ));
        $router->addRoute('comunidadSoftwareFormFoto', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-archivos',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "galeriaArchivos"
                                ));
        $router->addRoute('comunidadSoftwareGaleriaArchivos', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-archivos/procesar',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "archivosProcesar"
                                ));
        $router->addRoute('comunidadSoftwareArchivosProcesar', $route);
        $route = new RegexRoute('comunidad/descargas/galeria-archivos/form',
                                array(
                                    'module' => "comunidad",
                                    'controller' => "software",
                                    'action'     => "formArchivo"
                                ));
        $router->addRoute('comunidadSoftwareFormArchivo', $route);

        //'comunidad/descargas/nombre-categoria/23-titulo-software'
        $route = new RegexRoute('comunidad/descargas/(\S+)\/(\d+)-(.+)',
                                array(
                                    'module' => 'comunidad',
                                    'controller' => 'software',
                                    'action'     => 'verSoftware'
                                ),
                                array(
                                    1 => 'sUrlToken',
                                    2 => 'iSoftwareId',
                                    3 => 'sTituloUrlized'
                                ),
                                '');
        $router->addRoute('comunidadSoftwareVerAplicacion', $route);
        $route = new RegexRoute('comunidad/denunciar-aplicacion',
                                array(
                                        'module' => "comunidad",
                                        'controller' => "software",
                                        'action'     => "denunciar"
                                ));
        $router->addRoute('comunidadSoftwareDenunciar', $route);
    }
}