<?php
/**
 * SessionValidator. Cada vez que se realiza el start() de la sesion valida que sea el mismo HttpUserAgent.
 * Sino tira excepcion en clase Session.
 *
 */
class SessionValidator
{
    /**
     * SetValidData() - This method should be used to store the environment variables that
     * will be needed in order to validate the session later in the validate() method.
     * These values are stored in the session in the __ZF namespace, in an array named VALID
     *
     * @param  mixed $data
     * @return void
     */
    protected function setValidData($data)
    {
        $validatorName = get_class($this);

        $_SESSION['__ZF']['VALID'][$validatorName] = $data;
    }


    /**
     * GetValidData() - This method should be used to retrieve the environment variables that
     * will be needed to 'validate' a session.
     *
     * @return mixed
     */
    protected function getValidData()
    {
        $validatorName = get_class($this);

        return $_SESSION['__ZF']['VALID'][$validatorName];
    }
    
    /**
     * Setup() - this method will get the current user agent and store it in the session
     * as 'valid data'
     *
     * @return void
     */
    public function setup()
    {
        $this->setValidData( (isset($_SERVER['HTTP_USER_AGENT'])
            ? $_SERVER['HTTP_USER_AGENT'] : null) );
    }

    /**
     * Validate() - this method will determine if the current user agent matches the
     * user agent we stored when we initialized this variable.
     *
     * will be called at the beginning of every session to determine if the current environment matches
     * that which was store in the setup() procedure
     *
     * @return bool
     */
    public function validate()
    {
        $currentBrowser = (isset($_SERVER['HTTP_USER_AGENT'])
            ? $_SERVER['HTTP_USER_AGENT'] : null);

        return $currentBrowser === $this->getValidData();
    }
}