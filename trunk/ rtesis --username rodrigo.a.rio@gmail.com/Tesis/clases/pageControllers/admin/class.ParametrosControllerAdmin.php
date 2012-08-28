<?php

/**
 * Con este controlador se van a poder administrar las acciones del sistema
 * crearlas, activarlas/desactivarlas y asociarlas a los distintos perfiles del sistema
 */
class AccionesPerfilControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listarAccionesSistema();
    }

    public function listarAccionesSistema()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionAvanzadas");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "mainContent", "ListadoAccionesBlock");

            $filtro = array();
            $iRecordPerPage = 5;
            $iPage = $this->getRequest()->getPost("iPage");
            $iPage = strlen($iPage) ? $iPage : 1;
            $iItemsForPage = $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit = ($iPage-1) * $iItemsForPage;
            $sOrderBy = null;
            $sOrder = null;
            $iRecordsTotal = 0;

            $aAcciones = AdminController::getInstance()->obtenerAccionesSistema($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            $hrefEditarAccion = "admin/acciones-perfil-form";

            if(count($aAcciones) > 0){
            	$i=0;
                foreach($aAcciones as $oAccion){

                    $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");

                    $this->getTemplate()->set_var("sModulo", $oAccion->getModulo());
                    $this->getTemplate()->set_var("sControlador", $oAccion->getControlador());
                    $this->getTemplate()->set_var("sAccion", $oAccion->getNombre());
                    $this->getTemplate()->set_var("sPerfil", $oAccion->getNombreGrupoPerfil());
                    $this->getTemplate()->set_var("iAccionId", $oAccion->getId());
                    $this->getTemplate()->set_var("hrefEditarAccion", $hrefEditarAccion);

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

        if($this->getRequest()->has('eliminarAccion')){
            $this->eliminarAccion();
            return;
        }

        if($this->getRequest()->has('cambiarEstadoAccion')){
            $this->cambiarEstadoAccion();
            return;
        }

        if($this->getRequest()->has('crearAccion')){
            $this->crearAccion();
            return;
        }

        if($this->getRequest()->has('modificarAccion')){
            $this->modificarAccion();
            return;
        }
    }

    private function cambiarEstadoAccion()
    {
        $iAccionId = $this->getRequest()->getParam('iAccionId');
        $estadoAccion = $this->getRequest()->getParam('estadoAccion');

        if(empty($iAccionId) || !$this->getRequest()->has('estadoAccion')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oAccion = AdminController::getInstance()->getAccionById($iAccionId);
        $bActivo = ($estadoAccion == "1") ? true : false;
        $oAccion->isActivo($bActivo);

        AdminController::getInstance()->guardarAccion($oAccion);
    }

    private function eliminarAccion()
    {
        $iAccionId = $this->getRequest()->getParam('iAccionId');
        if(empty($iAccionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oAccion = AdminController::getInstance()->getAccionById($iAccionId);
            $result = AdminController::getInstance()->borrarAccion($oAccion);

            $this->restartTemplate();

            if($result){
                $msg = "La accion fue eliminada del sistema. Tenga en cuenta que los permisos en sesion que estan actualmente para los usuarios se actualizaran en los proximos minutos.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la accion del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la accion del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function form()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionAvanzadas");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/accionesPerfil.gui.html", "mainContent", "FormularioBlock");

            $editar = $this->getRequest()->getParam('editar');
            if(empty($editar))
            {
                //agregar accion
                $this->getTemplate()->unset_blocks("SubmitModificarAccionBlock");
                $this->getTemplate()->unset_blocks("CamposModificarAccionBlock");

                //valores por defecto
                $oAccion = null;
                $iAccionId = "";

                $sModulo = "";
                $sControlador = "";
                $sNombre = "";
                $sGrupoPerfil = "";
                $bActivo = false;

                $sTituloForm = "Agregar";
            }else{
                $iAccionId = $this->getRequest()->getParam('iAccionId');
                if(empty($iAccionId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                $this->getTemplate()->unset_blocks("SubmitCrearAccionBlock");
                $this->getTemplate()->unset_blocks("CamposCrearAccionBlock");
                $oAccion = AdminController::getInstance()->getAccionById($iAccionId);

                $sModulo = $oAccion->getModulo();
                $sControlador = $oAccion->getControlador();
                $sNombre = $oAccion->getNombre();
                $sGrupoPerfil = $oAccion->getNombreGrupoPerfil();
                $bActivo = $oAccion->isActivo();

                $sTituloForm = "Modificar";
                $this->getTemplate()->set_var("iAccionId", $iAccionId);
                $this->getTemplate()->set_var("sModulo", $sModulo);
            }

            $this->getTemplate()->set_var("sTituloFormAccion", $sTituloForm);

            switch($sModulo){
                case "index": $this->getTemplate()->set_var("sSelectedModuloIndex", "selected='selected'"); break;
                case "comunidad": $this->getTemplate()->set_var("sSelectedModuloComunidad", "selected='selected'"); break;
                case "seguimientos": $this->getTemplate()->set_var("sSelectedModuloSeguimientos", "selected='selected'"); break;
                case "admin": $this->getTemplate()->set_var("sSelectedModuloAdmin", "selected='selected'"); break;
            }

            $this->getTemplate()->set_var("sControlador", $sControlador);
            $this->getTemplate()->set_var("sAccion", $sNombre);

            switch($sGrupoPerfil){
                case 'Administrador': $this->getTemplate()->set_var("sSelectedPerfilAdministrador", "selected='selected'"); break;
                case 'Moderador': $this->getTemplate()->set_var("sSelectedPerfilModerador", "selected='selected'"); break;
                case 'Integrante Activo': $this->getTemplate()->set_var("sSelectedPerfilIntegranteActivo", "selected='selected'"); break;
                case 'Integrante Inactivo': $this->getTemplate()->set_var("sSelectedPerfilIntegranteInactivo", "selected='selected'"); break;
                case 'Visitante': $this->getTemplate()->set_var("sSelectedPerfilVisitante", "selected='selected'"); break;
            }

            if($bActivo){
                $this->getTemplate()->set_var("sSelectedAccionActivada", "selected='selected'");
            }else{
                $this->getTemplate()->set_var("sSelectedAccionDesactivada", "selected='selected'");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function crearAccion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("crearAccion", "1");

            $oAccion = new stdClass();

            $oAccion->sModulo = $this->getRequest()->getPost("modulo");
            $oAccion->sControlador = $this->getRequest()->getPost("controlador");
            $oAccion->sNombre = $this->getRequest()->getPost("accion");
            $oAccion->iGrupoPerfilId = $this->getRequest()->getPost("perfil");
            $oAccion->bActivo = ($this->getRequest()->getPost("activo") == "1") ? true : false;

            $oAccion = Factory::getAccionInstance($oAccion);

            if(AdminController::getInstance()->existeAccion($oAccion))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("La accion ya existe en el sistema");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            AdminController::getInstance()->guardarAccion($oAccion);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            echo $e->getMessage();
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function modificarAccion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $this->getJsonHelper()->setValor("modificarAccion", "1");

            $iAccionId = $this->getRequest()->getPost('iAccionId');
            $oAccion = AdminController::getInstance()->getAccionById($iAccionId);

            $oAccion->setNombre($this->getRequest()->getPost("accion"));
            $oAccion->setGrupoPerfilId($this->getRequest()->getPost("perfil"));
            $activo = ($this->getRequest()->getPost("activo") == "1") ? true : false;
            $oAccion->isActivo($activo);

            $result = AdminController::getInstance()->guardarAccion($oAccion);

            $this->getJsonHelper()->setSuccess($result);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}