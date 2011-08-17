<?php
/**
 * @author Rodrigo A. Rio
 */
class InstitucionesControllerComunidad extends PageControllerAbstract
{
    /**
     * Setea el Head para las vistas de Instituciones
     */
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/Instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");

        $this->getTemplate()->set_var("hrefNuevaInstitucion", $this->getRequest()->getBaseTagUrl()."comunidad/nueva-institucion");
        $this->getTemplate()->set_var("hrefInstituciones", $this->getRequest()->getBaseTagUrl()."comunidad/instituciones");
        $this->getTemplate()->set_var("hrefMisInstituciones", $this->getRequest()->getBaseTagUrl()."comunidad/instituciones-listado");        
    }

    /**
     * Establece descripcion de Instituciones y el menu con 2 opciones,
     * estado de Instituciones enviadas y formulario para enviar nueva Institucion
     */
    public function index()
    {
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Instituciones");

        //menu derecha
        $this->setMenuDerecha();

        //contenido ppal home Instituciones
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "PageRightInnerMainContBlock");
        $this->getTemplate()->set_var("hrefNuevaInstitucion", $this->getRequest()->getBaseTagUrl()."comunidad/nueva-institucion");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Procesa el envio desde un formulario de Institucion.
     */
    public function procesar(){
    	 //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();
            $oUsuario					= SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
   			$oInstitucion				= new stdClass();
			$oInstitucion->sNombre 		= $this->getRequest()->getPost('nombre');
	   		$oInstitucion->sDescripcion	= $this->getRequest()->getPost('descripcion');
			$oInstitucion->iTipoInstitucion = $this->getRequest()->getPost('tipo');
			$oInstitucion->sCargo 		= $this->getRequest()->getPost('cargo');
			$oInstitucion->sPersoneriaJuridica	= $this->getRequest()->getPost('personaJuridica');
			$oInstitucion->sDireccion 		= $this->getRequest()->getPost('direccion');
			$oCiudad 		= ComunidadController::getInstance()->getCiudadById($this->getRequest()->getPost('ciudad'));
			$oInstitucion->oCiudad 		=  $oCiudad[0];
			$oInstitucion->sTelefono	= $this->getRequest()->getPost('tel');
			$oInstitucion->sSitioWeb	= $this->getRequest()->getPost('web');
			$oInstitucion->sEmail 		= $this->getRequest()->getPost('email');
			$oInstitucion->sHorariosAtencion	= $this->getRequest()->getPost('horarioAtencion');
			$oInstitucion->sSedes 		= $this->getRequest()->getPost('sedes');
			$oInstitucion->sAutoridades	= $this->getRequest()->getPost('autoridades');
			$oInstitucion->sActividadesMes	= $this->getRequest()->getPost('actividadesMes');
			$oInstitucion->iModerado	= 0;
			$oInstitucion->oUsuario		= $oUsuario;
			
			$oInstitucion	= Factory::getInstitucionInstance($oInstitucion);
			ComunidadController::getInstance()->guardarInstitucion($oInstitucion);
			echo 1;
			exit;
			$this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Vista para enviar una nueva Institucion
     */
    public function nuevaInstitucion(){
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();

        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Nueva Institucion");

        //menu derecha
        $this->setMenuDerecha();

        //contenido ppal
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FormularioBlock");
		$listaPaises	= ComunidadController::getInstance()->listaPaises();
		foreach ($listaPaises as $oPais){
    	    $this->getTemplate()->set_var("iPaisId", $oPais->getId());
    	    $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
    	    $this->getTemplate()->parse("ListaPaisesBlock", true);
		}
		$filtro = array();
		$iRecordsTotal=0;
		$sOrderBy= $sOrder= $iIniLimit= $iRecordCount= null;
		$vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro, &$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
		foreach ($vListaInstitucionTipos as $oInstitucionTipos){
    	    $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipos->iId);
    	    $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipos->sNombre);
    	    $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
		}
        	
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function listadoInstituciones(){
    	try{
    		$this->restartTemplate();
    		$perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
       		$usuario = $perfil->getUsuario();
	        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
	        $this->setHeadTag();
	        IndexControllerComunidad::setCabecera($this->getTemplate());
	        IndexControllerComunidad::setCenterHeader($this->getTemplate());
	        //titulo seccion
	        $this->getTemplate()->set_var("tituloSeccion", "Mis instituciones");
	        //menu derecha
	        $this->setMenuDerecha();
	        //contenido ppal
	        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "ListadoInstitucionBlock");
			$filtro = array("i.usuario_id"=>$usuario->getId());
			$iRecordsTotal=0;
			$sOrderBy= $sOrder= $iIniLimit= $iRecordCount= null;
			$vListaInstitucion	= ComunidadController::getInstance()->obtenerMisInstituciones($filtro);
			foreach ($vListaInstitucion as $oInstitucion){
	    	    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
	    	    $this->getTemplate()->set_var("sInstitucionNombre", $oInstitucion->getNombre());
	    	    $this->getTemplate()->parse("ListaDeInstitucionesBlock", true);
			}
       	 	$this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    	}catch(Exception $e){
        	print_r($e);
            throw new Exception('Error Template');
            //return;
    	}
    }
	/**
	 * 
	 */
    public function provinciasByPais(){
		 if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
    	 try{
    	 	$iPaisId =  $this->getRequest()->getPost("iPaisId");
			$result = array();
    	 	if($iPaisId != 0){
				$vListaProvincias	= ComunidadController::getInstance()->listaProvinciasByPais($iPaisId);
				foreach($vListaProvincias as $oProvincia){
					$obj 		= new stdClass();
					$obj->id 	= $oProvincia->getId();
					$obj->sNombre = $oProvincia->getNombre();
					array_push($result,$obj);
				}
    	 	}
			echo json_encode($result);
        }catch(Exception $e){
        	print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
    }
	public function ciudadesByProvincia(){
		 if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
    	 try{
    	 	$iProvinciaId =  $this->getRequest()->getPost("iProvinciaId");
			$result = array();
    	 	if($iProvinciaId != 0){
				$vListaCiudades	= ComunidadController::getInstance()->listaCiudadByProvincia($iProvinciaId);
				foreach($vListaCiudades as $oCiudad){
					$obj = new stdClass();
					$obj->id = $oCiudad->getId();
					$obj->sNombre = $oCiudad->getNombre();
					array_push($result,$obj);
				}
    	 	}
			echo json_encode($result);
        }catch(Exception $e){
        	print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
    }
    /**
     * Lista de todas las Instituciones realizadas y el estado en el que se encuentran
     */
    public function listado()
    {
    }
	/**
     * Muestra pagina de sitio en construccion
     */
    public function sitioEnConstruccion()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");
        
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->set_var("tituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("subtituloVista", "Estamos trabajando, muy pronto estaremos en línea");
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function sitioOffline()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
    
}