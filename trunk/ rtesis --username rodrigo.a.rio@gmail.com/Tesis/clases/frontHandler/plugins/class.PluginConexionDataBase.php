<?php
/**
 * Establece una nueva conexion desde parametros estaticos y la asigna a los controladores de modelo.
 * Si no se puede establecer una conexion elimina el resto de los plugins y setea en el request una redireccion.
 * 
 * @author Matias Velilla
 */
class PluginConexionDataBase extends PluginAbstract
{
    private static $connected = false;

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
            ComunidadController::getInstance()->setDBDriver($oDB);
            AdminController::getInstance()->setDBDriver($oDB);
            IndexController::getInstance()->setDBDriver($oDB);
            SeguimientosController::getInstance()->setDBDriver($oDB);
            self::$connected = true;           
        }catch(Exception $e){                       
            self::$connected = false;
            //saco los plugins que no quiero que ya no quiero que se ejecuten
            $front->unregisterPlugin('PluginRedireccion404')
                  ->unregisterPlugin('PluginPermisos')
                  ->unregisterPlugin('PluginRedireccionAccionDesactivada')
                  ->unregisterPlugin('PluginCache');
        }
    }

    public function preDispatch(HttpRequest $request)
    {
        if(!self::$connected)
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

    /**
     * Este flag estatico es para que las distintas clases puedan modificar su comportamiento
     * dependiendo si existe conexion a la base de datos o no.
     * 
     * @return boolean
     */
    public static function isConnected()
    {
        return self::$connected;
    }
}