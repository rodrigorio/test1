<?php

class EspecialidadControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    public function index(){
        $this->listarEspecialidades();
    }
    
    public function listarEspecialidades()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");

            $iRecordsTotal = 0;
            $vEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtro = array(), $iRecordsTotal, null, null, null);
            if(count($vEspecialidad)>0){
                foreach ($vEspecialidad as $oEspecialidad){

                    $hrefEditarEspecialidad = $this->getUrlFromRoute("adminEspecialidadEditarEspecialidad", true)."?id=".$oEspecialidad->getId();

                    $this->getTemplate()->set_var("hrefEditarEspecialidad", $hrefEditarEspecialidad);
                    $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
                    $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());

                    $sDescripcion = (null === $oEspecialidad->getDescripcion())?" - ":$oEspecialidad->getDescripcion();
                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

                    $this->getTemplate()->parse("EspecialidadesBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsEspecialidadesBlock", "");
            }else{
                $this->getTemplate()->set_var("EspecialidadesBlock", "");
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function nuevaEspecialidad()
    {
        try{            
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();
            
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "FormEspecialidadBlock");

            $this->getTemplate()->set_var("sTituloForm", "Crear nueva especialidad");
            $this->getTemplate()->set_var("SubmitModificarEspecialidadBlock", "");
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function editarEspecialidad()
    {
        try{
            $iEspecialidadId = $this->getRequest()->getParam('id');

            if(empty($iEspecialidadId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "FormEspecialidadBlock");

            $this->getTemplate()->set_var("sTituloForm", "Modificar especialidad");
            $this->getTemplate()->set_var("SubmitCrearEspecialidadBlock", "");

            $oEspecialidad = AdminController::getInstance()->obtenerEspecialidadById($iEspecialidadId);

            $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
            $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());
            $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
                
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    /**
     * Si viene id quiere decir que se esta modificando y se verifica si existe el nombre
     * pero con la excepcion de ese registro.
     *
     * si no viene id la especialidad no existe y se fija si ya se usa el nombre en toda
     * la tabla
     */
    public function verificarUsoDeEspecialidad()
    {
        $iEspecialidadId = $this->getRequest()->getParam('iEspecialidadId');
        $sNombre = $this->getRequest()->getParam('sNombre');

        if(null === $iEspecialidadId){
            $oEspecialidad = new stdClass();
            $oEspecialidad->sNombre = $sNombre;
            $oEspecialidad = Factory::getEspecialidadInstance($oEspecialidad);
        }else{
            $oEspecialidad = AdminController::getInstance()->obtenerEspecialidadById($iEspecialidadId);
            //no lo guardo es solo para la comprobacion
            $oEspecialidad->setNombre($sNombre);
        }

        $dataResult = '0';
        if(AdminController::getInstance()->verificarExisteEspecialidad($oEspecialidad)){
            $dataResult = '1';
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);
    }
    
    public function eliminarEspecialidad()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iEspecialidadId = $this->getRequest()->getParam('iEspecialidadId');
        if(empty($iEspecialidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        try{

            $this->getJsonHelper()->initJsonAjaxResponse();
            try{
                $result = AdminController::getInstance()->borrarEspecialidad($iEspecialidadId);

                $this->restartTemplate();

                if($result){
                    $msg = "La especialidad fue eliminada del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar la institucion del sistema. Compruebe que ningun usuario este asociado a esta especialidad.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

            $this->getJsonHelper()->sendJsonAjaxResponse();
            
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function procesarEspecialidad()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sNombre = $this->getRequest()->getPost("nombre");
            $sDescripcion = $this->getRequest()->getPost("descripcion");

            if($this->getRequest()->has('crearEspecialidad')){
                $oEspecialidad = new stdClass();
                $oEspecialidad->sNombre = $sNombre;
                $oEspecialidad->sDescripcion = $sDescripcion;
                $oEspecialidad = Factory::getEspecialidadInstance($oEspecialidad);

                $accion = "agregarEspecialidad";
                $mensaje = "Se agrego la especialidad al sistema";
            }
            
            if($this->getRequest()->has('modificarEspecialidad')){
                $iEspecialidadId = $this->getRequest()->getPost("iEspecialidadId");
                $oEspecialidad = AdminController::getInstance()->obtenerEspecialidadById($iEspecialidadId);
                $oEspecialidad->setNombre($sNombre);
                $oEspecialidad->setDescripcion($sDescripcion);

                $accion = "modificarEspecialidad";
                $mensaje = "La especialidad se modifico exitosamente";
            }

            if(AdminController::getInstance()->verificarExisteEspecialidad($oEspecialidad)){
                $this->getJsonHelper()->setMessage("Ya existe una especialidad con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
            }else{
                AdminController::getInstance()->guardarEspecialidad($oEspecialidad);
                $this->getJsonHelper()->setMessage($mensaje);
                $this->getJsonHelper()->setValor('accion', $accion);
                $this->getJsonHelper()->setSuccess(true);
            }
                                           
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}