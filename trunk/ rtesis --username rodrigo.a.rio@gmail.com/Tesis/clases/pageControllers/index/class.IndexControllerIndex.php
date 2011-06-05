<?php

/**
 * 	Action Controller Index
 */
class IndexControllerIndex extends PageControllerAbstract
{
    /**
     * Muestra pagina de sitio en construccion
     */
    public function sitioEnConstruccion()
    {
        $this->getTemplate()->load_file("gui/templates/frameBlog02-01.gui.html", "frame");
        
        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaCentralContent", "MsgInfoBlockI32");
        $this->getTemplate()->set_var("sMensaje", "El sitio se encuentra en construccion.");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "columnaCentralContent", "ImagenSitioEnConstruccionBlock", true);
        $this->getTemplate()->set_var("srcSitioEnConstruccion", 'gui/images/banners-logos/bajo_construccion.jpg');
        $this->getTemplate()->set_var("widthSitioEnConstruccion", "580");
        $this->getTemplate()->set_var("heightSitioEnConstruccion", "300");
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function sitioOffline()
    {
        $this->getTemplate()->load_file("gui/templates/frameBlog02-01.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-offline.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaCentralContent", "MsgInfoBlockI32");
        $this->getTemplate()->set_var("sMensaje", "El sitio se encuentra fuera de línea por el momento.");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "columnaCentralContent", "ImagenSitioEnConstruccionBlock", true);
        $this->getTemplate()->set_var("srcSitioEnConstruccion", 'gui/images/banners-logos/fuera_de_linea.jpg');
        $this->getTemplate()->set_var("widthSitioEnConstruccion", "580");
        $this->getTemplate()->set_var("heightSitioEnConstruccion", "300");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function ajaxError()
    {
        $this->getResponse()->setBody("<br>entro ajax error<br>");
    }
}