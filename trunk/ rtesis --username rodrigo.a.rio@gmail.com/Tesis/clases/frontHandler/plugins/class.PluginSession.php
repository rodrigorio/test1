<?php
/**
 * Este plugin es complejo y necesita un test mas delicado.
 *
 * En un futuro hay que analizar la posibilidad de guardar la sesion en memcache en lugar de en el server
 * (Si es que el sitio no va a correr en un servidor nuestro, esto da mas seguridad)
 * Para tal funcionalidad hay que setear un SaveHandler customizado.. en la web hay varios dando vueltas para memcache.
 *
 * @author Matias Velilla
 */
class PluginSession extends PluginAbstract
{
    const MENSAJE_ERROR_SESSION = 'Se produjo un error en la sesion activa.';
    const MENSAJE_ERROR_SESSION_LOGIN = 'Se produjo un error en la sesion activa, debe volver a identificarse.';
    
    /**
     * Se utiliza para guardar una copia de la clasa de la instancia de PerfilAbstract identificado antes de destruir la session
     * @param string $classPerfil
     */
    private $classPerfilAnterior;

    private function startSession()
    {
        try{           
            //A partir del 2do request puede tirar excepcion si el userAgent cambia segun SessionValidator.
            Session::start();
            
            //regeneracion de id: ver (http://framework.zend.com/manual/en/zend.session.global_session_management.html)            
            $defaultNamespace = new SessionNamespace(); //se crea en espacio por defecto
            if (!isset($defaultNamespace->initialized)) {
                Session::regenerateId();
                $defaultNamespace->initialized = true;
            }           
            Session::registerValidator( new SessionValidator() );
        } catch (Exception $e){
            throw $e;
        }
    }

    private function cargarPerfilDefecto()
    {
        SessionAutentificacion::getInstance()->cargarAutentificacion(SysController::getInstance()->obtenerPerfilDefecto());
    }

    static function destruirSesion(){
        //destruyo la session actual
        $remove_cookie = true;
        $readonly_namespaces = true;
        Session::destroy($remove_cookie, $readonly_namespaces);        
    }
   
    public function routeStartup(HttpRequest $request)
    {
        try{
            $this->startSession();
        }catch (Exception $e){

            //si habia un perfil cargado identificado en session lo obtengo para extraer la clase (Visitante, Administrador, etc).
            $perfilAbstract = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            if (null !== $perfilAbstract){
                //obtengo la clase concreta
                $this->classPerfilAnterior = get_class($perfilAbstract);
            }

            self::destruirSesion();
            
            //no tiro excepcion para que se carguen parametros estaticos y se conecte a la DB pero en el predispatch
            //no quiero que se ejecuten cosas innecesarias ya que se destruyo la session: se redirecciona a la home o a login.
            FrontController::getInstance()->unregisterPlugin('PluginRedireccion404')
                                          ->unregisterPlugin('PluginPermisos')
                                          ->unregisterPlugin('PluginRedireccionAccionDesactivada')
                                          ->unregisterPlugin('PluginCache');            
        }
    }

    public function preDispatch(HttpRequest $request)
    {
        if(!Session::isDestroyed()){
            if(!SessionAutentificacion::getInstance()->estaIdentificado()){
                $this->cargarPerfilDefecto();
            }
        }else{
            $this->getResponse()->setRawHeader('HTTP/1.0 401 Unauthorized');
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros'); 
            $soloSistema = true;
            //si la session se destruyo con un perfil autentificado que no era el perfil por defecto pido que se autentifique nuevamente
            if( !empty($this->classPerfilAnterior) && $this->classPerfilAnterior != 'Visitante'){
                $request->setModuleName('index')
                        ->setControllerName('login')
                        ->setActionName('index')
                        ->setParam('msgError', self::MENSAJE_ERROR_SESSION_LOGIN);
            }else{
                //redirecciono al home del sitio
                $homeSitioModulo = $parametros->obtener('HOME_SITIO_MODULO', $soloSistema);
                $homeSitioControlador = $parametros->obtener('HOME_SITIO_CONTROLADOR', $soloSistema);
                $homeSitioAccion = $parametros->obtener('HOME_SITIO_ACCION', $soloSistema);
                
                $request->setModuleName($homeSitioModulo)
                        ->setControllerName($homeSitioControlador)
                        ->setActionName($homeSitioAccion)
                        ->setParam('msgError', self::MENSAJE_ERROR_SESSION);
            }
        }
    }
}