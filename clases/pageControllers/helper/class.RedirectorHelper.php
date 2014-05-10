<?php

/**
 * para referencia http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 *
 * Esta clase establece metodos para redireccionar desde las acciones de los pageControllers.
 *
 * NOTA: Se puede redireccionar por suspencion de sistema/modulo, porque hay un error 404,
 *       porque algún plugin detecta error, o porque no hay permiso segun perfil.
 *       Este helper es para redireccionar por OTROS motivos desde las propias acciones,
 *       por ejemplo si se guarda una entidad y luego se redirecciona a la pagina de listado con mensaje.
 *       Otro ejemplo es cuando existe permiso segun el perfil para realizar la accion pero otra condicion
 *       prohibe llevarla a cabo (como en facebook, que no te deja ver albunes de personas q no son amigos).
 *
 * Muchas veces no hace falta cargar TODO de nuevo, el request esta en el loop del FrontController,
 * En esta clase hay metodos para redireccionar no para loopear en el front controller
 *
 * @TODO Hace falta ir trabajando la clase, dejar lo que sirve a medida que se necesita.
 */
class RedirectorHelper extends HelperAbstract
{
    /**
     * HTTP status code for redirects
     * Guarda que desde los page controllers se podria considerar NO redireccionar y mostrar una pantalla de 404.
     * o tambien NO redireccionar y devolver excepcion 401 cuando no hay permiso y que procese el plugin de permisos.
     * @var int
     */
    private $code = 302;

    /**
     * Whether or not calls to redirect() should exit script execution
     * @var boolean
     */
    private $exit = true;

    /**
     * Whether or not redirect() should attempt to prepend the base URL to the
     * passed URL (if it's a relative URL)
     * @var boolean
     */
    private $prependBase = true;

    /**
     * Url to which to redirect
     * @var string
     */
    private $redirectUrl = null;

    /**
     * Whether or not to use an absolute URI when redirecting
     * @var boolean
     */
    private $useAbsoluteUri = false;

    /**
     * Whether or not to close the session before exiting
     * @var boolean
     */
    private $closeSessionOnExit = true;

    /**
     * Retrieve HTTP status code to emit on {@link redirect()} call
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Validate HTTP status redirect code
     *
     * @param  int $code
     * @return true
     */
    private function checkCode($code)
    {
        $code = (int)$code;
        if ((300 > $code) || (307 < $code) || (304 == $code) || (306 == $code)) {
            throw new Exception('Invalid redirect HTTP status code (' . $code  . ')');
        }
        return true;
    }

    /**
     * Retrieve HTTP status code for {@link _redirect()} behaviour
     *
     * @param  int $code
     * @return Redirector Provides a fluent interface
     */
    public function setCode($code)
    {
        $this->checkCode($code);
        $this->code = $code;
        return $this;
    }

    /**
     * Retrieve flag for whether or not {@link _redirect()} will exit when finished.
     *
     * @return boolean
     */
    public function getExit()
    {
        return $this->exit;
    }

    /**
     * Retrieve exit flag for {@link _redirect()} behaviour
     *
     * @param  boolean $flag
     * @return Redirector Provides a fluent interface
     */
    public function setExit($flag)
    {
        $this->exit = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve flag for whether or not {@link _redirect()} will prepend the
     * base URL on relative URLs
     *
     * @return boolean
     */
    public function getPrependBase()
    {
        return $this->prependBase;
    }

    /**
     * Retrieve 'prepend base' flag for {@link _redirect()} behaviour
     *
     * @param  boolean $flag
     * @return Redirector Provides a fluent interface
     */
    public function setPrependBase($flag)
    {
        $this->prependBase = ($flag) ? true : false;
        return $this;
    }

    /**
     * Retrieve flag for whether or not {@link redirectAndExit()} shall close the session before
     * exiting.
     *
     * @return boolean
     */
    public function getCloseSessionOnExit()
    {
        return $this->closeSessionOnExit;
    }

    /**
     * Set flag for whether or not {@link redirectAndExit()} shall close the session before exiting.
     *
     * @param  boolean $flag
     * @return Redirector Provides a fluent interface
     */
    public function setCloseSessionOnExit($flag)
    {
        $this->closeSessionOnExit = ($flag) ? true : false;
        return $this;
    }

    /**
     * Return use absolute URI flag
     *
     * @return boolean
     */
    public function getUseAbsoluteUri()
    {
        return $this->useAbsoluteUri;
    }

    /**
     * Set use absolute URI flag
     *
     * @param  boolean $flag
     */
    public function setUseAbsoluteUri($flag = true)
    {
        $this->useAbsoluteUri = ($flag) ? true : false;
        return $this;
    }

    /**
     * Set redirect in response object
     *
     * @return void
     */
    private function redirect($url)
    {
        if ($this->getUseAbsoluteUri() && !preg_match('#^(https?|ftp)://#', $url)) {
            $host  = (isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'');
            $proto = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=="off") ? 'https' : 'http';
            $port  = (isset($_SERVER['SERVER_PORT'])?$_SERVER['SERVER_PORT']:80);
            $uri   = $proto . '://' . $host;
            if ((('http' == $proto) && (80 != $port)) || (('https' == $proto) && (443 != $port))) {
                // do not append if HTTP_HOST already contains port
                if (strrchr($host, ':') === false) {
                    $uri .= ':' . $port;
                }
            }
            $url = $uri . '/' . ltrim($url, '/');
        }

        $this->redirectUrl = $url;
        $this->getResponse()->setRedirect($url, $this->getCode());
    }

