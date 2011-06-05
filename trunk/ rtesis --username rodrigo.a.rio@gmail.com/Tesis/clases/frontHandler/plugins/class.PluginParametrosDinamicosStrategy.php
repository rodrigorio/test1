<?php
/**
 * Interface strategy para manejar parametro dinamicos en PluginParametros
 *
 * Por el momento solo dos, una que maneja los parametros en session y consulta a DB
 * otra que solo hace consultas a DB y no utiliza Session.
 *
 * En el futuro puede haber otras formas de setear configuraciones en el sistema (.xml, etc).
 *
 * @author Matias Velilla
 */
interface PluginParametrosDinamicosStrategy
{
    public function setRequest(HttpRequest $request);

    /**
     * Carga todos los parametros dinamicos, los de entidad, los de controlador y los de sistema.
     */
    public function cargarParametrosDinamicos();

    /**
     * Obtener el valor de un parametro a partir de grupo (por ejemplo un grupo de parametros correspondiente a un controlador)
     * y key, es la clave del parametro
     */
    public function obtenerParametroDinamico($grupo, $key);
}