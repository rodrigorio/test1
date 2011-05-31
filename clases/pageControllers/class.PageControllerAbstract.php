<?php
/**
 * Se codifican todos los metodos que comparten en comun los PageControllers.
 * Tambien se declaran los metodos para que esten disponibles los helpers.
 * Los helpers se instancian solo cuando se hace el get (Agregacion)
 * 
 * @author Matias Velilla
 */
class PageControllerAbstract implements PageControllerInterface
{
    private $request;
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

    public function __construct(HttpRequest $request, array $invokeArgs = array())
    {
        $this->request = $request;
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
        echo "La pagina no existe";
    }
}