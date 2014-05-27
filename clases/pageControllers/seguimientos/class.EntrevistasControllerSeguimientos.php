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

        if($this->getRequest()->has('guardarRespuestas')){
            $this->guardarRespuestas();
            return;
        }
    }

    private function guardarRespuestas()
    {
        //set fecha realizado hoy, seria como guardar unidad esporadica

        //si no es editable no puedo seguir (realizada y expirada)

        //setPreguntasRespuestas() porque es el get que se usa en el SQL

        //guardarRespuestasEntrevista($oEntrevista);
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

            /*
                FALTA QUE LAS QUE ESTAN COMPLETADAS Y EXPIRADAS QUEDEN BLOQUEADAS
                POR JS POPUP AUTOMATICO Y RETURN FALSE
            */

            $aEntrevistasAsociadas = SeguimientosController::getInstance()->getEntrevistasBySeguimientoId($oSeguimiento->getId(), false);
            if(count($aEntrevistasAsociadas) > 0){

                $this->getTemplate()->set_var("NoRecordsAsociadasBlock", "");
                $htmlEntrevistas = "";

                foreach($aEntrevistasAsociadas as $oEntrevista){

                    $this->getTemplate()->set_var("iEntrevistaId", $oEntrevista->getId());
                    $this->getTemplate()->set_var("sDescripcionEntrevista", $oEntrevista->getDescripcion());

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entrevistas.gui.html", "entrevista", "EntrevistaListadoAsociarBlock");
                    $htmlEntrevistas .= $this->getTemplate()->pparse("entrevista", false);
                    $this->getTemplate()->delete_parsed_blocks("EntrevistaListadoAsociarBlock");
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

        $oEntrevista = SeguimientosController::getInstance()->getUnidadById($iEntrevistaId);

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

        $oEntrevista = SeguimientosController::getInstance()->getUnidadById($iEntrevistaId);
        if($oEntrevista->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para desasociar esta entrevista", 401);
        }

        if(!$oEntrevista->isEditable()){
            throw new Exception("La entrevista ya fue realizada y expiró el período de edición", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
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

    /**
     * Si la entrevista aun no fue realizada cargo el formulario.
     * Si ya se realizo muestro las preguntas con las respuestas sin formulario
     * Si todavia no expiro el tiempo de edicion (desde la fecha en la que se guardo x primera vez)
     * entonces muestro el boton de EDITAR, y cargo el form con las respuestas
     */
    public function ampliar()
    {
        /*
        $iUnidadId = $this->getRequest()->getParam('iUnidadEsporadicaId');
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        if(empty($iUnidadId) || empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);
        if(!$oUnidad->isPrecargada()){
            if(!SeguimientosController::getInstance()->isUnidadUsuario($iUnidadId)){
                throw new Exception("No tiene permiso para editar este seguimiento", 401);
            }
        }

        try{
            //ultima entrada en la que se asocio la unidad
            $oEntrada = $oUnidad->getUltimaEntrada($iSeguimientoId);

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "popUpContent", "AmpliarEntradaEsporadicaBlock");

            $this->getTemplate()->set_var("iUnidadIdForm", $iUnidadId);
            $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);
            $this->getTemplate()->set_var("subtituloSeccion", "Unidad: <span class='fost_it'>".$oUnidad->getNombre()."</span>");
            $this->getTemplate()->set_var("sUnidadDescripcion", $oUnidad->getDescripcion());

            //si $oEntrada == null, muestro el popup con el form pero con el mensaje de que no existen entradas. Sino muestro la info de la unidad
            if($oEntrada === null){
                $this->getTemplate()->set_var("EntradaEsporadicaBlock", "");
                $this->getTemplate()->set_var("VerEntradasButtonBlock", "");
                $this->getTemplate()->set_var("EliminarEntradaEsporadicaButtonBlock", "");

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTopEntrada", "MsgFichaHintBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Unidad sin entradas.");
                $this->getTemplate()->set_var("sMsgFicha", "Aún no se ha guardado información en esta unidad en ninguna fecha. Seleccione una fecha desde el calendario marcada como disponible.");
            }else{
                if(!$oEntrada->isEditable()){
                    $this->getTemplate()->set_var("EliminarEntradaEsporadicaButtonBlock", "");
                }
                $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));
                $sUltimaEntrada = str_replace("-", "/", $oEntrada->getFecha());
                $this->getTemplate()->set_var("sUltimaEntrada", $sUltimaEntrada);
                $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());
                $this->getTemplate()->set_var("hrefVerEntradasUnidadEsporadica", $this->getUrlFromRoute("seguimientosEntradasEntradasUnidadEsporadica", true)."?unidad=".$oUnidad->getId()."&seguimiento=".$iSeguimientoId);

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
        */
    }
}
