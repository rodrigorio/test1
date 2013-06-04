<?php

/**
 * @author Matias Velilla
 *
 */
class EntradasControllerSeguimientos extends PageControllerAbstract
{  
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-02.gui.html", "frame");
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
                 
            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Entradas por fecha");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            //$this->getTemplate()->load_file_section("gui/vistas/seguimientos/entradas.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

         }catch(Exception $e){
            print_r($e);
        } 
    }       
    
    public function buscarSeguimientos(){

        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "ajaxGrillaSeguimientosBlock", "GrillaSeguimientosBlock");
                
        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

        $iRecordsTotal = 0;
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $filtroSql["u.id"] = $perfil->getUsuario()->getId();
        $aSeguimientos = SeguimientosController::getInstance()->buscarSeguimientos($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

        if(count($aSeguimientos) > 0){

            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            $this->getTemplate()->set_var("NoRecordsGrillaSeguimientosBlock", "");

            foreach ($aSeguimientos as $oSeguimiento){

                $sEstadoSeguimiento = $oSeguimiento->getEstado();
                $hrefAmpliarSeguimiento = $this->getUrlFromRoute("seguimientosSeguimientosVer", true);

                $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
                $this->getTemplate()->set_var("sSeguimientoPersona", $oSeguimiento->getDiscapacitado()->getNombreCompleto());
                $this->getTemplate()->set_var("iPersonaId", $oSeguimiento->getDiscapacitado()->getId());

                $this->getTemplate()->set_var("sSeguimientoTipo", $aTiposSeguimientos[get_class($oSeguimiento)]);
                $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oSeguimiento->getDiscapacitado()->getNumeroDocumento());
                $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

                $this->getTemplate()->set_var("sEstadoSeguimiento", "Activo");
                if($sEstadoSeguimiento == "activo"){
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                }
                $this->getTemplate()->parse("EstadoSeguimientoBlock",false);

                $this->getTemplate()->set_var("sEstadoSeguimiento", "Detenido");
                if($sEstadoSeguimiento == "detenido"){
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "");
                }
                $this->getTemplate()->parse("EstadoSeguimientoBlock",true);

                if($sEstadoSeguimiento == "activo"){
                    $this->getTemplate()->set_var("sEstadoClass", "");
                }else{
                    $this->getTemplate()->set_var("sEstadoClass", "disabled");
                }

                $this->getTemplate()->set_var("hrefAmpliarSeguimiento", $hrefAmpliarSeguimiento);

                $srcAvatarPersona = $this->getUploadHelper()->getDirectorioUploadFotos().$oSeguimiento->getDiscapacitado()->getNombreAvatar();
                $this->getTemplate()->set_var("scrAvatarPersona", $srcAvatarPersona);

                $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
                $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
                $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

                //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
                $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
                $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

                $this->getTemplate()->parse("SeguimientoBlock", true);                               
            }

            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/buscar-seguimientos", "listadoSeguimientosResult", $paramsPaginador);

        }else{
            $this->getTemplate()->set_var("SeguimientoBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay seguimientos para el filtro actual.");
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('ajaxGrillaSeguimientosBlock', false));
    }

    /**
     * Form Nuevo Seguimiento
     */
    public function nuevoSeguimiento(){        
        try{
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaHome();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Seguimientos");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioCrearSeguimientoBlock");

            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            $this->getTemplate()->set_var("sSeguimientoSCCValue", "SeguimientoSCC");
            $this->getTemplate()->set_var("sSeguimientoSCCNombre", $aTiposSeguimientos['SeguimientoSCC']);
            $this->getTemplate()->set_var("sSeguimientoPersonalizadoValue", "SeguimientoPersonalizado");
            $this->getTemplate()->set_var("sSeguimientoPersonalizadoNombre", $aTiposSeguimientos['SeguimientoPersonalizado']);
            
            $aPracticas = SeguimientosController::getInstance()->obtenerPracticas();
            foreach($aPracticas as $oPractica){
                $this->getTemplate()->set_var("iPracticaId", $oPractica->getId());
                $this->getTemplate()->set_var("sPracticaNombre", $oPractica->getNombre());
                $this->getTemplate()->parse("OptionPracticaBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
         }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Guardar Seguimiento Nuevo
     *
     * El modificar tiene que ser metodo aparte porque no podes cambiar el tipo una vez creado.
     */
    public function procesarSeguimiento(){

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sTipoSeguimiento = $this->getRequest()->getPost('tipoSeguimiento');
            $iPersonaId = $this->getRequest()->getPost('personaId');
            $sFrecuencias   = $this->getRequest()->getPost('frecuencias');
            $sDiaHorario    = $this->getRequest()->getPost('diaHorario');

            $filtro = array("s.discapacitados_id" => $iPersonaId);

            $iRecordsTotal = 0;
            $aSeguimientos = SeguimientosController::getInstance()->obtenerSeguimientos($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(count($aSeguimientos) > 2){
                $this->getJsonHelper()->setSuccess(false)->setMessage("La persona a la que quiere hacer un seguimiento ya posee 2. No se puede agregar mas de 2 seguimientos a una persona.");
                $this->getJsonHelper()->sendJsonAjaxResponse(); 
                return;
            }
            /*if(count($aSeguimientos) > 0){
                if(get_class($aSeguimientos[0]) == $sTipoSeguimiento){
                     $this->getJsonHelper()->setSuccess(false)->setMessage("No puede agregar 2 seguimientos del mismo tipo a una persona");
                     $this->getJsonHelper()->sendJsonAjaxResponse();
                     return;
                }
            }*/

            $iPracticaId  = $this->getRequest()->getPost('practica');
            $oPractica = SeguimientosController::getInstance()->getPracticaById($iPracticaId);

            $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($iPersonaId);

            $oSeguimiento = new stdClass();
            $oSeguimiento->oPractica = $oPractica;
            $oSeguimiento->sFrecuenciaEncuentros = $sFrecuencias;
            $oSeguimiento->sDiaHorario = $sDiaHorario;
            $oSeguimiento->oDiscapacitado = $oDiscapacitado;
            $oSeguimiento->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
                        
            switch($sTipoSeguimiento)
            {
                case self::TIPO_SEGUIMIENTO_SCC:
                    $oSeguimiento = Factory::getSeguimientoSCCInstance($oSeguimiento);
                    break;
                case self::TIPO_SEGUIMIENTO_PERSONALIZADO:
                    $oSeguimiento = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                    break;
            }            
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            if($resultado){
                $this->getJsonHelper()->setMessage("El seguimiento fue creado con exito");
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
            
        }catch(Exception $e){
           $this->getJsonHelper()->setMessage($e->getMessage());
           $this->getJsonHelper()->setSuccess(false);
        }
        
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function formModificarSeguimiento()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "popUpContent", "FormularioModificarSeguimientoBlock");

        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);

        $iPracticaId = $oSeguimiento->getPractica()->getId();
        $sFrecuenciaEncuentros = $oSeguimiento->getFrecuenciaEncuentros();
        $sDiaHorario = $oSeguimiento->getDiaHorario();
        
        $aPracticas = SeguimientosController::getInstance()->obtenerPracticas();
        foreach($aPracticas as $oPractica){
            $this->getTemplate()->set_var("iPracticaId", $oPractica->getId());
            $this->getTemplate()->set_var("sPracticaNombre", $oPractica->getNombre());
            if($oPractica->getId() == $iPracticaId){
                $this->getTemplate()->set_var("sPracticaSelected", "selected='selected'");
            }            
            $this->getTemplate()->parse("OptionPracticaBlock", true);
            $this->getTemplate()->set_var("sPracticaSelected", "");
        }
        
        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $sFrecuenciaEncuentros);
        $this->getTemplate()->set_var("sDiaHorario", $sDiaHorario);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));      
    }

    public function guardarSeguimiento()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoIdForm');
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar este seguimiento", 401);
            }

            $iPracticaId  = $this->getRequest()->getPost('practica');
            $oPractica = SeguimientosController::getInstance()->getPracticaById($iPracticaId);

            $oSeguimiento->setPractica($oPractica);
            $oSeguimiento->setDiaHorario($this->getRequest()->getPost("diaHorario"));
            $oSeguimiento->setFrecuenciaEncuentros($this->getRequest()->getPost("frecuencias"));

            SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            
            $this->getJsonHelper()->setMessage("El seguimiento se ha modificado con éxito. Los cambios se veran cuando refresque la pagina.");
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function cambiarEstadoSeguimientos()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iIdSeguimiento = $this->getRequest()->getPost('iSeguimientoId');
        $sEstadoSeguimiento = $this->getRequest()->getPost('estadoSeguimiento');

        if(empty($iIdSeguimiento) || empty($sEstadoSeguimiento)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
     
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar este seguimiento", 401);
            }

            switch($sEstadoSeguimiento)
            {
                case "Activo":
                    $oSeguimiento->setEstadoActivo();
                    break;
                case "Detenido":
                    $oSeguimiento->setEstadoDetenido();
                    break;
            }
                        
            SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    public function editarAntecedentes(){

        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}
        
        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionEditarAntecedentesSeguimiento";

            $this->setFrameTemplate()
                 ->setJSAntecedentes()
                 ->setHeadTag()
                 ->setMenuDerechaVerSeguimiento($aCurrentOptions);

            SeguimientosControllerSeguimientos::setFichaPersonaMenuDerechaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();
            
            //titulo seccion
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            }else{
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            }
            $this->getTemplate()->set_var("subtituloSeccion", "antecedentes");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "pageRightInnerMainCont", "FormularioBlock");
          
            //form para ingresar uno nuevo
            $this->getUploadHelper()->setTiposValidosDocumentos();
            $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
            
            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);
            
            $this->getTemplate()->set_var("sAntecedentes", $oSeguimiento->getAntecedentes());

            //si ya tiene un archivo de antecedentes que aparezca.
            if(null !== $oSeguimiento->getArchivoAntecedentes()){
                $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

                $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
                $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
                $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
                $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

                $this->getTemplate()->set_var("hrefDescargarAntActual", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());

                $this->getTemplate()->parse("ArchivoAntecedentesActualBlock");
            }else{
                $this->getTemplate()->unset_blocks("ArchivoAntecedentesActualBlock");
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function procesarAntecedentes(){

    	if($this->getRequest()->has('fileAntecedentesUpload')){
            $this->fileAntecedentesUpload();
            return;
        }

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
                
    	if($this->getRequest()->has('formAntecedentes')){
            $this->guardarFormAntecedentes();
            return;
        }
    }
    
    private function fileAntecedentesUpload()
    {
        try{            
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $idItem = $perfil->getUsuario()->getId();
            
            $iIdSeguimiento = $this->getRequest()->getPost('iSeguimientoId');
            $nombreInputFile = 'archivoAntecedentes';

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);
            
            if($oSeguimiento->getUsuarioId() != $idItem){
                throw new Exception("No tiene permiso para editar este seguimiento", 401);
            }
			
            $this->getUploadHelper()->setTiposValidosDocumentos();
            
            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

            	$pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            	list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, "antecedentes", $nombreInputFile);
                $resultado = SeguimientosController::getInstance()->guardarAntecedentesFile($oSeguimiento, $nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor);
                $oArchivo = $oSeguimiento->getArchivoAntecedentes();

                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "ajaxAntecedentesActual", "ArchivoAntecedentesActualBlock");
                $link = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                $this->getTemplate()->set_var("sNombreArchivo", $oArchivo->getNombre());
                $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                $this->getTemplate()->set_var("sFechaArchivo", $oArchivo->getFechaAlta());
                $this->getTemplate()->set_var("hrefDescargarAntActual", $link);
                $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxAntecedentesActual', false);
            }
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
			
        }catch(Exception $e){
            $respuesta = "0;; Error al guardar en base de datos";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }
    
    private function guardarFormAntecedentes()
    {
        $iSeguimientoId = $this->getRequest()->getParam('idSeguimiento');
        
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}
        
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para editar este seguimiento", 401);
            }
                        
            $sAntecedentes = $this->getRequest()->getPost('antecedentes');
           
            $oSeguimiento->setAntecedentes($sAntecedentes);
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            			
            if($resultado){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
            
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
               
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function eliminar()
    {
    	$iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

    	$this->getJsonHelper()->initJsonAjaxResponse();
    	try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar este seguimiento", 401);
            }

            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            $result = SeguimientosController::getInstance()->eliminarSeguimiento($oSeguimiento, $pathServidorFotos, $pathServidorArchivos);

            $this->restartTemplate();

            if($result){
                    $msg = "El seguimiento fue eliminado con exito";
                    $bloque = 'MsgCorrectoBlockI32';
                    $this->getJsonHelper()->setSuccess(true);
            }else{
                    $msg = "Ocurrio un error, no se ha podido eliminar el seguimiento";
                    $bloque = 'MsgErrorBlockI32';
                    $this->getJsonHelper()->setSuccess(false);
            }
    
    	}catch(Exception $e){
    		$msg = "Ocurrio un error, no se ha podido eliminar el seguimiento";
    		$bloque = 'MsgErrorBlockI32';
    		$this->getJsonHelper()->setSuccess(false);
    	}
    
    	$this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
    	$this->getTemplate()->set_var("sMensaje", $msg);
    	$this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));
    
    	$this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Metodo para la vista ampliada de un seguimiento
     */
    public function ver()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
       
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
        
        try{
            $aCurrentOptions[] = "currentOptionVerSeguimiento";
            
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaVerSeguimiento($aCurrentOptions);

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->verSeguimientoPersonalizado($oSeguimiento);
            }else{
                $this->verSeguimientoSCC($oSeguimiento);
            }
    	}catch(Exception $e){
            throw new Exception($e->getMessage());
    	}
    }

    private function verSeguimientoSCC($oSeguimiento)
    {
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
        $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "VerSeguimientoBlock");

        $oDiscapacitado = $oSeguimiento->getDiscapacitado();
       
        $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
        $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $this->getTemplate()->set_var("iPersonaId", $oDiscapacitado->getId());

        $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());
        $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorMediumSize=$pathFotoServidorBigSize=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada",$pathFotoServidorBigSize);
        $this->getTemplate()->set_var("scrFotoPerfilActual",$pathFotoServidorMediumSize);
        
        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
        $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
        $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

        //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
        $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
        $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
        $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

        //antecedentes
        $sAntecedentes = $oSeguimiento->getAntecedentes();
        $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

        if(null === $sAntecedentes && $oAntecedentes === null){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("NoInfoAntecedentesBlock", "");
        }

        if(null === $sAntecedentes){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sAntecedentes", $sAntecedentes);
        }

        if(null === $oAntecedentes){
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
            $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
            $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
            $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

            $this->getTemplate()->set_var("hrefDescargar", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function verSeguimientoPersonalizado($oSeguimiento)
    {
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
        $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "VerSeguimientoBlock");

        $oDiscapacitado = $oSeguimiento->getDiscapacitado();

        $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
        $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $this->getTemplate()->set_var("iPersonaId", $oDiscapacitado->getId());

        $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());
        $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorMediumSize=$pathFotoServidorBigSize=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada",$pathFotoServidorBigSize);
        $this->getTemplate()->set_var("scrFotoPerfilActual",$pathFotoServidorMediumSize);

        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
        $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
        $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

        //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
        $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
        $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
        $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

        //antecedentes
        $sAntecedentes = $oSeguimiento->getAntecedentes();
        $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

        if(null === $sAntecedentes && $oAntecedentes === null){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("NoInfoAntecedentesBlock", "");
        }

        if(null === $sAntecedentes){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sAntecedentes", $sAntecedentes);
        }

        if(null === $oAntecedentes){
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
            $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
            $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
            $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

            $this->getTemplate()->set_var("hrefDescargar", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());
        }
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function verAdjuntos()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
        
        try{
            $aCurrentOptions[] = "currentOptionVerAdjuntosSeguimiento";
            
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaVerSeguimiento($aCurrentOptions)
                 ->setFichaPersonaMenuDerechaSeguimiento($oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            }else{
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            }  
            $this->getTemplate()->set_var("subtituloSeccion", "galería adjuntos");
            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaAdjuntosBlock");
            $this->getTemplate()->set_var("TituloItemBlock", "");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
            $this->getTemplate()->set_var("iItemIdForm", $oSeguimiento->getId());

            //uso los thumbnails de edicion asi que descarto los otros:
            $this->getTemplate()->set_var("ThumbnailFotoBlock", "");
            $this->getTemplate()->set_var("ThumbnailVideoBlock", "");
            $this->getTemplate()->set_var("RowArchivoBlock", "");
            
            //Fotos
            $aFotos = $oSeguimiento->getFotos();
            if(count($aFotos) > 0){
                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                foreach($aFotos as $oFoto){
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion());
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    $this->getTemplate()->parse("ThumbnailFotoEditBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
            }else{
                $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            }

            //Videos
            $aEmbedVideos = $oSeguimiento->getEmbedVideos();

            if(count($aEmbedVideos) > 0){

                foreach($aEmbedVideos as $oEmbedVideo){

                    $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                    $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                    $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                    $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                    $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                    $this->getTemplate()->parse("ThumbnailVideoEditBlock", true);
                }

                $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
            }else{
                $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            }

            //Archivos
            $aArchivos = $oSeguimiento->getArchivos();

            if(count($aArchivos) > 0){

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aArchivos as $oArchivo){

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());

                    $sTitulo = $oArchivo->getTitulo();
                    $sDescripcion = $oArchivo->getDescripcion();
                    if(empty($sTitulo) && empty($sDescripcion)){
                        $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                    }else{
                        if(empty($sTitulo)){
                            $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("tituloArchivo", $sTitulo);
                        }

                        if(empty($sDescripcion)){
                            $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("descripcionArchivo", $sDescripcion);
                        }
                    }

                    $this->getTemplate()->parse("RowArchivoEditBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                }

                $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");
            }else{
                $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    	}catch(Exception $e){
            throw new Exception($e->getMessage());
    	}
    }

    public function formAdjuntarFoto()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaFotosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsFotosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantFotos >= 12){
            $this->getTemplate()->set_var("FormularioCrearFotoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteFotosBlock", "");

            $this->getUploadHelper()->setTiposValidosFotos();
            $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function formAdjuntarVideo()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaVideosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsVideosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantVideos >= 12){
            $this->getTemplate()->set_var("FormularioCrearEmbedVideoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteEmbedVideosBlock", "");
            $this->getTemplate()->set_var("sServidoresPermitidos", $this->getEmbedVideoHelper()->getStringServidoresValidos());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
    
    public function formAdjuntarArchivo()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaArchivosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("RowArchivoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantArchivos >= 12){
            $this->getTemplate()->set_var("FormularioCrearArchivoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteArchivosBlock", "");

            $this->getUploadHelper()->setTiposValidosDocumentos();
            $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    //un mismo metodo para mostrar los 3 forms porq es simple esta accion
    public function formEditarAdjunto()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('editarFoto')){
            $this->formEditarFoto();
            return;
        }
        if($this->getRequest()->has('editarArchivo')){
            $this->formEditarArchivo();
            return;
        }
        if($this->getRequest()->has('editarVideo')){
            $this->formEditarVideo();
            return;
        }
    }

    private function formEditarFoto()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioFotoBlock");

        $iFotoId = $this->getRequest()->getParam('iFotoId');
        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

        $this->getTemplate()->set_var("iFotoId", $iFotoId);

        $sTitulo = $oFoto->getTitulo();
        $sDescripcion = $oFoto->getDescripcion();
        $iOrden = $oFoto->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function formEditarArchivo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioArchivoBlock");

        $iArchivoId = $this->getRequest()->getParam('iArchivoId');
        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

        $this->getTemplate()->set_var("iArchivoId", $iArchivoId);

        $sTitulo = $oArchivo->getTitulo();
        $sDescripcion = $oArchivo->getDescripcion();
        $iOrden = $oArchivo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    private function formEditarVideo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioVideoBlock");

        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');
        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

        $this->getTemplate()->set_var("iEmbedVideoId", $iEmbedVideoId);

        $sTitulo = $oEmbedVideo->getTitulo();
        $sDescripcion = $oEmbedVideo->getDescripcion();
        $iOrden = $oEmbedVideo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function procesarAdjunto()
    {        
        if($this->getRequest()->has('agregarFoto')){
            $this->agregarFoto();
            return;
        }

        if($this->getRequest()->has('agregarArchivo')){
            $this->agregarArchivo();
            return;
        }
        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('agregarVideo')){
            $this->agregarVideo();
            return;
        }

        if($this->getRequest()->has('guardarVideo')){
            $this->guardarVideo();
            return;
        }

        if($this->getRequest()->has('eliminarVideo')){
            $this->eliminarVideo();
            return;
        }

        if($this->getRequest()->has('guardarFoto')){
            $this->guardarFoto();
            return;
        }

        if($this->getRequest()->has('eliminarFoto')){
            $this->eliminarFoto();
            return;
        }

        if($this->getRequest()->has('guardarArchivo')){
            $this->guardarArchivo();
            return;
        }

        if($this->getRequest()->has('eliminarArchivo')){
            $this->eliminarArchivo();
            return;
        }
    }

    private function agregarFoto()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar fotos a este seguimiento", 401);
            }

            $nombreInputFile = 'fotoGaleria';

            $this->getUploadHelper()->setTiposValidosFotos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oSeguimiento->getId();

                //un array con los datos de las fotos
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($idItem, $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);

                try{
                    $oFoto = new stdClass();
                    $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
                    $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
                    $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];

                    $oFoto = Factory::getFotoInstance($oFoto);

                    $oFoto->setTitulo('');
                    $oFoto->setDescripcion('');
                    $oFoto->setTipoAdjunto();

                    $oSeguimiento->addFoto($oFoto);

                    SeguimientosController::getInstance()->guardarFotoSeguimiento($oSeguimiento, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailFoto", "ThumbnailFotoEditBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion());
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    //OJO QUE SI TIENE UN ';' EL HTML Y HAGO UN SPLIT EN EL JS SE ROMPE TODO !!
                    $respuesta = "1; ".$this->getTemplate()->pparse('ajaxThumbnailFoto', false);
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

    private function agregarArchivo()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar archivos a este seguimiento", 401);
            }

            $nombreInputFile = 'archivoGaleria';

            $this->getUploadHelper()->setTiposValidosDocumentos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oSeguimiento->getId();

                list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, 'seguimiento', $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

                try{
                    $oArchivo = new stdClass();

                    $oArchivo->sNombre = $nombreArchivo;
                    $oArchivo->sNombreServidor = $nombreServidorArchivo;
                    $oArchivo->sTipoMime = $tipoMimeArchivo;
                    $oArchivo->iTamanio = $tamanioArchivo;
                    $oArchivo = Factory::getArchivoInstance($oArchivo);
                    $oArchivo->setTipoAdjunto();

                    $oSeguimiento->addArchivo($oArchivo);

                    SeguimientosController::getInstance()->guardarArchivoSeguimiento($oSeguimiento, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxRowFoto", "RowArchivoEditBlock");

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());

                    $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxRowFoto', false);

                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){

                    $respuesta = "0;; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){

            $respuesta = "0;; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }        
    }

    private function agregarVideo()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar videos a este seguimiento", 401);
            }

            $this->getJsonHelper()->initJsonAjaxResponse();

            if(!$this->getEmbedVideoHelper()->canBeParsed($this->getRequest()->getPost('codigo'))){
                $this->getJsonHelper()->setMessage("No se encontro un video para insertar desde la url ingresada. (o el servidor no es soportado)");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            try{
                $oEmbedVideo = new stdClass();
                $oEmbedVideo = Factory::getEmbedVideoInstance($oEmbedVideo);
                $oEmbedVideo->setCodigo($this->getRequest()->getPost('codigo'));

                $servidorOrigen = $this->getEmbedVideoHelper()->getServidor($oEmbedVideo);
                $oEmbedVideo->setOrigen($servidorOrigen);

                $oSeguimiento->addEmbedVideo($oEmbedVideo);

                SeguimientosController::getInstance()->guardarEmbedVideosSeguimiento($oSeguimiento);

                $this->restartTemplate();

                //creo el thumbnail para agregar a la galeria
                $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailVideo", "ThumbnailVideoEditBlock");

                $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                $this->getJsonHelper()->setMessage("El video fue agregado con éxito en el seguimiento");
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('ajaxThumbnailVideo', false));
                $this->getJsonHelper()->setSuccess(true);
                $this->getJsonHelper()->sendJsonAjaxResponse();

            }catch(Exception $e){
                $this->getJsonHelper()->setMessage("Error al guardar en base de datos.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

        }catch(Exception $e){
            $this->getJsonHelper()->setMessage("Error al procesar el video");
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }        
    }

    private function guardarFoto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iFotoId = $this->getRequest()->getPost('iFotoIdForm');

            if(empty($iFotoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bFotoUsuario = SeguimientosController::getInstance()->isFotoSeguimientoUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para editar esta foto", 401);
            }

            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            $oFoto->setOrden($this->getRequest()->getPost("orden"));
            $oFoto->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oFoto->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarFoto($oFoto);

            $this->getJsonHelper()->setMessage("La foto se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function eliminarFoto()
    {
        $iFotoId = $this->getRequest()->getParam('iFotoId');

        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si la foto es de una publicacion creada por el usuario que esta logueado
            $bFotoUsuario = SeguimientosController::getInstance()->isFotoSeguimientoUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para borrar esta foto", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            IndexController::getInstance()->borrarFoto($oFoto, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function guardarVideo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEmbedVideoId = $this->getRequest()->getPost('iEmbedVideoId');

            if(empty($iEmbedVideoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bVideoUsuario = SeguimientosController::getInstance()->isEmbedVideoSeguimientoUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            $oEmbedVideo->setOrden($this->getRequest()->getPost("orden"));
            $oEmbedVideo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oEmbedVideo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarEmbedVideo($oEmbedVideo);

            $this->getJsonHelper()->setMessage("El video se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function eliminarVideo()
    {
        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');

        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $bVideoUsuario = SeguimientosController::getInstance()->isEmbedVideoSeguimientoUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            IndexController::getInstance()->borrarEmbedVideo($oEmbedVideo);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                        
    }

    private function guardarArchivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iArchivoId = $this->getRequest()->getParam('iArchivoIdForm');

            if(empty($iArchivoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bArchivoUsuario = SeguimientosController::getInstance()->isArchivoSeguimientoUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para editar este archivo", 401);
            }

            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            $oArchivo->setOrden($this->getRequest()->getPost("orden"));
            $oArchivo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oArchivo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarArchivo($oArchivo);

            $this->getJsonHelper()->setMessage("El archivo se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function eliminarArchivo()
    {
        $iArchivoId = $this->getRequest()->getParam('iArchivoId');

        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si el archivo es de una publicacion creada por el usuario que esta logueado
            $bArchivoUsuario = SeguimientosController::getInstance()->isArchivoSeguimientoUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para borrar este archivo", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);
            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            IndexController::getInstance()->borrarArchivo($oArchivo, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }
    
    public function editarDiagnostico()
    {
    	$iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}
        
        $filtroSql["s.id"] = $iSeguimientoId;
        $iRecordsTotal = 0;
        $sOrderBy = $sOrder =  $iIniLimit =  $iRecordCount = null;
        $vSeguimientos = SeguimientosController::getInstance()->obtenerSeguimientos($filtroSql,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount);
        if(count($vSeguimientos) == 0 ){
            throw new Exception("No tiene permiso para este seguimiento", 401);
        }else{
            $oSeguimiento = $vSeguimientos[0];
        }
        
        try{
            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionEditarDiagnosticoSeguimiento";

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            $this->setFrameTemplate()
                 ->setJSDiagnostico()
                 ->setHeadTag()
                 ->setMenuDerechaVerSeguimiento($aCurrentOptions)
                 ->setFichaPersonaMenuDerechaSeguimiento($oSeguimiento->getDiscapacitado());

            
            $this->getTemplate()->set_var("subtituloSeccion", "diagnóstico");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());

            if($oSeguimiento->isSeguimientoPersonalizado()){
            	$this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            	$this->formDiagnosticoPersonalizado($oSeguimiento);
            }else{
            	$this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            	$this->formDiagnosticoSCC($oSeguimiento);
            }
            
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function formDiagnosticoPersonalizado($oSeguimiento)
    {
        try{            
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "pageRightInnerMainCont", "FormularioPersonalizadoBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $oDiagnostico = $oSeguimiento->getDiagnostico();
            if($oDiagnostico ){
            	$this->getTemplate()->set_var("sDiagnostico",$oDiagnostico->getDescripcion());
           		$this->getTemplate()->set_var("sCodigo",$oDiagnostico->getCodigo());
           		$this->getTemplate()->set_var("iDiagnosticoId",$oDiagnostico->getId());
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
   		  }catch(Exception $e){
            print_r($e);
        }
    }

    private function formDiagnosticoSCC($oSeguimiento)
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "pageRightInnerMainCont", "FormularioSCCBlock");

            $oDiagnostico = $oSeguimiento->getDiagnostico();
            $iAreaId  = "";
            $iCicloId = "";
            $iNivelId = "";
         	if ($oDiagnostico) {
	            $this->getTemplate()->set_var("sDiagnostico",$oDiagnostico->getDescripcion());
	            $this->getTemplate()->load_file_section("gui/componentes/grillas.gui.html", "listaEje", "GrillaEjesTematicos");
	            if ($oDiagnostico->getEjesTematicos()) {
	            	foreach ($oDiagnostico->getEjesTematicos() as $oEje) {
	            		$this->getTemplate()->set_var("iEjeId", $oEje->getId());
	            		$this->getTemplate()->set_var("sEjeText", $oEje->getDescripcion());
	            		$this->getTemplate()->set_var("sEstadoInicial", $oEje->getEstadoInicial());
	            		$this->getTemplate()->set_var("sNivelText", $oEje->getArea()->getCiclo()->getNivel()->getDescripcion());
	            		$this->getTemplate()->set_var("sCicloText", $oEje->getArea()->getCiclo()->getDescripcion());
	            		$this->getTemplate()->set_var("sAreaText", $oEje->getArea()->getDescripcion());
		            	$this->getTemplate()->parse("ResultListEjes", true);
	            	}
		            $this->getTemplate()->parse("listaEje", false);
	            }else{
		            $this->getTemplate()->set_var("listaEje", "");
	            }
	            $this->getTemplate()->set_var("iDiagnosticoId",$oDiagnostico->getId());
         	}
         	$iRecordsTotal = 0;
        	$sOrderBy 	= $sOrder = $iIniLimit = $iRecordCount = null;
        	$filtroSql 	= array();
         	$vNiveles 	= SeguimientosController::getInstance()->getNiveles($filtroSql, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
         	if( $vNiveles ){
         	  	foreach($vNiveles as $oNivel){
         	  		if($iNivelId == $oNivel->getId()){
         	  			 $this->getTemplate()->set_var("sSelectedNivel", "selected='selected'");
         	  		}else{
         	  			 $this->getTemplate()->set_var("sSelectedNivel", "");
         	  		}
	                $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
	                $this->getTemplate()->set_var("sNivelDescripcion", $oNivel->getDescripcion());
	                $this->getTemplate()->parse("NivelesListBlock", true);
            	}
         	}
         	
         	$iRecordsTotal = 0;
         	if($iNivelId!=""){
	  			$vCiclos 	= SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount );
	  			foreach($vCiclos as $oCiclo){
	  				if($iCicloId == $oCiclo->getId()){
         	  			 $this->getTemplate()->set_var("sSelectedCiclo", "selected='selected'");
         	  		}else{
         	  			 $this->getTemplate()->set_var("sSelectedCiclo", "");
         	  		}
					$this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
		            $this->getTemplate()->set_var("sCicloDescripcion", $oCiclo->getDescripcion());
		            $this->getTemplate()->parse("CiclosListBlock", true);
	          	}
	         }
   			$iRecordsTotal = 0;
         	if($iCicloId!=""){
	         	$vAreas 	= SeguimientosController::getInstance()->getAreasByCicloId($iCicloId,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount );
	  			foreach($vAreas as $oArea){
	  				if($iAreaId == $oArea->getId()){
         	  			 $this->getTemplate()->set_var("sSelectedArea", "selected='selected'");
         	  		}else{
         	  			 $this->getTemplate()->set_var("sSelectedArea", "");
         	  		}
					$this->getTemplate()->set_var("iAreaId", $oArea->getId());
		            $this->getTemplate()->set_var("sAreaDescripcion", $oArea->getDescripcion());
		            $this->getTemplate()->parse("AreasListBlock", true);
	          	}
         	}
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
   		  }catch(Exception $e){
           	//print_r($e);
        }
    }
    
    public function procesarDiagnostico(){
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            //$perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        	//$filtroSql["u.id"] = $perfil->getUsuario()->getId();
        	
            $iDiagnosticoId = $this->getRequest()->getPost('idDiagnostico');
           // TODO Agregar validacion de pedir el diagnostico segun permiso d
            $oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoById($iDiagnosticoId);
        
			if($oDiagnostico){
				$sDescripcion 	= $this->getRequest()->getPost('diagnostico');
	            if(get_class($oDiagnostico) == "DiagnosticoPersonalizado"){
	            	$sCodigo	 	= $this->getRequest()->getPost('codigo');
			    	$oDiagnostico->setCodigo($sCodigo);
	            }else{
	            	$sEjesEliminadosId	= $this->getRequest()->getPost('ejeEliminados');
	            	if ($sEjesEliminadosId != "") {
	            		SeguimientosController::getInstance()->eliminarEjesByDiagnostico($sEjesEliminadosId, $iDiagnosticoId);
	            	}
	            	$ejes	    	= $this->getRequest()->getPost('ejeHidden');
	            	//$estadoInicial	= $this->getRequest()->getPost('estadoInicialHiddenNew');
	            	$i = 0;
	            	$vEjesTematicos = array();
	            	if (count($ejes)>0) {
		            	foreach ($ejes as $eje){
		             		$oEjeTematico = Factory::getEjeTematicoInstance(new stdClass());
		            		$oEjeTematico->setId($eje["id"]);
		            		$oEjeTematico->setEstadoInicial($eje["estadoInicial"]);
		            		$vEjesTematicos[] = $oEjeTematico;
		            	}	
	            	}
	            	            	
			    	$oDiagnostico->setEjesTematicos($vEjesTematicos);
	            }
	            $oDiagnostico->setDescripcion($sDescripcion);
		        $res = SeguimientosController::getInstance()->guardarDiagnostico($oDiagnostico);
			}else{
				$res = false;
			}
			if($res){
				 $this->getJsonHelper()->setSuccess(true);
			}else{
				 $this->getJsonHelper()->setSuccess(false);
			}
            
        }catch(Exception $e){
           $this->getJsonHelper()->setMessage($e->getMessage());
           $this->getJsonHelper()->setSuccess(false);
        }
        
        $this->getJsonHelper()->sendJsonAjaxResponse($oDiagnostico);
    }
    
    public function listarCiclosPorNiveles(){
    	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "ciclos", "CiclosListBlock");
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iNivelId	    = $this->getRequest()->getPost('nivelId');
            $iRecordsTotal 	= 0;
        	$sOrderBy 	= $sOrder =  $iIniLimit =  $iRecordCount = null;
  			$vCiclos 	= SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount );
  			$this->getTemplate()->set_var("iCicloId", "");
            $this->getTemplate()->set_var("sCicloDescripcion", "Seleccione el ciclo");
            $this->getTemplate()->parse("CiclosListBlock", true);	
            if ($vCiclos) {
            	foreach($vCiclos as $oCiclo){
					$this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
		            $this->getTemplate()->set_var("sCicloDescripcion", $oCiclo->getDescripcion());
		            $this->getTemplate()->parse("CiclosListBlock", true);
	          	}
            }
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ciclos', false));
        }catch(Exception $e){
            $this->getAjaxHelper()->sendHtmlAjaxResponse("");
            return;
        }
    }
    
 	 public function listarAreasPorCiclos(){
    	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "areas", "AreasListBlock");
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iCicloId	    = $this->getRequest()->getPost('cicloId');
            $iRecordsTotal 	= 0;
        	$sOrderBy 	= $sOrder =  $iIniLimit =  $iRecordCount = null;
  			$vAreas 	= SeguimientosController::getInstance()->getAreasByCicloId($iCicloId,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount );
			$this->getTemplate()->set_var("iAreaId", "");
            $this->getTemplate()->set_var("sAreaDescripcion", "Seleccione el area");
            $this->getTemplate()->parse("AreasListBlock", true);	
            if ($vAreas) {
	  			foreach($vAreas as $oArea){
					$this->getTemplate()->set_var("iAreaId", $oArea->getId());
		            $this->getTemplate()->set_var("sAreaDescripcion", $oArea->getDescripcion());
		            $this->getTemplate()->parse("AreasListBlock", true);
	          	}
            }
          	
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('areas', false));
        }catch(Exception $e){
            $this->getAjaxHelper()->sendHtmlAjaxResponse("");
            return;
        }
    }
 	 public function listarEjesPorArea(){
    	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "ejes", "EjesListBlock");
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iAreaId	    = $this->getRequest()->getPost('areaId');
            $iRecordsTotal 	= 0;
        	$sOrderBy 	= $sOrder =  $iIniLimit =  $iRecordCount = null;
  			$vEjes	= SeguimientosController::getInstance()->getEjesByAreaId($iAreaId,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount );
			$this->getTemplate()->set_var("iEjeId", "");
            $this->getTemplate()->set_var("sEjeDescripcion", "Seleccione el eje");
            $this->getTemplate()->parse("EjesListBlock", true);
            if ($vEjes) {
	  			foreach($vEjes as $oEje){
					$this->getTemplate()->set_var("iEjeId", $oEje->getId());
		            $this->getTemplate()->set_var("sEjeDescripcion", $oEje->getDescripcion());
		            $this->getTemplate()->parse("EjesListBlock", true);
	          	}
            }
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ejes', false));
        }catch(Exception $e){
            $this->getAjaxHelper()->sendHtmlAjaxResponse("");
            return;
        }
    }

    
}