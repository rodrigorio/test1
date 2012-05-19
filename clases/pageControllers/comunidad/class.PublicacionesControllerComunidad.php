<?php

/**
 * @author Matias Velilla
 */
class PublicacionesControllerComunidad extends PageControllerAbstract
{
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
    }
    
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

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

        $this->getTemplate()->set_var("hrefListadoPublicaciones", $this->getUrlFromRoute("comunidadPublicacionesIndex", true));
        $this->getTemplate()->set_var("hrefMisPublicaciones", $this->getUrlFromRoute("comunidadPublicacionesMisPublicaciones", true));
        $this->getTemplate()->set_var("hrefCrearPublicacion", $this->getUrlFromRoute("comunidadPublicacionesCrearPublicacionForm", true));
        $this->getTemplate()->set_var("hrefCrearReview", $this->getUrlFromRoute("comunidadPublicacionesCrearReviewForm", true));

        return $this;
    }
    
    public function index(){
        $this->listar();
    }
    
    public function misPublicaciones(){}       
    public function procesar(){}
    public function galeriaFotos(){}
    public function fotosProcesar(){}
    public function formFoto(){}
    public function galeriaArchivos(){}
    public function archivosProcesar(){}
    public function formArchivo(){}
    public function galeriaVideos(){}
    public function videosProcesar(){}
    public function formVideo(){}

    /**
     * Lista todas las publicaciones de integrantes para la comunidad.
     * Son fichas miniatura una abajo de la otra paginadas y con posibilidad de filtros.
     */
    private function listar()
    {
        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();
       
        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Publicaciones Comunidad");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "pageRightInnerMainCont", "ListadoPublicacionesBlock");
               
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function crearPublicacionForm()
    {
        $this->mostrarFormularioPublicacionPopUp();
    }
    
    public function modificarPublicacionForm()
    {
        $this->mostrarFormularioPublicacionPopUp();               
    }

    private function mostrarFormularioPublicacionPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        //AGREGAR PUBLICACION
        if($this->getRequest()->getActionName() == "crearPublicacionForm"){
            
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "popUpContent", "FormularioPublicacionBlock");
            $this->getTemplate()->unset_blocks("SubmitModificarPublicacionBlock");

            $sTituloForm = "Agregar una nueva publicación";

            //valores por defecto en el agregar
            $oPublicacion = null;
            $iPublicacionIdForm = "";
            $sTitulo = "";
            $sDescripcionBreve = "";
            $bActivoComentarios = true;
            $bActivo = true;
            $bPublico = false;
            $sDescripcion = "";
            $sKeywords = "";

        //MODIFICAR PUBLICACION
        }else{
            $iPublicacionIdForm = $this->getRequest()->getParam('publicacionIdForm');
            if(empty($iPublicacionIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar publicación";

            $oPublicacion = ComunidadController::getInstance()->getPublicacionById($iPublicacionIdForm);

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/personas.gui.html", "popUpContent", "FormularioPublicacionBlock");
            $this->getTemplate()->unset_blocks("SubmitCrearPublicacionBlock");

            $this->getTemplate()->set_var("iPublicacionIdForm", $iPublicacionIdForm);

            $sTitulo = $oPublicacion->getTitulo();
            $sDescripcionBreve = $oPublicacion->getDescripcionBreve();
            $bActivoComentarios = $oPublicacion->isActivoComentarios();
            $bActivo = $oPublicacion->isActivo();
            $bPublico = $oPublicacion->isPublico();
            $sDescripcion = $oPublicacion->getDescripcion();
            $sKeywords = $oPublicacion->getKeywords();
        }

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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcionBreve", $sDescripcionBreve);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sKeywords", $sKeywords);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function guardarPublicacion()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        if($this->getRequest()->has('crearPublicacion')){
            $this->crearPublicacion();
            return;
        }

        if($this->getRequest()->has('modificarPublicacion')){
            $this->modificarPublicacion();
            return;
        }
    }

    private function crearPublicacion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oPublicacion = new stdClass();

            $oPublicacion->sTitulo = $this->getRequest()->getPost("titulo");
            $oPublicacion->sDescripcionBreve = $this->getRequest()->getPost("descripcionBreve");
            $oPublicacion->bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $oPublicacion->bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $oPublicacion->bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;
            $oPublicacion->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oPublicacion->sKeywords = $this->getRequest()->getPost("keywords");            
            $oPublicacion->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            $oPublicacion = Factory::getPublicacionInstance($oPublicacion);

            ComunidadController::getInstance()->guardarPublicacion($oPublicacion);

            $this->getJsonHelper()->setValor("agregarPublicacion", "1");
            $this->getJsonHelper()->setMessage("La publicación se ha creado con éxito. Puede agregar fotos, archivos y videos desde 'Mis Publicaciones'");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
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
            $this->getJsonHelper()->setValor("modificarPersona", "1");
            $this->getJsonHelper()->setSuccess(true);
            
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function crearReviewForm()
    {
        $this->mostrarFormularioReviewPopUp();
    }

    public function modificarReviewForm()
    {
        $this->mostrarFormularioReviewPopUp();
    }

    private function mostrarFormularioReviewPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/publicaciones.gui.html", "popUpContent", "FormularioReviewBlock");

        //AGREGAR REVIEW
        if($this->getRequest()->getActionName() == "crearReviewForm"){
           
            $this->getTemplate()->unset_blocks("SubmitModificarReviewBlock");

            $sTituloForm = "Agregar un nuevo Review";

            //valores por defecto en el agregar
            $oReview = null;
            $iReviewIdForm = "";
            $sTitulo = "";
            $sDescripcionBreve = "";
            $bActivoComentarios = true;
            $bActivo = true;
            $bPublico = false;
            $sDescripcion = "";
            $sKeywords = "";
            $sItemType = "";
            $sItemName = "";
            $sItemEventSummary = "";
            $sItemUrl = "";
            $fRating = "";
            $sFuenteOriginal = "";

        //MODIFICAR REVIEW
        }else{
            $iReviewIdForm = $this->getRequest()->getParam('reviewIdForm');
            if(empty($iReviewIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar Review";

            $oReview = ComunidadController::getInstance()->getReviewById($iReviewIdForm);
            
            $this->getTemplate()->unset_blocks("SubmitCrearReviewBlock");

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
        }

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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
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
                $this->getTemplate()->set_var("sSelected_0.5", "selected='selected'");
                break;
            case ($fRating >= 1 && $fRating < 1.5):
                $this->getTemplate()->set_var("sSelected_1", "selected='selected'");
                break;
            case ($fRating >= 1.5 && $fRating < 2):
                $this->getTemplate()->set_var("sSelected_1.5", "selected='selected'");
                break;
            case ($fRating >= 2 && $fRating < 2.5):
                $this->getTemplate()->set_var("sSelected_2", "selected='selected'");
                break;
            case ($fRating >= 2.5 && $fRating < 3):
                $this->getTemplate()->set_var("sSelected_2.5", "selected='selected'");
                break;
            case ($fRating >= 3 && $fRating < 3.5):
                $this->getTemplate()->set_var("sSelected_3", "selected='selected'");
                break;
            case ($fRating >= 3.5 && $fRating < 4):
                $this->getTemplate()->set_var("sSelected_3.5", "selected='selected'");
                break;
            case ($fRating >= 4 && $fRating < 4.5):
                $this->getTemplate()->set_var("sSelected_4", "selected='selected'");
                break;
            case ($fRating >= 4.5 && $fRating < 5):
                $this->getTemplate()->set_var("sSelected_4.5", "selected='selected'");
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

    public function guardarReview()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearReview')){
            $this->crearReview();
            return;
        }

        if($this->getRequest()->has('modificarReview')){
            $this->modificarReview();
            return;
        }        
    }

    private function crearReview()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oReview = new stdClass();

            $oReview->sTitulo = $this->getRequest()->getPost("titulo");
            $oReview->sDescripcionBreve = $this->getRequest()->getPost("descripcionBreve");
            $oReview->bActivo = ($this->getRequest()->getPost("activo") == "1")?true:false;
            $oReview->bPublico = ($this->getRequest()->getPost("publico") == "1")?true:false;
            $oReview->bActivoComentarios = ($this->getRequest()->getPost("activoComentarios") == "1")?true:false;
            $oReview->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oReview->sKeywords = $this->getRequest()->getPost("keywords");
            $oReview->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            //porque 0 en realidad es un valor real (valoracion 0 para el review)
            //por eso hay que tener cuidado de que si no se utilizo el campo en el form asignarle null al atributo de la clase.
            $fRating = $this->getRequest()->getPost("rating");
            if(empty($fRating)){ $fRating = null; }

            $oReview->sItemType = $this->getRequest()->getPost("itemType");
            $oReview->sItemName = $this->getRequest()->getPost("item");
            $oReview->sItemEventSummary = $this->getRequest()->getPost("itemEventSummary");
            $oReview->sItemUrl = $this->getRequest()->getPost("itemUrl");
            $oReview->fRating = $fRating;
            $oReview->sFuenteOriginal = $this->getRequest()->getPost("fuenteOriginal");
                    
            $oReview = Factory::getReviewInstance($oReview);

            ComunidadController::getInstance()->guardarReview($oReview);

            $this->getJsonHelper()->setValor("agregarReview", "1");
            $this->getJsonHelper()->setMessage("El Review se ha creado con éxito. Puede agregar fotos, archivos y videos desde 'Mis Publicaciones'");
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
            $this->getJsonHelper()->setValor("modificarReview", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}