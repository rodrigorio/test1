<?php

/**
 *
 */
class RegistracionControllerIndex extends PageControllerAbstract
{
    private function validarUrlTemporal(){
    	try{
            $user   = $this->getRequest()->get("us");
            $inv    = $this->getRequest()->get("inv");
            $email  = $this->getRequest()->get("email");
            $token  = $this->getRequest()->get("token");
            return IndexController::getInstance()->validarUrlTmp($user,$inv,$email,$token);
     	}catch(Exception $e){
            return false;
            print_r($e);
        }
    }

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

    public function formulario(){
        try{
            if(!$this->validarUrlTemporal()){
                exit("La pagina ha caducado");
            }

            $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
            $this->setHeadTag();
            
            IndexControllerIndex::setCabecera($this->getTemplate());

            $this->getTemplate()->set_var("topPageContent", "Registracion");

            $this->getTemplate()->set_var("sEmail", $this->getRequest()->get("email"));
            $this->getTemplate()->set_var("sNombre", $this->getRequest()->get("nom"));
            $this->getTemplate()->set_var("sApellido", $this->getRequest()->get("ape"));

            $this->getTemplate()->set_var("us", $this->getRequest()->get("us"));
            $this->getTemplate()->set_var("inv",$this->getRequest()->get("inv"));
            
            $this->getTemplate()->load_file("gui/vistas/index/registracion.gui.html", "centerPageContent");

            $this->getTemplate()->parse("centerPageContent", false);

            IndexControllerIndex::setFooter($this->getTemplate());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesar(){
	 	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
    	try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
	        $sUserName 	= $this->getRequest()->getPost("username");
	        $iTipoDni 	= $this->getRequest()->getPost("tipoDni");
	        $iDni	 	= $this->getRequest()->getPost("dni");
	        $sPassword 	= $this->getRequest()->getPost("password");
	        $sEmail 	= $this->getRequest()->getPost("email");
	        $sFirstName	= $this->getRequest()->getPost("firstname");
	        $sLastName 	= $this->getRequest()->getPost("lastname");
	        $sSex	 	= $this->getRequest()->getPost("sex");
	        $iUserId 	= $this->getRequest()->getPost("us");
	        $iInvId	 	= $this->getRequest()->getPost("inv");
	        $dFechaNacimiento	 	= trim($this->getRequest()->getPost("fechaNacimiento"));
	        $oObj		= new stdClass();
	        $oObj->iId 	= $iInvId;
	        $oObj->sNombreUsuario 	= $sUserName;
	        $oObj->sContrasenia	= $sPassword;
	        $oObj->sNombre		= $sFirstName;
	        $oObj->sApellido	= $sLastName;
	        $oObj->sSexo		= $sSex;
	        $oObj->iTipoDocumentoId	= $iTipoDni;
	    	$oObj->sNumeroDocumento	= $iDni;
	    	$oObj->sEmail		= $sEmail;
	    	$oObj->dFechaNacimiento	= $dFechaNacimiento." 00:00";

    		$res =  IndexController::getInstance()->registrar($oObj,$iUserId);
    		$redirect = "/comunidad/home";
    		$this->getJsonHelper()->setSuccess($res)
                                      ->setRedirect($redirect);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}