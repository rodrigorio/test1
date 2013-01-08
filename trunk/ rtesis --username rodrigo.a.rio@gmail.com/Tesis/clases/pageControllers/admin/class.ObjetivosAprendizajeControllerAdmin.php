<?php

class ObjetivosCurricularesControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    public function index(){
        $this->listarNiveles();
    }

    public function procesarNivel()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('crearNivel')){
            $this->crearNivel();
            return;
        }

        if($this->getRequest()->has('modificarNivel')){
            $this->modificarNivel();
            return;
        }

        if($this->getRequest()->has('borrarNivel')){
            $this->borrarNivel();
            return;
        }       
    }

    private function crearNivel()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");

            if(AdminController::getInstance()->existeNivelByDescripcion($sDescripcion)){
                $this->getJsonHelper()->setMessage("Ya existe un nivel con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oNivel = new stdClass();
            $oNivel->sDescripcion = $sDescripcion;
            $oNivel = Factory::getNivelInstance($oNivel);

            AdminController::getInstance()->guardarNivel($oNivel);
            $this->getJsonHelper()->setMessage("El nivel fue creado con éxito");
            $this->getJsonHelper()->setValor("accion", "crearNivel");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }

    private function modificarNivel()
    {
        try{
            $iNivelId = $this->getRequest()->getPost("iNivelId");

            $oNivel = SeguimientosController::getInstance()->getNivelById($iNivelId);

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            if(!empty($sDescripcion) && $sDescripcion !== $oNivel->getDescripcion()){
                if(AdminController::getInstance()->existeNivelByDescripcion($sDescripcion)){
                    $this->getJsonHelper()->setMessage("Ya existe un nivel con ese nombre.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oNivel->setDescripcion($sDescripcion);

            AdminController::getInstance()->guardarNivel($oNivel);
            $this->getJsonHelper()->setMessage("El nivel fue modificado con éxito");
            $this->getJsonHelper()->setValor("accion", "modificarNivel");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();   
    }

    private function borrarNivel()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iNivelId = $this->getRequest()->getParam('iNivelId');
        if(empty($iNivelId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarNivel($iNivelId);

                $this->restartTemplate();

                if($result){
                    $msg = "El nivel fue eliminado del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar el nivel del sistema. Compruebe que no tenga ningun ciclo asociado.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){
            throw $e;
        }
    }

    public function listarNiveles()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderNivelesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "ListadoNivelesBlock");

            $iRecordsTotal = 0;
            $aNiveles = SeguimientosController::getInstance()->getNiveles($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aNiveles)>0){
                foreach ($aNiveles as $oNivel){

                    $hrefEditarNivel = $this->getUrlFromRoute("adminObjetivosCurricularesFormularioNivel", true)."?editar=1&id=".$oNivel->getId();

                    $this->getTemplate()->set_var("hrefEditarNivel", $hrefEditarNivel);

                    $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oNivel->getDescripcion());
                    $this->getTemplate()->parse("NivelBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsNivelesBlock", "");
            }else{
                $this->getTemplate()->set_var("NivelBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function formularioNivel()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderNivelesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "FormNivelBlock");

        if($this->getRequest()->has('editar')){
            $this->editarNivelForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearNivelForm();
        }
    }

    private function editarNivelForm()
    {
        try{
            $iNivelId = $this->getRequest()->getParam('id');

            if(empty($iNivelId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            $this->getTemplate()->set_var("sTituloForm", "Modificar Nivel");
            $this->getTemplate()->set_var("SubmitCrearNivelBlock", "");

            $oNivel = SeguimientosController::getInstance()->getNivelById($iNivelId);

            $this->getTemplate()->set_var("iNivelId", $iNivelId);
            $this->getTemplate()->set_var("sDescripcion", $oNivel->getDescripcion());
         
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function crearNivelForm()
    {
        try{
            $this->getTemplate()->set_var("sTituloForm", "Crear nuevo Nivel");
            $this->getTemplate()->set_var("SubmitModificarNivelBlock", "");
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }

    public function procesarCiclo()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('crearCiclo')){
            $this->crearCiclo();
            return;
        }

        if($this->getRequest()->has('modificarCiclo')){
            $this->modificarCiclo();
            return;
        }

        if($this->getRequest()->has('borrarCiclo')){
            $this->borrarCiclo();
            return;
        }
    }

    private function crearCiclo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $sDescripcion = $this->getRequest()->getPost("descripcion");

            if(AdminController::getInstance()->existeNivelByDescripcion($sDescripcion)){
                $this->getJsonHelper()->setMessage("Ya existe un nivel con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oNivel = new stdClass();
            $oNivel->sDescripcion = $sDescripcion;
            $oNivel = Factory::getNivelInstance($oNivel);

            AdminController::getInstance()->guardarNivel($oNivel);
            $this->getJsonHelper()->setMessage("El nivel fue creado con éxito");
            $this->getJsonHelper()->setValor("accion", "crearNivel");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();  
    }

    private function modificarCiclo()
    {
        try{
            $iCategoriaId = $this->getRequest()->getPost("iCategoriaId");

            if(AdminController::getInstance()->verificarExisteCategoria($oCategoria)){
                $this->getJsonHelper()->setMessage("Ya existe una categoría con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);
            $oCategoria->setNombre($sNombre);
            $oCategoria->setUrlToken($this->getInflectorHelper()->urlize($sNombre));
            $oCategoria->setDescripcion($sDescripcion);

            AdminController::getInstance()->guardarCategoria($oCategoria);
            $this->getJsonHelper()->setMessage($mensaje);
            $this->getJsonHelper()->setValor("accion", "modificarCiclo");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function borrarCiclo()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iCategoriaId = $this->getRequest()->getParam('iCategoriaId');
        if(empty($iCategoriaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarCategoria($iCategoriaId);

                $this->restartTemplate();

                if($result){
                    $msg = "La categoría fue eliminada del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar la categoría del sistema. Compruebe que no haya ningún software asociado.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){
            throw $e;
        }
    }

    public function listarCiclos()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderCiclosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "ListadoCiclosBlock");

            $iRecordsTotal = 0;
            $aCiclos = SeguimientosController::getInstance()->getCiclos($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aCiclos)>0){
                foreach ($aCiclos as $oCiclo){
                    $hrefEditarCiclo = $this->getUrlFromRoute("adminObjetivosCurricularesFormularioCiclo", true)."?editar=1&id=".$oCiclo->getId();

                    $this->getTemplate()->set_var("hrefEditarCiclo", $hrefEditarCiclo);

                    $this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oCiclo->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionNivel", $oCiclo->getNivel()->getDescripcion());

                    $this->getTemplate()->parse("CicloBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsCiclosBlock", "");
            }else{
                $this->getTemplate()->set_var("CicloBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function formularioCiclo()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderCiclosBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "FormCicloBlock");

        if($this->getRequest()->has('editar')){
            $this->editarCicloForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearCicloForm();
        }
    }

    private function editarCicloForm()
    {
        try{
            $iCategoriaId = $this->getRequest()->getParam('id');

            if(empty($iCategoriaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar categoría");
            $this->getTemplate()->set_var("SubmitCrearCategoriaBlock", "");

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);

            $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
            $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
            $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    private function crearCicloForm()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

            $this->getTemplate()->set_var("sTituloForm", "Crear nueva categoria");
            $this->getTemplate()->set_var("SubmitModificarCategoriaBlock", "");
            $this->getTemplate()->set_var("EditarFotoBlock", "");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    public function procesarArea()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('crearArea')){
            $this->crearArea();
            return;
        }

        if($this->getRequest()->has('modificarArea')){
            $this->modificarArea();
            return;
        }

        if($this->getRequest()->has('borrarArea')){
            $this->borrarArea();
            return;
        }
    }

    private function crearArea()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sNombre = $this->getRequest()->getPost("nombre");
            $sDescripcion = $this->getRequest()->getPost("descripcion");

            if(AdminController::getInstance()->verificarExisteCategoria($oCategoria)){
                $this->getJsonHelper()->setMessage("Ya existe una categoría con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oCategoria = new stdClass();
            $oCategoria->sNombre = $sNombre;
            $oCategoria->sDescripcion = $sDescripcion;
            $oCategoria->sUrlToken = $this->getInflectorHelper()->urlize($sNombre);
            $oCategoria = Factory::getCategoriaInstance($oCategoria);

            AdminController::getInstance()->guardarCategoria($oCategoria);
            $this->getJsonHelper()->setMessage($mensaje);
            $this->getJsonHelper()->setValor("accion", "crearArea");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarArea()
    {
        try{
            $iCategoriaId = $this->getRequest()->getPost("iCategoriaId");

            if(AdminController::getInstance()->verificarExisteCategoria($oCategoria)){
                $this->getJsonHelper()->setMessage("Ya existe una categoría con ese nombre.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);
            $oCategoria->setNombre($sNombre);
            $oCategoria->setUrlToken($this->getInflectorHelper()->urlize($sNombre));
            $oCategoria->setDescripcion($sDescripcion);

            AdminController::getInstance()->guardarCategoria($oCategoria);
            $this->getJsonHelper()->setMessage($mensaje);
            $this->getJsonHelper()->setValor("accion", "modificarArea");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function borrarArea()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iCategoriaId = $this->getRequest()->getParam('iCategoriaId');
        if(empty($iCategoriaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarCategoria($iCategoriaId);

                $this->restartTemplate();

                if($result){
                    $msg = "La categoría fue eliminada del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar la categoría del sistema. Compruebe que no haya ningún software asociado.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){
            throw $e;
        }
    }

    public function listarAreas()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderAreasBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "ListadoAreasBlock");

            $iRecordsTotal = 0;
            $aAreas = SeguimientosController::getInstance()->getAreas($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aAreas)>0){
                foreach ($aAreas as $oArea){
                    $hrefEditarArea = $this->getUrlFromRoute("adminObjetivosCurricularesFormularioArea", true)."?editar=1&id=".$oArea->getId();

                    $this->getTemplate()->set_var("hrefEditarArea", $hrefEditarArea);

                    $this->getTemplate()->set_var("iAreaId", $oArea->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oArea->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionNivel", $oArea->getCiclo()->getNivel()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionCiclo", $oArea->getCiclo()->getDescripcion());

                    $this->getTemplate()->parse("AreaBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsAreasBlock", "");
            }else{
                $this->getTemplate()->set_var("AreaBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function formularioArea()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "widgetsContent", "HeaderAreasBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "mainContent", "FormAreaBlock");

        if($this->getRequest()->has('editar')){
            $this->editarAreaForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearAreaForm();
        }
    }

    private function editarAreaForm()
    {
        try{
            $iCategoriaId = $this->getRequest()->getParam('id');

            if(empty($iCategoriaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar categoría");
            $this->getTemplate()->set_var("SubmitCrearCategoriaBlock", "");

            $oCategoria = ComunidadController::getInstance()->obtenerCategoriaById($iCategoriaId);

            $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
            $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
            $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    private function crearAreaForm()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

            $this->getTemplate()->set_var("sTituloForm", "Crear nueva categoria");
            $this->getTemplate()->set_var("SubmitModificarCategoriaBlock", "");
            $this->getTemplate()->set_var("EditarFotoBlock", "");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
}