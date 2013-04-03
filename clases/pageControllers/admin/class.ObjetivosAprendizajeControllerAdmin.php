<?php

/**
 * Esta clase tiene todo el manejo de las vistas del ABM de la rama
 * Nivel, Ciclo, Area, Eje, Obj Aprendizaje
 *
 */
class ObjetivosAprendizajeControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "jsContent", "JsContent");
        
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
        
        //select con ajax
        if($this->getRequest()->has('ciclosByNivel')){
            $this->getCiclosByNivel();
            return;
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

        //select con ajax
        if($this->getRequest()->has('areasByCiclo')){
            $this->getAreasByCiclo();
            return;
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

        //select con ajax
        if($this->getRequest()->has('ejesByArea')){
            $this->getEjesByArea();
            return;
        }
    }

    public function procesarEje()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('crearEje')){
            $this->crearEje();
            return;
        }

        if($this->getRequest()->has('modificarEje')){
            $this->modificarEje();
            return;
        }

        if($this->getRequest()->has('borrarEje')){
            $this->borrarEje();
            return;
        }
    }

    public function procesarObjetivoAprendizaje()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('crearObjetivoAprendizaje')){
            $this->crearObjetivoAprendizaje();
            return;
        }

        if($this->getRequest()->has('modificarObjetivoAprendizaje')){
            $this->modificarObjetivoAprendizaje();
            return;
        }

        if($this->getRequest()->has('borrarObjetivoAprendizaje')){
            $this->borrarObjetivoAprendizaje();
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

    private function crearCiclo()
    {       
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $iNivelId = $this->getRequest()->getPost("nivel");
            $oNivel = AdminController::getInstance()->getNivelById($iNivelId);
                    
            if(AdminController::getInstance()->existeCicloByDescripcion($sDescripcion, $oNivel)){
                $this->getJsonHelper()->setMessage("Ya existe un ciclo con ese nombre dentro del nivel.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oCiclo = new stdClass();
            $oCiclo->sDescripcion = $sDescripcion;
            $oCiclo = Factory::getCicloInstance($oCiclo);
            $oCiclo->setNivel($oNivel);

            AdminController::getInstance()->guardarCiclo($oCiclo);
            $this->getJsonHelper()->setMessage("El ciclo fue creado con éxito dentro del nivel");
            $this->getJsonHelper()->setValor("accion", "crearCiclo");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearArea()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $iCicloId = $this->getRequest()->getPost("ciclo");
            $oCiclo = AdminController::getInstance()->getCicloById($iCicloId);
            
            if(AdminController::getInstance()->verificarExisteAreaByDescripcion($sDescripcion, $oCiclo)){
                $this->getJsonHelper()->setMessage("Ya existe un área con ese nombre en el ciclo seleccionado.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oArea = new stdClass();
            $oArea->sDescripcion = $sDescripcion;
            $oArea = Factory::getAreaInstance($oArea);
            $oArea->setCiclo($oCiclo);

            AdminController::getInstance()->guardarArea($oArea);
            $this->getJsonHelper()->setMessage("El área fue creada con éxito dentro del ciclo");
            $this->getJsonHelper()->setValor("accion", "crearArea");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearEje()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $sContenidos = $this->getRequest()->getPost("contenidos");
            $iAreaId = $this->getRequest()->getPost("area");
            $oArea = AdminController::getInstance()->getAreaById($iAreaId);

            if(AdminController::getInstance()->verificarExisteEjeByDescripcion($sDescripcion, $oArea)){
                $this->getJsonHelper()->setMessage("Ya existe un Eje Temático con ese nombre en el área seleccionada.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }            
            
            $oEjeTematico = new stdClass();
            $oEjeTematico->sDescripcion = $sDescripcion;
            $oEjeTematico->sContenidos = $sContenidos;
            $oEjeTematico = Factory::getEjeTematicoInstance($oEjeTematico);
            $oEjeTematico->setArea($oArea);
            
            AdminController::getInstance()->guardarEjeTematico($oEjeTematico);
            $this->getJsonHelper()->setMessage("El Eje Temático fue creado con éxito dentro del Área");
            $this->getJsonHelper()->setValor("accion", "crearEje");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearObjetivoAprendizaje()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $iEjeTematicoId = $this->getRequest()->getPost("ejeTematico");
            $oEjeTematico = AdminController::getInstance()->getEjeTematicoById($iEjeTematicoId);

            $oObjetivoAprendizaje = new stdClass();
            $oObjetivoAprendizaje->sDescripcion = $sDescripcion;
            $oObjetivoAprendizaje = Factory::getObjetivoAprendizajeInstance($oObjetivoAprendizaje);
            $oObjetivoAprendizaje->setEjeTematico($oEjeTematico);

            AdminController::getInstance()->guardarObjetivoAprendizaje($oObjetivoAprendizaje);
            $this->getJsonHelper()->setMessage("El Objetivo de Aprendizaje fue creado con éxito dentro del Eje Temático");
            $this->getJsonHelper()->setValor("accion", "crearObjetivoAprendizaje");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarNivel()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iNivelId = $this->getRequest()->getPost("iNivelId");

            $oNivel = AdminController::getInstance()->getNivelById($iNivelId);

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
    
    private function modificarCiclo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $iCicloId = $this->getRequest()->getPost("iCicloId");
            $iNivelId = $this->getRequest()->getPost("nivel");
            $sDescripcion = $this->getRequest()->getPost("descripcion");

            $oCiclo = AdminController::getInstance()->getCicloById($iCicloId);
            $oNivel = AdminController::getInstance()->getNivelById($iNivelId);

            if(!empty($sDescripcion) && $sDescripcion !== $oCiclo->getDescripcion()){
                if(AdminController::getInstance()->existeCicloByDescripcion($sDescripcion, $oNivel)){
                    $this->getJsonHelper()->setMessage("Ya existe un ciclo con esa descripción en el nivel seleccionado.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oCiclo->setDescripcion($sDescripcion);
            $oCiclo->setNivel($oNivel);

            AdminController::getInstance()->guardarCiclo($oCiclo);
            $this->getJsonHelper()->setMessage("El ciclo fue modificado con éxito dentro del nivel");
            $this->getJsonHelper()->setValor("accion", "modificarCiclo");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarArea()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $iAreaId = $this->getRequest()->getPost("iAreaId");
            $iCicloId = $this->getRequest()->getPost("ciclo");
            $oCiclo = AdminController::getInstance()->getCicloById($iCicloId);
            $oArea = AdminController::getInstance()->getAreaById($iAreaId);            

            if(!empty($sDescripcion) && $sDescripcion !== $oArea->getDescripcion()){
                if(AdminController::getInstance()->verificarExisteAreaByDescripcion($sDescripcion, $oCiclo)){
                    $this->getJsonHelper()->setMessage("Ya existe un área con ese nombre en el ciclo.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oArea->setDescripcion($sDescripcion);
            $oArea->setCiclo($oCiclo);

            AdminController::getInstance()->guardarArea($oArea);
            $this->getJsonHelper()->setMessage("El área fue modificada con éxito dentro del ciclo");
            $this->getJsonHelper()->setValor("accion", "modificarArea");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarEje()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $sContenidos = $this->getRequest()->getPost("contenidos");
            $iEjeId = $this->getRequest()->getPost("iEjeId");
            $iAreaId = $this->getRequest()->getPost("area");

            $oArea = AdminController::getInstance()->getAreaById($iAreaId);
            $oEje = AdminController::getInstance()->getEjeTematicoById($iEjeId);            

            if(!empty($sDescripcion) && $sDescripcion !== $oEje->getDescripcion()){
                if(AdminController::getInstance()->verificarExisteEjeByDescripcion($sDescripcion, $oArea)){
                    $this->getJsonHelper()->setMessage("Ya existe un Eje Temático con ese nombre en el área seleccionada.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oEje->setDescripcion($sDescripcion);
            $oEje->setContenidos($sContenidos);
            $oEje->setArea($oArea);

            AdminController::getInstance()->guardarEjeTematico($oEje);
            $this->getJsonHelper()->setMessage("El Eje Temático fue modificado con éxito dentro del Área");
            $this->getJsonHelper()->setValor("accion", "modificarEje");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarObjetivoAprendizaje()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sDescripcion = $this->getRequest()->getPost("descripcion");
            $iObjetivoAprendizajeId = $this->getRequest()->getPost("iObjetivoAprendizajeId");
            $iEjeTematicoId = $this->getRequest()->getPost("ejeTematico");

            $oEjeTematico = AdminController::getInstance()->getEjeTematicoById($iEjeTematicoId);
            $oObjetivoAprendizaje = AdminController::getInstance()->getObjetivoAprendizajeById($iObjetivoAprendizajeId);

            $oObjetivoAprendizaje->setDescripcion($sDescripcion);
            $oObjetivoAprendizaje->setEjeTematico($oEjeTematico);

            AdminController::getInstance()->guardarObjetivoAprendizaje($oObjetivoAprendizaje);
            $this->getJsonHelper()->setMessage("El Objetivo de Aprendizaje fue modificado con éxito dentro del Eje Temático");
            $this->getJsonHelper()->setValor("accion", "modificarObjetivoAprendizaje");
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
                $oNivel = AdminController::getInstance()->getNivelById($iNivelId);
                
                $result = AdminController::getInstance()->eliminarNivel($oNivel);

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

    private function borrarCiclo()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iCicloId = $this->getRequest()->getParam('iCicloId');
        if(empty($iCicloId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $oCiclo = AdminController::getInstance()->getCicloById($iCicloId);
                $result = AdminController::getInstance()->eliminarCiclo($oCiclo);

                $this->restartTemplate();

                if($result){
                    $msg = "El ciclo fue eliminado del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar el ciclo del sistema. Compruebe que no haya ningún área asociada.";
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

    private function borrarArea()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iAreaId = $this->getRequest()->getParam('iAreaId');
        if(empty($iAreaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarArea($iAreaId);

                $this->restartTemplate();

                if($result){
                    $msg = "El área fue eliminada del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar el área del sistema. Compruebe que no haya ningún eje temático asociado.";
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

    private function borrarEje()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iEjeId = $this->getRequest()->getParam('iEjeId');
        if(empty($iEjeId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarArea($iAreaId);

                $this->restartTemplate();

                if($result){
                    $msg = "El eje temático fue eliminado del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "No se pudo eliminar el eje temático del sistema. Compruebe que no haya ningún objetivo de aprendizaje asociado.";
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

    private function borrarObjetivoAprendizaje()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iObjetivoAprendizajeId = $this->getRequest()->getParam('iObjetivoAprendizajeId');
        if(empty($iObjetivoAprendizajeId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            try{
                $result = AdminController::getInstance()->eliminarObjetivoAprendizaje($iObjetivoAprendizajeId);

                $this->restartTemplate();

                if($result){
                    $msg = "El objetivo aprendizaje fue eliminado del sistema";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
                }

            }catch(Exception $e){
                $msg = "Ocurrió un error. No se pudo eliminar el objetivo de aprendizaje del sistema.";
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

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderNivelesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "ListadoNivelesBlock");
            
            $this->getTemplate()->set_var("hrefCrearNivel", $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioNivel", true)."?crear=1");

            $iRecordsTotal = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aNiveles)>0){
                foreach($aNiveles as $oNivel){

                    $hrefEditarNivel = $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioNivel", true)."?editar=1&id=".$oNivel->getId();

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

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderCiclosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "ListadoCiclosBlock");

            $this->getTemplate()->set_var("hrefCrearCiclo", $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioCiclo", true)."?crear=1");

            $iRecordsTotal = 0;
            $aCiclos = AdminController::getInstance()->getCiclos($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aCiclos)>0){
                foreach ($aCiclos as $oCiclo){
                    $hrefEditarCiclo = $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioCiclo", true)."?editar=1&id=".$oCiclo->getId();

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

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderAreasBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "ListadoAreasBlock");

            $this->getTemplate()->set_var("hrefCrearArea", $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioArea", true)."?crear=1");

            $iRecordsTotal = 0;
            $aAreas = AdminController::getInstance()->getAreas($filtro = array(), $iRecordsTotal, null, null, null, null);
            if(count($aAreas)>0){
                foreach ($aAreas as $oArea){
                    $hrefEditarArea = $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioArea", true)."?editar=1&id=".$oArea->getId();

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
            throw $e;
        }
    }

    public function listarEjes()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderEjesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "ListadoEjesBlock");

            $this->getTemplate()->set_var("hrefCrearEje", $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioEje", true)."?crear=1");

            $iRecordsTotal = 0;
            $aEjes = AdminController::getInstance()->getEjes($filtro = array(), $iRecordsTotal, null, null, null, null);
            
            if(count($aEjes)>0){
                foreach($aEjes as $oEje){
                    $hrefEditarEje = $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioEje", true)."?editar=1&id=".$oEje->getId();

                    $this->getTemplate()->set_var("hrefEditarEje", $hrefEditarEje);

                    $this->getTemplate()->set_var("iEjeId", $oEje->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oEje->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionNivel", $oEje->getArea()->getCiclo()->getNivel()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionCiclo", $oEje->getArea()->getCiclo()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionArea", $oEje->getArea()->getDescripcion());

                    $this->getTemplate()->parse("EjeBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsEjesBlock", "");
            }else{
                $this->getTemplate()->set_var("EjeBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function listarObjetivosAprendizaje()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderObjetivosAprendizajeBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "ListadoObjetivosAprendizajeBlock");

            $this->getTemplate()->set_var("hrefCrearObjetivoAprendizaje", $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioObjetivoAprendizaje", true)."?crear=1");

            $iRecordsTotal = 0;
            $aObjetivosAprendizaje = AdminController::getInstance()->getObjetivosAprendizaje($filtro = array(), $iRecordsTotal, null, null, null, null);
           
            if(count($aObjetivosAprendizaje)>0){
                foreach ($aObjetivosAprendizaje as $oObjetivoAprendizaje){
                    $hrefEditarObjetivoAprendizaje = $this->getUrlFromRoute("adminObjetivosAprendizajeFormularioObjetivoAprendizaje", true)."?editar=1&id=".$oObjetivoAprendizaje->getId();

                    $this->getTemplate()->set_var("hrefEditarObjetivoAprendizaje", $hrefEditarObjetivoAprendizaje);

                    $this->getTemplate()->set_var("iObjetivoAprendizajeId", $oObjetivoAprendizaje->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oObjetivoAprendizaje->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionNivel", $oObjetivoAprendizaje->getEjeTematico()->getArea()->getCiclo()->getNivel()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionCiclo", $oObjetivoAprendizaje->getEjeTematico()->getArea()->getCiclo()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionArea", $oObjetivoAprendizaje->getEjeTematico()->getArea()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionEje", $oObjetivoAprendizaje->getEjeTematico()->getDescripcion());
                                   
                    $this->getTemplate()->parse("ObjetivoAprendizajeBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsObjetivoAprendizajeBlock", "");
            }else{
                $this->getTemplate()->set_var("ObjetivoAprendizajeBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }
        
    public function formularioNivel()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderNivelesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "FormNivelBlock");

        if($this->getRequest()->has('editar')){
            $this->editarNivelForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearNivelForm();
        }
    }

    public function formularioCiclo()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderCiclosBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "FormCicloBlock");

        if($this->getRequest()->has('editar')){
            $this->editarCicloForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearCicloForm();
        }
    }

    public function formularioArea()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderAreasBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "FormAreaBlock");

        if($this->getRequest()->has('editar')){
            $this->editarAreaForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearAreaForm();
        }
    }

    public function formularioEje()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderEjesBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "FormEjeBlock");

        if($this->getRequest()->has('editar')){
            $this->editarEjeForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearEjeForm();
        }
    }

    public function formularioObjetivoAprendizaje()
    {
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerAdmin::setCabecera($this->getTemplate());
        IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "widgetsContent", "HeaderObjetivosAprendizajeBlock");
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosAprendizaje.gui.html", "mainContent", "FormObjetivoAprendizajeBlock");

        if($this->getRequest()->has('editar')){
            $this->editarObjetivoAprendizajeForm();
        }

        if($this->getRequest()->has('crear')){
            $this->crearObjetivoAprendizajeForm();
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
    
    private function crearCicloForm()
    {
        try{
            $this->getTemplate()->set_var("sTituloForm", "Crear nuevo Ciclo");
            $this->getTemplate()->set_var("SubmitModificarCicloBlock", "");

            //combo niveles
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("sNivelSelected", "");
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                $this->getTemplate()->parse("OptionSelectNivel", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }  
    
    private function crearAreaForm()
    {
        try{
            $this->getTemplate()->set_var("sTituloForm", "Crear nueva Area");
            $this->getTemplate()->set_var("SubmitModificarAreaBlock", "");

            //combo niveles
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("sNivelSelected", "");
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                $this->getTemplate()->parse("OptionSelectNivel", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function crearEjeForm()
    {
        try{
            $this->getTemplate()->set_var("sTituloForm", "Crear nuevo Eje Temático");
            $this->getTemplate()->set_var("SubmitModificarEjeBlock", "");

            //combo niveles
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("sNivelSelected", "");
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                $this->getTemplate()->parse("OptionSelectNivel", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function crearObjetivoAprendizajeForm()
    {
        try{
            $this->getTemplate()->set_var("sTituloForm", "Crear nuevo Objetivo de Aprendizaje");
            $this->getTemplate()->set_var("SubmitModificarObjetivoAprendizajeBlock", "");

            //combo niveles
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("sNivelSelected", "");
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                $this->getTemplate()->parse("OptionSelectNivel", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
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

            $oNivel = AdminController::getInstance()->getNivelById($iNivelId);

            $this->getTemplate()->set_var("iNivelId", $iNivelId);
            $this->getTemplate()->set_var("sDescripcion", $oNivel->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function editarCicloForm()
    {
        try{
            $iCicloId = $this->getRequest()->getParam('id');

            if(empty($iCicloId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar Ciclo");
            $this->getTemplate()->set_var("SubmitCrearCicloBlock", "");

            $oCiclo = AdminController::getInstance()->getCicloById($iCicloId);

            //combo niveles
            $iNivelId = $oCiclo->getNivel()->getId();
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                if($iNivelId == $oNivel->getId()){
                    $this->getTemplate()->set_var("sNivelSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectNivel", true);
                $this->getTemplate()->set_var("sNivelSelected", "");
            }

            $this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
            $this->getTemplate()->set_var("sDescripcion", $oCiclo->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function editarAreaForm()
    {
        try{
            $iAreaId = $this->getRequest()->getParam('id');

            if(empty($iAreaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar Área");
            $this->getTemplate()->set_var("SubmitCrearAreaBlock", "");

            $oArea = AdminController::getInstance()->getAreaById($iAreaId);
            
            //combo niveles
            $iNivelId = $oArea->getCiclo()->getNivel()->getId();
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                if($iNivelId == $oNivel->getId()){
                    $this->getTemplate()->set_var("sNivelSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectNivel", true);
                $this->getTemplate()->set_var("sNivelSelected", "");
            }

            //combo ciclos
            $iCicloId = $oArea->getCiclo()->getId();
            $iRecordsCiclos = 0;
            $aCiclos = AdminController::getInstance()->getCiclos($filtro = array(), $iRecordsCiclos, null, null, null, null);
            foreach ($aCiclos as $oCiclo){
                $this->getTemplate()->set_var("iValueCiclo", $oCiclo->getId());
                $this->getTemplate()->set_var("sDescripcionCiclo", $oCiclo->getDescripcion());
                if($iCicloId == $oCiclo->getId()){
                    $this->getTemplate()->set_var("sCicloSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectCiclo", true);
                $this->getTemplate()->set_var("sCicloSelected", "");
            }
            
            $this->getTemplate()->set_var("iAreaId", $oArea->getId());
            $this->getTemplate()->set_var("sDescripcion", $oArea->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function editarEjeForm()
    {
        try{
            $iEjeId = $this->getRequest()->getParam('id');

            if(empty($iEjeId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar Eje Temático");
            $this->getTemplate()->set_var("SubmitCrearEjeBlock", "");

            $oEje = AdminController::getInstance()->getEjeTematicoById($iEjeId);

            //combo niveles
            $iNivelId = $oEje->getArea()->getCiclo()->getNivel()->getId();
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                if($iNivelId == $oNivel->getId()){
                    $this->getTemplate()->set_var("sNivelSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectNivel", true);
                $this->getTemplate()->set_var("sNivelSelected", "");
            }

            //combo ciclos
            $iCicloId = $oEje->getArea()->getCiclo()->getId();
            $iRecordsCiclos = 0;
            $aCiclos = AdminController::getInstance()->getCiclos($filtro = array(), $iRecordsCiclos, null, null, null, null);
            foreach ($aCiclos as $oCiclo){
                $this->getTemplate()->set_var("iValueCiclo", $oCiclo->getId());
                $this->getTemplate()->set_var("sDescripcionCiclo", $oCiclo->getDescripcion());
                if($iCicloId == $oCiclo->getId()){
                    $this->getTemplate()->set_var("sCicloSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectCiclo", true);
                $this->getTemplate()->set_var("sCicloSelected", "");
            }

            //combo areas
            $iAreaId = $oEje->getArea()->getId();
            $iRecordsAreas = 0;
            $aAreas = AdminController::getInstance()->getAreas($filtro = array(), $iRecordsAreas, null, null, null, null);
            foreach ($aAreas as $oArea){
                $this->getTemplate()->set_var("iValueArea", $oArea->getId());
                $this->getTemplate()->set_var("sDescripcionArea", $oArea->getDescripcion());
                if($iAreaId == $oArea->getId()){
                    $this->getTemplate()->set_var("sAreaSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectArea", true);
                $this->getTemplate()->set_var("sAreaSelected", "");
            }

            $this->getTemplate()->set_var("iEjeId", $oEje->getId());
            $this->getTemplate()->set_var("sDescripcion", $oEje->getDescripcion());
            $this->getTemplate()->set_var("sContenidos", $oEje->getContenidos());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function editarObjetivoAprendizajeForm()
    {
        try{
            $iObjetivoAprendizajeId = $this->getRequest()->getParam('id');

            if(empty($iObjetivoAprendizajeId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->set_var("sTituloForm", "Modificar Objetivo Aprendizaje");
            $this->getTemplate()->set_var("SubmitCrearObjetivoAprendizajeBlock", "");

            $oObjetivoAprendizaje = AdminController::getInstance()->getObjetivoAprendizajeById($iObjetivoAprendizajeId);

            //combo niveles
            $iNivelId = $oObjetivoAprendizaje->getEjeTematico()->getArea()->getCiclo()->getNivel()->getId();
            $iRecordsNiveles = 0;
            $aNiveles = AdminController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
            foreach ($aNiveles as $oNivel){
                $this->getTemplate()->set_var("iValueNivel", $oNivel->getId());
                $this->getTemplate()->set_var("sDescripcionNivel", $oNivel->getDescripcion());
                if($iNivelId == $oNivel->getId()){
                    $this->getTemplate()->set_var("sNivelSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectNivel", true);
                $this->getTemplate()->set_var("sNivelSelected", "");
            }

            //combo ciclos
            $iCicloId = $oObjetivoAprendizaje->getEjeTematico()->getArea()->getCiclo()->getId();
            $iRecordsCiclos = 0;
            $aCiclos = AdminController::getInstance()->getCiclos($filtro = array(), $iRecordsCiclos, null, null, null, null);
            foreach ($aCiclos as $oCiclo){
                $this->getTemplate()->set_var("iValueCiclo", $oCiclo->getId());
                $this->getTemplate()->set_var("sDescripcionCiclo", $oCiclo->getDescripcion());
                if($iCicloId == $oCiclo->getId()){
                    $this->getTemplate()->set_var("sCicloSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectCiclo", true);
                $this->getTemplate()->set_var("sCicloSelected", "");
            }

            //combo areas
            $iAreaId = $oObjetivoAprendizaje->getEjeTematico()->getArea()->getId();
            $iRecordsAreas = 0;
            $aAreas = AdminController::getInstance()->getAreas($filtro = array(), $iRecordsAreas, null, null, null, null);
            foreach ($aAreas as $oArea){
                $this->getTemplate()->set_var("iValueArea", $oArea->getId());
                $this->getTemplate()->set_var("sDescripcionArea", $oArea->getDescripcion());
                if($iAreaId == $oArea->getId()){
                    $this->getTemplate()->set_var("sAreaSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectArea", true);
                $this->getTemplate()->set_var("sAreaSelected", "");
            }

            //combo ejes
            $iEjeId = $oObjetivoAprendizaje->getEjeTematico()->getId();
            $iRecordsEjes = 0;
            $aEjes = AdminController::getInstance()->getEjes($filtro = array(), $iRecordsEjes, null, null, null, null);
            
            foreach ($aEjes as $oEje){
                $this->getTemplate()->set_var("iValueEjeTematico", $oEje->getId());
                $this->getTemplate()->set_var("sDescripcionEjeTematico", $oEje->getDescripcion());
                if($iEjeId == $oEje->getId()){
                    $this->getTemplate()->set_var("sEjeTematicoSelected", "selected='selected'");
                }
                $this->getTemplate()->parse("OptionSelectEjeTematico", true);
                $this->getTemplate()->set_var("sEjeTematicoSelected", "");
            }

            $this->getTemplate()->set_var("iObjetivoAprendizajeId", $oObjetivoAprendizaje->getId());
            $this->getTemplate()->set_var("sDescripcion", $oObjetivoAprendizaje->getDescripcion());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function getCiclosByNivel(){
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iNivelId =  $this->getRequest()->getPost("iNivelId");

            if(empty($iNivelId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            $jCiclos = array();
            $iRecordsTotal = 0;
            $sOrderBy = $sOrder = $iIniLimit = $iRecordCount = null;
            $aCiclos = SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
            if(!empty($aCiclos)){
                foreach($aCiclos as $oCiclo){
                    $obj = new stdClass();
                    $obj->iId = $oCiclo->getId();
                    $obj->sDescripcion = $oCiclo->getDescripcion();
                    array_push($jCiclos, $obj);
                }
            }
            
            $this->getJsonHelper()->sendJson($jCiclos);
        }catch(Exception $e){
            throw $e;
        }
    }

    private function getAreasByCiclo(){
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iCicloId =  $this->getRequest()->getPost("iCicloId");

            if(empty($iCicloId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $jAreas = array();
            $iRecordsTotal = 0;
            $sOrderBy = $sOrder = $iIniLimit = $iRecordCount = null;
            $aAreas = SeguimientosController::getInstance()->getAreasByCicloId($iCicloId, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
            if(!empty($aAreas)){
                foreach($aAreas as $oArea){
                    $obj = new stdClass();
                    $obj->iId = $oArea->getId();
                    $obj->sDescripcion = $oArea->getDescripcion();
                    array_push($jAreas, $obj);
                }
            }

            $this->getJsonHelper()->sendJson($jAreas);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function getEjesByArea(){
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iAreaId =  $this->getRequest()->getPost("iAreaId");

            if(empty($iAreaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $jEjes = array();
            $iRecordsTotal = 0;
            $sOrderBy = $sOrder = $iIniLimit = $iRecordCount = null;
            $aEjes = SeguimientosController::getInstance()->getEjesByAreaId($iAreaId, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
            if(!empty($aEjes)){
                foreach($aEjes as $oEje){
                    $obj = new stdClass();
                    $obj->iId = $oEje->getId();
                    $obj->sDescripcion = $oEje->getDescripcion();
                    array_push($jEjes, $obj);
                }
            }

            $this->getJsonHelper()->sendJson($jEjes);
        }catch(Exception $e){
            throw $e;
        }
    }    
}