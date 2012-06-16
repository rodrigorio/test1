<?php
/**
 * Session. Adaptacion desde Zend_Session.
 * (ver http://framework.zend.com/manual/en/zend.session.html)
 */
class Session extends SessionAbstract
{
    /**
     * Whether or not Zend_Session is being used with unit tests
     *
     * @internal
     * @var bool
     */
    public static $unitTestEnabled = false;

    /**
     * $throwStartupException
     *
     * @var bool|bitset This could also be a combiniation of error codes to catch
     */
    protected static $throwStartupExceptions = true;

    /**
     * Check whether or not the session was started
     *
     * @var bool
     */
    private static $sessionStarted = false;

    /**
     * Whether or not the session id has been regenerated this request.
     *
     * Id regeneration state
     * <0 - regenerate requested when session is started
     * 0  - do nothing
     * >0 - already called session_regenerate_id()
     *
     * @var int
     */
    private static $regenerateIdState = 0;

    /**
     * Private list of php's ini values for ext/session
     * null values will default to the php.ini value, otherwise
     * the value below will overwrite the default ini value, unless
     * the user has set an option explicity with setOptions()
     *
     * @var array
     */
    private static $defaultOptions = array(
        'save_path'                 => null,
        'name'                      => null, /* this should be set to a unique value for each application */
        'save_handler'              => null,
        //'auto_start'                => null, /* intentionally excluded (see manual) */
        'gc_probability'            => null,
        'gc_divisor'                => null,
        'gc_maxlifetime'            => null,
        'serialize_handler'         => null,
        'cookie_lifetime'           => null,
        'cookie_path'               => null,
        'cookie_domain'             => null,
        'cookie_secure'             => null,
        'cookie_httponly'           => null,
        'use_cookies'               => null,
        'use_only_cookies'          => 'on',
        'referer_check'             => null,
        'entropy_file'              => null,
        'entropy_length'            => null,
        'cache_limiter'             => null,
        'cache_expire'              => null,
        'use_trans_sid'             => null,
        'bug_compat_42'             => null,
        'bug_compat_warn'           => null,
        'hash_function'             => null,
        'hash_bits_per_character'   => null
    );

    /**
     * List of options pertaining to Zend_Session that can be set by developers
     * using Zend_Session::setOptions(). This list intentionally duplicates
     * the individual declaration of static "class" variables by the same names.
     *
     * @var array
     */
    private static $localOptions = array(
        'strict'                   => 'strict',
        'remember_me_seconds'      => 'rememberMeSeconds',
        'throw_startup_exceptions' => 'throwStartupExceptions'
    );

    /**
     * Whether or not write close has been performed.
     *
     * @var bool
     */
    private static $writeClosed = false;

    /**
     * Whether or not session id cookie has been deleted
     *
     * @var bool
     */
    private static $sessionCookieDeleted = false;

    /**
     * Whether or not session has been destroyed via session_destroy()
     *
     * @var bool
     */
    private static $destroyed = false;

    /**
     * Whether or not session must be initiated before usage
     *
     * @var bool
     */
    private static $strict = false;

    /**
     * Default number of seconds the session will be remembered for when asked to be remembered
     *
     * @var int
     */
    private static $rememberMeSeconds = 1209600; // 2 weeks

    /**
     * Whether the default options listed in Session::$localOptions have been set
     *
     * @var bool
     */
    private static $defaultOptionsSet = false;

    /**
     * A reference to the set session save handler (nosotros no lo usamos, no guardamos session en DB sino en el server donde corre la pag)
     * en un futuro se puede programar un savehandler para usar memcache
     */
    private static $saveHandler = null;


    /**
     * Constructor overriding - make sure that a developer cannot instantiate
     */
    protected function __construct()
    {
    }

