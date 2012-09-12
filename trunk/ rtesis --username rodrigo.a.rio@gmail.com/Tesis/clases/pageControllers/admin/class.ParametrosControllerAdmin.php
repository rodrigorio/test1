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

    public function listarParametrosUsuario()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionAvanzadas");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "widgetsContent", "HeaderUsuariosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "mainContent", "ListadoParametrosUsuarioBlock");

            $iRecordsTotal = 0;
            $aParametros = AdminController::getInstance()->obtenerParametrosDinamicosUsuario($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);

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

    /**
     * Lista todos los parametros que actualmente estan asociados a todos los usuarios del sistema
     */
    public function listarParametrosAsociadosUsuarios()
    {

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
        
        if($this->getRequest()->has('crearParametro')){
            $this->crearParametro();
            return;
        }

        if($this->getRequest()->has('modificarParametro')){
            $this->modificarParametro();
            return;
        }

        if($this->getRequest()->has('crearAsociacionSistema')){
            $this->asociarParametroSistema();
            return;
        }

        if($this->getRequest()->has('crearAsociacionControlador')){
            $this->asociarParametroControlador();
            return;
        }

        if($this->getRequest()->has('crearAsociacionParametroUsuarios')){
            $this->crearAsociacionParametroUsuarios();
            return;
        }

        if($this->getRequest()->has('modificarAsociacionSistema')){
            $this->modificarValorParametroSistema();
            return;
        }

        if($this->getRequest()->has('modificarAsociacionControlador')){
            $this->modificarValorParametroControlador();
            return;
        }

        if($this->getRequest()->has('modificarParametroUsuario')){
            $this->modificarValorParametroUsuario();
            return;
        }

        if($this->getRequest()->has('listarParametrosAsociadosUsuarios')){
            $this->listarParametrosAsociadosUsuarios();
            return;
        }

        if($this->getRequest()->has('eliminarAsociacionUsuarios')){
            $this->eliminarAsociacionParametroUsuarios();
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

        if($this->getRequest()->has('modificarValorParametroUsuario')){
            $this->formModificarParametroUsuario();
            return;
        }

        if($this->getRequest()->has('asociarParametroUsuarios')){
            $this->formAsociarParametroUsuarios();
            return;
        }
    }

    private function formParametroSistema()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroSistemaBlock");

            if($this->getRequest()->has('asociarParametroSistema')){
                $this->getTemplate()->unset_blocks("SubmitModificarAsociacionSistemaBlock");
                $this->getTemplate()->unset_blocks("ModificarAsociacionSistemaBlock");

                $sTituloForm = "Crear";

                $oParametro = null;
                $sValor = "";

                //select con parametros existentes
                $iRecordsTotal = 0;
                $aParametros = AdminController::getInstance()->obtenerParametros($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);
                foreach($aParametros as $oParametro){
                    $value = $oParametro->getId();
                    $text = $oParametro->getNamespace();
                    $this->getTemplate()->set_var("iParametroId", $value);
                    $this->getTemplate()->set_var("sParametroNombre", $text);
                    $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                    $this->getTemplate()->parse("OptionParametroBlock", true);
                }
            }else{
                $iParametroId = $this->getRequest()->getParam('iParametroId');
                if(empty($iParametroId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearAsociacionSistemaBlock");
                $this->getTemplate()->unset_blocks("CrearAsociacionSistemaBlock");

                $oParametro = AdminController::getInstance()->getParametroSistema($iParametroId);

                $sTituloForm = "Modificar";

                $sParametro = $oParametro->getNamespace();
                $sValor = $oParametro->getValor();

                $this->getTemplate()->set_var("sParametro", $sParametro);
                $this->getTemplate()->set_var("iParametroId", $iParametroId);
                $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                $this->getTemplate()->set_var("sDescripcion", $oParametro->getDescripcion());
            }

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
            $this->getTemplate()->set_var("sValor", $sValor);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formParametroControlador()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioParametroControladorBlock");

            if($this->getRequest()->has('asociarParametroControlador')){
                $this->getTemplate()->unset_blocks("SubmitModificarAsociacionControladorBlock");
                $this->getTemplate()->unset_blocks("ModificarAsociacionControladorBlock");

                $sTituloForm = "Crear";

                $oParametro = null;
                $oControlador = null;
                $sValor = "";

                //select con parametros existentes
                $iRecordsTotal = 0;
                $aParametros = AdminController::getInstance()->obtenerParametros($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);
                foreach($aParametros as $oParametro){
                    $value = $oParametro->getId();
                    $text = $oParametro->getNamespace();
                    $this->getTemplate()->set_var("iParametroId", $value);
                    $this->getTemplate()->set_var("sParametroNombre", $text);
                    $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                    $this->getTemplate()->parse("OptionParametroBlock", true);
                }

                //select con controladores de pagina existentes
                $iRecordsTotal = 0;
                $aControladoresPagina = AdminController::getInstance()->obtenerControladoresPagina($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);
                foreach($aControladoresPagina as $oControladorPagina){
                    $value = $oControladorPagina->getId();
                    $text = $oControladorPagina->getKey();
                    $this->getTemplate()->set_var("iControladorId", $value);
                    $this->getTemplate()->set_var("sControlador", $text);
                    $this->getTemplate()->parse("OptionControladorBlock", true);
                }
            }else{
                $iParametroId = $this->getRequest()->getParam('iParametroId');
                $iControladorId = $this->getRequest()->getParam('iControladorId');
                if(empty($iParametroId) || empty($iControladorId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearAsociacionControladorBlock");
                $this->getTemplate()->unset_blocks("CrearAsociacionControladorBlock");

                $oParametro = AdminController::getInstance()->getParametroControlador($iParametroId, $iControladorId);
                $oControladorPagina = AdminController::getInstance()->getControladorPaginaById($iControladorId);

                $sTituloForm = "Modificar";

                $sParametro = $oParametro->getNamespace();
                $sValor = $oParametro->getValor();

                $this->getTemplate()->set_var("sParametro", $sParametro);
                $this->getTemplate()->set_var("iParametroId", $iParametroId);
                $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                $this->getTemplate()->set_var("sDescripcion", $oParametro->getDescripcion());

                $this->getTemplate()->set_var("sControlador", $oControladorPagina->getKey());
                $this->getTemplate()->set_var("iControladorId", $oControladorPagina->getId());
            }

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
            $this->getTemplate()->set_var("sValor", $sValor);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formModificarParametroUsuario()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioModificarParametroUsuarioBlock");

            $iParametroId = $this->getRequest()->getParam('iParametroId');
            $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
            if(empty($iParametroId) || empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oParametro = AdminController::getInstance()->getParametroUsuario($iParametroId, $iUsuarioId);
            $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

            $sParametro = $oParametro->getNamespace();
            $sValor = $oParametro->getValor();

            $this->getTemplate()->set_var("sParametro", $sParametro);
            $this->getTemplate()->set_var("iParametroId", $iParametroId);
            $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
            $this->getTemplate()->set_var("sDescripcion", $oParametro->getDescripcion());

            $this->getTemplate()->set_var("sNombreUsuario", $oUsuario->getNombre()." ".$oUsuario->getApellido());
            $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
            
            $this->getTemplate()->set_var("sValor", $sValor);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function formAsociarParametroUsuarios()
    {
        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/parametros.gui.html", "popUpContent", "FormularioAsociarParametroUsuariosBlock");

            //select con parametros existentes
            $iRecordsTotal = 0;
            $aParametros = AdminController::getInstance()->obtenerParametros($filtro = array(), $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iMinLimit = null, $iItemsForPage = null);
            foreach($aParametros as $oParametro){
                $value = $oParametro->getId();
                $text = $oParametro->getNamespace();
                $this->getTemplate()->set_var("iParametroId", $value);
                $this->getTemplate()->set_var("sParametroNombre", $text);
                $this->getTemplate()->set_var("sTipo", $oParametro->getTipo());
                $this->getTemplate()->parse("OptionParametroBlock", true);
            }

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

    private function asociarParametroSistema()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("asociarParametroSistema", "1");

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');
            
            //me fijo que la asociacion del parametro al sistema no exista.                       
            if(AdminController::getInstance()->existeParametroSistema($iParametroId))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El parametro ya esta asociado al sistema.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oParametroSistema = new stdClass();
            $oParametroSistema->iId = $iParametroId;
            $oParametroSistema->sValor = $this->getRequest()->getPost("valor");
            
            $oParametroSistema = Factory::getParametroSistemaInstance($oParametroSistema);
            
            AdminController::getInstance()->guardarParametroSistema($oParametroSistema);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();   
    }

    private function asociarParametroControlador()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("asociarParametroControlador", "1");

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');
            $iControladorId = $this->getRequest()->getPost('iControladorIdForm');

            if(AdminController::getInstance()->existeParametroControlador($iParametroId, $iControladorId))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El parametro ya esta asociado al controlador.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oParametroControlador = new stdClass();
            $oParametroControlador->iId = $iParametroId;
            $oParametroControlador->iGrupoId = $iControladorId;
            $oParametroControlador->sValor = $this->getRequest()->getPost("valor");

            $oParametroControlador = Factory::getParametroControladorInstance($oParametroControlador);

            AdminController::getInstance()->guardarParametroControlador($oParametroControlador);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearAsociacionParametroUsuarios()
    {
        try{            
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');

            if(AdminController::getInstance()->existeParametroUsuarios($iParametroId))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El parametro ya esta asociado a los usuarios del sistema.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oParametroUsuario = new stdClass();
            $oParametroUsuario->iId = $iParametroId;
            $oParametroUsuario->sValor = $this->getRequest()->getPost("valor");

            $oParametroUsuario = Factory::getParametroUsuarioInstance($oParametroUsuario);

            AdminController::getInstance()->asociaParametroUsuariosSistema($oParametroUsuario);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function modificarValorParametroSistema()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("modificarParametroSistema", "1");

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');

            $oParametroSistema = AdminController::getInstance()->getParametroSistema($iParametroId);
            $oParametroSistema->setValor($this->getRequest()->getPost("valor"));

            AdminController::getInstance()->guardarParametroSistema($oParametroSistema);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse(); 
    }  
    
    private function modificarValorParametroControlador()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("modificarParametroControlador", "1");

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');
            $iControladorId = $this->getRequest()->getPost('iControladorIdForm');

            $oParametroControlador = AdminController::getInstance()->getParametroControlador($iParametroId, $iControladorId);
            $oParametroControlador->setValor($this->getRequest()->getPost("valor"));

            AdminController::getInstance()->guardarParametroControlador($oParametroControlador);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }  
    
    private function modificarValorParametroUsuario()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iParametroId = $this->getRequest()->getPost('iParametroIdForm');
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioIdForm');

            $oParametroUsuario = AdminController::getInstance()->getParametroUsuario($iParametroId, $iUsuarioId);
            $oParametroUsuario->setValor($this->getRequest()->getPost("valor"));

            AdminController::getInstance()->guardarParametroUsuario($oParametroUsuario);

            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function eliminarAsociacionSistema()
    {
        $iParametroId = $this->getRequest()->getParam('iParametroId');
        if(empty($iParametroId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oParametroSistema = AdminController::getInstance()->getParametroSistema($iParametroId);
            $result = AdminController::getInstance()->eliminarParametroSistema($oParametroSistema);

            $this->restartTemplate();

            if($result){
                $msg = "Se elimino la asociacion entre el parametro y el sistema. Tenga en cuenta que puede mantenerse en las variables de sesion por unos minutos.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "No se pudo eliminar la asociacion entre el parametro y el sistema.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "No se pudo eliminar la asociacion entre el parametro y el sistema.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function eliminarAsociacionControlador()
    {
        $iParametroId = $this->getRequest()->getParam('iParametroId');
        $iControladorId = $this->getRequest()->getParam('iControladorId');

        if(empty($iParametroId) || empty($iControladorId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oParametroControlador = AdminController::getInstance()->getParametroControlador($iParametroId, $iControladorId);
            $result = AdminController::getInstance()->eliminarParametroControlador($oParametroControlador);

            $this->restartTemplate();

            if($result){
                $msg = "Se elimino la asociacion entre el parametro y el controlador. Tenga en cuenta que puede mantenerse en las variables de sesion por unos minutos.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "No se pudo eliminar la asociacion entre el parametro y el controlador.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "No se pudo eliminar la asociacion entre el parametro y el controlador.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function eliminarAsociacionParametroUsuarios()
    {
        $iParametroId = $this->getRequest()->getParam('iParametroId');

        if(empty($iParametroId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $result = AdminController::getInstance()->eliminarAsociacionParametroUsuarios($iParametroId);

            $this->restartTemplate();

            if($result){
                $msg = "Se elimino la asociacion entre el parametro y los usuarios del sistema. Tenga en cuenta que puede mantenerse en las variables de sesion por unos minutos.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "No se pudo eliminar la asociacion entre el parametro los usuarios del sistema.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "No se pudo eliminar la asociacion entre el parametro los usuarios del sistema.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}