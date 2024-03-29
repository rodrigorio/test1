<?php

/**
 * @author Matias Velilla
 *
 */
class UnidadesControllerAdmin extends PageControllerAbstract
{
    const TIPO_EDICION_REGULAR = "regular";
    const TIPO_EDICION_ESPORADICA = "esporadica";    

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
        
        $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "jsContent", "JsContent");
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
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionSeguimientoSCC");
            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "mainContent", "ListadoUnidadesBlock");
           
            $iRecordsTotal = 0;            
            $aUnidades = AdminController::getInstance()->obtenerUnidadesPrecargadasSeguimientosSCC($filtro = array(), $iRecordsTotal, null, null, null, null);
            
            if(count($aUnidades) > 0){

                $this->getTemplate()->set_var("NoRecordsThumbsUnidadesBlock", "");
                
            	foreach ($aUnidades as $oUnidad){
                    $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                    $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());

                    //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                    $sDescripcionUnidad = $oUnidad->getDescripcion();
                    if(strlen($sDescripcionUnidad) > 150){
                        $sDescripcionUnidad = Utils::tokenTruncate($sDescripcionUnidad, 150);
                        $sDescripcionUnidad = nl2br($sDescripcionUnidad);
                    }else{
                        $this->getTemplate()->set_var("LinkVerMasBlock", "");
                    }
                    $this->getTemplate()->set_var("sDescripcionUnidad", $sDescripcionUnidad);
                    
                    if($oUnidad->isTipoEdicionRegular()){
                        $this->getTemplate()->set_var("sTipoEdicion", "Regular");
                    }
                    if($oUnidad->isTipoEdicionEsporadica()){                        
                        $this->getTemplate()->set_var("sTipoEdicion", "Esporádica");
                    }

                    //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                    list($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosUnidad($oUnidad->getId());
                    $this->getTemplate()->set_var("iCantidadVariables", $iCantidadVariablesAsociadas);
                                        
                    $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("adminVariablesIndex", true)."?id=".$oUnidad->getId());

                    $this->getTemplate()->parse("UnidadBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "ajaxThumbnailsUnidadesBlock", "ThumbsUnidadesBlock");
       
        $iRecordsTotal = 0;
        $aUnidades = AdminController::getInstance()->obtenerUnidadesPrecargadasSeguimientosSCC($filtro = array(), $iRecordsTotal, null, null, null, null);
                
        if(count($aUnidades) > 0){

            $this->getTemplate()->set_var("NoRecordsThumbsUnidadesBlock", "");

            foreach($aUnidades as $oUnidad){
                $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
                
                //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                $sDescripcionUnidad = $oUnidad->getDescripcion();
                if(strlen($sDescripcionUnidad) > 150){
                    $sDescripcionUnidad = Utils::tokenTruncate($sDescripcionUnidad, 150);
                    $sDescripcionUnidad = nl2br($sDescripcionUnidad);
                }else{
                    $this->getTemplate()->set_var("LinkVerMasBlock", "");
                }
                $this->getTemplate()->set_var("sDescripcionUnidad", $sDescripcionUnidad);

                if($oUnidad->isTipoEdicionRegular()){
                    $this->getTemplate()->set_var("sTipoEdicion", "Regular");
                }
                if($oUnidad->isTipoEdicionEsporadica()){
                    $this->getTemplate()->set_var("sTipoEdicion", "Esporádica");
                }

                //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                list($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosUnidad($oUnidad->getId());
                $this->getTemplate()->set_var("iCantidadVariables", $iCantidadVariablesAsociadas);

                $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("adminVariablesIndex", true)."?id=".$oUnidad->getId());

                $this->getTemplate()->parse("UnidadBlock", true);
                $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
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

    private function mostrarFormularioUnidadPopUp()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "popUpContent", "FormularioUnidadBlock");

        //AGREGAR UNIDAD
        if($this->getRequest()->getActionName() == "formCrearUnidad"){

            $this->getTemplate()->unset_blocks("SubmitModificarUnidadBlock");

            $sTituloForm = "Agregar una nueva Unidad";

            $this->getTemplate()->set_var("eTipoEdicionEsporadica", self::TIPO_EDICION_ESPORADICA);
            $this->getTemplate()->set_var("eTipoEdicionRegular", self::TIPO_EDICION_REGULAR);
                      
            //valores por defecto en el agregar
            $oUnidad = null;
            $sNombre = "";
            $sDescripcion = "";

        //MODIFICAR UNIDAD
        }else{
            $iUnidadIdForm = $this->getRequest()->getParam('unidadId');
            if(empty($iUnidadIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar Unidad";
            $oUnidad = AdminController::getInstance()->getUnidadById($iUnidadIdForm);

            $this->getTemplate()->unset_blocks("SetTipoEdicionBlock");
            $this->getTemplate()->unset_blocks("SubmitCrearUnidadBlock");
            $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadIdForm);

            $sNombre = $oUnidad->getNombre();
            $sDescripcion = $oUnidad->getDescripcion();
        }


        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
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
            $oUnidad->eTipoEdicion = $this->getRequest()->getPost("tipoEdicion");           

            $oUnidad = Factory::getUnidadInstance($oUnidad);
            $oUnidad->isAsociacionAutomatica(false);
            $oUnidad->isPreCargada(true);

            AdminController::getInstance()->guardarUnidad($oUnidad);

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
            $oUnidad = AdminController::getInstance()->getUnidadById($iUnidadId);

            $oUnidad->setNombre($this->getRequest()->getPost("nombre"));
            $oUnidad->setDescripcion($this->getRequest()->getPost("descripcion"));
                        
            AdminController::getInstance()->guardarUnidad($oUnidad);

            $this->getJsonHelper()->setMessage("La unidad se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarUnidad", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function eliminar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        //devuelvo el dialog para confirmar el borrado de la unidad
        if($this->getRequest()->has('mostrarDialogConfirmar')){
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgInfoBlockI32");
            $this->getTemplate()->set_var("sMensaje", "Cuidado, se eliminaran de forma permanente todas las variables y la información que haya sido guardada de los seguimientos a los que la unidad esta asociada.
                                                        Solo se mantendrá una copia del historial para aquellos valores asignados en seguimientos hace mas de ".$cantDiasExpiracion." días.
                                                       <br>Una vez eliminada la Unidad la información guardada en las variables no podrá volver a recuperarse.");

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));
            return;
        }

        //elimino la unidad seleccionada
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $iUnidadId = $this->getRequest()->getPost('iUnidadId');
            $oUnidad = AdminController::getInstance()->getUnidadById($iUnidadId);

            $oUnidad->setFechaBorradoLogicoHoy();
            $result = AdminController::getInstance()->borrarUnidad($oUnidad);

            if($result){
                $msg = "La Unidad y las variables asociadas fueron eliminadas del sistema.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha podido eliminar la Unidad del sistema.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la Unidad del sistema.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}