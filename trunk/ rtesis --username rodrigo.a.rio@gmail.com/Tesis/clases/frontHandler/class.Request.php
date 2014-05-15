<?php
/*
 * Tiene que devolver: base URL, request URI, path information, $_GET and $_POST parameters, etc.
 * La idea es instanciarla en index y utilizarla siempre que haya que consultar informaci�n a lo largo del sistema.
 */
class Request
{
    /**
     * Scheme for http
     *
     */
    const SCHEME_HTTP  = 'http';

    /**
     * Scheme for https
     *
     */
    const SCHEME_HTTPS = 'https';

    /**
     * Has the action been dispatched?
     * @var boolean
     */
    private $dispatched = false;

    private $module = "";

    private $moduleKey = 'module';

    private $controller = "";

    private $controllerKey = 'controller';

    private $action = "";

    private $actionKey = 'action';


    /**
     * Request parameters
     * @var array
     */
    private $params = array();

    /**
     * Allowed parameter sources
     * @var array
     */
    private $paramSources = array('_GET', '_POST');

    /**
     * REQUEST_URI
     * @var string;
     */
    private $requestUri;

    /**
     * Base URL of request
     * @var string
     */
    private $baseUrl = '';

    /**
     * Base path of request
     * @var string
     */
    private $basePath = '';

    /**
     * PATH_INFO
     * @var string
     */
    private $pathInfo = '';

    /**
     * Raw request body
     * @var string|false
     */
    private $rawBody;

    /**
     * Constructor
	 *
     * @return void
     */
    public function __construct()
    {
        $this->setRequestUri();
    }

