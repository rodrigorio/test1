<?php

/**
 * En el caso de los visitantes se crean perfiles sin objeto Usuario.
 */
abstract class PerfilAbstract
{
    const SEGUNDOS_EXPIRACION_PERMISOS_ACCIONES = 1200; //20 minutos

    /**
     * Instancia de Usuario
     */
    protected $usuario = null;

    /**
     * @var SessionNamespace
     */
    protected $permisos;

    /**
     * Id de perfil
     */
    protected $id;

    /**
     * Nombre del perfil instanciado
     */
    protected $descripcion;
	
    public function __construct(Usuario $usuario = null)
    {
        $this->permisos = new SessionNamespace('permisos');

        if(null !== $usuario)
        {
            $this->setUsuario($usuario);
        }
    }
	
    public function setUsuario(Usuario $usuario)
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
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
        if(!isset($this->permisos->acciones))
        {
            $array = SysController::getInstance()->cargarPermisosPerfil($this->id);
            if(!empty($array))
            {
                $this->permisos->acciones = $array;
                $this->permisos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_PERMISOS_ACCIONES, 'acciones');
            }
        }
        return $this;
    }
	
    public function tiene($funcion)
    {
        if(!isset($this->permisos->acciones))
        {
            $this->cargarPermisos();
        }
        return isset($this->permisos->acciones[$funcion]);
    }
	
    public function activo($funcion)
    {
        if(!isset($this->permisos->acciones))
        {
            $this->cargarPermisos();
        }        
        if($this->tiene($funcion))
        {
            return $this->permisos->acciones[$funcion];
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