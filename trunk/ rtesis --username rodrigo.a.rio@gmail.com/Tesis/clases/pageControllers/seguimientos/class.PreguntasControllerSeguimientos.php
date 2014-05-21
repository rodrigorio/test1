<?php

/**
 * @author Matias Velilla
 *
 */
class PreguntasControllerSeguimientos extends PageControllerAbstract
{
    private $orderByConfig = array('descripcion' => array('variableTemplate' => 'orderByDescripcion',
                                                     'orderBy' => 'p.descripcion',
                                                     'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipo',
                                                   'orderBy' => 'p.tipo',
                                                   'order' => 'desc'));

    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/seguimientos/frame01-01.gui.html", "frame");
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

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "pageRightInnerCont", "PageRightInnerContListadoPreguntasBlock");

        $this->getTemplate()->set_var("hrefListadoSeguimientos", $this->getUrlFromRoute("seguimientosIndexIndex", true));
        $this->getTemplate()->set_var("hrefListadoEntrevistas", $this->getUrlFromRoute("seguimientosEntrevistasIndex", true));

        return $this;
    }


    public function index(){
        $this->listar();
    }

    public function procesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masPreguntas')){
            $this->masPreguntas();
            return;
        }

        if($this->getRequest()->has('agregarOpcion')){
            $this->agregarOpcion();
            return;
        }
    }

    public function listar()
    {
        try{
            //primero me fijo que este el id de entrevista
            $iEntrevistaId = $this->getRequest()->getParam('id');
            if(empty($iEntrevistaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //despues me fijo que el id sea de una entrevista perteneciente al integrante logueado
            if(!SeguimientosController::getInstance()->isEntrevistaUsuario($iEntrevistaId)){
                throw new Exception("No tiene permiso para editar la entrevista", 401);
            }

            $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

            $this->setFrameTemplate()
                 ->setMenuDerecha()
                 ->setHeadTag();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Preguntas");
            $this->getTemplate()->set_var("subtituloSeccion", "Entrevista: <span class='fost_it'>".$oEntrevista->getDescripcion()."</span>");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "pageRightInnerMainCont", "ListadoPreguntasBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $filtro = array('p.entrevistas_id' => $iEntrevistaId);
            $aPreguntas = SeguimientosController::getInstance()->getPreguntas($filtro, $iRecordsTotal, null, null, null, null);

            $this->getTemplate()->set_var("iEntrevistaId", $iEntrevistaId);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
            if(count($aPreguntas) > 0){

                $this->getTemplate()->set_var("NoRecordsPreguntasBlock", "");

            	foreach ($aPreguntas as $oPregunta){

                    $this->getTemplate()->set_var("iPreguntaId", $oPregunta->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oPregunta->getDescripcion());
                    $this->getTemplate()->set_var("sTipoEnum", get_class($oPregunta));
                    $this->getTemplate()->set_var("dFechaHora", $oPregunta->getFecha(true));

                    if($oPregunta->isPreguntaAbierta()){
                        $this->getTemplate()->set_var("sTipo", "Pregunta Abierta");
                        $iconoPreguntaBlock = "IconoTipoTextoBlock";
                        $this->getTemplate()->set_var("sOpciones", "");
                    }

                    if($oPregunta->isPreguntaMC()){
                        $this->getTemplate()->set_var("sTipo", "Pregunta Multiple Choise");
                        $iconoPreguntaBlock = "IconoTipoMCBlock";
                        $sOpciones = "<strong>Opciones: </strong> ";
                        $aOpciones = $oPregunta->getOpciones();
                        foreach($aOpciones as $oOpcion){
                            $sOpciones .= $oOpcion->getDescripcion().", ";
                        }
                        $sOpciones = substr($sOpciones, 0, -2);
                        $this->getTemplate()->set_var("sOpciones", $sOpciones);
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "iconoPregunta", $iconoPreguntaBlock);
                    $this->getTemplate()->set_var("iconoPregunta", $this->getTemplate()->pparse("iconoPregunta"));
                    $this->getTemplate()->delete_parsed_blocks($iconoPreguntaBlock);

                    $this->getTemplate()->parse("PreguntaBlock", true);
                }

                $params[] = "id=".$iEntrevistaId;
                $params[] = "masPreguntas=1";
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/preguntas-procesar", "listadoPreguntasResult", $params);
            }else{
                $this->getTemplate()->set_var("sNoRecords", "No hay preguntas cargadas en la entrevista");
                $this->getTemplate()->set_var("PreguntaBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    private function masPreguntas()
    {
        $iEntrevistaId = $this->getRequest()->getPost('id');
        if(empty($iEntrevistaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        if(!SeguimientosController::getInstance()->isEntrevistaUsuario($iEntrevistaId)){
            throw new Exception("No tiene permiso para editar la entrevista", 401);
        }

        $oEntrevista = SeguimientosController::getInstance()->getEntrevistaById($iEntrevistaId);

        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "ajaxGrillaPreguntasBlock", "GrillaPreguntasBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

        $filtro = array('p.entrevistas_id' => $iEntrevistaId);
        $aPreguntas = SeguimientosController::getInstance()->getPreguntas($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);
        if(count($aPreguntas) > 0){

            $this->getTemplate()->set_var("NoRecordsPreguntasBlock", "");

            foreach ($aPreguntas as $oPregunta){

                    $this->getTemplate()->set_var("iPreguntaId", $oPregunta->getId());
                    $this->getTemplate()->set_var("sDescripcion", $oPregunta->getDescripcion());
                    $this->getTemplate()->set_var("sTipoEnum", get_class($oPregunta));
                    $this->getTemplate()->set_var("dFechaHora", $oPregunta->getFecha(true));

                    if($oPregunta->isPreguntaAbierta()){
                        $this->getTemplate()->set_var("sTipo", "Pregunta Abierta");
                        $iconoPreguntaBlock = "IconoTipoTextoBlock";
                        $this->getTemplate()->set_var("sOpciones", "");
                    }

                    if($oPregunta->isPreguntaMC()){
                        $this->getTemplate()->set_var("sTipo", "Pregunta Multiple Choise");
                        $iconoPreguntaBlock = "IconoTipoMCBlock";
                        $sOpciones = "<strong>Opciones: </strong> ";
                        $aOpciones = $oPregunta->getOpciones();
                        foreach($aOpciones as $oOpcion){
                            $sOpciones .= $oOpcion->getDescripcion().", ";
                        }
                        $sOpciones = substr($sOpciones, 0, -2);
                        $this->getTemplate()->set_var("sOpciones", $sOpciones);
                    }

                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "iconoPregunta", $iconoPreguntaBlock);
                    $this->getTemplate()->set_var("iconoPregunta", $this->getTemplate()->pparse("iconoPregunta"));
                    $this->getTemplate()->delete_parsed_blocks($iconoPreguntaBlock);

                    $this->getTemplate()->parse("PreguntaBlock", true);
            }

            $paramsPaginador[] = "id=".$iEntrevistaId;
            $paramsPaginador[] = "masPreguntas=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/preguntas-procesar", "listadoPreguntasResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("PreguntaBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron resultados");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaPreguntasBlock', false));
    }

    /**
     * Devuelve el html de una nueva fila en la tabla de opciones dentro del formulario de pregunta mc.
     */
    private function agregarOpcion()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        //genero un id para el array del input del form, es solo para el html.
        $sHtmlId = uniqid();

        $this->restartTemplate();
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "ajaxRowOpcion", "OpcionBlock");

        $this->getTemplate()->set_var("opcionHtmlId", $sHtmlId);
        $this->getTemplate()->set_var("iOrden", "0");

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxRowOpcion', false));
    }

    /**
     * Las acciones se dividen asi por el hecho de que puede ser de utilidad activar o desactivar la creacion o la edicion de preguntas
     * de manera independiente.
     */
    public function formCrearPregunta()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        if($this->getRequest()->has('formAbierta')){
            $this->mostrarFormularioPreguntaAbiertaPopUp();
            return;
        }
        if($this->getRequest()->has('formMC')){
            $this->mostrarFormularioPreguntaMCPopUp();
            return;
        }
    }

    public function formEditarPregunta()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iPreguntaId = $this->getRequest()->getPost('iPreguntaId');
        if(empty($iPreguntaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        //me fijo que la pregunta pertenezca a una entrevista creada por el usuario.
        if(!SeguimientosController::getInstance()->isPreguntaUsuario($iPreguntaId)){
            throw new Exception("No tiene permiso para editar la pregunta", 401);
        }

        $oPregunta = SeguimientosController::getInstance()->getPreguntaById($iPreguntaId);

        if($oPregunta->isPreguntaAbierta()){
            $this->mostrarFormularioPreguntaAbiertaPopUp($oPregunta);
            return;
        }

        if($oPregunta->isPreguntaMC()){
            $this->mostrarFormularioPreguntaMCPopUp($oPregunta);
            return;
        }
    }

    private function mostrarFormularioPreguntaAbiertaPopUp($oPreguntaAbierta = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "popUpContent", "FormularioPreguntaAbiertaBlock");

        //FORMULARIO CREAR
        if($oPreguntaAbierta === null){

            $this->getTemplate()->unset_blocks("SubmitModificarPreguntaAbiertaBlock");

            $sTituloForm = "Agregar nueva pregunta abierta a la Entrevista";

            //valores por defecto en el agregar
            $iPreguntaIdForm = "";
            $sDescripcion = "";
            $iOrden = "";

        //FORMULARIO EDITAR
        }else{

            $sTituloForm = "Editar pregunta abierta";

            $this->getTemplate()->unset_blocks("SubmitCrearPreguntaAbiertaBlock");
            $this->getTemplate()->set_var("iPreguntaIdForm", $oPreguntaAbierta->getId());

            $sDescripcion = $oPreguntaAbierta->getDescripcion();
            $iOrden = $oPreguntaAbierta->getOrden();
        }

        $iEntrevistaId = $this->getRequest()->getPost('entrevistaId');
        $this->getTemplate()->set_var("iEntrevistaIdForm", $iEntrevistaId);

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function mostrarFormularioPreguntaMCPopUp($oPreguntaMC = null)
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "popUpContent", "FormularioPreguntaMCBlock");

        //FORMULARIO CREAR
        if($oPreguntaMC === null){

            $this->getTemplate()->unset_blocks("SubmitModificarPreguntaMCBlock");
            $this->getTemplate()->unset_blocks("OpcionBlock");

            $sTituloForm = "Agregar nueva pregunta multiple choise a la Entrevista";

            //valores por defecto en el agregar
            $iPreguntaIdForm = "";
            $sDescripcion = "";
            $iOrden = "";

        //FORMULARIO EDITAR
        }else{

            $sTituloForm = "Editar pregunta multiple choise";

            $this->getTemplate()->unset_blocks("SubmitCrearPreguntaMCBlock");
            $this->getTemplate()->unset_blocks("NoRecordsOpcionesBlock");

            $this->getTemplate()->set_var("iPreguntaIdForm", $oPreguntaMC->getId());

            $sDescripcion = $oPreguntaMC->getDescripcion();
            $iOrden = $oPreguntaMC->getOrden();

            foreach($oPreguntaMC->getOpciones() as $oOpcion){

                $sHtmlId = uniqid();
                $this->getTemplate()->set_var("opcionHtmlId", $sHtmlId);
                $this->getTemplate()->set_var("iOpcionId", $oOpcion->getId());
                $this->getTemplate()->set_var("iOrden", $oOpcion->getOrden());
                $this->getTemplate()->set_var("sDescripcion", $oOpcion->getDescripcion());

                $this->getTemplate()->parse("OpcionBlock", true);
            }
        }

        $iEntrevistaId = $this->getRequest()->getPost('entrevistaId');
        $this->getTemplate()->set_var("iEntrevistaIdForm", $iEntrevistaId);

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    public function guardar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('crearPreguntaAbierta')){
            $this->crearPreguntaAbierta();
            return;
        }

        if($this->getRequest()->has('modificarPreguntaAbierta')){
            $this->modificarPreguntaAbierta();
            return;
        }

        if($this->getRequest()->has('crearPreguntaMC')){
            $this->crearPreguntaMC();
            return;
        }

        if($this->getRequest()->has('modificarPreguntaMC')){
            $this->modificarPreguntaMC();
            return;
        }
    }

    private function crearPreguntaAbierta()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEntrevistaId = $this->getRequest()->getPost('entrevistaIdForm');

            if(!SeguimientosController::getInstance()->isEntrevistaUsuario($iEntrevistaId)){
                throw new Exception("No tiene permiso para editar la entrevista", 401);
            }

            $oPreguntaAbierta = new stdClass();
            $oPreguntaAbierta = Factory::getPreguntaAbiertaInstance($oPreguntaAbierta);

            $oPreguntaAbierta->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oPreguntaAbierta->setOrden($this->getRequest()->getPost("orden"));

            SeguimientosController::getInstance()->guardarPregunta($oPreguntaAbierta, $iEntrevistaId);

            $this->getJsonHelper()->setValor("agregarPregunta", "1");
            $this->getJsonHelper()->setMessage("La pregunta se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarPreguntaAbierta()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iPreguntaId = $this->getRequest()->getPost('preguntaIdForm');

            if(!SeguimientosController::getInstance()->isPreguntaUsuario($iPreguntaId)){
                throw new Exception("No tiene permiso para editar la pregunta", 401);
            }

            $oPregunta = SeguimientosController::getInstance()->getPreguntaById($iPreguntaId);
            $oPregunta->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oPregunta->setOrden($this->getRequest()->getPost("orden"));

            SeguimientosController::getInstance()->guardarPregunta($oPregunta);

            $this->getJsonHelper()->setMessage("La pregunta se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarPregunta", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function crearPreguntaMC()
    {
        $iEntrevistaId = $this->getRequest()->getPost('entrevistaIdForm');
        if(!SeguimientosController::getInstance()->isEntrevistaUsuario($iEntrevistaId)){
            throw new Exception("No tiene permiso para editar la entrevista", 401);
        }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $vOpcion = $this->getRequest()->getPost("opcion");
            if( empty($vOpcion) || !is_array($vOpcion) || count($vOpcion) < 2 ){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Deben guardarse al menos 2 opciones para la pregunta");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oPreguntaMC = new stdClass();
            $oPreguntaMC = Factory::getPreguntaMCInstance($oPreguntaMC);

            $oPreguntaMC->setOrden($this->getRequest()->getPost("orden"));
            $oPreguntaMC->setDescripcion($this->getRequest()->getPost("descripcion"));

            //listado opciones
            $aOpciones = array();
            $aOpcionesAux = array(); //lo uso para asegurarme de que no haya dos opciones con la misma descripcion
            foreach($vOpcion as $opcion){

                $sDescripcion = trim($opcion['descripcion']);
                if(empty($sDescripcion)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("Ninguna descripción puede quedar vacia");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                $iOpcionId = (empty($opcion['opcionId'])) ? null : $opcion['opcionId'];
                $iOrden = (empty($opcion['orden'])) ? 0 : $opcion['orden'];

            	$oOpcion = new stdClass();
            	$oOpcion->iId = $iOpcionId;
            	$oOpcion->sDescripcion = $sDescripcion;
                $oOpcion->iOrden = $iOrden;

                $aOpcionesAux[] = $sDescripcion;
            	$aOpciones[] = Factory::getOpcionInstance($oOpcion);
            }

            //hubo al menos una repeticion en el array.
            if(count($aOpcionesAux) != count(array_unique($aOpcionesAux))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("No puede haber 2 opciones con la misma descripción");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oPreguntaMC->setOpciones($aOpciones);

            SeguimientosController::getInstance()->guardarPregunta($oPreguntaMC, $iEntrevistaId);

            $this->getJsonHelper()->setValor("agregarPregunta", "1");
            $this->getJsonHelper()->setMessage("La pregunta se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarPreguntaMC()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iPreguntaId = $this->getRequest()->getPost('preguntaIdForm');

            if(!SeguimientosController::getInstance()->isPreguntaUsuario($iPreguntaId)){
                throw new Exception("No tiene permiso para editar la pregunta", 401);
            }

            $iEntrevistaId = $this->getRequest()->getPost('entrevistaIdForm');
            $oPreguntaMC = SeguimientosController::getInstance()->getPreguntaById($iPreguntaId);

            $oPreguntaMC->setOrden($this->getRequest()->getPost("orden"));
            $oPreguntaMC->setDescripcion($this->getRequest()->getPost("descripcion"));

            $vOpcion = $this->getRequest()->getPost("opcion");
            if( empty($vOpcion) || !is_array($vOpcion) || count($vOpcion) < 2 ){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Deben guardarse al menos 2 opciones");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //listado opciones
            $aOpciones = array();
            $aOpcionesAux = array();
            foreach($vOpcion as $opcion){

                $sDescripcion = trim($opcion['descripcion']);
                if(empty($sDescripcion)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("Ninguna descripción puede quedar vacía");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                $iOpcionId = (empty($opcion['opcionId'])) ? null : $opcion['opcionId'];
                $iOrden = (empty($opcion['orden'])) ? 0 : $opcion['orden'];

            	$oOpcion = new stdClass();
            	$oOpcion->iId = $iOpcionId;
            	$oOpcion->sDescripcion = $sDescripcion;
                $oOpcion->iOrden = $iOrden;

                $aOpcionesAux[] = $sDescripcion;
            	$aOpciones[] = Factory::getOpcionInstance($oOpcion);
            }
            $oPreguntaMC->setOpciones($aOpciones);

            //hubo al menos una repeticion en el array.
            if(count($aOpcionesAux) != count(array_unique($aOpcionesAux))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("No puede haber 2 opciones con la misma descripción");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            SeguimientosController::getInstance()->guardarPregunta($oPreguntaMC);

            //genero el html de la grilla de las opciones con el id actualizado.
            $this->restartTemplate();
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/preguntas.gui.html", "ajaxGrillaOpciones", "GrillaOpcionesBlock");
            $this->getTemplate()->set_var("NoRecordsOpcionesBlock", "");

            foreach($oPreguntaMC->getOpciones() as $oOpciones){
                $sHtmlId = uniqid();
                $this->getTemplate()->set_var("opcionHtmlId", $sHtmlId);
                $this->getTemplate()->set_var("iOpcionId", $oOpciones->getId());
                $this->getTemplate()->set_var("iOrden", $oOpciones->getOrden());
                $this->getTemplate()->set_var("sDescripcion", $oOpciones->getDescripcion());
                $this->getTemplate()->parse("OpcionBlock", true);
            }

            $this->getJsonHelper()->setMessage("La pregunta se ha modificado con éxito");
            $this->getJsonHelper()->setValor("modificarPregunta", "1");
            $this->getJsonHelper()->setValor("grillaOpciones", $this->getTemplate()->pparse('ajaxGrillaOpciones', false));
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function eliminarOpcion()
    {
        $iOpcionId = $this->getRequest()->getParam('iOpcionId');

        if(empty($iOpcionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $bOpcionUsuario = SeguimientosController::getInstance()->isOpcionPreguntaUsuario($iOpcionId);
        if(!$bOpcionUsuario){
            throw new Exception("No tiene permiso para eliminar esta opción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            SeguimientosController::getInstance()->borrarOpcionPregunta($iOpcionId);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function eliminar()
    {
        $iPreguntaId = $this->getRequest()->getPost('iPreguntaId');

        if(empty($iPreguntaId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        if(!SeguimientosController::getInstance()->isPreguntaUsuario($iPreguntaId)){
            throw new Exception("No tiene permiso para borrar la pregunta", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oPregunta = SeguimientosController::getInstance()->getPreguntaById($iPreguntaId);

            $aPreguntas[] = $oPregunta;
            $result = SeguimientosController::getInstance()->borrarPreguntas($aPreguntas);

            $this->restartTemplate();

            if($result){
                $msg = "La pregunta fue eliminada de la entrevista";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la pregunta de la entrevista";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la pregunta de la entrevista";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}
