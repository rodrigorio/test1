<?php

/**
 * @author Matias Velilla
 *
 */
class EntradasControllerSeguimientos extends PageControllerAbstract
{  
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-02.gui.html", "frame");
        return $this;
    }

    private function setFrameTemplateB(){
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setTituloSeccion()
    {        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "iconoBlock", "IconoHeaderBlock");
        $this->getTemplate()->set_var("sTituloSeccion", "Entradas por fecha");
    }

    private function setContenidoColumnaIzquierda($oSeguimiento)
    {
        $oDiscapacitado = $oSeguimiento->getDiscapacitado();
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageBodyLeftCont", "PageBodyLeftContBlock");

        $hrefAmpliarSeguimiento = $this->getUrlFromRoute("seguimientosSeguimientosVer", true);
        $this->getTemplate()->set_var("hrefAmpliarSeguimiento", $hrefAmpliarSeguimiento);
        $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "fichaPersona", "PageRightInnerContFichaPersonaBlock");

        $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $this->getTemplate()->set_var("iPersonaId", $oDiscapacitado->getId());
        $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorSmallSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreSmallSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorSmallSize= $this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);
        $this->getTemplate()->set_var("scrFotoPerfilActual", $pathFotoServidorSmallSize);

        return $this;
    }
        
    public function index(){
        $bUltimaEntrada = true;
        $this->ampliar($bUltimaEntrada);
    }

    /**
     * Amplia una entrada para una fecha determinada.
     * Esta vista no es de edicion.
     */
    public function ampliar($bUltimaEntrada = false)
    {        
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }

        //tiene al menos un objetivo, antecedentes y diagnostico seteado?
        if(!SeguimientosController::getInstance()->checkEntradasOK($oSeguimiento)){
            $this->getRedirectorHelper()->setCode(307);
            $url = $this->getUrlFromRoute("seguimientosSeguimientosVer");
            $this->getRedirectorHelper()->gotoUrl($url."?iSeguimientoId=".$iSeguimientoId);
            return;
        }
        
        try{            
            $this->setFrameTemplate()
                 ->setHeadTag();
                 
            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $this->setTituloSeccion();
            $this->setContenidoColumnaIzquierda($oSeguimiento);
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageBodyCenterCont", "AmpliarEntradaBlock");

            $sFechaActual = strtok(date("d/m/Y"), " ");
            $this->getTemplate()->set_var("dFechaActual", $sFechaActual);

            $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
            if($bUltimaEntrada){
                $oEntrada = $oUltimaEntrada;

                //Si ultima entrada es null entonces no hay entradas en el seguimiento
                if($oEntrada === null){
                    $this->getTemplate()->unset_blocks("TituloBlock");
                    $this->getTemplate()->unset_blocks("MenuEntradaBlock");

                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTopEntrada", "MsgFichaHintBlock");
                    $this->getTemplate()->set_var("sTituloMsgFicha", "Seguimiento sin entradas.");
                    $this->getTemplate()->set_var("sMsgFicha", "Este seguimiento todavía no posee entradas. Para crear una nueva, seleccione una fecha desde el calendario marcada como disponible.");

                    $this->getTemplate()->unset_blocks("EntradaContBlock");
                    $this->getTemplate()->unset_blocks("HistorialEntradasBlock");
                    $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                    return;
                }
            }else{
                $sFechaEntrada = Utils::fechaAFormatoSQL($this->getRequest()->getParam("sDate"));
                if(null === $sFechaEntrada){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }
                
                $oEntrada = $oSeguimiento->getEntradaByFecha($sFechaEntrada);                
                if($oEntrada === null){
                    throw new Exception("No existe entrada en la fecha ".$this->getRequest()->getParam("sDate"), 401);
                }
            }

            $result = Utils::dateDiffDays($oSeguimiento->getUltimaEntrada()->getFecha(), date('Y-m-d'));
            if($result == 0){
                $this->getTemplate()->set_var("BlockCrearEntradaHoy", "");
            }

            $sEntradaActual = str_replace("-", "/", $oEntrada->getFecha());
            $sUltimaEntrada = str_replace("-", "/", $oSeguimiento->getUltimaEntrada()->getFecha());
            $this->getTemplate()->set_var("sEntradaActual", $sEntradaActual);
            $this->getTemplate()->set_var("sUltimaEntrada", $sUltimaEntrada);            
            $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));       
            $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());
            
            if(!$oEntrada->isEditable()){
                $this->getTemplate()->set_var("EditarEntradaBlock", "");
                if($oEntrada->isGuardada()){
                    $this->getTemplate()->set_var("EliminarEntradaBlock", "");
                }
            }else{
                $hrefEditar = $this->getUrlFromRoute("seguimientosEntradasEditar", true)."?entrada=".$oEntrada->getId();
                $this->getTemplate()->set_var("hrefEditarEntrada", $hrefEditar);
            }

            $this->crearWidgetHistorial($iSeguimientoId);
                                                            
            $aObjetivos = $oEntrada->getObjetivos();                       
            $this->getTemplate()->set_var("iRecordsTotal", count($aObjetivos));
            foreach($aObjetivos as $oObjetivo){
                $this->getTemplate()->set_var("iObjetivoId", $oObjetivo->getId());
                $this->getTemplate()->set_var("sRelevancia", $oObjetivo->getRelevancia()->getDescripcion());

                switch($oObjetivo->getRelevancia()->getDescripcion())
                {
                    case "baja":{
                        $iconoRelevanciaBlock = "IconoRelevanciaBajaBlock";
                        break;
                    }
                    case "normal":{
                        $iconoRelevanciaBlock = "IconoRelevanciaNormalBlock";
                        break;
                    }
                    case "alta":{
                        $iconoRelevanciaBlock = "IconoRelevanciaAltaBlock";
                        break;
                    }
                }
                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "iconoRelevancia", $iconoRelevanciaBlock);
                $this->getTemplate()->set_var("iconoRelevancia", $this->getTemplate()->pparse("iconoRelevancia"));
                $this->getTemplate()->delete_parsed_blocks($iconoRelevanciaBlock);

                if($oObjetivo->isLogrado()){
                    $this->getTemplate()->set_var("EstimacionBlock", "");
                    $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha(true));
                }else{
                    $this->getTemplate()->set_var("FechaLogradoBlock", "");
                    $this->getTemplate()->set_var("dEstimacion", $oObjetivo->getEstimacion(true));
                    if(!$oObjetivo->isEstimacionVencida()){
                        $this->getTemplate()->set_var("expiradaClass", "");
                    }else{
                        $this->getTemplate()->set_var("expiradaClass", "txt_cuidado");
                    }
                }

                if($oObjetivo->isObjetivoPersonalizado()){
                    $this->getTemplate()->set_var("sDescripcionEjeBreve", $oObjetivo->getEje()->getDescripcion());

                    $sDescripcionEje = $oObjetivo->getEje()->getEjePadre()->getDescripcion()." > ".$oObjetivo->getEje()->getDescripcion();
                    $this->getTemplate()->set_var("sDescripcionEje", $sDescripcionEje);
                }

                if($oObjetivo->isObjetivoAprendizaje()){
                    $this->getTemplate()->set_var("sNivel", $oObjetivo->getEje()->getArea()->getCiclo()->getNivel()->getDescripcion());
                    $this->getTemplate()->set_var("sCiclo", $oObjetivo->getEje()->getArea()->getCiclo()->getDescripcion());
                    $this->getTemplate()->set_var("sArea", $oObjetivo->getEje()->getArea()->getDescripcion());

                    $sDescripcionEje = $oObjetivo->getEje()->getArea()->getCiclo()->getNivel()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getArea()->getCiclo()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getArea()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getDescripcion();

                    $this->getTemplate()->set_var("sDescripcionEjeBreve", $oObjetivo->getEje()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionEje", $sDescripcionEje);
                }

                $sDescripcionObjetivoBreve = $oObjetivo->getDescripcion();
                if(strlen($sDescripcionObjetivoBreve) > 100){
                    $sDescripcionObjetivoBreve = Utils::tokenTruncate($sDescripcionObjetivoBreve, 100);
                    $sDescripcionObjetivoBreve = nl2br($sDescripcionObjetivoBreve);
                }

                $this->getTemplate()->set_var("sDescripcionObjetivoBreve", $sDescripcionObjetivoBreve);
                $this->getTemplate()->set_var("sDescripcionObjetivo", $oObjetivo->getDescripcion(true));

                $oEvolucion = $oObjetivo->getUltimaEvolucionToDate($oEntrada->getFecha());
                if(null === $oEvolucion){
                    $this->getTemplate()->set_var("iEvolucion", "0");
                    $this->getTemplate()->set_var("VerComentariosEvolucionBlock", "");
                }else{                                   
                    $this->getTemplate()->set_var("iEvolucion", $oEvolucion->getProgreso());
                    if($oEvolucion->isObjetivoLogrado()){
                        $this->getTemplate()->set_var("sGoalClass", "goal");
                    }else{
                        $this->getTemplate()->set_var("sGoalClass", "");
                    }
                    $this->getTemplate()->set_var("sEvolucionComentarios", $oEvolucion->getComentarios());
                }

                $this->getTemplate()->set_var("dFechaCreacion", $oObjetivo->getFechaCreacion(true));
                
                $this->getTemplate()->parse("ObjetivoBlock", true);

                $this->getTemplate()->delete_parsed_blocks("EstimacionBlock");
                $this->getTemplate()->delete_parsed_blocks("FechaLogradoBlock");
                $this->getTemplate()->delete_parsed_blocks("VerComentariosEvolucionBlock");
            }

            $aUnidades = $oEntrada->getUnidades();                        
            foreach($aUnidades as $oUnidad)
            {
                $aVariables = $oUnidad->getVariables();

                //si no tiene variables la salteo. puede darse que asocio una unidad y no tiene variables
                // TAMBIEN PUEDE SER QUE HAYA DESASOCIADO UNA UNIDAD, CREO UNA ENTRADA SIN LA UNIDAD Y DESPUES VOLVIO A ASOCIAR LA UNIDAD.
                // ESTO HACE QUE QUEDE UN 'HUECO' EN EL QUE LA ENTRADA LEVANTA LA UNIDAD PERO NO HAY VARIABLES EN ELLA.
                if(count($aVariables) == 0){
                    continue;
                }

                $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
                $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());                                
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

                $this->getTemplate()->parse("UnidadBlock", true);
                $this->getTemplate()->delete_parsed_blocks("VariableBlock");
            }
                        
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }

    private function crearWidgetHistorial($iSeguimientoId)
    {
        $aYears = SeguimientosController::getInstance()->obtenerCantidadEntradasByMonths($iSeguimientoId);
        foreach($aYears as $oYear){
            $this->getTemplate()->set_var("sYear", $oYear->year);
            foreach($oYear->months as $oMonth){
                $this->getTemplate()->set_var("sMonth", $oMonth->month);
                $this->getTemplate()->set_var("sMonthNumber", $oMonth->monthNumber);
                $this->getTemplate()->set_var("iCantidad", $oMonth->cantidad);
                $this->getTemplate()->parse("MonthHistorialBlock", true);
            }
            $this->getTemplate()->parse("YearHistorialBlock", true);
            $this->getTemplate()->delete_parsed_blocks("MonthHistorialBlock");
        }
    }

    public function procesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('fechasEntradasMes')){
            $this->fechasEntradasMes();
            return;
        }
    }

    /**
     * Devuelve json al front con un array de fechas formato yyyy/(mm-1)/dd de un mes dado por GET
     */
    private function fechasEntradasMes()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        $sYear = $this->getRequest()->getParam('year');
        $sMonth = $this->getRequest()->getParam('month');

        if(empty($iSeguimientoId) || empty($sYear) || empty($sMonth)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
                
        //primer y ultima fecha del mes (inclusive)
        $dFechaDesde = $sYear."-".$sMonth."-01";
        $dFechaHasta = date($sYear.'-'.$sMonth.'-t');
        $aEntradas = $oSeguimiento->getEntradas($dFechaDesde, $dFechaHasta);

        $aDates = array();
        if(count($aEntradas) > 0){
            foreach($aEntradas as $oEntrada){
                $sDate = str_replace("-", "/", $oEntrada->getFecha());
                $aDates[] = $sDate;
            }
        }

        $this->getJsonHelper()->initJsonAjaxResponse()->sendJson($aDates);
    }

    public function crear()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        $dFecha = $this->getRequest()->getParam('dFecha');

        if(empty($iSeguimientoId) || empty($dFecha)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }

        //primero dialog de confirmar
        try{
            if($this->getRequest()->has('confirmar')){
                $this->confirmar($dFecha);
                return;
            }
        }catch(Exception $e){
            throw $e;
        }

        if($this->getRequest()->has('crearEntradaEsporadica')){
            $iUnidadId = $this->getRequest()->getParam('iUnidadId');
            if(empty($iUnidadId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }
        }

        $this->getJsonHelper()->initJsonAjaxResponse();                       
        try{
            //si confirmo, creo la entrada. Puede ser entrada esporadica o regular. todavia no se de donde vino
            if($this->getRequest()->has('crearEntradaEsporadica')){
                //se acomoda solo esta, la otra ya viene en formato sql desde javascript
                $dFecha = Utils::fechaAFormatoSQL($dFecha);
                $oEntrada = SeguimientosController::getInstance()->crearEntradaUnidadEsporadica($oSeguimiento, $iUnidadId, $dFecha);
                $sMensaje = "La unidad se creo con éxito, luego de completar el formulario de edición podrá ver el listado de todas las fechas.";
            }else{
                $oEntrada = SeguimientosController::getInstance()->crearEntrada($oSeguimiento, $dFecha);
                $sMensaje = "Tenga en cuenta que las variaciones variables numéricas o cualitativas son utilizadas para generación de gráficos.";
            }
            
            SeguimientosController::getInstance()->guardarEntrada($oEntrada);
            $sFechaUrl = $oEntrada->getFecha(true);
            $sRedirect = "/seguimientos/entradas/editar?entrada=".$oEntrada->getId();

            //mensaje de creacion exitosa
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaCorrectoBlock");
            $this->getTemplate()->set_var("sTituloMsgFicha", "Creación exitosa");
            $this->getTemplate()->set_var("sMsgFicha", $sMensaje);
            $html = $this->getTemplate()->pparse('html', false);

            $this->getJsonHelper()->setSuccess(true)
                                  ->setValor("html", $html)
                                  ->setRedirect($sRedirect)
                                  ->sendJsonAjaxResponse();
            return;
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false)->setMessage("Ocurrio un error al tratar de crear la entrada.");
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }            
    }

    private function confirmar($dFecha)
    {
        //la fecha de creacion esta fuera del periodo de edicion de seguimientos?
        $dFechaMsg = Utils::fechaFormateada($dFecha, "d/m/Y");
        $iCantDias = SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento();
        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaHintBlock");
        $this->getTemplate()->set_var("sTituloMsgFicha", "Nueva entrada en historial");
        $this->getTemplate()->set_var("sMsgFicha", "Se creará una nueva entrada en el seguimiento el día <strong>".$dFechaMsg."</strong>.<br>
                                                    El sistema brinda un plazo de edición de <strong>".$iCantDias."</strong> días una vez creada.<br>
                                                    Vencido el plazo la entrada no podra editarse y solo podrá eliminarse si no se ha guardado información en ella.
                                                    Desea continuar?");

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('html', false));       
    }

    public function eliminar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iEntradaId = $this->getRequest()->getParam("iEntradaId");
        if(null === $iEntradaId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrada = SeguimientosController::getInstance()->getEntradaById($iEntradaId);
        if($oEntrada === null){
            throw new Exception("No existe la entrada para el identificador seleccionado", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($oEntrada->getSeguimientoId());
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para eliminar esta entrada", 401);
        }

        //me fijo periodo de expiracion
        if(!$oEntrada->isEditable() && $oEntrada->isGuardada()){
            throw new Exception("La entrada no se puede eliminar, periodo de edicion expirado", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        
        $bConfirmo = $this->getRequest()->getParam("confirmo") == "1"?true:false;
        //si se edito al menos 1 vez devuelvo el dialog de confirmacion, el otro flag es para mostrar solo una vez (viene del js)
        if($oEntrada->isGuardada() && !$bConfirmo){
            $dFechaEntrada = $oEntrada->getFecha(true);
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgFichaInfoBlock");
            $this->getTemplate()->set_var("sTituloMsgFicha", "Contenidos entrada");
            $this->getTemplate()->set_var("sMsgFicha", "Se eliminará de forma permanente toda la información ingresada en el día ".$dFechaEntrada.". Desea continuar?");

            $this->getJsonHelper()->setSuccess(true)
                                  ->setValor("html", $this->getTemplate()->pparse('html', false))
                                  ->setValor("confirmar", "1")
                                  ->sendJsonAjaxResponse();
            return;
        }

        //elimino la entrada
        try{
            $result = SeguimientosController::getInstance()->borrarEntrada($oEntrada);

            if($result){
                //redirecciono a la ultima entrada
                $redirect = $this->getUrlFromRoute("seguimientosEntradasIndex")."?iSeguimientoId=".$oEntrada->getSeguimientoId();
                
                $msg = "La entrada para el dia ".$oEntrada->getFecha(true)." junto a toda la información asociada, fue eliminada del sistema.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true)
                                      ->setRedirect($redirect);
            }else{
                $msg = "Ocurrio un error, no se ha podido eliminar la Entrada del sistema.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la Entrada del sistema.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));
        $this->getJsonHelper()->sendJsonAjaxResponse();         
    }

    public function editar()
    {        
        $iEntradaId = $this->getRequest()->getParam("entrada");
        if(null === $iEntradaId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrada = SeguimientosController::getInstance()->getEntradaById($iEntradaId);
        if($oEntrada === null){
            throw new Exception("No existe la entrada para el identificador seleccionado", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($oEntrada->getSeguimientoId());
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar esta entrada", 401);
        }

        //me fijo periodo de expiracion
        if(!$oEntrada->isEditable()){
            throw new Exception("La entrada no se puede editar, periodo de edicion expirado", 401);
        }

        //puedo devolver el formulario de edicion de evolucion para un objetivo
        if($this->getRequest()->has('formEvolucion')){
            $this->formEvolucion($oEntrada, $oSeguimiento);
            return;
        }

        //puede ser una entrada con solo una unidad esporadica desvio a otra vista
        if($oEntrada->isEsporadica()){
            $this->editarEsporadica($oEntrada, $oSeguimiento);
            return;
        }

        try{          
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $this->setTituloSeccion();
            $this->setContenidoColumnaIzquierda($oSeguimiento);
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageBodyCenterCont", "EditarEntradaBlock");

            $result = Utils::dateDiffDays($oSeguimiento->getUltimaEntrada()->getFecha(), date('Y-m-d'));
            if($result == 0){
                $this->getTemplate()->set_var("BlockCrearEntradaHoy", "");
            }

            $sEntradaActual = str_replace("-", "/", $oEntrada->getFecha());
            $sUltimaEntrada = str_replace("-", "/", $oSeguimiento->getUltimaEntrada()->getFecha());
            $sFechaActual = strtok(date("d/m/Y"), " ");
            $this->getTemplate()->set_var("sEntradaActual", $sEntradaActual);
            $this->getTemplate()->set_var("sUltimaEntrada", $sUltimaEntrada);
            $this->getTemplate()->set_var("dFechaActual", $sFechaActual);
            $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));
            $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());

            $hrefCerrarVistaEdicion = $this->getRequest()->getBaseUrl()."/seguimientos/entradas/".$oEntrada->getSeguimientoId()."-".$oEntrada->getFecha(true);
            $this->getTemplate()->set_var("hrefCerrarVistaEdicion", $hrefCerrarVistaEdicion);

            $this->crearWidgetHistorial($oSeguimiento->getId());

            $aObjetivos = $oEntrada->getObjetivos();

            $this->getTemplate()->set_var("iRecordsTotal", count($aObjetivos));
            foreach($aObjetivos as $oObjetivo){
                $this->getTemplate()->set_var("iObjetivoId", $oObjetivo->getId());
                $this->getTemplate()->set_var("sRelevancia", $oObjetivo->getRelevancia()->getDescripcion());

                switch($oObjetivo->getRelevancia()->getDescripcion())
                {
                    case "baja":{
                        $iconoRelevanciaBlock = "IconoRelevanciaBajaBlock";
                        break;
                    }
                    case "normal":{
                        $iconoRelevanciaBlock = "IconoRelevanciaNormalBlock";
                        break;
                    }
                    case "alta":{
                        $iconoRelevanciaBlock = "IconoRelevanciaAltaBlock";
                        break;
                    }
                }
                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "iconoRelevancia", $iconoRelevanciaBlock);
                $this->getTemplate()->set_var("iconoRelevancia", $this->getTemplate()->pparse("iconoRelevancia"));
                $this->getTemplate()->delete_parsed_blocks($iconoRelevanciaBlock);

                if($oObjetivo->isLogrado()){
                    $this->getTemplate()->set_var("EstimacionEditarBlock", "");
                    $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha(true));
                }else{
                    $this->getTemplate()->set_var("FechaLogradoEditarBlock", "");
                    $this->getTemplate()->set_var("dEstimacion", $oObjetivo->getEstimacion(true));
                    if(!$oObjetivo->isEstimacionVencida()){
                        $this->getTemplate()->set_var("expiradaClass", "");
                    }else{
                        $this->getTemplate()->set_var("expiradaClass", "txt_cuidado");
                    }
                }

                if($oObjetivo->isObjetivoPersonalizado()){
                    $this->getTemplate()->set_var("sDescripcionEjeBreve", $oObjetivo->getEje()->getDescripcion());

                    $sDescripcionEje = $oObjetivo->getEje()->getEjePadre()->getDescripcion()." > ".$oObjetivo->getEje()->getDescripcion();
                    $this->getTemplate()->set_var("sDescripcionEje", $sDescripcionEje);
                }

                if($oObjetivo->isObjetivoAprendizaje()){
                    $this->getTemplate()->set_var("sNivel", $oObjetivo->getEje()->getArea()->getCiclo()->getNivel()->getDescripcion());
                    $this->getTemplate()->set_var("sCiclo", $oObjetivo->getEje()->getArea()->getCiclo()->getDescripcion());
                    $this->getTemplate()->set_var("sArea", $oObjetivo->getEje()->getArea()->getDescripcion());

                    $sDescripcionEje = $oObjetivo->getEje()->getArea()->getCiclo()->getNivel()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getArea()->getCiclo()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getArea()->getDescripcion()." > ".
                                       $oObjetivo->getEje()->getDescripcion();

                    $this->getTemplate()->set_var("sDescripcionEjeBreve", $oObjetivo->getEje()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionEje", $sDescripcionEje);
                }

                $sDescripcionObjetivoBreve = $oObjetivo->getDescripcion();
                if(strlen($sDescripcionObjetivoBreve) > 100){
                    $sDescripcionObjetivoBreve = Utils::tokenTruncate($sDescripcionObjetivoBreve, 100);
                    $sDescripcionObjetivoBreve = nl2br($sDescripcionObjetivoBreve);
                }

                $this->getTemplate()->set_var("sDescripcionObjetivoBreve", $sDescripcionObjetivoBreve);
                $this->getTemplate()->set_var("sDescripcionObjetivo", $oObjetivo->getDescripcion(true));

                //la evolucion puede no existir, ser de la entrada actual o provenir de una entrada anterior(evolucion mas cercana)
                $oEvolucion = $oObjetivo->getUltimaEvolucionToDate($oEntrada->getFecha());                
                if(null === $oEvolucion){
                    $this->getTemplate()->set_var("iEvolucion", "0");
                    $this->getTemplate()->set_var("EditarProgresoBlock", "");
                }else{
                    if($oEvolucion->getEntradaId() == $iEntradaId){
                        $this->getTemplate()->set_var("CrearEvolucionBlock", "");
                        $this->getTemplate()->set_var("iEvolucionId", $oEvolucion->getId());
                    }else{
                        $this->getTemplate()->set_var("EditarProgresoBlock", "");
                    }                    
                    $this->getTemplate()->set_var("iEvolucion", $oEvolucion->getProgreso());
                    if($oEvolucion->isObjetivoLogrado()){
                        $this->getTemplate()->set_var("sGoalClass", "goal");
                    }else{
                        $this->getTemplate()->set_var("sGoalClass", "");
                    }
                }

                $this->getTemplate()->set_var("dFechaCreacion", $oObjetivo->getFechaCreacion(true));

                $this->getTemplate()->parse("ObjetivoEditarBlock", true);

                $this->getTemplate()->delete_parsed_blocks("EstimacionEditarBlock");
                $this->getTemplate()->delete_parsed_blocks("FechaLogradoEditarBlock");
                $this->getTemplate()->delete_parsed_blocks("EditarProgresoBlock");
                $this->getTemplate()->delete_parsed_blocks("CrearEvolucionBlock");
                $this->getTemplate()->delete_parsed_blocks("ProgresoEvolucionBlock");
            }

            $aUnidades = $oEntrada->getUnidades();
            foreach($aUnidades as $oUnidad)
            {
                $aVariables = $oUnidad->getVariables();

                //si no tiene variables la salteo. puede darse que asocio una unidad y no tiene variables
                // TAMBIEN PUEDE SER QUE HAYA DESASOCIADO UNA UNIDAD, CREO UNA ENTRADA SIN LA UNIDAD Y DESPUES VOLVIO A ASOCIAR LA UNIDAD.
                // ESTO HACE QUE QUEDE UN 'HUECO' EN EL QUE LA ENTRADA LEVANTA LA UNIDAD PERO NO HAY VARIABLES EN ELLA.
                if(count($aVariables) == 0){
                    continue;
                }

                $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
                $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());                
                foreach($aVariables as $oVariable){

                    $this->getTemplate()->set_var("sVariableDescription", $oVariable->getDescripcion());
                    $this->getTemplate()->set_var("sVariableNombre", $oVariable->getNombre());
                    $this->getTemplate()->set_var("iVariableId", $oVariable->getId());

                    if($oVariable->isVariableNumerica()){
                        $block = "VariableNumericaEditar";
                        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                        $this->getTemplate()->set_var("sValor", $oVariable->getValor());
                    }

                    if($oVariable->isVariableTexto()){
                        $block = "VariableTextoEditar";
                        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                        $this->getTemplate()->set_var("sValor", $oVariable->getValor(true));
                    }

                    if($oVariable->isVariableCualitativa()){
                        $block = "VariableCualitativaEditar";
                        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                        $aModalidades = $oVariable->getModalidades();
                        foreach($aModalidades as $oModalidad){
                            $this->getTemplate()->set_var("iModalidadId", $oModalidad->getId());
                            $this->getTemplate()->set_var("sModalidad", $oModalidad->getModalidad());
                            if($oVariable->getValor() !== null && $oModalidad->getId() == $oVariable->getValor()->getId()){
                                $this->getTemplate()->set_var("sChecked", "checked='checked'");
                            }                            
                            $this->getTemplate()->parse("ModalidadEditar", true);
                            $this->getTemplate()->set_var("sChecked", "");
                        }
                    }
                    
                    $this->getTemplate()->set_var("variableEditar", $this->getTemplate()->pparse("variableEditar"));
                    $this->getTemplate()->delete_parsed_blocks($block);
                    $this->getTemplate()->delete_parsed_blocks("ModalidadEditar");
                    $this->getTemplate()->parse("VariableEditarBlock", true);
                }

                $this->getTemplate()->parse("UnidadEditarBlock", true);
                $this->getTemplate()->delete_parsed_blocks("VariableEditarBlock");
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }

    private function editarEsporadica($oEntrada, $oSeguimiento)
    {
        //es solo una unidad
        $aUnidad = $oEntrada->getUnidades();
        $oUnidad = $aUnidad[0];

        if(null === $oUnidad){
            throw new Exception("La unidad no tiene entrada para esta fecha", 401);
        }
        
        try{
            $this->setFrameTemplateB()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, null);
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Unidad Esporádica");
            $this->getTemplate()->set_var("subtituloSeccion", "Unidad: <span class='fost_it'>".$oUnidad->getNombre()."</span>");
            $this->getTemplate()->set_var("sUnidadDescripcion", $oUnidad->getDescripcion(true));

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageRightInnerMainCont", "EditarEntradaEsporadicaBlock");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
            $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());
            $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
            $this->getTemplate()->set_var("sNombreUnidadEsporadica", $oUnidad->getNombre());

            $aVariables = $oUnidad->getVariables();

            if(count($aVariables) == 0){
                $this->getTemplate()->set_var("FormEditarEsporadicaBlock", "");
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "mensajeSinVariables", "MsgFichaInfoBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Variables Unidad");
                $this->getTemplate()->set_var("sMsgFicha", "La unidad se encuentra sin variables, no se puede generar el formulario");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }
            
            foreach($aVariables as $oVariable){
                $this->getTemplate()->set_var("sVariableDescription", $oVariable->getDescripcion());
                $this->getTemplate()->set_var("sVariableNombre", $oVariable->getNombre());
                $this->getTemplate()->set_var("iVariableId", $oVariable->getId());

                if($oVariable->isVariableNumerica()){
                    $block = "VariableNumericaEditar";
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                    $this->getTemplate()->set_var("sValor", $oVariable->getValor());
                }

                if($oVariable->isVariableTexto()){
                    $block = "VariableTextoEditar";
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                    $this->getTemplate()->set_var("sValor", $oVariable->getValor(true));
                }

                if($oVariable->isVariableCualitativa()){
                    $block = "VariableCualitativaEditar";
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "variableEditar", $block);
                    $aModalidades = $oVariable->getModalidades();
                    foreach($aModalidades as $oModalidad){
                        $this->getTemplate()->set_var("iModalidadId", $oModalidad->getId());
                        $this->getTemplate()->set_var("sModalidad", $oModalidad->getModalidad());
                        if($oVariable->getValor() !== null && $oModalidad->getId() == $oVariable->getValor()->getId()){
                            $this->getTemplate()->set_var("sChecked", "checked='checked'");
                        }
                        $this->getTemplate()->parse("ModalidadEditar", true);
                        $this->getTemplate()->set_var("sChecked", "");
                    }
                }

                $this->getTemplate()->set_var("variableUnidadEsporadicaEditar", $this->getTemplate()->pparse("variableEditar"));
                $this->getTemplate()->delete_parsed_blocks($block);
                $this->getTemplate()->delete_parsed_blocks("ModalidadEditar");
                $this->getTemplate()->parse("VariableUnidadEsporadicaEditarBlock", true);
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }

    private function formEvolucion($oEntrada, $oSeguimiento)
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "formEvolucion", "FormularioEvolucionBlock");

        $iObjetivoId = $this->getRequest()->getParam("iObjetivoId");
        if(null === $iObjetivoId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        if($this->getRequest()->has('editarProgresoEvolucion')){
            $iEvolucionId = $this->getRequest()->getParam("iEvolucionId");
            if(null === $iEvolucionId){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            try{
                $oEvolucion = SeguimientosController::getInstance()->getEvolucionById($iEvolucionId);
                $iProgreso = $oEvolucion->getProgreso();

                $this->getTemplate()->set_var("FormCrearEvolucionBlock", "");
                $this->getTemplate()->set_var("sComentarios", $oEvolucion->getComentarios());
                $this->getTemplate()->set_var("iProgreso", $oEvolucion->getProgreso());
                $this->getTemplate()->set_var("iEvolucionIdForm", $iEvolucionId);
            }catch(Exception $e){
                throw $e;
            }
        }

        if($this->getRequest()->has('crearEvolucion')){
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($oSeguimiento->getId(), $iObjetivoId);
            }

            try{
                $this->getTemplate()->set_var("FormEditarProgresoBlock", "");
                $this->getTemplate()->set_var("sComentarios", "");

                //levanto ultima evolucion to date, si es != null copio el ultimo progreso
                $iProgreso = "1"; //porque si se crea una evolucion es valor entre 1 y 100. solo cuando es null va un 0.
                $oUltimaEvolucion = $oObjetivo->getUltimaEvolucionToDate($oEntrada->getFecha());
                if(null !== $oUltimaEvolucion){
                    $iProgreso = $oUltimaEvolucion->getProgreso();
                }
                $this->getTemplate()->set_var("iProgreso", $iProgreso);
            }catch(Exception $e){
                throw $e;
            }
        }

        $this->getTemplate()->set_var("iEntradaIdForm", $oEntrada->getId());
        $this->getTemplate()->set_var("iObjetivoIdForm", $iObjetivoId);
        $this->getResponse()->setBody($this->getTemplate()->pparse('formEvolucion', false));
    }

    /**
     * Se guarda tanto el progreso dentro de la evolucion (form evolucion) o
     * los valores para todas las variables de todas las unidades (form unidades)
     */
    public function guardar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iEntradaId = $this->getRequest()->getPost("entradaIdForm");
        if(null === $iEntradaId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEntrada = SeguimientosController::getInstance()->getEntradaById($iEntradaId);
        if($oEntrada === null){
            throw new Exception("No existe la entrada para el identificador seleccionado", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($oEntrada->getSeguimientoId());
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar esta entrada", 401);
        }

        //me fijo periodo de expiracion
        if(!$oEntrada->isEditable()){
            throw new Exception("La entrada no se puede editar, periodo de edicion expirado", 401);
        }
        
        if($this->getRequest()->has('editarProgreso') || $this->getRequest()->has('crearEvolucion')){
            $this->guardarEvolucion($oEntrada, $oSeguimiento);
            return;
        }
        
        if($this->getRequest()->has('guardarUnidad') || $this->getRequest()->has('guardarUnidadEsporadica')){
            
            $iUnidadId = $this->getRequest()->getPost("unidadIdForm");
            if(null === $iUnidadId){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            /*
             * Guardo todas las variables para una unidad.
             * La estrategia: Obtengo la unidad, agrego todas las variables que vienen del form,
             * adjunto la unidad a la entrada y actualizo la entrada.
             */
            try{
                $this->getJsonHelper()->initJsonAjaxResponse();

                $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);

                $vVariables = $this->getRequest()->getPost("variables");                
                if( empty($vVariables) || !is_array($vVariables)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("El formulario no tiene un formato correcto");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }

                $aVariables = array();
                foreach($vVariables as $variable){
                    $iVariableId = $variable['id'];

                    $oVariable = SeguimientosController::getInstance()->getVariableById($iVariableId);

                    //si es cualitativa levanto la modalidad, sino agrego el valor de manera normal.
                    if($oVariable->isVariableCualitativa()){
                        $oModalidad = null;
                        if(isset($variable['valor'])){
                            $oModalidad = SeguimientosController::getInstance()->getModalidadById($variable['valor']);
                            $oVariable->setValor($oModalidad);
                        }
                    }else{
                        $oVariable->setValor($variable['valor']);
                    }

                    $aVariables[] = $oVariable;
                }
                $oUnidad->setVariables($aVariables);

                $oEntrada->setUnidades(null);
                $oEntrada->addUnidad($oUnidad);
                
                SeguimientosController::getInstance()->guardarEntrada($oEntrada);
                
                $this->getJsonHelper()->setSuccess(true);
                $this->getJsonHelper()->setMessage("La unidad se guardo correctamente.");

                if($this->getRequest()->has('guardarUnidadEsporadica')){
                    //redirecciona a la vista del listado de todas las entradas por unidad.
                    $sRedirect = "/seguimientos/entradas/entradas-unidad-esporadica?unidad=".$oUnidad->getId()."&seguimiento=".$oSeguimiento->getId();

                    //como redirecciona agrego antes un dialog con mensaje de que guardo correctamente.
                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", "MsgCorrectoBlockI32");
                    $this->getTemplate()->set_var("sMensaje", "La unidad ".$oUnidad->getNombre()." se guardo correctamente.<br>Será redireccionado al listado con todas las fechas para la unidad.");
                    
                    $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));                 
                    $this->getJsonHelper()->setValor("esporadica", "1");
                    $this->getJsonHelper()->setRedirect($sRedirect);
                }
            }catch(Exception $e){
                $this->getJsonHelper()->setSuccess(false);
            }

            $this->getJsonHelper()->sendJsonAjaxResponse();
        }
    }

    private function guardarEvolucion($oEntrada, $oSeguimiento)
    {
        $iObjetivoId = $this->getRequest()->getPost("objetivoIdForm");
        $sComentarios = $this->getRequest()->getPost("comentarios");
        $iProgreso = $this->getRequest()->getPost("progreso");

        if(empty($iProgreso) OR empty($sComentarios) OR empty($iObjetivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            if($this->getRequest()->has('editarProgreso')){
                $iEvolucionId = $this->getRequest()->getPost("evolucionIdForm");

                $oEvolucion = SeguimientosController::getInstance()->getEvolucionById($iEvolucionId);
                $oEvolucion->setProgreso($iProgreso);
                $oEvolucion->setComentarios($sComentarios);
                SeguimientosController::getInstance()->actualizarEvolucion($oEvolucion);
            }

            if($this->getRequest()->has('crearEvolucion')){
                //el objetivo se necesita para guardar la evolucion
                if($oSeguimiento->isSeguimientoPersonalizado()){
                    $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
                }
                if($oSeguimiento->isSeguimientoSCC()){
                    $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeById($iObjetivoId);
                    $oObjetivo->setSeguimientoSCCId($oSeguimiento->getId());
                }

                $oEvolucion = new stdClass();
                $oEvolucion->oEntrada = $oEntrada;
                $oEvolucion = Factory::getEvolucionInstance($oEvolucion);
                $oEvolucion->setProgreso($iProgreso);
                $oEvolucion->setComentarios($sComentarios);

                $oObjetivo->setEvolucion(null);
                $oObjetivo->addEvolucion($oEvolucion);

                SeguimientosController::getInstance()->guardarEvolucionObjetivo($oObjetivo);
            }

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "evolucion", "ProgresoEvolucionBlock");
            $this->getTemplate()->set_var("CrearEvolucionBlock", "");
            if($oEvolucion->isObjetivoLogrado()){
                $this->getTemplate()->set_var("sGoalClass", "goal");
            }
            $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());
            $this->getTemplate()->set_var("iEvolucion", $iProgreso);
            $this->getTemplate()->set_var("iObjetivoId", $iObjetivoId);
            $this->getTemplate()->set_var("iEvolucionId", $oEvolucion->getId());
                        
            $this->getJsonHelper()->setValor("objetivoId", $iObjetivoId);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('evolucion', false));
            $this->getJsonHelper()->setSuccess(true);            
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Listar entradas de una unidad esporadica
     */
    public function entradasUnidadEsporadica()
    {
        $iUnidadId = $this->getRequest()->getParam("unidad");
        $iSeguimientoId = $this->getRequest()->getParam("seguimiento");
        
        if(null === $iUnidadId || null === $iSeguimientoId){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oUnidad = SeguimientosController::getInstance()->getUnidadById($iUnidadId);
        if($oUnidad === null || !$oUnidad->isTipoEdicionEsporadica()){
            throw new Exception("No existe la unidad para el identificador", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        if($oSeguimiento === null){
            throw new Exception("No existe el seguimiento para el identificador", 401);
        }
               
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if(!$oUnidad->isPrecargada()){
            if($oUnidad->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para ver esta unidad", 401);
            }
        }
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para editar esta entrada", 401);
        }

        //ultima comprobacion
        if(!SeguimientosController::getInstance()->isUnidadAsociadaSeguimiento($iUnidadId, $iSeguimientoId)){
            throw new Exception("La unidad no esta asociada al seguimiento", 401);
        }
                
        try{
            $this->setFrameTemplateB()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, null);
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            $this->getTemplate()->set_var("iSeguimientoId", "$iSeguimientoId");

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Unidad Esporádica");
            $this->getTemplate()->set_var("subtituloSeccion", "Unidad: <span class='fost_it'>".$oUnidad->getNombre()."</span>");
            
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageRightInnerMainCont", "ListadoEntradasPorUnidadEsporadica");

            $this->getTemplate()->set_var("sUnidadDescripcion", $oUnidad->getDescripcion(true));
            $this->getTemplate()->set_var("iSeguimientoIdForm", $oSeguimiento->getId());
            $this->getTemplate()->set_var("iUnidadIdForm", $oUnidad->getId());

            $aEntradas = SeguimientosController::getInstance()->getEntradasSeguimientoByUnidadId($iSeguimientoId, $oUnidad->getId());
            $this->getTemplate()->set_var("iRecordsTotal", count($aEntradas));
            if(count($aEntradas) == 0){
                $this->getTemplate()->set_var("EntradaBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "La unidad no tiene entradas");
            }else{
                $this->getTemplate()->set_var("NoRecordsGrillaEntradasBlock", "");
                $first = true;
                foreach($aEntradas as $oEntrada){
                    //primer fecha es la ultima entrada, info que necesito para el form de nueva entrada.
                    if($first){
                        $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));
                        $sUltimaEntrada = str_replace("-", "/", $oEntrada->getFecha());
                        $this->getTemplate()->set_var("sUltimaEntrada", $sUltimaEntrada);
                        $first = false;
                    }

                    $this->getTemplate()->set_var("sFecha", $oEntrada->getFecha(true));
                    $this->getTemplate()->set_var("iEntradaId", $oEntrada->getId());

                    if($oEntrada->isEditable()){
                        $hrefEditar = $this->getUrlFromRoute("seguimientosEntradasEditar", true)."?entrada=".$oEntrada->getId();
                        $this->getTemplate()->set_var("hrefEditarEntrada", $hrefEditar);
                    }else{
                        if($oEntrada->isGuardada()){
                            $this->getTemplate()->set_var("EliminarEsporadicaButton", "");
                        }
                        $this->getTemplate()->set_var("EditarEsporadicaButton", "");
                    }
                                                                                                 
                    //Esto se hace asi porque los valores de las variables se obtienen desde la llamada de la entrada
                    $aUnidades = $oEntrada->getUnidades();
                    $oUnidad = $aUnidades[0];
                    $aVariables = $oUnidad->getVariables();
                    if(count($aVariables) == 0){
                        continue;
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

                    $this->getTemplate()->parse("EntradaBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("VariableBlock");
                    $this->getTemplate()->delete_parsed_blocks("EditarEsporadicaButton");
                    $this->getTemplate()->delete_parsed_blocks("EliminarEsporadicaButton");
                }
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
}