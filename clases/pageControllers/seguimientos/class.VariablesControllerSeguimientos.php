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

            $iRecordsTotal = 0;
            $filtro = array('v.unidad_id' => $iUnidadId);
            //no utilizo getVariablesByUnidadId porque necesito el filtro de los orderBy del listado.
            $aVariables = SeguimientosController::getInstance()->getVariables($filtro, $iRecordsTotal, null, null, null, null);
            
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
            }else{
                $this->getTemplate()->set_var("sNoRecords", "No hay variables cargadas en la unidad");
                $this->getTemplate()->set_var("VariableBlock", "");
            }
             
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    public function formCrearVariable(){}

    public function formEditarVariable(){}
}