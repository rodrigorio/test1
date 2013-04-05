<?php

/**
 * @author Matias Velilla
 *
 */
class UnidadesControllerSeguimientos extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroNombreUnidad' => 'u.nombre');

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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "pageRightInnerCont", "PageRightInnerContListadoUnidadesBlock");

        $this->getTemplate()->set_var("hrefListadoSeguimientos", $this->getUrlFromRoute("seguimientosIndexIndex", true));
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

        if($this->getRequest()->has('masUnidades')){
            $this->masUnidades();
            return;
        }
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setMenuDerecha()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Unidades de Variables");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "pageRightInnerMainCont", "ListadoUnidadesBlock");

            $iRecordsTotal = 0;            
            $aUnidades = SeguimientosController::getInstance()->obtenerUnidadesPersonalizadasUsuario($filtro = array(), $iRecordsTotal, null, null, null, null);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
            if(count($aUnidades) > 0){

                $this->getTemplate()->set_var("NoRecordsThumbsUnidadesBlock", "");
                
            	foreach ($aUnidades as $oUnidad){
                    $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                    $this->getTemplate()->set_var("sNombreVariable", $oUnidad->getNombre());
                    $this->getTemplate()->set_var("sDescripcionVariable", $oUnidad->getDescripcion(true));

                    //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                    list($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosUnidad($oUnidad->getId());
                    $this->getTemplate()->set_var("iCantidadVariables", $iCantidadVariablesAsociadas);
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);

                    if($iCantidadSeguimientosAsociados > 0){
                        $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                        $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                    }else{
                        $this->getTemplate()->set_var("LinkSeguimientos", "");
                    }
                    
                    $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("seguimientosVariablesIndex", true)."?id=".$oUnidad->getId());

                    $this->getTemplate()->parse("UnidadBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("UnidadBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            throw $e;
        }
    }

    private function masUnidades()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "ajaxThumbnailsUnidadesBlock", "ThumbsUnidadesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
       
        $iRecordsTotal = 0;
        //en este listado no hay paginacion.
        $aUnidades = SeguimientosController::getInstance()->obtenerUnidadesPersonalizadasUsuario($filtroSql, $iRecordsTotal, null, null, null, null);
        
        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
        
        if(count($aUnidades) > 0){

            $this->getTemplate()->set_var("NoRecordsThumbsUnidadesBlock", "");

            foreach($aUnidades as $oUnidad){
                $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                $this->getTemplate()->set_var("sNombreVariable", $oUnidad->getNombre());
                $this->getTemplate()->set_var("sDescripcionVariable", $oUnidad->getDescripcion(true));

                //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad                
                list($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosUnidad($oUnidad->getId());
                $this->getTemplate()->set_var("iCantidadVariables", $iCantidadVariablesAsociadas);

                if($iCantidadSeguimientosAsociados > 0){
                    $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                }else{
                    $this->getTemplate()->set_var("LinkSeguimientos", "");
                }

                $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("seguimientosVariablesIndex", true)."?id=".$oUnidad->getId());

                $this->getTemplate()->parse("UnidadBlock", true);
            }
        }else{
            $this->getTemplate()->set_var("UnidadBlock", "");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxThumbnailsUnidadesBlock', false));
    }

    public function formCrearUnidad()
    {
        $this->mostrarFormularioUnidadPopUp();
    }

    public function formEditarUnidad()
    {
        $this->mostrarFormularioUnidadPopUp();
    }

    /**
     * Se dividi en formCrearUnidad y formModificarUnidad para poder desactivar/activar las funciones
     * de manera independiente desde el administrador
     */
    private function mostrarFormularioUnidadPopUp()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "popUpContent", "FormularioUnidadBlock");

        //AGREGAR UNIDAD
        if($this->getRequest()->getActionName() == "formCrearUnidad"){

            $this->getTemplate()->unset_blocks("SubmitModificarUnidadBlock");

            $sTituloForm = "Agregar una nueva Unidad";

            //valores por defecto en el agregar
            $oPublicacion = null;
            $sNombre = "";
            $sDescripcion = "";

        //MODIFICAR UNIDAD
        }else{
            $iUnidadIdForm = $this->getRequest()->getParam('unidadId');
            if(empty($iUnidadIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar Unidad";
            $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadIdForm);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oUnidad->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta unidad", 401);
            }

            $this->getTemplate()->unset_blocks("SubmitCrearUnidadBlock");
            $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadIdForm);

            $sNombre = $oUnidad->getNombre();
            $sDescripcion = $oUnidad->getDescripcion();
        }

        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getResponse()->setBody($this->getTemplate()->pparse('frame', false)));
    }

    public function guardarUnidad()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearUnidad')){
            $this->crearUnidad();
            return;
        }

        if($this->getRequest()->has('modificarUnidad')){
            $this->modificarUnidad();
            return;
        }        
    }

    private function crearUnidad()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oUnidad = new stdClass();

            $oUnidad->sNombre = $this->getRequest()->getPost("nombre");
            $oUnidad->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oUnidad->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            $oUnidad = Factory::getUnidadInstance($oUnidad);
            $oUnidad->isAsociacionAutomatica(false);
            $oUnidad->isPreCargada(false);

            SeguimientosController::getInstance()->guardarUnidad($oUnidad);

            $this->getJsonHelper()->setValor("agregarUnidad", "1");
            $this->getJsonHelper()->setMessage("La unidad se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarUnidad()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iUnidadId = $this->getRequest()->getPost('unidadIdForm');
            $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oUnidad->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta unidad", 401);
            }

            $oUnidad->setNombre($this->getRequest()->getPost("nombre"));
            $oUnidad->setDescripcion($this->getRequest()->getPost("descripcion"));
                        
            SeguimientosController::getInstance()->guardarUnidad($oUnidad);

            $this->getJsonHelper()->setMessage("La unidad se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarUnidad", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}