	/*
	 * Son los key para obtener modulo, controlador de pagina y accion desde el array de parametros
	 * Cuando el router extrae los valores setea los valores en el array de parametros y tambien en los atributos del request
	 */
    public function getModuleKey(){ return $this->moduleKey; }
    public function setModuleKey($key){ $this->moduleKey = (string) $key; return $this; }
    public function getControllerKey(){ return $this->controllerKey; }
    public function setControllerKey($key){ $this->controllerKey = (string) $key; return $this; }
    public function getActionKey(){ return $this->actionKey; }
    public function setActionKey($key){ $this->actionKey = (string) $key; return $this; }

    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->module;
    }

    /**
     * Set the module name to use
     *
     * @param string $value
     * @return Zend_Controller_Request_Abstract
     */
    public function setModuleName($value)
    {
        $this->module = $value;
        return $this;
    }

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * Set the controller name to use
     *
     * @param string $value
     * @return Zend_Controller_Request_Abstract
     */
    public function setControllerName($value)
    {
        $this->controller = $value;
        return $this;
    }

    /**
     * Retrieve the action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->action;
    }

    /**
     * Set the action name
     *
     * @param string $value
     * @return Zend_Controller_Request_Abstract
     */
    public function setActionName($value)
    {
        $this->action = $value;
        return $this;
    }

    /**
     * Devuelve un key para consultar permisos y acciones activas a partir de modulo, controlador y accion
     */
    public function getKeyPermiso()
    {
        return $this->module."_".$this->controller."_".$this->action;
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParam($key, $value)
    {
        $key = (string)$key;
        if ((null === $value) && isset($this->params[$key])){
            unset($this->params[$key]);
        }elseif (null !== $value){
            $this->params[$key] = $value;
        }
        return $this;
    }

    /**
     * Imprime el contenido del objeto para pruebas, etc
     */
    public function imprimirContenido($exit = false)
    {
        echo "<pre>"; print_r($this); echo "</pre>";
        if ($exit){ exit(); }
    }

    /**
     * Imprime el contenido del array en $_POST
     */
    public function imprimirPost($exit = false)
    {
        echo "<pre>"; print_r($this->getPost()); echo "</pre>";
        if ($exit){ exit(); }
    }

    /**
     * Set flag indicating whether or not request has been dispatched
     *
     * @param boolean $flag
     * @return Zend_Controller_Request_Abstract
     */
    public function setDispatched($flag = true)
    {
        $this->dispatched = $flag ? true : false;
        return $this;
    }

    /**
     * Determine if the request has been dispatched
     *
     * @return boolean
     */
    public function isDispatched()
    {
        return $this->dispatched;
    }

    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
     *
     * @see http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        switch(true){
            case isset($this->params[$key]):
                return $this->params[$key];
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case ($key == 'REQUEST_URI'):
                return $this->getRequestUri();
            case ($key == 'PATH_INFO'):
                return $this->getPathInfo();
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    }

    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function has($key)
    {
        switch(true){
            case isset($this->params[$key]):
                return true;
            case isset($_GET[$key]):
                return true;
            case isset($_POST[$key]):
                return true;
            case isset($_COOKIE[$key]):
                return true;
            case isset($_SERVER[$key]):
                return true;
            case isset($_ENV[$key]):
                return true;
            default:
                return false;
        }
    }

    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key = null, $default = null)
    {
        if(null === $key){
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }

    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * Retrieve a member of the $_COOKIE superglobal
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Retrieve a member of the $_ENV superglobal
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null)
    {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }

    /**
     * Set the REQUEST_URI on which the instance operates
     *
     * uses the value in $_SERVER['REQUEST_URI'], $_SERVER['HTTP_X_REWRITE_URL'], or
	 * $_SERVER['ORIG_PATH_INFO'] + $_SERVER['QUERY_STRING'].
     *
     * @return Request
     */
    public function setRequestUri()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (
                // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
                isset($_SERVER['IIS_WasUrlRewritten'])
                && $_SERVER['IIS_WasUrlRewritten'] == '1'
                && isset($_SERVER['UNENCODED_URL'])
                && $_SERVER['UNENCODED_URL'] != ''
                ) {
                $requestUri = $_SERVER['UNENCODED_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                // Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
                $schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                        $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                        $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
        } else {
                return $this;
        }

        $this->requestUri = $requestUri;
	return $this;
    }

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */
    public function getRequestUri()
    {
        if (empty($this->requestUri)) {
            $this->setRequestUri();
        }

        return $this->requestUri;
    }

    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
	 * Esto es util para usarla con el DNS del localhost en el que programamos porq aparece un subdirectorio nuevo.
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Do not use the full URI when providing the base. The following are
     * examples of what not to use:
     * - http://example.com/admin (should be just /admin)
     * - http://example.com/subdir/index.php (should be just /subdir/index.php)
     *
     * If no $baseUrl is provided, attempts to determine the base URL from the
     * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
     * ORIG_SCRIPT_NAME in its determination.
     *
     * @param mixed $baseUrl
     * @return Request
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ((null !== $baseUrl) && !is_string($baseUrl)) {
            return $this;
        }

        if ($baseUrl === null) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
            } else {
                // Backtrack up the script_filename to find the portion matching
                // php_self
                $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
                $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
                $segs    = explode('/', trim($file, '/'));
                $segs    = array_reverse($segs);
                $index   = 0;
                $last    = count($segs);
                $baseUrl = '';
                do {
                    $seg     = $segs[$index];
                    $baseUrl = '/' . $seg . $baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
            }

            // Does the baseUrl have anything in common with the request_uri?
            $requestUri = $this->getRequestUri();

            if (0 === strpos($requestUri, $baseUrl)) {
                // full $baseUrl matches
                $this->baseUrl = $baseUrl;
                return $this;
            }

            if (0 === strpos($requestUri, dirname($baseUrl))) {
                // directory portion of $baseUrl matches
                $this->baseUrl = rtrim(dirname($baseUrl), '/');
                return $this;
            }

            $truncatedRequestUri = $requestUri;
            if (($pos = strpos($requestUri, '?')) !== false) {
                $truncatedRequestUri = substr($requestUri, 0, $pos);
            }

            $basename = basename($baseUrl);
            if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                // no match whatsoever; set it blank
                $this->baseUrl = '';
                return $this;
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            if ((strlen($requestUri) >= strlen($baseUrl))
                && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0)))
            {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Everything in REQUEST_URI before PATH_INFO
     * <form action="<?=$baseUrl?>/news/submit" method="POST"/>
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl();
        }

        return urldecode($this->baseUrl);
    }

    /**
     * Set the base path for the URL
     */
    public function setBasePath($basePath = null)
    {
        if ($basePath === null) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME']))
                      ? basename($_SERVER['SCRIPT_FILENAME'])
                      : '';

            $baseUrl = $this->getBaseUrl();
            if (empty($baseUrl)) {
                $this->basePath = '';
                return $this;
            }

            if (basename($baseUrl) === $filename) {
                $basePath = dirname($baseUrl);
            } else {
                $basePath = $baseUrl;
            }
        }

        if (substr(PHP_OS, 0, 3) === 'WIN') {
            $basePath = str_replace('\\', '/', $basePath);
        }

        $this->basePath = rtrim($basePath, '/');
        return $this;
    }

    /**
     * Everything in REQUEST_URI before PATH_INFO not including the filename
     * <img src="<?=$basePath?>/images/zend.png"/>
     *
     * @return string
     */
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->setBasePath();
        }

        return $this->basePath;
    }

    /**
     * Esta funcion la hice porque en ie6 y ie7 hay error si no pones la ruta completa en base tag.
     *
     */
    public function getBaseTagUrl()
    {
        $scheme = $this->getScheme();
        $httpHost = $this->getHttpHost();
        $baseUrl = $this->getBaseUrl();
        return $scheme.'://'.$httpHost.$baseUrl.'/';
    }

    /**
     * Set the PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $baseUrl = $this->getBaseUrl();

            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $requestUri = urldecode($requestUri);

            if (null !== $baseUrl
                && ((!empty($baseUrl) && 0 === strpos($requestUri, $baseUrl))
                    || empty($baseUrl))
                    && false === ($pathInfo = substr($requestUri, strlen($baseUrl)))
            ){
                // If substr() returns false then PATH_INFO is set to an empty string
                $pathInfo = '';
            } elseif (null === $baseUrl
                    || (!empty($baseUrl) && false === strpos($requestUri, $baseUrl))
            ) {
                $pathInfo = $requestUri;
            }
        }
        $this->pathInfo = (string) $pathInfo;
        return $this;
    }

    /**
     * Returns everything between the BaseUrl and QueryString.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getPathInfo()
    {
        if(empty($this->pathInfo)){
                $this->setPathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * Set allowed parameter sources
     *
     * Can be empty array, or contain one or more of '_GET' or '_POST'.
     *
     * @param  array $paramSoures
     * @return Zend_Controller_Request_Http
     */
    public function setParamSources(array $paramSources = array())
    {
        $this->paramSources = $paramSources;
        return $this;
    }

    /**
     * Get list of allowed parameter sources
     *
     * @return array
     */
    public function getParamSources()
    {
        return $this->paramSources;
    }

    /**
     * Retrieve a parameter
     *
     * Retrieves a parameter from the instance. Priority is in the order of
     * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, null is returned.
     *
     * If the $key is an alias, the actual key aliased will be used.
     *
     * @param mixed $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $paramSources = $this->getParamSources();
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } elseif (in_array('_GET', $paramSources) && (isset($_GET[$key]))) {
            return $_GET[$key];
        } elseif (in_array('_POST', $paramSources) && (isset($_POST[$key]))) {
            return $_POST[$key];
        }

        return $default;
    }

    /**
     * Retrieve an array of parameters
     *
     * Retrieves a merged array of parameters, with precedence of userland
     * params (see {@link setParam()}), $_GET, $_POST (i.e., values in the
     * userland params will take precedence over all others).
     *
     * @return array
     */
    public function getParams()
    {
        $return       = $this->params;
        $paramSources = $this->getParamSources();
        if (in_array('_GET', $paramSources)
            && isset($_GET)
            && is_array($_GET)
        ) {
            $return += $_GET;
        }
        if (in_array('_POST', $paramSources)
            && isset($_POST)
            && is_array($_POST)
        ) {
            $return += $_POST;
        }
        return $return;
    }

    /**
     * Set parameters
     *
     * Set one or more parameters. Parameters are set as userland parameters,
     * using the keys specified in the array.
     *
     * @param array $params
     * @return Zend_Controller_Request_Http
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value){
            $this->setParam($key, $value);
        }
	return $this;
    }

    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost()
    {
        if('POST' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet()
    {
        if('GET' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut()
    {
        if('PUT' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete()
    {
        if('DELETE' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead()
    {
        if('HEAD' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions()
    {
        if('OPTIONS' == $this->getMethod()){
            return true;
        }
        return false;
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Funciona con Jquery =)
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * Is this a Flash request?
     *
     * @return boolean
     */
    public function isFlashRequest()
    {
        $header = strtolower($this->getHeader('USER_AGENT'));
        return (strstr($header, ' flash')) ? true : false;
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure()
    {
        return ($this->getScheme() === self::SCHEME_HTTPS);
    }

    /**
     * Return the raw body of the request, if present
     *
     * @return string|false Raw body, or false if not present
     */
    public function getRawBody()
    {
        if (null === $this->_rawBody) {
            $body = file_get_contents('php://input');

            if (strlen(trim($body)) > 0) {
                $this->rawBody = $body;
            } else {
                $this->rawBody = false;
            }
        }
        return $this->rawBody;
    }

    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|false HTTP header value, or false if not found
     * @throws Zend_Controller_Request_Exception
     */
    public function getHeader($header)
    {
        if (empty($header)) {
            throw new Exception('An HTTP header name is required');
        }

        //Try to get it from the $_SERVER array first
        $temp = 'HTTP_'.strtoupper(str_replace('-','_',$header));
        if (isset($_SERVER[$temp])){
            return $_SERVER[$temp];
        }

        // This seems to be the only way to get the Authorization header on Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers[$header])) {
                return $headers[$header];
            }
            $header = strtolower($header);
            foreach ($headers as $key => $value) {
                if (strtolower($key) == $header) {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name   = $this->getServer('SERVER_NAME');
        $port   = $this->getServer('SERVER_PORT');

        if(null === $name) {
            return '';
        }
        elseif (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }

    /**
     * Get the client's IP addres
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        if ($checkProxy && $this->getServer('HTTP_CLIENT_IP') != null) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else if ($checkProxy && $this->getServer('HTTP_X_FORWARDED_FOR') != null) {
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }

        return $ip;
    }
}
