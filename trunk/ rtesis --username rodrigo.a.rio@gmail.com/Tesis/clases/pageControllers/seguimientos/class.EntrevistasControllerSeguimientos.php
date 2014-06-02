<?php

/**
 * @author Matias Velilla
 *
 */
class EntrevistasControllerSeguimientos extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroDescripcionEntrevista' => 'e.descripcion');

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

    private function setJsEntrevistas()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "jsContent", "JsContent");
        return $this;
    }

    private function setJsAsociarEntrevistaSeguimiento()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "jsContent", "JsContentAsociarEntrevistas");
        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "pageRightInnerCont", "PageRightInnerContListadoEntrevistasBlock");

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

        if($this->getRequest()->has('masEntrevistas')){
            $this->masEntrevistas();
            return;
        }

        if($this->getRequest()->has('verSeguimientos')){
            $this->verSeguimientos();
            return;
        }
    }

    /**
     * Seguimientos personalizados asociados a la entrevista
     */
    private function verSeguimientos()
    {
        $iEntrevistaId = $this->getRequest()->getParam('iEntrevistaId');
        if(empty($iEntrevistaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver esta entrevista", 401);
        }

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "ajaxSeguimientosAsociadosBlock", "VerSeguimientosAsociadosBlock");

        $iRecordsTotal = 0;
        $filtroSql["u.id"] = $iUsuarioId;
        $filtroSql["se.entrevistas_id"] = $iEntrevistaId;
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
                 ->setJsEntrevistas()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Plantilla de Entrevistas");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "pageRightInnerMainCont", "ListadoEntrevistasBlock");

            $iRecordsTotal = 0;
            $aEntrevistas = SeguimientosController::getInstance()->obtenerEntrevistasUsuario($filtro = array(), $iRecordsTotal, null, null, null, null);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
            if(count($aEntrevistas) > 0){

                $this->getTemplate()->set_var("NoRecordsThumbsEntrevistasBlock", "");

            	foreach ($aEntrevistas as $oEntrevista){
                    $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
                    $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

                    //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                    list($iCantidadPreguntasAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosEntrevista($oEntrevista->getId());
                    $this->getTemplate()->set_var("iCantidadPreguntas", $iCantidadPreguntasAsociadas);
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);

                    if($iCantidadSeguimientosAsociados > 0){
                        $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                        $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                    }else{
                        $this->getTemplate()->set_var("LinkSeguimientos", "");
                    }

                    $this->getTemplate()->set_var("hrefListarPreguntasEntrevista", $this->getUrlFromRoute("seguimientosPreguntasIndex", true)."?id=".$oEntrevista->getId());

                    $this->getTemplate()->parse("EntrevistaBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                    $this->getTemplate()->delete_parsed_blocks("NoLinkSeguimientos");
                    $this->getTemplate()->delete_parsed_blocks("LinkSeguimientos");
                }
            }else{
                $this->getTemplate()->set_var("EntrevistaBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function masEntrevistas()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "ajaxThumbnailsEntrevistasBlock", "ThumbsEntrevistasBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        $iRecordsTotal = 0;
        //en este listado no hay paginacion.
        $aEntrevistas = SeguimientosController::getInstance()->obtenerEntrevistasUsuario($filtroSql, $iRecordsTotal, null, null, null, null);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

        if(count($aEntrevistas) > 0){

            $this->getTemplate()->set_var("NoRecordsThumbsEntrevistasBlock", "");

            foreach ($aEntrevistas as $oEntrevista){
                $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
                $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

                //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                list($iCantidadPreguntasAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosEntrevista($oEntrevista->getId());
                $this->getTemplate()->set_var("iCantidadPreguntas", $iCantidadPreguntasAsociadas);
                $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);

                if($iCantidadSeguimientosAsociados > 0){
                    $this->getTemplate()->set_var("NoLinkSeguimientos", "");
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                }else{
                    $this->getTemplate()->set_var("LinkSeguimientos", "");
                }

                $this->getTemplate()->set_var("hrefListarPreguntasEntrevista", $this->getUrlFromRoute("seguimientosPreguntasIndex", true)."?id=".$oEntrevista->getId());

                $this->getTemplate()->parse("EntrevistaBlock", true);
                $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                $this->getTemplate()->delete_parsed_blocks("NoLinkSeguimientos");
                $this->getTemplate()->delete_parsed_blocks("LinkSeguimientos");
            }
        }else{
            $this->getTemplate()->set_var("EntrevistaBlock", "");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxThumbnailsEntrevistasBlock', false));
    }

    public function formCrearEntrevista()
    {
        $this->mostrarFormularioEntrevistaPopUp();
    }

    public function formEditarEntrevista()
    {
        $this->mostrarFormularioEntrevistaPopUp();
    }

    /**
     * Notar que esta private para que puedan manejarse permisos individuales en crear/modificar
     */
    private function mostrarFormularioEntrevistaPopUp()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "popUpContent", "FormularioEntrevistaBlock");

        //AGREGAR ENTREVISTA
        if($this->getRequest()->getActionName() == "formCrearEntrevista"){

            $this->getTemplate()->unset_blocks("SubmitModificarEntrevistaBlock");

            $sTituloForm = "Agregar una nueva Entrevista";

            //valores por defecto en el agregar
            $oEntrevista = null;
            $sDescripcion = "";

        //MODIFICAR ENTREVISTA
        }else{
            $iEntrevistaIdForm = $this->getRequest()->getParam('entrevistaId');
            if(empty($iEntrevistaIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $sTituloForm = "Modificar Entrevista";
            $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaIdForm);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oEntrevista->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta entrevista", 401);
            }

            $this->getTemplate()->unset_blocks("SubmitCrearEntrevistaBlock");
            $this->getTemplate()->set_var("iEntrevistaIdForm", $iEntrevistaIdForm);

            $sDescripcion = $oEntrevista->getDescripcion();
        }

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getResponse()->setBody($this->getTemplate()->pparse('frame', false)));
    }

    public function guardarEntrevista()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearEntrevista')){
            $this->crearEntrevista();
            return;
        }

        if($this->getRequest()->has('modificarEntrevista')){
            $this->modificarEntrevista();
            return;
        }
    }

    private function crearEntrevista()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oEntrevista = new stdClass();

            $oEntrevista->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oEntrevista->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            $oEntrevista = Factory::getEntrevistaInstance($oEntrevista);

            SeguimientosController::getInstance()->guardarEntrevista($oEntrevista);

            $this->getJsonHelper()->setValor("agregarEntrevista", "1");
            $this->getJsonHelper()->setMessage("La entrevista se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarEntrevista()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEntrevistaId = $this->getRequest()->getPost('entrevistaIdForm');
            $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oEntrevista->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta entrevista", 401);
            }

            $oEntrevista->setDescripcion($this->getRequest()->getPost("descripcion"));

            SeguimientosController::getInstance()->guardarEntrevista($oEntrevista);

            $this->getJsonHelper()->setMessage("La entrevista se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarEntrevista", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function eliminar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        //devuelvo el dialog para confirmar el borrado de la entrevista
        if($this->getRequest()->has('mostrarDialogConfirmar')){
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaInfoBlock");
            $this->getTemplate()->set_var("sTituloMsgFicha", "Plantilla de Entrevista");
            $this->getTemplate()->set_var("sMsgFicha", "Cuidado, se eliminaran de forma permanente todas las preguntas y las respuestas que hayan sido guardadas de los seguimientos a los que la entrevista esta asociada.
                                                        Solo se mantendrá una copia del historial para aquellas entrevistas realizadas hace mas de ".$cantDiasExpiracion." días.
                                                       <br>Una vez eliminada la Entrevista la información no podrá volver a recuperarse.");

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));
            return;
        }

        //elimino la entrevista seleccionada
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $iEntrevistaId = $this->getRequest()->getPost('iEntrevistaId');
            $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oEntrevista->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar esta entrevista", 401);
            }

            $oEntrevista->setFechaBorradoLogicoHoy();
            $result = SeguimientosController::getInstance()->borrarEntrevista($oEntrevista);

            if($result){
                $msg = "La Entrevista fue eliminada del sistema.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha podido eliminar la Entrevista del sistema.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la Entrevista del sistema.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Esta vista lista las entrevistas asociadas a un seguimiento y permite administrarlas mediante drag and drop.
     *
     */
    public function listarEntrevistasPorSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para administrar entrevistas en este seguimiento", 401);
        }

        try{
            $aCurrentOptions[] = "currentOptionAsociarEntrevistasSeguimiento";

            $this->setFrameTemplate()
                 ->setJsAsociarEntrevistaSeguimiento()
                 ->setHeadTag();

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            //para que pueda ser reutilizado en otras vistas
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $this->getTemplate()->set_var("tituloSeccion", "Asociar entrevistas a Seguimiento");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "pageRightInnerMainCont", "AsociarEntrevistasBlock");
            $iCantDias = SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento();
            $this->getTemplate()->set_var("iCantDiasExpiracion", $iCantDias);

            $aEntrevistasDisponibles = SeguimientosController::getInstance()->getEntrevistasDisponiblesBySeguimiento($oSeguimiento);

            if(count($aEntrevistasDisponibles) > 0){

                $this->getTemplate()->set_var("NoRecordsSinAsociarBlock", "");
                $htmlEntrevistas = "";

                foreach($aEntrevistasDisponibles as $oEntrevista){

                    $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
                    $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "entrevista", "EntrevistaListadoAsociarBlock");
                    $htmlEntrevistas .= $this->getTemplate()->pparse("entrevista", false);
                    $this->getTemplate()->delete_parsed_blocks("EntrevistaListadoAsociarBlock");
                }

                $this->getTemplate()->set_var("EntrevistasSinAsociar", $htmlEntrevistas);
            }else{
                $this->getTemplate()->set_var("EntrevistasSinAsociar", "");
            }

            //Obtengo la lista de entrevistas asociadas al seguimiento actualmente
            $aEntrevistasAsociadas = SeguimientosController::getInstance()->getEntrevistasBySeguimientoId($oSeguimiento->getId(), false);
            if(count($aEntrevistasAsociadas) > 0){

                $this->getTemplate()->set_var("NoRecordsAsociadasBlock", "");
                $htmlEntrevistas = "";

                foreach($aEntrevistasAsociadas as $oEntrevista){

                    if(!$oEntrevista->isEditable()){
                        $this->getTemplate()->set_var("expiradaClass", "thumb03 expirada");
                        $this->getTemplate()->set_var("title", "Edición Bloqueada");
                    }

                    $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
                    $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "entrevista", "EntrevistaListadoAsociarBlock");
                    $htmlEntrevistas .= $this->getTemplate()->pparse("entrevista", false);
                    $this->getTemplate()->delete_parsed_blocks("EntrevistaListadoAsociarBlock");
                    $this->getTemplate()->delete_parsed_blocks("ExpiradaBlock");
                }

                $this->getTemplate()->set_var("EntrevistasAsociadas", $htmlEntrevistas);
            }else{
                $this->getTemplate()->set_var("EntrevistasAsociadas", "");
            }


            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error");
        }
    }

    public function entrevistasPorSeguimientoProcesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('ampliarEntrevista')){
            $this->ampliarEntrevista();
            return;
        }

        if($this->getRequest()->has('dialogConfirmar')){
            $this->dialogConfirmar();
            return;
        }

        if($this->getRequest()->has('moverEntrevista')){
            if($this->getRequest()->getParam('moverEntrevista') == "asociarEntrevistaSeguimiento"){
                $this->asociarEntrevistaSeguimiento();
            }
            if($this->getRequest()->getParam('moverEntrevista') == "desasociarEntrevistaSeguimiento"){
                $this->desasociarEntrevistaSeguimiento();
            }
            return;
        }
    }

    private function dialogConfirmar()
    {
        $iCantDias = SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento();
        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaHintBlock");
        $this->getTemplate()->set_var("sTituloMsgFicha", "Desasociar Entrevista");
        $this->getTemplate()->set_var("sMsgFicha", "Se eliminara la entrevista para el seguimiento.<br>
                                                    Las respuestas no podrán recuperarse.<br>
                                                    Desea continuar?");

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));
    }

    private function ampliarEntrevista()
    {
        $iEntrevistaId = $this->getRequest()->getParam('iEntrevistaId');

        if(empty($iEntrevistaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver esta entrevista", 401);
        }

        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "popUpContent", "AmpliarEntrevistaBlock");

            //mostrar descripcion entrevista y lista de preguntas.
            $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

            $aPreguntas = SeguimientosController::getInstance()->getPreguntasByEntrevistaId($iEntrevistaId, false);
            $this->getTemplate()->set_var("iRecordsTotal", count($aPreguntas));
            if(count($aPreguntas) > 0){

                $this->getTemplate()->set_var("iEntrevistaId", $iEntrevistaId);
                $this->getTemplate()->set_var("NoRecordsPreguntasBlock", "");

            	foreach ($aPreguntas as $oPregunta){

                    $this->getTemplate()->set_var("iPreguntaId", $oPregunta->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oPregunta->getDescripcion());
                    $this->getTemplate()->set_var("dFechaHora", $oPregunta->getFecha(true));

                    if($oPregunta->isPreguntaMC()){
                        $this->getTemplate()->set_var("sTipo", "Multiple Choise");
                        $iconoPreguntaBlock = "IconoTipoMCBlock";
                        $sOpciones = "<strong>Opciones: </strong> ";
                        $aOpciones = $oPregunta->getOpciones();
                        foreach($aOpciones as $oOpcion){
                            $sOpciones .= $oOpcion->getDescripcion().", ";
                        }
                        $sOpciones = substr($sOpciones, 0, -2);
                        $this->getTemplate()->set_var("sOpciones", $sOpciones);
                    }else{
                        $this->getTemplate()->set_var("sTipo", "Abierta");
                        $iconoPreguntaBlock = "IconoTipoAbiertaBlock";
                        $this->getTemplate()->set_var("sOpciones", "");
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "iconoPregunta", $iconoPreguntaBlock);
                    $this->getTemplate()->set_var("iconoPregunta", $this->getTemplate()->pparse("iconoPregunta"));
                    $this->getTemplate()->delete_parsed_blocks($iconoPreguntaBlock);

                    $this->getTemplate()->parse("PreguntaBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("sNoRecords", "No hay preguntas cargadas en la entrevista");
                $this->getTemplate()->set_var("PreguntaBlock", "");
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error al procesar lo solicitado");
        }
    }

    private function asociarEntrevistaSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        $iEntrevistaId = $this->getRequest()->getParam('iEntrevistaId');

        if(empty($iSeguimientoId) || empty($iEntrevistaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
           throw new Exception("No tiene permiso para asociar esta entrevista", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            SeguimientosController::getInstance()->asociarEntrevistaSeguimiento($iSeguimientoId, $oEntrevista);
            $this->getJsonHelper()->setSuccess(true)
                                  ->sendJsonAjaxResponse();
            return;
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }
    }

    /**
     * No se puede desasociar una entrevista realizada si ya expiro. teoricamente js no te permite pero checkeamos
     * desde server tmb por las dudas.
     */
    private function desasociarEntrevistaSeguimiento()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        $iEntrevistaId = $this->getRequest()->getParam('iEntrevistaId');

        if(empty($iSeguimientoId) || empty($iEntrevistaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaBySeguimientoId($iEntrevistaId, $iSeguimientoId);
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para desasociar esta entrevista", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();

        if(!$oEntrevista->isEditable()){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }

        try{
            SeguimientosController::getInstance()->desasociarEntrevistaSeguimiento($iSeguimientoId, $oEntrevista);
            $this->getJsonHelper()->setSuccess(true)
                                  ->sendJsonAjaxResponse();
            return;
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }
    }

    public function formEntrevistaRespuestas()
    {
        $iEntrevistaId = $this->getRequest()->getParam("iEntrevistaId");
        $iSeguimientoId = $this->getRequest()->getParam("iSeguimientoId");
        if(null === $iEntrevistaId || null === $iSeguimientoId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaBySeguimientoId($iEntrevistaId, $iSeguimientoId);
        if($oEntrevista === null){
            throw new Exception("No existe la entrevista para el identificador seleccionado", 401);
        }

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar esta entrevista", 401);
        }

        //me fijo periodo de expiracion
        if(!$oEntrevista->isEditable()){
            throw new Exception("La entrevista no se puede editar, periodo de edicion expirado", 401);
        }

        try{
            $aCurrentOptions[] = "currentOptionAsociarEntrevistasSeguimiento";

            $this->setFrameTemplate()
                 ->setJsEntrevistas()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, null);
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Completar Entrevista");
            $this->getTemplate()->set_var("subtituloSeccion", "Entrevista: <span class='fost_it'>".$oEntrevista->getDescripcion()."</span>");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "pageRightInnerMainCont", "RealizarEntrevistaBlock");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
            $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
            $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

            $aPreguntas = $oEntrevista->getPreguntasRespuestas();

            if(count($aPreguntas) == 0){
                $this->getTemplate()->set_var("FormEntrevistaRespuestasBlock", "");
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "mensajeSinPreguntas", "MsgFichaInfoBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Preguntas Entrevista");
                $this->getTemplate()->set_var("sMsgFicha", "La entrevista se encuentra sin preguntas, no se puede generar el formulario");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }

            foreach($aPreguntas as $oPregunta){
                $this->getTemplate()->set_var("sPreguntaDescripcion", $oPregunta->getDescripcion());
                $this->getTemplate()->set_var("iPreguntaId", $oPregunta->getId());

                if($oPregunta->isPreguntaAbierta()){
                    $block = "PreguntaAbiertaEditar";
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "preguntaEditar", $block);
                    $this->getTemplate()->set_var("sRespuesta", $oPregunta->getRespuesta(true));
                }

                if($oPregunta->isPreguntaMC()){
                    $block = "PreguntaMCEditar";
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "preguntaEditar", $block);
                    $aOpciones = $oPregunta->getOpciones();
                    foreach($aOpciones as $oOpcion){
                        $this->getTemplate()->set_var("iOpcionId", $oOpcion->getId());
                        $this->getTemplate()->set_var("sDescripcion", $oOpcion->getDescripcion());
                        if($oPregunta->getRespuesta() !== null && $oOpcion->getId() == $oPregunta->getRespuesta()->getId()){
                            $this->getTemplate()->set_var("sChecked", "checked='checked'");
                        }
                        $this->getTemplate()->parse("OpcionEditar", true);
                        $this->getTemplate()->set_var("sChecked", "");
                    }
                }

                $this->getTemplate()->set_var("preguntaEntrevistaEditar", $this->getTemplate()->pparse("preguntaEditar"));
                $this->getTemplate()->delete_parsed_blocks($block);
                $this->getTemplate()->delete_parsed_blocks("OpcionEditar");
                $this->getTemplate()->parse("PreguntaEntrevistaEditarBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }

    public function formEntrevistaGuardarRespuestas()
    {
        $iEntrevistaId = $this->getRequest()->getPost("entrevistaIdForm");
        $iSeguimientoId = $this->getRequest()->getPost("seguimientoIdForm");

        if(null === $iEntrevistaId || null === $iSeguimientoId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaBySeguimientoId($iEntrevistaId, $iSeguimientoId);
        if($oEntrevista === null){
            throw new Exception("No existe la entrevista para el identificador seleccionado", 401);
        }

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar esta entrevista", 401);
        }

        //me fijo periodo de expiracion
        if(!$oEntrevista->isEditable()){
            throw new Exception("La entrevista no se puede editar, periodo de edicion expirado", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $vPreguntas = $this->getRequest()->getPost("preguntas");
            if( empty($vPreguntas) || !is_array($vPreguntas)){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El formulario no tiene un formato correcto");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $aPreguntas = array();
            foreach($vPreguntas as $pregunta){
                $iPreguntaId = $pregunta['id'];

                $oPregunta = SeguimientosController::getInstance()->getPreguntaById($iPreguntaId);

                //si es multiple choise levanto la opcion, sino agrego la respuesta de manera normal.
                if($oPregunta->isPreguntaMC()){
                    $oOpcion = null;
                    if(isset($pregunta['respuesta'])){
                        $oOpcion = SeguimientosController::getInstance()->getOpcionById($pregunta['respuesta']);
                        $oPregunta->setRespuesta($oOpcion);
                    }
                }else{
                    $oPregunta->setRespuesta($pregunta['respuesta']);
                }

                $aPreguntas[] = $oPregunta;
            }
            $oEntrevista->setPreguntas($aPreguntas);
            SeguimientosController::getInstance()->guardarRespuestasEntrevista($oEntrevista);

            $this->getJsonHelper()->setMessage("Las respuestas se han guardado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * muestro las preguntas con las respuestas
     * Si todavia no expiro el tiempo de edicion (desde la fecha en la que se guardo x primera vez)
     * entonces muestro el boton de EDITAR
     */
    public function ampliar()
    {
        $iEntrevistaId = $this->getRequest()->getParam("iEntrevistaId");
        $iSeguimientoId = $this->getRequest()->getParam("iSeguimientoId");

        if(null === $iEntrevistaId || null === $iSeguimientoId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaBySeguimientoId($iEntrevistaId, $iSeguimientoId);
        if($oEntrevista === null){
            throw new Exception("No existe la entrevista para el identificador seleccionado", 401);
        }

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ampliar esta entrevista", 401);
        }

        try{
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "popUpContent", "AmpliarEntrevistaRespuestasBlock");

            $this->getTemplate()->set_var("iEntrevistaId", $iEntrevistaId);
            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);

            $this->getTemplate()->set_var("subtituloSeccion", "<span class='fost_it'>".$oEntrevista->getDescripcion()."</span>");

            if(!$oEntrevista->isEditable()){
                $this->getTemplate()->set_var("FormEditarEntrevistaBlock", "");
            }else{
                $this->getTemplate()->set_var("hrefFormEntrevistaRespuestas", $this->getUrlFromRoute("seguimientosEntrevistasFormEntrevistaRespuestas", true));
            }

            $this->getTemplate()->set_var("sFechaRealizado", $oEntrevista->getFechaRealizado(true));

            $aPreguntas = $oEntrevista->getPreguntasRespuestas();
            foreach($aPreguntas as $oPregunta){

                $this->getTemplate()->set_var("sPreguntaDescripcion", $oPregunta->getDescripcion());

                if($oPregunta->isPreguntaAbierta()){
                    $pregunta = "PreguntaAbierta";
                    $respuesta = $oPregunta->getRespuesta(TRUE);
                    if(null === $respuesta){ $respuesta = " - "; }
                    $this->getTemplate()->set_var("sPreguntaRespuestaAbierta", $respuesta);
                }

                if($oPregunta->isPreguntaMC()){
                    $pregunta = "PreguntaMC";
                    //respuesta en multiple choise es un objeto Opcion
                    $respuesta = $oPregunta->getRespuestaStr();
                    if(null === $respuesta){ $respuesta = " - "; }
                    $this->getTemplate()->set_var("sPreguntaOpcion", $respuesta);
                }

                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "pregunta", $pregunta);
                $this->getTemplate()->set_var("pregunta", $this->getTemplate()->pparse("pregunta"));
                $this->getTemplate()->delete_parsed_blocks($pregunta);
                $this->getTemplate()->parse("PreguntaBlock", true);
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error al procesar lo solicitado");
        }
    }
}
