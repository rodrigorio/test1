<?php
class UsuariosControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listar();
    }

    public function listar()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionUsuarios");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "ListadoUsuariosBlock");

            $filtro = array();
            $iRecordPerPage = 5;
            $iPage = $this->getRequest()->getPost("iPage");
            $iPage = strlen($iPage) ? $iPage : 1;
            $iItemsForPage = $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit = ($iPage-1) * $iItemsForPage;
            $sOrderBy = null;
            $sOrder = null;
            $iRecordsTotal = 0;

            $aUsuarios = AdminController::getInstance()->obtenerUsuariosSistema($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            $hrefEditarUsuario = "admin/usuarios-form";

            if(count($aUsuarios) > 0){
            	$i=0;
                foreach($aUsuarios as $oUsuario){

                    $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");


                    $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                    $this->getTemplate()->set_var("hrefEditarUsuario", $hrefEditarUsuario);

                    if($oAccion->isActivo()){
                        $this->getTemplate()->set_var("sSelectedAccionActivada", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedAccionDesactivada", "selected='selected'");
                    }

                    $this->getTemplate()->parse("AccionesBlock", true);

                    $this->getTemplate()->set_var("sSelectedAccionActivada","");
                    $this->getTemplate()->set_var("sSelectedAccionDesactivada","");
                    $i++;
                }
                $this->getTemplate()->set_var("NoRecordsAccionesBlock", "");
            }else{
                $this->getTemplate()->set_var("AccionesBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "noRecords", "NoRecordsAccionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay acciones cargadas en el sistema");
                $this->getTemplate()->parse("noRecords", false);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('ver')){
            $this->verDatos();
            return;
        }

        if($this->getRequest()->has('editar')){
            $this->editarInformacion();
            return;
        }

        if($this->getRequest()->has('suspender')){
            $this->suspenderCuentaIntegrante();
            return;
        }

        if($this->getRequest()->has('activar')){
            $this->activarCuentaIntegrante();
            return;
        }
    }

    public function cambiarPerfil()
    {
        
    }

    public function cerrarCuenta()
    {

    }

    public function crear()
    {

    }

    public function vistaImpresion(){}

    /**
     * Imprime el filtro actual de usuarios
     */
    public function imprimir(){}

    /**
     * Exporta el filtro actual de usuarios
     */
    public function exportar(){}
    
    private function verDatos(){}
    private function editarInformacion(){}
    private function suspenderCuentaIntegrante(){}
    private function activarCuentaIntegrante(){}
}
