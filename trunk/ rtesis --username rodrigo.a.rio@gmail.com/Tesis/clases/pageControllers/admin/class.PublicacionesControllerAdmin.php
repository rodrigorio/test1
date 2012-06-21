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
                $this->formModificarReview($iReviewId);
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

    private function formModificarReview()
    {

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