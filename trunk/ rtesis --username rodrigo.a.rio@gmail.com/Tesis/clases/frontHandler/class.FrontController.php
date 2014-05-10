<?php

/**
 * Front controller debera encargarse de todas las funciones que deben estar concentradas en un solo punto.
 * - Autorizacion para ejecutar las acciones en el sistema
 * - Detectar informacion malintencionada en POST y GET
 * - Manejar el cache de las paginas (memcache)
 * - Permite suspender el sistema en caso de mantenimiento
 */
class FrontController
{
    private $baseUrl = null;

    /**
     * Array of invocation parameters to use when instantiating action
     */
    private $invokeParams = array();

    private $dispatcher = null;

    /**
     * Instance of PluginBroker
     */
    private $plugins = null;

    private $request = null;

    private $router = null;

    private $response = null;

    /**
     * Whether or not to return the response prior to rendering output while in
     * {@link dispatch()}; default is to send headers and render output.
     * @var boolean
     */
    protected $returnResponse = false;

    /**
     * Whether or not exceptions encountered in {@link dispatch()} should be
     * thrown or trapped in the response object
     */
    private $throwExceptions = false;

    private static $instance = null;

    /**
     * Constructor
     *
     * Instantiate using {@link getInstance()}; front controller is a singleton
     * object.
     *
     * Instantiates the plugin broker.
     *
     * @return void
     */
    private function __construct()
    {
        $this->plugins = new PluginBroker();
    }

    /**
     * Singleton instance
     *
     * @return FrontController
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Set request class/object
     *
     * Set the request object.  The request holds the request environment.
     */
    public function setRequest($request)
    {
        if (!$request instanceof Request) {
            throw new Exception('Invalid request class');
        }
        $this->request = $request;
	return $this;
    }

    /**
     * Return the request object.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set router class/object
     *
     * Set the router object.  The router is responsible for mapping
     * the request to a controller and action.
     *
     */
    public function setRouter($router)
    {
        if (!$router instanceof Router) {
            throw new Exception('Invalid router class');
        }
        $router->setFrontController($this);
        $this->router = $router;
	return $this;
    }

    /**
     * Return the router object.
     *
     * Instantiates a Router object if no router currently set.
     */
    public function getRouter()
    {
        if (null == $this->router) {
            $this->setRouter(new Router());
        }
        return $this->router;
    }

