<?php

/**
 * Page Controller para las vistas basicas del modulo comunidad.
 *
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.
 */
class IndexControllerComunidad extends PageControllerAbstract
{    
   private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

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
        $template->set_var("topHeaderMenuHrefDatosPersonales", $request->getBaseUrl().'/comunidad/datosPersonales');
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

        $template->set_var("nombreUsuarioLogged", $nombreUsuario);
        $template->set_var("hrefEditarPerfil", $request->getBaseUrl().'/comunidad/datos-personales');
        $template->set_var("hrefAdministrador", $request->getBaseUrl().'/admin/home');
        //si no es moderador o admin quito el boton al administrador
        if($perfilDesc != 'administrador' && $perfilDesc != 'moderador'){
            $template->set_var("AdministradorButton", "");
        }        
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            $this->setCabecera($this->getTemplate());
            $this->setCenterHeader($this->getTemplate());

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Comunidad - Inicio");
            
            //contenido ppal home comunidad
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "pageRightInnerMainCont", "PageRightInnerMainContBlock");


            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));            
         }catch(Exception $e){
            print_r($e);
        }
    }    
}