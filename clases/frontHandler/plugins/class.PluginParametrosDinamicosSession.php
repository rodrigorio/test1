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
    const SEGUNDOS_EXPIRACION_CONTROLADORES = 600; //600 = 10 minutos
    const SEGUNDOS_EXPIRACION_SISTEMA = 600;

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

    /**
     * En sistemas donde el usuario pueda setear sus parametros, aca hay que cargar tambien los valores de los parametros de usuario.
     */
    public function cargarParametrosDinamicos()
    {        
        $grupoControlador = $this->getGrupoControladorParametro();
        //si existieran de usuario $grupoUsuario = $this->getGrupoUsuarioParametro y llamo a metodo de SysController que extraiga valores de parametro con relacion a la tabla usuarios =).

        if(!empty($grupoControlador) && !isset($this->parametrosDinamicos->{$grupoControlador}))
        {
            $array = SysController::getInstance()->obtenerParametrosControlador($grupoControlador);
            if(!empty($array)){
                $this->parametrosDinamicos->{$grupoControlador} = $array;
                //luego de $seconds segundos se borran los parametros forzando a que se vuelvan a buscar en DB.
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_CONTROLADORES, $grupoControlador);
            }
        }

        if(!isset($this->parametrosDinamicos->sistema))
        {
            $array = SysController::getInstance()->obtenerParametrosControlador(self::GRUPO_SISTEMA);
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