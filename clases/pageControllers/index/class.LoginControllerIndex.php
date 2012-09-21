<?php
/**
 * @author Matias Velilla
 *
 * Si se hace submit del formulario se redirecciona dependiendo si existe o no el codigo de error 401
 * Si existe se redirecciona al request original (la pagina restringida que se solicitaba)
 * Si no existe se redirecciona a la url por defecto que dependera del perfil del usuario que se loguea.
 */
class LoginControllerIndex extends PageControllerAbstract
{
    public function index()
    {
        if($this->getRequest()->has("popUp")){
            $this->mostrarFormularioPopUp();
        }else{
            $this->mostrarFormulario();
        }
    }

    /**
     * Ejecuta el logout y redirecciona al home de la pagina
     */
    public function logout()
    {
        SysController::getInstance()->cerrarSesion();
        $this->getRedirectorHelper()->gotoUrl("/");
    }

    /**
     * Procesa el envio desde un formulario de login.
     * El metodo es ajax, si se detecta que la peticion fue a traves de otro metodo tira excepcion 404
     * Este metodo resuelve tambien a que url tiene que dirigirse el usuario una vez que se loguea.
     */
    public function procesar()
    {        
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        //seguridad extra
        $iTipoDocumento = $this->getRequest()->getPost('tipoDocumento');
        $sNumeroDocumento = $this->getRequest()->getPost('nroDocumento');
        $sContraseniaMd5 = $this->getRequest()->getPost('contraseniaMD5');
        if(empty($iTipoDocumento) || empty($sNumeroDocumento) || empty($sContraseniaMd5)){
            $this->getJsonHelper()->setSuccess(false)->setMessage("No se han enviado correctamente los datos desde el formulario.");
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }
        
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();

            list($errorDatos, $errorSuspendido, $exito) = SysController::getInstance()->loginUsuario($iTipoDocumento, $sNumeroDocumento, $sContraseniaMd5);

            if($exito){
                $redirect = $this->getRequest()->getPost('next');
                if(empty($redirect)){
                    $redirect = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccionLoginDefecto(true);
                }

                //agrega una url para que el js redireccione
                $this->getJsonHelper()->setSuccess(true)
                                      ->setRedirect($redirect)
                                      ->sendJsonAjaxResponse();
                return;
            }

            if($errorDatos){            
                //indica que la accion no se concreto con exito y agrega un mensaje de error
                $this->getJsonHelper()->setSuccess(false)
                                      ->setMessage('Usuario o contraseña incorrectos.')
                                      ->sendJsonAjaxResponse();
                return;
            }
            
            if($errorSuspendido){                            
                $this->getJsonHelper()->setSuccess(false)
                                      ->setMessage('La cuenta se encuentra suspendida, comuniquese con los administradores del sistema')
                                      ->sendJsonAjaxResponse();
                return;
            }
                
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false)
                                  ->setMessage('Ocurrio un error al tratar de procesar la informacion')
                                  ->sendJsonAjaxResponse();
        }       
    }

    /**
     * Si entro a login por error de permiso guardo la url original donde queria ir el user.
     * Esta funcion devuelve esa url.
     */
    private function getNextUrl()
    {
        $nextFormUrl = "";
        $pathInfo = $this->getRequest()->getPathInfo();
        if($pathInfo != '/login'){
            $nextFormUrl = $this->getRequest()->get('REQUEST_URI');
            $nextFormUrl = str_replace($nextFormUrl, "",$pathInfo);
        }
        return $nextFormUrl;
    }

    public function mostrarFormularioPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-01.gui.html", "frame");

        //si ya esta logueado cancelo la accion y redirecciono a url por defecto.
        if(SessionAutentificacion::getInstance()->realizoLogin()){
            $pathInfo = true;
            $url = $this->getRequest()->getBaseUrl().SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccionLoginDefecto($pathInfo);

            $tituloMensajeError = "Ya existe un perfil autentificado";
            $ficha = "MsgFichaInfoBlock";
            $mensajeInfoError = "Ya existe un perfil autentificado en sesion, por favor accede desde el siguiente link.";

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "popUpContent", $ficha);
            $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
            $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

            //Link
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->unset_blocks("OpcionesMenu"); //solo uso un link
            $this->getTemplate()->set_var("idOpcion", 'acceder');
            $this->getTemplate()->set_var("hrefOpcion", $url);
            $this->getTemplate()->set_var("sNombreOpcion", "Volver");
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }else{

            $linkRecuperarPass = $this->getRequest()->getBaseUrl()."/recuperar-contrasenia";           
            $nextFormUrl = $this->getNextUrl();
            //se procesa el envio del form en un metodo de esta misma clase.
            $actionFormUrl = "login-procesar";
            
            $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "popUpContent", "FormularioBlock");
            $this->getTemplate()->set_var("sFormAction", $actionFormUrl);
            $this->getTemplate()->set_var("sNextUrl", $nextFormUrl);
            $this->getTemplate()->set_var("sLinkRecuperarPass", $linkRecuperarPass);

            //armo el select con los tipos de documentos cargados en db
            $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
            foreach ($aTiposDocumentos as $value => $text){
                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $text);
                $this->getTemplate()->parse("OptionSelectDocumento", true);
            }
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function mostrarFormulario()
    {
        //si ya esta logueado cancelo la accion y redirecciono a url por defecto.
        if(SessionAutentificacion::getInstance()->realizoLogin()){
            $pathInfo = true;
            $url = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccionLoginDefecto($pathInfo);
            $this->getRedirectorHelper()->gotoUrl($url); //por defecto redireccion resulta en un inmediato exit() luego de la sentencia.
        }
                
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');
        $fileNameLogo = $parametros->obtener('FILE_NAME_LOGO_SITIO');
        $linkRecuperarPass = $this->getRequest()->getBaseUrl()."/recuperar-contrasenia";

        $nextFormUrl = $this->getNextUrl();
        //se procesa el envio del form en un metodo de esta misma clase.
        $actionFormUrl = "login-procesar";

        $this->getTemplate()->load_file("gui/templates/index/frame01-03.gui.html", "frame");
        $this->getTemplate()->set_var("sNombreSeccionTopPage", "Autenticarse");

        $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "jsContent", "JsContent");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        IndexControllerIndex::setCabecera($this->getTemplate());

        $this->printMsgTop();

        $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "columnaIzquierdaContent", "FormularioBlock");
        $this->getTemplate()->set_var("sFormAction", $actionFormUrl);
        $this->getTemplate()->set_var("sNextUrl", $nextFormUrl);
        $this->getTemplate()->set_var("sLinkRecuperarPass", $linkRecuperarPass);

        //armo el select con los tipos de documentos cargados en db
        $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
        foreach ($aTiposDocumentos as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            $this->getTemplate()->parse("OptionSelectDocumento", true);
        }

        //Si vino a Login por error de permiso muestro ficho con advertencia y link a inicio
        if($this->getRequest()->has('msgError') || $this->getRequest()->has('msgInfo')){
            if($this->getRequest()->has('msgError')){
                $tituloMensajeError = $this->getRequest()->getParam('msgError');
                $ficha = "MsgFichaErrorBlock";
            }else{
                $tituloMensajeError = $this->getRequest()->getParam('msgInfo');
                $ficha = "MsgFichaInfoBlock";
            }

            $mensajeInfoError = "La página que solicitaste no se puede mostrar en este momento.
                                 Puede que esté temporalmente fuera de servicio,
                                 que el enlace donde hiciste clic haya expirado o que no tengas permiso para ver esta página.";

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaDerechaContent", $ficha);
            $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
            $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

            //Link a Inicio
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->unset_blocks("OpcionesMenu"); //solo uso un link
            $this->getTemplate()->set_var("idOpcion", 'opt1');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Volver a inicio");
        }else{
            //si no muestro foto informativa por defecto (tipo gmail)
            $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "columnaDerechaContent", "ImagenLoginBlock");
            $this->getTemplate()->set_var("srcImagenLogin", 'gui/images/banners-logos/welcome.jpg');
            $this->getTemplate()->set_var("widthImagenLogin", "460");
            $this->getTemplate()->set_var("heightImagenLogin", "200");
        }

        IndexControllerIndex::setFooter($this->getTemplate());
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function formRecuperarContrasenia()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "popUpContent", "FormRecuperarContrasenia");

        //armo el select con los tipos de documentos cargados en db
        $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
        foreach ($aTiposDocumentos as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            $this->getTemplate()->parse("OptionSelectDocumento", true);
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }
    
    public function procesarRecuperarContrasenia()
    {
        if($this->getRequest()->has('token')){
            $this->confirmarNuevaContrasenia();
            return;
        }

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        if($this->getRequest()->has('enviarContrasenia'))
        {
            try{
                $this->getJsonHelper()->initJsonAjaxResponse();

                $sEmail = $this->getRequest()->getPost("emailRecuperarPass");
                $iTipoDocumentoId = $this->getRequest()->getPost("tipoDocumentoRecuperarPass");
                $sNumeroDocumento = $this->getRequest()->getPost("nroDocumentoRecuperarPass");

                if(empty($sEmail) || empty($iTipoDocumentoId) || empty($sNumeroDocumento)){
                    $this->getJsonHelper()->setSuccess(false)->setMessage("No se han enviado correctamente los datos desde el formulario.");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }

                $oUsuario = ComunidadController::getInstance()->getUsuarioByEmailDni($sEmail, $iTipoDocumentoId, $sNumeroDocumento);
                if(null === $oUsuario){
                    $this->getJsonHelper()->setSuccess(false)->setMessage("Los datos no coinciden con ningún integrante de la comunidad.");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }

                //todavia existe un password temporal generado que no caduco
                if(IndexController::getInstance()->existePasswordTemporal($oUsuario->getId()))
                {
                    $this->getJsonHelper()
                         ->setSuccess(false)
                         ->setMessage("Ya se ha procesado una solicitd de cambio de contraseña, el sistema no generará otra hasta que ésta expire.
                                       El link para confirmar el cambio fue enviado a la dirección de correo electrónico ".$oUsuario->getEmail());
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }

                IndexController::getInstance()->borrarPasswordTemporalExpiradaUsuario($oUsuario->getId());

                IndexController::getInstance()->crearPasswordTemporal($oUsuario);

                $oPasswordTemporal = $oUsuario->getPasswordTemporal();

                if(null === $oPasswordTemporal)
                {
                    $this->getJsonHelper()
                         ->setSuccess(false)
                         ->setMessage("No se pudo generar una nueva contraseña, vuelva a intentarlo mas tarde.");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                
                $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
                $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
                $mailContacto = $parametros->obtener('EMAIL_SITIO_CONTACTO');

                $sMailDestino = $oUsuario->getEmail();                
                $hrefSitio = htmlentities($this->getRequest()->getBaseTagUrl());                
                $sNombreUsuario = $oUsuario->getNombreCompleto();
                
                $hrefCancelarSuscripcion = htmlentities($hrefSitio."desactivar-notificaciones-mail?id=".$oUsuario->getId()."&key=".$oUsuario->getUrlTokenKey());

                $this->getTemplate()->load_file("gui/templates/index/frameMail01-01.gui.html", "frameMail");

                //head y footer mail.
                $this->getTemplate()->set_var("hrefSitio", $hrefSitio);
                $this->getTemplate()->set_var("sNombreSitio", $nombreSitio." - Comunidad");
                $this->getTemplate()->set_var("sEmailDestino", $sMailDestino);
                $this->getTemplate()->set_var("sEmailContacto", $mailContacto);
                $this->getTemplate()->set_var("hrefCancelarSuscripcion", $hrefCancelarSuscripcion);

                $this->getTemplate()->load_file_section("gui/componentes/mails.gui.html", "sMainContent", "TituloMensajeSubMensajeDestacadoBlock");

                $sTituloMensaje = htmlentities("Cambio de contraseña");
                $this->getTemplate()->set_var("sTituloMensaje", $sTituloMensaje);

                $sMensaje = htmlentities("Se ha solicitado desde el sistema la generación de una nueva contraseña para acceder como integrante de la comunidad.
                                          Si no deseas utilizarla simplemente ignora este mensaje, la solicitud quedara obsoleta.");

                $this->getTemplate()->set_var("sMensaje", $sMensaje);

                $sSubMensaje = htmlentities("Tu nueva contraseña es: ".$oPasswordTemporal->getPassword()." ");
                $this->getTemplate()->set_var("sSubMensaje", $sSubMensaje);

                $this->getTemplate()->load_file_section("gui/componentes/mails.gui.html", "sMainContent", "PanelBotonesBlock", true);

                $hrefButton = htmlentities($hrefSitio."recuperar-contrasenia-procesar?token=".$oPasswordTemporal->getToken());
                $this->getTemplate()->set_var("sButton", htmlentities("Confirmar cambio de contraseña"));
                $this->getTemplate()->set_var("hrefButton", $hrefButton);
                $this->getTemplate()->parse("ButtonBlock");
                $sMensajeBody = $this->getTemplate()->pparse("frameMail", false);

                $this->getMailerHelper()->sendMail($mailContacto, $nombreSitio." - Comunidad", $sMailDestino, $sNombreUsuario, $nombreSitio." - Solicitud cambio de contrasena", $sMensajeBody);

                //envio el html para el dialog de exito
                $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_REC_PASS');
                $sMensaje = "Se ha generado una nueva contraseña, se envió un link temporal a la dirección de e-mail.<br>
                             El link expirará dentro de ".$cantDiasExpiracion." día/s.";

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sDialogExito", "MsgCorrectoBlockI32");
                $this->getTemplate()->set_var("sMensaje", $sMensaje);
            
                $this->getJsonHelper()->setSuccess(true)
                     ->setValor('html', $this->getTemplate()->pparse('sDialogExito'))
                     ->sendJsonAjaxResponse();
                
            }catch(Exception $e){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
            }                                    
        }
    }
    
    private function confirmarNuevaContrasenia()
    {
    	try{
            $sToken = $this->getRequest()->getParam('token');

            if(empty($sToken))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $front = FrontController::getInstance();
            $parametros = $front->getPlugin('PluginParametros');
            $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
            $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
            $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
            $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');            
            $linkRecuperarPass = $this->getRequest()->getBaseUrl()."/recuperar-contrasenia";

            $this->getTemplate()->load_file("gui/templates/index/frame01-03.gui.html", "frame");
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Autenticarse");

            $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "jsContent", "JsContent");
            $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
            $this->getTemplate()->set_var("sTituloVista", $tituloVista);
            $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
            $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

            IndexControllerIndex::setCabecera($this->getTemplate());

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/index/login.gui.html", "columnaIzquierdaContent", "FormularioBlock");
            $this->getTemplate()->set_var("sLinkRecuperarPass", $linkRecuperarPass);

            //armo el select con los tipos de documentos cargados en db
            $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
            foreach ($aTiposDocumentos as $value => $text){
                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $text);
                $this->getTemplate()->parse("OptionSelectDocumento", true);
            }

            //muestro mensaje segun se haya confirmado el cambio de password o no.
            //si no existe password temporal quiere decir que ya se utilizo o que el enlace caduco
            $sTituloMsgFicha = "Recuperar Contraseña";
            if(!IndexController::getInstance()->existePasswordTemporalToken($sToken))
            {
                $ficha = "MsgFichaErrorBlock";                
                $sMsgFicha = "No se ha actualizado la contraseña para su cuenta en el sistema.
                              Puede ser debido a que la solicitud ya se ha utilizado o que el link de solicitud haya expirado y necesite generar uno nuevo.";
            }else{
                IndexController::getInstance()->confirmarPasswordTemporal($sToken);
                $ficha = "MsgFichaCorrectoBlock";
                $sMsgFicha = "El cambio de contraseña se ha producido con éxito, puede iniciar sesión con los nuevos datos de cuenta.";
            }
                      
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaDerechaContent", $ficha);
            $this->getTemplate()->set_var("sTituloMsgFicha", $sTituloMsgFicha);
            $this->getTemplate()->set_var("sMsgFicha", $sMsgFicha);

            //Link a Inicio
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->unset_blocks("OpcionesMenu"); //solo uso un link
            $this->getTemplate()->set_var("idOpcion", 'opt1');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Volver a inicio");

            IndexControllerIndex::setFooter($this->getTemplate());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
           
    	}catch(Exception $e){
            throw $e;
    	}
    }
}