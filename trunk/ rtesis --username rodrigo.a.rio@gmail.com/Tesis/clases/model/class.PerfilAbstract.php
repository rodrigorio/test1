<?php

/**
 * En el caso de los visitantes se crean perfiles sin objeto Usuario.
 */
abstract class PerfilAbstract
{
    const SEGUNDOS_EXPIRACION_PERMISOS_ACCIONES = 20; //20 segundos

    /**
     * Instancia de Usuario
     */
    protected $oUsuario = null;

    /**
     * @var SessionNamespace
     */
    protected $oPermisos;

    /**
     * Id de perfil
     */
    protected $iId;

    /**
     * Nombre del perfil instanciado
     */
    protected $sDescripcion;

 	public function __construct(){}

	public function iniciarPermisos()
    {
        $this->oPermisos = new SessionNamespace('permisos');
    }
    
    public function setUsuario(Usuario $oUsuario)
    {
        $this->oUsuario = $oUsuario;
        return $this;
    }

    public function getUsuario()
    {
        return $this->oUsuario;
    }

    public function setId($iId)
    {
        $this->iId = $iId;
        return $this;
    }

    public function getId()
    {
        return $this->iId;
    }

    public function setDescripcion($sDescripcion)
    {
        $this->sDescripcion = $sDescripcion;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->sDescripcion;
    }

    /**
     * Esto es un atajo. siempre conviene extraer el objeto usuario del objeto perfil.
     * Es una conveniencia porque siempre se quiere saber el nombre de usuario del Administrador por ejemplo.
     *
     * @return UsuarioAbstract
     */
    public function getNombreUsuario()
    {
        return $this->getUsuario()->getNombreUsuario();
    }

    protected function cargarPermisos()
    {
        if(!isset($this->oPermisos->acciones))
        {
            $array = SysController::getInstance()->cargarPermisosPerfil($this->iId);
            if(!empty($array))
            {
                $this->oPermisos->acciones = $array;
               // $this->oPermisos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_PERMISOS_ACCIONES, 'acciones');
            }
        }
        return $this;
    }

    public function tiene($funcion)//??es un string?
    {
        if(!isset($this->oPermisos->acciones))
        {
            $this->cargarPermisos();
        }
        return isset($this->oPermisos->acciones[$funcion]);
    }

    public function activo($funcion)
    {
        if(!isset($this->oPermisos->acciones))
        {
            $this->cargarPermisos();
        }
        if($this->tiene($funcion))
        {
            return $this->oPermisos->acciones[$funcion];
        }
        return null;
    }

    //METODOS DE INTERFACE (varian segun el perfil).

    /**
     * Por defecto devuelve Modulo/Controlador/Accion a la cual se debe redirigir el sistema luego de que un usuario solicita una accion
     * a la cual no tiene permiso ó esta desactivada.
     *
     * Si el flag esta activado devuelve el pathInfo de la redireccion. (ver HttpRequest getPathInfo() )
     */
    public function getUrlRedireccion($pathInfo = false){}

    /**
     * Url a la cual se redirecciona por defecto luego de realizar un login satisfactorio. Tambien formato Modulo/Controlador/Accion.
     * Si el flag esta activado devuelve el pathInfo de la redireccion. (ver HttpRequest getPathInfo() )
     */
    public function getUrlRedireccionLoginDefecto($pathInfo = false){}
}