<?php

/**
 *
 * @author Matias Velilla
 */
class AjaxHelper extends HelperAbstract
{
    /**
     * Devuelve si el contexto del Request es ajax o no
     * @return boolean
     */
    public function isAjaxContext()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    /**
     * Seteo los headers en Response para peticiones Ajax que devuelven HTML
     */
    public function setHtmlResponseHeaders()
    {
        $this->getResponse()->setRawHeader("Content-Type: text/html")
                            ->setRawHeader("Cache-Control: no-cache, must-revalidate")
                            ->setRawHeader("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT")
                            ->setRawHeader("Pragma: no-cache");
        return $this;
    }

    public function sendHtmlAjaxResponse($bodyContent)
    {
        $this->setHtmlResponseHeaders();
        $this->getResponse()->setBody($bodyContent);
    }
}
