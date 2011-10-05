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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "jsContent", "JsContent");

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
        $this->printMsgTop();

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
            $oUsuario			= SessionAutentificacion::getInstance()->obtenerIdentificacion();
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
            $oInstitucion->oPerfilUsuario	= $oUsuario;

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

            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "ListadoInstitucionesBlock");
            $array = array();
            $params = array();
	    	$iRecordsTotalPais=0;
			$listaPaises	= ComunidadController::getInstance()->listaPaises($array, $iRecordsTotalPais, null,  null,  null,  null);
			foreach ($listaPaises as $oPais){
                $this->getTemplate()->set_var("iPaisId", $oPais->getId());
                $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
                $this->getTemplate()->parse("ListaPaisesBlock", true);
			}
	    	$filtro1 = array();
			$iRecordsTotal1=0;
			$sOrderBy1= $sOrder1= $iIniLimit1= $iRecordCount1= null;
			$vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro1, $iRecordsTotal1, $sOrderBy1, $sOrder1, $iIniLimit1, $iRecordCount1);
			foreach ($vListaInstitucionTipos as $oInstitucionTipos){
            	$this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipos->iId);
	            $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipos->sNombre);
	            $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
			}
            $filtro 		= array();
            $iRecordPerPage	= 5;
	    	$iPage			= $this->getRequest()->getPost("iPage");
		   	$iPage			= strlen($iPage) ? $iPage : 1;
		  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
			$iMinLimit		= ($iPage-1) * $iItemsForPage;
			$sOrderBy		= null;	
			$sOrder			= null;
			$iRecordsTotal	= 0;
            $vListaInstitucion	= ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            $i 				= 0;
           	if(count($vListaInstitucion)>0){
	            foreach ($vListaInstitucion as $oInstitucion){
	                $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "par" : "impar");
	                $this->getTemplate()->set_var("iInstitucionId",     $oInstitucion->getId());
	                $this->getTemplate()->set_var("sInstitucionNombre", $oInstitucion->getNombre());
	                $this->getTemplate()->set_var("sInstitucionTipo",   $oInstitucion->getNombreTipoInstitucion() );
	                $this->getTemplate()->set_var("sInstitucionCiudad", $oInstitucion->getCiudad()->getNombre() );
	                $this->getTemplate()->set_var("sInstitucionProvincia", $oInstitucion->getCiudad()->getProvincia()->getNombre() );
	                $this->getTemplate()->set_var("sInstitucionPais",   $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre() );
	                if($oInstitucion->getPerfilUsuario()->getUsuario()->getId() == $usuario->getId()){
	                	$this->getTemplate()->parse("PermisoEditarInstitucionBlock",false);
	                }else{
	                	$this->getTemplate()->set_var("PermisoEditarInstitucionBlock","");
	                }
	                $this->getTemplate()->parse("ListaDeInstitucionesBlock", true);
	                $i++;
	            }
           	}else{
    			$this->getTemplate()->set_var("ListaDeInstitucionesBlock", "");
			}
    		
			$this->getTemplate()->load_file_section("gui/componentes/paginacion.gui.html", "paginacion", "Paginacion01Block");
           	$this->getTemplate()->set_var("iPageActual", $iPage);
    		// Navigator
			if($iRecordsTotal > $iItemsForPage){
				$TotalPages = ceil($iRecordsTotal / $iItemsForPage);
				//$tpl->set_var("iLastPage",	$TotalPages);
				$iPageMin = $iPage-2;
				$iPageMax = $iPage+2;
				if($iPageMin < 1){
					$iPageMin = 1;
					$iPageMax = 5;
				}
				if($iPageMax > $TotalPages){
					$iPageMax = $TotalPages;
					if ($TotalPages - 4 >= 1){
						$iPageMin = $TotalPages - 4;
					}
				}
				$params[] = "busquedaInstitucion=1";
				if(count($params)>0){
	            	$params = implode($params, "&");
	            }else{
	            	$params = "";
	            }
				for($i=$iPageMin; $i<=$iPageMax; $i++){
			        $this->getTemplate()->set_var("iPage", $i);
			        $this->getTemplate()->set_var("funcion", "paginar($i,'comunidad/masInstituciones','listadoInstituciones','$params');");
					$class = $i==$iPage ? "activo" : "";
					$this->getTemplate()->set_var("ClassPag", $class);
			        $this->getTemplate()->parse("PaginaListBlock", true);
				}
				$this->getTemplate()->parse("paginacion", false);	
			}else{
				$this->getTemplate()->set_var("paginacion", "");	
			}
			
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    	}catch(Exception $e){
        	print_r($e);
            throw new Exception('Error Template');
            //return;
    	}
    }
    public function masInstituciones(){
    	try{
            $this->restartTemplate();
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $usuario = $perfil->getUsuario();
            //contenido ppal
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "GrillaInstitucionBlock");
            $this->setHeadTag();
            $filtro = array();
            $params = array();
            if($this->getRequest()->getPost("busquedaInstitucion")==1){
            	$params[]= "busquedaInstitucion=1";
            	if($this->getRequest()->getPost("institucion_nombre")!= ""){
            		$filtro["i.nombre"] = $this->getRequest()->getPost("institucion_nombre");
            		$params[]= "institucion_nombre=".$this->getRequest()->getPost("institucion_nombre");
            	}
            	if($this->getRequest()->getPost("pais")!= ""){
            		$filtro["pais.id"] = $this->getRequest()->getPost("pais");
            		$params[]= "pais=".$this->getRequest()->getPost("pais");
            	}
            	if($this->getRequest()->getPost("provincia")!= ""){
            		$filtro["prov.id"] = $this->getRequest()->getPost("provincia");
            		$params[]= "provincia=".$this->getRequest()->getPost("provincia");
            	}
            	if($this->getRequest()->getPost("ciudad")!= ""){
            		$filtro["i.ciudades_id"] = $this->getRequest()->getPost("ciudad");
            		$params[]= "ciudad=".$this->getRequest()->getPost("ciudad");
            	}
            	if($this->getRequest()->getPost("tipoInstitucion")!= ""){
            		$filtro["i.tipoInstitucion_id"] = $this->getRequest()->getPost("tipoInstitucion");
            		$params[]= "tipoInstitucion=".$this->getRequest()->getPost("tipoInstitucion");
            	}
            }
            if(count($params)>0){
            	$params = implode($params, "&");
            }else{
            	$params = "";
            }
            $iRecordPerPage	= 5;
	    $iPage		= $this->getRequest()->getPost("iPage");
            $iPage		= strlen($iPage) ? $iPage : 1;
            $iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
            $iMinLimit          = ($iPage-1) * $iItemsForPage;
            $sOrderBy		= null;
            $sOrder		= null;
            $iRecordsTotal      = 0;
            $vListaInstitucion	= ComunidadController::getInstance()->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            $i = 0;
            $this->getTemplate()->load_file_section("gui/componentes/paginacion.gui.html", "paginacion", "Paginacion01Block");
            $this->getTemplate()->set_var("iPageActual", $iPage);
            if(count($vListaInstitucion)>0){
                foreach ($vListaInstitucion as $oInstitucion){
                    $this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "par" : "impar");
                    $this->getTemplate()->set_var("iInstitucionId",     $oInstitucion->getId());
                    $this->getTemplate()->set_var("sInstitucionNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sInstitucionTipo",   $oInstitucion->getNombreTipoInstitucion() );
                    $this->getTemplate()->set_var("sInstitucionCiudad", $oInstitucion->getCiudad()->getNombre() );
                    $this->getTemplate()->set_var("sInstitucionProvincia", $oInstitucion->getCiudad()->getProvincia()->getNombre() );
                    $this->getTemplate()->set_var("sInstitucionPais",   $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre() );
                    if($oInstitucion->getPerfilUsuario()->getUsuario()->getId() == $usuario->getId()){
                            $this->getTemplate()->parse("PermisoEditarInstitucionBlock",false);
                    }else{
                            $this->getTemplate()->set_var("PermisoEditarInstitucionBlock","");
                    }
                    $this->getTemplate()->parse("ListaDeInstitucionesBlock", true);
                    $i++;
                }
            }else{
                $this->getTemplate()->set_var("Block", "");
            }
    		
    	    // Navigator
            if($iRecordsTotal > $iItemsForPage){
                    $TotalPages = ceil($iRecordsTotal / $iItemsForPage);
                    //$tpl->set_var("iLastPage",	$TotalPages);
                    $iPageMin = $iPage-2;
                    $iPageMax = $iPage+2;
                    if($iPageMin < 1){
                            $iPageMin = 1;
                            $iPageMax = 5;
                    }
                    if($iPageMax > $TotalPages){
                            $iPageMax = $TotalPages;
                            if ($TotalPages - 4 >= 1){
                                    $iPageMin = $TotalPages - 4;
                            }
                    }
                    for($i=$iPageMin; $i<=$iPageMax; $i++){
                    $this->getTemplate()->set_var("iPage", $i);
                    $this->getTemplate()->set_var("funcion", "paginar($i,'comunidad/masInstituciones','listadoInstituciones','$params');");
                            $class = $i==$iPage ? "activo" : "";
                            $this->getTemplate()->set_var("ClassPag", $class);
                    $this->getTemplate()->parse("PaginaListBlock", true);
                    }
                    $this->getTemplate()->parse("paginacion", false);
            }else{
                    $this->getTemplate()->set_var("paginacion", "");
            }
			
            $this->getResponse()->setBody($this->getTemplate()->pparse('pageRightInnerMainCont', false));
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
                        if(count($vListaProvincias)>0){
                                foreach($vListaProvincias as $oProvincia){
                                        $obj 		= new stdClass();
                                        $obj->id 	= $oProvincia->getId();
                                        $obj->sNombre = $oProvincia->getNombre();
                                        array_push($result,$obj);
                                }
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
	    $iInstitucionId     = $this->getRequest()->getPost("iInstitucionId");
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
            $array = array();
            $iRecordsTotalPais=0;
            $listaPaises	= ComunidadController::getInstance()->listaPaises($array, $iRecordsTotalPais, null,  null,  null,  null);
            foreach ($listaPaises as $oPais){
                $this->getTemplate()->set_var("iPaisId", $oPais->getId());
                $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
                $this->getTemplate()->parse("ListaPaisesBlock", true);
            }
            $iRecordsTotal=0;
            $sOrderBy= $sOrder= $iIniLimit= $iRecordCount= null;

            $iRecordPerPage	= 5;
	    $iInstitucionId     = $this->getRequest()->getPost("iInstitucionId");
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
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
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