    /**
     * setOptions - set both the class specified
     *
     * @param  array $userOptions - pass-by-keyword style array of <option name, option value> pairs
     * @throws SessionException
     * @return void
     */
    public static function setOptions(array $userOptions = array())
    {
        // set default options on first run only (before applying user settings)
        if (!self::$defaultOptionsSet) {
            foreach (self::$defaultOptions as $defaultOptionName => $defaultOptionValue) {
                if (isset(self::$defaultOptions[$defaultOptionName])) {
                    ini_set("session.$defaultOptionName", $defaultOptionValue);
                }
            }

            self::$defaultOptionsSet = true;
        }

        // set the options the user has requested to set
        foreach ($userOptions as $userOptionName => $userOptionValue) {

            $userOptionName = strtolower($userOptionName);

            // set the ini based values
            if (array_key_exists($userOptionName, self::$defaultOptions)) {
                ini_set("session.$userOptionName", $userOptionValue);
            }
            elseif (isset(self::$localOptions[$userOptionName])) {
                self::${self::$localOptions[$userOptionName]} = $userOptionValue;
            }
            else {
                throw new SessionException("Unknown option: $userOptionName = $userOptionValue");
            }
        }
    }

    /**
     * getOptions()
     *
     * @param string $optionName OPTIONAL
     * @return array|string
     */
    public static function getOptions($optionName = null)
    {
        $options = array();
        foreach (ini_get_all('session') as $sysOptionName => $sysOptionValues) {
            $options[substr($sysOptionName, 8)] = $sysOptionValues['local_value'];
        }
        foreach (self::$localOptions as $localOptionName => $localOptionMemberName) {
            $options[$localOptionName] = self::${$localOptionMemberName};
        }

        if ($optionName) {
            if (array_key_exists($optionName, $options)) {
                return $options[$optionName];
            }
            return null;
        }

        return $options;
    }

