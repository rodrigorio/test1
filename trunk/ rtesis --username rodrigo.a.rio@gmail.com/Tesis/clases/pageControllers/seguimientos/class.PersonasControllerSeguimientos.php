<?php

/**
 * @author Matias Velilla
 * 
 */
class PersonasControllerSeguimientos extends PageControllerAbstract
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
        $this->listar();
    }

    public function agregar()
    {
        if($this->getRequest()->has("popUp")){
            $this->mostrarFormularioPopUp();
        }else{
            $this->mostrarFormulario();
        }
    }

    public function modificar()
    {
        if($this->getRequest()->has("popUp")){
            $this->mostrarFormularioPopUp();
        }else{
            $this->mostrarFormulario();
        }
    }

    private function mostrarFormularioPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/personas.gui.html", "popUpContent", "FormularioBlock");

        if($this->getRequest()->getAction() == "agregar"){
            $this->getTemplate()->unset_blocks("SubmitModificarPersonaBlock");
        }else{
            $iPersonaIdForm = $this->getRequest()->getParam('personaId');
            if(empty($iPersonaIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            $this->getTemplate()->unset_blocks("SubmitCrearPersonaBlock");
        }
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormulario()
    {
        
    }

    /**
     * Con un post se determina si es edicion o alta.
     * Se puede hacer asi porque los permisos de edicion y alta serian los mismos.
     * Todo integrante activo podria crear y tambien modificar una persona.
     *
     * En el procesar tambien se procesa la foto de perfil.
     * La foto de perfil se puede asociar unicamente luego de que se crea la persona
     */
    public function procesar(){}

    public function listar(){}
}