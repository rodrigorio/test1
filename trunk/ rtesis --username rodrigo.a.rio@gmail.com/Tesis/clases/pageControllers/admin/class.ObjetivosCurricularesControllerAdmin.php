<?php

class ObjetivosCurricularesControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/objetivosCurriculares.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    public function index(){
        $this->listarNiveles();
    }

    public function procesarNivel(){}
    public function listarNiveles()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");
    }
    public function formularioNivel(){}
    public function procesarCiclo(){}
    public function listarCiclos()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");
    }
    public function formularioCiclo(){}
    public function procesarArea(){}
    public function listarAreas()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionEspecialidades");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "ListadoEspecialidadesBlock");
    }
    public function formularioArea(){}
}