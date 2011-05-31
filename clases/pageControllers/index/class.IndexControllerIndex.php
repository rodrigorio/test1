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
        $this->getTemplate()->load_file_section("gui/vistas/sitio-en-construccion.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file("gui/templates/frameBlog02-01.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html",
                                                "columnaCentralContent",
                                                "MsgInfoBlockI32");

        $this->getTemplate()->set_var("sMensaje", "El sitio se encuentra en construccion.");
        $this->getTemplate()->pparse('frame', false);
    }

    public function sitioOffline()
    {
        echo "Sitio fuera de linea <br><br>";
    }

    public function ajaxError()
    {
        echo "<br>entro ajax error<br>";
    }
}
?>