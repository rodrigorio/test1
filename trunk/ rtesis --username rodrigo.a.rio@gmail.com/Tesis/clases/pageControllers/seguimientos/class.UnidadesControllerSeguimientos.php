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

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oUnidad->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver esta unidad", 401);
        }
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "ajaxSeguimientosAsociadosBlock", "VerSeguimientosAsociadosBlock");

        $iRecordsTotal = 0;
        $filtroSql["u.id"] = $iUsuarioId;
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

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para administrar unidades en este seguimiento", 401);
        }
        
        try{            
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
            //es el mismo conjunto que se levanta cuando se crea una entrada pero sin las unidades de asociacion automatica.
            $aUnidadesAsociadas = SeguimientosController::getInstance()->getUnidadesBySeguimientoId($oSeguimiento->getId(), false, null, false);
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

    public function unidadesPorSeguimientoProcesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('ampliarUnidad')){
            $this->ampliarUnidad();
            return;
        }
        
        if($this->getRequest()->has('dialogConfirmar')){
            $this->dialogConfirmar();
            return;
        }

        if($this->getRequest()->has('moverUnidad')){
            if($this->getRequest()->getParam('moverUnidad') == "asociarUnidadSeguimiento"){
                $this->asociarUnidadSeguimiento();
            }
            if($this->getRequest()->getParam('moverUnidad') == "desasociarUnidadSeguimiento"){
                $this->desasociarUnidadSeguimiento();
            }
            return;
        }
    }

    private function dialogConfirmar()
    {
        //la fecha de creacion esta fuera del periodo de edicion de seguimientos?
        $iCantDias = SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento();
        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaHintBlock");
        $this->getTemplate()->set_var("sTituloMsgFicha", "Desasociar Unidad");
        $this->getTemplate()->set_var("sMsgFicha", "Se eliminará la asociación entre la unidad y el seguimiento.<br>
                                                    Esta acción provocará que el contenido guardado en las entradas de los últimos <strong>".$iCantDias."</strong> días en las variables de la unidad se elimine de manera permanente.<br>
                                                    Desea continuar?");
        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));       
    }

    private function ampliarUnidad()
    {
        $iUnidadId = $this->getRequest()->getParam('iUnidadId');

        if(empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oUnidad->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver esta unidad", 401);
        }

        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "popUpContent", "AmpliarUnidadBlock");

            //mostrar descripcion unidad y lista de variables.
            $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
            $this->getTemplate()->set_var("sDescripcionUnidad", $oUnidad->getDescripcion(true));

            $aVariables = SeguimientosController::getInstance()->getVariablesByUnidadId($iUnidadId, false);            
            $this->getTemplate()->set_var("iRecordsTotal", count($aVariables));
            if(count($aVariables) > 0){

                $this->getTemplate()->set_var("iUnidadId", $iUnidadId);
                $this->getTemplate()->set_var("NoRecordsVariablesBlock", "");

            	foreach ($aVariables as $oVariable){

                    $this->getTemplate()->set_var("iVariableId", $oVariable->getId());
                    $this->getTemplate()->set_var("sNombre", $oVariable->getNombre());
                    $this->getTemplate()->set_var("dFechaHora", $oVariable->getFecha(true));
                    $this->getTemplate()->set_var("sDescripcionVariable", $oVariable->getDescripcion(true));

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
                        $sModalidades = "<strong>Modalidades: </strong> ";
                        $aModalidades = $oVariable->getModalidades();
                        foreach($aModalidades as $oModalidad){
                            $sModalidades .= $oModalidad->getModalidad().", ";
                        }
                        $sModalidades = substr($sModalidades, 0, -2);
                        $this->getTemplate()->set_var("sModalidades", $sModalidades);
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/unidades.gui.html", "iconoVariable", $iconoVariableBlock);
                    $this->getTemplate()->set_var("iconoVariable", $this->getTemplate()->pparse("iconoVariable"));
                    $this->getTemplate()->delete_parsed_blocks($iconoVariableBlock);

                    $this->getTemplate()->parse("VariableBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("sNoRecords", "No hay variables cargadas en la unidad");
                $this->getTemplate()->set_var("VariableBlock", "");
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error al procesar lo solicitado");
        }
    }

    private function asociarUnidadSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        $iUnidadId = $this->getRequest()->getParam('iUnidadId');
        
        if(empty($iSeguimientoId) || empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            SeguimientosController::getInstance()->asociarUnidadSeguimiento($iSeguimientoId, $iUnidadId);

            $this->getJsonHelper()->setSuccess(true)
                                  ->sendJsonAjaxResponse();
            return;

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }            
    }
    
    private function desasociarUnidadSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        $iUnidadId = $this->getRequest()->getParam('iUnidadId');
        
        if(empty($iSeguimientoId) || empty($iUnidadId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            SeguimientosController::getInstance()->desasociarUnidadSeguimiento($iSeguimientoId, $iUnidadId);
            $this->getJsonHelper()->setSuccess(true)
                                  ->sendJsonAjaxResponse();
            return;
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }   
    }

    public function ampliarEsporadica()
    {
        $iUnidadId = $this->getRequest()->getParam('iUnidadEsporadicaId');
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        if(empty($iUnidadId) || empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        if(!SeguimientosController::getInstance()->isUnidadUsuario($iUnidadId)){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }

        try{
            $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

            //ultima entrada en la que se asocio la unidad
            $oEntrada = $oUnidad->getUltimaEntrada($iSeguimientoId);

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "popUpContent", "AmpliarEntradaEsporadicaBlock");

            $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadId);
            $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);
            $this->getTemplate()->set_var("subtituloSeccion", "Unidad: <span class='fost_it'>".$oUnidad->getNombre()."</span>");
            $this->getTemplate()->set_var("sUnidadDescripcion", $oUnidad->getDescripcion(true));

            //si $oEntrada == null, muestro el popup con el form pero con el mensaje de que no existen entradas. Sino muestro la info de la unidad
            if($oEntrada === null){
                $this->getTemplate()->set_var("EntradaEsporadicaBlock", "");
                $this->getTemplate()->set_var("VerEntradasButtonBlock", "");

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTopEntrada", "MsgFichaHintBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Unidad sin entradas.");
                $this->getTemplate()->set_var("sMsgFicha", "Aún no se ha guardado información en esta unidad en ninguna fecha. Seleccione una fecha desde el calendario marcada como disponible.");
            }else{
                $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));
                $sUltimaEntrada = str_replace("-", "/", $oEntrada->getFecha());
                $this->getTemplate()->set_var("sUltimaEntrada", $sUltimaEntrada);
                $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());
                $this->getTemplate()->set_var("hrefVerEntradasUnidadEsporadica", $this->getUrlFromRoute("seguimientosEntradasEntradasUnidadEsporadica", true)."?unidad=".$oUnidad->getId());

                //Esto se hace asi porque los valores de las variables se obtienen desde la llamada de la entrada
                $aUnidades = $oEntrada->getUnidades();
                $oUnidad = $aUnidades[0];
                $aVariables = $oUnidad->getVariables();
                if(count($aVariables) == 0){
                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "MsgTopEntradaBlock", "MsgFichaInfoBlock");
                    $this->getTemplate()->set_var("sTituloMsgFicha", "Variables Unidad");
                    $this->getTemplate()->set_var("sMsgFicha", "La unidad se encuentra sin variables, no hay datos para ampliar.");
                    $this->getTemplate()->set_var("EntradaEsporadicaBlock", "");
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
                    return;
                }else{
                    $this->getTemplate()->set_var("MsgTopEntradaBlock", "");
                }
                
                foreach($aVariables as $oVariable){

                    $this->getTemplate()->set_var("sVariableDescription", $oVariable->getDescripcion());
                    $this->getTemplate()->set_var("sVariableNombre", $oVariable->getNombre());

                    if($oVariable->isVariableNumerica()){
                        $variable = "VariableNumerica";
                        $valor = $oVariable->getValor();
                        if(null === $valor){ $valor = " - "; }
                        $this->getTemplate()->set_var("sVariableValorNumerico", $valor);
                    }

                    if($oVariable->isVariableTexto()){
                        $variable = "VariableTexto";
                        $valor = $oVariable->getValor(true);
                        if(null === $valor){ $valor = " - "; }
                        $this->getTemplate()->set_var("sVariableValorTexto", $valor);
                    }

                    if($oVariable->isVariableCualitativa()){
                        $variable = "VariableCualitativa";
                        //valor en cualitativa es un objeto Modalidad
                        $valor = $oVariable->getValorStr();
                        if(null === $valor){ $valor = " - "; }
                        $this->getTemplate()->set_var("sVariableModalidad", $valor);
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variable", $variable);
                    $this->getTemplate()->set_var("variable", $this->getTemplate()->pparse("variable"));
                    $this->getTemplate()->delete_parsed_blocks($variable);
                    $this->getTemplate()->parse("VariableBlock", true);
                }
            }
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error al procesar lo solicitado");
        }
    }
}