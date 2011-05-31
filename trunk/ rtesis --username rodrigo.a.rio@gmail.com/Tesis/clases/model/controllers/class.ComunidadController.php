<?php

/**
 * Controlador principal de la 'logica de negocio'. 
 *
 */
class ComunidadController
{
    /**
     * @var Instancia de DB
     */
    private $db = null;

    /**
     * @var Instancia de clase que maneja session de usuario
     */
    private $auth = null;
    
    private static $instance = null;

    private function __construct(){ }	
	
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Auth $session
     */
    public function setAuth(Auth $auth){
        $this->auth = $auth;
    }

    /**
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }
	
    /**
     * Retorna excepcion si no encuentra la publicacion
     *
     */
    public function obtenerPublicacion($publicacionId)
    {
        //hago el new aca por ahora para probar el tema del template
        $usuario = new Usuario();
        $usuario -> setNombreUsuario ('Pepe.Fernandez');
        $blogger = new Blogger($usuario);

        $descripcion = "Aasdkj al alskdj alskj las dlaks jdlask jdal sk asdj.
                        ASDlkjs dlkas jdlksj dasd ASD ASDa sdlka sdlkajs dasd...";

        $publicacion = new Publicacion();
        $publicacion -> setId($publicacionId)
                     -> setValoracion(2)
                     -> setCantidadCriticas(10)
                     -> setTitulo('Titulo Publicaci&oacute;n')
                     -> setFechaAlta('01/10/2011')
                     -> setAutor($blogger)
                     -> setDescripcion($descripcion);

        return $publicacion;
    }

    public function obtenerUltimaPublicacion()
    {
        $publicacion = null;
        return $publicacion;
    }
}
?>