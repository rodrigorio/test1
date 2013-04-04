<?php

/**
 * @author Matias Velilla
 *
 */
class UnidadesControllerSeguimientos extends PageControllerAbstract
{
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

        $this->getTemplate()->load_file_section("gui/vistas/admin/unidades.gui.html", "jsContent", "JsContent");

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

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setMenuDerecha()
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
                    $this->getTemplate()->set_var("sNombreVariable", $oUnidad->getNombre());
                    $this->getTemplate()->set_var("sDescripcionVariable", $oUnidad->getDescripcion(true));

                    //lo hago asi porque sino es re pesado obtener todas las variables, etc. solo para saber cantidad
                    list($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados) = SeguimientosController::getInstance()->obtenerMetadatosUnidad($oUnidad->getId());
                    $this->getTemplate()->set_var("iCantidadVariables", $iCantidadVariablesAsociadas);
                    $this->getTemplate()->set_var("iCantidadSeguimientos", $iCantidadSeguimientosAsociados);
                    
                    $this->getTemplate()->set_var("hrefListarVariablesUnidad", $this->getUrlFromRoute("seguimientosVariablesIndex", true)."?id=".$oUnidad->getId());
                }
            }else{
                $this->getTemplate()->set_var("UnidadBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            throw $e;
        }
    }

    
}