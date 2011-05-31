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
 * NOTA: QUE PASA SI UN VISITANTE ACCEDE POR EJEMPLO A "ADMIN/PUBLICACIONES/AKLDJASLDKJASLKDJ/" (UNA DIRECCION Q NO EXISTE PERO DENTRO DE ADMIN)
 *       EN TAL CASO 404 DETECTA QUE LA PAGINA NO EXISTE Y RUTEA A REQUEST A: ADMIN_PUBLICACIONES_REDIRECCION404 (ACCION FINAL)
 *       PERO NOSOTROS NO QUEREMOS QUE NINGUN PERFIL PUEDA VER SIQUIERA UNA PAGINA 404 DEL MODULO DE ADMINISTRADOR.
 *       ESTA DECICION LA TOMA EL PLUGIN DE PERMISOS, YA QUE REDIRECCION404 ES UNA ACCION MAS Y TIENE QUE SER ASIGNADA A ALGUN GRUPO.
 *       En tal caso ADMIN_PUBLICACIONES_REDIRECCION404 sera solo autorizada a los administradores y moderadores (por ejemplo)
 *       Si es un visitante pedira autorizacion para acceder a la pagina. Si se autentifica y sigue sin tener permiso sera redireccionado segun
 *       getUrlRedireccion401() que determina la pagina a donde van a parar las acciones desautorizadas para usuarios que ya estan logueados.
 *
 * @author Matias Velilla
 */
class PluginRedireccion404 extends PluginAbstract
{
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
        $response = $front->getResponse();
        if(null !== $response && $response->getCode() == '404')
        {
            //Puede que el router no haya matcheado ninguna ruta por lo que agrego modulo y controlador del home del sitio
            try{
                $front->getRouter()->getCurrentRoute();
            } catch( Exception $e){
                $parametros = $front->getPlugin('PluginParametros');
                $soloSistema = true;
                $this->getRequest()->setModuleName($parametros->obtener('HOME_SITIO_MODULO', $soloSistema));
                $this->getRequest()->setControllerName($parametros->obtener('HOME_SITIO_CONTROLADOR', $soloSistema));
            }
            $this->getRequest()->setActionName(self::NOMBRE_ACCION_404);
            FrontController::getInstance()->cleanResponse();
            return true;
        }
        return false;
    }

    public function preDispatch(HttpRequest $request)
    {
        //en este caso analizo tambien el caso de una posible peticion ajax
        if($request->isXmlHttpRequest())
        {
            if(null !== $response && $response->getCode() == '404')
            {
                $request->setModuleName('index')
                        ->setControllerName('index')
                        ->setActionName('ajaxError')
                        ->setParam('msgError', 'La accion que solicito no existe')
                        ->setParam('codigoError', '404');
                FrontController::getInstance()->cleanResponse();
            }
            return;
        }

        //si no es peticion ajax procedo a una redireccion normal.
        $this->setRequest($request);
        $this->requestRedireccionar404();
    }

    public function postDispatch(HttpRequest $request)
    {
        $this->setRequest($request);
        if($this->requestRedireccionar404()){
            $request->setDispatched(false);
        }
    }
}