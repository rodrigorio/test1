<?php
/**
 * @author Rodrigo A. Rio
 */
class InstitucionesControllerComunidad extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroNombre' => 'i.nombre',
                                       'filtroTipoInstitucion' => 'i.tipoInstitucion_id',
                                       'filtroPais' => 'pa.id',
                                       'filtroProvincia' => 'pr.id',
                                       'filtroCiudad' => 'i.ciudades_id');

    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
    }
    
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
        
        $this->getTemplate()->set_var("hrefListadoInstituciones", $this->getUrlFromRoute("comunidadInstitucionesIndex", true));
        $this->getTemplate()->set_var("hrefMisInstituciones", $this->getUrlFromRoute("comunidadInstitucionesMisInstituciones", true));
        $this->getTemplate()->set_var("hrefNuevaInstitucion", $this->getUrlFromRoute("comunidadInstitucionesNueva", true));

        return $this;
    }

    /**
     * Establece descripcion de Instituciones y el menu con 2 opciones,
     * estado de Instituciones enviadas y formulario para enviar nueva Institucion
     */
    public function index()
    {
        $this->listadoInstituciones();
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
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            if($this->getRequest()->getPost('id')!=""){
            	 $iRecordsTotal = 0;
            	 $sOrderBy=$sOrder=$iIniLimit=$iRecordCount=null;
            	 $filtro = array("i.id"=>$this->getRequest()->getPost('id'));
            	 $oInstitucion	= ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
            	 $oInstitucion  = $oInstitucion[0];
            	 $oInstitucion->setNombre($this->getRequest()->getPost('nombre'));
            	 $oInstitucion->setDescripcion($this->getRequest()->getPost('descripcion'));
            	 $oInstitucion->setTipoInstitucion( $this->getRequest()->getPost('tipo'));
            	 $oInstitucion->setCargo($this->getRequest()->getPost('cargo'));
            	 $oInstitucion->setPersoneriaJuridica($this->getRequest()->getPost('personaJuridica'));
            	 $oInstitucion->setDireccion($this->getRequest()->getPost('direccion'));
            	 $oInstitucion->setTelefono($this->getRequest()->getPost('tel'));
            	 $oInstitucion->setSitioWeb($this->getRequest()->getPost('web'));
            	 $oInstitucion->setAutoridades($this->getRequest()->getPost('autoridades'));
            	 $oInstitucion->setEmail($this->getRequest()->getPost('email'));
            	 $oInstitucion->setHorariosAtencion($this->getRequest()->getPost('horarioAtencion'));
            	 $oInstitucion->setActividadesMes($this->getRequest()->getPost('actividadesMes'));
            	 $oInstitucion->setLatitud($this->getRequest()->getPost('latitud'));
            	 $oInstitucion->setLongitud( $this->getRequest()->getPost('longitud'));
            }else{
	            $oInstitucion		= new stdClass();
	            $oInstitucion->sNombre 	= $this->getRequest()->getPost('nombre');
	            $oInstitucion->sDescripcion	= $this->getRequest()->getPost('descripcion');
	            $oInstitucion->iTipoInstitucion = $this->getRequest()->getPost('tipo');
	            $oInstitucion->sCargo 	= $this->getRequest()->getPost('cargo');
	            $oInstitucion->sPersoneriaJuridica	= $this->getRequest()->getPost('personaJuridica');
	            $oInstitucion->sDireccion 	= $this->getRequest()->getPost('direccion');
	            $oCiudad 		= ComunidadController::getInstance()->getCiudadById($this->getRequest()->getPost('ciudad'));
	            $oInstitucion->oCiudad 	=  $oCiudad;
	            $oInstitucion->sTelefono	= $this->getRequest()->getPost('tel');
	            $oInstitucion->sSitioWeb	= $this->getRequest()->getPost('web');
	            $oInstitucion->sEmail 	= $this->getRequest()->getPost('email');
	            $oInstitucion->sHorariosAtencion	= $this->getRequest()->getPost('horarioAtencion');
	            $oInstitucion->sSedes 	= $this->getRequest()->getPost('sedes');
	            $oInstitucion->sAutoridades	= $this->getRequest()->getPost('autoridades');
	            $oInstitucion->sActividadesMes	= $this->getRequest()->getPost('actividadesMes');
	            $oInstitucion->sLatitud 	= $this->getRequest()->getPost('latitud');
	            $oInstitucion->sLongitud	= $this->getRequest()->getPost('longitud');
	            $oInstitucion->iModerado	= 0;
	            $oInstitucion->oUsuario	= $oUsuario;
	            $oInstitucion	= Factory::getInstitucionInstance($oInstitucion);
            }
            ComunidadController::getInstance()->guardarInstitucion($oInstitucion);
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
        $this->setHeadTag();

        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Nueva Institucion");

        //menu derecha
        $this->setMenuDerecha();

        //contenido ppal
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FormularioBlock");
        $array = array();
		$iRecordsTotalPais=0;
		$listaPaises	= ComunidadController::getInstance()->listaPaises($array, $iRecordsTotalPais, null,  null,  null,  null);
		foreach ($listaPaises as $oPais){
                    $this->getTemplate()->set_var("iPaisId", $oPais->getId());
                    $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
                    $this->getTemplate()->parse("ListaPaisesBlock", true);
		}
		$filtro = array();
		$iRecordsTotal=0;
		$sOrderBy= $sOrder= $iIniLimit= $iRecordCount= null;
		$vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
		foreach ($vListaInstitucionTipos as $oInstitucionTipos){
                    $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipos->iId);
                    $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipos->sNombre);
                    $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
		}
        	
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function listadoInstituciones()
    {
        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        $this->getTemplate()->set_var("tituloSeccion", "Instituciones Comunidad");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "ListadoInstitucionesBlock");

        ///////////// ARMO LOS SELECTS DEL FORMULARIO DEL FILTRO

        $iRecordsTotalPais = 0;
        $listaPaises = ComunidadController::getInstance()->listaPaises($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($listaPaises as $oPais){
            $this->getTemplate()->set_var("iPaisId", $oPais->getId());
            $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
            $this->getTemplate()->parse("ListaPaisesBlock", true);
        }

        $vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($vListaInstitucionTipos as $oInstitucionTipo){
            $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipo->iId);
            $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipo->sNombre);
            $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
        }

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);                                
            }

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            $params = array();
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/masInstituciones", "listadoInstitucionesResult", $params);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
            $this->getTemplate()->set_var("sNoRecords", "No hay instituciones cargadas en la comunidad");
            $this->getTemplate()->parse("noRecords", false);
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function masInstituciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "ajaxFichasInstitucionesBlock", "FichasInstitucionesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/masInstituciones", "listadoInstitucionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron instituciones");
            $this->getTemplate()->parse("noRecords", false);
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasInstitucionesBlock', false));
    }

    public function ampliarInstitucion(){
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
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FichaInstitucionBlock");
            $this->setHeadTag();
            $iRecordPerPage	= 5;
	   	
            $iInstitucionId     = $this->getRequest()->get("iInstitucionId");
	    	$iPage		= $this->getRequest()->getPost("iPage");
            $iPage		= strlen($iPage) ? $iPage : 1;
            $iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit          = ($iPage-1) * $iItemsForPage;
            $sOrderBy		= null;
            $sOrder		= null;
            $iRecordsTotal      = 0;
            $filtro             = array("i.id"=>$iInstitucionId);
            $vListaInstitucion	= ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vListaInstitucion)>0){
                foreach ($vListaInstitucion as $oInstitucion){
                    $this->getTemplate()->set_var("iInstitucionId",     $oInstitucion->getId());
                    $this->getTemplate()->set_var("sInstitucionTipo",   $oInstitucion->getNombreTipoInstitucion() );
                    $this->getTemplate()->set_var("sInstitucionNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sInstitucionDescripcion", $oInstitucion->getDescripcion());
                    $this->getTemplate()->set_var("sInstitucionActividades", $oInstitucion->getActividadesMes());
                    $this->getTemplate()->set_var("sInstitucionAutoridades", $oInstitucion->getAutoridades());
                    $this->getTemplate()->set_var("sInstitucionSedes", $oInstitucion->getSedes());
                    $this->getTemplate()->set_var("sInstitucionHorarios", $oInstitucion->getHorariosAtencion());
                    $this->getTemplate()->set_var("sInstitucionEmail", $oInstitucion->getEmail());
                    $this->getTemplate()->set_var("sInstitucionTelefono", $oInstitucion->getTelefono());
                    $this->getTemplate()->set_var("sInstitucionSitioWeb", $oInstitucion->getSitioWeb());
                    $this->getTemplate()->set_var("sInstitucionDireccion", $oInstitucion->getDireccion());
                    $this->getTemplate()->set_var("sInstitucionCiudad", $oInstitucion->getCiudad()->getNombre() );
                    $this->getTemplate()->set_var("sInstitucionProvincia", $oInstitucion->getCiudad()->getProvincia()->getNombre() );
                    $this->getTemplate()->set_var("sInstitucionPais",   $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre() );
                }
            }else{
            	 $this->getTemplate()->set_var("FichaInstitucionBlock", "");
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
     }

      public function editarInstitucion(){
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
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FormularioBlock");
           
            $iRecordsTotal=0;
            $sOrderBy= $sOrder= $iIniLimit= $iRecordCount= null;
            $iRecordPerPage	= 5;
	    	$iInstitucionId     = $this->getRequest()->get("iInstitucionId");
            $this->getTemplate()->set_var("idInstitucion", $iInstitucionId);
	    	$iPage		= $this->getRequest()->getPost("iPage");
            $iPage		= strlen($iPage) ? $iPage : 1;
            $iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit          = ($iPage-1) * $iItemsForPage;
            $sOrderBy		= null;
            $sOrder		= null;
            $iRecordsTotal      = 0;
            $filtro             = array("i.id"=>$iInstitucionId);
            $filtro1 = array();
            $vListaInstitucion	= ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vListaInstitucion)>0){
                $oInstitucion  = $vListaInstitucion[0];
                $this->getTemplate()->set_var("iInstitucionId",     $oInstitucion->getId());
                $vListaInstitucionTipos = ComunidadController::getInstance()->listaTiposDeInstitucion($filtro1, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
                foreach ($vListaInstitucionTipos as $oInstitucionTipos){
                    $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipos->iId);
                    $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipos->sNombre);
                    if($oInstitucion->getTipoInstitucion() == $oInstitucionTipos->iId){
                        $this->getTemplate()->set_var("sInstitucionTiposSelect", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sInstitucionTiposSelect", "");
                    }
                    $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
                }
	            $array = array();
	            $iRecordsTotalPais=0;
	            $listaPaises	= ComunidadController::getInstance()->listaPaises($array, $iRecordsTotalPais, null,  null,  null,  null);
	            foreach ($listaPaises as $oPais){
            		if($oInstitucion->getCiudad()->getProvincia()->getPais()->getId() == $oPais->getId()){
                        $this->getTemplate()->set_var("sInstitucionPaisSelect", "selected='selected'");
            		}else{
                        $this->getTemplate()->set_var("sInstitucionPaisSelect", "");
            		} 	
	                $this->getTemplate()->set_var("iPaisId", $oPais->getId());
	                $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
	                $this->getTemplate()->parse("ListaPaisesBlock", true);
	            }
            	$arrayProv = array();
	            $iRecordsTotalProvincia=0;
	            $listaProvincias	= ComunidadController::getInstance()->listaProvinciasByPais($oInstitucion->getCiudad()->getProvincia()->getPais()->getId());
	            foreach ($listaProvincias as $oProvincia){
            		if($oInstitucion->getCiudad()->getProvincia()->getId() == $oProvincia->getId()){
                        $this->getTemplate()->set_var("sInstitucionProvinciaSelect", "selected='selected'");
            		}else{
                        $this->getTemplate()->set_var("sInstitucionProvinciaSelect", "");
            		} 	
	                $this->getTemplate()->set_var("iProvinciaId", $oProvincia->getId());
	                $this->getTemplate()->set_var("sProvinciaNombre", $oProvincia->getNombre());
	                $this->getTemplate()->parse("ListaProvinciasBlock", true);
	            }
	            $vListaCiudades	= ComunidadController::getInstance()->listaCiudadByProvincia($oInstitucion->getCiudad()->getProvincia()->getId());
				foreach($vListaCiudades as $oCiudad){
					if($oInstitucion->getCiudad()->getId() == $oCiudad->getId()){
                        $this->getTemplate()->set_var("sInstitucionCiudadSelect", "selected='selected'");
            		}else{
                        $this->getTemplate()->set_var("sInstitucionCiudadSelect", "");
            		} 	
	                $this->getTemplate()->set_var("iCiudadId", $oCiudad->getId());
	                $this->getTemplate()->set_var("sCiudadNombre", $oCiudad->getNombre());
	                $this->getTemplate()->parse("ListaCiudadesBlock", true);
				}
                $this->getTemplate()->set_var("sInstitucionNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sInstitucionDescripcion", $oInstitucion->getDescripcion());
                $this->getTemplate()->set_var("sInstitucionActividades", $oInstitucion->getActividadesMes());
                $this->getTemplate()->set_var("sInstitucionAutoridades", $oInstitucion->getAutoridades());
                $this->getTemplate()->set_var("sInstitucionSedes", $oInstitucion->getSedes());
                $this->getTemplate()->set_var("sInstitucionCargo", $oInstitucion->getCargo());
                $this->getTemplate()->set_var("sInstitucionPersonaJuridica", $oInstitucion->getPersoneriaJuridica());
                $this->getTemplate()->set_var("sInstitucionHorarios", $oInstitucion->getHorariosAtencion());
                $this->getTemplate()->set_var("sInstitucionEmail", $oInstitucion->getEmail());
                $this->getTemplate()->set_var("sInstitucionTelefono", $oInstitucion->getTelefono());
                $this->getTemplate()->set_var("sInstitucionSitioWeb", $oInstitucion->getSitioWeb());
                $this->getTemplate()->set_var("sInstitucionDireccion", $oInstitucion->getDireccion());
                $this->getTemplate()->set_var("sInstitucionCiudad", $oInstitucion->getCiudad()->getNombre() );
                $this->getTemplate()->set_var("sInstitucionProvincia", $oInstitucion->getCiudad()->getProvincia()->getNombre() );
                $this->getTemplate()->set_var("sInstitucionPais",   $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre() );
                $this->getTemplate()->set_var("sInstitucionLatitud",   $oInstitucion->getLatitud() );
                $this->getTemplate()->set_var("sInstitucionLongitud",   $oInstitucion->getLongitud() );
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
     }
     
	/**
     * Devuelve las instituciones para el autocomplete de la busqueda de instituciones
     */
    public function buscarInstituciones(){
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iRecordsTotal = 0;
            $sOrderBy=$sOrder=$iIniLimit=$iRecordCount=null;
            $filtro = array("i.nombre" => $this->getRequest()->get('str'));
            $vInstituciones = ComunidadController::getInstance()->obtenerInstituciones($filtro, $iRecordsTotal,$sOrderBy,$sOrder,$iIniLimit,$iRecordCount);
            $vResult = array();
            if(count($vInstituciones)>0){
                foreach($vInstituciones as $oInstitucion){
                    $obj = new stdClass();
                    $obj->id = $oInstitucion->getId();
                    $obj->nombre = $oInstitucion->getNombre();
                    $vResult[] = $obj;
                }
            }
            //agrega una url para que el js redireccione
            $this->getJsonHelper()->setSuccess(true)->setValor("instituciones", $vResult);
         }catch(Exception $e){
            print_r($e);
        }

        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}