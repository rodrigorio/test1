<?php

/**
 * @author Matias Velilla
 *
 */
class InformesControllerSeguimientos extends PageControllerAbstract
{
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-03.gui.html", "frame");
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setTituloSeccion($sTitulo)
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "iconoBlock", "IconoHeaderBlock");
        $this->getTemplate()->set_var("sTituloSeccion", $sTitulo);
    }

    public function index(){
        $this->configuracion();
    }

    public function configuracion(){
        $this->setFrameTemplate()
             ->setHeadTag();

        IndexControllerSeguimientos::setCabecera($this->getTemplate());
        IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
        $this->printMsgTop();

        $this->setTituloSeccion("Configuracion de Informes");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "pageBodyCont", "ConfigurarInformesBlock");

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        $oInformeConfiguracion = SeguimientosController::getInstance()->getConfiguracionInformeByUsuarioId($iUsuarioId);

        $this->getTemplate()->set_var("sTitulo", $oInformeConfiguracion->getTitulo());
        $this->getTemplate()->set_var("sSubtitulo", $oInformeConfiguracion->getSubTitulo());
        $this->getTemplate()->set_var("sPie", $oInformeConfiguracion->getPie());

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "vistaPreliminar", "VistaPreliminarBlock");

        // porque llevan el salto de linea
        $this->getTemplate()->set_var("sTituloPreliminar", $oInformeConfiguracion->getTitulo(true));
        $this->getTemplate()->set_var("sSubtituloPreliminar", $oInformeConfiguracion->getSubTitulo(true));
        $this->getTemplate()->set_var("sPiePreliminar", $oInformeConfiguracion->getPie(true));

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function guardarConfiguracion()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            $oInformeConfiguracion = SeguimientosController::getInstance()->getConfiguracionInformeByUsuarioId($iUsuarioId);

            if($this->getRequest()->has("titulo")){
                $oInformeConfiguracion->setTitulo($this->getRequest()->getPost("titulo"));
            }

            if($this->getRequest()->has("subtitulo")){
                $oInformeConfiguracion->setSubtitulo($this->getRequest()->getPost("subtitulo"));
            }

            if($this->getRequest()->has("pie")){
                $oInformeConfiguracion->setPie($this->getRequest()->getPost("pie"));
            }

            SeguimientosController::getInstance()->guardarConfiguracionInformeUsuario($oInformeConfiguracion);

            $this->getJsonHelper()->setMessage("La configuracion para los informes se guardo con éxito.");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function procesar(){
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('refrescarVistaPreliminar')){
            $this->refrescarVistaPreliminar();
            return;
        }
    }

    private function refrescarVistaPreliminar()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        $oInformeConfiguracion = SeguimientosController::getInstance()->getConfiguracionInformeByUsuarioId($iUsuarioId);

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "vistaPreliminarAjax", "VistaPreliminarBlock");

        $this->getTemplate()->set_var("sTituloPreliminar", $oInformeConfiguracion->getTitulo(true));
        $this->getTemplate()->set_var("sSubtituloPreliminar", $oInformeConfiguracion->getSubTitulo(true));
        $this->getTemplate()->set_var("sPiePreliminar", $oInformeConfiguracion->getPie(true));

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('vistaPreliminarAjax', false));
    }

    public function confeccionar()
    {
        $iSeguimientoId = $this->getRequest()->getParam('id');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }

        try{
            $this->setFrameTemplateB()
                 ->setHeadTag();

            SeguimientosControllerSeguimientos::setMenuDerechaVerSeguimiento($this->getTemplate(), $this);
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            $this->getTemplate()->set_var("tituloSeccion", "Exportar Informe");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "pageRightInnerMainCont", "ConfeccionarInformeBlock");

            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     * @TODO hay que validar lo mismo que para poder realizar entradas x fecha. + que haya al menos una entrada
     *
     */
    public function generar()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoIdForm');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para utilizar este seguimiento", 401);
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            $oInformeConfiguracion = SeguimientosController::getInstance()->getConfiguracionInformeByUsuarioId($iUsuarioId);

            $this->getTemplate()->load_file("gui/templates/index/frameExport01-01.gui.html", "frame");

            $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
            $this->getTemplate()->set_var("titulo", $oInformeConfiguracion->getTitulo(true));
            $this->getTemplate()->set_var("subtitulo", $oInformeConfiguracion->getSubTitulo(true));
            $this->getTemplate()->set_var("pie", $oInformeConfiguracion->getPie(true));

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/informes.gui.html", "bodyContent", "InformeSeguimientoBodyContentBlock");

            $this->getTemplate()->set_var("dFechaHoy", date("d/m/y"));

            $oDiscapacitado = $oSeguimiento->getDiscapacitado();
            $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
            $this->getTemplate()->set_var("sTipoDocumento", $oDiscapacitado->getTipoDocumento());
            $this->getTemplate()->set_var("sNumeroDocumento", $oDiscapacitado->getNumeroDocumento());
            $this->getTemplate()->set_var("sEdad", $oDiscapacitado->getEdad());

            if($this->getRequest()->has('antecedentesCheck')){
                $this->getTemplate()->set_var("sAntecedentes", $oSeguimiento->getAntecedentes(true));
            }else{
                $this->getTemplate()->set_var("AntecedentesBlock", "");
            }

            if($this->getRequest()->has('diagnosticoCheck')){

                $this->getTemplate()->set_var("sDescripcionDiagnostico", $oSeguimiento->getDiagnostico()->getDescripcion(true));

                if($oSeguimiento->isSeguimientoSCC()){
                    $this->getTemplate()->set_var("CodigoDiagnosticoBlock", "");

                    $aEjesTematicos = $oSeguimiento->getDiagnostico()->getEjesTematicos();
                    foreach($aEjesTematicos as $oEjeTematico){
                        $this->getTemplate()->set_var("sArea", $oEjeTematico->getArea()->getDescripcion());
                        $this->getTemplate()->set_var("sEjeTematico", $oEjeTematico->getDescripcion());
                        $this->getTemplate()->set_var("sEstadoInicial", $oEjeTematico->getEstadoInicial());
                        $this->getTemplate()->parse("EstadoInicialRow", true);
                    }
                }

                if($oSeguimiento->isSeguimientoPersonalizado()){
                    $this->getTemplate()->set_var("EstadoInicialBlock", "");

                    $sCodigoDiagnostico = $oSeguimiento->getDiagnostico()->getCodigo();
                    if(!empty($sCodigoDiagnostico)){
                        $this->getTemplate()->set_var("sCodigoDiagnostico", $sCodigoDiagnostico);
                    }else{
                        $this->getTemplate()->set_var("CodigoDiagnosticoBlock", "");
                    }
                }
            }else{
                $this->getTemplate()->set_var("DiagnosticoBlock", "");
                $this->getTemplate()->set_var("CodigoDiagnosticoBlock", "");
                $this->getTemplate()->set_var("EstadoInicialBlock", "");
            }

            if($this->getRequest()->has('objetivosCheck')){

                $aObjetivos = $oSeguimiento->getObjetivos();

                if($oSeguimiento->isSeguimientoSCC()){
                    $this->getTemplate()->set_var("ObjetivoPersonalizadoRowBlock", "");

                    foreach($aObjetivos as $oObjetivo){
                        $this->getTemplate()->set_var("sArea", $oObjetivo->getEje()->getArea()->getDescripcion());
                        $this->getTemplate()->set_var("sEjeTematico", $oObjetivo->getEje()->getDescripcion());
                        $this->getTemplate()->set_var("sDescripcionObjetivo", $oObjetivo->getDescripcion(true));
                        $this->getTemplate()->set_var("sEstimacion", $oObjetivo->getEstimacion(true));
                        $this->getTemplate()->parse("ObjetivoSCCRowBlock", true);
                    }
                }

                if($oSeguimiento->isSeguimientoPersonalizado()){
                    $this->getTemplate()->set_var("ObjetivoSCCRowBlock", "");

                    foreach($aObjetivos as $oObjetivo){
                        $this->getTemplate()->set_var("sEje", $oObjetivo->getEje()->getEjePadre()->getDescripcion()." > ".$oObjetivo->getEje()->getDescripcion());
                        $this->getTemplate()->set_var("sDescripcionObjetivo", $oObjetivo->getDescripcion(true));
                        $this->getTemplate()->set_var("sEstimacion", $oObjetivo->getEstimacion(true));
                        $this->getTemplate()->parse("ObjetivoPersonalizadoRowBlock", true);
                    }
                }

            }else{
                $this->getTemplate()->set_var("ObjetivosBlock", "");
                $this->getTemplate()->set_var("ObjetivoSCCRowBlock", "");
                $this->getTemplate()->set_var("ObjetivoPersonalizadoRowBlock", "");
            }

            if($this->getRequest()->has('entradasCheck')){

                $aEntrada = $oSeguimiento->getEntradas(null, null, "e.fecha", "asc");

                foreach($aEntrada as $oEntrada){

                    $sVariableEntradaBasica = "";
                    $aUnidades = $oEntrada->getUnidades();

                    foreach ($aUnidades as $oUnidad){
                        if(!$oUnidad->isAsociacionAutomatica()){ continue; }

                        $aVariables = $oUnidad->getVariables();
                        $sVariableEntradaBasica = $aVariables[0]->getValor(true);
                        break;
                    }

                    $this->getTemplate()->set_var("sVariableEntradaBasica", $sVariableEntradaBasica);
                    $this->getTemplate()->set_var("sFechaEntrada", $oEntrada->getFecha(true));
                    $this->getTemplate()->parse("EntradaRowBlock", true);
                }

            }else{
                $this->getTemplate()->set_var("EntradasBlock", "");
                $this->getTemplate()->set_var("EntradaRowBlock", "");
            }

            if($this->getRequest()->has('pronosticoCheck')){
                $this->getTemplate()->set_var("sPronostico", $oSeguimiento->getPronostico(true));
            }else{
                $this->getTemplate()->set_var("PronosticoBlock", "");
            }

            $sNotaAdicional = $this->getRequest()->getParam('notaAdicional');
            if(!empty($sNotaAdicional)){
                $this->getTemplate()->set_var("sNotaAdjunta", $sNotaAdicional);
            }else{
                $this->getTemplate()->set_var("NotaAdjuntaBlock", "");
            }

            $aTokens[] = $oSeguimiento->getDiscapacitado()->getNombreCompleto();
            $this->getHtmlToPdfHelper()->generarFileName($aTokens);
            $this->getHtmlToPdfHelper()->agregarPagina($this->getTemplate()->pparse('frame', false));
            $this->getHtmlToPdfHelper()->generar();

        }catch(Exception $e){
            throw($e);
        }
    }
}
