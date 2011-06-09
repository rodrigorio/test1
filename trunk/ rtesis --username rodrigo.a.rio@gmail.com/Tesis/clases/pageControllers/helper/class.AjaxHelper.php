<?php
/**
 *
 * @author Matias Velilla
 */
class AjaxHelper {

    public function isAjaxContext()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    public function setHeaders()
    {
        $this->getResponse()->setRawHeader("Content-Type: text/html");
        $this->getResponse()->setRawHeader("Cache-Control: no-cache, must-revalidate");
        $this->getResponse()->setRawHeader("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        $this->getResponse()->setRawHeader("Pragma: no-cache");
    }
    
}
?>