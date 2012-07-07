<?php
class InstitucionesControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listarInstituciones();
    }

    public function listarInstituciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionInstituciones");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "mainContent", "ListadoInstitucionesBlock");

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
            $aInstituciones = ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);

            if(count($aInstituciones) > 0){
            	$i=0;
                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                    $this->getTemplate()->parse("InstitucionesBlock", true);
                    
                    $i++;
                }
                $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");
            }else{
                $this->getTemplate()->set_var("InstitucionesBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay instituciones cargadas en el sistema");
                $this->getTemplate()->parse("noRecords", false);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('eliminar')){
            $this->eliminarInstitucion();
            return;
        }
    }

    private function eliminarInstitucion()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $result = ComunidadController::getInstance()->borrarInstitucion($iInstitucionId);

            $this->restartTemplate();

            if($result){
                $msg = "La institucion fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la institucion del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la institucion del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}