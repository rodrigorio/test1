<?php


abstract class HelperAbstract
{
    /**
     * @var mixed $frontController
     */
    protected $frontController = null;

    /**
     * Retrieve front controller instance
     */
    public function getFrontController()
    {
        return FrontController::getInstance();
    }

    /**
     * getRequest()
     */
    public function getRequest()
    {
        $controller = $this->getFrontController();
        return $controller->getRequest();
    }

    /**
     * getResponse()
     */
    public function getResponse()
    {
        $controller = $this->getFrontController();
        return $controller->getResponse();
    }
}