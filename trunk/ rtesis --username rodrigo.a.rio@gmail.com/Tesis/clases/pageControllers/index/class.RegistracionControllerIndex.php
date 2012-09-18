<?php

class RegistracionControllerIndex extends PageControllerAbstract
{
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
        $this->getTemplate()->load_file_section("gui/vistas/index/registracion.gui.html", "jsContent", "JsContent");
        return $this;
    }

    public function index()
    {
        $this->formulario();
    }

    public function formulario()
    {
        try{
            $sToken = $this->getRequest()->getParam('token');

            if(empty($sToken))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $this->getTemplate()->load_file("gui/templates/index/frame01-03.gui.html", "frame");
            $this->setHeadTag();
            $this->printMsgTop();
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Registrarse");
            $this->getTemplate()->load_file_section("gui/vistas/index/registracion.gui.html", "topPageContent", "DescripcionSeccionBlock");
            $this->getTemplate()->load_file_section("gui/vistas/index/registracion.gui.html", "columnaDerechaContent", "InfoIntegrantesComunidadBlock");

            IndexControllerIndex::setCabecera($this->getTemplate());
            IndexControllerIndex::setFooter($this->getTemplate());

            $oInvitacion = ComunidadController::getInstance()->getInvitacionByToken($sToken);

            //si devuelve null entonces la invitacion no existe o ha caducado
            if(null === $oInvitacion){
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaIzquierdaContent", "MsgFichaErrorBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "La invitación no existe");
                $this->getTemplate()->set_var("sMsgFicha", "La invitación no existe o el link que acabas de utilizar pertenece a una invitación que ha caducado.");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }

            //estado aceptada entonces ya fue utilizada
            if(!$oInvitacion->isPendiente()){
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaIzquierdaContent", "MsgFichaErrorBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Parece que algo anda mal");
                $this->getTemplate()->set_var("sMsgFicha", "El link de invitación que acabas de usar ya fue utilizado anteriormente en una registración exitosa.");
                $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                return;
            }

            $this->getTemplate()->load_file_section("gui/vistas/index/registracion.gui.html", "columnaIzquierdaContent", "FormularioBlock");
            $this->getTemplate()->set_var("sNombreInvitado", $oInvitacion->getInvitado()->getNombre()." ".$oInvitacion->getInvitado()->getApellido());
            $this->getTemplate()->set_var("sEmail", $oInvitacion->getInvitado()->getEmail());
            $this->getTemplate()->set_var("sNombre", $oInvitacion->getInvitado()->getNombre());
            $this->getTemplate()->set_var("sApellido", $oInvitacion->getInvitado()->getApellido());

            //genero los selects del formulario
            $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
            foreach ($aTiposDocumentos as $value => $text){
                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $text);
                $this->getTemplate()->parse("OptionSelectDocumento", true);
            }

            for($i = 1; $i <= 31; $i++){
                $value = (string)$i;
                if($i<10){ $value = "0".$value; }

                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $value);
                $this->getTemplate()->parse("OptionSelectDia", true);
            }

            $aMeses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
                            '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre',
                            '11' => 'noviembre', '12' => 'diciembre');

            foreach ($aMeses as $value => $text){
                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $text);
                $this->getTemplate()->parse("OptionSelectMes", true);
            }

            $anioActual = date("Y");
            for($i = $anioActual; $i >= 1905; $i--){
                $value = (string)$i;
                $this->getTemplate()->set_var("iValue", $value);
                $this->getTemplate()->set_var("sDescripcion", $value);
                $this->getTemplate()->parse("OptionSelectAnio", true);
            }
            $this->getTemplate()->set_var("sToken", $sToken);
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    public function procesar()
    {
	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $this->getJsonHelper()->initJsonAjaxResponse();        
    	try{
            $sToken = $this->getRequest()->getPost("sToken");
            $oInvitacion = ComunidadController::getInstance()->getInvitacionByToken($sToken);

            if(null == $oInvitacion || 
               !$oInvitacion->isPendiente() ||
               $oInvitacion->getInvitado()->getEmail() !== $this->getRequest()->getPost("email"))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("La invitación ya fue utilizada, no existe o la información no es válida.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //primero valido que la informacion enviada pueda ser utilizada

            //numero de documento
            if(ComunidadController::getInstance()->existeDocumentoUsuario($this->getRequest()->getPost('nroDocumento'))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Ya existe un integrante de la comunidad con ese número de documento.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }
            $filtro = array("p.numeroDocumento" => $this->getRequest()->getPost('nroDocumento'));
            if(SeguimientosController::getInstance()->existeDiscapacitado($filtro)){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Ya existe una persona en la comunidad con ese número de documento.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //mail
            if(ComunidadController::getInstance()->existeMailDb($this->getRequest()->getPost('email'))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Ya existe un integrante de la comunidad con la dirección de correo ingresada.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //nombre de usuario
            $sNombreUsuario = $this->getRequest()->getPost('nombreUsuario');
            if(!empty($sNombreUsuario)){
                if(ComunidadController::getInstance()->existeNombreUsuarioDb($sNombreUsuario)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("El nombre de usuario ingresado ya esta siendo utilizado");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
            }else{
                //genero un nombre de usuario
                $sNombreUsuario = ucwords($this->getRequest()->getPost('nombre')." ".$this->getRequest()->getPost('apellido'))." ".
                                  $oInvitacion->getInvitado()->getId();
            }
            $sNombreUsuario = trim($sNombreUsuario);
            $sNombreUsuario = str_replace(' ', '_', $sNombreUsuario);
            //le saco los caracteres especiales.
            $sNombreUsuario = InflectorHelper::unaccent($sNombreUsuario);
            
            //hasta que no se registra con exito no es un usuario.
            $oObj = new stdClass();
            $oObj->iInvitadoId = $oInvitacion->getInvitado()->getId();
            $oObj->iUsuarioId = $oInvitacion->getUsuario()->getId();
            $oObj->sNombreUsuario = $sNombreUsuario;
            $oObj->sContrasenia	= $this->getRequest()->getPost('contraseniaMD5');
            $oObj->sNombre = ucwords($this->getRequest()->getPost('nombre'));
            $oObj->sApellido = $this->getRequest()->getPost('apellido');
            $oObj->sSexo = strtolower($this->getRequest()->getPost('sexo'));
            $oObj->iTipoDocumentoId = $this->getRequest()->getPost('tipoDocumento');
            $oObj->sNumeroDocumento = $this->getRequest()->getPost('nroDocumento');
            $oObj->sEmail = $oInvitacion->getInvitado()->getEmail();

            $fechaNacimientoDia     = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes     = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio    = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $fechaNacimiento = implode('-', $aFechaNacimiento);            
            $oObj->dFechaNacimiento = $fechaNacimiento." 00:00";

            //invitado exitosamente registrado
            $oUsuario = IndexController::getInstance()->registrarInvitado($oObj);

            //creo el perfil que le corresponde, agrego la url para redireccionar y envio el html del dialog registracion exitosa
            SysController::getInstance()->iniciarPerfilSessionUsuario($oUsuario);

            //url de redireccion para perfil integrante inactivo
            $redirect = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUrlRedireccionLoginDefecto(true);

            //html dialog
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sDialogExito", "MsgFichaCorrectoBlock");
            $this->getTemplate()->set_var("sTituloMsgFicha", "Felicitaciones ".$oUsuario->getNombre()." ".$oUsuario->getApellido().", te has registrado con éxito!.");
            $this->getTemplate()->set_var("sMsgFicha", "Ya eres un integrante de nuestra comunidad.
                                                       Sin embargo, tu perfil aún esta limitado a las acciones básicas.<br>
                                                       Para pasar a ser un integrante activo debes completar los datos referidos a tu cuenta desde la sección de edición de datos personales.
                                                       Puedes dejar este paso para más adelante, aunque probablemente te lleve solo unos minutos.<br>
                                                       También puedes dirigirte inmediatamente a la sección comunidad, donde encontrarás la totalidad del material actual publicado por los integrantes.");

            //menu en el cartel del dialog
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
            $this->getTemplate()->set_var("idOpcion", 'datosPersonales');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/comunidad/datos-personales');
            $this->getTemplate()->set_var("sNombreOpcion", "Ir a editar cuenta");
            $this->getTemplate()->parse("OpcionesMenu", true);

            $this->getTemplate()->set_var("idOpcion", 'comunidadHome');
            $this->getTemplate()->set_var("hrefOpcion", $redirect);
            $this->getTemplate()->set_var("sNombreOpcion", "Ir a comunidad inicio");
            $this->getTemplate()->parse("OpcionMenuLastOpt");
            
            $this->getJsonHelper()->setSuccess(true)
                 ->setRedirect($redirect)
                 ->setValor('html', $this->getTemplate()->pparse('sDialogExito'))
                 ->sendJsonAjaxResponse();

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            throw $e;
        }
    }    
}