<?php

/**
 * A este plugin se llega con la 'seguridad' de que el perfil tiene permiso para ejecutar la accion.
 * Sin embargo, la accion puede estar desactivada y debe cancelarse con su respectivo mensaje.
 *
 * Notar la sutil diferencia con el plugin de permisos. Eventualmente los botones en las vistas APARECERAN para que el usuario
 * seleccione una accion permitida, pero la accion puede desactivarse y entonces la redireccion tiene que ser diferente:
 *
 * Se emplea un mecanismo con namespace que guarda el ultimo request visitado. Luego, si se accede a una accion desactivada
 * se redirecciona a la url anterior en la que estaba parado. Si el namespace se encuentra vacio entonces se redirecciona a traves del
 * metodo del perfil getUrlRedireccion().
 *
 * LAS REDIRECCIONES SON 302, IMAGINENSE QUE SI SE DESACTIVA UN MODULO DE LA PAGINA DE LOS VISITANTES SE TIENE QUE DAR A ENTENDER
 * QUE EN UN FUTURO SE VA A PODER ACCEDER A LA ACCION, Y QUE ESTA DESACTIVADA MOMENTANEAMENTE.
 * 
 * Nuevamente la peticion puede ser Ajax, en tal caso no hay redireccion sino que se devuelve codigo de error con mensaje al .js
 *
 * Es conceptualmente correcto NO DESACTIVAR la vista que corresponde a la home de un modulo,
 * en todo caso se desactiva el modulo entero con el parametros de sistema utilizado para tal caso.
 * No esta de mas decirlo, NO TIENE SENTIDO DESACTIVAR EL MODULO DE ADMINISTRADOR.
 *
 * NOTA: hay funciones del modulo index controlador index que no pueden ser deshabilitadas
 *       (las de error de ajax o las de sitio fuera de linea, etc)
 *
 * @TODO acordarse de hacer un login de 'emergencia' solo para administradores en /admin/ en caso que todos los demas modulos se desactiven
 *
 * @author Matias Velilla
 */
class PluginRedireccionAccionDesactivada extends PluginAbstract
{
    const MENSAJE_ACCION_DESACTIVADA = 'La accion se encuentra desactivada momentaneamente.';
    const MENSAJE_MODULO_DESACTIVADO = 'El modulo se encuentra desactivado momentaneamente.';

    /**
     * @var SessionNamespace
     */
    private $historial;

    /**
     * En casos especiales cuando se desee desde cualquier parte la ultima vista procesada con exito por el usuario actual.
     */
    private static $lastRequestUri;

    /**
     * Tener en cuenta que el constructor se ejecuta en el bootstrap file (index.php) antes de siquiera entrar al dispatch() del Front.
     * Por lo tanto tengo que fijarme si el plugin de session (que se ejecuta primero) destruyo la session.
     * Este es un caso excepcional debido a que es un plugin. En el resto de los casos no llega a existir la necesidad de comprobar
     * el estado de la session porque si es destruida el plugin automaticamente redirecciona.
     */
    public function __construct()
    {                       
        if(!Session::isDestroyed()){
            $this->historial = new SessionNamespace('historial');
        }

        //si existe guardo desde el comienzo para que este disponible a lo largo de la ejecucion del request
        if(isset($this->historial->request_uri)){
           self::$lastRequestUri = $this->historial->request_uri;
        }
    }

    /**
     * Devuelve el ultimo RequestUri de la ultima vista mostrada con exito. Si el historial esta vacio devuelve cadena vacia.
     */
    public static function getLastRequestUri()
    {       
        return self::$lastRequestUri;
    }

