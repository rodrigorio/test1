<?php

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginBroker extends PluginAbstract
{

    /**
     * Array of instance of objects extending PluginAbstract
     *
     * @var array
     */
    private $plugins = array();


    /**
     * Register a plugin.
     *
     * Cuando se registra el plugin se hace el set de $request.
     */
    public function registerPlugin(PluginAbstract $plugin, $stackIndex = null)
    {
        $stackIndex = (int) $stackIndex;

		//si no se setea stack index va a parar a ultima posicion
        if ($stackIndex) {
            $this->plugins[$stackIndex] = $plugin;
        } else {
            $stackIndex = count($this->plugins);
            while (isset($this->plugins[$stackIndex])) {
                ++$stackIndex;
            }
            $this->plugins[$stackIndex] = $plugin;
        }

        $request = $this->getRequest();  //si se setean los plugins en index.php los registra con null.
        if($request){
            $this->plugins[$stackIndex]->setRequest($request);
        }
        $response = $this->getResponse();
        if ($response) {
            $this->plugins[$stackIndex]->setResponse($response);
        }		
		
        ksort($this->plugins);

        return $this;
    }

    /**
     * Unregister a plugin.
     */
    public function unregisterPlugin($pluginClass)
    {
        foreach ($this->plugins as $key => $plugin) {
            $type = get_class($plugin);
            if ($pluginClass == $type){
                unset($this->plugins[$key]);
            }
        }
    }

    /**
     * Is a plugin of a particular class registered?
     */
    public function hasPlugin($class)
    {
        foreach ($this->plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                return true;
            }
        }
        return false;
    }
	
    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class Class name of plugin(s) desired
     * @return false|PluginAbstract|array Returns false if none found, plugin if only one found, and array of plugins if multiple plugins of same class found
     */
    public function getPlugin($class)
    {
        $found = array();
        foreach ($this->plugins as $plugin) {
            $type = get_class($plugin);
            if ($class == $type) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
                return $found;
        }
    }	

    /**
     * Set request object, and register with each plugin
     *
     * Se pisa el metodo de la clase abstracta porque setea el request en todos los plugins
     * Este metodo lo dispara el frontController
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        foreach ($this->plugins as $plugin) {
            $plugin->setRequest($request);
        }
        return $this;
    }

    /**
     * Get request object
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set response object
     */
    public function setResponse(Exception $response)
    {
        $this->response = $response;

        foreach ($this->plugins as $plugin) {
            $plugin->setResponse($response);
        }


        return $this;
    }

    /**
     * Get response object
     *
     * @return Exception $response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /*
    EN CUALQUIERA DE ESTOS METODOS SE PUEDE ACCEDER A LOS PARAMETROS DE ACCIONES ANTERIORES A TRAVES DE $request->getParams()
    TAMBIEN SE PUEDE ACCEDER A RESPUESTAS DE ERROR ARROJADAS POR OTROS METODOS A TRAVES DE FrontController::getResponse()
    Eso devolvera la ultima excepcion emitida dentro de FrontController::dispatch()
    Tambien se pueden setear excepciones en el broker
    */

    /**
     * Called before Zend_Controller_Front begins evaluating the
     * request against its routes.
     */
    public function routeStartup(HttpRequest $request)
    {
        foreach ($this->plugins as $plugin) {
            try {
                $plugin->routeStartup($request);
            } catch (Exception $e) {
                if (FrontController::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->response = $e;
                }
            }
        }
    }

    /**
     * Called before an action is dispatched by Zend_Controller_Dispatcher.
     *
     */
    public function preDispatch(HttpRequest $request)
    {
        foreach ($this->plugins as $plugin) {
            try {
                $plugin->preDispatch($request);
            } catch (Exception $e) {
                if (FrontController::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->response = $e;
                }
            }
        }
    }


    /**
     * Called after an action is dispatched by Zend_Controller_Dispatcher.
     */
    public function postDispatch(HttpRequest $request)
    {
        foreach ($this->plugins as $plugin) {
            try {
                $plugin->postDispatch($request);
            } catch (Exception $e) {
                if (FrontController::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->response = $e;
                }
            }
        }
    }

    /**
     * Called before FrontController exits its dispatch loop.
     *
     * Notar que aca ya no hay request. porque ya no hay que modificar nada, ya se proceso.
     */
    public function dispatchLoopShutdown()
    {
       foreach ($this->plugins as $plugin) {
           try {
                $plugin->dispatchLoopShutdown();
            } catch (Exception $e) {
                if (FrontController::getInstance()->throwExceptions()) {
                    throw $e;
                } else {
                    $this->response = $e;
                }
            }
       }
    }
}