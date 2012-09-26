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
            $this->getJsonHelper()->setValor($accion, "crearNivel");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }

    private function modificarNivel()
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
            $this->getJsonHelper()->setValor($accion, $mensaje);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();   
    }

    private function borrarNivel()
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

    public function listarNiveles()
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
            $vCategoria = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($vCategoria)>0){
                foreach ($vCategoria as $oCategoria){

                    $hrefEditarCategoria = $this->getUrlFromRoute("adminCategoriaEditarCategoria", true)."?id=".$oCategoria->getId();

                    $this->getTemplate()->set_var("hrefEditarCategoria", $hrefEditarCategoria);

                    $sDescripcion = (null === $oCategoria->getDescripcion())?" - ":$oCategoria->getDescripcion();

                    $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
                    $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
                    $this->getTemplate()->parse("CategoriasBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("CategoriasBlock", "");
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
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

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

    private function crearNivelForm()
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
            $this->getJsonHelper()->setValor($accion, "crearNivel");
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
            $this->getJsonHelper()->setValor($accion, $mensaje);
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
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");

            $iRecordsTotal = 0;
            $vCategoria = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($vCategoria)>0){
                foreach ($vCategoria as $oCategoria){

                    $hrefEditarCategoria = $this->getUrlFromRoute("adminCategoriaEditarCategoria", true)."?id=".$oCategoria->getId();

                    $this->getTemplate()->set_var("hrefEditarCategoria", $hrefEditarCategoria);

                    $sDescripcion = (null === $oCategoria->getDescripcion())?" - ":$oCategoria->getDescripcion();

                    $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
                    $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
                    $this->getTemplate()->parse("CategoriasBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("CategoriasBlock", "");
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
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

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
            $this->getJsonHelper()->setValor($accion, "crearNivel");
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
            $this->getJsonHelper()->setValor($accion, $mensaje);
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
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");

            $iRecordsTotal = 0;
            $vCategoria = ComunidadController::getInstance()->obtenerCategoria($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($vCategoria)>0){
                foreach ($vCategoria as $oCategoria){

                    $hrefEditarCategoria = $this->getUrlFromRoute("adminCategoriaEditarCategoria", true)."?id=".$oCategoria->getId();

                    $this->getTemplate()->set_var("hrefEditarCategoria", $hrefEditarCategoria);

                    $sDescripcion = (null === $oCategoria->getDescripcion())?" - ":$oCategoria->getDescripcion();

                    $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
                    $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
                    $this->getTemplate()->parse("CategoriasBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("CategoriasBlock", "");
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
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "HeaderBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "FormCategoriaBlock");

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