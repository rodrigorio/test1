<?php

/**
 *
 *
 *
 */
class Administrador extends PerfilAbstract
{
    const PERFIL_ADMINISTRADOR_ID = 1;
    const PERFIL_ADMINISTRADOR_DESCRIPCION = 'administrador';

    public function __construct(stdClass $oParams = null){
        parent::__construct();

        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if(is_array($vArray)){
            foreach($vArray as $varName => $value){
                if(array_key_exists($varName,$vThisVars)){
                    $this->$varName = $value;
                }else{
                    throw new Exception("Unknown property $varName in "  . __CLASS__,-1);
                }
            }
        }

        //si el stdClass no tenia los atributos principales los seteo por constante
        if(empty($this->iId)){ $this->iId =  self::PERFIL_ADMINISTRADOR_ID; }
        if(empty($this->sDescripcion)){ $this->sDescripcion = self::PERFIL_ADMINISTRADOR_DESCRIPCION; }
    }
    
    /**
     * Se supone que el administrador no tiene redirecciones por permisos =)
     * tiro excepcion para tener una guia al programar, si se desactiva desde index.php no sale nada.
     */
    public function getUrlRedireccion($pathInfo = false)
    {
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        if(!$pathInfo){
            $modulo = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCION_MODULO');
            $controlador = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCION_CONTROLADOR');
            $accion = $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCION_ACCION');
            return array($modulo, $controlador, $accion);
        }else{
            return $parametros->obtener('PERFIL_ADMINISTRADOR_REDIRECCION_PATH');
        }        
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

    public function isAdministrador(){
        return true;
    }
}