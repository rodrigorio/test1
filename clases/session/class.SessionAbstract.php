<?php
/**
 * SessionAbstract
 */
abstract class SessionAbstract
{
   /**
     * Whether or not session permits writing (modification of $_SESSION[])
     *
     * @var bool
     */
    protected static $writable = false;

    /**
     * Whether or not session permits reading (reading data in $_SESSION[])
     *
     * @var bool
     */
    protected static $readable = false;

    /**
     * Since expiring data is handled at startup to avoid __destruct difficulties,
     * the data that will be expiring at end of this request is held here
     *
     * @var array
     */
    protected static $expiringData = array();


    /**
     * Error message thrown when an action requires modification,
     * but current Zend_Session has been marked as read-only.
     */
    const THROW_NOT_WRITABLE_MSG = 'Session is currently marked as read-only.';


    /**
     * Error message thrown when an action requires reading session data,
     * but current Zend_Session is not marked as readable.
     */
    const THROW_NOT_READABLE_MSG = 'Session is not marked as readable.';


    /**
     * namespaceIsset() - check to see if a namespace or a variable within a namespace is set
     *
     * @param  string $namespace
     * @param  string $name
     * @return bool
     */
    protected static function namespaceIsset($namespace, $name = null)
    {
        if (self::$readable === false){
            throw new SessionException(self::THROW_NOT_READABLE_MSG);
        }

        if ($name === null) {
            return ( isset($_SESSION[$namespace]) || isset(self::$expiringData[$namespace]) );
        } else {
            return ( isset($_SESSION[$namespace][$name]) || isset(self::$expiringData[$namespace][$name]) );
        }
    }


    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param  string $namespace
     * @param  string $name
     * @throws SessionException
     * @return void
     */
    protected static function namespaceUnset($namespace, $name = null)
    {
        if (self::$writable === false) {
            throw new SessionException(self::THROW_NOT_WRITABLE_MSG);
        }

        $name = (string) $name;

        // check to see if the api wanted to remove a var from a namespace or a namespace
        if ($name === '') {
            unset($_SESSION[$namespace]);
            unset(self::$expiringData[$namespace]);
        } else {
            unset($_SESSION[$namespace][$name]);
            unset(self::$expiringData[$namespace]);
        }

        // if we remove the last value, remove namespace.
        if (empty($_SESSION[$namespace])) {
            unset($_SESSION[$namespace]);
        }
    }


    /**
     * namespaceGet() - Get $name variable from $namespace, returning by reference.
     *
     * @param  string $namespace
     * @param  string $name
     * @return mixed
     */
    protected static function & namespaceGet($namespace, $name = null)
    {
        if (self::$readable === false) {
            throw new SessionException(self::THROW_NOT_READABLE_MSG);
        }

        if ($name === null) {
            if (isset($_SESSION[$namespace])) { // check session first for data requested
                return $_SESSION[$namespace];
            } elseif (isset(self::$expiringData[$namespace])) { // check expiring data for data reqeusted
                return self::$expiringData[$namespace];
            } else {
                return $_SESSION[$namespace]; // satisfy return by reference
            }
        } else {
            if (isset($_SESSION[$namespace][$name])) { // check session first
                return $_SESSION[$namespace][$name];
            } elseif (isset(self::$expiringData[$namespace][$name])) { // check expiring data
                return self::$expiringData[$namespace][$name];
            } else {
                return $_SESSION[$namespace][$name]; // satisfy return by reference
            }
        }
    }


    /**
     * namespaceGetAll() - Get an array containing $namespace, including expiring data.
     *
     * @param string $namespace
     * @param string $name
     * @return mixed
     */
    protected static function namespaceGetAll($namespace)
    {
        $currentData  = (isset($_SESSION[$namespace]) && is_array($_SESSION[$namespace])) ?
            $_SESSION[$namespace] : array();
        $expiringData = (isset(self::$expiringData[$namespace]) && is_array(self::$expiringData[$namespace])) ?
            self::$expiringData[$namespace] : array();
        return array_merge($currentData, $expiringData);
    }
}