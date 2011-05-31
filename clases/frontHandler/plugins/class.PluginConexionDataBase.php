<?php
/**
 * Establece una nueva conexion desde parametros estaticos y la asigna a los controladores de modelo.
 * Si no se puede establecer una conexion elimina el resto de los plugins y setea en el request una redireccion.
 * 
 * @author Matias Velilla
 */
class PluginConexionDataBase extends PluginAbstract
{
    private $connected = false;

    public function routeStartup(HttpRequest $request)
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $driver = $parametros->obtener('DATABASE_DRIVER');
        $host = $parametros->obtener('DATABASE_HOST');
        $user = $parametros->obtener('DATABASE_USER');
        $pass = $parametros->obtener('DATABASE_PASSWORD');
        $dbName = $parametros->obtener('DATABASE_NAME');
        $port = $parametros->obtener('DATABASE_PORT');
        $autoCommit = $parametros->obtener('DATABASE_AUTOCOMMIT');

        try{
            $oDB = DriversFactory::getInstace($driver, $host, $user, $pass, $dbName, $port, $autoCommit);
            SysController::getInstance()->setDBDriver($oDB);
            BlogController::getInstance()->setDBDriver($oDB);
            AdminController::getInstance()->setDBDriver($oDB);
            $this->connected = true;
        }catch(Exception $e){
            $this->connected = false;
            //saco los plugins que no quiero que se ejecuten en el predispatch()
            $front->unregisterPlugin('PluginRedireccion404')
                  ->unregisterPlugin('PluginPermisos')
                  ->unregisterPlugin('PluginRedireccionDesactivada')
                  ->unregisterPlugin('PluginCache')
                  ->unregisterPlugin('PluginParametros');

            //evito el ruteo porque eligo la vista desde el preDispatch del plugin.
            $request->setParam('noRutear', true);
        }
    }

    public function preDispatch(HttpRequest $request)
    {
        if(!$this->isConnected())
        {
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $modulo = $parametros->obtener('ERROR_DB_REDIRECICON_MODULO');
            $controlador = $parametros->obtener('ERROR_DB_REDIRECICON_CONTROLADOR');
            $accion = $parametros->obtener('ERROR_DB_REDIRECICON_ACCION');
            $request->setModuleName($modulo)
                    ->setControllerName($controlador)
                    ->setActionName($accion);
        }
    }

    public function isConnected()
    {
        return $this->connected;
    }
}