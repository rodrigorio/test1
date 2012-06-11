<?php

/**
 * @author Matias Velilla
 *
 */
class SeguimientosControllerSeguimientos extends PageControllerAbstract
{
    private $orderByConfig = array('persona' => array('variableTemplate' => 'orderByPersona',
                                                      'orderBy' => 'p.nombre',
                                                      'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipo',
                                                   'orderBy' => 'tipo',
                                                   'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 's.fechaCreacion',
                                                    'order' => 'desc'),
                                   'estado' => array('variableTemplate' => 'orderByEstado',
                                                     'orderBy' => 's.estado',
                                                     'order' => 'desc'));

    private $filtrosFormConfig = array('filtroEstado' => 's.estado',
                                       'filtroApellidoPersona' => 'p.apellido',
                                       'filtroDni' => 'p.numeroDocumento',
                                       'filtroTipoSeguimiento' => 'tipo',
                                       'filtroFechaDesde' => 'fechaDesde',
                                       'filtroFechaHasta' => 'fechaHasta');

    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-01.gui.html", "frame");
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
    
    private function setJSSeguimientos(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "jsContent", "JsContent");
        return $this;
    	
    }
    private function setJSAntecedentes(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "jsContent", "JsContent");
        return $this;
    }

    private function setMenuDerechaHome()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContHomeBlock");

        $this->getTemplate()->set_var("hrefCrearSeguimientos", $this->getUrlFromRoute("seguimientosSeguimientosNuevoSeguimiento", true));
        $this->getTemplate()->set_var("hrefAgregarPersona", $this->getUrlFromRoute("seguimientosPersonasAgregar", true));
        $this->getTemplate()->set_var("hrefListadoSeguimientos", $this->getUrlFromRoute("seguimientosIndexIndex", true));

        return $this;
    }

    private function setMenuDerechaVerSeguimiento()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContVerSeguimientoBlock");

        //falta crear el menu
        return $this;
    }
    
    public function index(){
        $this->listar();
    }

    public function listar()
    {
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
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");

            //select form filtro                       
            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            foreach ($aTiposSeguimientos as $value => $descripcion){
                $this->getTemplate()->set_var("sSeguimientoTipoValue", $value);
                $this->getTemplate()->set_var("sSeguimientoTipoNombre", $descripcion);
                $this->getTemplate()->parse("OptionTipoSeguimientoBlock", true);
            }

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aSeguimientos = SeguimientosController::getInstance()->buscarSeguimientos($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSeguimientos) > 0){

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

                $params = array();
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/buscar-seguimientos", "listadoSeguimientosResult", $params);
            }else{
                $this->getTemplate()->set_var("SeguimientoBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "Todavia no hay seguimientos creados.");
            }

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

            $aSeguimientos = SeguimientosController::getInstance()->obtenerSeguimientos($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);

            if(count($aSeguimientos) > 1){
                $this->getJsonHelper()->setSuccess(false)->setMessage("La persona a la que quiere hacer un seguimiento ya posee 2. No se puede agregar mas de 2 seguimientos a una persona.");
                $this->getJsonHelper()->sendJsonAjaxResponse(); 
                return;
            }

            if(count($aSeguimientos) > 0){
                if(get_class($aSeguimientos[0]) == $sTipoSeguimiento){
                     $this->getJsonHelper()->setSuccess(false)->setMessage("No puede agregar 2 seguimientos del mismo tipo a una persona");
                     $this->getJsonHelper()->sendJsonAjaxResponse();
                     return;
                }
            }

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
                case "SeguimientoSCC":
                    $oSeguimiento = Factory::getSeguimientoSCCInstance($oSeguimiento);
                    break;
                case "SeguimientoPersonalizado":
                    $oSeguimiento = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                    break;
            }            
            
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            
            if($resultado){
                $this->getJsonHelper()->setSuccess(true);
                $this->getJsonHelper()->setMessage("El seguimiento fue creado con exito");
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
            
        }catch(Exception $e){
           $this->getJsonHelper()->setMessage($e->getMessage());
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
        try{
            $this->setFrameTemplate()
            	 ->setJSAntecedentes()                  
            	 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("hrefListadoSeguimientos", "seguimientos/home");
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");

            //contenido ppal home seguimientos
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");
            $this->getTemplate()->set_var("hrefAgregarPersona", "seguimientos/agregar-persona");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "pageRightInnerMainCont", "FormularioBlock");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
          
            //form para ingresar uno nuevo
	        $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
	        $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
	        $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
            
            $iIdSeguimiento = $this->getRequest()->getPost('idSeg');
            $iRecordsTotal	= 0;
            $sOrderBy 		= null;
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $oSeguimiento 	= SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
			if($oSeguimiento){
				$this->getTemplate()->set_var("idSeguimiento", $iIdSeguimiento);
				$this->getTemplate()->set_var("sAntecedentes", $oSeguimiento->getAntecedentes());
				foreach($oSeguimiento->getArchivoAntecedentes() as $archivo){
					$sNombreArchivo = $archivo->getNombreServidor();
					$link =   $this->getUploadHelper()->getDirectorioUploadArchivos().$sNombreArchivo;
					//$this->getTemplate()->set_var("sFileAntecedentes", "<a href='".$link."'>".$sNombreArchivo."</a>");
					
					$this->getTemplate()->set_var("sNombreArchivo", $archivo->getNombre());
					$this->getTemplate()->set_var("sExtensionArchivo", $archivo->getTipoMime());
					$this->getTemplate()->set_var("sTamanioArchivo", $archivo->getTamanio());
					$this->getTemplate()->set_var("sFechaArchivo", $archivo->getFechaAlta());
					$this->getTemplate()->set_var("hrefDescargarAntActual", $link);
				}
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
                
    	if($this->getRequest()->has('textoAntecedentes')){
            $this->procesarTextoAntecedentes();
            return;
        }
    }
    
    private function fileAntecedentesUpload(){
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $perfil 			= SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $idItem 			= $perfil->getUsuario()->getId();
            
            $iIdSeguimiento 	= $this->getRequest()->getPost('seguimientoId');
            $nombreInputFile 	= 'fileAntecedentes'; //el nombre del input file (se setea por javascript con el ajax uploader)
			$oSeguimiento		= SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);
			
			$this->getUploadHelper()->setTiposValidosDocumentos();
            
            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){
            	$pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);
            	list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, "antecedentes", $nombreInputFile);
				$res = SeguimientosController::getInstance()->guardarAntecedentesFile($oSeguimiento,$nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor);
				$oArchivo = $oSeguimiento->getArchivoAntecedentes();
				
				$this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "antecedentesActual", "AntecedentesActualBlock");
				$link =   $this->getUploadHelper()->getDirectorioUploadArchivos().$oArchivo->getNombreServidor();
				$this->getTemplate()->set_var("sNombreArchivo", $oArchivo->getNombre());
				$this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
				$this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
				$this->getTemplate()->set_var("sFechaArchivo", $oArchivo->getFechaAlta());
				$this->getTemplate()->set_var("hrefDescargarAntActual", $link);
				$respuesta = "1; ";
            }
			$respuesta .= $this->getTemplate()->pparse('antecedentesActual', false);
			$this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
			
        }catch(Exception $e){
            $respuesta = "0; Error al guardar en base de datos";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }
    
    private function procesarTextoAntecedentes()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);
        
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
        try{
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaVerSeguimiento();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Seguimientos");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "verSeguimientoBlock");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    	}catch(Exception $e){
            throw new Exception($e->getMessage());
    	}
    }
}
	  