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
            $aFichas = ComunidadController::getInstance()->buscarPublicacionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

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
    }

    private function masPublicaciones()
    {
        
    }

    public function listarModeraciones()
    {
        
    }
}