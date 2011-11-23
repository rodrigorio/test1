<?php

/**
 *  Action Controller Publicaciones
 *
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.*
 */
class EspecialidadControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    /*
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setCabecera(Templates &$template)
    {
        $request = FrontController::getInstance()->getRequest();
        
        //menu cabecera
        $template->set_var("hrefHomeModuloIndex", $request->getBaseTagUrl()."/");
        $template->set_var("hrefHomeModuloComunidad", $request->getBaseTagUrl()."/comunidad/home");
        $template->set_var("hrefHomeModuloSeguimientos", $request->getBaseTagUrl()."/seguimientos/home");
        $template->set_var("hrefHomeModuloAdmin", $request->getBaseTagUrl()."/admin/home");

        //info user
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $perfilDesc = $perfil->getDescripcion();
        $nombreUsuario = $perfil->getNombreUsuario();
        
        $template->set_var("srcImageUser", "#");
        $template->set_var("userName", $nombreUsuario);
        $template->set_var("hrefEditarPerfil", $request->getBaseTagUrl().'/comunidad/datos-personales');
        $template->set_var("perfilDescripcion", $perfilDesc);
        $template->set_var("hrefCerrarSesion", $request->getBaseTagUrl().'/logout');
    }

    /*
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setMenu(Templates &$template, $currentOption = '')
    {
        $request = FrontController::getInstance()->getRequest();

        //menu cabecera
        $template->set_var("sHrefEspecialidadCargar", $request->getBaseTagUrl()."admin/nueva-especialidad");
        $template->set_var("sHrefEspecialidadListar", $request->getBaseTagUrl()."admin/listar-especialidad");
        $template->set_var("sHrefEspecialidadIndex", $request->getBaseTagUrl()."/admin/administrar-especialidad");
        $template->set_var("sHrefCategoriaIndex", $request->getBaseTagUrl()."/admin/administrar-categorias");

    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();
            $this->printMsgTop();
            $this->setCabecera($this->getTemplate());
            $this->setMenu($this->getTemplate());
            $this->getTemplate()->set_var("CargarEspecialidadBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "MainContent");
            $filtro = array();
           	$iRecordPerPage	= 5;
	    	$iPage			= $this->getRequest()->getPost("iPage");
		   	$iPage			= strlen($iPage) ? $iPage : 1;
		  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
			$iMinLimit		= ($iPage-1) * $iItemsForPage;
			$sOrderBy		= null;	
			$sOrder			= null;
			$iRecordsTotal	= 0;
            $vEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vEspecialidad)>0){
            	$i=0;
	            foreach ($vEspecialidad as $oEspecialidad){
	            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
	                $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
	                $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
	                $this->getTemplate()->parse("ListaEspecialidadesBlock", true);
	                $i++;
	            }
                $this->getTemplate()->set_var("NoRecordsListaEspecialidadesBlock", "");
            }else{
                $this->getTemplate()->set_var("ListaEspecialidadesBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "noRecords", "NoRecordsListaEspecialidadesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
	            $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function nuevaEspecialidad(){
         try{
            $this->setFrameTemplate()
                 ->setHeadTag();
            $this->printMsgTop();
            $this->setCabecera($this->getTemplate());
            $this->setMenu($this->getTemplate());
            
            $this->getTemplate()->set_var("ListadoEspecialidadesBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "MainContent");
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function editarEspecialidad(){
         try{
            $this->setFrameTemplate()
                 ->setHeadTag();
            $this->printMsgTop();
            $this->setCabecera($this->getTemplate());
            $this->setMenu($this->getTemplate());
            
            $this->getTemplate()->set_var("ListadoEspecialidadesBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "mainContent", "MainContent");
            
            if($this->getRequest()->getPost("iEspecialidadId")!=""){
                $filtro = array("e.id"=>$this->getRequest()->getPost("iEspecialidadId"));
                $vEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtro);
                foreach ($vEspecialidad as $oEspecialidad){
	                $this->getTemplate()->set_var("iEspecialidadId",     $oEspecialidad->getId());
	                $this->getTemplate()->set_var("sNombre",     $oEspecialidad->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
                }
            }
                
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function verificarUsoDeEspecialidad() {
    	try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "listaEspecialidad", "ListadoEspecialidadesBlock");
            $filtroEliminar = array("e.id"=>$this->getRequest()->getParam("id") );
            $vEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtroEliminar);
            $res = false;
			if(count($vEspecialidad)>0){
            	$oEspecialidad = $vEspecialidad[0];
            	$res = AdminController::getInstance()->especialidadUsadaPorUsuario($oEspecialidad);
			}
			echo $res;
    	}catch(Exception $e){
            print_r($e);
        }
    }
    
    public function eliminarEspecialidad(){
		try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "listaEspecialidad", "ListadoEspecialidadesBlock");
            $filtroEliminar = array("e.id"=>$this->getRequest()->getPost("id"));
            $vEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtroEliminar);
            if(count($vEspecialidad)>0){
            	$oEspecialidad = $vEspecialidad[0];
	            $res = AdminController::getInstance()->eliminarEspecialidad($oEspecialidad);
	            if($res){
		            $filtro			 = array();
		           	$iRecordPerPage	= 5;
			    	$iPage			= $this->getRequest()->getPost("iPage");
				   	$iPage			= strlen($iPage) ? $iPage : 1;
				  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
					$iMinLimit		= ($iPage-1) * $iItemsForPage;
					$sOrderBy		= null;	
					$sOrder			= null;
					$iRecordsTotal	= 0;
		            $vEspecialidad 	= AdminController::getInstance()->obtenerEspecialidad($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
		            if(count($vEspecialidad)>0){
		            	$i=0;
			            foreach ($vEspecialidad as $oEspecialidad){
			            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
			                $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
			                $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());
			                $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
			                $this->getTemplate()->parse("ListaEspecialidadesBlock", true);
			                $i++;
			            }
		                $this->getTemplate()->set_var("NoRecordsListaEspecialidadesBlock", "");
		            }else{
		                $this->getTemplate()->set_var("listaEspecialidad", "");
		                $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "noRecords", "NoRecordsListaEspecialidadesBlock");
		                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
			            $this->getTemplate()->parse("noRecords", false);
		            }
	            }
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('listaEspecialidad', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function procesarEspecialidad(){
        try{
            $sNombre        = $this->getRequest()->getPost("nombre");
            $sDescripcion   = $this->getRequest()->getPost("descripcion");
            if($sNombre == "" && $sDescripcion==""){
                $this->index();
                return;
            }
            if($this->getRequest()->getPost("id")!=""){
                $filtro = array("e.id"=>$this->getRequest()->getPost("id"));
                $oEspecialidad = AdminController::getInstance()->obtenerEspecialidad($filtro);
                $oEspecialidad = $oEspecialidad[0];
            }else{
                $oEspecialidad = Factory::getEspecialidadInstance(new stdClass());
            }
			$oEspecialidad->setDescripcion($sDescripcion);
            $oEspecialidad->setNombre($sNombre);
            $r = AdminController::getInstance()->guardarEspecialidad($oEspecialidad);
            $this->index();
        }catch(Exception $e){
            print_r($e);
        }
    }
    
  	public function buscarEspecialidad(){
		try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "listaEspecialidad", "ListadoEspecialidadesBlock");
            $filtro = array("e.nombre"=>$this->getRequest()->getPost("nombre"));
           	$iRecordPerPage	= 5;
	    	$iPage			= $this->getRequest()->getPost("iPage");
		   	$iPage			= strlen($iPage) ? $iPage : 1;
		  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
			$iMinLimit		= ($iPage-1) * $iItemsForPage;
			$sOrderBy		= null;	
			$sOrder			= null;
			$iRecordsTotal	= 0;
            $vEspecialidad 	= AdminController::getInstance()->buscar($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vEspecialidad)>0){
            	$i=0;
	            foreach ($vEspecialidad as $oEspecialidad){
	            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
	                $this->getTemplate()->set_var("iEspecialidadId", $oEspecialidad->getId());
	                $this->getTemplate()->set_var("sNombre", $oEspecialidad->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oEspecialidad->getDescripcion());
	                $this->getTemplate()->parse("ListaEspecialidadesBlock", true);
	                $i++;
	            }
                $this->getTemplate()->set_var("NoRecordsListaEspecialidadesBlock", "");
            }else{
                $this->getTemplate()->set_var("listaEspecialidad", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/especialidad.gui.html", "noRecords", "NoRecordsListaEspecialidadesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
	            $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('listaEspecialidad', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
}