<?php
/**
 * Para manejar las excepciones que disparan los metodos que operan con la session del sistema.
 */
class SessionException extends ZendException
{
    /**
     * sessionStartError
     *
     * @see http://framework.zend.com/issues/browse/ZF-1325
     * @var string PHP Error Message
     */
    static public $sessionStartError = null;

    /**
     * handleSessionStartError() - interface for set_error_handler()
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1325
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
    static public function handleSessionStartError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        self::$sessionStartError = $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
    }

    /**
     * handleSilentWriteClose() - interface for set_error_handler()
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1325
     * @param  int    $errno
     * @param  string $errstr
     * @return void
     */
    static public function handleSilentWriteClose($errno, $errstr, $errfile, $errline, $errcontext)
    {
        self::$sessionStartError .= PHP_EOL . $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
    }
}