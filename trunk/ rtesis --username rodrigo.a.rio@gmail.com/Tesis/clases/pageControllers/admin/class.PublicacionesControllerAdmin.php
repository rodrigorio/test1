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

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
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

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));                
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

            //$this->agregarAdjuntosAmpliarFicha($oPublicacion);
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }            
    }

    private function ampliarReview($iReviewId)
    {
        echo "entro ampliar review"; exit();
    }

    private function agregarAdjuntosAmpliarFicha($oFicha)
    {
        list($cantFotos, $cantVideos, $cantArchivos) = ComunidadController::getInstance()->obtenerCantidadMultimediaFicha($oFicha->getId());

        if($cantFotos > 0 || $cantVideos > 0 || $cantArchivos > 0){

            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "galeriaAdjuntos", "GaleriaAdjuntosBlock");

            //primero borro todos los bloques que ya se que no se usan
            $this->getTemplate()->set_var("TituloItemBlock", "");
            $this->getTemplate()->set_var("MenuGaleriaAdjuntos", "");
            $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
            $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
            $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");

            if($cantFotos > 0){

                $aFotos = $oFicha->getFotos();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aFotos as $oFoto){
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->parse("ThumbnailFotoBlock", true);
                }

            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosFotosBlock", "");
            }

            if($cantVideos > 0)
            {
                $aEmbedVideos = $oFicha->getEmbedVideos();

                foreach($aEmbedVideos as $oEmbedVideo){

                    $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                    $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?embedVideoId=".$oEmbedVideo->getId();

                    $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                    $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                    $this->getTemplate()->set_var("tituloVideo", $oEmbedVideo->getTitulo());
                    $this->getTemplate()->set_var("descripcionVideo", $oEmbedVideo->getDescripcion());
                    $this->getTemplate()->parse("ThumbnailVideoBlock", true);
                }

            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosVideosBlock", "");
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

                    $this->getTemplate()->parse("RowArchivoBlock", true);

                    $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                }
            }else{
                $this->getTemplate()->set_var("GaleriaAdjuntosArchivosBlock", "");
            }
        }        
    }

    private function agregarComentariosAmpliarFicha($oFicha)
    {
        try{
            $aComentarios = $oFicha->getComentarios();

            if(count($aComentarios)>0){
                $this->getTemplate()->load_file_section("gui/componentes/backEnd/comentarios.gui.html", "comentarios", "ComentariosBlock");
                $this->getTemplate()->set_var("ComentarioValoracionBlock", "");
                $this->getTemplate()->set_var("totalComentarios", count($aComentarios));

                foreach($aComentarios as $oComentario){

                    $oUsuario = $oComentario->getUsuario();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();

                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();

                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("dFechaComentario", $oComentario->getFecha());
                    $this->getTemplate()->set_var("sComentario", $oComentario->getDescripcion());
                    $this->getTemplate()->set_var("iComentarioId", $oComentario->getId());

                    $this->getTemplate()->parse("ComentarioBlock", true);
                }
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

    public function listarModeraciones()
    {
        
    }
}