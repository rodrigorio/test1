<?php

/**
 * Action Controller Index
 * 
 * Es Singleton para que se pueda reutilizar los pedazos del header y el footer.
 */
class IndexControllerIndex extends PageControllerAbstract
{    
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/index/frame01-01.gui.html", "frame");
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
        $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "jsContent", "JsContent");
        return $this;
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setCabecera(Templates $template)
    {
        $request = FrontController::getInstance()->getRequest();
        
        //links menu ppal
        $template->set_var("hrefOpcionInicio", $request->getBaseUrl().'/');
        $template->set_var("hrefOpcionAcceder", $request->getBaseUrl().'/login');
        $template->set_var("hrefOpcionPublicaciones", $request->getBaseUrl().'/publicaciones');
        $template->set_var("hrefOpcionInstituciones", $request->getBaseUrl().'/instituciones');
        $template->set_var("hrefOpcionProyecto", $request->getBaseUrl().'/proyecto-sgpapd');
        $template->set_var("hrefOpcionGrupoTrabajo", $request->getBaseUrl().'/grupo-de-trabajo');
        $template->set_var("hrefOpcionContacto", $request->getBaseUrl().'/contacto');
    }

    /**
     * Este metodo es estatico porque se usa desde los otros controladores de pagina del modulo.
     */
    static function setFooter(Templates $template)
    {
        $request = FrontController::getInstance()->getRequest();
        
        //redes sociales
        $template->set_var("hrefDelicious", '#');
        $template->set_var("hrefDigg", '#');
        $template->set_var("hrefFacebook", '#');
        $template->set_var("hrefLinkedin", '#');
        $template->set_var("hrefMyspace", '#');
        $template->set_var("hrefReddit", '#');
        $template->set_var("hrefTwitter", '#');

        //menu footer
        $template->set_var("hrefOpcionInicio", $request->getBaseUrl().'/');
        $template->set_var("hrefOpcionAcceder", $request->getBaseUrl().'/login');
        $template->set_var("hrefOpcionPublicaciones", $request->getBaseUrl().'/publicaciones');
        $template->set_var("hrefOpcionInstituciones", $request->getBaseUrl().'/instituciones');
        $template->set_var("hrefOpcionProyecto", $request->getBaseUrl().'/proyecto-sgpapd');
    }

    public function index(){
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();
            
            $this->setCabecera($this->getTemplate());
            $this->setFooter($this->getTemplate());

            $this->printMsgTop();

            //nombre seccion
            $this->getTemplate()->set_var("sNombreSeccionTopPage", "Inicio");

            if($this->getRequest()->get("mt")!=""){
            	$this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "msgTop", "MsgDialogInfoBlockI32");
                $this->getTemplate()->set_var("sMensaje", "Su contrase&ntilde;a ha sido modificada.");
                $this->getTemplate()->parse('msgTop', false);
            }
            //contenido home
            $this->getTemplate()->load_file_section("gui/vistas/index/home.gui.html", "centerPageContent", "HomeCenterPageBlock");
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
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

    /**
     *  luego de cerrar sesion redirige a la home del sitio.
     *  Syscontroller solo destruye la sesion si el perfil es != visitante
     */
    public function cerrarSesion()
    {
        SysController::getInstance()->cerrarSesion();
        $this->getRedirectorHelper()->gotoUrl("/");
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
            case $request->has('msgError'):
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

    /**
     * Vista ampliada de un video.. para utilizar con algun visor de javascript
     */
    public function ampliarVideo()
    {
        $iEmbedVideoId = $this->getRequest()->getParam('embedVideoId');

        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-01.gui.html", "frame");

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);
        $this->getTemplate()->set_var("popUpContent", $this->getEmbedVideoHelper()->getEmbedVideoCode($oEmbedVideo));

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
}
