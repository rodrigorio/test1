<?php

/**
 * @author Matias Velilla
 *
 */
class VariablesControllerSeguimientos extends PageControllerAbstract
{
    private $orderByConfig = array('nombre' => array('variableTemplate' => 'orderByNombre',
                                                     'orderBy' => 'v.nombre',
                                                     'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipo',
                                                   'orderBy' => 'v.tipo',
                                                   'order' => 'desc'));

    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-01.gui.html", "frame");
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "pageRightInnerCont", "PageRightInnerContListadoVariablesBlock");

        $this->getTemplate()->set_var("hrefListadoSeguimientos", $this->getUrlFromRoute("seguimientosIndexIndex", true));
        $this->getTemplate()->set_var("hrefListadoUnidades", $this->getUrlFromRoute("seguimientosUnidadesIndex", true));
        
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
    }

    public function listar()
    {
        try{
            //primero me fijo que este el id de unidad
            $iUnidadId = $this->getRequest()->getParam('id');
            if(empty($iUnidadId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //despues me fijo que el id sea de una unidad perteneciente al integrante logueado
            if(!SeguimientosController::getInstance()->isUnidadUsuario($iUnidadId)){
                throw new Exception("No tiene permiso para editar la unidad", 401);
            }

            $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);
            
            $this->setFrameTemplate()
                 ->setMenuDerecha()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Variables");
            $this->getTemplate()->set_var("subtituloSeccion", "Unidad: <span class='fost_it'>".$oUnidad->getNombre()."</span>");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "pageRightInnerMainCont", "ListadoVariablesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);
            
            $iRecordsTotal = 0;
            $filtro = array('v.unidad_id' => $iUnidadId);
            //no utilizo getVariablesByUnidadId porque necesito el filtro de los orderBy del listado.
            $aVariables = SeguimientosController::getInstance()->getVariables($filtro, $iRecordsTotal, null, null, null, null);

            $this->getTemplate()->set_var("iUnidadId", $iUnidadId);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
            if(count($aVariables) > 0){

                $this->getTemplate()->set_var("NoRecordsVariablesBlock", "");

            	foreach ($aVariables as $oVariable){

                    $this->getTemplate()->set_var("iVariableId", $oVariable->getId());
                    $this->getTemplate()->set_var("sNombre", $oVariable->getNombre());
                    $this->getTemplate()->set_var("sTipoEnum", get_class($oVariable));
                    $this->getTemplate()->set_var("dFechaHora", $oVariable->getFecha(true));
                    $this->getTemplate()->set_var("sDescripcion", $oVariable->getDescripcion(true));

                    if($oVariable->isVariableNumerica()){
                        $this->getTemplate()->set_var("sTipo", "Variable Numérica");
                        $iconoVariableBlock = "IconoTipoNumericaBlock";
                    }

                    if($oVariable->isVariableTexto()){
                        $this->getTemplate()->set_var("sTipo", "Variable de Texto");
                        $iconoVariableBlock = "IconoTipoTextoBlock";
                    }

                    if($oVariable->isVariableCualitativa()){
                        $this->getTemplate()->set_var("sTipo", "Variable Cualitativa");
                        $iconoVariableBlock = "IconoTipoCualitativaBlock";
                        $sModalidades = "<strong>Modalidades: </strong> ";
                        $aModalidades = $oVariable->getModalidades();
                        foreach($aModalidades as $oModalidad){
                            $sModalidades .= $oModalidad->getModalidad()." ";
                        }
                        $this->getTemplate()->set_var("sModalidades", $sModalidades);
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "iconoVariable", $iconoVariableBlock);
                    $this->getTemplate()->set_var("iconoVariable", $this->getTemplate()->pparse("iconoVariable"));
                    $this->getTemplate()->delete_parsed_blocks($iconoVariableBlock);
                    
                    $this->getTemplate()->parse("VariableBlock", true);
                }

                $params[] = "masVariables=1";
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/variables-procesar", "listadoVariablesResult", $params);

            }else{
                $this->getTemplate()->set_var("sNoRecords", "No hay variables cargadas en la unidad");
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
        $iUnidadId = $this->getRequest()->getPost('unidadId');
        if(empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        //despues me fijo que el id sea de una unidad perteneciente al integrante logueado
        if(!SeguimientosController::getInstance()->isUnidadUsuario($iUnidadId)){
            throw new Exception("No tiene permiso para editar la unidad", 401);
        }

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "ajaxGrillaVariablesBlock", "GrillaVariablesBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

        $filtro = array('v.unidad_id' => $iUnidadId);
        //no utilizo getVariablesByUnidadId porque necesito el filtro de los orderBy del listado.
        $aVariables = SeguimientosController::getInstance()->getVariables($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
        if(count($aVariables) > 0){

            $this->getTemplate()->set_var("NoRecordsVariablesBlock", "");

            foreach ($aVariables as $oVariable){

                $this->getTemplate()->set_var("iVariableId", $oVariable->getId());
                $this->getTemplate()->set_var("sNombre", $oVariable->getNombre());
                $this->getTemplate()->set_var("sTipoEnum", get_class($oVariable));
                $this->getTemplate()->set_var("dFechaHora", $oVariable->getFecha(true));
                $this->getTemplate()->set_var("sDescripcion", $oVariable->getDescripcion(true));

                if($oVariable->isVariableNumerica()){
                    $this->getTemplate()->set_var("sTipo", "Variable Numérica");
                    $iconoVariableBlock = "IconoTipoNumericaBlock";
                }

                if($oVariable->isVariableTexto()){
                    $this->getTemplate()->set_var("sTipo", "Variable de Texto");
                    $iconoVariableBlock = "IconoTipoTextoBlock";
                }

                if($oVariable->isVariableCualitativa()){
                    $this->getTemplate()->set_var("sTipo", "Variable Cualitativa");
                    $iconoVariableBlock = "IconoTipoCualitativaBlock";
                    $sModalidades = "<strong>Modalidades: </strong> ";
                    $aModalidades = $oVariable->getModalidades();
                    foreach($aModalidades as $oModalidad){
                        $sModalidades .= $oModalidad->getModalidad()." ";
                    }
                    $this->getTemplate()->set_var("sModalidades", $sModalidades);
                }

                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "iconoVariable", $iconoVariableBlock);
                $this->getTemplate()->set_var("iconoVariable", $this->getTemplate()->pparse("iconoVariable"));
                $this->getTemplate()->delete_parsed_blocks($iconoVariableBlock);

                $this->getTemplate()->parse("VariableBlock", true);
            }

            $paramsPaginador[] = "masVariables=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/variables-procesar", "listadoVariablesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("VariableBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron resultados");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaVariablesBlock', false));
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

        //me fijo que la variable pertenezca a una unidad creada por el usuario.
        if(!SeguimientosController::getInstance()->isVariableUsuario($iVariableId)){
            throw new Exception("No tiene permiso para editar la variable", 401);
        }

        $oVariable = SeguimientosController::getInstance()->getVariableById($iVariableId);

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
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "popUpContent", "FormularioVariableTextoBlock");

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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormularioVariableNumericaPopUp($oVariableNumerica = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/variables.gui.html", "popUpContent", "FormularioVariableNumericaBlock");

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

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormularioVariableCualitativaPopUp($oVariableCualitativa = null)
    {

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

        if($this->getRequest()->has('crearVariableNumerica')){
            $this->crearVariableNumerica();
            return;
        }

        if($this->getRequest()->has('crearVariableCualitativa')){
            $this->crearVariableCualitativa();
            return;
        }

        if($this->getRequest()->has('crearVariableCualitativa')){
            $this->crearVariableCualitativa();
            return;
        } 
    }

    private function crearVariableTexto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oVariableTexto = new stdClass();

            $oVariableTexto = Factory::getVariableTextoInstance($oUnidad);

            $oVariableTexto->setNombre($this->getRequest()->getPost("nombre"));
            $oVariableTexto->setDescripcion($this->getRequest()->getPost("descripcion"));
            
            SeguimientosController::getInstance()->guardarVariable($oVariableTexto);

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
            $oVariable = SeguimientosController::getInstance()->getVariableById($iVariableId);

            if(!SeguimientosController::getInstance()->isVariableUsuario($iVariableId)){
                throw new Exception("No tiene permiso para editar la variable", 401);
            }

            $oVariable->setNombre($this->getRequest()->getPost("nombre"));
            $oVariable->setDescripcion($this->getRequest()->getPost("descripcion"));

            SeguimientosController::getInstance()->guardarVariable($oVariable);

            $this->getJsonHelper()->setMessage("La variable se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarVariable", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}