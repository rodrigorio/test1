<?php
/**
 * Guarda una instancia de PerfilAbstract en session.
 */
class SessionAutentificacion
{
    /**
     * Object to proxy $_SESSION storage
     *
     * @var SessionNamespace
     */
    private $autentificacion;

    private $logged = false;
    
    /**
     * Singleton instance
     *
     * @var SessionPerfilStorage
     */
    private static $instance = null;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @return void
     */
    private function __construct()
    {
        $this->autentificacion = new SessionNamespace('autentificacion');
    }

    /**
     * Returns an instance of SessionPerfilStorage
     *
     * Singleton pattern implementation
     *
     * @return SessionPerfilStorage
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *
     * @return boolean
     */
    public function estaIdentificado()
    {
        return isset($this->autentificacion->perfil);
    }

    public function realizoLogin($flag = null)
    {
        if (null !== $flag){
            $this->logged = $flag;
            return $this;
        }
        return $this->logged;
    }

    /**
     *
     * @return PerfilAbstract
     */
    public function obtenerIdentificacion()
    {
        if (!$this->estaIdentificado()) {
            return null;
        }

        return $this->autentificacion->perfil;
    }

    public function limpiarAutentificacion()
    {
        unset($this->autentificacion->perfil);
    }

    public function cargarAutentificacion(PerfilAbstract $perfil)
    {
        if(isset($this->autentificacion->perfil)){
            unset($this->autentificacion->perfil);
        }
        $this->autentificacion->perfil = $perfil;
    }
}