    /**
     * Utilizado para guardar una copia de la ultima vista a la que se accedio.
     * Extrae Modulo, Controlador, Accion, Parametros y los guarda en variables de session.
     * Este metodo se utiliza en dispatchLoopShutdown() porque no quiero guardar una accion como 'eliminar', 'descargarArchivo', etc, etc.
     * Se supone que los valores guardados por esta funcion seran utilizados para redireccionar luego de que el usuario
     * accede a una accion que esta desactivada en el metodo preDistpatch() de este mismo plugin.
     *
     * Tambien guardo el REQUEST_URI para devolver con metodo estatico de clase por si se necesita en alguna parte del sistema =)
     * (por ejemplo para volver a la pagina anterior en un 404)
     */
    private function guardarVista()
    {               
        $request = FrontController::getInstance()->getRequest();

        //solo guarda el ultimo requestUri si fue una ruta que el router pudo procesar
        //de lo contrario si alguien pone una ruta del tipo www.mipagina.com/asdkjaskjdASDSss se guardaria
        //por mas que luego se termine en una pagina 404 es preferible que se redireccione por defecto segun perfil.
        if($request->getActionName() != 'redireccion404'){
            $this->historial->request_uri = $request->getRequestUri();
        }

        //luego guardo la informacion del nuevo request en el historial
        $this->historial->modulo = $request->getModuleName();
        $this->historial->controlador = $request->getControllerName();
        $this->historial->accion = $request->getActionName();

        //Por ultimo guardo los parametros que acompañaban a este request.
        //NOTA IMPORTANTE: 
        //Cuando se hace un getParam($key) al request, internamente se fija en los parametros de clase y en los $_GET y $_POST del request
        //Al pasar todos los parametros a sesion a traves de getParams() se unen TODOS los parametros:
        //Los internos (Zend los llama userland parameters) + los $_GET + los $_POST. Todos en un solo array (tienen prioridad los internos).
        //En ultima instancia, para el metodo cliente que ejecute getParam($key) en el request luego de una redireccion sera transparente
        //y no habra diferencia. La diferencia es solo interna, cuando se haga una redireccion al ultimo request guardado en sesion
        //los parametros seran todos internos (cuando posiblemente en el request original algunos fueran de $_GET o $_POST). Very tricky uh?
        //$this->historial->params = array();
        //borro los parametros de codigo de error y de mensaje antiguamente usados porque estarian desactualizados
        //luego de una posible redireccion
        $request->setParam('codigoError', null)
                 ->setParam('msgInfo', null)
                 ->setParam('msgError', null)
                 ->setParam('msgCorrecto', null);
        //guardo los parametros en el historial
        $this->historial->params = $request->getParams();

        return $this;
    }

    /**
     * Atencion, el apuntador a $request es al que llega por preDispatch() no tiene nada que ver con el que se guarda en
     * el evento dispatchLoopShutdown().
     * 
     * @param HttpRequest $request
     * @return boolean Si pudo pisar con los valores del historial
     */
    private function rutearUltimaVista(){
        $request = $this->getRequest();
        
        //si poseo informacion en el historial obtengo los datos de sesion y se los copio al request nuevo.
        if(isset($this->historial->modulo) && isset($this->historial->controlador) && isset($this->historial->accion))
        {            
            $request->setModuleName($this->historial->modulo);
            $request->setControllerName($this->historial->controlador);
            $request->setActionName($this->historial->accion);
            if(!empty($this->historial->params)){
                $request->setParams($this->historial->params);
            }
            return true;
        }
        
        //si no se modifico el request se devuelve falso
        return false;
    }

    /**
     * Limpia el historial almacenado
     */
    private function limpiarHistorial()
    {
        $this->historial->unsetAll();
        return $this;
    }

    /**
     * Se fija si el modulo del request procesado esta desactivado
     */
    private function esModuloActivo($moduleName)
    {
        $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
        $moduleName = strtoupper($moduleName);
        $key = "ACTIVO_MODULO_".$moduleName;
        $soloSistema = true;
        return $parametros->obtener($key, $soloSistema);
    }

    private function redireccionarSitioFueraDeLinea()
    {
        $this->getResponse()->setHttpResponseCode(302);
        $this->getRequest()->setModuleName('index')
                           ->setControllerName('index')
                           ->setActionName('sitioOffLine')
                           ->setParam('codigoRedireccion', '302');
    }
    
