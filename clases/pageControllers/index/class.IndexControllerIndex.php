<?php

/**
 * 	Action Controller Index
 */
class IndexControllerIndex extends PageControllerAbstract
{
	private function setFrameTemplate(){
	    $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
		return $this;
	}
  	private function setHeadTemplate()
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

	 private function setMenuTemplate()
    {
        $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "menuHeader", "MenuHorizontal03Block");
        //Opcion1
        $this->getTemplate()->set_var("idOpcion", 'opt1');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/home');
        $this->getTemplate()->set_var("sNombreOpcion", "Inicio");
        $this->getTemplate()->parse("OpcionesMenu", false);
        //Opcion2
        $this->getTemplate()->set_var("idOpcion", 'opt2');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/registrarse');
        $this->getTemplate()->set_var("sNombreOpcion", "Registrarse");
        $this->getTemplate()->parse("OpcionMenuLastOpt", false);
		$this->getTemplate()->parse("menuHeader", false);
		
        return $this;
    }
	public function index(){
		 try{
             $this->setFrameTemplate()
                  ->setHeadTemplate()
                  ->setMenuTemplate();
			
	        $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
	        $this->getTemplate()->set_var("tituloHeader", "SGP...");
	        $this->getTemplate()->set_var("subtituloHeader", "subtitulo header");
	        $this->getTemplate()->set_var("topPageContent", "top page content");
	        
	        
	        $this->getTemplate()->set_var("centerPageContent", "       aaa");
	        
	        
	        $this->getTemplate()->set_var("footerContent", "footer content");
	        $this->getTemplate()->set_var("footerSubContent", "footer subcontent");
	        $this->getTemplate()->set_var("footerSubCopyright", "copyright");
        	$this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
		 }catch(Exception $e){
        	print_r($e);
            //throw new Exception('Error Template');
            //return;
        }
	} 
	public function registrarse(){
		 try{
             $this->setFrameTemplate()
                  ->setHeadTemplate()
                  ->setMenuTemplate();
			
	        $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
	        $this->getTemplate()->set_var("tituloHeader", "SGP...");
	        $this->getTemplate()->set_var("subtituloHeader", "subtitulo header");
	        $this->getTemplate()->set_var("topPageContent", "top page content");
	        
	        $this->getTemplate()->load_file("gui/vistas/index/registracion.gui.html", "centerPageContent");
	        
	        $this->getTemplate()->parse("centerPageContent", false);
	        
	        $this->getTemplate()->set_var("footerContent", "footer content");
	        $this->getTemplate()->set_var("footerSubContent", "footer subcontent");
	        $this->getTemplate()->set_var("footerSubCopyright", "copyright");
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
        $this->getTemplate()->load_file("gui/templates/index/frame02-01.gui.html", "frame");
        
        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaCentralContent", "MsgInfoBlockI32");
        $this->getTemplate()->set_var("sMensaje", "El sitio se encuentra en construccion.");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "columnaCentralContent", "ImagenSitioEnConstruccionBlock", true);
        $this->getTemplate()->set_var("srcSitioEnConstruccion", 'gui/images/banners-logos/bajo_construccion.jpg');
        $this->getTemplate()->set_var("widthSitioEnConstruccion", "580");
        $this->getTemplate()->set_var("heightSitioEnConstruccion", "300");
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function sitioOffline()
    {
        $$this->getTemplate()->load_file("gui/templates/index/frame02-01.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-offline.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "columnaCentralContent", "MsgInfoBlockI32");
        $this->getTemplate()->set_var("sMensaje", "El sitio se encuentra fuera de línea por el momento.");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "columnaCentralContent", "ImagenSitioEnConstruccionBlock", true);
        $this->getTemplate()->set_var("srcSitioEnConstruccion", 'gui/images/banners-logos/fuera_de_linea.jpg');
        $this->getTemplate()->set_var("widthSitioEnConstruccion", "580");
        $this->getTemplate()->set_var("heightSitioEnConstruccion", "300");

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Si existe $_GET['callback'] entonces quiere decir que hay que devolver Json (porque usamos Jquery en el ajax)
     * Si no existe callback asumimos que es una peticion ajax de html y devolvemos una ficha con mensaje de error
     */
    public function ajaxError()
    {
        $request = $this->getRequest();
        
        //extraigo mensaje si es que existe y el tipo de ficha (la ficha solo se usa si hay que devolver html)
        switch(true){
            case $request->has('msgInfo'):
            {
                $mensaje = $request->getParam('msgInfo');
                $ficha = "MsgInfoBlockI32";
                break;
            }
            case $this->request->has('msgError'):
            {
                $mensaje = $request->getParam('msgError');
                $ficha = "MsgErrorBlockI32";
                break;
            }
            default:
                $mensaje = "Ha ocurrido un error al procesar los datos";
                $ficha = "MsgInfoBlockI32";
                break;
        }
       
        if($request->has('callback')){
            //devuelvo error ajax en formato json
            $this->getJsonHelper()->initJsonAjaxResponse()
                                  ->setSuccess(false)
                                  ->setMessage($mensaje)
                                  ->sendJsonAjaxResponse();
        }else{
            //devuelvo error ajax en formato html

            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "respuesta", $ficha);
            $this->getTemplate()->set_var("sMensaje", $mensaje);
            
            //setea los headers para response ajax html y setea el body content
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('respuesta', false));
        }       
    }

    public function index()
    {
        $this->home();
    }

    public function home()
    {
        
    }
}