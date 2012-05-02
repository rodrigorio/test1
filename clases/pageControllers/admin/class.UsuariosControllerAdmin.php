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

                    $srcAvatar = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                    if(null !== $oUsuario->getFotoPerfil()){
                        $srcAvatarAmpliar = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getFotoPerfil()->getNombreBigSize();
                    }else{
                        $srcAvatarAmpliar = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar(true);
                    }

                    if($oUsuario->isActivo()){
                        $this->getTemplate()->set_var("sSelectedUsuarioActivo", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedUsuarioSuspendido", "selected='selected'");                        
                    }
                    
                    $sNombreUsuario = $oUsuario->getNombre()." ".$oUsuario->getApellido();
                    $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                    $sDocumento = $aTiposDocumentos[$oUsuario->getTipoDocumento()]." ".$oUsuario->getNumeroDocumento();
                    $sEmail = $oUsuario->getEmail();
                    $sPerfil = AdminController::getInstance()->obtenerDescripcionPerfilUsuario($oUsuario);

                    $this->getTemplate()->set_var("scrAvatarUsuarioAmpliada", $srcAvatarAmpliar);
                    $this->getTemplate()->set_var("scrAvatarUsuario", $srcAvatar);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("sPerfil", $sPerfil);
                    $this->getTemplate()->set_var("sEmail", $sEmail);

                    $this->getTemplate()->parse("UsuariosBlock", true);

                    $this->getTemplate()->set_var("sSelectedUsuarioActivo","");
                    $this->getTemplate()->set_var("sSelectedUsuarioSuspendido","");
                    $i++;
                }
                $this->getTemplate()->set_var("NoRecordsUsuariosBlock", "");
            }else{
                $this->getTemplate()->set_var("UsuariosBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "noRecords", "NoRecordsUsuariosBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay usuarios cargados en el sistema");
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

        if($this->getRequest()->has('modificarUsuario')){
            $this->editarInformacion();
            return;
        }
        
        if($this->getRequest()->has('borrarFotoPerfil')){
            $this->borrarFotoPerfil();
            return;
        }

        if($this->getRequest()->has('cambiarEstado')){
            $this->cambiarEstadoCuentaIntegrante();
            return;
        }
    }

    private function cambiarEstadoCuentaIntegrante()
    {
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
        $estadoUsuario = $this->getRequest()->getParam('estadoUsuario');

        if(empty($iUsuarioId) || !$this->getRequest()->has('estadoUsuario')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        $bActivo = ($estadoUsuario == "1") ? true : false;
        $oUsuario->isActivo($bActivo);

        ComunidadController::getInstance()->guardarUsuario($oUsuario);
    }

    private function borrarFotoPerfil()
    {
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
        
        if(empty($iUsuarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
            ComunidadController::getInstance()->borrarFotoPerfil($oUsuario, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        
        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    public function cambiarPerfil()
    {
        
    }

    public function cerrarCuenta()
    {
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
        if(empty($iUsuarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);            
            $result = AdminController::getInstance()->cerrarCuentaIntegrante($oUsuario, $pathServidor);

            $this->restartTemplate();

            if($result){
                $msg = "El usuario fue eliminado del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado el usuario del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado el usuario del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
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

    public function form()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionUsuarios");

            $this->printMsgTop();            
           
            //los formularios de creacion y edicion son distintos
            if(!$this->getRequest()->has('editar')){
                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "widgetsContent", "HeaderBlock");
                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "FormularioCrearBlock");
            }else{                
                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "FormularioModificarBlock");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }            
    }
    
    private function editarInformacion()
    {

    }

    public function crear()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oUsuario = new stdClass();

            $oUsuario->iTipoDocumentoId = $this->getRequest()->getPost("tipoDocumento");
            $oUsuario->sNumeroDocumento = $this->getRequest()->getPost("numeroDocumento");
            $oUsuario->sNombre = $this->getRequest()->getPost("nombre");
            $oUsuario->sApellido = $this->getRequest()->getPost("apellido");
            $oUsuario->sEmail = $this->getRequest()->getPost("email");
            $oUsuario->sNombreUsuario = $this->getRequest()->getPost("nombreUsuario");
            $oUsuario->sSexo = $this->getRequest()->getPost("sexo");
            $oUsuario->sContrasenia = $this->getRequest()->getPost("contraseniaMD5");

            $fechaNacimientoDia     = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes     = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio    = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $fechaNacimiento = implode('-', $aFechaNacimiento);

            $oUsuario->dFechaNacimiento = $fechaNacimiento;
                      
            $oUsuario = Factory::getUsuarioInstance($oUsuario);

            if(ComunidadController::getInstance()->existeDocumentoPersona($tipoDocumento, $numeroDocumento))
            {
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Ya existe una persona en el sistema con el numero de documento ingresado");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            ComunidadController::getInstance()->guardarUsuario($oUsuario);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            echo $e->getMessage();
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function verDatos(){
        
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
        if(empty($iUsuarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "popUpContent", "FichaUsuarioBlock");

        $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

        $hrefDescargarCvActual = "";
        if(null !== $oUsuario->getCurriculumVitae()){
            $oArchivo = $oUsuario->getCurriculumVitae();
            $hrefDescargarCvActual = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
        }
       
        $iPaisId = "";
        $iProvinciaId = "";
        $iCiudadId = "";
        $sUbicacion = "";
        if(null != $oUsuario->getCiudad()){
            $iCiudadId = $oUsuario->getCiudad()->getId();
            $sUbicacion .= $oUsuario->getCiudad()->getNombre();
            if(null != $oUsuario->getCiudad()->getProvincia()){
                $iProvinciaId = $oUsuario->getCiudad()->getProvincia()->getId();
                $sUbicacion .= " ".$oUsuario->getCiudad()->getProvincia()->getNombre();
                if(null != $oUsuario->getCiudad()->getProvincia()->getPais()){
                    $iPaisId = $oUsuario->getCiudad()->getProvincia()->getPais()->getId();
                    $sUbicacion .= " ".$oUsuario->getCiudad()->getProvincia()->getPais()->getNombre();
                }
            }
        }

        $iInstitucionId = "";
        $sNombreInstitucion = "";
        if(null != $oUsuario->getInstitucion()){
            $iInstitucionId = $oUsuario->getInstitucion()->getId();
            $sNombreInstitucion = $oUsuario->getInstitucion()->getNombre();
        }

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oUsuario->getFotoPerfil()){
            $oFoto = $oUsuario->getFotoPerfil();
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
            $scrFotoPerfilActual = $pathFotoServidorMediumSize;
            $hrefFotoPerfilActualAmpliada = $pathFotoServidorBigSize;
            $this->getTemplate()->set_var("scrFotoPerfilActual", $scrFotoPerfilActual);
            $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $hrefFotoPerfilActualAmpliada);
        }else{
            $this->getTemplate()->unset_blocks("FotoPerfilActualBlock");
        }
        
        $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
        $sDocumento = $aTiposDocumentos[$oUsuario->getTipoDocumento()]." ".$oUsuario->getNumeroDocumento();

        $sNombre = $oUsuario->getNombre()." ".$oUsuario->getApellido();
        $sEmail = $oUsuario->getEmail();
        $sSexo = ($oUsuario->getSexo() == 'm')?"Masculino":"Femenino";
        $sFechaNacimiento = Utils::fechaFormateada($oUsuario->getFechaNacimiento(), "d/m/Y");
        $sCiudadOrigen = $oUsuario->getCiudadOrigen();
        $sCodigoPostal = $oUsuario->getCodigoPostal();
        $sDireccion = $oUsuario->getDomicilio();
        $sTelefono = $oUsuario->getTelefono();
        $sCelular = $oUsuario->getCelular();
        $sFax = $oUsuario->getFax(); 
        $sInstitucionCargo = $oUsuario->getCargoInstitucion();
        $sBiografia = $oUsuario->getBiografia();
        $sEmpresa = $oUsuario->getEmpresa();
        $sSecundaria = $oUsuario->getSecundaria();
        $sUniversidad = $oUsuario->getUniversidad();
        $sCarrera = $oUsuario->getUniversidadCarrera();
        $sFinalizada = ($oUsuario->isCarreraFinalizada())?"Sí":"No";
        $sSitioWeb = $oUsuario->getSitioWeb();       
                       
        $sEspecialidad = "";
        if(null != $oUsuario->getEspecialidad()){
            $sEspecialidad = $oUsuario->getEspecialidad()->getNombre();
        }                

        $this->getTemplate()->set_var("iUsuarioId", $iUsuarioId);
        $this->getTemplate()->set_var("sDocumento", $sDocumento);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sEmail", $sEmail);
        $this->getTemplate()->set_var("sSexo", $sSexo);
        $this->getTemplate()->set_var("sFechaNacimiento", $sFechaNacimiento);
        $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
        $this->getTemplate()->set_var("sCiudadOrigen", $sCiudadOrigen);
        $this->getTemplate()->set_var("sCodigoPostal", $sCodigoPostal);
        $this->getTemplate()->set_var("sDireccion", $sDireccion);
        $this->getTemplate()->set_var("sTelefono", $sTelefono);
        $this->getTemplate()->set_var("sCelular", $sCelular);
        $this->getTemplate()->set_var("sFax", $sFax);
        $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);
        $this->getTemplate()->set_var("sNombreInstitucion", $sNombreInstitucion);
        $this->getTemplate()->set_var("sInstitucionCargo", $sInstitucionCargo);
        $this->getTemplate()->set_var("sBiografia", $sBiografia);
        $this->getTemplate()->set_var("sEmpresa", $sEmpresa);
        $this->getTemplate()->set_var("sSecundaria", $sSecundaria);
        $this->getTemplate()->set_var("sUniversidad", $sUniversidad);
        $this->getTemplate()->set_var("sCarrera", $sCarrera);
        $this->getTemplate()->set_var("sFinalizada", $sFinalizada);
        $this->getTemplate()->set_var("sSitioWeb", $sSitioWeb);
        $this->getTemplate()->set_var("sEspecialidad", $sEspecialidad);
        $this->getTemplate()->set_var("hrefDescargarCvActual", $hrefDescargarCvActual);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
}
