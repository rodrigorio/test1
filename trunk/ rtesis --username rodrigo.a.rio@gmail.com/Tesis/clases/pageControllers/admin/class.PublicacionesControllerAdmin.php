<?php

class PublicacionesControllerAdmin extends PageControllerAbstract
{
    //el de fecha se hace automatico en la funcion initFiltrosForm
    private $filtrosFormConfig = array('filtroTitulo' => 'f.titulo',
                                       'filtroApellidoAutor' => 'ap.apellido',
                                       'filtroFechaDesde' => 'fechaDesde',
                                       'filtroFechaHasta' => 'fechaHasta');

    private $orderByConfig = array('autor' => array('variableTemplate' => 'orderByAutor',
                                                    'orderBy' => 'ap.apellido',
                                                    'order' => 'desc'),
                                   'titulo' => array('variableTemplate' => 'orderByTitulo',
                                                     'orderBy' => 'f.titulo',
                                                     'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 'f.fecha',
                                                    'order' => 'desc'),
                                   'activo' => array('variableTemplate' => 'orderByActivo',
                                                   'orderBy' => 'f.activo',
                                                   'order' => 'desc'));
    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/admin/frame01-02.gui.html", "frame");
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listar();
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionPublicaciones");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "mainContent", "ListadoPublicacionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aFichas = AdminController::getInstance()->buscarPublicacionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aFichas) > 0){

                foreach($aFichas as $oFicha){

                    $oUsuario = $oFicha->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();
                    
                    $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                    $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);

                    if($oFicha->isActivo()){
                        $this->getTemplate()->set_var("sSelectedPublicacionActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());                    
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());

                    $this->getTemplate()->parse("PublicacionBlock", true);
                    $this->getTemplate()->set_var("sSelectedPublicacionActivo", "");
                    $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "");
                }

                $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");
                
            }else{
                $this->getTemplate()->set_var("PublicacionBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "noRecords", "NoRecordsPublicacionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay publicaciones cargadas en la comunidad");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/publicaciones-procesar", "listadoPublicacionesResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Por ahora solo se puede modificar y eliminar, si queres crear publicacion vas al modulo comunidad
     */
    public function form()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iPublicacionId = $this->getRequest()->getPost('iPublicacionId');
        $objType = $this->getRequest()->getPost('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        switch($objType)
        {
            case "publicacion":
                $this->formModificarPublicacion($iPublicacionId);
                break;
            case "review":
                $this->formModificarReview($iPublicacionId);
                break;
        }        
    }

    private function formModificarPublicacion($iPublicacionId)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "popUpContent", "FormularioPublicacionBlock");

        $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);

        $this->getTemplate()->set_var("iPublicacionIdForm", $iPublicacionId);

        $sTitulo = $oPublicacion->getTitulo();
        $sDescripcionBreve = $oPublicacion->getDescripcionBreve();
        $bActivoComentarios = $oPublicacion->isActivoComentarios();
        $bActivo = $oPublicacion->isActivo();
        $bPublico = $oPublicacion->isPublico();
        $sDescripcion = $oPublicacion->getDescripcion();
        $sKeywords = $oPublicacion->getKeywords();

        if($bActivo){
            $this->getTemplate()->set_var("sSelectedActivo", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivado", "selected='selected'");
        }

        if($bPublico){
            $this->getTemplate()->set_var("sSelectedPublico", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedComunidad", "selected='selected'");
        }

        if($bActivoComentarios){
            $this->getTemplate()->set_var("sSelectedActivoComentarios", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivadoComentarios", "selected='selected'");
        }
       
        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sKeywords", $sKeywords);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function formModificarReview($iReviewIdForm)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "popUpContent", "FormularioReviewBlock");

        $oReview = ComunidadController::getInstance()->getReviewById($iReviewIdForm);
        $this->getTemplate()->set_var("iReviewIdForm", $iReviewIdForm);

        $sTitulo = $oReview->getTitulo();
        $sDescripcionBreve = $oReview->getDescripcionBreve();
        $bActivoComentarios = $oReview->isActivoComentarios();
        $bActivo = $oReview->isActivo();
        $bPublico = $oReview->isPublico();
        $sDescripcion = $oReview->getDescripcion();
        $sKeywords = $oReview->getKeywords();

        $sItemType = $oReview->getItemType();
        $sItemName = $oReview->getItemName();
        $sItemEventSummary = $oReview->getItemEventSummary();
        $sItemUrl = $oReview->getItemUrl();
        $fRating = $oReview->getRating();
        $sFuenteOriginal = $oReview->getFuenteOriginal();

        if($bActivo){
            $this->getTemplate()->set_var("sSelectedActivo", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivado", "selected='selected'");
        }

        if($bPublico){
            $this->getTemplate()->set_var("sSelectedPublico", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedComunidad", "selected='selected'");
        }

        if($bActivoComentarios){
            $this->getTemplate()->set_var("sSelectedActivoComentarios", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedDesactivadoComentarios", "selected='selected'");
        }

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sKeywords", $sKeywords);

        switch($sItemType){
            case "product":
                $this->getTemplate()->set_var("sSelectedProduct", "selected='selected'");
                break;
            case "business":
                $this->getTemplate()->set_var("sSelectedBusiness", "selected='selected'");
                break;
            case "event":
                $this->getTemplate()->set_var("sSelectedEvent", "selected='selected'");
                break;
            case "person":
                $this->getTemplate()->set_var("sSelectedPerson", "selected='selected'");
                break;
            case "place":
                $this->getTemplate()->set_var("sSelectedPlace", "selected='selected'");
                break;
            case "website":
                $this->getTemplate()->set_var("sSelectedWebsite", "selected='selected'");
                break;
            case "url":
                $this->getTemplate()->set_var("sSelectedUrl", "selected='selected'");
                break;
        }

        switch($fRating){
            case ($fRating >= 0 && $fRating < 0.5):
                $this->getTemplate()->set_var("sSelected_0", "selected='selected'");
                break;
            case ($fRating >= 0.5 && $fRating < 1):
                $this->getTemplate()->set_var("sSelected_05", "selected='selected'");
                break;
            case ($fRating >= 1 && $fRating < 1.5):
                $this->getTemplate()->set_var("sSelected_1", "selected='selected'");
                break;
            case ($fRating >= 1.5 && $fRating < 2):
                $this->getTemplate()->set_var("sSelected_15", "selected='selected'");
                break;
            case ($fRating >= 2 && $fRating < 2.5):
                $this->getTemplate()->set_var("sSelected_2", "selected='selected'");
                break;
            case ($fRating >= 2.5 && $fRating < 3):
                $this->getTemplate()->set_var("sSelected_25", "selected='selected'");
                break;
            case ($fRating >= 3 && $fRating < 3.5):
                $this->getTemplate()->set_var("sSelected_3", "selected='selected'");
                break;
            case ($fRating >= 3.5 && $fRating < 4):
                $this->getTemplate()->set_var("sSelected_35", "selected='selected'");
                break;
            case ($fRating >= 4 && $fRating < 4.5):
                $this->getTemplate()->set_var("sSelected_4", "selected='selected'");
                break;
            case ($fRating >= 4.5 && $fRating < 5):
                $this->getTemplate()->set_var("sSelected_45", "selected='selected'");
                break;
            case ($fRating >= 5):
                $this->getTemplate()->set_var("sSelected_5", "selected='selected'");
                break;
        }

        $this->getTemplate()->set_var("sItemEventSummary", $sItemEventSummary);
        $this->getTemplate()->set_var("sItemName", $sItemName);
        $this->getTemplate()->set_var("sItemUrl", $sItemUrl);
        $this->getTemplate()->set_var("sFuenteOriginal", $sFuenteOriginal);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    public function procesar()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masPublicaciones')){
            $this->masPublicaciones();
            return;
        }

        if($this->getRequest()->has('masModeraciones')){
            $this->masModeraciones();
            return;
        }

        if($this->getRequest()->has('cambiarEstado')){
            $this->cambiarEstadoPublicacion();
            return;
        }

        if($this->getRequest()->has('borrarPublicacion')){
            $this->borrarPublicacion();
            return;
        }

        if($this->getRequest()->has('modificarPublicacion')){
            $this->modificarPublicacion();
            return;
        }

        if($this->getRequest()->has('modificarReview')){
            $this->modificarReview();
            return;
        }
        
        if($this->getRequest()->has('ampliarPublicacion')){
            $this->ampliar();
            return;
        }

        if($this->getRequest()->has('eliminarComentario')){
            $this->eliminarComentario();
            return;
        }
        
        if($this->getRequest()->has('moderarPublicacion')){
            $this->moderarPublicacion();
            return;
        }
        
        if($this->getRequest()->has('toggleModeraciones')){
            $this->toggleModeraciones();
            return;
        }

        //adjuntos en publicacion ampliada 
        if($this->getRequest()->has('eliminarArchivo')){
            $this->eliminarArchivo();
            return;
        }
        if($this->getRequest()->has('eliminarVideo')){
            $this->eliminarVideo();
            return;
        }
        if($this->getRequest()->has('eliminarFoto')){
            $this->eliminarFoto();
            return;
        }
        if($this->getRequest()->has('formArchivo')){
            $this->formArchivo();
            return;
        }
        if($this->getRequest()->has('formVideo')){
            $this->formVideo();
            return;
        }
        if($this->getRequest()->has('formFoto')){
            $this->formFoto();
            return;
        }        
        if($this->getRequest()->has('guardarFoto')){
            $this->guardarFoto();
            return;
        }
        if($this->getRequest()->has('guardarVideo')){
            $this->guardarVideo();
            return;
        }
        if($this->getRequest()->has('guardarArchivo')){
            $this->guardarArchivo();
            return;
        }
    }

    private function moderarPublicacion()
    {        
        $iModeracionId = $this->getRequest()->getParam('iModeracionId');
        $sEstado = $this->getRequest()->getParam('estado');
        $sMensaje = $this->getRequest()->getParam('mensaje');

        if(empty($iModeracionId) || empty($sEstado) || empty($sMensaje)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oModeracion = AdminController::getInstance()->getModeracionById($iModeracionId);

            switch($sEstado)
            {
                case "aprobado": $oModeracion->setEstadoAprobado(); break;
                case "rechazado": $oModeracion->setEstadoRechazado(); break;
            }

            $oModeracion->setMensaje($sMensaje);

            $result = AdminController::getInstance()->guardarModeracion($oModeracion);

            $this->restartTemplate();

            if($result){
                $msg = "La publicación fue moderada";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha procesado la moderacion en la publicación";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    /**
     * Modifica el valor booleano del parametro activar moderaciones
     * para el controlador publicaciones del modulo comunidad
     */
    private function toggleModeraciones()
    {
        $sValor = $this->getRequest()->getParam('sValor');

        //si o si tiene que ser boolean
        if($sValor != '1' && $sValor != '0'){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oParametroControlador = AdminController::getInstance()->getParametroControladorByNombre('ACTIVAR_MODERACIONES', 'comunidad_publicaciones');
        $oParametroControlador->setValor($sValor);
        AdminController::getInstance()->guardarParametroControlador($oParametroControlador);
    }

    private function eliminarComentario()
    {
        $iComentarioId = $this->getRequest()->getParam('iComentarioId');

        if(empty($iComentarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            ComunidadController::getInstance()->borrarComentario($iComentarioId);
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function ampliar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iPublicacionId = $this->getRequest()->getPost('iPublicacionId');
        $objType = $this->getRequest()->getPost('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        switch($objType)
        {
            case "publicacion":
                $this->ampliarPublicacion($iPublicacionId);
                break;
            case "review":
                $this->ampliarReview($iPublicacionId);
                break;
        }          
    }

    private function ampliarPublicacion($iPublicacionId)
    {
        try{
            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);

            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oPublicacion->getTitulo());
            $sPermalink = 'comunidad/publicaciones/'.$oPublicacion->getId()."-".$sTituloUrlizedActual;

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "popUpContent", "FichaPublicacionBlock");

            $oUsuarioAutor = $oPublicacion->getUsuario();
            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();

            $sActiva = ($oPublicacion->isActivo())?"Si":"No";
            $sPrivacidad = ($oPublicacion->isPublico())?"El Mundo":"Comunidad";
            $sActivoComentarios = ($oPublicacion->isActivoComentarios())?"Si":"No";
                        
            $this->getTemplate()->set_var("sTitulo", $oPublicacion->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oPublicacion->getFecha());
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sActiva", $sActiva);
            $this->getTemplate()->set_var("sPrivacidad", $sPrivacidad);
            $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);
            $this->getTemplate()->set_var("sDescripcionBreve", $oPublicacion->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oPublicacion->getDescripcion(true));
            $this->getTemplate()->set_var("sPermalink", $sPermalink);

            $this->agregarComentariosAmpliarFicha($oPublicacion);

            $this->agregarAdjuntosAmpliarFicha($oPublicacion);
            
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }            
    }

    private function ampliarReview($iReviewId)
    {
        try{
            $oReview = ComunidadController::getInstance()->getReviewById($iReviewId);

            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oReview->getTitulo());
            $sPermalink = 'comunidad/reviews/'.$oReview->getId()."-".$sTituloUrlizedActual;

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "popUpContent", "FichaReviewBlock");

            $oUsuarioAutor = $oReview->getUsuario();
            $sNombreAutor = $oUsuarioAutor->getApellido()." ".$oUsuarioAutor->getNombre();

            $sActiva = ($oReview->isActivo())?"Si":"No";
            $sPrivacidad = ($oReview->isPublico())?"El Mundo":"Comunidad";
            $sActivoComentarios = ($oReview->isActivoComentarios())?"Si":"No";

            $this->getTemplate()->set_var("sTitulo", $oReview->getTitulo());
            $this->getTemplate()->set_var("sFecha", $oReview->getFecha());
            $this->getTemplate()->set_var("sAutor", $sNombreAutor);
            $this->getTemplate()->set_var("sActiva", $sActiva);
            $this->getTemplate()->set_var("sPrivacidad", $sPrivacidad);
            $this->getTemplate()->set_var("sActivoComentarios", $sActivoComentarios);
            $this->getTemplate()->set_var("sDescripcionBreve", $oReview->getDescripcionBreve());
            $this->getTemplate()->set_var("sDescripcion", $oReview->getDescripcion(true));
            $this->getTemplate()->set_var("sPermalink", $sPermalink);

            //detalles review
            $this->getTemplate()->set_var("sItemName", $oReview->getItemName());

            if(null !== $oReview->getItemType()){
                $sItemType = "";

                switch($oReview->getItemType()){
                    case "product":
                        $sItemType = "producto"; break;
                    case "business":
                        $sItemType = "negocio"; break;
                    case "event":
                        $sItemType = "evento"; break;
                    case "person":
                        $sItemType = "persona"; break;
                    case "place":
                        $sItemType = "lugar"; break;
                    case "website":
                        $sItemType = "sitio web"; break;
                    case "url":
                        $sItemType = "link"; break;
                }

                $this->getTemplate()->set_var("sItemType", $sItemType);
                $this->getTemplate()->parse("ItemTypeBlock");
            }else{
                $this->getTemplate()->set_var("ItemTypeBlock", "");
            }

            if(null !== $oReview->getItemEventSummary()){
                $this->getTemplate()->set_var("sItemEventSummary", $oReview->getItemEventSummary());
                $this->getTemplate()->parse("ItemEventSummaryBlock");
            }else{
                $this->getTemplate()->set_var("ItemEventSummaryBlock", "");
            }

            if(null !== $oReview->getItemUrl()){
                $this->getTemplate()->set_var("hrefItemUrl", $oReview->getItemUrl());
                $this->getTemplate()->set_var("sItemUrl", $oReview->getItemUrl());
                $this->getTemplate()->parse("ItemUrlBlock");
            }else{
                $this->getTemplate()->set_var("ItemUrlBlock", "");
            }

            if(null !== $oReview->getRating()){

                $fValoracion = $oReview->getRating();

                switch($fValoracion){
                    case ($fValoracion >= 0 && $fValoracion < 0.5): $valoracionBloque = 'Valoracion0Block'; break;
                    case ($fValoracion >= 0.5 && $fValoracion < 1): $valoracionBloque = 'Valoracion0_2Block'; break;
                    case ($fValoracion >= 1 && $fValoracion < 1.5): $valoracionBloque = 'Valoracion1Block'; break;
                    case ($fValoracion >= 1.5 && $fValoracion < 2): $valoracionBloque = 'Valoracion1_2Block'; break;
                    case ($fValoracion >= 2 && $fValoracion < 2.5): $valoracionBloque = 'Valoracion2Block'; break;
                    case ($fValoracion >= 2.5 && $fValoracion < 3): $valoracionBloque = 'Valoracion2_2Block'; break;
                    case ($fValoracion >= 3 && $fValoracion < 3.5): $valoracionBloque = 'Valoracion3Block'; break;
                    case ($fValoracion >= 3.5 && $fValoracion < 4): $valoracionBloque = 'Valoracion3_2Block'; break;
                    case ($fValoracion >= 4 && $fValoracion < 4.5): $valoracionBloque = 'Valoracion4Block'; break;
                    case ($fValoracion >= 4.5 && $fValoracion < 5): $valoracionBloque = 'Valoracion4_2Block'; break;
                    case ($fValoracion >= 5): $valoracionBloque = 'Valoracion5Block'; break;
                    default: $valoracionBloque = 'Valoracion0Block'; break;
                }

                $this->getTemplate()->load_file_section("gui/componentes/valoracion.gui.html", "valoracion", $valoracionBloque);
                $valoracion = $this->getTemplate()->pparse("valoracion");                
                $this->getTemplate()->set_var("valoracion", $valoracion);
                $this->getTemplate()->parse("RatingBlock");
            }else{
                $this->getTemplate()->set_var("RatingBlock", "");
            }
            

            if(null !== $oReview->getFuenteOriginal()){
                $this->getTemplate()->set_var("hrefFuenteUriginal", $oReview->getFuenteOriginal());
                $this->getTemplate()->set_var("sFuenteOriginal", $oReview->getFuenteOriginal());
                $this->getTemplate()->parse("FuenteOriginalBlock");
            }else{
                $this->getTemplate()->set_var("FuenteOriginalBlock", "");
            }

            $this->agregarComentariosAmpliarFicha($oReview);

            $this->agregarAdjuntosAmpliarFicha($oReview);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }                    
    }

    private function agregarAdjuntosAmpliarFicha($oFicha)
    {
        list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());

        if($cantFotos > 0 || $cantVideos > 0 || $cantArchivos > 0){

            $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "galeriaAdjuntos", "GaleriaAdjuntosBlock");
                 
            if($cantFotos > 0){

                $aFotos = $oFicha->getFotos();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aFotos as $oFoto){
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion(true));
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    $this->getTemplate()->parse("ThumbnailFotoEditBlock", true);
                }

                $this->getTemplate()->set_var("NoRecordsFotosBlock", "");

            }else{
                $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            }

            if($cantVideos > 0)
            {
                $aEmbedVideos = $oFicha->getEmbedVideos();

                foreach($aEmbedVideos as $oEmbedVideo){

                    $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                    $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                    $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                    $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                    $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                    $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                    $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                    $this->getTemplate()->parse("ThumbnailVideoEditBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
            }else{
                $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            }

            if($cantArchivos > 0)
            {
                $aArchivos = $oFicha->getArchivos();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aArchivos as $oArchivo){

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);

                    $sTitulo = $oArchivo->getTitulo();
                    $sDescripcion = $oArchivo->getDescripcion();
                    if(empty($sTitulo) && empty($sDescripcion)){
                        $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                    }else{
                        if(empty($sTitulo)){
                            $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("tituloArchivo", $sTitulo);
                        }

                        if(empty($sDescripcion)){
                            $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("descripcionArchivo", $sDescripcion);
                        }
                    }

                    $this->getTemplate()->parse("RowArchivoEditBlock", true);

                    $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                }
                $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");
            }else{
                $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            }

            $this->getTemplate()->set_var("iItemIdForm", $oFicha->getId());
            $this->getTemplate()->set_var("sTipoItemForm", get_class($oFicha));
        }else{
            $this->getTemplate()->set_var("galeriaAdjuntos", "La publicacion no tiene adjuntos");
        }
    }

    private function agregarComentariosAmpliarFicha($oFicha)
    {
        try{
            $aComentarios = $oFicha->getComentarios();

            if(count($aComentarios)>0){
                $this->getTemplate()->load_file_section("gui/componentes/backEnd/comentarios.gui.html", "comentarios", "ComentariosBlock");
                $this->getTemplate()->set_var("totalComentarios", count($aComentarios));

                foreach($aComentarios as $oComentario){

                    $oUsuario = $oComentario->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
                    $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion(true));
                    $this->getTemplate()->set_var("iComentarioId", $oComentario->getId());

                    $this->getTemplate()->parse("ComentarioBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("comentarios", "La publicacion no tiene comentarios");
            }
        }catch(Exception $e){
            print($e->getMessage());
        }
    }

    private function modificarPublicacion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iPublicacionIdForm = $this->getRequest()->getPost('publicacionIdForm');
            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionIdForm);

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oPublicacion->setTitulo($this->getRequest()->getPost("titulo"));
            $oPublicacion->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oPublicacion->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oPublicacion->setKeywords($this->getRequest()->getPost("keywords"));
            $oPublicacion->isActivo($bActivo);
            $oPublicacion->isPublico($bPublico);
            $oPublicacion->isActivoComentarios($bActivoComentarios);

            ComunidadController::getInstance()->guardarPublicacion($oPublicacion);
            $this->getJsonHelper()->setMessage("La publicación se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarReview()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iReviewIdForm = $this->getRequest()->getPost('reviewIdForm');
            $oReview = ComunidadController::getInstance()->getReviewById($iReviewIdForm);

            $bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;

            $oReview->setTitulo($this->getRequest()->getPost("titulo"));
            $oReview->setDescripcionBreve($this->getRequest()->getPost("descripcionBreve"));
            $oReview->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oReview->setKeywords($this->getRequest()->getPost("keywords"));
            $oReview->isActivo($bActivo);
            $oReview->isPublico($bPublico);
            $oReview->isActivoComentarios($bActivoComentarios);

            $fRating = $this->getRequest()->getPost("rating");
            if(empty($fRating)){ $fRating = null; }

            $oReview->setItemType($this->getRequest()->getPost("itemType"));
            $oReview->setItemName($this->getRequest()->getPost("item"));
            $oReview->setItemEventSummary($this->getRequest()->getPost("itemEventSummary"));
            $oReview->setItemUrl($this->getRequest()->getPost("itemUrl"));
            $oReview->setRating($fRating);
            $oReview->setFuenteOriginal($this->getRequest()->getPost("fuenteOriginal"));

            ComunidadController::getInstance()->guardarReview($oReview);
            $this->getJsonHelper()->setMessage("El review se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function masPublicaciones()
    {
        try{
            $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "ajaxGrillaPublicacionesBlock", "GrillaPublicacionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aFichas = AdminController::getInstance()->buscarPublicacionesComunidad($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aFichas) > 0){

                foreach($aFichas as $oFicha){

                    $oUsuario = $oFicha->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                    $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);

                    if($oFicha->isActivo()){
                        $this->getTemplate()->set_var("sSelectedPublicacionActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "selected='selected'");
                    }

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());

                    $this->getTemplate()->parse("PublicacionBlock", true);
                    $this->getTemplate()->set_var("sSelectedPublicacionActivo", "");
                    $this->getTemplate()->set_var("sSelectedPublicacionDesactivado", "");
                }

                $this->getTemplate()->set_var("NoRecordsPublicacionesBlock", "");
            }else{
                $this->getTemplate()->set_var("PublicacionBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "noRecords", "NoRecordsPublicacionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron publicaciones");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masPublicaciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/publicaciones-procesar", "listadoPublicacionesResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaPublicacionesBlock', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function cambiarEstadoPublicacion()
    {
        $iPublicacionId = $this->getRequest()->getParam('iPublicacionId');
        $estadoPublicacion = $this->getRequest()->getParam('estadoPublicacion');
        $objType = $this->getRequest()->getParam('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('estadoPublicacion') ||
            !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $bActivo = ($estadoPublicacion == "1") ? true : false;
        switch($objType)
        {
            case "publicacion":
                $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                $oFicha->isActivo($bActivo);
                ComunidadController::getInstance()->guardarPublicacion($oFicha);
                break;
            case "review":
                $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                $oFicha->isActivo($bActivo);
                ComunidadController::getInstance()->guardarReview($oFicha);
                break;
        }
    }

    private function borrarPublicacion()
    {
        $iPublicacionId = $this->getRequest()->getPost('iPublicacionId');
        $objType = $this->getRequest()->getPost('objType');

        if(empty($iPublicacionId) || !$this->getRequest()->has('objType')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            switch($objType)
            {
                case "publicacion":
                    $oFicha = ComunidadController::getInstance()->getPublicacionById($iPublicacionId);
                    break;
                case "review":
                    $oFicha = ComunidadController::getInstance()->getReviewById($iPublicacionId);
                    break;
            }

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            //polimorfico
            $result = ComunidadController::getInstance()->borrarPublicacion($oFicha, $pathServidorFotos, $pathServidorArchivos);

            $this->restartTemplate();

            if($result){
                $msg = "La publicación fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la publicación del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la publicación del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    //adjuntos
    public function formArchivo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "popUpContent", "FormularioArchivoBlock");

        $iArchivoId = $this->getRequest()->getParam('iArchivoId');
        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

        $this->getTemplate()->set_var("iArchivoId", $iArchivoId);

        $sTitulo = $oArchivo->getTitulo();
        $sDescripcion = $oArchivo->getDescripcion();
        $iOrden = $oArchivo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarArchivo()
    {
        $iArchivoId = $this->getRequest()->getParam('iArchivoId');

        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);
            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            IndexController::getInstance()->borrarArchivo($oArchivo, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function guardarArchivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iArchivoId = $this->getRequest()->getParam('iArchivoIdForm');

            if(empty($iArchivoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            $oArchivo->setOrden($this->getRequest()->getPost("orden"));
            $oArchivo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oArchivo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarArchivo($oArchivo);

            $this->getJsonHelper()->setMessage("El archivo se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function formVideo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "popUpContent", "FormularioVideoBlock");

        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');
        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

        $this->getTemplate()->set_var("iEmbedVideoId", $iEmbedVideoId);

        $sTitulo = $oEmbedVideo->getTitulo();
        $sDescripcion = $oEmbedVideo->getDescripcion();
        $iOrden = $oEmbedVideo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function guardarVideo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEmbedVideoId = $this->getRequest()->getPost('iEmbedVideoId');

            if(empty($iEmbedVideoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            $oEmbedVideo->setOrden($this->getRequest()->getPost("orden"));
            $oEmbedVideo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oEmbedVideo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarEmbedVideo($oEmbedVideo);

            $this->getJsonHelper()->setMessage("El video se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function eliminarVideo()
    {
        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');

        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            IndexController::getInstance()->borrarEmbedVideo($oEmbedVideo);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function formFoto()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/backEnd/galerias.gui.html", "popUpContent", "FormularioFotoBlock");
               
        $iFotoId = $this->getRequest()->getParam('iFotoId');
        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

        $this->getTemplate()->set_var("iFotoId", $iFotoId);

        $sTitulo = $oFoto->getTitulo();
        $sDescripcion = $oFoto->getDescripcion();
        $iOrden = $oFoto->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function eliminarFoto()
    {
        $iFotoId = $this->getRequest()->getParam('iFotoId');

        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            IndexController::getInstance()->borrarFoto($oFoto, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function guardarFoto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iFotoId = $this->getRequest()->getPost('iFotoIdForm');

            if(empty($iFotoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            $oFoto->setOrden($this->getRequest()->getPost("orden"));
            $oFoto->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oFoto->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarFoto($oFoto);

            $this->getJsonHelper()->setMessage("La foto se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function listarModeraciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "widgetsContent", "HeaderModeracionesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "mainContent", "ListadoModeracionBlock");

            //check activar/desactivar moderaciones
            $oParametroControlador = AdminController::getInstance()->getParametroControladorByNombre('ACTIVAR_MODERACIONES', 'comunidad_publicaciones');
            if($oParametroControlador->getValor()){
                $this->getTemplate()->set_var("moderacionesChecked", "checked='checked'");
            }
            
            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aFichas = AdminController::getInstance()->buscarPublicacionesModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aFichas) > 0){

                foreach($aFichas as $oFicha){

                    $oUsuario = $oFicha->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                    $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());
                    
                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesFicha($oFicha->getId());
                    //al menos 1 porque es un listado de publicaciones con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }                      
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());
                        
                        $this->getTemplate()->parse("ModeracionHistorialPublicacionBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");
                    
                    $this->getTemplate()->parse("PublicacionModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialPublicacionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("PublicacionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay publicaciones pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/publicaciones-procesar", "listadoModeracionesResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }        
    }

    private function masModeraciones()
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "ajaxGrillaModeracionesBlock", "GrillaModeracionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            
            $iRecordsTotal = 0;
            $aFichas = AdminController::getInstance()->buscarPublicacionesModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aFichas) > 0){

                foreach($aFichas as $oFicha){

                    $oUsuario = $oFicha->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                    $sTipoPublicacion = (get_class($oFicha) == "Publicacion")?"publicacion":"review";
                    $this->getTemplate()->set_var("iPublicacionId", $oFicha->getId());
                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("sTipo", $sTipoPublicacion);

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                    $this->getTemplate()->set_var("sTitulo", $oFicha->getTitulo());
                    $this->getTemplate()->set_var("sFecha", $oFicha->getFecha());

                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesFicha($oFicha->getId());
                    //al menos 1 porque es un listado de publicaciones con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());

                        $this->getTemplate()->parse("ModeracionHistorialPublicacionBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");

                    $this->getTemplate()->parse("PublicacionModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialPublicacionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("PublicacionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/publicaciones.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay publicaciones pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/publicaciones-procesar", "listadoModeracionesResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaModeracionesBlock', false));
        }catch(Exception $e){
            print_r($e);
        }        
    }

    public function listarDenuncias()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionDenuncias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "widgetsContent", "HeaderDenunciasBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "mainContent", "ListadoDenunciasBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesDenuncias($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aDenuncias = $oInstitucion->getDenuncias();

                    $this->getTemplate()->set_var("iCantDenuncias", count($aDenuncias));

                    foreach($aDenuncias as $oDenuncia){
                        $oUsuario = $oDenuncia->getUsuario();
                        $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                        $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                        $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                        $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                        $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                        $this->getTemplate()->set_var("sFechaDenuncia", $oDenuncia->getFecha(true));
                        $this->getTemplate()->set_var("sRazonDenuncia", $oDenuncia->getRazon());

                        $sMensaje = $oDenuncia->getMensaje(true);
                        if(empty($sMensaje)){ $sMensaje = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensaje);
                        $this->getTemplate()->set_var("iDenunciaId", $oDenuncia->getId());

                        $this->getTemplate()->parse("DenunciaHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->parse("InstitucionDenunciaBlock", true);
                    $this->getTemplate()->set_var("DenunciaHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsDenunciasBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsDenunciasBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay instituciones con denuncias");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masDenuncias=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-denuncias-procesar", "listadoDenunciasResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesarDenuncias()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masDenuncias')){
            $this->masDenuncias();
            return;
        }

        if($this->getRequest()->has('limpiarDenuncias')){
            $this->limpiarDenuncias();
            return;
        }

        if($this->getRequest()->has('eliminar')){
            $this->eliminarPorDenuncias();
            return;
        }
    }

    /**
     * Agrega el envio de mail notificando al usuario administrador (si es que poseia)
     * que la institucion fue eliminada del sistema por acumulacion de denuncias.
     */
    private function eliminarPorDenuncias()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            //tiene administrador?
            if(null !== $oInstitucion->getUsuario())
            {
                //el usuario tiene activadas las notificaciones por mail?
                //lo tengo que levantar asi porque NO es el usuario que inicio sesion.
                $oParametroUsuario = AdminController::getInstance()->getParametroUsuarioByNombre('NOTIFICACIONES_MAIL', $oInstitucion->getUsuario()->getId());

                //porque es booleano
                if($oParametroUsuario->getValor()){

                    $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
                    $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
                    $mailContacto = $parametros->obtener('EMAIL_SITIO_CONTACTO');

                    //envio mail al usuario administrador de la institucion
                    $sMailDestino = $oInstitucion->getUsuario()->getEmail();
                    $hrefSitio = htmlentities($this->getRequest()->getBaseTagUrl());

                    //link externo para desactivar notificaciones de mail
                    $hrefCancelarSuscripcion = htmlentities($hrefSitio."desactivar-notificaciones-mail?id=".$oInstitucion->getUsuario()->getId()."&key=".$oInstitucion->getUsuario()->getUrlTokenKey());

                    $this->getTemplate()->load_file("gui/templates/index/frameMail01-01.gui.html", "frameMail");

                    //head y footer mail.
                    $this->getTemplate()->set_var("hrefSitio", $hrefSitio);
                    $this->getTemplate()->set_var("sNombreSitio", $nombreSitio." - Comunidad");
                    $this->getTemplate()->set_var("sEmailDestino", $sMailDestino);
                    $this->getTemplate()->set_var("sEmailContacto", $mailContacto);
                    $this->getTemplate()->set_var("hrefCancelarSuscripcion", $hrefCancelarSuscripcion);

                    $this->getTemplate()->load_file_section("gui/componentes/mails.gui.html", "sMainContent", "TituloMensajeBlock");

                    $sTituloMensaje = htmlentities("Institución eliminada de la comunidad.");
                    $this->getTemplate()->set_var("sTituloMensaje", $sTituloMensaje);

                    $sNombreUsuario = $oInstitucion->getUsuario()->getNombre()." ".$oInstitucion->getUsuario()->getApellido();
                    $sNombreInstitucion = $oInstitucion->getNombre();
                    $sMensaje = htmlentities($sNombreUsuario." le informamos que la institución '".$sNombreInstitucion."' fue revisada y eliminada de la comunidad por uno de nuestros moderadores debido a acumulación de denuncias.");

                    $this->getTemplate()->set_var("sMensaje", $sMensaje);

                    $sMensajeBody = $this->getTemplate()->pparse("frameMail", false);

                    $this->getMailerHelper()->sendMail($mailContacto, $nombreSitio." - Comunidad", $sMailDestino, $sNombreUsuario, "Institucion eliminada de la comunidad.", $sMensajeBody);
                }
            }
        }catch(Exception $e){
            //hubo un error en el envio de mail.
            $this->getJsonHelper()->initJsonAjaxResponse();
            $msg = "Ocurrio un error, no se ha eliminado la institucion del sistema. No se pudo enviar el mail de notificacion al usuario administrador de la institución";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }

        //si se envio bien el mail entonces elimino la institucion
        $this->eliminarInstitucion();
    }

    private function masDenuncias()
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "ajaxGrillaDenunciasBlock", "GrillaDenunciasBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesDenuncias($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aDenuncias = $oInstitucion->getDenuncias();

                    $this->getTemplate()->set_var("iCantDenuncias", count($aDenuncias));

                    foreach($aDenuncias as $oDenuncia){
                        $oUsuario = $oDenuncia->getUsuario();
                        $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                        $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();

                        $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                        $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                        $this->getTemplate()->set_var("sAutor", $sNombreUsuario);
                        $this->getTemplate()->set_var("sFechaDenuncia", $oDenuncia->getFecha(true));
                        $this->getTemplate()->set_var("sRazonDenuncia", $oDenuncia->getRazon());

                        $sMensaje = $oDenuncia->getMensaje(true);
                        if(empty($sMensaje)){ $sMensaje = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensaje);
                        $this->getTemplate()->set_var("iDenunciaId", $oDenuncia->getId());

                        $this->getTemplate()->parse("DenunciaHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->parse("InstitucionDenunciaBlock", true);
                    $this->getTemplate()->set_var("DenunciaHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsDenunciasBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsDenunciasBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron instituciones denunciadas");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masDenuncias=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-denuncias-procesar", "listadoDenunciasResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaDenunciasBlock', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function limpiarDenuncias()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            $result = AdminController::getInstance()->limpiarDenuncias($oInstitucion);

            $this->restartTemplate();

            if($result){
                $msg = "Se limpiaron las denuncias para la institución.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se han limpiado las denuncias para la institución.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se han limpiado las denuncias para la institución.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}