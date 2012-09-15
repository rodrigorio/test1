<?php


/**
 * Description of classIntegranteInactivo
 *
 * @author Andres
 */
class IntegranteInactivo extends PerfilAbstract
{
    const PERFIL_INTEGRANTE_INACTIVO_ID = 3;
    const PERFIL_INTEGRANTE_INACTIVO_DESCRIPCION = 'integrante inactivo';

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
        if(empty($this->iId)){ $this->iId =  self::PERFIL_INTEGRANTE_INACTIVO_ID; }
        if(empty($this->sDescripcion)){ $this->sDescripcion = self::PERFIL_INTEGRANTE_INACTIVO_DESCRIPCION; }
    }

    /**
     * Devuelve Modulo/Controlador/Accion a la cual se debe redirigir el sistema luego de que un usuario solicita una accion
     * a la cual no tiene permiso ó esta desactivada.
     *
     * Esta funcion debe ser utilizada SOLO CON USUARIOS QUE SE ENCUENTREN LOGEADOS.
     * Si un visitante solicita una accion restringida se debe redireccionar SIEMPRE a login.
     *
     * @return array|string 1)Modulo 2)Controlador 3) Accion o pathInfo, de la forma HttpRequest $request->getPathInfo()     *
     */
    public function getUrlRedireccion($pathInfo = false)
    {
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        if(!$pathInfo){
            $modulo = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCION_MODULO');
            $controlador = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCION_CONTROLADOR');
            $accion = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCION_ACCION');
            return array($modulo, $controlador, $accion);
        }else{
            return $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCION_PATH');
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
            $modulo = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_MODULO');
            $controlador = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_CONTROLADOR');
            $accion = $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_ACCION');
            return array($modulo, $controlador, $accion);
        }else{
            return $parametros->obtener('PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_PATH');
        }
    }

    public function isIntegranteInactivo(){ return true; }
}