<?php

/**
 * @author Matias Velilla
 *
 */
class SeguimientosControllerSeguimientos extends PageControllerAbstract
{
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "jsContent", "JsContent");

        return $this;
    }
    
    public function index(){
        $this->listar();
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");

            //contenido ppal home seguimientos
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");
            $this->getTemplate()->set_var("hrefAgregarPersona", "seguimientos/agregar-persona");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }        
    }

    public function nuevoSeguimiento(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");

            //contenido ppal home comunidad
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioBlock");
            $listaTiposSeguimiento = array();
            $obj = new stdClass();
            $oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $listaTiposSeguimiento = $oTipoSeg->getLista();
            foreach ($listaTiposSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposNombre", $value);
                $this->getTemplate()->parse("ListaTipoDeSeguimientosBlock", true);
            }
            $oTipoPractica = Factory::getTipoPracticasSeguimientoInstance($obj);
            $listaTiposPracticaSeguimiento = $oTipoPractica->getLista();
            foreach ($listaTiposPracticaSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposPracticaId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposPracticaNombre", $value);
                $this->getTemplate()->parse("ListaTipoDePracticaSeguimientosBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesarSeguimiento(){
        try{
            $iTipoSeguimiento = $this->getRequest()->getPost('tipoSeguimiento');
            $iPersona       = $this->getRequest()->getPost('persona');
            $sFrecuencias   = $this->getRequest()->getPost('frecuencias');
            $sDiaHorario    = $this->getRequest()->getPost('diaHorario');
            $iTipoPractica  = $this->getRequest()->getPost('tipoPractica');
            $obj = new stdClass();
            $oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $sTipoSeguimiento = $oTipoSeg->getTipoById($iTipoSeguimiento);
            if($sTipoSeguimiento == "SCC" ){

            }elseif( $sTipoSeguimiento == "PERSONALIZADO"){

            }
         }catch(Exception $e){
            print_r($e);
        }
    }
}