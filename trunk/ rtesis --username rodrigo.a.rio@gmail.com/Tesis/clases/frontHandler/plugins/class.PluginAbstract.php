<?php

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class PluginAbstract
{
    /**
     *	Notar que los plugins no llevan el array de parametros.
     *	El array de params lo lleva el Front controller (para que sean seteados parametros desde el index.php)
     *	y despues se propagan a traves de $request, $router, $dispatcher y los page controllers (a traves de dispatch() )
     */
    protected $request;
	
    /**
     * @var Response
     */
    protected $response;

    /**
     * Set request object
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_Controller_Plugin_Abstract
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request object
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set response object
     *
     * @param Response
     * @return PluginAbstract
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get response object
     *
     * @return Response $response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /*
    SAQUE LOS TRIGGERS QUE NO VAMOS A USAR, EN CUALQUIERA DE ESTOS EVENTOS SE PUEDEN PASAR PARAMETROS AL OBJETO $request.
    ACORDARSE QUE ESE ARRAY ASOCIATIVO DE PARAMETROS EN ULTIMA VA A TERMINAR LLEGANDO AL EVENTO DEL PAGE CONTROLLER
    (ADEMAS DE LOS GET Y POST)
    LA CADENA ES:
            - PLUGIN RESETEA MODULE, CONTROL, ACTION Y AGREGA PARAMS (setea los parametros sin necesidad de un router)
            - DISPATCHER REALIZA LA NUEVA ACCION Y PASA LOS PARAMETROS SETEADOS EN REQUEST POR EL PLUGIN AL EVENTO DEL PAGE CONTROLLER
    */

    /**
     * Called before FrontController begins evaluating the
     * request against its routes.
     *
     * @param HttpRequest $request
     * @return void
     */
    public function routeStartup(HttpRequest $request)
    {}

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior.  By altering the
     * request and resetting its dispatched flag (via
     * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * the current action may be skipped.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(HttpRequest $request)
    {}

    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     *
     * This callback allows for proxy or filter behavior. By altering the
     * request and resetting its dispatched flag 
     * (via {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
     * a new action may be specified for dispatching.
     *
     *
     * @return void
     */
    public function postDispatch(HttpRequest $request)
    {}

    /**
     * Called before FrontController exits its dispatch loop.
     *
     * @return void
     */
    public function dispatchLoopShutdown()
    {}
}