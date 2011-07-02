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
        
    }

    /**
     * Procesa el envio desde un formulario de invitacion.
     */
    public function procesar()
    {
    }

    /**
     * Vista para enviar una nueva invitacion
     */
    public function formulario()
    {
    }

    /**
     * Lista de todas las invitaciones realizadas y el estado en el que se encuentran
     */
    public function listado()
    {
    }
}