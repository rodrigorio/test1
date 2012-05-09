<?php

/**
 * @author Matias Velilla
 *
 */
class SeguimientosControllerSeguimientos extends PageControllerAbstract
{
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "jsContent", "JsContent");

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
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");

            //contenido ppal home seguimientos
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");
            $this->getTemplate()->set_var("hrefAgregarPersona", "seguimientos/agregar-persona");
            $this->getTemplate()->set_var("hrefListadoSeguimientos", "seguimientos/home");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
			$oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
			$obj = new stdClass();
        	$oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $listaTiposSeguimiento = $oTipoSeg->getLista();
            foreach ($listaTiposSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposNombre", $value);
                $this->getTemplate()->parse("ListaTipoDeSeguimientosBlock", true);
            }
            
            $filtro 		= array("s.usuarios_id"=>$oUsuario->getId());
            $iRecordsTotal 	= 0;
            $sOrderBy 		= null; 
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $listaSeguimientos = SeguimientosController::getInstance()->listarSeguimientos($filtro,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit, $iRecordCount);
            if(count($listaSeguimientos)>0){
            	foreach ($listaSeguimientos as $seguimiento){
            		$this->getTemplate()->set_var("iSeguimientoId",$seguimiento->getId());
            		$this->getTemplate()->set_var("sSeguimientoPersona",$seguimiento->getDiscapacitado()->getNombreCompleto());
                        $this->getTemplate()->set_var("iPersonaId",$seguimiento->getDiscapacitado()->getId());
            		$this->getTemplate()->set_var("sSeguimientoTipo",$seguimiento->getTipoSeguimiento());
            		$this->getTemplate()->set_var("sSeguimientoPersonaDNI",$seguimiento->getDiscapacitado()->getNumeroDocumento());
            		$this->getTemplate()->set_var("sSeguimientoFechaCreacion",Utils::fechaFormateada($seguimiento->getFechaCreacion()));

                        $this->getTemplate()->set_var("sEstadoSeguimiento","Activo");
                        if($seguimiento->getEstado()=="activo"){
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                        }
            		$this->getTemplate()->parse("EstadoSeguimientoBlock",false);
            		$this->getTemplate()->set_var("sEstadoSeguimiento","Detenido");
                          if($seguimiento->getEstado()=="detenido"){
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                        }
            		$this->getTemplate()->parse("EstadoSeguimientoBlock",true);


                        $this->getTemplate()->set_var("sFrecuenciaEncuentros",$seguimiento->getFrecuenciaEncuentros());
                        $this->getTemplate()->set_var("sDiaHorarioEncuentros",$seguimiento->getDiaHorario());
                        $this->getTemplate()->set_var("sTipoPractica",$seguimiento->getPractica()->getNombre());

                        $vFotos     = SeguimientosController::getInstance()->obtenerFotosSeguimiento($seguimiento->getId());
                        $vArchivos  = SeguimientosController::getInstance()->obtenerArchivosSeguimiento($seguimiento->getId());
                        $cantiArchivos = $cantiFotos = 0;
                        if($vFotos){
                            $cantiFotos = count($vFotos);
                        }
                        if($vArchivos){
                            $cantiArchivos = count($vArchivos);
                        }
                        $cantidadAdjuntos = $cantiArchivos + $cantiFotos;
                        $this->getTemplate()->set_var("sElementosMultimedia",$cantidadAdjuntos);
                        $this->getTemplate()->parse("ListaDeSeguimientosBlock",true);
            	}
           		$this->getTemplate()->set_var("NoRecordsListaDeSeguimientosBlock","");
            }else{
                    $this->getTemplate()->set_var("ListaDeSeguimientosBlock","");
           		$this->getTemplate()->set_var("sNoRecords","No se encontraron seguimientos.");
           		$this->getTemplate()->parse("NoRecordsListaDeSeguimientosBlock",false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        } 
    }       
    
    public function buscarSeguimientos(){
    	  //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->setFrameTemplate();
            $this->printMsgTop();
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "body", "GrillaSeguimientoBlock");
            
			$oUsuario 	= SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
			
			$filtro 	= array("s.usuarios_id"=>$oUsuario->getId());
			$nombre 	= $this->getRequest()->getPost('nombre');
			if($nombre!=""){
				$filtro["p.nombre"] = $nombre;
			}
			$tipo 		= $this->getRequest()->getPost('tipoSeguimiento');
			if($tipo!=""){
                $filtro["sp.id"] = $tipo==1 ? "IS NULL" : "NOT NULL";
			}
			$dni 		= $this->getRequest()->getPost('dni');
			if($dni!=""){
				$filtro["p.numeroDocumento"] = $dni;
			}
			$estado 	= $this->getRequest()->getPost('estado');
			if($estado!=""){
				$filtro["s.estado"] = $estado;
			}
			$fechaCreacion 	= $this->getRequest()->getPost('fechaCreacion');
			if($fechaCreacion!=""){
				$filtro["s.fechaCreacion"] = Utils::fechaAFormatoSQL($fechaCreacion);
			}
            $iRecordsTotal 	= 0;
            $sOrderBy 		= null; 
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $listaSeguimientos = SeguimientosController::getInstance()->listarSeguimientos($filtro,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit, $iRecordCount);
            if( count($listaSeguimientos) > 0 ){
            	foreach($listaSeguimientos as $seguimiento){
            		$this->getTemplate()->set_var("iSeguimientoId",$seguimiento->getId());
            		$this->getTemplate()->set_var("sSeguimientoPersona",$seguimiento->getDiscapacitado()->getNombreCompleto());
            		$this->getTemplate()->set_var("sSeguimientoTipo",$seguimiento->getTipoSeguimiento());
            		$this->getTemplate()->set_var("sSeguimientoPersonaDNI",$seguimiento->getDiscapacitado()->getNumeroDocumento());
            		$this->getTemplate()->set_var("sSeguimientoFechaCreacion",Utils::fechaFormateada($seguimiento->getFechaCreacion()));

            		$this->getTemplate()->set_var("sEstadoSeguimiento","Activo");
                        if($seguimiento->getEstado()=="activo"){
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                        }
            		$this->getTemplate()->parse("EstadoSeguimientoBlock",false);
            		$this->getTemplate()->set_var("sEstadoSeguimiento","Detenido");
                          if($seguimiento->getEstado()=="detenido"){
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                        }else{
                            $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                        }
            		$this->getTemplate()->parse("EstadoSeguimientoBlock",true);

                          $this->getTemplate()->set_var("sFrecuenciaEncuentros",$seguimiento->getFrecuenciaEncuentros());
                        $this->getTemplate()->set_var("sDiaHorarioEncuentros",$seguimiento->getDiaHorario());
                        $this->getTemplate()->set_var("sTipoPractica",$seguimiento->getPractica()->getNombre());
                        
                        $vFotos     = SeguimientosController::getInstance()->obtenerFotosSeguimiento($seguimiento->getId());
                        $vArchivos  = SeguimientosController::getInstance()->obtenerArchivosSeguimiento($seguimiento->getId());
                        $cantiArchivos = $cantiFotos = 0;
                        if($vFotos){
                            $cantiFotos = count($vFotos);
                        }
                        if($vArchivos){
                            $cantiArchivos = count($vArchivos);
                        }
                        $cantidadAdjuntos = $cantiArchivos + $cantiFotos;
                        $this->getTemplate()->set_var("sElementosMultimedia",$cantidadAdjuntos);
                        
            		$this->getTemplate()->parse("ListaDeSeguimientosBlock",true);
            	}
           		$this->getTemplate()->set_var("NoRecordsListaDeSeguimientosBlock","");
            }else{
            	$this->getTemplate()->set_var("ListaDeSeguimientosBlock","");
           		$this->getTemplate()->set_var("sNoRecords","No se encontraron seguimientos.");
           		$this->getTemplate()->parse("NoRecordsListaDeSeguimientosBlock",false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('body', false));
            
        }catch(Exception $e){
           $this->getResponse()->setBody($this->getTemplate()->pparse('body', false));
           print_r($e->getMessage());
        }
    }

    public function nuevoSeguimiento(){
        try{
            $this->setFrameTemplate()
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

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioBlock");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
            $listaTiposSeguimiento = array();
            $obj = new stdClass();
            $oTipoSeg = Factory::getTipoSeguimientoInstance($obj);
            $listaTiposSeguimiento = $oTipoSeg->getLista();
            foreach ($listaTiposSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposNombre", $value);
                $this->getTemplate()->parse("ListaTipoDeSeguimientosBlock", true);
            }
            $oTipoPractica = Factory::getTipoPracticasSeguimientoInstance($obj);
            $listaTiposPracticaSeguimiento = $oTipoPractica->getLista();
            foreach ($listaTiposPracticaSeguimiento as $key=>$value){
                $this->getTemplate()->set_var("iSeguimientoTiposPracticaId", $key);
                $this->getTemplate()->set_var("sSeguimientoTiposPracticaNombre", $value);
                $this->getTemplate()->parse("ListaTipoDePracticaSeguimientosBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesarSeguimiento(){
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iTipoSeguimiento = $this->getRequest()->getPost('tipoSeguimiento');
            
            $iPersona           = $this->getRequest()->getPost('personaId');
            $filtro             = array("s.discapacitados_id"=>$iPersona);
            $iRecordsTotal 	= 0;
            $sOrderBy 		= null;
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $listaSeguimientos  = SeguimientosController::getInstance()->listarSeguimientos($filtro,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit, $iRecordCount);
            
            if(count($listaSeguimientos)>1){
				$this->getJsonHelper()->setSuccess(false)->setMessage("La persona a la que quiere hacer un seguimiento ya posee 2. No se puede agregar mas de 2 seguimientos a una persona.");
                $this->getJsonHelper()->sendJsonAjaxResponse(); 
			 	return;
            }else if(count($listaSeguimientos)>0){
                if($listaSeguimientos[0]->getTipoSeguimientoId() == $iTipoSeguimiento){
                	 $this->getJsonHelper()->setSuccess(false)->setMessage("No puede agregar 2 seguimientos del mismo tipo a una persona");
                	 $this->getJsonHelper()->sendJsonAjaxResponse(); 
                	 return;
                }
            }

            $sFrecuencias   = $this->getRequest()->getPost('frecuencias');
            $sDiaHorario    = $this->getRequest()->getPost('diaHorario');
            $iTipoPractica  = $this->getRequest()->getPost('tipoPractica');
            
            $oTipoSeg 		= Factory::getTipoSeguimientoInstance(new stdClass());
            $sTipoSeguimiento = $oTipoSeg->getTipoById($iTipoSeguimiento);
            $obj 			= new stdClass();
            $oTipoPractica 	= Factory::getTipoPracticasSeguimientoInstance(new stdClass());
            $oTipoPractica->setId($iTipoPractica); 
            $iRecordsTotal	= 0;
            $sOrderBy 		= null;
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $filtro = array("p.id" => $iPersona);
            $aDiscapacitado = SeguimientosController::getInstance()->obtenerDiscapacitado($filtro,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
            $oDiscapacitado = $aDiscapacitado[0];
            $obj->oPractica 	= $oTipoPractica;
            $obj->sFrecuenciaEncuentros = $sFrecuencias;
            $obj->sDiaHorario 	= $sDiaHorario;
            $obj->oDiscapacitado = $oDiscapacitado;
            $obj->oUsuario	= $perfil->getUsuario();
            if($sTipoSeguimiento == "SCC" ){
                $oSeguimiento = Factory::getSeguimientoSCCInstance($obj);
            }elseif( $sTipoSeguimiento == "PERSONALIZADO"){
                $oSeguimiento = Factory::getSeguimientoPersonalizadoInstance($obj);
            }
            
            $res = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            if($res){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function cambiarEstadoSeguimientos(){
    	 //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $perfil 		= SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iIdSeguimiento = $this->getRequest()->getPost('id');
            $sEstadoSeguimiento = $this->getRequest()->getPost('estado');
            $iRecordsTotal	= 0;
            $sOrderBy 		= null;
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $oSeguimiento 	= SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
			$oSeguimiento->setEstado(strtolower($sEstadoSeguimiento));
            $res 			= SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            if($res){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    

 	public function editarAntecedentes(){
        try{
            $this->setFrameTemplate()
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
			}
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function procesarAntecedentes(){
    	 //si accedio a traves de la url muestra pagina 404
   //     if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        print_r($_POST);
        
    	if($this->getRequest()->has('fileAntecedentesUpload')){
            $this->fileAntecedentesUpload();
            return;
        }
        
    	if($this->getRequest()->has('textoAntecedentes')){
            $this->procesarTextoAntecedentes();
            return;
        }
        
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
 	public function fileAntecedentesUpload(){
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
				
            }
			if($res){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
    }
    
    public function procesarTextoAntecedentes(){
   		 try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $perfil 		= SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iIdSeguimiento = $this->getRequest()->getPost('id');
            $sAntecedentes 	= $this->getRequest()->getPost('antecedentes');
            $iRecordsTotal	= 0;
            $sOrderBy 		= null;
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $oSeguimiento 	= SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
			if($oSeguimiento){
				$oSeguimiento->setAntecedentes($sAntecedentes);
				$res = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
			}
			
			if($res){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
    }
}
	  