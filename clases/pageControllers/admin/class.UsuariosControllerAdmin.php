<?php
class UsuariosControllerAdmin extends PageControllerAbstract
{
    /**
     * Corresponde con las columnas del listado que poseen orden ascendente o descendente.
     * Se utiliza en los metodos de pageControllerAbstract
     */
    private $orderByConfig = array('nombre' => array('variableTemplate' => 'orderByNombre',
                                                     'orderBy' => 'p.apellido',
                                                     'order' => 'desc'),
                                   'perfil' => array('variableTemplate' => 'orderByPerfil',
                                                     'orderBy' => 'pe.descripcion',
                                                     'order' => 'desc'),
                                   'suspendido' => array('variableTemplate' => 'orderByActivo',
                                                         'orderBy' => 'u.activo',
                                                         'order' => 'desc'));

    /**
     * Corresponde con los filtros del formulario del listado.
     * Se utiliza para generar los filtros en la persistencia y para generar
     * los parametros que se tienen que arrastrar en los links del paginador
     * Las funciones estan en el pageControllerAbstract
     */
    private $filtrosFormConfig = array('filtroApellido' => 'p.apellido',
                                       'filtroNumeroDocumento' => 'p.numeroDocumento',
                                       'filtroInstitucion' => 'i.nombre',
                                       'filtroCiudad' => 'c.nombre',
                                       'filtroEspecialidad' => 'u.especialidades_id',
                                       'filtroPerfil' => 'u.perfiles_id',
                                       'filtroSuspendido' => 'u.activo');


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
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $perfilDesc = $perfil->getDescripcion();

            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionUsuarios");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "ListadoUsuariosBlock");

            if($perfilDesc != 'administrador'){
                $this->getTemplate()->set_var("PanelAdminBlock", "");
            }

            ///////////// ARMO LOS SELECTS DEL FORMULARIO DEL FILTRO            
            
            //select especialidad
            $aEspecialidades = AdminController::getInstance()->obtenerEspecialidad();
            if(!empty($aEspecialidades)){
                foreach ($aEspecialidades as $oEspecialidad){
                    $value = $oEspecialidad->getId();
                    $text = $oEspecialidad->getNombre();
                    $this->getTemplate()->set_var("iEspecialidadId", $value);
                    $this->getTemplate()->set_var("sFiltroEspecialidad", $text);
                    $this->getTemplate()->parse("OptionFiltroEspecialidadBlock", true);
                }
            }
            
            //select perfil
            $aPerfilesSistema = AdminController::getInstance()->obtenerArrayPerfiles();
            foreach($aPerfilesSistema as $sDescripcion => $iPerfilId){
                $this->getTemplate()->set_var("iPerfilId", $iPerfilId);
                $this->getTemplate()->set_var("sFiltroPerfil", $sDescripcion);
                $this->getTemplate()->parse("OptionFiltroPerfilBlock", true);
            }
            
            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aUsuarios = AdminController::getInstance()->buscarUsuariosSistema($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
            
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
                    
                    $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                    $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                    $sDocumento = $aTiposDocumentos[$oUsuario->getTipoDocumento()]." ".$oUsuario->getNumeroDocumento();
                    $sEmail = $oUsuario->getEmail();
                    $sPerfil = AdminController::getInstance()->obtenerDescripcionPerfilUsuario($oUsuario);

                    $this->getTemplate()->set_var("scrAvatarUsuarioAmpliada", $srcAvatarAmpliar);
                    $this->getTemplate()->set_var("scrAvatarUsuario", $srcAvatar);
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("sPerfil", $sPerfil);
                    $this->getTemplate()->set_var("sEmail", $sEmail);

                    if($perfilDesc != 'administrador'){
                        $this->getTemplate()->set_var("DeleteButton", "");
                    }else{
                        $this->getTemplate()->parse("DeleteButton");
                    }

                    $this->getTemplate()->parse("UsuarioBlock", true);

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

            $params[] = "masUsuarios=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/usuarios-procesar", "listadoUsuariosResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getRequest()->has('fotoPerfil') &&
           !$this->getRequest()->has('curriculum') &&
           !$this->getAjaxHelper()->isAjaxContext()){
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

        if($this->getRequest()->has('checkNumeroDocumento')){
            $this->checkNumeroDocumento();
            return;
        }
        
        if($this->getRequest()->has('checkMailExiste')){
            $this->checkMail();
            return;
        }

        if($this->getRequest()->has('masUsuarios')){
            $this->masUsuarios();
            return;
        }
    }

