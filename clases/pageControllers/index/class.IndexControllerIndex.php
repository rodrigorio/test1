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

        $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);
        return $this;
    }

    private function setMenuTemplate()
    {
        $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "menuHeader", "MenuPpalIndexBlock");
        //Opcion1
        $this->getTemplate()->set_var("idOpcion", 'menuPpalInicio');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
        $this->getTemplate()->set_var("sNombreOpcion", "Inicio");
        $this->getTemplate()->parse("OpcionesMenu", true);
        //Opcion3
        $this->getTemplate()->set_var("idOpcion", 'menuPpalAcceder');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/login');
        $this->getTemplate()->set_var("sNombreOpcion", "Acceder");
        $this->getTemplate()->parse("OpcionesMenu", true);

        //borro el submenu que todavia no se usa
        $this->getTemplate()->set_var("SubMenu", "");

        $this->getTemplate()->parse("menuHeader", false);		
        return $this;
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate()
                 ->setMenuTemplate();

            $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
            $this->getTemplate()->set_var("hrefLogoHeader", "http://www.ufasta.edu.ar");
            $this->getTemplate()->set_var("tituloHeader", "SGPAPD");
            $this->getTemplate()->set_var("subtituloHeader", "Sistema de gestión del proceso de aprendizaje en personas discapacitadas");

            //nombre seccion
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "topPageContent", "TituloSeccionBlock");
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Inicio");

            //contenido home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "centerPageContent", "HomeCenterPageBlock");
            
            //footer home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "footerContent", "HomeFooterBlock");

            //Limpio las opciones porque ya hay otros menues.
            $this->getTemplate()->set_var("OpcionesMenu", "");
            $this->getTemplate()->set_var("OpcionMenuLastOpt", "");
            
            $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "footerSubContent", "MenuHorizontal04Block");
            $this->getTemplate()->set_var("idOpcion", 'footerSubInicio');
            $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/');
            $this->getTemplate()->set_var("sNombreOpcion", "Inicio");
            $this->getTemplate()->parse("OpcionesMenu", true);

            $this->getTemplate()->set_var("idOpcion", 'footerSubAnterior');
            $this->getTemplate()->set_var("hrefOpcion", "javascript:history.go(-1)");
            $this->getTemplate()->set_var("sNombreOpcion", "Página anterior");
            $this->getTemplate()->parse("OpcionMenuLastOpt");

            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "footerSubCopyright", "HomeCopyrightBlock");

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }
        
    public function mostrarFormRegistracion(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTemplate()
                 ->setMenuTemplate();

            $this->getTemplate()->set_var("sourceLogoHeader", "gui/images/banners-logos/fasta.png");
            $this->getTemplate()->set_var("tituloHeader", "SGP...");
            $this->getTemplate()->set_var("subtituloHeader", "subtitulo header");
            $this->getTemplate()->set_var("topPageContent", "top page content");

            $this->getTemplate()->set_var("sEmail", "rio_rodrigo@gmail.com");
            $this->getTemplate()->set_var("sNombre", "Rodrigo");
            $this->getTemplate()->set_var("sApellido", "Rio");

            $this->getTemplate()->load_file("gui/vistas/index/registracion.gui.html", "centerPageContent");

            $this->getTemplate()->parse("centerPageContent", false);

            $this->getTemplate()->set_var("footerContent", "footer content");
            $this->getTemplate()->set_var("footerSubContent", "footer subcontent");
            $this->getTemplate()->set_var("footerSubCopyright", "copyright");
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            print_r($e);
        }
    }

    public function registrarse()
    {
        $sUserName 	= $this->getRequest()->getPost("username");
        $iTipoDni 	= $this->getRequest()->getPost("tipoDni");
        $iDni	 	= $this->getRequest()->getPost("dni");
        $sPassword 	= $this->getRequest()->getPost("password");
        $sEmail 	= $this->getRequest()->getPost("email");
        $sFirstName	= $this->getRequest()->getPost("firstname");
        $sLastName 	= $this->getRequest()->getPost("lastname");
        $sSex	 	= $this->getRequest()->getPost("sex");
        $dFechaNacimiento	 	= trim($this->getRequest()->getPost("fechaNacimiento"));
        $oObj		= new stdClass();
        $oObj->sNombreUsuario 	= $sUserName;
        $oObj->sContrasenia	= $sPassword;
        $oObj->sNombre		= $sFirstName;
        $oObj->sApellido	= $sLastName;
        $oObj->sSexo		= $sSex;
        $oObj->iTipoDocumentoId	= $iTipoDni;
    	$oObj->sNumeroDocumento	= $iDni;
    	$oObj->sEmail		= $sEmail;
    	$oObj->dFechaNacimiento	= $dFechaNacimiento." 00:00";
		
    	echo IndexController::getInstance()->registrar($oObj);
    }

    /**
     * Muestra pagina de sitio en construccion
     */
    public function sitioEnConstruccion()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");
        
        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "topPageContent", "TopPageBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-en-construccion.gui.html", "bottomPageContent", "BottomPageBlock");
        $this->getTemplate()->set_var("tituloVista", "Sitio en construccion");
        $this->getTemplate()->set_var("subtituloVista", "Estamos trabajando, muy pronto estaremos en línea");
            
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function sitioOffline()
    {
        $this->getTemplate()->load_file("gui/templates/index/frame02-02.gui.html", "frame");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-offline.gui.html", "headContent", "HeadBlock");
        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-offline.gui.html", "topPageContent", "TopPageBlock");
        $this->getTemplate()->load_file_section("gui/vistas/index/sitio-offline.gui.html", "bottomPageContent", "BottomPageBlock");
        $this->getTemplate()->set_var("tituloVista", "Sitio fuera de linea");
        $this->getTemplate()->set_var("subtituloVista", "El sitio se encuentra momentáneamente fuera de línea, sepa disculpar las molestias. No dude en concactarse con nosotros.");

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
}
