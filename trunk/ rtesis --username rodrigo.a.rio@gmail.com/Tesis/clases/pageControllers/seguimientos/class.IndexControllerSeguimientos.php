<?php

/**
 * Page Controller para las vistas basicas del modulo seguimientos.
 *
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.
 */
class IndexControllerSeguimientos extends PageControllerAbstract
{    
    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     *
     */
    static function setCabecera(Templates &$template)
    {
        $request = FrontController::getInstance()->getRequest();

        //links menu top comunidad
        $template->set_var("topHeaderMenuHrefComunidad", $request->getBaseUrl().'/comunidad/home');
        $template->set_var("topHeaderMenuHrefPublicaciones", $request->getBaseUrl().'/comunidad/publicaciones');
        $template->set_var("topHeaderMenuHrefInstituciones", $request->getBaseUrl().'/comunidad/instituciones');
        $template->set_var("topHeaderMenuHrefDescargas", $request->getBaseUrl().'/comunidad/descargas');
        $template->set_var("topHeaderMenuHrefSeguimientos", $request->getBaseUrl().'/seguimientos/home');
        $template->set_var("topHeaderMenuHrefDatosPersonales", $request->getBaseUrl().'/comunidad/datos-personales');
        $template->set_var("topHeaderMenuHrefInvitaciones", $request->getBaseUrl().'/comunidad/invitaciones');
        $template->set_var("topHeaderMenuHrefSoporte", $request->getBaseUrl().'/comunidad/soporte');
        $template->set_var("topHeaderMenuHrefCerrarSesion", $request->getBaseUrl().'/logout');
        $template->set_var("topHeaderMenuHrefPreferencias", $request->getBaseUrl().'/comunidad/preferencias');
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     *
     */
    static function setCenterHeader(Templates &$template){
        $request = FrontController::getInstance()->getRequest();
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $perfilDesc = $perfil->getDescripcion();
        $nombreUsuario = $perfil->getNombreUsuario();

        //lo hago asi para no enroscarme porq es un metodo estatico no puedo usar $this
        $oUploadHelper = new UploadHelper();
        $srcAvatar = $oUploadHelper->getDirectorioUploadFotos().$perfil->getAvatarUsuario();

        $template->set_var("scrAvatarSession", $srcAvatar);
        $template->set_var("nombreUsuarioLogged", $nombreUsuario);
        $template->set_var("hrefEditarPerfil", $request->getBaseUrl().'/comunidad/datos-personales');
        $template->set_var("hrefAdministrador", $request->getBaseUrl().'/admin/home');
        //si no es moderador o admin quito el boton al administrador
        if($perfilDesc != 'administrador' && $perfilDesc != 'moderador'){
            $template->set_var("AdministradorButton", "");
        }        
    }

    /**
     * Es un caso especial, el index del modulo es el listar seguimientos del page controller de seguimientos
     */
    public function index(){
        $seguimientosControllerSeguimientos = new SeguimientosControllerSeguimientos($this->getRequest(), $this->getResponse(), $this->getInvokeArgs());
        $seguimientosControllerSeguimientos->listar();
    }
    
    public function buscarDiscapacitados(){
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $filtro = array("p.apellido" => $this->getRequest()->get('str'));
            $aDiscapacitados = SeguimientosController::getInstance()->obtenerDiscapacitado($filtro);
            
            $vResult = array();
            if(count($aDiscapacitados) > 0){
                foreach($aDiscapacitados as $oDiscapacitado){
                    $obj = new stdClass();
                    $obj->iId = $oDiscapacitado->getId();
                    $obj->sNombre = $oDiscapacitado->getNombre() . " " . $oDiscapacitado->getApellido();
                    $obj->iNumeroDocumento = $oDiscapacitado->getNumeroDocumento();
                    $obj->sRutaFoto = $this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar();
                    $vResult[] = $obj;
                }
            }
            //agrega una url para que el js redireccione
            $this->getJsonHelper()->setSuccess(true)->setValor("discapacitados", $vResult);
        }catch(Exception $e){
            print_r($e);
	}
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }    
}