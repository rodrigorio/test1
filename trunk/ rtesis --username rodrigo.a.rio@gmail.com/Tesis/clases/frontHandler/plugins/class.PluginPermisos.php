<?php

/**
 * Este plugin puede ser considerado como seguridad de "bajo nivel" ya que un usuario utilizando el sistema en condiciones normales
 * no va a acceder a acciones que no dispone visualmente. En teoria puedo "hacer click" solo en las acciones que tengo permitidas.
 *
 * @author Matias Velilla
 */
class PluginPermisos extends PluginAbstract
{
    const MENSAJE_SE_SOLICITA_LOGIN = 'Debes autentificarte para acceder';
    const MENSAJE_ACCION_DENEGADA = 'No tienes permiso para acceder a la accion';
    
    /**
     * Esta funcion es para redirecciones que no sean desde peticiones Ajax.
     *
     * Importante: Conceptualmente SOLO se establece una nueva ruta, no hay un header location de redireccion,
     *             en el postDispatch hay que setear dispatched = false para que efecticamente se muestre la nueva vista.
     *
     * @param String $msg. Mensaje que se va a mostrar en la pagina a la que se redirecciona luego del error.
     */
    private function redireccionarRuta($mensajeAccionDenegada = self::MENSAJE_ACCION_DENEGADA, $mensajeSolicitaLogin = self::MENSAJE_SE_SOLICITA_LOGIN)
    {
        $this->getResponse()->setRawHeader('HTTP/1.0 401 Unauthorized');
        SessionAutentificacion::getInstance()->realizoLogin(true);
        if(!SessionAutentificacion::getInstance()->realizoLogin()){
            //no es ajax y no hizo login redirecciono a login
            //luego de iniciar trata de ir al request original que pidio el client.
            $this->getRequest()->setModuleName('index')
                               ->setControllerName('login')
                               ->setActionName('index')
                               ->setParam('msgInfo', $mensajeSolicitaLogin)
                               ->setParam('codigoError', '401');
        }else{
            //si ya se hizo login y no tiene permiso redirecciono. (a que lugar depende el perfil, se determina por parametro).
            //con esto tengo la flexibilidad de enviar a completar datos de perfil a un usuario inactivo, etc.
            list($modulo, $controlador, $accion) = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccion();
            $this->getRequest()->setModuleName($modulo)
                               ->setControllerName($controlador)
                               ->setActionName($accion)
                               ->setParam('msgInfo', $mensajeAccionDenegada)
                               ->setParam('codigoError', '401');
        }
    }
    
    public function preDispatch(HttpRequest $request)
    {
        $this->setRequest($request);
        
        //Se asume que desde el plugin de session existe por lo menos un perfil por defecto si no se logeo el usuario
        //me fijo si tiene permiso para ejecutar la funcion
        if(!SessionAutentificacion::getInstance()->obtenerIdentificacion()->tiene($request->getKeyPermiso()))
        {
            //si es ajax devuelvo codigo 401 solo x parametro, no cambian los headers porque no hay una nueva petision http
            //el javascript despues se encarga del mensaje. la accion esta en controlador Index.
            if($request->isXmlHttpRequest())
            {
                $request->setModuleName('index')
                        ->setControllerName('index')
                        ->setActionName('ajaxError')
                        ->setParam('msgInfo', self::MENSAJE_ACCION_DENEGADA)
                        ->setParam('codigoError', '401');
                return;
            }

            //si no es peticion ajax redirecciono normalmente
            $this->redireccionarRuta();
        }
    }

    /**
     * Toma la excepcion desde el response y se fija si es codigo 401.
     *
     * Si efectivamente se intento realizar una accion sobre un id o alguna entidad
     * sobre la cual no se podia ejecutar la accion, entonces redirecciona con un mensaje de advertencia.
     * (por ejemplo si un usuario quiere eliminar algo que el no creo)
     * Puede tener el permiso para eliminar, pero no para eliminar un id que no le pertenece.
     *    
     * @param HttpRequest $request
     */
    public function postDispatch(HttpRequest $request)
    {
        if($this->getResponse()->hasExceptionOfCode(401))
        {
            $this->setRequest($request);
            
            //El mensaje se captura desde la excepcion arrojada por la accion del pageController
            $this->redireccionarRuta($response->getMessagesByCode(401));

            //seteo como no despachada el request para que muestre la nueva vista
            $request->setDispatched(false);

            //limpio las excepciones del response. (Sino cae en un ciclo infinito)
            FrontController::getInstance()->cleanResponseExceptions(401);
        }
    }
}