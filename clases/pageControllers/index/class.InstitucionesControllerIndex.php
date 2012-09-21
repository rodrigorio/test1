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

    private function setHeadTagInstitucion($oInstitucion)
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');

        $tituloVista = $nombreSitio.' | '.$oInstitucion->getNombre();
        
        $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                      $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                      $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();        

        $descriptionVista = "Institución de ".$sUbicacion." relacionada con ".$oInstitucion->getNombreTipoInstitucion().".
                             Contacto a la dirección de email ".$oInstitucion->getEmail();

        $keywordsVista = $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getNombre().", ".$oInstitucion->getNombreTipoInstitucion();

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

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

        IndexControllerIndex::setCabecera($this->getTemplate());
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
        
        if($this->getRequest()->has('obtenerMarcas')){
            $this->obtenerMarcas();
            return;
        }           
    }

    /**
     * Devuelve un json de instituciones con latitud, longitud, y nubesita para el mapa.
     */
    public function obtenerMarcas()
    {
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
            $filtroSql['latLng'] = "latLng";

            $iRecordsTotal = 0;            
            $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesVisitantes($filtroSql, $iRecordsTotal, null, null, null, null);

            $aResult = array();
            if(count($aInstituciones)>0){
                foreach($aInstituciones as $oInstitucion){
                    $obj = new stdClass();
                    $obj->latitud = $oInstitucion->getLatitud();
                    $obj->longitud = $oInstitucion->getLongitud();
                    $obj->title = $oInstitucion->getNombre();

                    $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "ajaxInfoWindowMapaBlock", "InfoWindowMapaBlock");

                    $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                    $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());

                    $obj->info = $this->getTemplate()->pparse('ajaxInfoWindowMapaBlock', false);
                    $aResult[] = $obj;

                    $this->getTemplate()->delete_parsed_blocks("InfoWindowMapaBlock");
                }
            }

            $this->getJsonHelper()->setValor("marcas", $aResult);
         }catch(Exception $e){
            print_r($e);
        }

        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
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
                throw new Exception("", 404);
            }

            //validacion 3.
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
            if(!$oInstitucion->getModeracion()->isAprobado() || (count($oInstitucion->getDenuncias()) >= $iCantMaxDenuncias)){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("indexInstitucionesIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'instituciones/'.$oInstitucion->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //paso todas las validaciones muestro la vista

            $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
            $this->setHeadTagInstitucion($oInstitucion);

            $this->printMsgTop();
           
            //titulo seccion
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Instituciones");
            $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "topPageContent", "DescripcionSeccionBlock");

            IndexControllerIndex::setCabecera($this->getTemplate());
            IndexControllerIndex::setFooter($this->getTemplate());

            $this->getTemplate()->load_file_section("gui/vistas/index/instituciones.gui.html", "centerPageContent", "FichaInstitucionBlock");
            
            $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

            $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
            $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());            
            $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
            $this->getTemplate()->set_var("sUbicacion", $sUbicacion);                       
            $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

            if("" !== $oInstitucion->getActividadesMes(true)){
                $this->getTemplate()->set_var("sActividadesMes", $oInstitucion->getActividadesMes(true));
            }else{
                $this->getTemplate()->set_var("ActividadesMesBlock", "");                
            }
            
            if("" !== $oInstitucion->getAutoridades(true)){
                $this->getTemplate()->set_var("sAutoridades", $oInstitucion->getAutoridades(true));
            }else{
                $this->getTemplate()->set_var("AutoridadesBlock", "");
            }
            
            if("" !== $oInstitucion->getSedes(true)){
                $this->getTemplate()->set_var("sSedes", $oInstitucion->getSedes(true));
            }else{
                $this->getTemplate()->set_var("SedesBlock", "");
            }
            
            $sHorariosAtencion = (null !== $oInstitucion->getHorariosAtencion())? $oInstitucion->getHorariosAtencion() : " - ";                                   
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

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
     }    
}