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

    private function setContenidoColumnaIzquierda($oDiscapacitado)
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageBodyLeftCont", "PageBodyLeftContBlock");

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
            $this->setContenidoColumnaIzquierda($oSeguimiento->getDiscapacitado());
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageBodyCenterCont", "AmpliarEntradaBlock");

            $oEntrada = $oSeguimiento->getUltimaEntrada();

            //Si ultima entrada es null entonces no hay entradas en el seguimiento
            if($oEntrada === null){
                $this->getTemplate()->unset_blocks("TituloBlock");
                $this->getTemplate()->unset_blocks("MenuEntradaBlock");
                
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTopEntrada", "MsgFichaHintBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Seguimiento sin entradas.");
                $this->getTemplate()->set_var("sMsgFicha", "Este seguimiento todavía no posee entradas. Para crear una seleccione una fecha desde el calendario y luego elija 'Crear nueva entrada'.");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }

            
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }           
}