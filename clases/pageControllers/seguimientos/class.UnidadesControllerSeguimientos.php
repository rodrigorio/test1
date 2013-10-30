<?php

/**
 * @author Matias Velilla
 *
 */
class UnidadesControllerSeguimientos extends PageControllerAbstract
{
    const TIPO_EDICION_REGULAR = "regular";
    const TIPO_EDICION_ESPORADICA = "esporadica";
    
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

        return $this;
    }

    private function setJsUnidades()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "jsContent", "JsContent");
        return $this;
    }

    private function setJsAsociarUnidadSeguimiento()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "jsContent", "JsContentAsociarUnidades");
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
        
        if($this->getRequest()->has('verSeguimientos')){
            $this->verSeguimientos();
            return;
        }
    }

    /**
     * Seguimientos personalizados asociados a la unidad, se usa en la vista de administrar unidades seg personalizados
     */
    private function verSeguimientos()
    {
        $iUnidadId = $this->getRequest()->getParam('iUnidadId');
        if(empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "ajaxSeguimientosAsociadosBlock", "VerSeguimientosAsociadosBlock");

        $iRecordsTotal = 0;
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $filtroSql["u.id"] = $perfil->getUsuario()->getId();
        $filtroSql["su.unidades_id"] = $iUnidadId;
        $aSeguimientos = SeguimientosController::getInstance()->buscarSeguimientos($filtroSql, $iRecordsTotal, null, null, null, null);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
        
        foreach ($aSeguimientos as $oSeguimiento){
            $this->getTemplate()->set_var("sSeguimientoPersona", $oSeguimiento->getDiscapacitado()->getNombreCompleto());
            $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oSeguimiento->getDiscapacitado()->getNumeroDocumento());
            $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

            $sEstadoSeguimiento = $oSeguimiento->getEstado();
            if($sEstadoSeguimiento == "activo"){
                $this->getTemplate()->set_var("sEstadoClass", "");
            }else{
                $this->getTemplate()->set_var("sEstadoClass", "disabled");
            }

            $srcAvatarPersona = $this->getUploadHelper()->getDirectorioUploadFotos().$oSeguimiento->getDiscapacitado()->getNombreAvatar();
            $this->getTemplate()->set_var("scrAvatarPersona", $srcAvatarPersona);

            $this->getTemplate()->parse("SeguimientoBlock", true);
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('ajaxSeguimientosAsociadosBlock', false));
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setMenuDerecha()
                 ->setJsUnidades()
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
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);

                    if($iCantidadSeguimientosAsociados > 0){
                        $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                        $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                    }else{
                        $this->getTemplate()->set_var("LinkSeguimientos", "");
                    }
                    
                    $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("seguimientosVariablesIndex", true)."?id=".$oUnidad->getId());

                    $this->getTemplate()->parse("UnidadBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                    $this->getTemplate()->delete_parsed_blocks("NoLinkSeguimientos");
                    $this->getTemplate()->delete_parsed_blocks("LinkSeguimientos");
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

                if($iCantidadSeguimientosAsociados > 0){
                    $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                }else{
                    $this->getTemplate()->set_var("LinkSeguimientos", "");
                }

                $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("seguimientosVariablesIndex", true)."?id=".$oUnidad->getId());

                $this->getTemplate()->parse("UnidadBlock", true);
                $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                $this->getTemplate()->delete_parsed_blocks("NoLinkSeguimientos");
                $this->getTemplate()->delete_parsed_blocks("LinkSeguimientos");
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

            $this->getTemplate()->set_var("eTipoEdicionEsporadica", self::TIPO_EDICION_ESPORADICA);
            $this->getTemplate()->set_var("eTipoEdicionRegular", self::TIPO_EDICION_REGULAR);
                      
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

            $this->getTemplate()->unset_blocks("SetTipoEdicionBlock");
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
            $oUnidad->eTipoEdicion = $this->getRequest()->getPost("tipoEdicion");
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

    public function eliminar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        //devuelvo el dialog para confirmar el borrado de la unidad
        if($this->getRequest()->has('mostrarDialogConfirmar')){
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaInfoBlock");
            $this->getTemplate()->set_var("sTituloMsgFicha", "Unidad de Variables");
            $this->getTemplate()->set_var("sMsgFicha", "Cuidado, se eliminaran de forma permanente todas las variables y la información que haya sido guardada de los seguimientos a los que la unidad esta asociada.
                                                        Solo se mantendrá una copia del historial para aquellos valores asignados en seguimientos hace mas de ".$cantDiasExpiracion." días.
                                                       <br>Una vez eliminada la Unidad la información guardada en las variables no podrá volver a recuperarse.");

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));
            return;
        }

        //elimino la unidad seleccionada
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $iUnidadId = $this->getRequest()->getPost('iUnidadId');
            $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oUnidad->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar esta unidad", 401);
            }

            $oUnidad->setFechaBorradoLogicoHoy();
            $result = SeguimientosController::getInstance()->borrarUnidad($oUnidad);

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

    /**
     * Esta vista lista las unidades asociadas a un seguimiento y permite administrarlas mediante drag and drop.
     * La idea es que en la columna izquierda esten las unidades que actualmente no se asociaron,
     * en la columna derecha las que actualmente se asociaron al seguimiento.
     *
     * En la lista de unidades aparecen tanto las esporadicas como regulares.
     *
     * Si el seguimiento es SCC solo se muestran las precargadas desde el administrador.
     *
     * No se muestran las unidades de asociacion automatica
     *
     */
    public function listarUnidadesPorSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $aCurrentOptions[] = "currentOptionAsociarUnidadesSeguimiento";

            $this->setFrameTemplate()
                 ->setJsAsociarUnidadSeguimiento()
                 ->setHeadTag();

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            //para que pueda ser reutilizado en otras vistas
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $this->getTemplate()->set_var("tituloSeccion", "Asociar unidades a Seguimiento");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "pageRightInnerMainCont", "AsociarUnidadesBlock");

            //Obtengo la lista de unidades segun tipo de seguimiento que todavia no esten asociadas al seguimiento.
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $aUnidadesDisponibles = SeguimientosController::getInstance()->getUnidadesDisponiblesBySeguimientoPersonalizado($oSeguimiento);
            }
            if($oSeguimiento->isSeguimientoSCC()){
                $aUnidadesDisponibles = SeguimientosController::getInstance()->getUnidadesDisponiblesBySeguimientoSCC($oSeguimiento);
            }

            if(count($aUnidadesDisponibles) > 0){

                $this->getTemplate()->set_var("NoRecordsSinAsociarBlock", "");
                $htmlUnidades = "";

                foreach($aUnidadesDisponibles as $oUnidad){

                    $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());                    
                    $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());

                    //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                    $sDescripcionUnidad = $oUnidad->getDescripcion();
                    if(strlen($sDescripcionUnidad) > 150){
                        $sDescripcionUnidad = Utils::tokenTruncate($sDescripcionUnidad, 150);
                        $sDescripcionUnidad = nl2br($sDescripcionUnidad);
                    }
                    $this->getTemplate()->set_var("sDescripcionUnidad", $sDescripcionUnidad);

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "unidad", "UnidadListadoAsociarBlock");
                    $htmlUnidades .= $this->getTemplate()->pparse("unidad", false);
                    $this->getTemplate()->delete_parsed_blocks("UnidadListadoAsociarBlock");                    
                }

                $this->getTemplate()->set_var("UnidadesSinAsociar", $htmlUnidades);
            }else{
                $this->getTemplate()->set_var("UnidadesSinAsociar", "");
            }

            //Obtengo la lista de unidades asociadas al seguimiento actualmente,
            //es el mismo conjunto que se levanta cuando se crea una entrada.
            $aUnidadesAsociadas = SeguimientosController::getInstance()->getUnidadesBySeguimientoId($oSeguimiento->getId(), false);
            if(count($aUnidadesAsociadas) > 0){

                $this->getTemplate()->set_var("NoRecordsAsociadasBlock", "");
                $htmlUnidades = "";

                foreach($aUnidadesAsociadas as $oUnidad){

                    $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                    $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());

                    //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                    $sDescripcionUnidad = $oUnidad->getDescripcion();
                    if(strlen($sDescripcionUnidad) > 150){
                        $sDescripcionUnidad = Utils::tokenTruncate($sDescripcionUnidad, 150);
                        $sDescripcionUnidad = nl2br($sDescripcionUnidad);
                    }
                    $this->getTemplate()->set_var("sDescripcionUnidad", $sDescripcionUnidad);

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "unidad", "UnidadListadoAsociarBlock");
                    $htmlUnidades .= $this->getTemplate()->pparse("unidad", false);
                    $this->getTemplate()->delete_parsed_blocks("UnidadListadoAsociarBlock");
                }

                $this->getTemplate()->set_var("UnidadesAsociadas", $htmlUnidades);
            }else{
                $this->getTemplate()->set_var("UnidadesAsociadas", "");
            }
            
                                  
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error");
        }
    }
}