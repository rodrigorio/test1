<?php
/**
 * Este plugin debe ser de los mas sencillos (porque esta resuelto de manera inteligente ;) jajaj )
 *
 * Cada PageController tiene una accion abstracta para mostrar 404 (puede ser redeclarada para 404 customizados)
 *
 * Tanto en preDispatch como en postDispatch se checkea Modulo y Controlador ruteados por Router.
 * Si esta seteado el codigo 404 se redirecciona a la pagina 404 del controlador del request.
 *
 * Si el router en cambio no hizo ningun MATCH entonces modulo y accion son los que esten declarados en parametros de sistema
 * como el home del sitio para visitantes.
 *
 * Si el 404 es postDispatch se tiene que poner como siempre el dispatched en falso
 * y se tiene que limpiar el response para no caer en un ciclo infinito en el repeat del Front.
 *
 * @author Matias Velilla
 */
class PluginRedireccion404 extends PluginAbstract
{
    /**
     * Esta accion la poseen todos los controladores de pagina. Por defecto imprimen el 404 que se declara en el metodo de la clase abstracta
     */
    const NOMBRE_ACCION_404 = "redireccion404";

    /**
     * Deja exactamente el mismo modulo y controlador en el request pero la accion se reemplaza por la de mostrar pagina de 404. =)
     *
     * @return boolean Dependiendo si efectivamente redirecciono o no.
     */
    private function requestRedireccionar404()
    {
        $front = FrontController::getInstance();
        //me fijo si efectivamente hubo error 404
        if($this->getResponse()->hasExceptionOfCode(404))
        {
            //seteo header 404
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');

            //Puede que el router no haya matcheado ninguna ruta por lo que agrego modulo y controlador del home del sitio
            try{
                $front->getRouter()->getCurrentRoute();
            }catch(Exception $e){
                $parametros = $front->getPlugin('PluginParametros');
                $soloSistema = true;
                $this->getRequest()->setModuleName($parametros->obtener('HOME_SITIO_MODULO', $soloSistema));
                $this->getRequest()->setControllerName($parametros->obtener('HOME_SITIO_CONTROLADOR', $soloSistema));
            }
            $this->getRequest()->setActionName(self::NOMBRE_ACCION_404);
            //si la excepcion tenia mensaje lo adjunto al request
            $msg = $this->getResponse()->getMessagesByCode(404);
            if(!empty($msg)){
                $this->getRequest()->setParam('msgInfo', $msg);
            }

            $front->cleanResponseExceptions(404);
            return true;
        }
        return false;
    }

    public function preDispatch(Request $request)
    {
        //en este caso analizo tambien el caso de una posible peticion ajax
        if($request->isXmlHttpRequest())
        {
            if($this->getResponse()->hasExceptionOfCode(404))
            {
                $request->setModuleName('index')
                        ->setControllerName('index')
                        ->setActionName('ajaxError')
                        ->setParam('msgError', 'La accion que solicito no existe')
                        ->setParam('codigoError', '404');
                FrontController::getInstance()->cleanResponseExceptions(404);
            }
            return;
        }

        //si no es peticion ajax procedo a una redireccion normal.
        $this->setRequest($request);
        $this->requestRedireccionar404();
    }

    public function postDispatch(Request $request)
    {
        $this->setRequest($request);
        if($this->requestRedireccionar404()){
            $request->setDispatched(false);
        }
    }
}
