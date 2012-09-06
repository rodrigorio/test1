<?php
/**
 * Estrategia para manejar parametros dinamicos solo con arrays y directamente con la base de datos sin
 * utilizar un almacen intermedio en base de datos.
 *
 * @author Matias Velilla
 */
class PluginParametrosDinamicosDB implements PluginParametrosDinamicosStrategy
{
    /**
     * Nombre del controlador general para todo el sistema (corresponde con el contenido de los registros de la DB en tabla 'controladores_pagina')
     */
    const GRUPO_SISTEMA = 'sistema';
    
    /**
     * Array con los valores de los parametros que se guardan en DB
     * 
     * @var array
     */
    private $parametrosDinamicos = array();

    private $request;

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
     * Carga todos los parametros dinamicos, los de entidad, los de controlador y los de sistema
     */
    public function cargarParametrosDinamicos()
    {
        $grupoControlador = $this->getGrupoControladorParametro();
        list($grupoUsuario, $iUsuarioId) = $this->getGrupoUsuarioParametro();
               
        if(!empty($grupoControlador))
        {
            $array = SysController::getInstance()->obtenerParametrosControlador($grupoControlador);
            if(!empty($array)){
                $this->parametrosDinamicos[$grupoControlador] = $array;
            }
        }

        if(!empty($grupoUsuario) && !empty($iUsuarioId))
        {
            $array = SysController::getInstance()->obtenerParametrosUsuario($iUsuarioId);
            if(!empty($array)){
                $this->parametrosDinamicos[$grupoUsuario] = $array;
            }
        }

        $array = SysController::getInstance()->obtenerParametrosSistema();
        if(!empty($array)){
            $this->parametrosDinamicos[self::GRUPO_SISTEMA] = $array;
        }
    }

    /**
     * Obtener el valor de un parametro a partir de grupo (por ejemplo un grupo de parametros correspondiente a un controlador)
     * y key, es la clave del parametro
     */
    public function obtenerParametroDinamico($grupo, $key)
    {
        if(isset($this->parametrosDinamicos[$grupo][$key])){
            return $this->parametrosDinamicos[$grupo][$key];
        }else{
            return null;
        }
    }
}