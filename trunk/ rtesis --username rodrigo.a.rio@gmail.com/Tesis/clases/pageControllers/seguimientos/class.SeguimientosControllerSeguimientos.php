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

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
			$oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro 		= array("s.usuarios_id"=>$oUsuario->getId());
            $iRecordsTotal 	= 0;
            $sOrderBy 		= null; 
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $listaSeguimientos = SeguimientosController::getInstance()->listarSeguimiento($filtro,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit, $iRecordCount);
            if(count($listaSeguimientos)>0){
            	foreach ($listaSeguimientos as $seguimiento){
            		$this->getTemplate()->set_var("iSeguimientoId",$seguimiento->getId());
            		$this->getTemplate()->set_var("sSeguimientoPersona",$seguimiento->getDiscapacitado()->getNombreCompleto());
            		$this->getTemplate()->set_var("sSeguimientoTipo",$seguimiento->getTipoSeguimiento());
            		$this->getTemplate()->set_var("sSeguimientoPersonaDNI",$seguimiento->getDiscapacitado()->getNumeroDocumento());
            		$this->getTemplate()->set_var("sSeguimientoFechaCreacion",Utils::fechaFormateada($seguimiento->getFechaCreacion()));
            		$this->getTemplate()->parse("ListaDeSeguimientosBlock",true);
            	}
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
			
			$filtro 		= array("s.usuarios_id"=>$oUsuario->getId());
			$nombre 	= $this->getRequest()->getPost('nombre');
			if($nombre!=""){
				$filtro["p.nombre"] = $nombre;
			}
			$tipo 		= $this->getRequest()->getPost('tipoSeguimiento');
			if($tipo!=""){
				$filtro["p.tipoSeguimiento"] = $tipo;
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
				$filtro["s.fechaCreacion"] = $fechaCreacion;
			}
            $iRecordsTotal 	= 0;
            $sOrderBy 		= null; 
            $sOrder 		= null;
            $iIniLimit 		= null;
            $iRecordCount 	= null;
            $listaSeguimientos = SeguimientosController::getInstance()->listarSeguimiento($filtro,$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit, $iRecordCount);
            if( count($listaSeguimientos) > 0 ){
            	foreach($listaSeguimientos as $seguimiento){
            		$this->getTemplate()->set_var("iSeguimientoId",$seguimiento->getId());
            		$this->getTemplate()->set_var("sSeguimientoPersona",$seguimiento->getDiscapacitado()->getNombreCompleto());
            		$this->getTemplate()->set_var("sSeguimientoTipo",$seguimiento->getTipoSeguimiento());
            		$this->getTemplate()->set_var("sSeguimientoPersonaDNI",$seguimiento->getDiscapacitado()->getNumeroDocumento());
            		$this->getTemplate()->set_var("sSeguimientoFechaCreacion",Utils::fechaFormateada($seguimiento->getFechaCreacion()));
            		$this->getTemplate()->parse("ListaDeSeguimientosBlock",true);
            	}
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
            $this->getTemplate()->set_var("tituloSeccion", "Seguimientos - Inicio");
            $this->getTemplate()->set_var("hrefCrearSeguimientos", "seguimientos/nuevo-seguimiento");

            //contenido ppal home comunidad
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioBlock");
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
            $iPersona       = $this->getRequest()->getPost('personaId');
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
            $filtro = array("p.id"=>$iPersona);
            $oDiscapacitado	= ComunidadController::getInstance()->obtenerDiscapacitado($filtro,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount );
            $obj->oPractica 	= $oTipoPractica;
            $obj->sFrecuenciaEncuentros = $sFrecuencias;
            $obj->sDiaHorario 	= $sDiaHorario;
            $obj->oDiscapacitado		= $oDiscapacitado;
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
}