<?php
/**
 *
 *
 */
class Router
{
    private $frontController;

    /**
     * Array of invocation parameters to use when instantiating action controllers
     */
    private $invokeParams = array();

    /**
     * Whether or not to use default routes
     *
     * @var boolean
     */
    private $useDefaultRoutes = false;

    /**
     * Array of routes to match against
     *
     * @var array
     */
    private $routes = array();

    /**
     * Currently matched route
     *
     * @var Zend_Controller_Router_Route_Interface
     */
    private $currentRoute = null;

    /**
     * Global parameters given to all routes. No lo usamos
     *
     * @var array
     */
    private $globalParams = array();

    /**
     * Determines if request parameters should be used as global parameters
     * inside this router. No lo usamos
     *
     * @var boolean
     */
    private $useCurrentParamsAsGlobal = false;

    /**
     * Constructor
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }

    /**
     * Add or modify a parameter to use when instantiating an action controller
     */
    public function setParam($name, $value)
    {
        $name = (string) $name;
        $this->invokeParams[$name] = $value;
        return $this;
    }

    /**
     * Set parameters to pass to action controller constructors
     */
    public function setParams(array $params)
    {
        $this->invokeParams = array_merge($this->invokeParams, $params);
        return $this;
    }

    /**
     * Retrieve a single parameter from the controller parameter stack
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
     * Retrieve Front Controller
     */
    public function getFrontController()
    {
        // Used cache version if found
        if (null !== $this->frontController) {
            return $this-> frontController;
        }

        $this->frontController = FrontController::getInstance();
        return $this->frontController;
    }

    /**
     * Set Front Controller
     */
    public function setFrontController(FrontController $controller)
    {
        $this->frontController = $controller;
        return $this;
    }

    /**
     * Add route to the route chain
	 * Los nombres de las rutas no se pueden repetir y son los que van a ir dentro de la DB
	 * El nombre de una ruta no necesariamente tiene que corresponder al nombre del metodo Action dentro del PageController
     */
    public function addRoute($name, RegexRoute $route)
    {
        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * Add routes to the route chain
     */
    public function addRoutes($routes) {
        foreach ($routes as $name => $route) {
            $this->addRoute($name, $route);
        }
        return $this;
    }

    /**
     * Remove a route from the route chain
     *
     */
    public function removeRoute($name)
    {
        if (!isset($this->routes[$name])) {
            throw new Exception("Route $name is not defined");
        }
        unset($this->routes[$name]);

        return $this;
    }

    /**
     * Check if named route exists
     */
    public function hasRoute($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * Retrieve a named route
     */
    public function getRoute($name)
    {
        if (!isset($this->routes[$name])) {
            throw new Exception("Route $name is not defined");
        }
        return $this->routes[$name];
    }

    /**
     * Retrieve a currently matched route
     */
    public function getCurrentRoute()
    {
        if (!isset($this->currentRoute)) {
            throw new Exception("Current route is not defined");
        }
        return $this->getRoute($this->currentRoute);
    }

    /**
     * Retrieve a name of currently matched route
     */
    public function getCurrentRouteName()
    {
        if (!isset($this->currentRoute)) {
            throw new Exception("Current route is not defined");
        }
        return $this->currentRoute;
    }

    /**
     * Retrieve an array of routes added to the route chain
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Find a matching route to the current PATH_INFO and inject
     * returning values to the Request object.
     */
    public function route(Request $request)
    {
        if (!$request instanceof Request) {
            throw new Exception('requires a Request object');
        }

        // Find the matching route
        $routeMatched = false;
        $match = $request->getPathInfo();
        foreach (array_reverse($this->routes, true) as $name => $route) {
            if ($params = $route->match($match)){
                $this->setRequestParams($request, $params);
                $this->currentRoute = $name;
                $routeMatched       = true;
                break;
            }
        }

        if (!$routeMatched) {
            throw new Exception("La pagina no existe", 404);
        }

        return $request;
    }

    protected function setRequestParams($request, $params)
    {
        foreach ($params as $param => $value){
            $request->setParam($param, $value);

            if ($param === $request->getModuleKey()) {
                $request->setModuleName($value);
            }
            if ($param === $request->getControllerKey()) {
                $request->setControllerName($value);
            }
            if ($param === $request->getActionKey()) {
                $request->setActionName($value);
            }
        }
    }
}
