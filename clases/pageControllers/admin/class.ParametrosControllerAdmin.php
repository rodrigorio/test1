<?php

class ParametrosControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listar();
    }

    /**
     * Lista objetos del tipo Parametro (si aun no tiene asociaciones)
     * unidos a objetos ParametroSistema ParametroControlador y ParametroUsuario.
     */
    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionAvanzadas");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "mainContent", "ListadoParametrosBlock");

            $iRecordsTotal = 0;
            $aParametros = AdminController::getInstance()->obtenerParametrosDinamicos($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);

            if(count($aParametros) > 0){

                foreach($aParametros as $oParametro){

                    $this->getTemplate()->set_var("iParametroId", $oParametro->getId());
                    $this->getTemplate()->set_var("sKey", $oParametro->getNamespace());
                    $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                    $this->getTemplate()->set_var("sDescripcion", $oParametro->getDescripcion());

                    $tipoAsociacion = "-";
                    $sValor = "-";                    
                    $sGrupo = "-";
                    $iGrupoId = "";
                    if(get_class($oParametro) !== 'Parametro'){
                        $this->getTemplate()->set_var("EliminarParametroBlock", "");

                        $tipoAsociacion = get_class($oParametro);
                        $sValor = $oParametro->getValor();

                        $iGrupoId = "null";
                        if(get_class($oParametro) !== 'ParametroSistema'){
                            $iGrupoId = $oParametro->getGrupoId();
                            $sGrupo = $oParametro->getGrupo();
                        }                        
                    }else{                        
                        $this->getTemplate()->set_var("EditAsociacionBlock", "");
                    }

                    $this->getTemplate()->set_var("iGrupoId", $iGrupoId);
                    $this->getTemplate()->set_var("tipoAsociacion", $tipoAsociacion);
                    $this->getTemplate()->set_var("sGrupo", $sGrupo);
                    $this->getTemplate()->set_var("sValor", $sValor);

                    $this->getTemplate()->parse("ParametroBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("EditAsociacionBlock");
                    $this->getTemplate()->delete_parsed_blocks("EliminarParametroBlock");
                }
                
                $this->getTemplate()->set_var("NoRecordsParametrosBlock", "");
            }else{
                $this->getTemplate()->set_var("ParametroBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron parametros dinamicos");
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

        if($this->getRequest()->has('eliminarParametro')){
            $this->eliminarParametro();
            return;
        }

        if($this->getRequest()->has('eliminarAsociacionSistema')){
            $this->eliminarAsociacionSistema();
            return;
        }

        if($this->getRequest()->has('eliminarAsociacionControlador')){
            $this->eliminarAsociacionControlador();
            return;
        }

        if($this->getRequest()->has('eliminarAsociacionUsuario')){
            $this->eliminarAsociacionUsuario();
            return;
        }
        
        if($this->getRequest()->has('crearParametro')){
            $this->crearParametro();
            return;
        }

        if($this->getRequest()->has('modificarParametro')){
            $this->modificarParametro();
            return;
        }

        if($this->getRequest()->has('asociarParametroSistema')){
            $this->asociarParametroSistema();
            return;
        }

        if($this->getRequest()->has('asociarParametroControlador')){
            $this->asociarParametroControlador();
            return;
        }

        if($this->getRequest()->has('asociarParametroUsuario')){
            $this->asociarParametroUsuario();
            return;
        }

        if($this->getRequest()->has('modificarValorParametroSistema')){
            $this->modificarValorParametroSistema();
            return;
        }

        if($this->getRequest()->has('modificarValorParametroControlador')){
            $this->modificarValorParametroControlador();
            return;
        }

        if($this->getRequest()->has('modificarValorParametroUsuario')){
            $this->modificarValorParametroUsuario();
            return;
        }
    }

    public function form()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearParametro') || $this->getRequest()->has('editarParametro')){
            $this->formParametro();
            return;
        }

        if($this->getRequest()->has('asociarParametroSistema') || $this->getRequest()->has('modificarValorParametroSistema')){
            $this->formParametroSistema();
            return;
        }

        if($this->getRequest()->has('asociarParametroControlador') || $this->getRequest()->has('modificarValorParametroControlador')){
            $this->formParametroControlador();
            return;
        }

        if($this->getRequest()->has('asociarParametroUsuario') || $this->getRequest()->has('modificarValorParametroUsuario')){
            $this->formParametroUsuario();
            return;
        }       
    }

    private function formParametroSistema()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroBlock");

            if($this->getRequest()->has('crearParametro')){
                $this->getTemplate()->unset_blocks("SubmitModificarParametroBlock");

                $sTituloForm = "Agregar";

                $oParametro = null;
                $iParametroId = "";
                $sNamespace = "";
                $sDescripcion = "";
                $sTipo = "";

            }else{

                $iParametroId = $this->getRequest()->getParam('iParametroId');
                if(empty($iParametroId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearParametroBlock");

                $oParametro = AdminController::getInstance()->getParametroById($iParametroId);

                $sTituloForm = "Modificar";

                $sNamespace = $oParametro->getNamespace();
                $sDescripcion = $oParametro->getDescripcion();
                $sTipo = $oParametro->getTipo();

                $this->getTemplate()->set_var("iParametroId", $iParametroId);
            }

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);

            switch($sTipo){
                case "boolean": $this->getTemplate()->set_var("sSelectedTipoBoolean", "selected='selected'"); break;
                case "numeric": $this->getTemplate()->set_var("sSelectedTipoNumeric", "selected='selected'"); break;
                case "string": $this->getTemplate()->set_var("sSelectedTipoString", "selected='selected'"); break;
            }

            $this->getTemplate()->set_var("sNamespace", $sNamespace);
            $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formParametroControlador()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroBlock");

            if($this->getRequest()->has('crearParametro')){
                $this->getTemplate()->unset_blocks("SubmitModificarParametroBlock");

                $sTituloForm = "Agregar";

                $oParametro = null;
                $iParametroId = "";
                $sNamespace = "";
                $sDescripcion = "";
                $sTipo = "";

            }else{

                $iParametroId = $this->getRequest()->getParam('iParametroId');
                if(empty($iParametroId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearParametroBlock");

                $oParametro = AdminController::getInstance()->getParametroById($iParametroId);

                $sTituloForm = "Modificar";

                $sNamespace = $oParametro->getNamespace();
                $sDescripcion = $oParametro->getDescripcion();
                $sTipo = $oParametro->getTipo();

                $this->getTemplate()->set_var("iParametroId", $iParametroId);
            }

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);

            switch($sTipo){
                case "boolean": $this->getTemplate()->set_var("sSelectedTipoBoolean", "selected='selected'"); break;
                case "numeric": $this->getTemplate()->set_var("sSelectedTipoNumeric", "selected='selected'"); break;
                case "string": $this->getTemplate()->set_var("sSelectedTipoString", "selected='selected'"); break;
            }

            $this->getTemplate()->set_var("sNamespace", $sNamespace);
            $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formParametroUsuario()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroBlock");

            if($this->getRequest()->has('crearParametro')){
                $this->getTemplate()->unset_blocks("SubmitModificarParametroBlock");

                $sTituloForm = "Agregar";

                $oParametro = null;
                $iParametroId = "";
                $sNamespace = "";
                $sDescripcion = "";
                $sTipo = "";

            }else{

                $iParametroId = $this->getRequest()->getParam('iParametroId');
                if(empty($iParametroId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearParametroBlock");

                $oParametro = AdminController::getInstance()->getParametroById($iParametroId);

                $sTituloForm = "Modificar";

                $sNamespace = $oParametro->getNamespace();
                $sDescripcion = $oParametro->getDescripcion();
                $sTipo = $oParametro->getTipo();

                $this->getTemplate()->set_var("iParametroId", $iParametroId);
            }

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);

            switch($sTipo){
                case "boolean": $this->getTemplate()->set_var("sSelectedTipoBoolean", "selected='selected'"); break;
                case "numeric": $this->getTemplate()->set_var("sSelectedTipoNumeric", "selected='selected'"); break;
                case "string": $this->getTemplate()->set_var("sSelectedTipoString", "selected='selected'"); break;
            }

            $this->getTemplate()->set_var("sNamespace", $sNamespace);
            $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formParametro()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroBlock");

            if($this->getRequest()->has('crearParametro')){
                $this->getTemplate()->unset_blocks("SubmitModificarParametroBlock");

                $sTituloForm = "Agregar";

                $oParametro = null;
                $iParametroId = "";
                $sNamespace = "";
                $sDescripcion = "";
                $sTipo = "";
                
            }else{
                
                $iParametroId = $this->getRequest()->getParam('iParametroId');
                if(empty($iParametroId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }                
                
                $this->getTemplate()->unset_blocks("SubmitCrearParametroBlock");

                $oParametro = AdminController::getInstance()->getParametroById($iParametroId);
                
                $sTituloForm = "Modificar";

                $sNamespace = $oParametro->getNamespace();
                $sDescripcion = $oParametro->getDescripcion();
                $sTipo = $oParametro->getTipo();

                $this->getTemplate()->set_var("iParametroId", $iParametroId);
            }
            
            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);

            switch($sTipo){
                case "boolean": $this->getTemplate()->set_var("sSelectedTipoBoolean", "selected='selected'"); break;
                case "numeric": $this->getTemplate()->set_var("sSelectedTipoNumeric", "selected='selected'"); break;
                case "string": $this->getTemplate()->set_var("sSelectedTipoString", "selected='selected'"); break;
            }

            $this->getTemplate()->set_var("sNamespace", $sNamespace);
            $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function crearParametro()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("crearParametro", "1");

            $oParametro = new stdClass();

            $oParametro->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oParametro->sNamespace = strtoupper($this->getRequest()->getPost("namespace"));

            $oParametro = Factory::getParametroInstance($oParametro);

            switch($this->getRequest()->getPost("tipo")){
                case "string": $oParametro->setTipoCadena(); break;
                case "boolean": $oParametro->setTipoBooleano(); break;
                case "numeric": $oParametro->setTipoNumerico(); break;
            }
            
            if(AdminController::getInstance()->existeParametro($oParametro))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El parametro ya existe en el sistema.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            AdminController::getInstance()->guardarParametro($oParametro);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            echo $e->getMessage();
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function modificarParametro()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("modificarParametro", "1");

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');
            $oParametro = AdminController::getInstance()->getParametroById($iParametroId);

            //me fijo que no exista otro parametro con el mismo namespace
            //solo verifico si el namespace del form es diferente al namespace actual
            if($oParametro->getNamespace() !== $this->getRequest()->getPost("namespace")){
                $oParametro->setNamespace(strtoupper($this->getRequest()->getPost("namespace")));
                if(AdminController::getInstance()->existeParametro($oParametro))
                {
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("El parametro ya existe en el sistema.");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }                
            }

            $oParametro->setDescripcion($this->getRequest()->getPost("descripcion"));

            switch($this->getRequest()->getPost("tipo")){
                case "string": $oParametro->setTipoCadena(); break;
                case "boolean": $oParametro->setTipoBooleano(); break;
                case "numeric": $oParametro->setTipoNumerico(); break;
            }
           
            AdminController::getInstance()->guardarParametro($oParametro);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function eliminarParametro()
    {
        $iParametroId = $this->getRequest()->getParam('iParametroId');
        if(empty($iParametroId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oParametro = AdminController::getInstance()->getParametroById($iParametroId);
            $result = AdminController::getInstance()->borrarParametro($oParametro);

            $this->restartTemplate();

            if($result){
                $msg = "El parametro fue eliminado del sistema. Tenga en cuenta que puede haber una copia estatica en el plugin de parametros";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "No se pudo eliminar el parametro del sistema, compruebe que el parametro no tenga asociaciones creadas.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "No se pudo eliminar el parametro del sistema, compruebe que el parametro no tenga asociaciones creadas.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}