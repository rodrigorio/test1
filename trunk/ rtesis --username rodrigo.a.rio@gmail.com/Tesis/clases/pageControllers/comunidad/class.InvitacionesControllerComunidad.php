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

    /**
     * Establece descripcion de invitaciones y el menu con 2 opciones,
     * estado de invitaciones enviadas y formulario para enviar nueva invitacion
     */
    public function index()
    {
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();
        $this->printMsgTop();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Invitaciones Comunidad");

        //contenido ppal home invitaciones
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "pageRightInnerMainCont", "PageRightInnerMainContBlock");
        $this->getTemplate()->set_var("hrefNuevaInvitacion", $this->getRequest()->getBaseTagUrl()."comunidad/nueva-invitacion");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oPerfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $oUsuario = $oPerfil->getUsuario();

            if(!$oPerfil->isAdministrador() && !$oPerfil->isModerador() &&
               $oUsuario->getInvitacionesDisponibles() <= 0){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("No tiene invitaciones disponibles para enviar.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            if(ComunidadController::getInstance()->existeMailDb($this->getRequest()->getPost('email')))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("El mail ya esta siendo utilizado por un integrante de la comunidad.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            if(ComunidadController::getInstance()->existeInvitacionUsuario($this->getRequest()->getPost('email')))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Ya has enviado una invitación a esa dirección de correo.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }
           
            ComunidadController::getInstance()->borrarInvitacionesExpiradasUsuario();

            $oInvitado = ComunidadController::getInstance()->obtenerInvitadoByEmail($this->getRequest()->getPost('email'));
            
            if(null !== $oInvitado){
                //persona que ya fue invitada
                $oInvitado->setNombre(ucwords($this->getRequest()->getPost('nombre')));
                $oInvitado->setApellido(ucwords($this->getRequest()->getPost('apellido')));
            }else{
                //persona que es invitada por primera vez
                $oInvitado = new stdClass();
                $oInvitado->sNombre = ucwords($this->getRequest()->getPost('nombre'));
                $oInvitado->sApellido = ucwords($this->getRequest()->getPost('apellido'));
                $oInvitado->sEmail = $this->getRequest()->getPost('email');
                $oInvitado = Factory::getInvitadoInstance($oInvitado);
            }

            $oInvitacion = new stdClass();
            $oInvitacion->sRelacion = $this->getRequest()->getPost('relacion');
            $oInvitacion->oInvitado = $oInvitado;
            $oInvitacion->oUsuario = $oUsuario;
            $oInvitacion = Factory::getInvitacionInstance($oInvitacion);
            $oInvitacion->setEstadoPendiente();

            $result = ComunidadController::getInstance()->enviarInvitacion($oInvitacion);
            
            //si se dio de alta correctamente la invitacion envio el mail a la persona invitada
            if($result){
                $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
                $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
                $mailContacto = $parametros->obtener('EMAIL_SITIO_CONTACTO');

                $sMailDestino = $oInvitacion->getInvitado()->getEmail();
                $sMailDesde = $oInvitacion->getUsuario()->getEmail();
                $hrefSitio = htmlentities($this->getRequest()->getBaseTagUrl());
                $sNombreInvitado = $oInvitacion->getInvitado()->getNombre()." ".$oInvitacion->getInvitado()->getApellido();
                $sNombreUsuario = $oInvitacion->getUsuario()->getNombre()." ".$oInvitacion->getUsuario()->getApellido();

                //En este caso no se puede desuscribir a notificaciones por mail porque todavia no es un usuario.
                $hrefCancelarSuscripcion = $hrefSitio;

                $this->getTemplate()->load_file("gui/templates/index/frameMail01-01.gui.html", "frameMail");

                //head y footer mail.
                $this->getTemplate()->set_var("hrefSitio", $hrefSitio);
                $this->getTemplate()->set_var("sNombreSitio", $nombreSitio." - Comunidad");
                $this->getTemplate()->set_var("sEmailDestino", $sMailDestino);
                $this->getTemplate()->set_var("sEmailContacto", $mailContacto);
                $this->getTemplate()->set_var("hrefCancelarSuscripcion", $hrefCancelarSuscripcion);

                $this->getTemplate()->load_file_section("gui/componentes/mails.gui.html", "sMainContent", "TituloMensajeSubMensajeBlock");

                $sTituloMensaje = htmlentities($sNombreInvitado." has recibido una invitación para unirte a la comunidad ".$nombreSitio.".");
                $this->getTemplate()->set_var("sTituloMensaje", $sTituloMensaje);

                $sMensaje = htmlentities($sNombreUsuario." te ha invitado a abrir una cuenta en la comunidad de profesionales ".$nombreSitio.". 
                                         PodrÃ¡s encontrar recursos informÃ¡ticos y ser parte de una gran comunidad participativa de profesionales
                                         orientados a la educaciÃ³n inclusiva y discapacidad.");
                
                $this->getTemplate()->set_var("sMensaje", $sMensaje);

                $sSubMensaje = htmlentities("La comunidad esta destinada a docentes, profesionales y estudiantes avanzados de ciencias de la educaciÃ³n
                                            y salud, que estÃ©n involucrados y comprometidos en su desempeÃ±o laboral-profesional con el bienestar
                                            psicofÃ­sico, la inserciÃ³n social y la calidad de vida en su totalidad de personas discapacitadas.
                                            Principalmente se podrÃ¡ gestionar informaciÃ³n para  el seguimiento profesional de la evoluciÃ³n del
                                            aprendizaje en personas discapacitadas.
                                            Se quiere lograr el fÃ¡cil intercambio de experiencias y que los profesionales puedan tener acceso a
                                            recursos Ãºtiles que puedan usar en su desempeÃ±o laboral. El sistema tambiÃ©n ofrecerÃ¡ funcionalidad
                                            participativa a la comunidad en general.");

                $this->getTemplate()->set_var("sSubMensaje", $sSubMensaje);

                $this->getTemplate()->load_file_section("gui/componentes/mails.gui.html", "sMainContent", "PanelBotonesBlock", true);

                $hrefButton = htmlentities($hrefSitio."registracion?token=".$oInvitacion->getToken());
                $this->getTemplate()->set_var("sButton", "Registrarme");
                $this->getTemplate()->set_var("hrefButton", $hrefButton);
                $this->getTemplate()->parse("ButtonBlock");
                $sMensajeBody = $this->getTemplate()->pparse("frameMail", false);

                $this->getMailerHelper()->sendMail($sMailDesde, $sNombreUsuario, $sMailDestino, $sNombreInvitado, $sNombreUsuario." te ha invitado a ser parte de la comunidad ".$nombreSitio, $sMensajeBody);
            }

            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_INVITACION');
            $this->getJsonHelper()->setSuccess(true);
            $this->getJsonHelper()->setValor("cantidadInvitaciones", $oInvitacion->getUsuario()->getInvitacionesDisponibles());
            $this->getJsonHelper()->setMessage("La invitaciÃ³n se ha enviado con Ã©xito al correo indicado, el link de registraciÃ³n expirarÃ¡ dentro de ".$cantDiasExpiracion." dÃ­as.");

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->setMessage("OcurriÃ³ un error no se pudo enviar la invitaciÃ³n.");
        }
        
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function formulario()
    {
        $oPerfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $oUsuario = $oPerfil->getUsuario();        
        $iInvitacionesDisponibles = $oUsuario->getInvitacionesDisponibles();

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        
        //Si no es admin o mod y no tiene invitaciones devuelvo un cartel informando.
        if(!$oPerfil->isAdministrador() && !$oPerfil->isModerador() && empty($iInvitacionesDisponibles)){
            $msg = "No tiene invitaciones disponibles para enviar.";
            $bloque = 'MsgInfoBlockI32';
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
            $this->getTemplate()->set_var("sMensaje", $msg);
            $this->getTemplate()->set_var("popUpContent", $this->getTemplate()->pparse('html', false));
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
            return;
        }

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/invitaciones.gui.html", "popUpContent", "FormularioBlock");
        $this->getTemplate()->set_var("iInvitacionesDisponibles", $iInvitacionesDisponibles);
           
        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }  
}