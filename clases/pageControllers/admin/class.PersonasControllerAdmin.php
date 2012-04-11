<?php
class PersonasControllerAdmin extends PageControllerAbstract
{
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listarPersonas();
    }

    public function listarPersonas()
    {
        
    }

    public function listarModeracionesPendientes()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $filtro = array();
            $iRecordPerPage = 5;
            $iPage = $this->getRequest()->getPost("iPage");
            $iPage = strlen($iPage) ? $iPage : 1;
            $iItemsForPage = $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit = ($iPage-1) * $iItemsForPage;
            $sOrderBy = null;
            $sOrder = null;
            $iRecordsTotal = 0;

            //array con objetos discapacitados desde discapacitados_moderacion (datos sin aprobar).
            $aDiscapacitadosMod = AdminController::getInstance()->obtenerModeracionesDiscapacitados($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($aDiscapacitadosMod) > 0){
            	$i=0;
                foreach($aDiscapacitadosMod as $oDiscapacitadoMod){

                    

                    $i++;
                }

                $this->getTemplate()->set_var("NoRecordsDiscapacitadosModBlock", "");
            }else{
                $this->getTemplate()->set_var("ListaDiscapacitadosModBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/personas.gui.html", "noRecords", "NoRecordsDiscapacitadosModBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay moderaciones pendientes");
                $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Con una misma accion puedo aprobar o rechazar dependiendo un parametro.
     * Se puede porque el que tiene permiso para rechazar tiene permiso para aprobar cambios.
     */
    public function procesarModeracion()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('rechazar')){
            $this->rechazarCambiosModeracion();
            return;
        }

        if($this->getRequest()->has('aprobar')){
            $this->aprobarCambiosModeracion();
            return;
        }        
    }

    private function aprobarCambiosModeracion($oDescapacitado)
    {

    }
    
    private function rechazarCambiosModeracion($oDescapacitado)
    {

    }
}