<?php

/**
 *  Action Controller Publicaciones
 */
class IndexControllerAdmin extends PageControllerAbstract
{
    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/admin/frame01-02.gui.html", "frame");
        return $this;
    }

    private function setHeadTemplate()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');

        $this->getTemplate()->load_file_section("gui/vistas/admin/home.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);
        return $this;
    }

    private function setMenuTemplate()
    {
        return $this;
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate();

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
         }catch(Exception $e){
            print_r($e);
        }
    }
}