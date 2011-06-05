<?php
/**
 *
 * @author Matias Velilla
 */
class AjaxHelper {

    private function getRequest()
    {
        FrontController::getInstance()->getRequest();
    }

    public function isAjaxContext()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    public function setAjaxHeader()
    {
        header("Content-Type: text/html");
        header("Cache-Control: no-cache, must-revalidate");
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        header("Pragma: no-cache");
    }
    
}
?>