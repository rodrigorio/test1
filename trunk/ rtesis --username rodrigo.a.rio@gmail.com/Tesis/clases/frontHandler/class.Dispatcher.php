<?php

class Dispatcher
{
    private $frontController;

    /**
     * Array of invocation parameters to use when instantiating action controllers
     *
     * @var array
     */
    private $invokeParams = array();

    /**
     * Response object to pass to action controllers, if any
     * @var Response|null
     */
    protected $response = null;
		
    /**
     * Current module (formatted)
     * @var string
     */
    private $curModule;	
	
    /**
     * Constructor
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }
	
    /**
     * Formats a string into a controller name.  This is used to take a raw
     * controller name, such as one stored inside a Zend_Controller_Request_Abstract
     * object, and reformat it to a proper class name that a class extending
     * Zend_Controller_Action would use.
     *
     * Todas estas funciones se simplifican mucho con las rutas RegEx sino hay que formatear el pedazo de url.
     */
    public function formatControllerName($unformatted)
    {
        return ucfirst($unformatted).'Controller';
    }	

    /**
     * Retrieve front controller instance    
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * Set front controller instance
     */
    public function setFrontController(FrontController $controller)
    {
        $this->frontController = $controller;
        return $this;
    }
	
    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     *
     * @param array $params
     * @return Zend_Controller_Dispatcher_Abstract
     */
    public function setParams(array $params)
    {
        $this->invokeParams = array_merge($this->invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
     *
     * @param string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if(isset($this->invokeParams[$name])) {
            return $this->invokeParams[$name];
        }

        return null;
    }

    /**
     * Retrieve action controller instantiation parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->invokeParams;
    }	
	
    /**
     * Clear the controller parameter stack
     *
     * By default, clears all parameters. If a parameter name is given, clears
     * only that parameter; if an array of parameter names is provided, clears
     * each.
     */
    public function clearParams($name = null)
    {
        if (null === $name) {
            $this->invokeParams = array();
        } elseif (is_string($name) && isset($this->invokeParams[$name])) {
            unset($this->invokeParams[$name]);
        } elseif (is_array($name)) {
            foreach ($name as $key) {
                if (is_string($key) && isset($this->invokeParams[$key])) {
                    unset($this->invokeParams[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Set response object to pass to action controllers
     *
     * @param Response|null $response
     * @return Dispatcher
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Return the registered response object
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
			
    /**
     * Format the module name.
     */
    public function formatModuleName($unformatted)
    {
        return ucfirst($unformatted);
    }		
	
    /**
     * Format action class name
     */
    public function formatClassName($moduleName, $className)
    {
        return $className.$this->formatModuleName($moduleName);
    }

    /**
     * Convert a class name to a filename
     */
    public function classToFilename($class)
    {
        return $class.'.php';
    }	
			
    /**
     * Returns TRUE if the Request object can be dispatched to a controller.
     */
    public function isDispatchable(HttpRequest $request)
    {			
        //en el get del controller setea cual es el modulo actual para ese controlador (curModule)
        $className = $this->getControllerClass($request);
        if (!$className) {
            return false;
        }

        $finalClass = $className;		
        $finalClass = $this->formatClassName($this->curModule, $className);

        //internamente llama al autoload
        return class_exists($finalClass);
    }	

    /**
     * Dispatch to a controller/action
     */
    public function dispatch(HttpRequest $request, Response $response)
    {
        $this->setResponse($response);
        
        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)){
            throw new Exception('Invalid controller specified (' . $request->getControllerName() . ')');
        } else {
            $className = $this->getControllerClass($request);
        }		
		
        //originalmente hacia el 'include' como nosotros usamos include_path y autoload solo preparamos bien el string
        $className = $this->loadClass($className);

        /**
         * Instantiate controller with request and invocation arguments.
         * Esta bien que se pasen los parametros al controlador por el constructor en vez de a traves del metodo.
         * Por si mas de un metodo necesita saber con que parametros se llamo al controlador (metodos privados, etc)
         *
         * Los parametros que extrae el router quedan en $request y se acceden a traves de los metodos de HttpRequest
         */
        $controller = new $className($request, $this->getResponse(), $this->getParams());
        if (!($controller instanceof PageControllerInterface)){
            throw new Exception('Controller "' . $className . '" is not an instance of PageControllerInterface');
        }

        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);

        try {
            $controller->dispatch($action);
        }catch(Exception $e){ //puede tener un error 404 disparado por el action. porque no encontro id, etc.
            throw $e;
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }

    /**
     * Load a controller class (el new se hace en el dispatch)
     */
    public function loadClass($className)
    {	
        $finalClass = $className;
        $finalClass = $this->formatClassName($this->curModule, $className);

        //evita el autoload con el false
        if (class_exists($finalClass)){ 
            return $finalClass;
        } else {
            throw new Exception('Cannot load controller class');
        }
		
        return $finalClass;
    }

    /**
     * Get controller class name
     *
     * Try request first; if not found, try pulling from request parameter;
     * if still not found, fallback to default
     *
     * @return string|false Returns class name on success
     */
    public function getControllerClass(HttpRequest $request)
    {
        $controllerName = $request->getControllerName();
		
        if (empty($controllerName)) {
            return false;
        }

        $className = $this->formatControllerName($controllerName);
		
        $module = $request->getModuleName();		
        $this->curModule    = $module;
		
        //simplifique un poco la funcion porque no vamos a usar valores por defecto, el router siempre devuelve todo
        if(empty($this->curModule)){
            throw new Exception('El objeto $request no tiene seteado el modulo despues de router->route()');
        }

        return $className;
    }
	
    /**
     * Return the value of the currently selected dispatch directory (as set by {@link getController()})
     */
    public function getDispatchDirectory()
    {
        return $this->curDirectory;
    }	

    /**
     * Determine the action name
     *
     * First attempt to retrieve from request
     *
     * Returns formatted action name
     *
     * @return string
     */
    public function getActionMethod(HttpRequest $request)
    {
        return $request->getActionName();
    }		
}