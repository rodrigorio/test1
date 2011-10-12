<?php
/**
 * @author Matias Velilla
 */
class DatosPersonalesControllerComunidad extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index()
    {
        $this->formulario();
    }

    /**
     * Procesa el envio desde un formulario de modificacion de datos personales
     */
    public function procesar()
    {
        
    }

    /**
     * Vista con el formulario de modificacion de datos personales
     *
     */
    public function formulario()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();

        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        $this->printMsgTop();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Modificar datos personales");

        //privacidad (columna)
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
        //seteo los valores actuales para los campos
        $aPrivacidad = $usuario->obtenerPrivacidad();
        $this->getTemplate()->set_var($aPrivacidad['email']."EmailSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['telefono']."TelefonoSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['celular']."CelularSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['fax']."FaxSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['curriculum']."CurriculumSelected", "selected = 'selected' ");
        
        //contenido ppal
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerMainCont", "FormularioBlock");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function modificarPrivacidadCampo()
    {
        //si accedio a traves de la url muestra pagina 404 porq es ajax
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $usuario->guardarPrivacidadCampo($this->getRequest()->getPost('nombreCampo'), $this->getRequest()->getPost('valorPrivacidad'));
    }
}