<?php

class CategoriaControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "jsContent", "JsContent");
        
        return $this;
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();

            $this->getTemplate()->set_var("CargarCategoriaBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "MainContent");
            $filtro = array();
           	$iRecordPerPage	= 5;
	    	$iPage			= $this->getRequest()->getPost("iPage");
		   	$iPage			= strlen($iPage) ? $iPage : 1;
		  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
			$iMinLimit		= ($iPage-1) * $iItemsForPage;
			$sOrderBy		= null;	
			$sOrder			= null;
			$iRecordsTotal	= 0;
            $vCategoria = AdminController::getInstance()->obtenerCategoria($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vCategoria)>0){
            	$i=0;
	            foreach ($vCategoria as $oCategoria){
	            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
	                $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
	                $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());
	                $this->getTemplate()->parse("ListaCategoriasBlock", true);
	                $i++;
	            }
                $this->getTemplate()->set_var("NoRecordsListaCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("ListaCategoriasBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "noRecords", "NoRecordsListaCategoriasBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
	            $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function nuevaCategoria(){
         try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");

            $this->printMsgTop();
            
            $this->getTemplate()->set_var("ListadoCategoriasBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "MainContent");
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function editarCategoria(){
         try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionCategorias");
            
            $this->printMsgTop();
            
            $this->getTemplate()->set_var("ListadoCategoriasBlock","");
            //widgets
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "widgetsContent", "WidgetsContent");
            //contenido ppal home
            $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "mainContent", "MainContent");
            
            if($this->getRequest()->getPost("iCategoriaId")!=""){
                $filtro = array("c.id"=>$this->getRequest()->getPost("iCategoriaId"));
                $vCategoria = AdminController::getInstance()->obtenerCategoria($filtro);
                foreach ($vCategoria as $oCategoria){
	                $this->getTemplate()->set_var("iCategoriaId",     $oCategoria->getId());
	                $this->getTemplate()->set_var("sNombre",     $oCategoria->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());
                }
            }
                
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function verificarUsoDeCategoria() {
    	try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "listaCategoria", "ListadoCategoriasBlock");
            $filtroEliminar = array("e.id"=>$this->getRequest()->getParam("id") );
            $vCategoria = AdminController::getInstance()->obtenerCategoria($filtroEliminar);
            $res = false;
			if(count($vCategoria)>0){
            	$oCategoria = $vCategoria[0];
            	$res = AdminController::getInstance()->categoriaUsadaPorUsuario($oCategoria);
			}
			echo $res;
    	}catch(Exception $e){
            print_r($e);
        }
    }
    
    public function eliminarCategoria(){
		try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "listaCategoria", "ListadoCategoriasBlock");
            $filtroEliminar = array("c.id"=>$this->getRequest()->getPost("id"));
            $vCategoria = AdminController::getInstance()->obtenerCategoria($filtroEliminar);
            if(count($vCategoria)>0){
            	$oCategoria = $vCategoria[0];
	            $res = AdminController::getInstance()->eliminarCategoria($oCategoria);
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
		            $vCategoria 	= AdminController::getInstance()->obtenerCategoria($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
		            if(count($vCategoria)>0){
		            	$i=0;
			            foreach ($vCategoria as $oCategoria){
			            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
			                $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
			                $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
			                $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());
			                $this->getTemplate()->parse("ListaCategoriasBlock", true);
			                $i++;
			            }
		                $this->getTemplate()->set_var("NoRecordsListaCategoriasBlock", "");
		            }else{
		                $this->getTemplate()->set_var("ListaCategoriasBlock", "");
		                $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "noRecords", "NoRecordsListaCategoriasBlock");
		                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
			            $this->getTemplate()->parse("noRecords", false);
		            }
	            }
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('listaCategoria', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
    
    public function procesarCategoria(){
        try{
            $sNombre        = $this->getRequest()->getPost("nombre");
            $sDescripcion   = $this->getRequest()->getPost("descripcion");
            if($sNombre == "" && $sDescripcion==""){
                $this->index();
                return;
            }
            if($this->getRequest()->getPost("id")!=""){
                $filtro = array("c.id"=>$this->getRequest()->getPost("id"));
                $oCategoria = AdminController::getInstance()->obtenerCategoria($filtro);
                $oCategoria = $oCategoria[0];
            }else{
                $oCategoria = Factory::getCategoriaInstance(new stdClass());
            }
			$oCategoria->setDescripcion($sDescripcion);
            $oCategoria->setNombre($sNombre);
            $r = AdminController::getInstance()->guardarCategoria($oCategoria);
            $this->index();
        }catch(Exception $e){
            print_r($e);
        }
    }
    
  	public function buscarCategoria(){
		try{
			$this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "listaCategoria", "ListadoCategoriasBlock");
            $filtro = array("c.nombre"=>$this->getRequest()->getPost("nombre"));
           	$iRecordPerPage	= 5;
	    	$iPage			= $this->getRequest()->getPost("iPage");
		   	$iPage			= strlen($iPage) ? $iPage : 1;
		  	$iItemsForPage	= $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
			$iMinLimit		= ($iPage-1) * $iItemsForPage;
			$sOrderBy		= null;	
			$sOrder			= null;
			$iRecordsTotal	= 0;
            $vCategoria = AdminController::getInstance()->buscar($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iMinLimit,$iItemsForPage);
            if(count($vCategoria)>0){
            	$i=0;
	            foreach ($vCategoria as $oCategoria){
	            	$this->getTemplate()->set_var("odd", ($i % 2 == 0) ? "gradeC" : "gradeA");
	                $this->getTemplate()->set_var("iCategoriaId", $oCategoria->getId());
	                $this->getTemplate()->set_var("sNombre", $oCategoria->getNombre());
	                $this->getTemplate()->set_var("sDescripcion", $oCategoria->getDescripcion());
	                $this->getTemplate()->parse("ListaCategoriasBlock", true);
	                $i++;
	            }
                $this->getTemplate()->set_var("NoRecordsListaCategoriasBlock", "");
            }else{
                $this->getTemplate()->set_var("listaCategoria", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/categoria.gui.html", "noRecords", "NoRecordsListaCategoriasBlock");
                $this->getTemplate()->set_var("sNoRecords", "No se encontraron registros.");
	            $this->getTemplate()->parse("noRecords", false);
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('listaCategoria', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
}