    /**
     * Retrieve currently set URL for redirect
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Determine if the baseUrl should be prepended, and prepend if necessary
     *
     * @param  string $url
     * @return string
     */
    private function prependBase($url)
    {
        if ($this->getPrependBase()) {
            $request = $this->getRequest();
            if ($request instanceof Request) {
                $base = rtrim($request->getBaseUrl(), '/');
                if (!empty($base) && ('/' != $base)) {
                    $url = $base . '/' . ltrim($url, '/');
                } else {
                    $url = '/' . ltrim($url, '/');
                }
            }
        }

        return $url;
    }

    /**
     * Set a redirect URL string
     *
     * By default, emits a 302 HTTP status header, prepends base URL as defined
     * in request object if url is relative, and halts script execution by
     * calling exit().
     *
     * $options is an optional associative array that can be used to control
     * redirect behaviour. The available option keys are:
     * - exit: boolean flag indicating whether or not to halt script execution when done
     * - prependBase: boolean flag indicating whether or not to prepend the base URL when a relative URL is provided
     * - code: integer HTTP status code to use with redirect. Should be between 300 and 307.
     *
     * redirect() sets the Location header in the response object. If you set
     * the exit flag to false, you can override this header later in code
     * execution.
     *
     * If the exit flag is true (true by default), redirect() will write and
     * close the current session, if any.
     *
     * @param  string $url
     * @param  array  $options
     * @return void
     */
    public function setGotoUrl($url, array $options = array())
    {
        // prevent header injections
        $url = str_replace(array("\n", "\r"), '', $url);

        if (null !== $options) {
            if (isset($options['exit'])) {
                $this->setExit(($options['exit']) ? true : false);
            }
            if (isset($options['prependBase'])) {
                $this->setPrependBase(($options['prependBase']) ? true : false);
            }
            if (isset($options['code'])) {
                $this->setCode($options['code']);
            }
        }

        // If relative URL, decide if we should prepend base URL
        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = $this->prependBase($url);
        }

        $this->redirect($url);
    }

    /**
     * Perform a redirect to a url
     *
     * @param  string $url
     * @param  array  $options
     * @return void
     */
    public function gotoUrl($url, array $options = array())
    {
        $this->setGotoUrl($url, $options);

        if($this->getExit()) {
            $this->redirectAndExit();
        }
    }

    /**
     * Set a URL string for a redirect, perform redirect, and immediately exit
     *
     * @param  string $url
     * @param  array  $options
     * @return void
     */
    public function gotoUrlAndExit($url, array $options = array())
    {
        $this->setGotoUrl($url, $options);
        $this->redirectAndExit();
    }

    /**
     * exit(): Perform exit for redirector
     *
     * @return void
     */
    public function redirectAndExit()
    {
        if ($this->getCloseSessionOnExit()) {
            // Close session, if started
            if (class_exists('Session') && Session::isStarted()) {
                Session::writeClose();
            } elseif (isset($_SESSION)) {
                session_write_close();
            }
        }

        $this->getResponse()->sendHeaders();
        exit();
    }
}
