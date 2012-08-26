<?php
/**
 * @author Rodrigo A. Rio
 */
class InstitucionesControllerIndex extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroNombre' => 'i.nombre',
                                       'filtroTipoInstitucion' => 'i.tipoInstitucion_id',
                                       'filtroPais' => 'pa.id',
                                       'filtroProvincia' => 'pr.id',
                                       'filtroCiudad' => 'i.ciudades_id');
           
    /**
     * Setea el Head para las vistas de Instituciones
     */
    private function setHeadTag()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        //js de home
        $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index()
    {
        $this->listar();
    }

    public function listar()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame01-02.gui.html", "frame");
        $this->setHeadTag();

        $this->printMsgTop();
        
        //titulo seccion
        $this->getTemplate()->set_var("sNombreSeccionTopPage", "Instituciones");

        $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "columnaIzquierdaContent", "ListadoInstitucionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "columnaDerechaContent", "BuscarInstitucionesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "topPageContent", "DescripcionSeccionBlock");

        IndexControllerIndex::setFooter($this->getTemplate());

        ///////////// ARMO LOS SELECTS DEL FORMULARIO DEL FILTRO
        $iRecordsTotalPais = 0;
        $listaPaises = ComunidadController::getInstance()->listaPaises($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($listaPaises as $oPais){
            $this->getTemplate()->set_var("iPaisId", $oPais->getId());
            $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
            $this->getTemplate()->parse("ListaPaisesBlock", true);
        }

        $vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($vListaInstitucionTipos as $oInstitucionTipo){
            $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipo->iId);
            $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipo->sNombre);
            $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
        }

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesVisitantes($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);                                
            }

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            $params[] = "masInstituciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "instituciones/procesar", "listadoInstitucionesResult", $params);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "Por el momento no hay instituciones disponibles");
            $this->getTemplate()->parse("NoRecordsInstitucionesBlock");
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    private function masInstituciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "ajaxFichasInstitucionesBlock", "FichasInstitucionesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesVisitantes($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);
            }

            $paramsPaginador[] = "masInstituciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "instituciones/procesar", "listadoInstitucionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron resultados");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasInstitucionesBlock', false));
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masInstituciones')){
            $this->masInstituciones();
            return;
        }     
    }

    public function ampliarInstitucion()
    {
    	try{
            $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
            $sTituloUrlized = $this->getRequest()->getParam('sTituloUrlized');

            //validacion 1.
            if(empty($iInstitucionId))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //validacion 2.
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            if(null === $oInstitucion)
            {
                $this->redireccion404();
                return;
            }

            //validacion 3.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());

            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            $this->setFrameTemplate()
                 ->setHeadTag()
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Instituciones Comunidad");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FichaInstitucionBlock");

            $oUsuarioSesion = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            if(null !== $oInstitucion->getUsuario())
            {
                $this->getTemplate()->set_var("SolicitarInstitucionBlock", "");
                $this->getTemplate()->set_var("SolicitudEnviadaInstitucionBlock", "");
            }else{
                if(ComunidadController::getInstance()->existeSolicitudInstitucion($iInstitucionId, $oUsuarioSesion->getId()))
                {
                    $this->getTemplate()->set_var("SolicitarInstitucionBlock", "");
                }else{
                    $this->getTemplate()->set_var("SolicitudEnviadaInstitucionBlock", "");
                }
            }

            $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();


            $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
            $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());            
            $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
            $this->getTemplate()->set_var("sUbicacion", $sUbicacion);                       
            $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

            $sActividadesMes = (null !== $oInstitucion->getActividadesMes(true))? $oInstitucion->getActividadesMes(true) : " - ";
            $sAutoridades = (null !== $oInstitucion->getAutoridades(true))? $oInstitucion->getAutoridades(true) : " - ";
            $sSedes = (null !== $oInstitucion->getSedes(true))? $oInstitucion->getSedes(true) : " - ";
            $sHorariosAtencion = (null !== $oInstitucion->getHorariosAtencion())? $oInstitucion->getHorariosAtencion() : " - ";

            $this->getTemplate()->set_var("sActividadesMes", $sActividadesMes);
            $this->getTemplate()->set_var("sAutoridades", $sAutoridades);
            $this->getTemplate()->set_var("sSedes", $sSedes);
            $this->getTemplate()->set_var("sHorariosAtencion", $sHorariosAtencion);
            $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());
            $this->getTemplate()->set_var("sTelefono", $oInstitucion->getTelefono());            
            $this->getTemplate()->set_var("sDireccion", $oInstitucion->getDireccion());
            
            if(null !== $oInstitucion->getSitioWeb()){
                $this->getTemplate()->set_var("sSitioWeb", $oInstitucion->getSitioWeb());
            }else{
                $this->getTemplate()->set_var("SitioWebBlock", "");                                
            }

            if(null === $oInstitucion->getLatitud() && null === $oInstitucion->getLongitud()){
                $this->getTemplate()->set_var("MapaInstitucionBlock", "");                
            }else{
                $this->getTemplate()->set_var("sLatitud", $oInstitucion->getLatitud());
                $this->getTemplate()->set_var("sLongitud", $oInstitucion->getLongitud());
            }

            //listado de integrantes asociados a la institucion
            $aUsuarios = ComunidadController::getInstance()->obtenerUsuariosAsociadosInstitucion($iInstitucionId);
            
            if(count($aUsuarios) > 0){
                $this->getTemplate()->set_var("IntegranteNoRecords", "");
                
                foreach($aUsuarios as $oUsuario){

                    //foto de perfil actual
                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                    if(null != $oUsuario->getFotoPerfil()){
                        $oFoto = $oUsuario->getFotoPerfil();
                        $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                        $this->getTemplate()->set_var("hrefFotoPerfil", $pathFotoServidorBigSize);
                    }else{
                        $this->getTemplate()->set_var("hrefFotoPerfil", $scrAvatarAutor);
                    }                                        
                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);

                    $sNombreUsuario = $oUsuario->getNombre()." ".$oUsuario->getApellido();
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("sEmail", $oUsuario->getEmail());

                    $aPrivacidad = $oUsuario->obtenerPrivacidad();

                    if($aPrivacidad['telefono'] == 'comunidad' && null !== $oUsuario->getTelefono()){
                        $this->getTemplate()->set_var("sTelefono", $oUsuario->getTelefono());
                        $this->getTemplate()->parse("TelefonoBlock");
                    }else{
                        $this->getTemplate()->set_var("TelefonoBlock", "");
                    }

                    if($aPrivacidad['celular'] == 'comunidad' && null !== $oUsuario->getCelular()){
                        $this->getTemplate()->set_var("sCelular", $oUsuario->getCelular());
                        $this->getTemplate()->parse("CelularBlock");                        
                    }else{
                        $this->getTemplate()->set_var("CelularBlock", "");
                    }

                    if($aPrivacidad['fax'] == 'comunidad' && null !== $oUsuario->getFax()){
                        $this->getTemplate()->set_var("sFax", $oUsuario->getFax());
                        $this->getTemplate()->parse("FaxBlock");                        
                    }else{
                        $this->getTemplate()->set_var("FaxBlock", "");
                    }

                    if($aPrivacidad['curriculum'] == 'comunidad' && null !== $oUsuario->getCurriculumVitae()){
                        $hrefDescargarCv = "";
                        $oArchivo = $oUsuario->getCurriculumVitae();
                        $hrefDescargarCv = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
                        $this->getTemplate()->set_var("hrefDescargarCv", $hrefDescargarCv);
                        $this->getTemplate()->parse("CvBlock");
                    }else{
                        $this->getTemplate()->set_var("CvBlock", "");
                    }
                    
                    $this->getTemplate()->parse("IntegranteBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("IntegranteBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e);
        }
     }    
}