    /**
     * Como es un algoritmo que tiene que tener en cuenta muchas condiciones voy fijandome de lo mas general a lo mas particular.
     *
     * Si no cumple ninguna de las condiciones que redireccionan a request entonces el metodo llega al final sin modificar el request.
     * 
     * @param HttpRequest $request
     * 
     */
    public function preDispatch(HttpRequest $request)
    {        
        //seteo el request para que puedan utilizar los metodos privados del plugin
        $this->setRequest($request);

        //obtengo la vista del home del sitio
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $moduloHome = $parametros->obtener('HOME_SITIO_MODULO', true);
        $controladorHome = $parametros->obtener('HOME_SITIO_CONTROLADOR', true);
        $accionHome = $parametros->obtener('HOME_SITIO_ACCION', true);
        
        //guardo el perfil (estoy en el preDispatch al menos esta cargado el perfil de los visitantes)
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        //guardo el flag que indica si el modulo del request actual esta activo
        $moduloActivo = $this->esModuloActivo($request->getModuleName());
        //guardo tambien el flag que indica si el modulo de la home del sitio esta activo
        $moduloHomeActivo = $this->esModuloActivo($moduloHome);      
        //guardo el flag acerca de si el perfil autentificado tiene permiso para despachar la accion del request actual
        $accionActivo = $perfil->activo($request->getKeyPermiso());        

        //primero contemplo Ajax: Si el modulo del request actual esta deshabilitado o la accion esta deshabilitada tiro error 401 al .js
        if($request->isXmlHttpRequest() && (!$moduloActivo || !$accionActivo))
        {
            //diferente mensaje dependiendo que era lo que estaba desactivado.
            $msg = (!$moduloActivo)?self::MENSAJE_MODULO_DESACTIVADO:self::MENSAJE_ACCION_DESACTIVADA;
            $request->setModuleName('index')
                    ->setControllerName('index')
                    ->setActionName('ajaxError')
                    ->setParam('msgInfo', $msg)
                    ->setParam('codigoError', '401');
            return;
        }
        
        //si el modulo del request actual es el modulo de la home de los visitantes y esta desactivado
        //redirecciono a sitio fuera de linea.
        if($request->getModuleName() == $moduloHome && !$moduloActivo)
        {
            $this->redireccionarSitioFueraDeLinea();
            return;
        }
        
        //si el modulo del request actual es distinto del modulo de la home y esta desactivado
        //y tambien esta desactivado el modulo de la home redirecciono a sitio fuera de linea.
        if($request->getModuleName() != $moduloHome && !$moduloActivo && !$moduloHomeActivo)
        {
            $this->redireccionarSitioFueraDeLinea();
            return;
        }

        //si el modulo del request actual es distinto del modulo de la home y esta desactivado
        //pero la home esta activada redirecciono a la home del sitio
        if($request->getModuleName() != $moduloHome && !$moduloActivo && $moduloHomeActivo)
        {
            $this->getResponse()->setHttpResponseCode(302);
            $request->setModuleName($moduloHome)
                    ->setControllerName($controladorHome)
                    ->setActionName($accionHome)
                    ->setParam('msgInfo', self::MENSAJE_MODULO_DESACTIVADO)
                    ->setParam('codigoRedireccion', '302');
            return;
        }

        //De aca en mas ya estamos seguros de que el modulo esta activo.
        //Si la accion esta desactivada y coincide con la home tiro excepcion (si estan habilitadas en el FrontController) y retorno.
        if(!$accionActivo && $request->getKeyPermiso() == $moduloHome."_".$controladorHome."_".$accionHome)
        {
            if($front->throwExceptions()){
                throw new Exception("La accion desactivada corresponde con el home del sitio, no puede estar desactivada!,
                                    en tal caso utilizar el parametro de sistema que desactiva el modulo entero");
            }
            return;
        }

        //Si la accion en particular del request esta deshabilitada
        //trato de ir a la accion guardada en el historial.
        //Si no hay ninguna accion guardada ejecuto redireccion 302 dependiendo el perfil.
        if(!$accionActivo)
        {
            $this->getResponse()->setHttpResponseCode(302);
            if(!$this->rutearUltimaVista())
            {
                list($modulo, $controlador, $accion) = $perfil->getUrlRedireccion();
                $request->setModuleName($modulo)
                        ->setControllerName($controlador)
                        ->setActionName($accion);
            }
            //se haya redireccionado a la vista guardada en el historial o la vista de redireccion por defecto se agrega siempre mensaje y cod.
            $request->setParam('msgInfo', self::MENSAJE_ACCION_DESACTIVADA)
                    ->setParam('codigoRedireccion', '302');
            return;
        }               
    }

    public function dispatchLoopShutdown()
    {        
        //solo guardo cuando la peticion NO es ajax
        if(!FrontController::getInstance()->getRequest()->isXmlHttpRequest()){
            //limpio la informacion anterior y guardo la nueva        
            $this->limpiarHistorial()
                 ->guardarVista();
        }
    }
}