<?php

/**
 * Action Controller Index
 * 
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.
 */
class IndexControllerIndex extends PageControllerAbstract
{    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
        return $this;
    }

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
        $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "jsContent", "JsContent");
        return $this;
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setCabecera(Templates $template)
    {
        $request = FrontController::getInstance()->getRequest();
        
        //links menu ppal
        $template->set_var("hrefOpcionInicio", $request->getBaseUrl().'/');
        $template->set_var("hrefOpcionAcceder", $request->getBaseUrl().'/login');
        $template->set_var("hrefOpcionPublicaciones", $request->getBaseUrl().'/publicaciones');
        $template->set_var("hrefOpcionInstituciones", $request->getBaseUrl().'/instituciones');
        $template->set_var("hrefOpcionDescargas", $request->getBaseUrl().'/descargas');
        $template->set_var("hrefOpcionProyecto", $request->getBaseUrl().'/proyecto-sgpapd');
        $template->set_var("hrefOpcionGrupoTrabajo", $request->getBaseUrl().'/grupo-de-trabajo');
        $template->set_var("hrefOpcionContacto", $request->getBaseUrl().'/contacto');
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setFooter(Templates $template)
    {
        $request = FrontController::getInstance()->getRequest();

        $template->load_file_section("gui/vistas/index/home.gui.html", "footerContent", "FooterContent");
        
        //redes sociales
        $template->set_var("hrefDelicious", '#');
        $template->set_var("hrefDigg", '#');
        $template->set_var("hrefFacebook", '#');
        $template->set_var("hrefLinkedin", '#');
        $template->set_var("hrefMyspace", '#');
        $template->set_var("hrefReddit", '#');
        $template->set_var("hrefTwitter", '#');

        //ultimas 5 publicaciones
        $iRecordsTotal = 0;
        $aFichas = ComunidadController::getInstance()->buscarPublicacionesVisitantes($filtro = null, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = 1, $iItemsForPage = 5);
        if(count($aFichas) > 0){
            $template->set_var("UltimasPublicacionesNoRecordsBlock", "");
            foreach($aFichas as $oFicha){
                $oUsuario = $oFicha->getUsuario();
                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                $template->set_var("sTitulo", $oFicha->getTitulo());
                $template->set_var("sAutor", $sNombreUsuario);

                $oInflectorHelper = new InflectorHelper();
                $sTituloUrl = $oInflectorHelper->urlize($oFicha->getTitulo());
                if(get_class($oFicha) == 'Publicacion'){
                    $template->set_var("hrefAmpliarPublicacion", $request->getBaseUrl().'/publicaciones/'.$oFicha->getId()."-".$sTituloUrl);
                }else{
                    $template->set_var("hrefAmpliarPublicacion", $request->getBaseUrl().'/reviews/'.$oFicha->getId()."-".$sTituloUrl);
                }

                $template->parse("PublicacionRowBlock", true);
            }
        }else{
            $template->set_var("UltimasPublicacionesBlock", "");
        }
        
        //ultimas 5 instituciones
        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesVisitantes($filtro = null, $iRecordsTotal, $sOrderBy = 'i.id', $sOrder = 'desc', $iMinLimit = 1, $iItemsForPage = 5);
        if(count($aInstituciones) > 0){
            $template->set_var("UltimasInstitucionesNoRecordsBlock", "");
            foreach($aInstituciones as $oInstitucion){

                $template->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $template->set_var("sNombre", $oInstitucion->getNombre());

                $oInflectorHelper = new InflectorHelper();
                $sTituloUrl = $oInflectorHelper->urlize($oInstitucion->getNombre());
                $template->set_var("hrefAmpliarInstitucion", $request->getBaseUrl().'/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $template->parse("InstitucionRowBlock", true);
            }
        }else{
            $template->set_var("UltimasInstitucionesBlock", "");
        }

        //menu footer sub
        $template->set_var("hrefOpcionInicio", $request->getBaseUrl().'/');
        $template->set_var("hrefOpcionAcceder", $request->getBaseUrl().'/login');
        $template->set_var("hrefOpcionPublicaciones", $request->getBaseUrl().'/publicaciones');
        $template->set_var("hrefOpcionInstituciones", $request->getBaseUrl().'/instituciones');
        $template->set_var("hrefOpcionDescargas", $request->getBaseUrl().'/descargas');
        $template->set_var("hrefOpcionProyecto", $request->getBaseUrl().'/proyecto-sgpapd');
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();
            
            $this->setCabecera($this->getTemplate());
            $this->setFooter($this->getTemplate());

            $this->printMsgTop();

            //nombre seccion
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Inicio");

            if($this->getRequest()->get("mt")!=""){
            	$this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTop", "MsgDialogInfoBlockI32");
                $this->getTemplate()->set_var("sMensaje", "Su contrase&ntilde;a ha sido modificada.");
                $this->getTemplate()->parse('msgTop', false);
            }
            //contenido home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "centerPageContent", "HomeCenterPageBlock");
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
        
    /**
     * Muestra pagina de sitio en construccion
     */
    public function sitioEnConstruccion()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");
        
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->set_var("tituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("subtituloVista", "Estamos trabajando, muy pronto estaremos en línea");
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function sitioOffline()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     *  luego de cerrar sesion redirige a la home del sitio.
     *  Syscontroller solo destruye la sesion si el perfil es != visitante
     */
    public function cerrarSesion()
    {
        SysController::getInstance()->cerrarSesion();
        $this->getRedirectorHelper()->gotoUrl("/");
    }

    /**
     * Si existe $_GET['callback'] entonces quiere decir que hay que devolver Json (porque usamos Jquery en el ajax)
     * Si no existe callback asumimos que es una peticion ajax de html y devolvemos una ficha con mensaje de error
     */
    public function ajaxError()
    {
        $request = $this->getRequest();
        
        //extraigo mensaje si es que existe y el tipo de ficha (la ficha solo se usa si hay que devolver html)
        switch(true){
            case $request->has('msgInfo'):
            {
                $mensaje = $request->getParam('msgInfo');
                $ficha = "MsgInfoBlockI32";
                break;
            }
            case $request->has('msgError'):
            {
                $mensaje = $request->getParam('msgError');
                $ficha = "MsgErrorBlockI32";
                break;
            }
            default:
                $mensaje = "Ha ocurrido un error al procesar los datos";
                $ficha = "MsgInfoBlockI32";
                break;
        }
       
        if($request->has('callback')){
            //devuelvo error ajax en formato json
            $this->getJsonHelper()->initJsonAjaxResponse()
                                  ->setSuccess(false)
                                  ->setMessage($mensaje)
                                  ->sendJsonAjaxResponse();
        }else{
            //devuelvo error ajax en formato html
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "respuesta", $ficha);
            $this->getTemplate()->set_var("sMensaje", $mensaje);
            
            //setea los headers para response ajax html y setea el body content
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('respuesta', false));
        }       
    }

    /**
     * Vista ampliada de un video.. para utilizar con algun visor de javascript en modo iframe
     */
    public function video()
    {
        $sUrlKey = $this->getRequest()->getParam('v');
        $iEmbedVideoId = $this->getRequest()->getParam('id');

        if(empty($sUrlKey) || empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoUrlKey($iEmbedVideoId, $sUrlKey);

        if(null === $oEmbedVideo){
            throw new Exception("El video no existe", 404);
        }

        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "videoAmpliarHtml", "VideoAmpliarBlock");

        if(null !== $oEmbedVideo->getTitulo()){
            $this->getTemplate()->set_var("sTitulo", $oEmbedVideo->getTitulo());
        }else{
            $this->getTemplate()->set_var("TituloVideoAmpliarBlock", "");
        }

        $this->getTemplate()->set_var("sEmbedCode", $this->getEmbedVideoHelper()->getEmbedVideoCode($oEmbedVideo));

        if(null !== $oEmbedVideo->getDescripcion()){
            $this->getTemplate()->set_var("sDescripcion", $oEmbedVideo->getDescripcion(true));
        }else{
            $this->getTemplate()->set_var("DescripcionVideoAmpliarBlock", "");
        }

        $videoAmpliarHtml = $this->getTemplate()->pparse("videoAmpliarHtml");
        $this->getTemplate()->set_var("popUpContent", $videoAmpliarHtml);
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function provinciasByPais()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

    	try{
            $iPaisId = $this->getRequest()->getPost("iPaisId");

            $result = array();
            if($iPaisId != 0){
                $vListaProvincias = ComunidadController::getInstance()->listaProvinciasByPais($iPaisId);
                if(count($vListaProvincias)>0){
                    foreach($vListaProvincias as $oProvincia){
                        $obj = new stdClass();
                        $obj->id = $oProvincia->getId();
                        $obj->sNombre = $oProvincia->getNombre();
                        array_push($result,$obj);
                    }
                }
            }

            echo json_encode($result);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function ciudadesByProvincia()
    {
         if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
         
    	 try{
            $iProvinciaId =  $this->getRequest()->getPost("iProvinciaId");
            $result = array();
            if($iProvinciaId != 0){
                $vListaCiudades	= ComunidadController::getInstance()->listaCiudadByProvincia($iProvinciaId);
                foreach($vListaCiudades as $oCiudad){
                    $obj = new stdClass();
                    $obj->id = $oCiudad->getId();
                    $obj->sNombre = $oCiudad->getNombre();
                    array_push($result,$obj);
                }
            }

            echo json_encode($result);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
}
