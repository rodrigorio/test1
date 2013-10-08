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

            $oEntrada = $oSeguimiento->getUltimaEntrada();

            //Si ultima entrada es null entonces no hay entradas en el seguimiento
            if($oEntrada === null){
                $this->getTemplate()->unset_blocks("TituloBlock");
                $this->getTemplate()->unset_blocks("MenuEntradaBlock");
                
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTopEntrada", "MsgFichaHintBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Seguimiento sin entradas.");
                $this->getTemplate()->set_var("sMsgFicha", "Este seguimiento todavía no posee entradas. Para crear una seleccione una fecha desde el calendario y luego elija 'Crear nueva entrada'.");

                $this->getTemplate()->unset_blocks("EntradaContBlock");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }

            $this->getTemplate()->set_var("dFechaEntrada", $oEntrada->getFecha(true));

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
                    $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha());
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

                    $this->getTemplate()->set_var("ContenidosEjeBlock", "");
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
                    $this->getTemplate()->set_var("sContenidosEje", $oObjetivo->getEje()->getContenidos(true));
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
                }else{                                   
                    $this->getTemplate()->set_var("iEvolucion", $oEvolucion->getProgreso());
                    if($oEvolucion->isObjetivoLogrado()){
                        $this->getTemplate()->set_var("sGoalClass", "goal");
                    }else{
                        $this->getTemplate()->set_var("sGoalClass", "");
                    }
                }

                $this->getTemplate()->set_var("dFechaCreacion", $oObjetivo->getFechaCreacion(true));
                
                $this->getTemplate()->parse("ObjetivoBlock", true);

                $this->getTemplate()->delete_parsed_blocks("EstimacionBlock");
                $this->getTemplate()->delete_parsed_blocks("FechaLogradoBlock");
                $this->getTemplate()->delete_parsed_blocks("ContenidosEjeBlock");
            }

            $aUnidades = $oEntrada->getUnidades();
            foreach($aUnidades as $oUnidad)
            {
                $this->getTemplate()->set_var("sNombreUnidad", $oUnidad->getNombre());
                $this->getTemplate()->set_var("iUnidadId", $oUnidad->getId());
                
                $aVariables = $oUnidad->getVariables();
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
                        if(empty($valor)){ $valor = " - "; }
                        $this->getTemplate()->set_var("sVariableValorTexto", $valor);
                    }

                    if($oVariable->isVariableCualitativa()){
                        $variable = "VariableCualitativa";
                        $valor = $oVariable->getValor();
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
}