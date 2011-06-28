<?php

/**
 * Page Controller para las vistas basicas del modulo comunidad.
 */
class IndexControllerComunidad extends PageControllerAbstract
{
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
    }

    private function setHeadTemplate()
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');
        $tituloVista = $nombreSitio.' | '.$parametros->obtener('METATAG_TITLE');
        $descriptionVista = $parametros->obtener('METATAG_DESCRIPTION');
        $keywordsVista = $parametros->obtener('METATAG_KEYWORDS');

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);
        return $this;
    }

    private function setMenuTemplate()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "topHeaderMenuLeft", "TopHeaderMenuLeftBlock");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "topHeaderMenuRight", "TopHeaderMenuRightBlock");

        //links menu top comunidad
        $this->getTemplate()->set_var("topHeaderMenuHrefComunidad", $this->getRequest()->getBaseUrl().'/comunidad/home');
        $this->getTemplate()->set_var("topHeaderMenuHrefPublicaciones", $this->getRequest()->getBaseUrl().'/comunidad/publicaciones');
        $this->getTemplate()->set_var("topHeaderMenuHrefInstituciones", $this->getRequest()->getBaseUrl().'/comunidad/instituciones');
        $this->getTemplate()->set_var("topHeaderMenuHrefDescargas", $this->getRequest()->getBaseUrl().'/comunidad/descargas');
        $this->getTemplate()->set_var("topHeaderMenuHrefSeguimientos", $this->getRequest()->getBaseUrl().'/seguimientos/home');
        $this->getTemplate()->set_var("topHeaderMenuHrefDatosPersonales", $this->getRequest()->getBaseUrl().'/comunidad/datosPersonales');
        $this->getTemplate()->set_var("topHeaderMenuHrefInvitaciones", $this->getRequest()->getBaseUrl().'/comunidad/invitaciones');
        $this->getTemplate()->set_var("topHeaderMenuHrefSoporte", $this->getRequest()->getBaseUrl().'/comunidad/soporte');
        $this->getTemplate()->set_var("topHeaderMenuHrefCerrarSesion", $this->getRequest()->getBaseUrl().'/logout');
        $this->getTemplate()->set_var("topHeaderMenuHrefPreferencias", $this->getRequest()->getBaseUrl().'/comunidad/preferencias');
        
        $this->getTemplate()->parse("topHeaderMenuLeft", false);
        $this->getTemplate()->parse("topHeaderMenuRight", false);
        return $this;
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate()
                 ->setMenuTemplate();

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $perfilDesc = $perfil->getDescripcion();
            $nombreUsuario = $perfil->getNombreUsuario();

            $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
            $this->getTemplate()->set_var("hrefLogoHeader", "http://www.ufasta.edu.ar");
            $this->getTemplate()->set_var("logoDesc", "SGPAPD");
            $this->getTemplate()->set_var("moduloDesc", "Comunidad");
            
            //nombre seccion
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/home.gui.html", "centerHeaderContRight", "CenterHeaderContRight");
            $this->getTemplate()->set_var("nombreUsuarioLogged", $nombreUsuario);
            $this->getTemplate()->set_var("hrefEditarPerfil", $this->getRequest()->getBaseUrl().'/comunidad/datosPersonales');
            $this->getTemplate()->set_var("hrefAdministrador", $this->getRequest()->getBaseUrl().'/admin');
            //si no es moderador o admin quito el boton al administrador
            if($perfilDesc != 'administrador' && $perfilDesc != 'moderador'){
                $this->getTemplate()->set_var("AdministradorButton", "");
            }

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