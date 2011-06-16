<?php

/**
 *
 *
 *
 */
class Administrador extends PerfilAbstract
{
    const PERFIL_VISITANTE_ID = 1;
    const PERFIL_VISITANTE_DESCRIPCION = 'administrador';

    public function __construct(Usuario $usuario){
        parent::__construct($usuario);
        $this->id = self::PERFIL_VISITANTE_ID;
        $this->descripcion = self::PERFIL_VISITANTE_DESCRIPCION;
    }
    
    /**
     * Se supone que el administrador no tiene redirecciones por permisos =)
     * tiro excepcion para tener una guia al programar, si se desactiva desde index.php no sale nada.
     */
    public function getUrlRedireccion($pathInfo = false)
    {
        $keyPermiso = FrontController::getInstance()->getRequest()->getKeyPermiso();
        throw new Exception("ERROR, el administrador no debe tener acciones deshabilitadas. Accion: ".$keyPermiso." no aparece entre los permisos del perfil.");
    }

    /**
     * Url a la cual se redirecciona por defecto luego de realizar un login satisfactorio.
     *
     * Se tendria que llamar de la siguiente manera: list($modulo,$controlador,$accion) = $administrador->getUrlRedireccionLoginDefecto();
     *
     * @return array|string 1)Modulo 2)Controlador 3) Accion o pathInfo, de la forma HttpRequest $request->getPathInfo()
     */
    public function getUrlRedireccionLoginDefecto($pathInfo = false)
    {
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        if(!$pathInfo){
            $modulo = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_MODULO');
            $controlador = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_CONTROLADOR');
            $accion = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_ACCION');
            return array($modulo, $controlador, $accion);
        }else{
            return $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_PATH');
        }
    }
}
?>