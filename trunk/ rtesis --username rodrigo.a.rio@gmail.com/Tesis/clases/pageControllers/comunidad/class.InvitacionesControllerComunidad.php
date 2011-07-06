<?php
/**
 * @author Matias Velilla
 */
class InvitacionesControllerComunidad extends PageControllerAbstract
{
    /**
     * Setea el Head para las vistas de invitaciones
     */
    private function setHeadTag()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        //js de home
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

        $this->getTemplate()->set_var("hrefNuevaInvitacion", $this->getRequest()->getBaseTagUrl()."comunidad/nueva-invitacion");
        $this->getTemplate()->set_var("hrefInvitaciones", $this->getRequest()->getBaseTagUrl()."comunidad/invitaciones");
        $this->getTemplate()->set_var("hrefMisInvitaciones", $this->getRequest()->getBaseTagUrl()."comunidad/invitaciones-listado");        
    }

    /**
     * Establece descripcion de invitaciones y el menu con 2 opciones,
     * estado de invitaciones enviadas y formulario para enviar nueva invitacion
     */
    public function index()
    {
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Invitaciones");

        //menu derecha
        $this->setMenuDerecha();

        //contenido ppal home invitaciones
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "pageRightInnerMainCont", "PageRightInnerMainContBlock");
        $this->getTemplate()->set_var("hrefNuevaInvitacion", $this->getRequest()->getBaseTagUrl()."comunidad/nueva-invitacion");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Procesa el envio desde un formulario de invitacion.
     */
    public function procesar(){
    	 //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $oUsuario	= SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
   			$oInvitado	= new stdClass();
	   		$oInvitado->sRelacion 	= $this->getRequest()->getPost('relacion');
			$oInvitado->sNombre 	= $this->getRequest()->getPost('nombre');
			$oInvitado->sApellido 	= $this->getRequest()->getPost('apellido');
			$oInvitado->sEmail 		= $this->getRequest()->getPost('email');
		//	$sDescripcion			= $this->getRequest()->getPost('sDescripcion');
			$sDescripcion="";
			ComunidadController::getInstance()->enviarInvitacion($oUsuario, $oInvitado, $sDescripcion);
			$this->getJsonHelper()->setSuccess(true);
                                      //->setRedirect($redirect);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    	
    }

    /**
     * Vista para enviar una nueva invitacion
     */
    public function formulario()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $invitacionesDisponibles = $usuario->getInvitacionesDisponibles();

        //Si no tiene invitaciones redirecciono con mensaje.
        if(empty($invitacionesDisponibles)){
            $url = PluginRedireccionAccionDesactivada::getLastRequestUri();
            if(empty($url)){
                $url = $perfil->getUrlRedireccion(true);
            }else{
                $this->getRedirectorHelper()->setPrependBase(false);
            }
            $this->getRedirectorHelper()->gotoUrl($url); //por defecto redireccion resulta en un inmediato exit() luego de la sentencia.
        }

        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Enviar Nueva Invitacion");

        //menu derecha
        $this->setMenuDerecha();

        //contenido ppal
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "pageRightInnerMainCont", "FormularioBlock");
        $this->getTemplate()->set_var("iInvitacionesDisponibles", $invitacionesDisponibles);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Lista de todas las invitaciones realizadas y el estado en el que se encuentran
     */
    public function listado()
    {
    }    
}