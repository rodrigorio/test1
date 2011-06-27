<?php

/**
 * Page Controller para las vistas basicas del modulo comunidad.
 */
class IndexControllerComunidad extends PageControllerAbstract
{
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-02.gui.html", "frame");
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

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);
        return $this;
    }

    private function setMenuTemplate()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "topHeaderMenuLeft", "TopHeaderMenuLeftBlock");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "topHeaderMenuRight", "TopHeaderMenuRightBlock");

        /*
        //Opcion1
        $this->getTemplate()->set_var("idOpcion", 'menuPpalInicio');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
        $this->getTemplate()->set_var("sNombreOpcion", "Inicio");
        $this->getTemplate()->parse("OpcionesMenu", true);
        //Opcion3
        $this->getTemplate()->set_var("idOpcion", 'menuPpalAcceder');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/login');
        $this->getTemplate()->set_var("sNombreOpcion", "Acceder");
        $this->getTemplate()->parse("OpcionesMenu", true);

        //borro el submenu que todavia no se usa
        $this->getTemplate()->set_var("SubMenu", "");
        */
        
        $this->getTemplate()->parse("topHeaderMenuLeft", false);
        $this->getTemplate()->parse("topHeaderMenuRight", false);
        return $this;
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate()
                 ->setMenuTemplate();

            /*
            $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
            $this->getTemplate()->set_var("hrefLogoHeader", "http://www.ufasta.edu.ar");
            $this->getTemplate()->set_var("tituloHeader", "SGPAPD");
            $this->getTemplate()->set_var("subtituloHeader", "Sistema de gestión del proceso de aprendizaje en personas discapacitadas");

            //nombre seccion
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "topPageContent", "TituloSeccionBlock");
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Inicio");

            //contenido home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "centerPageContent", "HomeCenterPageBlock");

            //footer home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "footerContent", "HomeFooterBlock");

            //Limpio las opciones porque ya hay otros menues.
            $this->getTemplate()->set_var("OpcionesMenu", "");
            $this->getTemplate()->set_var("OpcionMenuLastOpt", "");

            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "footerSubContent", "MenuHorizontal04Block");
            $this->getTemplate()->set_var("idOpcion", 'footerSubInicio');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Inicio");
            $this->getTemplate()->parse("OpcionesMenu", true);

            $this->getTemplate()->set_var("idOpcion", 'footerSubAnterior');
            $this->getTemplate()->set_var("hrefOpcion", "javascript:history.go(-1)");
            $this->getTemplate()->set_var("sNombreOpcion", "Página anterior");
            $this->getTemplate()->parse("OpcionMenuLastOpt");

            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "footerSubCopyright", "HomeCopyrightBlock");
            */

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

            
         }catch(Exception $e){
            print_r($e);
        }
    }    
}