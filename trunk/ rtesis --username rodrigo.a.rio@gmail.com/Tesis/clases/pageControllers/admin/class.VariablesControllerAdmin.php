<?php

/**
 * @author Matias Velilla
 *
 */
class VariablesControllerAdmin extends PageControllerAbstract
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

        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "jsContent", "JsContent");
        return $this;
    }

    public function index(){
        $this->listar();
    }

    public function procesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masVariables')){
            $this->masVariables();
            return;
        }

        if($this->getRequest()->has('agregarModalidad')){
            $this->agregarModalidad();
            return;
        }
    }

    public function listar()
    {
        try{
            //primero me fijo que este el id de unidad
            $iUnidadId = $this->getRequest()->getParam('id');
            if(empty($iUnidadId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oUnidad = AdminController::getInstance()->getUnidadById($iUnidadId);
            
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");
            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "mainContent", "ListadoVariablesBlock");
            
            $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
            $this->getTemplate()->set_var("sUnidadDescripcion", $oUnidad->getDescripcion(true));
            
            $aVariables = $oUnidad->getVariables();
            $this->getTemplate()->set_var("iUnidadId", $iUnidadId);            
            if(count($aVariables) > 0){

                $this->getTemplate()->set_var("NoRecordsVariablesBlock", "");

            	foreach ($aVariables as $oVariable){

                    $this->getTemplate()->set_var("iVariableId", $oVariable->getId());
                    $this->getTemplate()->set_var("sNombre", $oVariable->getNombre());
                    $this->getTemplate()->set_var("sTipoEnum", get_class($oVariable));

                    $sDescripcion = $oVariable->getDescripcion(true);
                    
                    if($oVariable->isVariableNumerica()){
                        $this->getTemplate()->set_var("sTipo", "Variable Numérica");
                        $iconoVariableBlock = "IconoTipoNumericaBlock";
                        $this->getTemplate()->set_var("sModalidades", "");
                    }

                    if($oVariable->isVariableTexto()){
                        $this->getTemplate()->set_var("sTipo", "Variable de Texto");
                        $iconoVariableBlock = "IconoTipoTextoBlock";
                        $this->getTemplate()->set_var("sModalidades", "");
                    }

                    if($oVariable->isVariableCualitativa()){
                        $this->getTemplate()->set_var("sTipo", "Variable Cualitativa");
                        $iconoVariableBlock = "IconoTipoCualitativaBlock";
                        $sModalidades = "<br><strong>Modalidades: </strong> ";
                        $aModalidades = $oVariable->getModalidades();
                        foreach($aModalidades as $oModalidad){
                            $sModalidades .= $oModalidad->getModalidad().", ";
                        }
                        $sModalidades = substr($sModalidades, 0, -2);
                        $sDescripcion .= $sModalidades;
                    }

                    $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

                    $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "iconoVariable", $iconoVariableBlock);
                    $this->getTemplate()->set_var("iconoVariable", $this->getTemplate()->pparse("iconoVariable"));
                    $this->getTemplate()->delete_parsed_blocks($iconoVariableBlock);
                    
                    $this->getTemplate()->parse("VariableBlock", true);
                }
            }else{                
                $this->getTemplate()->set_var("VariableBlock", "");
            }
             
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function masVariables()
    {
        //primero me fijo que este el id de unidad
        $iUnidadId = $this->getRequest()->getPost('id');
        if(empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);
        
        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "ajaxGrillaVariablesBlock", "GrillaVariablesBlock");

        $aVariables = $oUnidad->getVariables();
        if(count($aVariables) > 0){

            $this->getTemplate()->set_var("NoRecordsVariablesBlock", "");

            foreach ($aVariables as $oVariable){

                $this->getTemplate()->set_var("iVariableId", $oVariable->getId());
                $this->getTemplate()->set_var("sNombre", $oVariable->getNombre());
                $this->getTemplate()->set_var("sTipoEnum", get_class($oVariable));

                $sDescripcion = $oVariable->getDescripcion(true);
                
                if($oVariable->isVariableNumerica()){
                    $this->getTemplate()->set_var("sTipo", "Variable Numérica");
                    $iconoVariableBlock = "IconoTipoNumericaBlock";
                    $this->getTemplate()->set_var("sModalidades", "");
                }

                if($oVariable->isVariableTexto()){
                    $this->getTemplate()->set_var("sTipo", "Variable de Texto");
                    $iconoVariableBlock = "IconoTipoTextoBlock";
                    $this->getTemplate()->set_var("sModalidades", "");
                }

                if($oVariable->isVariableCualitativa()){
                    $this->getTemplate()->set_var("sTipo", "Variable Cualitativa");
                    $iconoVariableBlock = "IconoTipoCualitativaBlock";
                    $sModalidades = "<br><strong>Modalidades: </strong> ";
                    $aModalidades = $oVariable->getModalidades();
                    foreach($aModalidades as $oModalidad){
                        $sModalidades .= $oModalidad->getModalidad().", ";
                    }
                    $sModalidades = substr($sModalidades, 0, -2);
                    $sDescripcion .= $sModalidades;
                }

                $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "iconoVariable", $iconoVariableBlock);
                $this->getTemplate()->set_var("iconoVariable", $this->getTemplate()->pparse("iconoVariable"));
                $this->getTemplate()->delete_parsed_blocks($iconoVariableBlock);

                $this->getTemplate()->parse("VariableBlock", true);
            }
        }else{
            $this->getTemplate()->set_var("VariableBlock", "");            
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaVariablesBlock', false));
    }

    /**
     * Devuelve el html de una nueva fila en la tabla de modalidades dentro del formulario de variable cualitativa.
     */
    private function agregarModalidad()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        //genero un id para el array del input del form, es solo para el html.
        $sHtmlId = uniqid();

        $this->restartTemplate();
        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "ajaxRowModalidad", "ModalidadBlock");

        $this->getTemplate()->set_var("modalidadHtmlId", $sHtmlId);
        $this->getTemplate()->set_var("iOrden", "0");
               
        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxRowModalidad', false));
    }

    /**
     * Las acciones se dividen asi por el hecho de que puede ser de utilidad activar o desactivar la creacion o la edicion de variables
     * de manera independiente.
     *
     * Como en un futuro los objetos pueden diferenciarse cada vez mas de su clase padre se opta por mantener vistas separadas
     * para cada tipo de variable aunque sean similares.
     * 
     */
    public function formCrearVariable()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        if($this->getRequest()->has('formTexto')){
            $this->mostrarFormularioVariableTextoPopUp();
            return;
        }
        if($this->getRequest()->has('formNumerica')){
            $this->mostrarFormularioVariableNumericaPopUp();
            return;
        }
        if($this->getRequest()->has('formCualitativa')){
            $this->mostrarFormularioVariableCualitativaPopUp();
            return;
        }
    }

    public function formEditarVariable()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iVariableId = $this->getRequest()->getPost('iVariableId');
        if(empty($iVariableId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oVariable = AdminController::getInstance()->getVariableById($iVariableId);

        if($oVariable->isVariableNumerica()){
            $this->mostrarFormularioVariableNumericaPopUp($oVariable);
            return;
        }

        if($oVariable->isVariableTexto()){
            $this->mostrarFormularioVariableTextoPopUp($oVariable);
            return;
        }

        if($oVariable->isVariableCualitativa()){
            $this->mostrarFormularioVariableCualitativaPopUp($oVariable);
            return;
        }
    }

    private function mostrarFormularioVariableTextoPopUp($oVariableTexto = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "popUpContent", "FormularioVariableTextoBlock");

        //FORMULARIO CREAR
        if($oVariableTexto === null){

            $this->getTemplate()->unset_blocks("SubmitModificarVariableTextoBlock");

            $sTituloForm = "Agregar nueva variable de texto a la Unidad";

            //valores por defecto en el agregar
            $iVariableIdForm = "";
            $sNombre = "";
            $sDescripcion = "";
            
        //FORMULARIO EDITAR
        }else{
            
            $sTituloForm = "Editar variable de texto";
            
            $this->getTemplate()->unset_blocks("SubmitCrearVariableTextoBlock");
            $this->getTemplate()->set_var("iVariableIdForm", $oVariableTexto->getId());

            $sNombre = $oVariableTexto->getNombre();
            $sDescripcion = $oVariableTexto->getDescripcion();
        }

        $iUnidadId = $this->getRequest()->getPost('unidadId');
        $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadId);

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormularioVariableNumericaPopUp($oVariableNumerica = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "popUpContent", "FormularioVariableNumericaBlock");

        //FORMULARIO CREAR
        if($oVariableNumerica === null){

            $this->getTemplate()->unset_blocks("SubmitModificarVariableNumericaBlock");

            $sTituloForm = "Agregar nueva variable numérica a la Unidad";

            //valores por defecto en el agregar
            $iVariableIdForm = "";
            $sNombre = "";
            $sDescripcion = "";

        //FORMULARIO EDITAR
        }else{

            $sTituloForm = "Editar variable numérica";

            $this->getTemplate()->unset_blocks("SubmitCrearVariableNumericaBlock");
            $this->getTemplate()->set_var("iVariableIdForm", $oVariableNumerica->getId());

            $sNombre = $oVariableNumerica->getNombre();
            $sDescripcion = $oVariableNumerica->getDescripcion();
        }

        $iUnidadId = $this->getRequest()->getPost('unidadId');
        $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadId);

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormularioVariableCualitativaPopUp($oVariableCualitativa = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "popUpContent", "FormularioVariableCualitativaBlock");

        //FORMULARIO CREAR
        if($oVariableCualitativa === null){

            $this->getTemplate()->unset_blocks("SubmitModificarVariableCualitativaBlock");
            $this->getTemplate()->unset_blocks("ModalidadBlock");

            $sTituloForm = "Agregar nueva variable cualitativa a la Unidad";

            //valores por defecto en el agregar
            $iVariableIdForm = "";
            $sNombre = "";
            $sDescripcion = "";

        //FORMULARIO EDITAR
        }else{

            $sTituloForm = "Editar variable cualitativa";

            $this->getTemplate()->unset_blocks("SubmitCrearVariableCualitativaBlock");
            $this->getTemplate()->unset_blocks("NoRecordsModalidadesBlock");
            
            $this->getTemplate()->set_var("iVariableIdForm", $oVariableCualitativa->getId());

            $sNombre = $oVariableCualitativa->getNombre();
            $sDescripcion = $oVariableCualitativa->getDescripcion();

            foreach($oVariableCualitativa->getModalidades() as $oModalidad){

                $sHtmlId = uniqid();
                $this->getTemplate()->set_var("modalidadHtmlId", $sHtmlId);
                $this->getTemplate()->set_var("iModalidadId", $oModalidad->getId());
                $this->getTemplate()->set_var("iOrden", $oModalidad->getOrden());
                $this->getTemplate()->set_var("sModalidad", $oModalidad->getModalidad());

                $this->getTemplate()->parse("ModalidadBlock", true);
            }
        }

        $iUnidadId = $this->getRequest()->getPost('unidadId');
        $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadId);

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    public function guardar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearVariableTexto')){
            $this->crearVariableTexto();
            return;
        }

        if($this->getRequest()->has('modificarVariableTexto')){
            $this->modificarVariableTexto();
            return;
        }

        if($this->getRequest()->has('crearVariableNumerica')){
            $this->crearVariableNumerica();
            return;
        }

        if($this->getRequest()->has('modificarVariableNumerica')){
            $this->modificarVariableNumerica();
            return;
        }

        if($this->getRequest()->has('crearVariableCualitativa')){
            $this->crearVariableCualitativa();
            return;
        }

        if($this->getRequest()->has('modificarVariableCualitativa')){
            $this->modificarVariableCualitativa();
            return;
        } 
    }

    private function crearVariableTexto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oVariableTexto = new stdClass();
            $oVariableTexto = Factory::getVariableTextoInstance($oVariableTexto);

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
                        
            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oVariableTexto->setNombre($this->getRequest()->getPost("nombre"));
            $oVariableTexto->setDescripcion($this->getRequest()->getPost("descripcion"));
            
            AdminController::getInstance()->guardarVariable($oVariableTexto, $iUnidadId);

            $this->getJsonHelper()->setValor("agregarVariable", "1");
            $this->getJsonHelper()->setMessage("La variable de texto se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarVariableTexto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iVariableId = $this->getRequest()->getPost('variableIdForm');
            $oVariable = AdminController::getInstance()->getVariableById($iVariableId);

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
            
            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if($this->getRequest()->getPost("nombre") != $oVariable->getNombre()){
                if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                    $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oVariable->setNombre($this->getRequest()->getPost("nombre"));
            $oVariable->setDescripcion($this->getRequest()->getPost("descripcion"));

            AdminController::getInstance()->guardarVariable($oVariable);

            $this->getJsonHelper()->setMessage("La variable se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarVariable", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearVariableNumerica()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oVariableNumerica = new stdClass();
            $oVariableNumerica = Factory::getVariableNumericaInstance($oVariableNumerica);

            $oVariableNumerica->setNombre($this->getRequest()->getPost("nombre"));
            $oVariableNumerica->setDescripcion($this->getRequest()->getPost("descripcion"));

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
            
            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            AdminController::getInstance()->guardarVariable($oVariableNumerica, $iUnidadId);

            $this->getJsonHelper()->setValor("agregarVariable", "1");
            $this->getJsonHelper()->setMessage("La variable numérica se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarVariableNumerica()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iVariableId = $this->getRequest()->getPost('variableIdForm');
            $oVariable = AdminController::getInstance()->getVariableById($iVariableId);

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
            
            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if($this->getRequest()->getPost("nombre") != $oVariable->getNombre()){
                if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                    $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }

            $oVariable->setNombre($this->getRequest()->getPost("nombre"));
            $oVariable->setDescripcion($this->getRequest()->getPost("descripcion"));

            AdminController::getInstance()->guardarVariable($oVariable);

            $this->getJsonHelper()->setMessage("La variable se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarVariable", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearVariableCualitativa()
    {       
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oVariableCualitativa = new stdClass();
            $oVariableCualitativa = Factory::getVariableCualitativaInstance($oVariableCualitativa);

            $oVariableCualitativa->setNombre($this->getRequest()->getPost("nombre"));
            $oVariableCualitativa->setDescripcion($this->getRequest()->getPost("descripcion"));

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');

            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $vModalidad = $this->getRequest()->getPost("modalidad");
            if( empty($vModalidad) || !is_array($vModalidad) || count($vModalidad) < 2 ){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Deben guardarse al menos 2 modalidades");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //listado modalidades
            $aModalidades = array();
            $aModalidadesAux = array(); //lo uso para asegurarme de que no haya dos modalidades con el mismo nombre
            foreach($vModalidad as $modalidad){

                $sModalidad = trim($modalidad['modalidad']);
                if(empty($sModalidad)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("Ninguna modalidad puede quedar vacia");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                $iModalidadId = (empty($modalidad['modalidadId'])) ? null : $modalidad['modalidadId'];
                $iOrden = (empty($modalidad['orden'])) ? 0 : $modalidad['orden'];
                
            	$oModalidad = new stdClass();
            	$oModalidad->iId = $iModalidadId;
            	$oModalidad->sModalidad = $sModalidad;
                $oModalidad->iOrden = $iOrden;

                $aModalidadesAux[] = $sModalidad;
            	$aModalidades[] = Factory::getModalidadInstance($oModalidad);
            }

            //hubo al menos una repeticion en el array.
            if(count($aModalidadesAux) != count(array_unique($aModalidadesAux))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("No puede haber 2 modalidades con el mismo nombre");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }
            
            $oVariableCualitativa->setModalidades($aModalidades);                        
                                   
            AdminController::getInstance()->guardarVariable($oVariableCualitativa, $iUnidadId);

            $this->getJsonHelper()->setValor("agregarVariable", "1");
            $this->getJsonHelper()->setMessage("La variable cualitativa se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarVariableCualitativa()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iVariableId = $this->getRequest()->getPost('variableIdForm');

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
            $oVariableCualitativa = AdminController::getInstance()->getVariableById($iVariableId);

            //no se permiten 2 variables con el mismo nombre dentro de una misma unidad.
            if($this->getRequest()->getPost("nombre") != $oVariableCualitativa->getNombre()){
                if(AdminController::getInstance()->existeVariableUnidad($this->getRequest()->getPost("nombre"), $iUnidadId)){
                    $this->getJsonHelper()->setMessage("No puede haber 2 variables con el mismo nombre en la unidad.");
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }
                                  
            $oVariableCualitativa->setNombre($this->getRequest()->getPost("nombre"));
            $oVariableCualitativa->setDescripcion($this->getRequest()->getPost("descripcion"));

            $vModalidad = $this->getRequest()->getPost("modalidad");
            if( empty($vModalidad) || !is_array($vModalidad) || count($vModalidad) < 2 ){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Deben guardarse al menos 2 modalidades");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //listado modalidades
            $aModalidades = array();
            $aModalidadesAux = array(); //lo uso para asegurarme de que no haya dos modalidades con el mismo nombre
            foreach($vModalidad as $modalidad){

                $sModalidad = trim($modalidad['modalidad']);
                if(empty($sModalidad)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("Ninguna modalidad puede quedar vacia");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                $iModalidadId = (empty($modalidad['modalidadId'])) ? null : $modalidad['modalidadId'];
                $iOrden = (empty($modalidad['orden'])) ? 0 : $modalidad['orden'];

            	$oModalidad = new stdClass();
            	$oModalidad->iId = $iModalidadId;
            	$oModalidad->sModalidad = $sModalidad;
                $oModalidad->iOrden = $iOrden;

                $aModalidadesAux[] = $sModalidad;
            	$aModalidades[] = Factory::getModalidadInstance($oModalidad);                
            }
            $oVariableCualitativa->setModalidades($aModalidades);

            //hubo al menos una repeticion en el array.
            if(count($aModalidadesAux) != count(array_unique($aModalidadesAux))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("No puede haber 2 modalidades con el mismo nombre");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            AdminController::getInstance()->guardarVariable($oVariableCualitativa);

            //genero el html de la grilla de las modalidades con el id actualizado.
            $this->restartTemplate();
            $this->getTemplate()->load_file_section("gui/vistas/admin/variables.gui.html", "ajaxGrillaModalidades", "GrillaModalidadesBlock");
            $this->getTemplate()->set_var("NoRecordsModalidadesBlock", "");

            foreach($oVariableCualitativa->getModalidades() as $oModalidad){
                $sHtmlId = uniqid();
                $this->getTemplate()->set_var("modalidadHtmlId", $sHtmlId);
                $this->getTemplate()->set_var("iModalidadId", $oModalidad->getId());
                $this->getTemplate()->set_var("iOrden", $oModalidad->getOrden());
                $this->getTemplate()->set_var("sModalidad", $oModalidad->getModalidad());
                $this->getTemplate()->parse("ModalidadBlock", true);
            }
                        
            $this->getJsonHelper()->setMessage("La variable se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarVariable", "1");
            $this->getJsonHelper()->setValor("grillaModalidades", $this->getTemplate()->pparse('ajaxGrillaModalidades', false));
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function eliminarModalidad()
    {
        $iModalidadId = $this->getRequest()->getParam('iModalidadId');

        if(empty($iModalidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            AdminController::getInstance()->borrarModalidadVariable($iModalidadId);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();  
    }

    public function eliminar()
    {
        $iVariableId = $this->getRequest()->getPost('iVariableId');

        if(empty($iVariableId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{            
            $oVariable = AdminController::getInstance()->getVariableById($iVariableId);

            $aVariables[] = $oVariable;
            $result = AdminController::getInstance()->borrarVariables($aVariables);

            $this->restartTemplate();

            if($result){
                $msg = "La variable fue eliminada de la unidad";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la variable de la unidad";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la variable de la unidad";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();   
    }
}