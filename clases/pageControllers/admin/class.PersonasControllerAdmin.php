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
            $aDiscapacitadosMod = SeguimientosController::getInstance()->obtenerEspecialidad($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vEspecialidad)>0){
            	$i=0;
	            foreach ($vEspecialidad as $oEspecialidad){
	            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
	                $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
	                $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
	                $this->getTemplate()->parse("ListaEspecialidadesBlock", true);
	                $i++;
	            }
                $this->getTemplate()->set_var("NoRecordsListaEspecialidadesBlock", "");
            }else{
                $this->getTemplate()->set_var("ListaEspecialidadesBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "noRecords", "NoRecordsListaEspecialidadesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
	            $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
}