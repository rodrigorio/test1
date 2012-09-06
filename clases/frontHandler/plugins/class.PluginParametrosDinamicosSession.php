<?php
/**
 * Estrategia para manejar parametros dinamicos con Session
 *
 * @author Matias Velilla
 */
class PluginParametrosDinamicosSession implements PluginParametrosDinamicosStrategy
{   
    /**
     * Cantidad de segundos antes de que expiren los parametros dinamicos
     */
    const SEGUNDOS_EXPIRACION_CONTROLADORES = 30; //600 = 10 minutos
    const SEGUNDOS_EXPIRACION_SISTEMA = 30;
    const SEGUNDOS_EXPIRACION_USUARIOS = 30;

    /**
     * Nombre del controlador general para todo el sistema (corresponde con el contenido de los registros de la DB en tabla 'controladores_pagina')
     */
    const GRUPO_SISTEMA = 'sistema';

    /**
     * SessionNamespace
     */
    private $parametrosDinamicos;

    private $request;

    public function __construct()
    {
        $this->parametrosDinamicos = new SessionNamespace('parametrosDinamicos');
    }

    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }

    private function getGrupoControladorParametro()
    {        
        $modulo = $this->request->getModuleName();
        $controlador = $this->request->getControllerName();
        if(empty($modulo)||empty($controlador)){
            return "";
        }else{
            return $modulo.'_'.$controlador;
        }
    }

    private function getGrupoUsuarioParametro()
    {
        $grupoUsuario = "";
        $iUsuarioId = "";

        if(!Session::isDestroyed()){
            if(null !== SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()){
                $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
                if(!empty($iUsuarioId)){
                    $grupoUsuario = 'user-'.$iUsuarioId;
                }
            }
        }

        return array($grupoUsuario, $iUsuarioId);
    }

    /**
     * En sistemas donde el usuario pueda setear sus parametros, aca hay que cargar tambien los valores de los parametros de usuario.
     */
    public function cargarParametrosDinamicos()
    {        
        $grupoControlador = $this->getGrupoControladorParametro();
        list($grupoUsuario, $iUsuarioId) = $this->getGrupoUsuarioParametro();

        if(!empty($grupoControlador) && !isset($this->parametrosDinamicos->{$grupoControlador}))
        {
            $array = SysController::getInstance()->obtenerParametrosControlador($grupoControlador);
            if(!empty($array)){
                $this->parametrosDinamicos->{$grupoControlador} = $array;
                //luego de $seconds segundos se borran los parametros forzando a que se vuelvan a buscar en DB.
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_CONTROLADORES, $grupoControlador);
            }
        }

        if(!empty($grupoUsuario) && !empty($iUsuarioId) && !isset($this->parametrosDinamicos->{$grupoUsuario}))
        {
            $array = SysController::getInstance()->obtenerParametrosUsuario($iUsuarioId);
            if(!empty($array)){
                $this->parametrosDinamicos->{$grupoUsuario} = $array;
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_USUARIOS, $grupoUsuario);
            }
        }

        if(!isset($this->parametrosDinamicos->sistema))
        {
            $array = SysController::getInstance()->obtenerParametrosSistema();
            if(!empty($array)){
                $this->parametrosDinamicos->sistema = $array;
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_SISTEMA, self::GRUPO_SISTEMA);
            }
        }
    }

    public function obtenerParametroDinamico($grupo, $key)
    {
        if(isset($this->parametrosDinamicos->{$grupo}[$key])){
            return $this->parametrosDinamicos->{$grupo}[$key];
        }else{
            return null;
        }
    }
}