    /**
     * Set the dispatcher object.  The dispatcher is responsible for
     * instantiating the controller, and
     * call the action method of the controller.
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Return the dispatcher object.
     */
    public function getDispatcher()
    {
        /**
         * Instantiate the default dispatcher if one was not set.
         */
        if (!$this->dispatcher instanceof Dispatcher) {
            $this->dispatcher = new Dispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * Set response class/object
     *
     * Set the response object.  The response is a container for action
     * responses and headers. Usage is optional.
     *
     * If a class name is provided, instantiates a response object.
     *
     * @param string|Response $response
     * @throws Exception if invalid response class
     * @return FrontController
     */
    public function setResponse($response)
    {
        $response = new $response();
        if (!$response instanceof Response) {
            throw new Exception('Invalid response class');
        }
        $this->response = $response;
        return $this;
    }

    /**
     * Return the response object.
     *
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Limpia excepciones de un codigo o todas si ninguno se provee ver metodo en Response
     */
    public function cleanResponseExceptions($code = "")
    {
        $this->response->cleanExceptions($code);
        return $this;
    }

    /**
     * Set whether {@link dispatch()} should return the response without first
     * rendering output. By default, output is rendered and dispatch() returns
     * nothing.
     *
     * @param boolean $flag
     * @return boolean|FrontController Used as a setter, returns object; as a getter, returns boolean
     */
    public function returnResponse($flag = null)
    {
        if (true === $flag) {
            $this->returnResponse = true;
            return $this;
        } elseif (false === $flag) {
            $this->returnResponse = false;
            return $this;
        }

        return $this->returnResponse;
    }

    /**
     * Set the base URL used for requests
     *
     * Use to set the base URL segment of the REQUEST_URI to use when
     * determining PATH_INFO, etc. Examples:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Note that the URL should not include the full URI. Do not use:
     * - http://example.com/admin
     * - http://example.com/myapp
     * - http://example.com/subdir/index.php
     *
     * If a null value is passed, this can be used as well for autodiscovery (default).
     *
     * @param string $base
     * @return Zend_Controller_Front
     * @throws Zend_Controller_Exception for non-string $base
     */
    public function setBaseUrl($base = null)
    {
        if (!is_string($base) && (null !== $base)) {
            throw new Exception('Rewrite base must be a string');
        }

        $this->baseUrl = $base;

        if((null !== ($request = $this->getRequest())) && (method_exists($request, 'setBaseUrl'))) {
            $request->setBaseUrl($base);
        }

	return $this;
    }

    /**
     * Retrieve the currently set base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $request = $this->getRequest();
        if ((null !== $request) && method_exists($request, 'getBaseUrl')) {
            return $request->getBaseUrl();
        }

        return $this->baseUrl;
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     *
     * @param string $name
     * @param mixed $value
     * @return Zend_Controller_Front
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
     * @return Zend_Controller_Front
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
     * Register a plugin.
     * @param  int $stackIndex Optional; stack index for plugin.
     * (sino se ejecutan en el orden que se setean / mayor velocidad tmb)
     */
    public function registerPlugin(PluginAbstract $plugin, $stackIndex = null)
    {
        $this->plugins->registerPlugin($plugin, $stackIndex);
        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param  $plugin class to unregister
     * @return FrontController
     */
    public function unregisterPlugin($plugin)
    {
        $this->plugins->unregisterPlugin($plugin);
        return $this;
    }

    /**
     * Is a particular plugin registered?
     *
     * @param  string $class
     * @return bool
     */
    public function hasPlugin($class)
    {
        return $this->plugins->hasPlugin($class);
    }

    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class
     * @return false|PluginAbstract|array
     */
    public function getPlugin($class)
    {
        return $this->plugins->getPlugin($class);
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins->getPlugins();
    }

    /**
     * Set the throwExceptions flag and retrieve current status
     *
     * Set whether exceptions encounted in the dispatch loop should be thrown
     * or caught and trapped in the response object.
     *
     * Passing no value will return the current value of the flag; passing a
     * boolean true or false value will set the flag and return the current
     * object instance.
     */
    public function throwExceptions($flag = null)
    {
        if ($flag !== null) {
            $this->throwExceptions = (bool)$flag;
            return $this;
        }
        return $this->throwExceptions;
    }

    /**
     * Dispatch an HTTP request to a controller/action.
     *
     */
    public function dispatch()
    {
	    $request = new Request();
        $this->setRequest($request);

        /**
         * Set base URL of request object, if available
         */
        if(null !== $this->baseUrl){
            $this->request->setBaseUrl($this->baseUrl);
        }

        $response = new Response();
        $this->setResponse($response);

        /**
         * Register request and response objects with plugin broker
         */
        $this->plugins
             ->setRequest($this->request)
             ->setResponse($this->response);

        /**
         * Initialize router
         */
        $router = $this->getRouter();
        $router->setParams($this->getParams());

        /**
         * Initialize dispatcher
         */
        $dispatcher = $this->getDispatcher();
        $dispatcher->setParams($this->getParams())
                   ->setResponse($this->response);

        // Begin dispatch
        try {
            /**
             * Route request to controller/action, if a router is provided
             */

            /**
             * Notify plugins of router startup
             *
             * 1) plugin start sesion
             * 2) plugin start parametros
             * 3) plugin iniciar conexion DB
             * 4) plugin AGREGAR LAS RUTAS REGEX EN $router
             * 5) plugin seguridad (FIJARSE QUE NO HAYA INFO MALIGNA EN $_POST Y $_GET, etc)
             *
             */
            $this->plugins->routeStartup($this->request);

            try {
                $router->route($this->request);
            } catch (Exception $e){ //ACA PUEDE TIRAR CODIGO 404 SI NO ENCUENTRA LA PAGINA
                if ($this->throwExceptions()){
                    throw $e;
                }
                $this->response->setException($e);
            }

            do {
                $this->request->setDispatched(true);

                /**
                 * Notify plugins of dispatch startup
                 *
                 * 1) session
                 * 2) plugin iniciar conexion DB
                 * 3) plugin redireccion404 (metodo preDispatch). setea accion del $request en accion 404.
                 * 4) plugin permisos.	SI NO TIENE PERMISO PARA DESPACHAR MODULO/CONTROL/ACCION (parametros no se toman en cuenta)
                 *			SE RESETEAN LOS PARAMETROS DEL REQUEST Y SE AGREGAN NUEVOS.
                 * 			LOS NUEVOS PARAMS VAN A INDICAR LA NUEVA ACCION EN EL DISPACHER.
                 * 5) plugin redirect x accion desactivada (metodo preDispatch). Pantalla muestra "accion desactivada momentaneamente". Tambien se fija modulo desactivado
                 * 6) plugin CACHE (antes de despachar la accion se fija si se puede levantar desde la cache)
                 * 7) plugin parametros (carga los parametros correspondientes al modulo y controlador de $request).
                 *
                 * NOTA: Si el request es AJAX el plugin de redireccion tiene que devolver codigo de Excepcion al javascript.
                 */
                $this->plugins->preDispatch($this->request);

                /**
                 * Skip requested action if preDispatch() has reset it
                 */
                if (!$this->request->isDispatched()) {
                    continue;
                }

                /**
                 * Dispatch request
                 */
                try {
                    //siempre que se logre despachar la ruta a una accion lo correcto es isDispatched = true.
                    $dispatcher->dispatch($this->request, $this->response);
                } catch (Exception $e){	//EXCEPCIONES DESDE LAS ACCIONES. Por ej. ID DE UNA PUBLICACION NO EXISTE, O SE SUSPENDIO, ETC.
                    if ($this->throwExceptions()) {
                        throw $e;
                    }
                    $this->response->setException($e);
                }

                /**
                 * Notify plugins of dispatch completion
                 *
                 * 1) Plugin redireccion404 (metodo posDispatch):
                 *    toma la excepcion desde el response del FrontController y se fija si es codigo 404.
                 *    Luego redirecciona a una pagina relacionada (setea dispatched en falso)
                 *    dependiendo los valores en 'modulo' y 'controlador' del $request.
                 *    Por ej. si un id de publicacion no existe, va a una accion especial de 404 con listado de publicaciones.
                 *
                 * 2) Plugin permisos.
                 *    toma la excepcion desde el response y se fija si es codigo 401
                 *    si efectivamente se intento realizar una accion sobre un id o alguna entidad
                 *    sobre la cual no se podia ejecutar la accion redirecciona con un mensaje de advertencia.
                 *    (por ejemplo si un usuario quiere eliminar algo que el no creo)
                 *    Puede tener el permiso para eliminar, pero no para eliminar un id que no le pertenece.
                 *
                 * 3) Levantar parametros dinamicos segun los nuevos valores de request
                 *
                 */
                $this->plugins->postDispatch($this->request);

            }while(!$this->request->isDispatched()); //si action tiro 404 o 401 entonces isDispatched = false y se ejecuta una nueva accion
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
            $this->response->setException($e);
        }

        /**
         * Notify plugins of dispatch loop completion
         */
        try {
            $this->plugins->dispatchLoopShutdown();
        } catch (Exception $e) {
            if ($this->throwExceptions()) {
                throw $e;
            }
        }

        if ($this->returnResponse()) {
            return $this->response;
        }

        //envia headers e imprime el frame (body content)
        $this->response->sendResponse();
    }
}
