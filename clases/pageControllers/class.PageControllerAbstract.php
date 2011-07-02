<?php
/**
 * Se codifican todos los metodos que comparten en comun los PageControllers.
 * Tambien se declaran los metodos para que esten disponibles los helpers.
 * Los helpers se instancian solo cuando se hace el get (Agregacion).
 * 
 * @author Matias Velilla
 */
class PageControllerAbstract implements PageControllerInterface
{
    private $request;

    private $response;

    private $invokeArgs;
    
    /**
     * Instancia de Templates
     */
    private $template = null;
    /**
     * Instancia de UrlHelper
     */
    private $url = null;
    /**
     * Instancia de JsonHelper
     */
    private $json = null;
    /**
     * Instancia de RedirectorHelper
     */
    private $redirector = null;
    /**
     * Instancia de AjaxHelper
     */
    private $ajax = null;

    public function __construct(HttpRequest $request, Response $response, array $invokeArgs = array())
    {
        $this->request = $request;
        $this->response = $response;
        $this->invokeArgs = $invokeArgs;
    }
	    
    public function dispatch($action)
    {
        $this->$action();
    }

    public function index(){}

    protected final function getTemplate()
    {
        if (null === $this->template){
            $this->template = new Templates();
        }
        return $this->template;
    }

    protected final function setTemplate(Templates $template)
    {
        $this->template = $template;
        return $this;
    }

    protected final function restartTemplate()
    {
        $this->template = null;
        $this->template = new Templates();
        return $this;
    }

    protected final function getUrlHelper()
    {
        if(null === $this->url)
        {
            $this->url = new UrlHelper();
        }
        return $this->url;
    }

    protected final function getJsonHelper()
    {
        if(null === $this->json)
        {
            $this->json = new JsonHelper();
        }
        return $this->json;
    }

    protected final function getRedirectorHelper()
    {
        if(null === $this->redirector)
        {
            $this->redirector = new RedirectorHelper();
        }
        return $this->redirector;
    }

    protected final function getAjaxHelper()
    {
        if(null === $this->ajax)
        {
            $this->ajax = new AjaxHelper();
        }
        return $this->ajax;
    }

    protected final function getRequest()
    {
        return $this->request;
    }

    protected final function getResponse()
    {
        return $this->response;
    }

    /**
     * Devuelve Parametro de Invocacion
     *
     * @param mixed $default Valor por defecto si el parametro no se encuentra
     */
    protected final function getInvokeParam($key, $default = null)
    {
        if(isset($this->invokeArgs[$key])){
            return $this->invokeArgs[$key];
        }
        return $default;
    }

    protected function imprimirInvokeParams()
    {
        echo "<pre>"; print_r($this->invokeArgs); echo "</pre>"; exit();
    }

    /**
     * Si dentro de los parametros del request existe msgInfo, msgError o msgCorrecto
     * entonces agrega el componente MsgTop segun corresponda en el template.
     * NOTA: el mensaje desaparece automaticamente a traves de javascript (vistas.js)
     *
     * @param boolean $iconos Por defecto se carga el bloque con icono
     */
    protected final function printMsgTop($iconos = true)
    {
        if (null !== $this->template)
        {
            switch(true){
                case $this->request->has('msgInfo'):
                {
                    $msg = $this->request->getParam('msgInfo');
                    $bloque = ($iconos)?'MsgTopInfoBlockI32':'MsgTopInfoBlock';
                    break;
                }
                case $this->request->has('msgError'):
                {
                    $msg = $this->request->getParam('msgError');
                    $bloque = ($iconos)?'MsgTopErrorBlockI32':'MsgTopErrorBlock';
                    break;                    
                }
                case $this->request->has('msgCorrecto'):
                {
                    $msg = $this->request->getParam('msgCorrecto');
                    $bloque = ($iconos)?'MsgTopCorrectoBlockI32':'MsgTopCorrectoBlock';
                    break;
                }
                default: return;
            }
            $this->template->load_file_section("gui/componentes/carteles.gui.html", "msgTop", $bloque);
            $this->template->set_var("sMensajeTop", $msg);
        }
    }

    /**
     * Todos los Controladores de pagina van a compartir un metodo en caso de que se tenga que mostrar una pagina de error 404
     * Sin embargo el metodo puede ser modificado por algun controlador concreto en caso de que se quiera funcionalidad extra.
     * Por ejemplo, puedo redeclarar el metodo en PublicacionesControllerIndex para mostrar un listado de las ultimas publicaciones.
     *
     * @TODO programar la vista, por ahora para probar simplemente imprime que la pagina no existe.
     */
    public function redireccion404()
    {
        //establesco titulo y mensaje de la ficha de mensaje
        switch(true){
            case $this->request->has('msgInfo'):
            {
                $tituloMensajeError = $this->request->getParam('msgInfo');
                $ficha = "MsgFichaInfoBlock";
                break;
            }
            case $this->request->has('msgError'):
            {
                $tituloMensajeError = $this->request->getParam('msgError');
                $ficha = "MsgFichaErrorBlock";
                break;
            }
            default:
                $tituloMensajeError = "No se ha encontrado la página solicitada.";
                $ficha = "MsgFichaInfoBlock";
                break;                
        }
        
        $this->getTemplate()->load_file("gui/templates/index/frame02-01.gui.html", "frame");

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", "Página no enconrada");
        $this->getTemplate()->set_var("sMetaDescription", "");
        $this->getTemplate()->set_var("sMetaKeywords", "");

        $this->getTemplate()->load_file_section("gui/vistas/index/redireccion404.gui.html", "centerPageContent", "TituloBlock");

        $mensajeInfoError = "Puedes que hayas hecho clic en un enlace caducado o que hayas escrito mal la dirección.
                             En algunas direcciones web se distingue entre mayúsculas y minúsculas.";

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "centerPageContent", $ficha, true);
        $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
        $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

        //Link a Inicio y pagina anterior
        $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
        $this->getTemplate()->set_var("idOpcion", 'opt1');
        $this->getTemplate()->set_var("hrefOpcion", $this->request->getBaseUrl().'/');
        $this->getTemplate()->set_var("sNombreOpcion", "Volver a inicio");
        $this->getTemplate()->parse("OpcionesMenu", true);

        $this->getTemplate()->set_var("idOpcion", 'opt1');
        $this->getTemplate()->set_var("hrefOpcion", "javascript:history.go(-1)");
        $this->getTemplate()->set_var("sNombreOpcion", "Volver a la página anterior");
        $this->getTemplate()->parse("OpcionMenuLastOpt");
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }
}