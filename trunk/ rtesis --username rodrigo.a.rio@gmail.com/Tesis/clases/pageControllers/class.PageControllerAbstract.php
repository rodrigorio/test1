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
     * Instancia de InflectorHelper
     */
    private $inflector = null;
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
    /**
     * Instancia de UploadHelper
     */
    private $upload = null;
    /**
     * Instancia de DownloadHelper
     */
    private $download = null;
    /**
     * Instancia de ExportarPlanillaCalculoHelper
     */
    private $exportarPlanilla = null;
    /**
     * Instancia de EmbedVideoHelper
     */
    private $embedVideo = null;

    
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

    protected final function getInflectorHelper()
    {
        if(null === $this->inflector)
        {
            $this->inflector = new InflectorHelper();
        }
        return $this->inflector;
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

    protected final function getUploadHelper()
    {
        if(null === $this->upload)
        {
            $this->upload = new UploadHelper();
        }
        return $this->upload;
    }

    protected final function getDownloadHelper()
    {
        if(null === $this->download)
        {
            $this->download = new DownloadHelper();
        }
        return $this->download;
    }

    protected final function getExportarPlanillaHelper()
    {
        if(null === $this->exportarPlanilla)
        {
            $this->exportarPlanilla = new ExportarPlanillaCalculoHelper();
        }
        return $this->exportarPlanilla;
    }
    
    protected final function getEmbedVideoHelper()
    {
        if(null === $this->embedVideo)
        {
            $this->embedVideo = new EmbedVideoHelper();
        }
        return $this->embedVideo;
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

    protected final function getInvokeArgs()
    {
        return $this->invokeArgs;
    }

    protected function imprimirInvokeParams()
    {
        echo "<pre>"; print_r($this->invokeArgs); echo "</pre>"; exit();
    }

    /**
     * Este metodo es para obtener la url desde una de las RegexRoute cargadas en el router
     */
    protected final function getUrlFromRoute($routeName, $baseUrl = false)
    {
        $router = FrontController::getInstance()->getRouter();
        $url = "/".$router->getRoute($routeName)->getRegex();
        if($baseUrl){
            return $this->request->getBaseUrl().$url;
        }
        return $url;
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
        $bHayMensaje = false;
        
        if (null !== $this->template)
        {
            switch(true){
                case $this->request->has('msgInfo'):
                {
                    $msg = $this->request->getParam('msgInfo');
                    $bloque = ($iconos)?'MsgTopInfoBlockI32':'MsgTopInfoBlock';
                    $bHayMensaje = true;
                    break;
                }
                case $this->request->has('msgError'):
                {
                    $msg = $this->request->getParam('msgError');
                    $bloque = ($iconos)?'MsgTopErrorBlockI32':'MsgTopErrorBlock';
                    $bHayMensaje = true;
                    break;                    
                }
                case $this->request->has('msgCorrecto'):
                {
                    $msg = $this->request->getParam('msgCorrecto');
                    $bloque = ($iconos)?'MsgTopCorrectoBlockI32':'MsgTopCorrectoBlock';
                    $bHayMensaje = true;
                    break;
                }
                default: return;
            }

            if($bHayMensaje){
                $this->template->load_file_section("gui/componentes/carteles.gui.html", "msgTop", $bloque);
                $this->template->set_var("sMensajeTop", $msg);
            }
        }
    }

    /**
     * Todos los Controladores de pagina van a compartir un metodo en caso de que se tenga que mostrar una pagina de error 404
     * Sin embargo el metodo puede ser modificado por algun controlador concreto en caso de que se quiera funcionalidad extra.
     * Por ejemplo, puedo redeclarar el metodo en PublicacionesControllerIndex para mostrar un listado de las ultimas publicaciones.
     *
     */
    protected function redireccion404()
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

    /**
     * Setea las variables necesarias para paginar la consulta en el sql.
     */
    protected final function initPaginator($iRecordPerPage = null)
    {
        if(null === $iRecordPerPage){
            $iRecordPerPage = 5;
        }

        $sOrderBy = $this->getRequest()->getPost("sOrderBy");
        $sOrderBy = strlen($sOrderBy) ? $sOrderBy : null;

        $sOrder = $this->getRequest()->getPost("sOrder");
        $sOrder = strlen($sOrder) ? $sOrder : null;

        $iPage = $this->getRequest()->getPost("iPage");
        $iPage = strlen($iPage) ? $iPage : 1;

        $iItemsForPage = $this->getRequest()->getPost("RecPerPage") ? $this->getRequest()->getPost("RecPerPage") : $iRecordPerPage ;
        $iMinLimit = ($iPage-1) * $iItemsForPage;

        return array($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder);                     
    }

    /**
     * Para generar los links de las paginas
     */
    protected final function calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, $url, $div, $params = null)
    {
        //Paginacion
        $this->getTemplate()->load_file_section("gui/componentes/paginacion.gui.html", "paginacion", "Paginacion01Block");
        $this->getTemplate()->set_var("iPageActual", $iPage);

        if($this->getRequest()->has('sOrder') && $this->getRequest()->getPost('sOrder') != ""
           && $this->getRequest()->has('sOrderBy') && $this->getRequest()->getPost('sOrderBy') != ""){
            $params[] = "sOrder=".$this->getRequest()->getPost('sOrder');
            $params[] = "sOrderBy=".$this->getRequest()->getPost('sOrderBy');
        }

        if ($iRecordsTotal > $iItemsForPage) {
            $TotalPages = ceil($iRecordsTotal / $iItemsForPage);
            $iPageMin = $iPage - 2;
            $iPageMax = $iPage + 2;

            if ($iPageMin < 1) {
                $iPageMin = 1;
                $iPageMax = 5;
            }

            if ($iPageMax > $TotalPages) {
                $iPageMax = $TotalPages;
                if ($TotalPages - 4 >= 1) {
                    $iPageMin = $TotalPages - 4;
                }
            }

            if (count($params) > 0) {
                $params = implode($params, "&");
            } else {
                $params = "";
            }

            for ($i = $iPageMin; $i <= $iPageMax; $i++) {
                $this->getTemplate()->set_var("iPage", $i);
                $this->getTemplate()->set_var("funcion", "paginar($i, '$url', '$div', '$params');");
                $class = $i == $iPage ? "activo" : "";
                $this->getTemplate()->set_var("ClassPag", $class);
                $this->getTemplate()->parse("PaginaListBlock", true);
            }

            $this->getTemplate()->parse("paginacion", false);
        }else{
            $this->getTemplate()->set_var("paginacion", "");
        }
    }

    /**
     * Genera los botones de ordenar ascendente y descendente segun el array $aOrderBy
     */
    protected final function initOrderBy(&$sOrderBy, $sOrder, &$aOrderBy)
    {        
        /*
         * porque si existen los parametros al menos uno cambio de estado
         * entonces para ESA columna invierto el order.
         */
        if($sOrderBy !== null && $sOrder !== null){
            
            $sOrder = ($sOrder == "asc")?"desc":"asc";
            $aOrderBy[$sOrderBy]['order'] = $sOrder;

            //convierto el alias que esta en la pagina por el verdadero orderBy que va a parar en la consulta
            $sOrderBy = $aOrderBy[$sOrderBy]['orderBy'];
        }

        //parseo los botones de orderBy
        foreach($aOrderBy as $aliasOrderBy => $aOrderByData){
            $block = ($aOrderByData['order'] == 'asc') ? "IconOrderByAscBlock" : "IconOrderByDescBlock";
            $this->getTemplate()->load_file_section("gui/componentes/backEnd/grillas.gui.html", $aOrderByData['variableTemplate'], $block);
            $this->getTemplate()->set_var("sAliasOrderBy", $aliasOrderBy);
            $this->getTemplate()->parse($aOrderByData['variableTemplate'], false);
            $this->getTemplate()->delete_parsed_blocks($block);
        }
    }

    /**
     * Extrae todos los post desde el form de filtro de listado segun $aFiltrosForm,
     * genera los parametros para adjuntar a los links del paginador por ejemplo,
     * ademas genera el filtro para enviar al metodo de sql segun el nombre de las columnas de las tablas
     */
    protected final function initFiltrosForm(&$filtroSql, &$paramsGet, $aFiltrosForm)
    {
        foreach($aFiltrosForm as $nombreFiltro => $columnaSql){
            if($this->getRequest()->has($nombreFiltro) && $this->getRequest()->getParam($nombreFiltro) != ""){

                $paramsGet[] = $nombreFiltro."=".$this->getRequest()->getParam($nombreFiltro);

                if($columnaSql != "fechaDesde" && $columnaSql != "fechaHasta"){
                    $filtroSql[$columnaSql] = $this->getRequest()->getParam($nombreFiltro);                                    
                }
                if($columnaSql == "fechaDesde"){
                    $filtroSql['fecha'][$columnaSql] =  $this->getRequest()->getParam($nombreFiltro);
                }
                if($columnaSql == "fechaHasta"){
                    $filtroSql['fecha'][$columnaSql] = $this->getRequest()->getParam($nombreFiltro);
                }
            }
        }
    }
}