    private function masUsuarios()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $perfilDesc = $perfil->getDescripcion();
        
        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
        
        $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "ajaxGrillaUsuariosBlock", "GrillaUsuariosBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);
        
        $iRecordsTotal = 0;        
        $aUsuarios = AdminController::getInstance()->buscarUsuariosSistema($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $hrefEditarUsuario = "admin/usuarios-form";       
        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
        
        $respuesta = "";
        if(count($aUsuarios) > 0){

            $this->getTemplate()->set_var("NoRecordsUsuariosBlock", "");            
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

                $sNombreUsuario = $oUsuario->getApellido()." ".$oUsuario->getNombre();
                $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                $sDocumento = $aTiposDocumentos[$oUsuario->getTipoDocumento()]." ".$oUsuario->getNumeroDocumento();
                $sEmail = $oUsuario->getEmail();
                $sPerfil = AdminController::getInstance()->obtenerDescripcionPerfilUsuario($oUsuario);

                $this->getTemplate()->set_var("scrAvatarUsuarioAmpliada", $srcAvatarAmpliar);
                $this->getTemplate()->set_var("scrAvatarUsuario", $srcAvatar);
                $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                $this->getTemplate()->set_var("sPerfil", $sPerfil);
                $this->getTemplate()->set_var("sEmail", $sEmail);

                if($perfilDesc != 'administrador'){
                    $this->getTemplate()->set_var("DeleteButton", "");
                }else{
                    $this->getTemplate()->parse("DeleteButton");
                }

                $this->getTemplate()->parse('UsuarioBlock', true);

                $this->getTemplate()->set_var("sSelectedUsuarioActivo","");
                $this->getTemplate()->set_var("sSelectedUsuarioSuspendido","");
                $i++;
            }

            $paramsPaginador[] = "masUsuarios=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/usuarios-procesar", "listadoUsuariosResult", $paramsPaginador);
                        
        }else{
            $this->getTemplate()->set_var("UsuarioBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay usuarios cargados en el sistema");            
        }
        
        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaUsuariosBlock', false));
    }

    private function checkNumeroDocumento()
    {
        $dataResult = '0';

        $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
        $numeroDocumento = $this->getRequest()->getPost('numeroDocumento');
        $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

        //porque la validacion no se hace con el numero de documento actual
        if( (!empty($iUsuarioId) && $oUsuario->getNumeroDocumento() != $numeroDocumento) || empty($iUsuarioId)){
            if(ComunidadController::getInstance()->existeDocumentoUsuario($numeroDocumento)){
                $dataResult = '1';
            }
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult); 
    }

    private function checkMail()
    {
        $dataResult = '0';

        $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
        $email = $this->getRequest()->getPost('email');
                
        if(ComunidadController::getInstance()->existeMailDb($email, $iUsuarioId)){
            $dataResult = '1';
        }
        
        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);
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
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');
        //el id del perfil nuevo que se le va a asignar al usuario
        $perfil = $this->getRequest()->getParam('perfil');

        if(empty($iUsuarioId) || !$this->getRequest()->has('perfil')){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
        AdminController::getInstance()->cambiarPerfilUsuario($oUsuario, $perfil);
    }

    public function cerrarCuenta()
    {
        $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
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
    public function exportar()
    {
        try{
            $aHeadColumns = array(
                "Tipo Documento",
                "Numero Documento",
                "Nombre",
                "Apellido",
                "E-Mail",
                "Universidad",
                "Especialidad",
                "Institucion",
            );

            $this->getExportarPlanillaHelper()->addFileLine($aHeadColumns);

            //ahora extraigo los datos que ya estan en el filtro del form del listado
            $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
            $this->initOrderBy($sOrderBy = null, $sOrder = null, $this->orderByConfig);
            $aUsuarios = AdminController::getInstance()->buscarUsuariosSistema($filtroSql, $iRecordsTotal = 0, $sOrderBy, $sOrder, $iMinLimit = null, $iItemsForPage = null);

            //agrego las filas con las que se va a crear el archivo.
            if(count($aUsuarios) > 0){
                foreach($aUsuarios as $oUsuario){

                    $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                    $tipoDocumento = $aTiposDocumentos[$oUsuario->getTipoDocumento()];
                    $numeroDocumento = $oUsuario->getNumeroDocumento();
                    $nombre = $oUsuario->getNombre();
                    $apellido = $oUsuario->getApellido();
                    $email = $oUsuario->getEmail();

                    if(null !== $oUsuario->getUniversidad()){
                        $universidad = $oUsuario->getUniversidad();
                    }else{
                        $universidad = "-";
                    }

                    if(null !== $oUsuario->getEspecialidad()){
                        $especialidad = $oUsuario->getEspecialidad()->getNombre();
                    }else{
                        $especialidad = "-";
                    }

                    if(null !== $oUsuario->getInstitucion()){
                        $institucion = $oUsuario->getInstitucion()->getNombre();
                    }else{
                        $institucion = "-";
                    }

                    $aColumns = array(
                        $tipoDocumento,
                        $numeroDocumento,
                        $nombre,
                        $apellido,
                        $email,
                        $universidad,
                        $especialidad,
                        $institucion,
                    );

                    $this->getExportarPlanillaHelper()->addFileLine($aColumns);
                }
            }

            $oPerfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $oUsuario = $oPerfil->getUsuario();
            $iUsuarioId = $oUsuario->getId();

            //genero el archivo y la descarga
            list($nombreArchivo, $tipoMimeArchivo, $nombreServidorArchivo) = $this->getExportarPlanillaHelper()->generarArchivo($iUsuarioId);
            
            $oArchivo = new stdClass();
            $oArchivo->sNombre = $nombreArchivo;
            $oArchivo->sNombreServidor = $nombreServidorArchivo;
            $oArchivo->sTipoMime = $tipoMimeArchivo;
            $oPlanilla = Factory::getArchivoInstance($oArchivo);

            $this->getDownloadHelper()->utilizarDirectorioDownloads()
                                      ->generarDescarga($oPlanilla);
            
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function form()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionUsuarios");

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $perfilDesc = $perfil->getDescripcion();

            $this->printMsgTop();

            $aMeses = array('01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo',
                            '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre',
                            '11' => 'Noviembre', '12' => 'Diciembre');
           
            //los formularios de creacion y edicion son distintos
            if(!$this->getRequest()->has('editar')){

                ///////////
                //CREAR ///
                ///////////

                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "widgetsContent", "HeaderBlock");
                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "FormularioCrearBlock");

                //armo el select con los tipos de documentos cargados en db
                $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                foreach ($aTiposDocumentos as $value => $text){
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", strtoupper($text));
                    $this->getTemplate()->parse("OptionSelectDocumento", true);
                }

                //dia mes año
                for($i = 1; $i <= 31; $i++){
                    $value = (string)$i;
                    if($i<10){ $value = "0".$value; }
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $value);
                    $this->getTemplate()->parse("OptionSelectDia", true);
                }

                foreach ($aMeses as $value => $text){
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $text);
                    $this->getTemplate()->parse("OptionSelectMes", true);
                }

                $anioActual = date("Y");
                for($i = $anioActual; $i >= 1905; $i--){
                    $value = (string)$i;
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $value);
                    $this->getTemplate()->parse("OptionSelectAnio", true);
                }
                
            }else{

                $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');

                if(empty($iUsuarioId)){
                    throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
                }

                ////////////
                //EDITAR ///
                ////////////

                $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "mainContent", "FormularioModificarBlock");

                $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

                if($perfilDesc != 'administrador'){
                    $this->getTemplate()->set_var("PanelAdminFormModifBlock", "");
                }else{
                    $sPerfilUsuario = AdminController::getInstance()->obtenerDescripcionPerfilUsuario($usuario);
                    $aPerfilesSistema = AdminController::getInstance()->obtenerArrayPerfiles();
                    foreach ($aPerfilesSistema as $sDescripcion => $iPerfilId){
                        $this->getTemplate()->set_var("iPerfilId", $iPerfilId);
                        $this->getTemplate()->set_var("sPerfilDesc", $sDescripcion);
                        if($sPerfilUsuario == $sDescripcion){
                            $this->getTemplate()->set_var("sSelectedPerfil", "selected='selected'");
                        }
                        $this->getTemplate()->parse("OptionSelectPerfilModif", true);
                        $this->getTemplate()->set_var("sSelectedPerfil", "");
                    }
                }

                $this->getTemplate()->set_var("iUsuarioId", $iUsuarioId);

                /////////////////form info basica
                $tipoDocumentoId = $usuario->getTipoDocumento();
                $numeroDocumento = $usuario->getNumeroDocumento();
                $nombre = $usuario->getNombre();
                $apellido = $usuario->getApellido();
                $email = $usuario->getEmail();
                $sexo = $usuario->getSexo();
                $fechaNacimiento = $usuario->getFechaNacimiento();
                list($nacimientoAnio, $nacimientoMes, $nacimientoDia) = explode("-", $fechaNacimiento);

                //armo el select con los tipos de documentos cargados en db
                $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                foreach ($aTiposDocumentos as $value => $text){
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", strtoupper($text));
                    if($tipoDocumentoId == $value){
                        $this->getTemplate()->set_var("sSelected", "selected='selected'");
                    }
                    $this->getTemplate()->parse("OptionSelectDocumento", true);
                    $this->getTemplate()->set_var("sSelected", "");
                }

                $this->getTemplate()->set_var("sNumeroDocumento", $numeroDocumento);
                $this->getTemplate()->set_var("sNombre", $nombre);
                $this->getTemplate()->set_var("sApellido", $apellido);
                $this->getTemplate()->set_var("sEmail", $email);

                if($sexo == 'm'){
                    $this->getTemplate()->set_var("sSelectedMasculino", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedFemenino", "selected='selected'");
                }

                for($i = 1; $i <= 31; $i++){
                    $value = (string)$i;
                    if($i<10){ $value = "0".$value; }

                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $value);
                    if($nacimientoDia == $i){
                        $this->getTemplate()->set_var("sSelected", "selected='selected'");
                    }
                    $this->getTemplate()->parse("OptionSelectDia", true);
                    $this->getTemplate()->set_var("sSelected", "");
                }

                foreach ($aMeses as $value => $text){
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $text);
                    if($nacimientoMes == $value){
                        $this->getTemplate()->set_var("sSelected", "selected='selected'");
                    }
                    $this->getTemplate()->parse("OptionSelectMes", true);
                    $this->getTemplate()->set_var("sSelected", "");
                }

                $anioActual = date("Y");
                for($i = $anioActual; $i >= 1905; $i--){
                    $value = (string)$i;
                    $this->getTemplate()->set_var("iValue", $value);
                    $this->getTemplate()->set_var("sDescripcion", $value);
                    if($nacimientoAnio == $i){
                        $this->getTemplate()->set_var("sSelected", "selected='selected'");
                    }
                    $this->getTemplate()->parse("OptionSelectAnio", true);
                    $this->getTemplate()->set_var("sSelected", "");
                }

                ///////////////////form info contacto
                $arrayPaises = array();
                $iRecordsTotalPais = 0;
                $listaPaises = ComunidadController::getInstance()->listaPaises($arrayPaises, $iRecordsTotalPais, null,  null,  null,  null);
                foreach ($listaPaises as $oPais){
                    if(null !== $usuario->getCiudadId() && $usuario->getCiudad()->getProvincia()->getPais()->getId() == $oPais->getId()){
                        $this->getTemplate()->set_var("sPaisSelect", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sPaisSelect", "");
                    }
                    $this->getTemplate()->set_var("iPaisId", $oPais->getId());
                    $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
                    $this->getTemplate()->parse("ListaPaisesBlock", true);
                }

                if(null !== $usuario->getCiudadId()){
                    $listaProvincias = ComunidadController::getInstance()->listaProvinciasByPais($usuario->getCiudad()->getProvincia()->getPais()->getId());
                    foreach ($listaProvincias as $oProvincia){
                        if($usuario->getCiudad()->getProvincia()->getId() == $oProvincia->getId()){
                            $this->getTemplate()->set_var("sProvinciaSelect", "selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sProvinciaSelect", "");
                        }
                        $this->getTemplate()->set_var("iProvinciaId", $oProvincia->getId());
                        $this->getTemplate()->set_var("sProvinciaNombre", $oProvincia->getNombre());
                        $this->getTemplate()->parse("ListaProvinciasBlock", true);
                    }

                    $listaCiudades = ComunidadController::getInstance()->listaCiudadByProvincia($usuario->getCiudad()->getProvincia()->getId());
                    foreach($listaCiudades as $oCiudad){
                        if($usuario->getCiudad()->getId() == $oCiudad->getId()){
                            $this->getTemplate()->set_var("sCiudadSelect", "selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sCiudadSelect", "");
                        }
                        $this->getTemplate()->set_var("iCiudadId", $oCiudad->getId());
                        $this->getTemplate()->set_var("sCiudadNombre", $oCiudad->getNombre());
                        $this->getTemplate()->parse("ListaCiudadesBlock", true);
                    }
                }

                $sCiudadOrigen   = $usuario->getCiudadOrigen();
                $sCodigoPostal   = $usuario->getCodigoPostal();
                $sDomicilio      = $usuario->getDomicilio();
                $sTelefono       = $usuario->getTelefono();
                $sCelular        = $usuario->getCelular();
                $sFax            = $usuario->getFax();

                $this->getTemplate()->set_var("sCiudadOrigen", $sCiudadOrigen);
                $this->getTemplate()->set_var("sCodigoPostal", $sCodigoPostal);
                $this->getTemplate()->set_var("sDireccion", $sDomicilio);
                $this->getTemplate()->set_var("sTelefono", $sTelefono);
                $this->getTemplate()->set_var("sCelular", $sCelular);
                $this->getTemplate()->set_var("sFax", $sFax);

                ///////////////////////// form foto de perfil
                if(null !== $usuario->getFotoPerfil()){
                    $oFoto = $usuario->getFotoPerfil();

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();

                    $this->getTemplate()->set_var("scrFotoPerfilActual", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);

                    $this->getTemplate()->parse("FotoPerfilActualFormBlock");
                }else{
                    $this->getTemplate()->unset_blocks("FotoPerfilActualFormBlock");
                }

                $nombreInputFile = 'fotoPerfil';
                $this->getUploadHelper()->setTiposValidosFotos();

                $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
                $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
                $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());


                /////////////////////////form info profesional
                $sCargoInstitucion   = $usuario->getCargoInstitucion();
                $sBiografia          = $usuario->getBiografia();
                $sEmpresa            = $usuario->getEmpresa();
                $sUniversidad        = $usuario->getUniversidad();
                $sUniversidadCarrera = $usuario->getUniversidadCarrera();
                $bCarreraFinalizada  = $usuario->isCarreraFinalizada();
                $sSecundaria         = $usuario->getSecundaria();
                $sSitioWeb           = $usuario->getSitioWeb();

                //verifico que posea los objetos asociados
                $iInstitucionId = "";
                $sInstitucion = "";
                if(null != $usuario->getInstitucion()){
                    $iInstitucionId = $usuario->getInstitucion()->getId();
                    $sInstitucion = $usuario->getInstitucion()->getNombre();
                }

                $iEspecialidadId = "";
                if(null != $usuario->getEspecialidad()){
                    $iEspecialidadId = $usuario->getEspecialidad()->getId();
                }

                $this->getTemplate()->set_var("sInstitucion", $sInstitucion);
                $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);
                $this->getTemplate()->set_var("sCargoInstitucion", $sCargoInstitucion);
                $this->getTemplate()->set_var("sBiografia", $sBiografia);
                $this->getTemplate()->set_var("sEmpresa", $sEmpresa);
                $this->getTemplate()->set_var("sUniversidad", $sUniversidad);
                $this->getTemplate()->set_var("sUniversidadCarrera", $sUniversidadCarrera);
                $this->getTemplate()->set_var("sSecundaria", $sSecundaria);
                $this->getTemplate()->set_var("sSitioWeb", $sSitioWeb);

                if($bCarreraFinalizada){
                    $this->getTemplate()->set_var("sSelectedFinalizadaSi", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedFinalizadaNo", "selected='selected'");
                }

                //select con especialidades
                $aEspecialidades = AdminController::getInstance()->obtenerEspecialidad();
                if(!empty($aEspecialidades)){
                    foreach ($aEspecialidades as $oEspecialidad){
                        $value = $oEspecialidad->getId();
                        $text = $oEspecialidad->getNombre();
                        $this->getTemplate()->set_var("iEspecialidadId", $value);
                        $this->getTemplate()->set_var("sEspecialidadNombre", $text);
                        if($iEspecialidadId == $value){
                            $this->getTemplate()->set_var("sDatosPersonalesEspecialidadSelect", "selected='selected'");
                        }
                        $this->getTemplate()->parse("ListaEspecialidadesBlock", true);
                        $this->getTemplate()->set_var("sDatosPersonalesEspecialidadSelect", "");
                    }
                }

                /////////////////////// form curriculum vitae
                $nombreInputFile = 'curriculum';
                $this->getUploadHelper()->setTiposValidosDocumentos();

                //si ya tiene curriculum que aparezca.
                if(null !== $usuario->getCurriculumVitae()){
                    $oArchivo = $usuario->getCurriculumVitae();

                    $this->getTemplate()->set_var("sNombreArchivo", $oArchivo->getNombre());
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("sFechaArchivo", $oArchivo->getFechaAlta());

                    $this->getTemplate()->set_var("hrefDescargarCvActual", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor());

                    $this->getTemplate()->parse("CurriculumActualFormBlock");
                }else{
                    $this->getTemplate()->unset_blocks("CurriculumActualFormBlock");
                }

                //form para ingresar uno nuevo
                $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
                $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
                $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }            
    }
    
    private function editarInformacion()
    {
        if($this->getRequest()->has('formInfoBasicaSubmit')){
            $this->procesarFormInfoBasica();
            return;
        }

        if($this->getRequest()->has('formInfoContactoSubmit')){
            $this->procesarFormInfoContacto();
            return;
        }

        if($this->getRequest()->has('formInfoProfesionalSubmit')){
            $this->procesarFormInfoProfesional();
            return;
        }

        if($this->getRequest()->has('fotoPerfil')){
            $this->procesarFormFotoPerfil();
            return;
        }

        if($this->getRequest()->has('curriculum')){
            $this->procesarFormCurriculum();
            return;
        }        
    }

    private function procesarFormInfoBasica(){
        try{
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
            if(empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

            $tipoDocumento  = $this->getRequest()->getPost("tipoDocumento");
            $nroDocumento   = $this->getRequest()->getPost("nroDocumento");
            $nombre         = $this->getRequest()->getPost("nombre");
            $apellido       = $this->getRequest()->getPost("apellido");
            $email          = $this->getRequest()->getPost("email");
            $contraseniaNuevaMD5    = $this->getRequest()->getPost("contraseniaNuevaMD5");
            $sexo                   = $this->getRequest()->getPost("sexo");
            $fechaNacimientoDia     = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes     = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio    = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $fechaNacimiento = implode('-', $aFechaNacimiento);

            $usuario->setTipoDocumento($tipoDocumento);
            $usuario->setNumeroDocumento($nroDocumento);
            $usuario->setNombre($nombre);
            $usuario->setApellido($apellido);
            $usuario->setEmail($email);
            //sino se borra la que ya estaba
            if(!empty($contraseniaNuevaMD5)){
                $usuario->setContrasenia($contraseniaNuevaMD5);
            }
            $usuario->setSexo($sexo);
            $usuario->setFechaNacimiento($fechaNacimiento);

            ComunidadController::getInstance()->guardarUsuario($usuario);

            //si tiene datos minimos lo paso a integrante activo directamente
            if(ComunidadController::getInstance()->cumpleIntegranteActivo($usuario)){
                AdminController::getInstance()->setIntegranteActivoUsuario($usuario);
            }

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function procesarFormInfoContacto(){
        try{
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
            if(empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();

            $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

            $iCiudadId      = $this->getRequest()->getPost("ciudad");
            $sCiudadOrigen  = $this->getRequest()->getPost("ciudadOrigen");
            $sCodigoPostal  = $this->getRequest()->getPost("codigoPostal");
            $sDomicilio     = $this->getRequest()->getPost("direccion");
            $sTelefono      = $this->getRequest()->getPost("telefono");
            $sCelular       = $this->getRequest()->getPost("celular");
            $sFax           = $this->getRequest()->getPost("fax");

            //internamente se fija de levantar de nuevo el objeto ciudad si es != null
            $usuario->setCiudadId($iCiudadId);
            $usuario->setCiudadOrigen($sCiudadOrigen);
            $usuario->setCodigoPostal($sCodigoPostal);
            $usuario->setDomicilio($sDomicilio);
            $usuario->setTelefono($sTelefono);
            //estos dos no son obligatorios, si se envian con cadena vacia elimina los valores viejos
            $usuario->setCelular($sCelular);
            $usuario->setFax($sFax);

            ComunidadController::getInstance()->guardarUsuario($usuario);

            //si tiene datos minimos lo paso a integrante activo directamente
            if(ComunidadController::getInstance()->cumpleIntegranteActivo($usuario)){
                AdminController::getInstance()->setIntegranteActivoUsuario($usuario);
            }

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function procesarFormInfoProfesional(){
        try{
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
            if(empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();

            $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);

            $iInstitucionId     = $this->getRequest()->getPost("institucionId");
            $sCargoInstitucion  = $this->getRequest()->getPost("cargoInstitucion");
            $sBiografia         = $this->getRequest()->getPost("biografia");
            $sEmpresa           = $this->getRequest()->getPost("empresa");
            $sUniversidad       = $this->getRequest()->getPost("universidad");
            $sUniveridadCarrera = $this->getRequest()->getPost("universidadCarrera");
            $bCarreraFinalizada = $this->getRequest()->getPost("carreraFinalizada") ? true : false;
            $sSecundaria        = $this->getRequest()->getPost("secundaria");
            $sSitioWeb          = $this->getRequest()->getPost("sitioWeb");
            $iEspecialidadId    = $this->getRequest()->getPost("especialidad");

            $usuario->setInstitucionId($iInstitucionId);
            $usuario->setCargoInstitucion($sCargoInstitucion);
            $usuario->setBiografia($sBiografia);
            $usuario->setEmpresa($sEmpresa);
            $usuario->setUniversidad($sUniversidad);
            $usuario->setUniversidadCarrera($sUniveridadCarrera);
            $usuario->isCarreraFinalizada($bCarreraFinalizada);
            $usuario->setSecundaria($sSecundaria);
            $usuario->setSitioWeb($sSitioWeb);

            //no piso de una porque hay que hacer una consulta para obtener, entonces me gasto en fijarme si es diferente
            if(null === $usuario->getEspecialidad() || $usuario->getEspecialidad()->getId() != $iEspecialidadId){
                $filtro = array("e.id" => $iEspecialidadId);
                $aEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtro);
                $usuario->setEspecialidad($aEspecialidad[0]);
            }

            ComunidadController::getInstance()->guardarUsuario($usuario);

            //si tiene datos minimos lo paso a integrante activo directamente
            if(ComunidadController::getInstance()->cumpleIntegranteActivo($usuario)){
                AdminController::getInstance()->setIntegranteActivoUsuario($usuario);
            }

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function procesarFormCurriculum(){
        try{
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
            if(empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            $nombreInputFile = 'curriculum';

            $this->getUploadHelper()->setTiposValidosDocumentos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){
                $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
                $idItem = $usuario->getId();

                list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, 'curriculum', 'curriculum');
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

                try{
                    ComunidadController::getInstance()->guardarCurriculumUsuario($usuario, $nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor);
                    $oArchivo = $usuario->getCurriculumVitae();

                    $this->restartTemplate();
                    $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "curriculumActualForm", "CurriculumActualFormBlock");

                    $this->getTemplate()->set_var("sNombreArchivo", $oArchivo->getNombre());
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("sFechaArchivo", $oArchivo->getFechaAlta());
                    $this->getTemplate()->set_var("hrefDescargarCvActual", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor());

                    //si tiene datos minimos lo paso a integrante activo directamente
                    if(ComunidadController::getInstance()->cumpleIntegranteActivo($usuario)){
                        AdminController::getInstance()->setIntegranteActivoUsuario($usuario);
                    }

                    $respuesta = "1; ".$this->getTemplate()->pparse('curriculumActual', false);

                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){                    
                    $respuesta = "0; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){            
            $respuesta = "0; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }

    private function procesarFormFotoPerfil()
    {
        try{
            $iUsuarioId = $this->getRequest()->getPost('iUsuarioId');
            if(empty($iUsuarioId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $nombreInputFile = 'fotoPerfil';

            $this->getUploadHelper()->setTiposValidosFotos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){
                $usuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
                $idItem = $usuario->getId();

                //un array con los datos de las fotos
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($idItem, 'fotoPerfil');
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);

                try{
                    ComunidadController::getInstance()->guardarFotoPerfil($aNombreArchivos, $pathServidor, $usuario);

                    $oFoto = $usuario->getFotoPerfil();

                    $this->restartTemplate();
                    $this->getTemplate()->load_file_section("gui/vistas/admin/usuarios.gui.html", "contFotoPerfilActual", "FotoPerfilActualFormBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("scrFotoPerfilActual", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);

                    $respuesta = "1; ".$this->getTemplate()->pparse('contFotoPerfilActual', false);
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    $respuesta = "0; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            $respuesta = "0; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }

    public function crear()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oUsuario = new stdClass();

            $oUsuario->iTipoDocumentoId = $this->getRequest()->getPost("tipoDocumento");
            $oUsuario->sNumeroDocumento = $this->getRequest()->getPost("nroDocumento");
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
            $this->getTemplate()->set_var("hrefDescargarCvActual", $hrefDescargarCvActual);
        }else{
            $this->getTemplate()->set_var("DescargarButton", "");
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
        $this->getTemplate()->set_var("sEspecialidad", $sEspecialidad);

        if(null !== $sSitioWeb){
            $this->getTemplate()->set_var("sSitioWeb", $sSitioWeb);
        }else{
            $this->getTemplate()->set_var("SitioWebButton", "");
        }
               
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
}