    /**
     * regenerateId() - Regenerate the session id.  Best practice is to call this after
     * session is started.  If called prior to session starting, session id will be regenerated
     * at start time.
     *
     * @throws SessionException
     * @return void
     */
    public static function regenerateId()
    {
        if (!self::$unitTestEnabled && headers_sent($filename, $linenum)) {
            throw new SessionException("You must call " . __CLASS__ . '::' . __FUNCTION__ .
                "() before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        if (self::$sessionStarted && self::$regenerateIdState <= 0) {
            if (!self::$unitTestEnabled) {
                session_regenerate_id(true);
            }
            self::$regenerateIdState = 1;
        } else {
            /**
             * @todo If we can detect that this requester had no session previously,
             *       then why regenerate the id before the session has started?
             *       Feedback wanted for:
             //
            if (isset($_COOKIE[session_name()]) || (!use only cookies && isset($_REQUEST[session_name()]))) {
                self::$_regenerateIdState = 1;
            } else {
                self::$_regenerateIdState = -1;
            }
            //*/
            self::$regenerateIdState = -1;
        }
    }


    /**
     * rememberMe() - Write a persistent cookie that expires after a number of seconds in the future. If no number of
     * seconds is specified, then this defaults to self::$rememberMeSeconds.  Due to clock errors on end users' systems,
     * large values are recommended to avoid undesirable expiration of session cookies.
     *
     * @param int $seconds OPTIONAL specifies TTL for cookie in seconds from present time
     * @return void
     */
    public static function rememberMe($seconds = null)
    {
        $seconds = (int) $seconds;
        $seconds = ($seconds > 0) ? $seconds : self::$rememberMeSeconds;

        self::rememberUntil($seconds);
    }


    /**
     * forgetMe() - Write a volatile session cookie, removing any persistent cookie that may have existed. The session
     * would end upon, for example, termination of a web browser program.
     *
     * @return void
     */
    public static function forgetMe()
    {
        self::rememberUntil(0);
    }


    /**
     * rememberUntil() - This method does the work of changing the state of the session cookie and making
     * sure that it gets resent to the browser via regenerateId()
     *
     * @param int $seconds
     * @return void
     */
    public static function rememberUntil($seconds = 0)
    {
        if (self::$unitTestEnabled) {
            self::regenerateId();
            return;
        }

        $cookieParams = session_get_cookie_params();

        session_set_cookie_params(
            $seconds,
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure']
            );

        // normally "rememberMe()" represents a security context change, so should use new session id
        self::regenerateId();
    }


    /**
     * sessionExists() - whether or not a session exists for the current request
     *
     * @return bool
     */
    public static function sessionExists()
    {
        if (ini_get('session.use_cookies') == '1' && isset($_COOKIE[session_name()])) {
            return true;
        } elseif (!empty($_REQUEST[session_name()])) {
            return true;
        } elseif (self::$unitTestEnabled) {
            return true;
        }

        return false;
    }


    /**
     * Whether or not session has been destroyed via session_destroy()
     *
     * @return bool
     */
    public static function isDestroyed()
    {
        return self::$destroyed;
    }


    /**
     * start() - Start the session.
     *
     * @param bool|array $options  OPTIONAL Either user supplied options, or flag indicating if start initiated automatically
     * @throws SessionException
     * @return void
     */
    public static function start($options = false)
    {
        if (self::$sessionStarted && self::$destroyed) {
            throw new SessionException('The session was explicitly destroyed during this request, attempting to re-start is not allowed.');
        }

        if (self::$sessionStarted) {
            return; // already started
        }

        // make sure our default options (at the least) have been set
        if (!self::$defaultOptionsSet) {
            self::setOptions(is_array($options) ? $options : array());
        }

        // In strict mode, do not allow auto-starting Session, such as via "new SessionNamespace()"
        if (self::$strict && $options === true) {
            throw new SessionException('You must explicitly start the session with Zend_Session::start() when session options are set to strict.');
        }

        $filename = $linenum = null;
        if (!self::$unitTestEnabled && headers_sent($filename, $linenum)) {
            throw new SessionException("Session must be started before any output has been sent to the browser;"
               . " output started in {$filename}/{$linenum}");
        }

        // See http://www.php.net/manual/en/ref.session.php for explanation
        if (!self::$unitTestEnabled && defined('SID')) {
            throw new SessionException('session has already been started by session.auto-start or session_start()');
        }

        /**
         * Hack to throw exceptions on start instead of php errors
         * @see http://framework.zend.com/issues/browse/ZF-1325
         */

        $errorLevel = (is_int(self::$throwStartupExceptions)) ? self::$throwStartupExceptions : E_ALL;

        /** @see SessionException */
        if (!self::$unitTestEnabled) {

            if (self::$throwStartupExceptions) {
                set_error_handler(array('SessionException', 'handleSessionStartError'), $errorLevel);
            }

            $startedCleanly = session_start();

            if (self::$throwStartupExceptions) {
                restore_error_handler();
            }

            if (!$startedCleanly || SessionException::$sessionStartError != null) {
                if (self::$throwStartupExceptions) {
                    set_error_handler(array('SessionException', 'handleSilentWriteClose'), $errorLevel);
                }
                session_write_close();
                if (self::$throwStartupExceptions) {
                    restore_error_handler();
                    throw new SessionException(__CLASS__ . '::' . __FUNCTION__ . '() - ' . SessionException::$sessionStartError);
                }
            }
        }

        parent::$readable = true;
        parent::$writable = true;
        self::$sessionStarted = true;
        if (self::$regenerateIdState === -1) {
            self::regenerateId();
        }

        // run validators if they exist
        if (isset($_SESSION['__ZF']['VALID'])) {
            self::processValidators();
        }

        self::processStartupMetadataGlobal();
    }


    /**
     * processGlobalMetadata() - this method initizes the sessions GLOBAL
     * metadata, mostly global data expiration calculations.
     *
     * @return void
     */
    private static function processStartupMetadataGlobal()
    {
        // process global metadata
        if (isset($_SESSION['__ZF'])) {

            // expire globally expired values
            foreach ($_SESSION['__ZF'] as $namespace => $namespace_metadata) {

                // Expire Namespace by Time (ENT)
                if (isset($namespace_metadata['ENT']) && ($namespace_metadata['ENT'] > 0) && (time() > $namespace_metadata['ENT']) ) {
                    unset($_SESSION[$namespace]);
                    unset($_SESSION['__ZF'][$namespace]);
                }

                // Expire Namespace by Global Hop (ENGH) if it wasnt expired above
                if (isset($_SESSION['__ZF'][$namespace]) && isset($namespace_metadata['ENGH']) && $namespace_metadata['ENGH'] >= 1) {

                    $_SESSION['__ZF'][$namespace]['ENGH']--;

                    if ($_SESSION['__ZF'][$namespace]['ENGH'] === 0) {
                        if (isset($_SESSION[$namespace])) {
                            parent::$expiringData[$namespace] = $_SESSION[$namespace];
                            unset($_SESSION[$namespace]);
                        }
                        unset($_SESSION['__ZF'][$namespace]);
                    }
                }

                // Expire Namespace Variables by Time (ENVT)
                if (isset($namespace_metadata['ENVT'])) {
                    foreach ($namespace_metadata['ENVT'] as $variable => $time) {
                        if (time() > $time) {
                            unset($_SESSION[$namespace][$variable]);
                            unset($_SESSION['__ZF'][$namespace]['ENVT'][$variable]);
                        }
                    }
                    if (empty($_SESSION['__ZF'][$namespace]['ENVT'])) {
                        unset($_SESSION['__ZF'][$namespace]['ENVT']);
                    }
                }

                // Expire Namespace Variables by Global Hop (ENVGH)
                if (isset($namespace_metadata['ENVGH'])) {
                    foreach ($namespace_metadata['ENVGH'] as $variable => $hops) {
                        $_SESSION['__ZF'][$namespace]['ENVGH'][$variable]--;

                        if ($_SESSION['__ZF'][$namespace]['ENVGH'][$variable] === 0) {
                            if (isset($_SESSION[$namespace][$variable])) {
                                parent::$expiringData[$namespace][$variable] = $_SESSION[$namespace][$variable];
                                unset($_SESSION[$namespace][$variable]);
                            }
                            unset($_SESSION['__ZF'][$namespace]['ENVGH'][$variable]);
                        }
                    }
                    if (empty($_SESSION['__ZF'][$namespace]['ENVGH'])) {
                        unset($_SESSION['__ZF'][$namespace]['ENVGH']);
                    }
                }
            }

            if (isset($namespace) && empty($_SESSION['__ZF'][$namespace])) {
                unset($_SESSION['__ZF'][$namespace]);
            }
        }

        if (isset($_SESSION['__ZF']) && empty($_SESSION['__ZF'])) {
            unset($_SESSION['__ZF']);
        }
    }


    /**
     * isStarted() - convenience method to determine if the session is already started.
     *
     * @return bool
     */
    public static function isStarted()
    {
        return self::$sessionStarted;
    }

    /**
     * setSaveHandler() - Session Save Handler assignment
     *
     */
    public static function setSaveHandler(SessionSaveHandlerInterface $saveHandler)
    {
        self::$saveHandler = $saveHandler;

        if (self::$unitTestEnabled) {
            return;
        }

        session_set_save_handler(
            array(&$saveHandler, 'open'),
            array(&$saveHandler, 'close'),
            array(&$saveHandler, 'read'),
            array(&$saveHandler, 'write'),
            array(&$saveHandler, 'destroy'),
            array(&$saveHandler, 'gc')
            );
    }

    /**
     * getSaveHandler() - Get the session Save Handler
     */
    public static function getSaveHandler()
    {
        return self::$saveHandler;
    }

    /**
     * isRegenerated() - convenience method to determine if session_regenerate_id()
     * has been called during this request by Session.
     *
     * @return bool
     */
    public static function isRegenerated()
    {
        return ( (self::$regenerateIdState > 0) ? true : false );
    }


    /**
     * getId() - get the current session id
     *
     * @return string
     */
    public static function getId()
    {
        return session_id();
    }


    /**
     * setId() - set an id to a user specified id
     *
     * @throws SessionException
     * @param string $id
     * @return void
     */
    public static function setId($id)
    {
        if (!self::$unitTestEnabled && defined('SID')) {
            throw new SessionException('The session has already been started.  The session id must be set first.');
        }

        if (!self::$unitTestEnabled && headers_sent($filename, $linenum)) {
            throw new SessionException("You must call ".__CLASS__.'::'.__FUNCTION__.
                "() before any output has been sent to the browser; output started in {$filename}/{$linenum}");
        }

        if (!is_string($id) || $id === '') {
            throw new SessionException('You must provide a non-empty string as a session identifier.');
        }

        session_id($id);
    }


    /**
     * registerValidator() - register a validator that will attempt to validate this session for
     * every future request
     *
     * @param SessionValidator $validator
     * @return void
     */
    public static function registerValidator(SessionValidator $validator)
    {
        $validator->setup();
    }


    /**
     * stop() - Disable write access.  Optionally disable read (not implemented).
     *
     * @return void
     */
    public static function stop()
    {
        parent::$writable = false;
    }


    /**
     * writeClose() - Shutdown the sesssion, close writing and detach $_SESSION from the back-end storage mechanism.
     * This will complete the internal data transformation on this request.
     *
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Session's attempt writes)
     * @return void
     */
    public static function writeClose($readonly = true)
    {
        if (self::$unitTestEnabled) {
            return;
        }

        if (self::$writeClosed) {
            return;
        }

        if ($readonly) {
            parent::$writable = false;
        }

        session_write_close();
        self::$writeClosed = true;
    }


    /**
     * destroy() - This is used to destroy session data, and optionally, the session cookie itself
     *
     * @param bool $remove_cookie - OPTIONAL remove session id cookie, defaults to true (remove cookie)
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Session's attempt writes)
     * @return void
     */
    public static function destroy($remove_cookie = true, $readonly = true)
    {
        if (self::$unitTestEnabled) {
            return;
        }

        if (self::$destroyed) {
            return;
        }

        if ($readonly) {
            parent::$writable = false;
        }

        session_destroy();
        self::$destroyed = true;

        if ($remove_cookie) {
            self::expireSessionCookie();
        }
    }


    /**
     * expireSessionCookie() - Sends an expired session id cookie, causing the client to delete the session cookie
     *
     * @return void
     */
    public static function expireSessionCookie()
    {
        if (self::$unitTestEnabled) {
            return;
        }

        if (self::$sessionCookieDeleted) {
            return;
        }

        self::$sessionCookieDeleted = true;

        if (isset($_COOKIE[session_name()])) {
            $cookie_params = session_get_cookie_params();

            setcookie(
                session_name(),
                false,
                315554400, // strtotime('1980-01-01'),
                $cookie_params['path'],
                $cookie_params['domain'],
                $cookie_params['secure']
                );
        }
    }


    /**
     * processValidator() - internal function that is called in the existence of VALID metadata
     *
     * @throws SessionException
     * @return void
     */
    private static function processValidators()
    {
        foreach ($_SESSION['__ZF']['VALID'] as $validator_name => $valid_data) {
            $validator = new $validator_name;
            if ($validator->validate() === false) {
                throw new SessionException("This session is not valid according to {$validator_name}.");
            }
        }
    }


    /**
     * namespaceIsset() - check to see if a namespace is set
     *
     * @param string $namespace
     * @return bool
     */
    public static function namespaceIsset($namespace, $name = null)
    {
        return parent::namespaceIsset($namespace, $name);
    }


    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param string $namespace
     * @throws SessionException
     * @return void
     */
    public static function namespaceUnset($namespace, $name = null)
    {
        parent::namespaceUnset($namespace, $name);
        SessionNamespace::resetSingleInstance($namespace);
    }

    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @throws SessionException
     * @return ArrayObject
     */
    public static function getIterator()
    {
        if (parent::$_readable === false) {
            throw new SessionException(parent::THROW_NOT_READABLE_MSG);
        }

        $spaces  = array();
        if (isset($_SESSION)) {
            $spaces = array_keys($_SESSION);
            foreach($spaces as $key => $space) {
                if (!strncmp($space, '__', 2) || !is_array($_SESSION[$space])) {
                    unset($spaces[$key]);
                }
            }
        }

        return new ArrayObject(array_merge($spaces, array_keys(parent::$_expiringData)));
    }


    /**
     * isWritable() - returns a boolean indicating if namespaces can write (use setters)
     *
     * @return bool
     */
    public static function isWritable()
    {
        return parent::$writable;
    }


    /**
     * isReadable() - returns a boolean indicating if namespaces can write (use setters)
     *
     * @return bool
     */
    public static function isReadable()
    {
        return parent::$readable;
    }
}
