<?php

/**
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.*
 */
class IndexControllerAdmin extends PageControllerAbstract
{
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/admin/frame01-02.gui.html", "frame");
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/home.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    /*
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setCabecera(Templates $template)
    {
        $request = FrontController::getInstance()->getRequest();
        
        //menu cabecera
        $template->set_var("hrefHomeModuloIndex", $request->getBaseTagUrl());
        $template->set_var("hrefHomeModuloComunidad", $request->getBaseTagUrl()."comunidad/home");
        $template->set_var("hrefHomeModuloSeguimientos", $request->getBaseTagUrl()."seguimientos/home");
        $template->set_var("hrefHomeModuloAdmin", $request->getBaseTagUrl()."admin/home");

        //info user
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $perfilDesc = $perfil->getDescripcion();
        $nombreUsuario = $perfil->getNombreUsuario();

        //lo hago asi para no enroscarme porq es un metodo estatico no puedo usar $this
        $oUploadHelper = new UploadHelper();
        $srcAvatar = $oUploadHelper->getDirectorioUploadFotos().$perfil->getAvatarUsuario();
        
        $template->set_var("scrAvatarSession", $srcAvatar);
        $template->set_var("userName", $nombreUsuario);
        $template->set_var("hrefEditarPerfil", $request->getBaseTagUrl().'comunidad/datos-personales');
        $template->set_var("perfilDescripcion", $perfilDesc);
        $template->set_var("hrefCerrarSesion", $request->getBaseTagUrl().'logout');
    }

    /*
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setMenu(Templates $template, $currentOption = '')
    {
        $request = FrontController::getInstance()->getRequest();

        $template->set_var($currentOption, "class='current'");

        //usuarios
        $template->set_var("sHrefUsuariosListar", $request->getBaseTagUrl()."admin/usuarios");
        
        //moderacion
        $template->set_var("sHrefPersonasModeracion", $request->getBaseTagUrl()."admin/personas-moderacion");
        $template->set_var("sHrefPublicacionesModeracion", $request->getBaseTagUrl()."admin/publicaciones-moderacion");
        $template->set_var("sHrefInstitucionesModeracion", $request->getBaseTagUrl()."admin/instituciones-moderacion");
        $template->set_var("sHrefInstitucionesSolicitudes", $request->getBaseTagUrl()."admin/instituciones-solicitudes");
        $template->set_var("sHrefSoftwareModeracion", $request->getBaseTagUrl()."admin/software-moderacion");

        //denuncias
        $template->set_var("sHrefPublicacionesDenuncias", $request->getBaseTagUrl()."admin/publicaciones-denuncias");
        $template->set_var("sHrefInstitucionesDenuncias", $request->getBaseTagUrl()."admin/instituciones-denuncias");
        $template->set_var("sHrefSoftwareDenuncias", $request->getBaseTagUrl()."admin/software-denuncias");
        
        //especialidades
        $template->set_var("sHrefEspecialidadIndex", $request->getBaseTagUrl()."admin/administrar-especialidad");
        $template->set_var("sHrefEspecialidadCargar", $request->getBaseTagUrl()."admin/nueva-especialidad");
        $template->set_var("sHrefEspecialidadListar", $request->getBaseTagUrl()."admin/listar-especialidad");

        //categorias
        $template->set_var("sHrefCategoriaIndex", $request->getBaseTagUrl()."admin/administrar-categorias");
        $template->set_var("sHrefCategoriaCargar", $request->getBaseTagUrl()."admin/nueva-categoria");
        $template->set_var("sHrefCategoriaListar", $request->getBaseTagUrl()."admin/listar-categoria");

        //publicaciones
        $template->set_var("sHrefPublicacionesListar", $request->getBaseTagUrl()."admin/publicaciones");

        //software
        $template->set_var("sHrefSoftwareListar", $request->getBaseTagUrl()."admin/software");

        //personas
        $template->set_var("sHrefDiscapacitadosListar", $request->getBaseTagUrl()."admin/personas");

        //instituciones
        $template->set_var("sHrefInstitucionesListar", $request->getBaseTagUrl()."admin/instituciones");

        //avanzadas
        $template->set_var("sHrefAccionesPerfil", $request->getBaseTagUrl()."admin/acciones-perfil");
        $template->set_var("sHrefParametros", $request->getBaseTagUrl()."admin/parametros");
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            $this->printMsgTop();

            $this->setCabecera($this->getTemplate());
            $this->setMenu($this->getTemplate());

            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/home.gui.html", "widgetsContent", "WidgetsContent");

            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/home.gui.html", "mainContent", "MainContent");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            print_r($e);
        }
    }
}