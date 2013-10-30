<?php

/**
 * @author Matias Velilla
 *
 */
class SeguimientosControllerSeguimientos extends PageControllerAbstract
{
    const TIPO_SEGUIMIENTO_SCC = "SeguimientoSCC";
    const TIPO_SEGUIMIENTO_PERSONALIZADO = "SeguimientoPersonalizado";

    const TIPO_SEGUIMIENTO_SCC_DESC = "Seguimiento por competencia curricular";
    const TIPO_SEGUIMIENTO_PERSONALIZADO_DESC = "Seguimiento personalizado";
    
    private $orderByConfig = array('persona' => array('variableTemplate' => 'orderByPersona',
                                                      'orderBy' => 'p.nombre',
                                                      'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipo',
                                                   'orderBy' => 'tipo',
                                                   'order' => 'desc'),
                                   'fecha' => array('variableTemplate' => 'orderByFecha',
                                                    'orderBy' => 's.fechaCreacion',
                                                    'order' => 'desc'));

    private $filtrosFormConfig = array('filtroEstado' => 's.estado',
                                       'filtroApellidoPersona' => 'p.apellido',
                                       'filtroDni' => 'p.numeroDocumento',
                                       'filtroTipoSeguimiento' => 'tipo',
                                       'filtroFechaDesde' => 'fechaDesde',
                                       'filtroFechaHasta' => 'fechaHasta');

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

        return $this;
    }
    
    private function setJSSeguimientos(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "jsContent", "JsContent");
        return $this;    	
    }
    private function setJSAntecedentes(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "jsContent", "JsContent");
        return $this;
    }
    private function setJSDiagnostico(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "jsContent", "JsContent");
        return $this;
    }
    private function setJSObjetivos(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "jsContent", "JsContent");
        return $this;
    }
    private function setJSPronostico(){
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/pronostico.gui.html", "jsContent", "JsContent");
        return $this;
    }
    
    private function setMenuDerechaHome()
    {
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContHomeBlock");

        $this->getTemplate()->set_var("hrefCrearSeguimientos", $this->getUrlFromRoute("seguimientosSeguimientosNuevoSeguimiento", true));
        $this->getTemplate()->set_var("hrefAgregarPersona", $this->getUrlFromRoute("seguimientosPersonasAgregar", true));
        $this->getTemplate()->set_var("hrefListadoSeguimientos", $this->getUrlFromRoute("seguimientosIndexIndex", true));

        $this->getTemplate()->set_var("hrefListadoUnidadesVariables", $this->getUrlFromRoute("seguimientosUnidadesIndex", true));
        $this->getTemplate()->set_var("hrefListadoPlantillasEntrevistas", "");

        return $this;
    }

    /**
     * @param array $aCurrentOption es un array que tiene que tener el nombre de las variables
     * seguin seguimientos.gui.html para marcar la opcion activa en el menu.
     */
    static function setMenuDerechaVerSeguimiento(Templates $oTemplate, PageControllerAbstract $oPageController, $aCurrentOption = null)
    {
        $oTemplate->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerCont", "PageRightInnerContVerSeguimientoBlock");

        $oTemplate->set_var("hrefListadoSeguimientos", $oPageController->getUrlFromRoute("seguimientosIndexIndex", true));

        $oTemplate->set_var("hrefVerSeguimiento", $oPageController->getUrlFromRoute("seguimientosSeguimientosVer", true));
        $oTemplate->set_var("hrefEditarAntecedentesSeguimiento", $oPageController->getUrlFromRoute("seguimientosSeguimientosEditarAntecedentes", true));
        $oTemplate->set_var("hrefEditarDiagnosticoSeguimiento", $oPageController->getUrlFromRoute("seguimientosEditarDiagnostico", true));
        $oTemplate->set_var("hrefVerAdjuntosSeguimiento", $oPageController->getUrlFromRoute("seguimientosSeguimientosAdjuntos", true));
        $oTemplate->set_var("hrefAdministrarObjetivosSeguimiento", $oPageController->getUrlFromRoute("seguimientosSeguimientosAdministrarObjetivos", true));
        $oTemplate->set_var("hrefEditarPronosticoSeguimiento", $oPageController->getUrlFromRoute("seguimientosEditarPronostico", true));
        $oTemplate->set_var("hrefAsociarUnidadesSeguimiento", $oPageController->getUrlFromRoute("seguimientosUnidadesListarUnidadesPorSeguimiento", true));

        //marco los selecteds en el menu de la izq
        if(is_array($aCurrentOption)){
            foreach($aCurrentOption as $sCurrentOption)
            {
                $oTemplate->set_var($sCurrentOption, "class='selected'");
            }
        }
    }

    static function setFichaPersonaSeguimiento(Templates $template, UploadHelper $oUploadHelper, $oDiscapacitado)
    {
        $template->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "fichaPersona", "PageRightInnerContFichaPersonaBlock");

        $template->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $template->set_var("iPersonaId", $oDiscapacitado->getId());
        $template->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());

        //foto de perfil actual
        $oUploadHelper->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorSmallSize = $oUploadHelper->getDirectorioUploadFotos().$oFoto->getNombreSmallSize();
            $pathFotoServidorBigSize = $oUploadHelper->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorSmallSize= $oUploadHelper->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar();
            $pathFotoServidorBigSize = $oUploadHelper->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $template->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);
        $template->set_var("scrFotoPerfilActual", $pathFotoServidorSmallSize);
    }
    
    public function index(){
        $this->listar();
    }

    public function listar()
    {
        try{            
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaHome();
                 
            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Seguimientos");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "ListadoSeguimientosBlock");

            //select form filtro                       
            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            foreach ($aTiposSeguimientos as $value => $descripcion){
                $this->getTemplate()->set_var("sSeguimientoTipoValue", $value);
                $this->getTemplate()->set_var("sSeguimientoTipoNombre", $descripcion);
                $this->getTemplate()->parse("OptionTipoSeguimientoBlock", true);
            }

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            
            $filtro["u.id"] = $perfil->getUsuario()->getId();
            $aSeguimientos = SeguimientosController::getInstance()->buscarSeguimientos($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);
            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aSeguimientos) > 0){

                $this->getTemplate()->set_var("NoRecordsGrillaSeguimientosBlock", "");
                
            	foreach ($aSeguimientos as $oSeguimiento){

                    $sEstadoSeguimiento = $oSeguimiento->getEstado();
                    $hrefAmpliarSeguimiento = $this->getUrlFromRoute("seguimientosSeguimientosVer", true);

                    $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
                    $this->getTemplate()->set_var("sSeguimientoPersona", $oSeguimiento->getDiscapacitado()->getNombreCompleto());
                    $this->getTemplate()->set_var("iPersonaId", $oSeguimiento->getDiscapacitado()->getId());
                    
                    $this->getTemplate()->set_var("sSeguimientoTipo", $aTiposSeguimientos[get_class($oSeguimiento)]);
                    $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oSeguimiento->getDiscapacitado()->getNumeroDocumento());
                    $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

                    $this->getTemplate()->set_var("sEstadoSeguimiento", "Activo");
                    if($sEstadoSeguimiento == "activo"){
                        $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                    }
                    $this->getTemplate()->parse("EstadoSeguimientoBlock",false);

                    $this->getTemplate()->set_var("sEstadoSeguimiento", "Detenido");
                    if($sEstadoSeguimiento == "detenido"){
                        $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "selected='selected'");
                    }else{
                        $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "");
                    }
                    $this->getTemplate()->parse("EstadoSeguimientoBlock",true);

                    if($sEstadoSeguimiento == "activo"){
                        $this->getTemplate()->set_var("sEstadoClass", "");
                    }else{
                        $this->getTemplate()->set_var("sEstadoClass", "disabled");
                    }

                    $this->getTemplate()->set_var("hrefAmpliarSeguimiento", $hrefAmpliarSeguimiento);
                                                            
                    $srcAvatarPersona = $this->getUploadHelper()->getDirectorioUploadFotos().$oSeguimiento->getDiscapacitado()->getNombreAvatar();
                    $this->getTemplate()->set_var("scrAvatarPersona", $srcAvatarPersona);

                    $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
                    $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
                    $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

                    //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                    list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
                    $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                    $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
                    $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

                    $sDuracionEstimada = SeguimientosController::getInstance()->obtenerDuracionEstimadaSeguimiento($oSeguimiento);
                    if($sDuracionEstimada === null){
                        $sDuracionEstimada = "Ningún objetivo se encuentra activo y sin alcanzar.";
                    }
                    $this->getTemplate()->set_var("sDuracionEstimada", $sDuracionEstimada);

                    $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
                    if($oUltimaEntrada !== null){
                        $this->getTemplate()->set_var("sUltimaModificacion", $oUltimaEntrada->getFecha(true));
                    }else{
                        $this->getTemplate()->set_var("sUltimaModificacion", "El Seguimiento no posee entradas por fecha");
                    }
                    

                    $this->getTemplate()->parse("SeguimientoBlock", true);
            	}

                $params = array();
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/buscar-seguimientos", "listadoSeguimientosResult", $params);
            }else{
                $this->getTemplate()->set_var("SeguimientoBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "Todavia no hay seguimientos creados.");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

         }catch(Exception $e){
            print_r($e);
        } 
    }       
    
    public function buscarSeguimientos()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);
        
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "ajaxGrillaSeguimientosBlock", "GrillaSeguimientosBlock");
                
        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

        $iRecordsTotal = 0;
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $filtroSql["u.id"] = $perfil->getUsuario()->getId();
        $aSeguimientos = SeguimientosController::getInstance()->buscarSeguimientos($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

        if(count($aSeguimientos) > 0){

            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            $this->getTemplate()->set_var("NoRecordsGrillaSeguimientosBlock", "");

            foreach ($aSeguimientos as $oSeguimiento){

                $sEstadoSeguimiento = $oSeguimiento->getEstado();
                $hrefAmpliarSeguimiento = $this->getUrlFromRoute("seguimientosSeguimientosVer", true);

                $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
                $this->getTemplate()->set_var("sSeguimientoPersona", $oSeguimiento->getDiscapacitado()->getNombreCompleto());
                $this->getTemplate()->set_var("iPersonaId", $oSeguimiento->getDiscapacitado()->getId());

                $this->getTemplate()->set_var("sSeguimientoTipo", $aTiposSeguimientos[get_class($oSeguimiento)]);
                $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oSeguimiento->getDiscapacitado()->getNumeroDocumento());
                $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

                $this->getTemplate()->set_var("sEstadoSeguimiento", "Activo");
                if($sEstadoSeguimiento == "activo"){
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento","");
                }
                $this->getTemplate()->parse("EstadoSeguimientoBlock",false);

                $this->getTemplate()->set_var("sEstadoSeguimiento", "Detenido");
                if($sEstadoSeguimiento == "detenido"){
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sSelectedEstadoSeguimiento", "");
                }
                $this->getTemplate()->parse("EstadoSeguimientoBlock",true);

                if($sEstadoSeguimiento == "activo"){
                    $this->getTemplate()->set_var("sEstadoClass", "");
                }else{
                    $this->getTemplate()->set_var("sEstadoClass", "disabled");
                }

                $this->getTemplate()->set_var("hrefAmpliarSeguimiento", $hrefAmpliarSeguimiento);

                $srcAvatarPersona = $this->getUploadHelper()->getDirectorioUploadFotos().$oSeguimiento->getDiscapacitado()->getNombreAvatar();
                $this->getTemplate()->set_var("scrAvatarPersona", $srcAvatarPersona);

                $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
                $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
                $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

                //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
                list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
                $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
                $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
                $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

                $sDuracionEstimada = SeguimientosController::getInstance()->obtenerDuracionEstimadaSeguimiento($oSeguimiento);
                if($sDuracionEstimada === null){
                    $sDuracionEstimada = "Ningún objetivo se encuentra activo y sin alcanzar.";
                }
                $this->getTemplate()->set_var("sDuracionEstimada", $sDuracionEstimada);

                $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
                if($oUltimaEntrada !== null){
                    $this->getTemplate()->set_var("sUltimaModificacion", $oUltimaEntrada->getFecha(true));
                }else{
                    $this->getTemplate()->set_var("sUltimaModificacion", "El Seguimiento no posee entradas por fecha");
                }

                $this->getTemplate()->parse("SeguimientoBlock", true);                               
            }

            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "seguimientos/buscar-seguimientos", "listadoSeguimientosResult", $paramsPaginador);

        }else{
            $this->getTemplate()->set_var("SeguimientoBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No hay seguimientos para el filtro actual.");
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('ajaxGrillaSeguimientosBlock', false));
    }

    /**
     * Form Nuevo Seguimiento
     */
    public function nuevoSeguimiento(){        
        try{
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag()
                 ->setMenuDerechaHome();

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Mis Seguimientos");
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "FormularioCrearSeguimientoBlock");

            $aTiposSeguimientos = SeguimientosController::getInstance()->obtenerTiposSeguimiento();
            $this->getTemplate()->set_var("sSeguimientoSCCValue", "SeguimientoSCC");
            $this->getTemplate()->set_var("sSeguimientoSCCNombre", $aTiposSeguimientos['SeguimientoSCC']);
            $this->getTemplate()->set_var("sSeguimientoPersonalizadoValue", "SeguimientoPersonalizado");
            $this->getTemplate()->set_var("sSeguimientoPersonalizadoNombre", $aTiposSeguimientos['SeguimientoPersonalizado']);
            
            $aPracticas = SeguimientosController::getInstance()->obtenerPracticas();
            foreach($aPracticas as $oPractica){
                $this->getTemplate()->set_var("iPracticaId", $oPractica->getId());
                $this->getTemplate()->set_var("sPracticaNombre", $oPractica->getNombre());
                $this->getTemplate()->parse("OptionPracticaBlock", true);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
            
         }catch(Exception $e){
            print_r($e);
        }
    }

    /**
     * Guardar Seguimiento Nuevo
     *
     * El modificar tiene que ser metodo aparte porque no podes cambiar el tipo una vez creado.
     */
    public function procesarSeguimiento(){

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $sTipoSeguimiento = $this->getRequest()->getPost('tipoSeguimiento');
            $iPersonaId = $this->getRequest()->getPost('personaId');
            $sFrecuencias   = $this->getRequest()->getPost('frecuencias');
            $sDiaHorario    = $this->getRequest()->getPost('diaHorario');

            $filtro = array("s.discapacitados_id" => $iPersonaId);

            $iRecordsTotal = 0;
            $aSeguimientos = SeguimientosController::getInstance()->obtenerSeguimientos($filtro, $iRecordsTotal, null, null, null, null);
            if(count($aSeguimientos) > 2){
                $this->getJsonHelper()->setSuccess(false)->setMessage("La persona a la que quiere hacer un seguimiento ya posee 2. No se puede agregar mas de 2 seguimientos a una persona.");
                $this->getJsonHelper()->sendJsonAjaxResponse(); 
                return;
            }

            $iPracticaId  = $this->getRequest()->getPost('practica');
            $oPractica = SeguimientosController::getInstance()->getPracticaById($iPracticaId);

            $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($iPersonaId);

            $oSeguimiento = new stdClass();
            $oSeguimiento->oPractica = $oPractica;
            $oSeguimiento->sFrecuenciaEncuentros = $sFrecuencias;
            $oSeguimiento->sDiaHorario = $sDiaHorario;
            $oSeguimiento->oDiscapacitado = $oDiscapacitado;
            $oSeguimiento->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
                        
            switch($sTipoSeguimiento)
            {
                case self::TIPO_SEGUIMIENTO_SCC:
                    $oSeguimiento = Factory::getSeguimientoSCCInstance($oSeguimiento);
                    break;
                case self::TIPO_SEGUIMIENTO_PERSONALIZADO:
                    $oSeguimiento = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                    break;
            }            
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            if($resultado){
                $this->getJsonHelper()->setMessage("El seguimiento fue creado con exito");
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
            
        }catch(Exception $e){
           $this->getJsonHelper()->setMessage("Ocurrio un error, no se pudo crear el seguimiento");
           $this->getJsonHelper()->setSuccess(false);
        }
        
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Procesar funcionalidades, checks, ajax, etc.
     */
    public function procesar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('checkEntradasOK')){
            $this->checkEntradasOK();
            return;
        }
    }

    /**
     * Si esta habilitado para entradas devuelve success,
     * sino devuelve falso y html para mostrar en dialog.
     */
    private function checkEntradasOK()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento === null || $oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar este seguimiento", 401);
            }

            //tiene al menos un objetivo, antecedentes y diagnostico seteado
            if(SeguimientosController::getInstance()->checkEntradasOK($oSeguimiento)){
                $this->getJsonHelper()->setSuccess(true);
                $redirect = $this->getUrlFromRoute("seguimientosEntradasIndex")."?iSeguimientoId=".$iSeguimientoId;
                $this->getJsonHelper()->setRedirect($redirect);                
            }else{
                $this->getJsonHelper()->setSuccess(false);                
                
                $tituloMensajeError = "Aún no se pueden cargar entradas";
                $ficha = "MsgFichaInfoBlock";
                $mensajeInfoError = "Para poder ingresar información por fecha en el seguimiento primero deben cargarse los Antecedentes, el Diagnóstico y al menos un Objetivo.";

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "mensajeCheck", $ficha);
                $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
                $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);

                //Links
                $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "itemExtraMsgFicha", "MenuVertical02Block");
                $this->getTemplate()->set_var("idOpcion", 'opt1');
                $this->getTemplate()->set_var("hrefOpcion", $this->getUrlFromRoute("seguimientosSeguimientosEditarAntecedentes", true)."?iSeguimientoId=".$iSeguimientoId);
                $this->getTemplate()->set_var("sNombreOpcion", "Editar Antecedentes");
                $this->getTemplate()->parse("OpcionesMenu", true);

                $this->getTemplate()->set_var("idOpcion", 'opt2');
                $this->getTemplate()->set_var("hrefOpcion", $this->getUrlFromRoute("seguimientosEditarDiagnostico", true)."?iSeguimientoId=".$iSeguimientoId);
                $this->getTemplate()->set_var("sNombreOpcion", "Editar Diagnóstico");
                $this->getTemplate()->parse("OpcionesMenu", true);

                $this->getTemplate()->set_var("idOpcion", 'opt3');
                $this->getTemplate()->set_var("hrefOpcion", $this->getUrlFromRoute("seguimientosSeguimientosAdministrarObjetivos", true)."?iSeguimientoId=".$iSeguimientoId);
                $this->getTemplate()->set_var("sNombreOpcion", "Asociar Objetivos");
                $this->getTemplate()->parse("OpcionMenuLastOpt");

                $sHtml = $this->getTemplate()->pparse('mensajeCheck', false);

                $this->getJsonHelper()->setValor('html', $sHtml);
            }
            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){            
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
        }
    }

    public function formModificarSeguimiento()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "popUpContent", "FormularioModificarSeguimientoBlock");

        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);

        $iPracticaId = $oSeguimiento->getPractica()->getId();
        $sFrecuenciaEncuentros = $oSeguimiento->getFrecuenciaEncuentros();
        $sDiaHorario = $oSeguimiento->getDiaHorario();
        
        $aPracticas = SeguimientosController::getInstance()->obtenerPracticas();
        foreach($aPracticas as $oPractica){
            $this->getTemplate()->set_var("iPracticaId", $oPractica->getId());
            $this->getTemplate()->set_var("sPracticaNombre", $oPractica->getNombre());
            if($oPractica->getId() == $iPracticaId){
                $this->getTemplate()->set_var("sPracticaSelected", "selected='selected'");
            }            
            $this->getTemplate()->parse("OptionPracticaBlock", true);
            $this->getTemplate()->set_var("sPracticaSelected", "");
        }
        
        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $sFrecuenciaEncuentros);
        $this->getTemplate()->set_var("sDiaHorario", $sDiaHorario);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));      
    }

    public function guardarSeguimiento()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoIdForm');
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar este seguimiento", 401);
            }

            $iPracticaId  = $this->getRequest()->getPost('practica');
            $oPractica = SeguimientosController::getInstance()->getPracticaById($iPracticaId);

            $oSeguimiento->setPractica($oPractica);
            $oSeguimiento->setDiaHorario($this->getRequest()->getPost("diaHorario"));
            $oSeguimiento->setFrecuenciaEncuentros($this->getRequest()->getPost("frecuencias"));

            SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            
            $this->getJsonHelper()->setMessage("El seguimiento se ha modificado con éxito. Los cambios se veran cuando refresque la pagina.");
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function cambiarEstadoSeguimientos()
    {        
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        $iIdSeguimiento = $this->getRequest()->getPost('iSeguimientoId');
        $sEstadoSeguimiento = $this->getRequest()->getPost('estadoSeguimiento');

        if(empty($iIdSeguimiento) || empty($sEstadoSeguimiento)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
     
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar este seguimiento", 401);
            }

            switch($sEstadoSeguimiento)
            {
                case "Activo":
                    $oSeguimiento->setEstadoActivo();
                    break;
                case "Detenido":
                    $oSeguimiento->setEstadoDetenido();
                    break;
            }
                        
            SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    public function editarAntecedentes()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}
        
        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionEditarAntecedentesSeguimiento";

            $this->setFrameTemplate()
                 ->setJSAntecedentes()
                 ->setHeadTag();

            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            //para que pueda ser reutilizado en otras vistas
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();
            
            //titulo seccion
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            }else{
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            }
            $this->getTemplate()->set_var("subtituloSeccion", "antecedentes");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "pageRightInnerMainCont", "FormularioBlock");

            //$this href para hashtags de los tabs
            $this->getTemplate()->set_var("thisHref", $this->getRequest()->getRequestUri());
                      
            //form para ingresar uno nuevo
            $this->getUploadHelper()->setTiposValidosDocumentos();
            $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
            
            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);
            
            $this->getTemplate()->set_var("sAntecedentes", $oSeguimiento->getAntecedentes());

            //si ya tiene un archivo de antecedentes que aparezca.
            if(null !== $oSeguimiento->getArchivoAntecedentes()){
                $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

                $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
                $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
                $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
                $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

                $this->getTemplate()->set_var("hrefDescargarAntActual", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());

                $this->getTemplate()->parse("ArchivoAntecedentesActualBlock");
            }else{
                $this->getTemplate()->unset_blocks("ArchivoAntecedentesActualBlock");
            }
            
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            $this->getResponse()->setBody("Ocurrio un error");
        }
    }
    
    public function procesarAntecedentes(){

    	if($this->getRequest()->has('fileAntecedentesUpload')){
            $this->fileAntecedentesUpload();
            return;
        }

        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
                
    	if($this->getRequest()->has('formAntecedentes')){
            $this->guardarFormAntecedentes();
            return;
        }
    }
    
    private function fileAntecedentesUpload()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $idItem = $perfil->getUsuario()->getId();

        $iIdSeguimiento = $this->getRequest()->getPost('iSeguimientoId');
        $nombreInputFile = 'archivoAntecedentes';

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iIdSeguimiento);

        if($oSeguimiento->getUsuarioId() != $idItem){
            throw new Exception("No tiene permiso para editar este seguimiento", 401);
        }
        
        try{            
            $this->getJsonHelper()->initJsonAjaxResponse();
            			
            $this->getUploadHelper()->setTiposValidosDocumentos();
            
            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

            	$pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            	list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, "antecedentes", $nombreInputFile);
                $resultado = SeguimientosController::getInstance()->guardarAntecedentesFile($oSeguimiento, $nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor);
                $oArchivo = $oSeguimiento->getArchivoAntecedentes();

                $this->getTemplate()->load_file_section("gui/vistas/seguimientos/antecedentes.gui.html", "ajaxAntecedentesActual", "ArchivoAntecedentesActualBlock");
                $link = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                $this->getTemplate()->set_var("sNombreArchivo", $oArchivo->getNombre());
                $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                $this->getTemplate()->set_var("sFechaArchivo", $oArchivo->getFechaAlta());
                $this->getTemplate()->set_var("hrefDescargarAntActual", $link);
                $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxAntecedentesActual', false);
            }
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
			
        }catch(Exception $e){
            $respuesta = "0;; Error al guardar en base de datos";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }
    }
    
    private function guardarFormAntecedentes()
    {
        $iSeguimientoId = $this->getRequest()->getParam('idSeguimiento');
        
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}
        
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);
        
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para editar este seguimiento", 401);
            }
                        
            $sAntecedentes = $this->getRequest()->getPost('antecedentes');
           
            $oSeguimiento->setAntecedentes($sAntecedentes);
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);
            			
            if($resultado){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }
            
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }
               
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function eliminar()
    {
    	$iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
        
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

    	$this->getJsonHelper()->initJsonAjaxResponse();
    	try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar este seguimiento", 401);
            }

            $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

            $pathServidorFotos = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $pathServidorArchivos = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

            $result = SeguimientosController::getInstance()->eliminarSeguimiento($oSeguimiento, $pathServidorFotos, $pathServidorArchivos);

            $this->restartTemplate();

            if($result){
                $msg = "El seguimiento fue eliminado con exito";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha podido eliminar el seguimiento";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }
    
    	}catch(Exception $e){            
            $msg = "Ocurrio un error, no se ha podido eliminar el seguimiento";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
    	}
    
    	$this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
    	$this->getTemplate()->set_var("sMensaje", $msg);
    	$this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));
    
    	$this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * Metodo para la vista ampliada de un seguimiento
     */
    public function ver()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
       
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
        
        try{
            $aCurrentOptions[] = "currentOptionVerSeguimiento";
            
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag();

            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->verSeguimientoPersonalizado($oSeguimiento);
            }else{
                $this->verSeguimientoSCC($oSeguimiento);
            }
    	}catch(Exception $e){
            throw new Exception($e->getMessage());
    	}
    }

    private function verSeguimientoSCC($oSeguimiento)
    {
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
        $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "VerSeguimientoBlock");

        $oDiscapacitado = $oSeguimiento->getDiscapacitado();
       
        $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
        $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $this->getTemplate()->set_var("iPersonaId", $oDiscapacitado->getId());

        $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());
        $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorMediumSize=$pathFotoServidorBigSize=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada",$pathFotoServidorBigSize);
        $this->getTemplate()->set_var("scrFotoPerfilActual",$pathFotoServidorMediumSize);
        
        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
        $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
        $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

        //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
        $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
        $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
        $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

        $sDuracionEstimada = SeguimientosController::getInstance()->obtenerDuracionEstimadaSeguimiento($oSeguimiento);
        if($sDuracionEstimada === null){
            $sDuracionEstimada = "Ningún objetivo se encuentra activo y sin alcanzar.";
        }
        $this->getTemplate()->set_var("sDuracionEstimada", $sDuracionEstimada);

        $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
        if($oUltimaEntrada !== null){
            $this->getTemplate()->set_var("sUltimaModificacion", $oUltimaEntrada->getFecha(true));
        }else{
            $this->getTemplate()->set_var("sUltimaModificacion", "El Seguimiento no posee entradas por fecha");
        }

        //antecedentes
        $sAntecedentes = $oSeguimiento->getAntecedentes();
        $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

        if(null === $sAntecedentes && $oAntecedentes === null){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("NoInfoAntecedentesBlock", "");
        }

        if(null === $sAntecedentes){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sAntecedentes", $sAntecedentes);
        }

        if(null === $oAntecedentes){
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
            $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
            $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
            $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

            $this->getTemplate()->set_var("hrefDescargar", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function verSeguimientoPersonalizado($oSeguimiento)
    {
        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
        $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/seguimientos.gui.html", "pageRightInnerMainCont", "VerSeguimientoBlock");

        $oDiscapacitado = $oSeguimiento->getDiscapacitado();

        $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
        $this->getTemplate()->set_var("sNombrePersona", $oDiscapacitado->getNombreCompleto());
        $this->getTemplate()->set_var("iPersonaId", $oDiscapacitado->getId());

        $this->getTemplate()->set_var("sSeguimientoPersonaDNI", $oDiscapacitado->getNumeroDocumento());
        $this->getTemplate()->set_var("sSeguimientoFechaCreacion", Utils::fechaFormateada($oSeguimiento->getFechaCreacion()));

        //foto de perfil actual
        $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
        if(null != $oDiscapacitado->getFotoPerfil()){
            $oFoto = $oDiscapacitado->getFotoPerfil();
            $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
            $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
        }else{
            $pathFotoServidorMediumSize=$pathFotoServidorBigSize=$this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar(true);
        }
        $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada",$pathFotoServidorBigSize);
        $this->getTemplate()->set_var("scrFotoPerfilActual",$pathFotoServidorMediumSize);

        $this->getTemplate()->set_var("sFrecuenciaEncuentros", $oSeguimiento->getFrecuenciaEncuentros());
        $this->getTemplate()->set_var("sDiaHorarioEncuentros", $oSeguimiento->getDiaHorario());
        $this->getTemplate()->set_var("sTipoPractica", $oSeguimiento->getPractica()->getNombre());

        //lo hago asi porque sino es re pesado obtener todos los objetos solo para saber cantidad
        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($oSeguimiento->getId());
        $this->getTemplate()->set_var("iCantidadFotos", $cantFotos);
        $this->getTemplate()->set_var("iCantidadVideos", $cantVideos);
        $this->getTemplate()->set_var("iCantidadArchivos", $cantArchivos);

        $sDuracionEstimada = SeguimientosController::getInstance()->obtenerDuracionEstimadaSeguimiento($oSeguimiento);
        if($sDuracionEstimada === null){
            $sDuracionEstimada = "Ningún objetivo se encuentra activo y sin alcanzar.";
        }
        $this->getTemplate()->set_var("sDuracionEstimada", $sDuracionEstimada);

        $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
        if($oUltimaEntrada !== null){
            $this->getTemplate()->set_var("sUltimaModificacion", $oUltimaEntrada->getFecha(true));
        }else{
            $this->getTemplate()->set_var("sUltimaModificacion", "El Seguimiento no posee entradas por fecha");
        }

        //antecedentes
        $sAntecedentes = $oSeguimiento->getAntecedentes();
        $oAntecedentes = $oSeguimiento->getArchivoAntecedentes();

        if(null === $sAntecedentes && $oAntecedentes === null){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("NoInfoAntecedentesBlock", "");
        }

        if(null === $sAntecedentes){
            $this->getTemplate()->set_var("VerAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sAntecedentes", $sAntecedentes);
        }

        if(null === $oAntecedentes){
            $this->getTemplate()->set_var("VerArchivoAntecedentesActualBlock", "");
        }else{
            $this->getTemplate()->set_var("sNombreArchivo", $oAntecedentes->getNombre());
            $this->getTemplate()->set_var("sExtensionArchivo", $oAntecedentes->getTipoMime());
            $this->getTemplate()->set_var("sTamanioArchivo", $oAntecedentes->getTamanio());
            $this->getTemplate()->set_var("sFechaArchivo", $oAntecedentes->getFechaAlta());

            $this->getTemplate()->set_var("hrefDescargar", $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oAntecedentes->getNombreServidor());
        }
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function verAdjuntos()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
        
        try{
            $aCurrentOptions[] = "currentOptionVerAdjuntosSeguimiento";
            
            $this->setFrameTemplate()
                 ->setJSSeguimientos()
                 ->setHeadTag();

            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());
            
            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            }else{
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            }  
            $this->getTemplate()->set_var("subtituloSeccion", "galería adjuntos");
            $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "pageRightInnerMainCont", "GaleriaAdjuntosBlock");
            $this->getTemplate()->set_var("TituloItemBlock", "");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());
            $this->getTemplate()->set_var("iItemIdForm", $oSeguimiento->getId());

            //uso los thumbnails de edicion asi que descarto los otros:
            $this->getTemplate()->set_var("ThumbnailFotoBlock", "");
            $this->getTemplate()->set_var("ThumbnailVideoBlock", "");
            $this->getTemplate()->set_var("RowArchivoBlock", "");
            
            //Fotos
            $aFotos = $oSeguimiento->getFotos();
            if(count($aFotos) > 0){
                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                foreach($aFotos as $oFoto){
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion());
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    $this->getTemplate()->parse("ThumbnailFotoEditBlock", true);
                }
                $this->getTemplate()->set_var("NoRecordsFotosBlock", "");
            }else{
                $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
            }

            //Videos
            $aEmbedVideos = $oSeguimiento->getEmbedVideos();

            if(count($aEmbedVideos) > 0){

                foreach($aEmbedVideos as $oEmbedVideo){

                    $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                    $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                    $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                    $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                    $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                    $this->getTemplate()->parse("ThumbnailVideoEditBlock", true);
                }

                $this->getTemplate()->set_var("NoRecordsVideosBlock", "");
            }else{
                $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
            }

            //Archivos
            $aArchivos = $oSeguimiento->getArchivos();

            if(count($aArchivos) > 0){

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();

                foreach($aArchivos as $oArchivo){

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());

                    $sTitulo = $oArchivo->getTitulo();
                    $sDescripcion = $oArchivo->getDescripcion();
                    if(empty($sTitulo) && empty($sDescripcion)){
                        $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                    }else{
                        if(empty($sTitulo)){
                            $this->getTemplate()->set_var("TituloInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("tituloArchivo", $sTitulo);
                        }

                        if(empty($sDescripcion)){
                            $this->getTemplate()->set_var("DescripcionInfoArchivoBlock", "");
                        }else{
                            $this->getTemplate()->set_var("descripcionArchivo", $sDescripcion);
                        }
                    }

                    $this->getTemplate()->parse("RowArchivoEditBlock", true);
                    $this->getTemplate()->delete_parsed_blocks("InfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("TituloInfoArchivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("DescripcionInfoArchivoBlock");
                }

                $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");
            }else{
                $this->getTemplate()->set_var("RowArchivoEditBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    	}catch(Exception $e){
            throw new Exception($e->getMessage());
    	}
    }

    public function formAdjuntarFoto()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaFotosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("ThumbnailFotoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsFotosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantFotos >= 12){
            $this->getTemplate()->set_var("FormularioCrearFotoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteFotosBlock", "");

            $this->getUploadHelper()->setTiposValidosFotos();
            $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function formAdjuntarVideo()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaVideosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("ThumbnailVideoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsVideosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantVideos >= 12){
            $this->getTemplate()->set_var("FormularioCrearEmbedVideoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteEmbedVideosBlock", "");
            $this->getTemplate()->set_var("sServidoresPermitidos", $this->getEmbedVideoHelper()->getStringServidoresValidos());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }
    
    public function formAdjuntarArchivo()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

        if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
        }

        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "GaleriaArchivosBlock");

        //borro estos bloques porque solo quiero usar el formulario
        $this->getTemplate()->set_var("TituloGaleriaBlock", "");
        $this->getTemplate()->set_var("RowArchivoEditBlock", "");
        $this->getTemplate()->set_var("NoRecordsArchivosBlock", "");

        list($cantFotos, $cantVideos, $cantArchivos) = SeguimientosController::getInstance()->obtenerCantidadMultimediaSeguimiento($iSeguimientoId);
        if($cantArchivos >= 12){
            $this->getTemplate()->set_var("FormularioCrearArchivoBlock", "");
        }else{
            $this->getTemplate()->set_var("MensajeLimiteArchivosBlock", "");

            $this->getUploadHelper()->setTiposValidosDocumentos();
            $this->getTemplate()->set_var("sTiposPermitidosArchivo", $this->getUploadHelper()->getStringTiposValidos());
            $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
            $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        }

        $this->getTemplate()->set_var("iItemIdForm", $iSeguimientoId);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    //un mismo metodo para mostrar los 3 forms porq es simple esta accion
    public function formEditarAdjunto()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('editarFoto')){
            $this->formEditarFoto();
            return;
        }
        if($this->getRequest()->has('editarArchivo')){
            $this->formEditarArchivo();
            return;
        }
        if($this->getRequest()->has('editarVideo')){
            $this->formEditarVideo();
            return;
        }
    }

    private function formEditarFoto()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioFotoBlock");

        $iFotoId = $this->getRequest()->getParam('iFotoId');
        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

        $this->getTemplate()->set_var("iFotoId", $iFotoId);

        $sTitulo = $oFoto->getTitulo();
        $sDescripcion = $oFoto->getDescripcion();
        $iOrden = $oFoto->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    private function formEditarArchivo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioArchivoBlock");

        $iArchivoId = $this->getRequest()->getParam('iArchivoId');
        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

        $this->getTemplate()->set_var("iArchivoId", $iArchivoId);

        $sTitulo = $oArchivo->getTitulo();
        $sDescripcion = $oArchivo->getDescripcion();
        $iOrden = $oArchivo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    private function formEditarVideo()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "popUpContent", "FormularioVideoBlock");

        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');
        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

        $this->getTemplate()->set_var("iEmbedVideoId", $iEmbedVideoId);

        $sTitulo = $oEmbedVideo->getTitulo();
        $sDescripcion = $oEmbedVideo->getDescripcion();
        $iOrden = $oEmbedVideo->getOrden();

        $this->getTemplate()->set_var("sTitulo", $sTitulo);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("iOrden", $iOrden);

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function procesarAdjunto()
    {        
        if($this->getRequest()->has('agregarFoto')){
            $this->agregarFoto();
            return;
        }

        if($this->getRequest()->has('agregarArchivo')){
            $this->agregarArchivo();
            return;
        }
        
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('agregarVideo')){
            $this->agregarVideo();
            return;
        }

        if($this->getRequest()->has('guardarVideo')){
            $this->guardarVideo();
            return;
        }

        if($this->getRequest()->has('eliminarVideo')){
            $this->eliminarVideo();
            return;
        }

        if($this->getRequest()->has('guardarFoto')){
            $this->guardarFoto();
            return;
        }

        if($this->getRequest()->has('eliminarFoto')){
            $this->eliminarFoto();
            return;
        }

        if($this->getRequest()->has('guardarArchivo')){
            $this->guardarArchivo();
            return;
        }

        if($this->getRequest()->has('eliminarArchivo')){
            $this->eliminarArchivo();
            return;
        }
    }

    private function agregarFoto()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar fotos a este seguimiento", 401);
            }

            $nombreInputFile = 'fotoGaleria';

            $this->getUploadHelper()->setTiposValidosFotos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oSeguimiento->getId();

                //un array con los datos de las fotos
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($idItem, $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);

                try{
                    $oFoto = new stdClass();
                    $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
                    $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
                    $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];

                    $oFoto = Factory::getFotoInstance($oFoto);

                    $oFoto->setTitulo('');
                    $oFoto->setDescripcion('');
                    $oFoto->setTipoAdjunto();

                    $oSeguimiento->addFoto($oFoto);

                    SeguimientosController::getInstance()->guardarFotoSeguimiento($oSeguimiento, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailFoto", "ThumbnailFotoEditBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("urlFoto", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFoto", $pathFotoServidorBigSize);
                    $this->getTemplate()->set_var("tituloFoto", $oFoto->getTitulo());
                    $this->getTemplate()->set_var("descripcionFoto", $oFoto->getDescripcion());
                    $this->getTemplate()->set_var("iFotoId", $oFoto->getId());

                    //OJO QUE SI TIENE UN ';' EL HTML Y HAGO UN SPLIT EN EL JS SE ROMPE TODO !!
                    $respuesta = "1; ".$this->getTemplate()->pparse('ajaxThumbnailFoto', false);
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                }catch(Exception $e){
                    $respuesta = "0; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){
            $respuesta = "0; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }        
    }

    private function agregarArchivo()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar archivos a este seguimiento", 401);
            }

            $nombreInputFile = 'archivoGaleria';

            $this->getUploadHelper()->setTiposValidosDocumentos();

            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $idItem = $oSeguimiento->getId();

                list($nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo) = $this->getUploadHelper()->generarArchivoSistema($idItem, 'seguimiento', $nombreInputFile);
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);

                try{
                    $oArchivo = new stdClass();

                    $oArchivo->sNombre = $nombreArchivo;
                    $oArchivo->sNombreServidor = $nombreServidorArchivo;
                    $oArchivo->sTipoMime = $tipoMimeArchivo;
                    $oArchivo->iTamanio = $tamanioArchivo;
                    $oArchivo = Factory::getArchivoInstance($oArchivo);
                    $oArchivo->setTipoAdjunto();

                    $oSeguimiento->addArchivo($oArchivo);

                    SeguimientosController::getInstance()->guardarArchivoSeguimiento($oSeguimiento, $pathServidor);

                    $this->restartTemplate();

                    //creo el thumbnail para agregar a la galeria
                    $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxRowFoto", "RowArchivoEditBlock");

                    $nombreArchivo = $oArchivo->getTitulo();
                    if(empty($nombreArchivo)){
                        $nombreArchivo = $oArchivo->getNombre();
                    }

                    $hrefDescargar = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();

                    $this->getTemplate()->set_var("sNombreArchivo", $nombreArchivo);
                    $this->getTemplate()->set_var("sExtensionArchivo", $oArchivo->getTipoMime());
                    $this->getTemplate()->set_var("sTamanioArchivo", $oArchivo->getTamanio());
                    $this->getTemplate()->set_var("hrefDescargar", $hrefDescargar);
                    $this->getTemplate()->set_var("iArchivoId", $oArchivo->getId());

                    $respuesta = "1;; ".$this->getTemplate()->pparse('ajaxRowFoto', false);

                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    
                }catch(Exception $e){
                    $respuesta = "0;; Error al guardar en base de datos";
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                    return;
                }
            }
        }catch(Exception $e){

            $respuesta = "0;; Error al procesar el archivo";
            $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
            return;
        }        
    }

    private function agregarVideo()
    {
        try{
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');

            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para agregar videos a este seguimiento", 401);
            }

            $this->getJsonHelper()->initJsonAjaxResponse();

            if(!$this->getEmbedVideoHelper()->canBeParsed($this->getRequest()->getPost('codigo'))){
                $this->getJsonHelper()->setMessage("No se encontro un video para insertar desde la url ingresada. (o el servidor no es soportado)");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            try{
                $oEmbedVideo = new stdClass();
                $oEmbedVideo = Factory::getEmbedVideoInstance($oEmbedVideo);
                $oEmbedVideo->setCodigo($this->getRequest()->getPost('codigo'));

                $servidorOrigen = $this->getEmbedVideoHelper()->getServidor($oEmbedVideo);
                $oEmbedVideo->setOrigen($servidorOrigen);

                $oSeguimiento->addEmbedVideo($oEmbedVideo);

                SeguimientosController::getInstance()->guardarEmbedVideosSeguimiento($oSeguimiento);

                $this->restartTemplate();

                //creo el thumbnail para agregar a la galeria
                $this->getTemplate()->load_file_section("gui/componentes/galerias.gui.html", "ajaxThumbnailVideo", "ThumbnailVideoEditBlock");

                $urlFotoThumbnail = $this->getEmbedVideoHelper()->getEmbedVideoThumbnail($oEmbedVideo);
                $hrefAmpliarVideo = $this->getUrlFromRoute("indexIndexVideoAmpliar", true)."?id=".$oEmbedVideo->getId()."&v=".$oEmbedVideo->getUrlKey();

                $this->getTemplate()->set_var("hrefAmpliarVideo", $hrefAmpliarVideo);
                $this->getTemplate()->set_var("urlFoto", $urlFotoThumbnail);
                $this->getTemplate()->set_var("iEmbedVideoId", $oEmbedVideo->getId());

                $this->getJsonHelper()->setMessage("El video fue agregado con éxito en el seguimiento");
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('ajaxThumbnailVideo', false));
                $this->getJsonHelper()->setSuccess(true);
                $this->getJsonHelper()->sendJsonAjaxResponse();

            }catch(Exception $e){
                $this->getJsonHelper()->setMessage("Error al guardar en base de datos.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

        }catch(Exception $e){
            $this->getJsonHelper()->setMessage("Error al procesar el video");
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
            return;
        }        
    }

    private function guardarFoto()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iFotoId = $this->getRequest()->getPost('iFotoIdForm');

            if(empty($iFotoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bFotoUsuario = SeguimientosController::getInstance()->isFotoSeguimientoUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para editar esta foto", 401);
            }

            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            $oFoto->setOrden($this->getRequest()->getPost("orden"));
            $oFoto->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oFoto->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarFoto($oFoto);

            $this->getJsonHelper()->setMessage("La foto se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function eliminarFoto()
    {
        $iFotoId = $this->getRequest()->getParam('iFotoId');

        if(empty($iFotoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si la foto es de una publicacion creada por el usuario que esta logueado
            $bFotoUsuario = SeguimientosController::getInstance()->isFotoSeguimientoUsuario($iFotoId);
            if(!$bFotoUsuario){
                throw new Exception("No tiene permiso para borrar esta foto", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
            $oFoto = IndexController::getInstance()->getFotoById($iFotoId);

            IndexController::getInstance()->borrarFoto($oFoto, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function guardarVideo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEmbedVideoId = $this->getRequest()->getPost('iEmbedVideoId');

            if(empty($iEmbedVideoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bVideoUsuario = SeguimientosController::getInstance()->isEmbedVideoSeguimientoUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            $oEmbedVideo->setOrden($this->getRequest()->getPost("orden"));
            $oEmbedVideo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oEmbedVideo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarEmbedVideo($oEmbedVideo);

            $this->getJsonHelper()->setMessage("El video se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                
    }

    private function eliminarVideo()
    {
        $iEmbedVideoId = $this->getRequest()->getParam('iEmbedVideoId');

        if(empty($iEmbedVideoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $bVideoUsuario = SeguimientosController::getInstance()->isEmbedVideoSeguimientoUsuario($iEmbedVideoId);
            if(!$bVideoUsuario){
                throw new Exception("No tiene permiso para editar este video", 401);
            }

            $oEmbedVideo = IndexController::getInstance()->getEmbedVideoById($iEmbedVideoId);

            IndexController::getInstance()->borrarEmbedVideo($oEmbedVideo);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();                        
    }

    private function guardarArchivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iArchivoId = $this->getRequest()->getParam('iArchivoIdForm');

            if(empty($iArchivoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $bArchivoUsuario = SeguimientosController::getInstance()->isArchivoSeguimientoUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para editar este archivo", 401);
            }

            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            $oArchivo->setOrden($this->getRequest()->getPost("orden"));
            $oArchivo->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oArchivo->setTitulo($this->getRequest()->getPost("titulo"));

            IndexController::getInstance()->guardarArchivo($oArchivo);

            $this->getJsonHelper()->setMessage("El archivo se ha modificado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function eliminarArchivo()
    {
        $iArchivoId = $this->getRequest()->getParam('iArchivoId');

        if(empty($iArchivoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //devuelve si el archivo es de una publicacion creada por el usuario que esta logueado
            $bArchivoUsuario = SeguimientosController::getInstance()->isArchivoSeguimientoUsuario($iArchivoId);
            if(!$bArchivoUsuario){
                throw new Exception("No tiene permiso para borrar este archivo", 401);
            }

            $pathServidor = $this->getUploadHelper()->getDirectorioUploadArchivos(true);
            $oArchivo = IndexController::getInstance()->getArchivoById($iArchivoId);

            IndexController::getInstance()->borrarArchivo($oArchivo, $pathServidor);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }
    
    public function editarDiagnostico()
    {
    	$iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

        $iUsuarioId = $perfil->getUsuario()->getId();
        if($oSeguimiento->getUsuarioId() != $iUsuarioId){
            throw new Exception("No tiene permiso para ver este seguimiento", 401);
        }
                
        try{
            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionEditarDiagnosticoSeguimiento";

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            $this->setFrameTemplate()
                 ->setJSDiagnostico()
                 ->setHeadTag();
            
            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());
                        
            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");

            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());

            if($oSeguimiento->isSeguimientoPersonalizado()){
            	$this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            	$this->formDiagnosticoPersonalizado($oSeguimiento);
            }else{
            	$this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            	$this->formDiagnosticoSCC($oSeguimiento);
            }            
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function formDiagnosticoPersonalizado($oSeguimiento)
    {
        try{            
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "pageRightInnerMainCont", "FormularioPersonalizadoBlock");

            $oDiagnostico = $oSeguimiento->getDiagnostico();
            if($oDiagnostico){
            	$this->getTemplate()->set_var("sDescripcion",$oDiagnostico->getDescripcion());
                $this->getTemplate()->set_var("sCodigo",$oDiagnostico->getCodigo());
                $this->getTemplate()->set_var("iDiagnosticoId", $oDiagnostico->getId());
            }
            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }

    private function formDiagnosticoSCC($oSeguimiento)
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "pageRightInnerMainCont", "FormularioSCCBlock");

            $oDiagnostico = $oSeguimiento->getDiagnostico();

            $this->getTemplate()->set_var("sDescripcion", $oDiagnostico->getDescripcion());
            $this->getTemplate()->set_var("iDiagnosticoId", $oDiagnostico->getId());
            
            $aEjesTematicos = $oDiagnostico->getEjesTematicos();

            if($aEjesTematicos === null){
                $this->getTemplate()->unset_blocks("EstadoInicialBlock");
            }else{
                //cargo todo el estado inicial para cada uno de los ejes.
                $this->getTemplate()->unset_blocks("NoRecordsEstadoInicialBlock");

                foreach($aEjesTematicos as $oEjeTematico){
                    $sHtmlId = uniqid();
                    $this->getTemplate()->set_var("estadoInicialHtmlId", $sHtmlId);
                    $this->getTemplate()->set_var("iDiagnosticoSCCId", $oDiagnostico->getId());
                    $this->getTemplate()->set_var("iEjeIdBorrar", $oEjeTematico->getId());
                    $this->getTemplate()->set_var("sEstadoInicial", $oEjeTematico->getEstadoInicial());

                    //combo niveles
                    $iNivelId = $oEjeTematico->getArea()->getCiclo()->getNivel()->getId();
                    $iRecordsNiveles = 0;
                    $aNiveles = SeguimientosController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
                    foreach ($aNiveles as $oNivel){                        
                        if($iNivelId == $oNivel->getId()){
                            $this->getTemplate()->set_var("sSelectedNivel", "selected='selected'");
                        }
                        $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
                        $this->getTemplate()->set_var("sNivelDescripcion", $oNivel->getDescripcion());                       
                        $this->getTemplate()->parse("NivelesListBlock", true);
                        $this->getTemplate()->set_var("sSelectedNivel", "");
                    }

                    //combo ciclos                    
                    $iCicloId = $oEjeTematico->getArea()->getCiclo()->getId();
                    $aCiclos = SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId);
                    foreach ($aCiclos as $oCiclo){
                        if($iCicloId == $oCiclo->getId()){
                            $this->getTemplate()->set_var("sSelectedCiclo", "selected='selected'");
                        }
                        $this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
                        $this->getTemplate()->set_var("sCicloDescripcion", $oCiclo->getDescripcion());
                        $this->getTemplate()->parse("CiclosListBlock", true);
                        $this->getTemplate()->set_var("sSelectedCiclo", "");
                    }

                    //combo areas
                    $iAreaId = $oEjeTematico->getArea()->getId();
                    $aAreas = SeguimientosController::getInstance()->getAreasByCicloId($iCicloId);
                    foreach ($aAreas as $oArea){
                        if($iAreaId == $oArea->getId()){
                            $this->getTemplate()->set_var("sSelectedArea", "selected='selected'");
                        }
                        $this->getTemplate()->set_var("iAreaId", $oArea->getId());
                        $this->getTemplate()->set_var("sAreaDescripcion", $oArea->getDescripcion());
                        $this->getTemplate()->parse("AreaListBlock", true);
                        $this->getTemplate()->set_var("sSelectedArea", "");
                    }

                    //combo ejes (no es recursivo, es que para el area hay mas de un eje y tiene q listarse)
                    $iEjeId = $oEjeTematico->getId(); //este es el que viene del foreach padre
                    $aEjesSelect = SeguimientosController::getInstance()->getEjesByAreaId($iAreaId);
                    foreach ($aEjesSelect as $oEjeSelect){
                        if($iEjeId == $oEjeSelect->getId()){
                            $this->getTemplate()->set_var("sSelectedEje", "selected='selected'");
                        }
                        $this->getTemplate()->set_var("iEjeId", $oEjeSelect->getId());
                        $this->getTemplate()->set_var("sEjeDescripcion", $oEjeSelect->getDescripcion());
                        $this->getTemplate()->parse("EjeListBlock", true);
                        $this->getTemplate()->set_var("sSelectedEje", "");
                    }
                                       
                    $this->getTemplate()->parse("EstadoInicialBlock", true);
                    
                    $aSelects = array("NivelesListBlock", "CiclosListBlock", "AreaListBlock", "EjeListBlock");
                    $this->getTemplate()->delete_parsed_blocks($aSelects);
                }
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }
	             
    public function procesarDiagnostico()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        if($this->getRequest()->has('guardarDiagnosticoPersonalizado') ||
           $this->getRequest()->has('guardarDiagnosticoSCC')){            
            $this->guardarDiagnostico();
            return;
        }

        if($this->getRequest()->has('agregarEstadoInicial')){
            $this->agregarEstadoInicial();
            return;
        }
        
        if($this->getRequest()->has('eliminarEstadoInicial')){
            $this->eliminarEstadoInicial();
            return;
        }
    }

    private function eliminarEstadoInicial()
    {
        $iEjeId = $this->getRequest()->getParam('iEjeId');
        $iDiagnosticoSCCId = $this->getRequest()->getParam('iDiagnosticoSCCId');

        if(empty($iEjeId) || empty($iDiagnosticoSCCId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            //tiene el usuario permiso para modificar el diagnostico ?
            if(!SeguimientosController::getInstance()->isDiagnosticoUsuario($iDiagnosticoSCCId)){
                throw new Exception("No tiene permiso para editar el diagnostico", 401);
            }

            SeguimientosController::getInstance()->eliminarEstadoInicial($iEjeId, $iDiagnosticoSCCId);
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();  
    }

    private function agregarEstadoInicial()
    {
        $this->getJsonHelper()->initJsonAjaxResponse();
        
        //genero un id para el array del input del form, es solo para el html.
        $sHtmlId = uniqid();

        $this->restartTemplate();
        $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "ajaxRowEstadoInicial", "EstadoInicialBlock");

        $this->getTemplate()->set_var("estadoInicialHtmlId", $sHtmlId);
        $this->getTemplate()->set_var("sEstadoInicial", "");
        $this->getTemplate()->set_var("iEjeId", "");
        $this->getTemplate()->set_var("disabled", "disabled");
        $this->getTemplate()->set_var("iDiagnosticoSCCId", "");

        //combo niveles        
        $iRecordsNiveles = 0;
        $aNiveles = SeguimientosController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
        foreach ($aNiveles as $oNivel){            
            $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
            $this->getTemplate()->set_var("sNivelDescripcion", $oNivel->getDescripcion());            
            $this->getTemplate()->set_var("sNivelSelected", "");
            $this->getTemplate()->parse("NivelesListBlock", true);
        }

        $this->getJsonHelper()->setSuccess(true);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('ajaxRowEstadoInicial', false));
        $this->getJsonHelper()->setValor("estadoInicialHtmlId", $sHtmlId);
        $this->getJsonHelper()->sendJsonAjaxResponse();               
    }

    private function guardarDiagnostico()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            //tiene permiso para modificar el diagnostico ?
            $iDiagnosticoId = $this->getRequest()->getPost('iDiagnosticoIdForm');
            if(!SeguimientosController::getInstance()->isDiagnosticoUsuario($iDiagnosticoId)){
                throw new Exception("No tiene permiso para editar el diagnostico", 401);
            }

            $oDiagnostico = SeguimientosController::getInstance()->getDiagnosticoById($iDiagnosticoId);

            if ($oDiagnostico->isDiagnosticoPersonalizado()){
                $this->guardarDiagnosticoPersonalizado($oDiagnostico);
                return;
            }else{
                $this->guardarDiagnosticoSCC($oDiagnostico);
                return;
            }
        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
           $this->getJsonHelper()->sendJsonAjaxResponse();
        }
    }

    private function guardarDiagnosticoPersonalizado($oDiagnostico)
    {
        try{
            $oDiagnostico->setDescripcion($this->getRequest()->getPost("descripcion"));
            $oDiagnostico->setCodigo($this->getRequest()->getPost('codigo'));

            SeguimientosController::getInstance()->guardarDiagnostico($oDiagnostico);

            $this->getJsonHelper()->setMessage("El diagnóstico se guardo con éxito");
            $this->getJsonHelper()->setSuccess(true);
            $this->getJsonHelper()->sendJsonAjaxResponse();
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
    }

    private function guardarDiagnosticoSCC($oDiagnostico)
    {
        try{
            $oDiagnostico->setDescripcion($this->getRequest()->getPost("descripcion"));

            $vEstadoInicial = $this->getRequest()->getPost("estadoInicial");
            if(empty($vEstadoInicial) || !is_array($vEstadoInicial)){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Debe guardarse al menos un eje temático con estado inicial.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            //listado ejes tematicos con estado inicial
            $aEjeTematico = array();
            $aEjeTematicoAux = array(); //lo uso para asegurarme de que no haya ejes repetidos
            foreach($vEstadoInicial as $estadoInicial){

                $sEstadoInicial = trim($estadoInicial['estadoInicial']);
                if(empty($sEstadoInicial)){
                    $this->getJsonHelper()->setSuccess(false);
                    $this->getJsonHelper()->setMessage("Ningún eje puede quedar sin la descripción del estado inicial");
                    $this->getJsonHelper()->sendJsonAjaxResponse();
                    return;
                }
                
                $oEjeTematico = SeguimientosController::getInstance()->getEjeTematicoById($estadoInicial['ejeTematico']);
                $oEjeTematico->setEstadoInicial($sEstadoInicial);

                $aEjeTematicoAux[] = $oEjeTematico->getId();
            	$aEjeTematico[] = $oEjeTematico;
            }
            $oDiagnostico->setEjesTematicos($aEjeTematico);

            //hubo al menos una repeticion en el array.
            if(count($aEjeTematico) != count(array_unique($aEjeTematicoAux))){
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setMessage("Solo un estado inicial por Eje Temático. No pueden repetirse.");
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }
                       
            SeguimientosController::getInstance()->guardarDiagnostico($oDiagnostico);
           
            //genero el html de la grilla de los estados iniciales con el id actualizado.
            $this->restartTemplate();

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/diagnostico.gui.html", "ajaxGrillaEstadosIniciales", "GrillaEstadosInicialesBlock");
            $this->getTemplate()->set_var("NoRecordsEstadoInicialBlock", "");

            foreach($oDiagnostico->getEjesTematicos() as $oEjeTematico){
                $sHtmlId = uniqid();
                $this->getTemplate()->set_var("estadoInicialHtmlId", $sHtmlId);
                $this->getTemplate()->set_var("iDiagnosticoSCCId", $oDiagnostico->getId());
                $this->getTemplate()->set_var("iEjeIdBorrar", $oEjeTematico->getId());
                $this->getTemplate()->set_var("sEstadoInicial", $oEjeTematico->getEstadoInicial());

                //combo niveles
                $iNivelId = $oEjeTematico->getArea()->getCiclo()->getNivel()->getId();
                $iRecordsNiveles = 0;
                $aNiveles = SeguimientosController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
                foreach ($aNiveles as $oNivel){
                    if($iNivelId == $oNivel->getId()){
                        $this->getTemplate()->set_var("sNivelSelected", "selected='selected'");
                    }
                    $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
                    $this->getTemplate()->set_var("sNivelDescripcion", $oNivel->getDescripcion());
                    $this->getTemplate()->parse("NivelesListBlock", true);
                    $this->getTemplate()->set_var("sNivelSelected", "");
                }

                //combo ciclos
                $iCicloId = $oEjeTematico->getArea()->getCiclo()->getId();
                $aCiclos = SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId);
                foreach ($aCiclos as $oCiclo){
                    if($iCicloId == $oCiclo->getId()){
                        $this->getTemplate()->set_var("sSelectedCiclo", "selected='selected'");
                    }
                    $this->getTemplate()->set_var("iCicloId", $oCiclo->getId());
                    $this->getTemplate()->set_var("sCicloDescripcion", $oCiclo->getDescripcion());
                    $this->getTemplate()->parse("CiclosListBlock", true);
                    $this->getTemplate()->set_var("sSelectedCiclo", "");
                }

                //combo areas
                $iAreaId = $oEjeTematico->getArea()->getId();
                $aAreas = SeguimientosController::getInstance()->getAreasByCicloId($iCicloId);
                foreach ($aAreas as $oArea){
                    if($iAreaId == $oArea->getId()){
                        $this->getTemplate()->set_var("sSelectedArea", "selected='selected'");
                    }
                    $this->getTemplate()->set_var("iAreaId", $oArea->getId());
                    $this->getTemplate()->set_var("sAreaDescripcion", $oArea->getDescripcion());
                    $this->getTemplate()->parse("AreaListBlock", true);
                    $this->getTemplate()->set_var("sSelectedArea", "");
                }

                //combo ejes (no es recursivo, es que para el area hay mas de un eje y tiene q listarse)
                $iEjeId = $oEjeTematico->getId(); //este es el que viene del foreach padre
                $aEjesSelect = SeguimientosController::getInstance()->getEjesByAreaId($iAreaId);
                foreach ($aEjesSelect as $oEjeSelect){
                    if($iEjeId == $oEjeSelect->getId()){
                        $this->getTemplate()->set_var("sSelectedEje", "selected='selected'");
                    }
                    $this->getTemplate()->set_var("iEjeId", $oEjeSelect->getId());
                    $this->getTemplate()->set_var("sEjeDescripcion", $oEjeSelect->getDescripcion());
                    $this->getTemplate()->parse("EjeListBlock", true);
                    $this->getTemplate()->set_var("sSelectedEje", "");
                }

                $this->getTemplate()->parse("EstadoInicialBlock", true);

                $aSelects = array("NivelesListBlock", "CiclosListBlock", "AreaListBlock", "EjeListBlock");
                $this->getTemplate()->delete_parsed_blocks($aSelects);
            }
            
            $this->getJsonHelper()->setMessage("El diagnóstico se ha guardado con éxito");
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('ajaxGrillaEstadosIniciales', false));
            $this->getJsonHelper()->setValor("modificarDiagnosticoSCC", "1");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    public function listarCiclosPorNiveles()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404);}
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iNivelId = $this->getRequest()->getPost("iNivelId");

            if(empty($iNivelId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
            
            $jCiclos = array();
            $aCiclos = SeguimientosController::getInstance()->getCiclosByNivelId($iNivelId);
            if(!empty($aCiclos)){
                foreach($aCiclos as $oCiclo){
                    $obj = new stdClass();
                    $obj->iId = $oCiclo->getId();
                    $obj->sDescripcion = $oCiclo->getDescripcion();
                    array_push($jCiclos, $obj);
                }
            }
            
            $this->getJsonHelper()->sendJson($jCiclos);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function listarAreasPorCiclos()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404);}
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iCicloId =  $this->getRequest()->getPost("iCicloId");

            if(empty($iCicloId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $jAreas = array();
            $aAreas = SeguimientosController::getInstance()->getAreasByCicloId($iCicloId);
            if(!empty($aAreas)){
                foreach($aAreas as $oArea){
                    $obj = new stdClass();
                    $obj->iId = $oArea->getId();
                    $obj->sDescripcion = $oArea->getDescripcion();
                    array_push($jAreas, $obj);
                }
            }

            $this->getJsonHelper()->sendJson($jAreas);
        }catch(Exception $e){
            throw $e;
        }
    }
 	 
    public function listarEjesPorArea()
    {
    	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iAreaId =  $this->getRequest()->getPost("iAreaId");

            if(empty($iAreaId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $jEjes = array();
            $aEjes = SeguimientosController::getInstance()->getEjesByAreaId($iAreaId);
            if(!empty($aEjes)){
                foreach($aEjes as $oEje){
                    $obj = new stdClass();
                    $obj->iId = $oEje->getId();
                    $obj->sDescripcion = $oEje->getDescripcion();
                    array_push($jEjes, $obj);
                }
            }

            $this->getJsonHelper()->sendJson($jEjes);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function listarObjetivosAprendizajePorEje()
    {
    	if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iEjeId =  $this->getRequest()->getPost("iEjeId");

            if(empty($iEjeId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $aObjetivosAprendizaje = SeguimientosController::getInstance()->getObjetivosAprendizajeByEjeId($iEjeId);
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "ListObjetivosAprendizajes", "ObjetivosAprendizaListBlock");
            if(!empty($aObjetivosAprendizaje)){
                foreach($aObjetivosAprendizaje as $oObjetivoAprendizaje){
                    $this->getTemplate()->set_var("iObjetivoAprendizajeId", $oObjetivoAprendizaje->getId());
                    $this->getTemplate()->set_var("sObjetivoAprendizajeDescripcion", $oObjetivoAprendizaje->getDescripcion());
                    $this->getTemplate()->parse("ListadoBlock", true);
                }
                $this->getTemplate()->set_var("ListadoNoRecordsBlock","");
            }else{
                $this->getTemplate()->set_var("ListadoBlock", "");
                $this->getTemplate()->parse("ListadoNoRecordsBlock",false);
            }
               
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse("ListObjetivosAprendizajes"));
 
        }catch(Exception $e){
            throw $e;
        }
    }

    public function administrarObjetivos()
    {
        try
        {
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para ver este seguimiento", 401);
            }

            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionAdministrarObjetivosSeguimiento";

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            $this->setFrameTemplate()
                 ->setJSObjetivos()
                 ->setHeadTag();

            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->set_var("iSeguimientoId", $oSeguimiento->getId());           
            $this->getTemplate()->set_var("tituloSeccion", "Administrar Objetivos Seguimiento");

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "pageRightInnerMainCont", "ListadoObjetivosBlock");

            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("AsociarObjetivoAprendizajeBlock", "");
            }else{
                $this->getTemplate()->set_var("CrearObjetivoPersonalizadoBlock", "");
            }

            $iRecordsTotal = 0;
            $aObjetivos = $oSeguimiento->getObjetivos();
            if(count($aObjetivos) > 0){

                $this->getTemplate()->set_var("NoRecordsThumbsObjetivosBlock", "");

            	foreach ($aObjetivos as $oObjetivo){

                    $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);
                    $this->getTemplate()->set_var("iObjetivoId", $oObjetivo->getId());
                    
                    $this->getTemplate()->set_var("dEstimacion", $oObjetivo->getEstimacion(true));
                                        
                    if($oObjetivo->isLogrado()){                        
                        $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                        $this->getTemplate()->set_var("EstimacionBlock", "");

                        $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha(true));
                    }else{                        
                        $this->getTemplate()->set_var("ObjetivoLogradoBlock", "");
                        $this->getTemplate()->set_var("FechaLogradoBlock", "");
                        
                        if(!$oObjetivo->isEstimacionVencida()){
                            $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                            $this->getTemplate()->set_var("expiradaClass", "");
                        }else{
                            $this->getTemplate()->set_var("expiradaClass", "txt_cuidado");
                        }                       
                    }

                    if(!$oObjetivo->isActivo()){
                        $this->getTemplate()->set_var("sActivoClass", "disabled");
                        $this->getTemplate()->set_var("calendarClass", "calendar");
                        $this->getTemplate()->set_var("MenuObjetivoActivoBlock", "");

                        $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                        $this->getTemplate()->set_var("ObjetivoLogradoBlock", "");
                    }else{
                        $this->getTemplate()->set_var("sActivoClass", "");
                        $this->getTemplate()->set_var("calendarClass", "calendarEdit ihover");
                        $this->getTemplate()->set_var("MenuObjetivoDesactivadoBlock", "");
                    }
                                       
                    switch($oObjetivo->getRelevancia()->getDescripcion())
                    {
                        case "baja":{
                            $iconoRelevanciaBlock = "IconoRelevanciaBajaBlock";
                            break;
                        }
                        case "normal":{
                            $iconoRelevanciaBlock = "IconoRelevanciaNormalBlock";
                            break;
                        }                        
                        case "alta":{
                            $iconoRelevanciaBlock = "IconoRelevanciaAltaBlock";
                            break;
                        }                        
                    }
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "iconoRelevancia", $iconoRelevanciaBlock);
                    $this->getTemplate()->set_var("iconoRelevancia", $this->getTemplate()->pparse("iconoRelevancia"));
                    $this->getTemplate()->delete_parsed_blocks($iconoRelevanciaBlock);

                    $this->getTemplate()->set_var("sRelevancia", $oObjetivo->getRelevancia()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionEje", $oObjetivo->getEje()->getDescripcion());

                    //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                    $sDescripcionObjetivo = $oObjetivo->getDescripcion();
                    if(strlen($sDescripcionObjetivo) > 150){
                        $sDescripcionObjetivo = Utils::tokenTruncate($sDescripcionObjetivo, 150);
                        $sDescripcionObjetivo = nl2br($sDescripcionObjetivo);
                    }else{
                        $this->getTemplate()->set_var("LinkVerMasBlock", "");
                    }
                    
                    $this->getTemplate()->set_var("tipoObjetivo", get_class($oObjetivo));
                    $this->getTemplate()->set_var("sDescripcionObjetivo", $sDescripcionObjetivo);
                    
                    $this->getTemplate()->parse("ObjetivoBlock", true);

                    $this->getTemplate()->delete_parsed_blocks("FechaLogradoBlock");
                    $this->getTemplate()->delete_parsed_blocks("EstimacionBlock");
                    $this->getTemplate()->delete_parsed_blocks("ObjetivoLogradoBlock");
                    $this->getTemplate()->delete_parsed_blocks("EstimacinoExpiradaBlock");
                    $this->getTemplate()->delete_parsed_blocks("MenuObjetivoActivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("MenuObjetivoDesactivadoBlock");
                    $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                }
            }else{
                $this->getTemplate()->set_var("ObjetivoBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
                        
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function procesarObjetivos()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('masObjetivos')){
            $this->masObjetivos();
            return;
        }

        if($this->getRequest()->has('toggleActivo')){
            $this->toggleActivo();
            return;
        }        
        
        if($this->getRequest()->has('recronogramaForm')){
            $this->recronogramaForm();
            return;
        }
        
        if($this->getRequest()->has('modificarEstimacion')){
            $this->modificarEstimacion();
            return;
        }
        
        if($this->getRequest()->has('eliminarObjetivo')){
            $this->eliminarObjetivo();
            return;
        }
    }

    /**
     * Devuelve el contenido de un dialog: tiempo de edicion expirado / se borro el objetivo con exito
     */
    private function eliminarObjetivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            $iObjetivoId = $this->getRequest()->getParam('iObjetivoId');
            $tipoObjetivo = $this->getRequest()->getParam('tipoObjetivo');

            if(empty($iSeguimientoId) || empty($iObjetivoId) || empty($tipoObjetivo)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            if($this->getRequest()->getParam('tipoObjetivo') == "ObjetivoPersonalizado"){
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);
            }

            if(!$oObjetivo->isEditable()){
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeEdicionExpirada", "MsgFichaInfoBlock");
                $this->getTemplate()->set_var("sTituloMsgFicha", "Plazo de edición expirado");
                $this->getTemplate()->set_var("sMsgFicha", "El plazo para edición de Seguimientos y entidades relacionadas en el sistema es de "
                                                            .SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento()." días.
                                                            Puede desactivar el objetivo para las entradas posteriores, sin embargo la referencia permanecerá en el historial.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse("sMensajeEdicionExpirada"));
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            SeguimientosController::getInstance()->borrarObjetivoSeguimiento($oObjetivo, $iSeguimientoId);
            $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeExito", "MsgCorrectoBlockI32");
            $this->getTemplate()->set_var("sMensaje", "El objetivo se ha borrado de manera exitosa.");
            $this->getJsonHelper()->setSuccess(true);
            $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse("sMensajeExito"));

            $this->getJsonHelper()->sendJsonAjaxResponse();
        }catch(Exception $e){
            throw $e;
        }                
    }

    private function recronogramaForm()
    {
        try
        {
            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            $iObjetivoId = $this->getRequest()->getParam('iObjetivoId');
            $tipoObjetivo = $this->getRequest()->getParam('tipoObjetivo');

            if(empty($iSeguimientoId) || empty($iObjetivoId) || empty($tipoObjetivo)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            if($this->getRequest()->getParam('tipoObjetivo') == "ObjetivoPersonalizado"){
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);
            }

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "popUpContent", "FormularioRecronogramaBlock");

            $dEstimacion = $oObjetivo->getEstimacion(true);

            $this->getTemplate()->set_var("tipoObjetivoIdForm", $tipoObjetivo);
            $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);
            $this->getTemplate()->set_var("iObjetivoIdForm", $iObjetivoId);
            $this->getTemplate()->set_var("dEstimacion", $dEstimacion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            throw $e;
        }
    }

    private function modificarEstimacion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iSeguimientoId = $this->getRequest()->getPost('seguimientoIdForm');
            $iObjetivoId = $this->getRequest()->getPost('objetivoIdForm');
            $tipoObjetivoIdForm = $this->getRequest()->getPost('tipoObjetivoIdForm');

            if(empty($iSeguimientoId) || empty($iObjetivoId) || empty($tipoObjetivoIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            if($tipoObjetivoIdForm == "ObjetivoPersonalizado"){
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);
            }
            
            $dEstimacion = Utils::fechaAFormatoSQL($this->getRequest()->getPost("estimacion"));            
            $oObjetivo->setEstimacion($dEstimacion);

            $bSuccess = false;
            if($oObjetivo->isObjetivoPersonalizado()){
                $bSuccess = SeguimientosController::getInstance()->guardarObjetivoPersonalizado($oObjetivo);
            }

            if($oObjetivo->isObjetivoAprendizaje()){
                $bSuccess = SeguimientosController::getInstance()->guardarObjetivoAprendizajeSeguimientoScc($oObjetivo, $iSeguimientoId);
            }

            $this->getJsonHelper()->setSuccess($bSuccess);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function toggleActivo()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
            $iObjetivoId = $this->getRequest()->getParam('iObjetivoId');
            $bActivo = $this->getRequest()->getParam('bActivo');

            if(empty($iSeguimientoId) || empty($iObjetivoId) || $bActivo == null){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            if($this->getRequest()->getParam('tipoObjetivo') == "ObjetivoPersonalizado"){
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);
            }

            $bActivo = ($bActivo == "1")?true:false;
            $oObjetivo->isActivo($bActivo);
            if(!$bActivo){
                $oObjetivo->setFechaDesactivadoHoy();
            }else{
                $oObjetivo->setFechaDesactivado(null);
            }
            
            $bSuccess = false;
            if($oObjetivo->isObjetivoPersonalizado()){
                $bSuccess = SeguimientosController::getInstance()->guardarObjetivoPersonalizado($oObjetivo);
            }

            if($oObjetivo->isObjetivoAprendizaje()){
                $bSuccess = SeguimientosController::getInstance()->guardarObjetivoAprendizajeSeguimientoScc($oObjetivo, $iSeguimientoId);
            }
            
            $this->getJsonHelper()->setSuccess($bSuccess);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function masObjetivos()
    {
        try
        {
            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para ver este seguimiento", 401);
            }
            
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "ajaxThumbsObjetivosBlock", "ThumbsObjetivosBlock");

            $sOrderBy = $this->getRequest()->getPost("sOrderBy");
            $sOrderBy = strlen($sOrderBy) ? $sOrderBy : null;
            $sOrder = $this->getRequest()->getPost("sOrder");
            $sOrder = strlen($sOrder) ? $sOrder : null;
            if($sOrderBy !== null && $sOrderBy == 'relevancia'){ $sOrderBy = "iRelevanciaId"; }
            if($sOrderBy !== null && $sOrderBy == 'estimacion'){ $sOrderBy = "dEstimacion"; }

            $iRecordsTotal = 0;
            $aObjetivos = $oSeguimiento->getObjetivos($sOrderBy, $sOrder);

            if(count($aObjetivos) > 0){

                $this->getTemplate()->set_var("NoRecordsThumbsObjetivosBlock", "");

            	foreach ($aObjetivos as $oObjetivo){

                    $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);
                    $this->getTemplate()->set_var("iObjetivoId", $oObjetivo->getId());

                    $this->getTemplate()->set_var("tipoObjetivo", get_class($oObjetivo));

                    $this->getTemplate()->set_var("dEstimacion", $oObjetivo->getEstimacion(true));

                    if($oObjetivo->isLogrado()){
                        $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                        $this->getTemplate()->set_var("EstimacionBlock", "");

                        $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha(true));
                    }else{
                        $this->getTemplate()->set_var("ObjetivoLogradoBlock", "");
                        $this->getTemplate()->set_var("FechaLogradoBlock", "");

                        if(!$oObjetivo->isEstimacionVencida()){
                            $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                            $this->getTemplate()->set_var("expiradaClass", "");
                        }else{
                            $this->getTemplate()->set_var("expiradaClass", "txt_cuidado");
                        }
                    }

                    if(!$oObjetivo->isActivo()){
                        $this->getTemplate()->set_var("sActivoClass", "disabled");
                        $this->getTemplate()->set_var("calendarClass", "calendar");
                        $this->getTemplate()->set_var("MenuObjetivoActivoBlock", "");

                        $this->getTemplate()->set_var("EstimacinoExpiradaBlock", "");
                        $this->getTemplate()->set_var("ObjetivoLogradoBlock", "");
                    }else{
                        $this->getTemplate()->set_var("sActivoClass", "");
                        $this->getTemplate()->set_var("calendarClass", "calendarEdit ihover");
                        $this->getTemplate()->set_var("MenuObjetivoDesactivadoBlock", "");
                    }

                    switch($oObjetivo->getRelevancia()->getDescripcion())
                    {
                        case "baja":{
                            $iconoRelevanciaBlock = "IconoRelevanciaBajaBlock";
                            break;
                        }
                        case "normal":{
                            $iconoRelevanciaBlock = "IconoRelevanciaNormalBlock";
                            break;
                        }
                        case "alta":{
                            $iconoRelevanciaBlock = "IconoRelevanciaAltaBlock";
                            break;
                        }
                    }
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "iconoRelevancia", $iconoRelevanciaBlock);
                    $this->getTemplate()->set_var("iconoRelevancia", $this->getTemplate()->pparse("iconoRelevancia"));
                    $this->getTemplate()->delete_parsed_blocks($iconoRelevanciaBlock);

                    $this->getTemplate()->set_var("sRelevancia", $oObjetivo->getRelevancia()->getDescripcion());
                    $this->getTemplate()->set_var("sDescripcionEje", $oObjetivo->getEje()->getDescripcion());
                    
                    //corto si es una descripcion muy larga, lo hago asi porque sino me puede cortar los <br>
                    $sDescripcionObjetivo = $oObjetivo->getDescripcion();
                    if(strlen($sDescripcionObjetivo) > 150){
                        $sDescripcionObjetivo = Utils::tokenTruncate($sDescripcionObjetivo, 150);
                        $sDescripcionObjetivo = nl2br($sDescripcionObjetivo);
                    }else{
                        $this->getTemplate()->set_var("LinkVerMasBlock", "");
                    }
                    $this->getTemplate()->set_var("sDescripcionObjetivo", $sDescripcionObjetivo);

                    $this->getTemplate()->parse("ObjetivoBlock", true);

                    $this->getTemplate()->delete_parsed_blocks("FechaLogradoBlock");
                    $this->getTemplate()->delete_parsed_blocks("EstimacionBlock");
                    $this->getTemplate()->delete_parsed_blocks("ObjetivoLogradoBlock");
                    $this->getTemplate()->delete_parsed_blocks("EstimacinoExpiradaBlock");
                    $this->getTemplate()->delete_parsed_blocks("MenuObjetivoActivoBlock");
                    $this->getTemplate()->delete_parsed_blocks("MenuObjetivoDesactivadoBlock");
                    $this->getTemplate()->delete_parsed_blocks("LinkVerMasBlock");
                }
            }else{
                $this->getTemplate()->set_var("ObjetivoBlock", "");
            }

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxThumbsObjetivosBlock', false));

        }catch(Exception $e){
            throw $e;
        }  
    }

    public function formObjetivo()
    {
        try
        {
            if(!$this->getAjaxHelper()->isAjaxContext()){
                throw new Exception("", 404);
            }

            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            if(empty($iSeguimientoId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para ver este seguimiento", 401);
            }
            
            if($this->getRequest()->getParam('tipoObjetivo') == "ObjetivoPersonalizado"){
                $this->mostrarFormularioObjetivoPersonalizadoPopUp($iSeguimientoId);
            }else{
                $this->mostrarFormularioObjetivoAprendizajePopUp($iSeguimientoId);
            }
        }catch(Exception $e){
            throw $e;
        }            
    }

    private function mostrarFormularioObjetivoPersonalizadoPopUp($iSeguimientoId)
    {
        try
        {
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "popUpContent", "FormularioObjetivoBlock");

            $this->getTemplate()->set_var("ObjetivoAprendizajeBlock", "");

            $sCheckedRelevanciaAlta = "";
            $sCheckedRelevanciaNormal = "";
            $sCheckedRelevanciaBaja = "";

            //FORMULARIO CREAR
            if(!$this->getRequest()->has('iObjetivoId')){
                $sTituloForm = "Crear un nuevo objetivo";
                $this->getTemplate()->set_var("SubmitModificarObjetivoBlock", "");
                
                $iEjeId = "";
                $sCheckedRelevanciaNormal = "checked='checked'";
                $sDescripcion = "";
                $dEstimacion =  "";
                $iObjetivoId = "";
                
            //FORMULARIO EDITAR
            }else{
                //tiene permiso para editar este objetivo?
                $iObjetivoId = $this->getRequest()->get('iObjetivoId');
                
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }

                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);

                if(!$oObjetivo->isEditable()){                    
                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeEdicionExpirada", "MsgFichaInfoBlock");
                    $this->getTemplate()->set_var("sTituloMsgFicha", "Plazo de edición expirado");
                    $this->getTemplate()->set_var("sMsgFicha", "El plazo para edición de Seguimientos y entidades relacionadas en el sistema es de "
                                                                .SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento()." días.
                                                                Puede desactivar el objetivo para las entradas posteriores, sin embargo la referencia permanecerá en el historial.");
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse("sMensajeEdicionExpirada"));
                    return;
                }

                $sTituloForm = "Modificar objetivo";
                $this->getTemplate()->set_var("SubmitCrearObjetivoBlock", "");
                
                $iEjeId = $oObjetivo->getEje()->getId();

                switch($oObjetivo->getRelevancia()->getDescripcion()){
                    case "alta": $sCheckedRelevanciaAlta = "checked='checked'"; break;
                    case "normal": $sCheckedRelevanciaNormal = "checked='checked'"; break;
                    case "baja": $sCheckedRelevanciaBaja = "checked='checked'"; break;
                }
                
                $sDescripcion = $oObjetivo->getDescripcion();
                $dEstimacion =  $oObjetivo->getEstimacion(true);
            }

            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);
            $this->getTemplate()->set_var("iObjetivoIdForm", $iObjetivoId);

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);            
            $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
            
            $this->getTemplate()->set_var("checkedRelevanciaAlta", $sCheckedRelevanciaAlta);                        
            $this->getTemplate()->set_var("checkedRelevanciaNormal", $sCheckedRelevanciaNormal);                        
            $this->getTemplate()->set_var("checkedRelevanciaBaja", $sCheckedRelevanciaBaja);
            $aRelevancias = SeguimientosController::getInstance()->obtenerArrayRelevancias();
            $this->getTemplate()->set_var("iRelevanciaAltaId", $aRelevancias['alta']);
            $this->getTemplate()->set_var("iRelevanciaNormalId", $aRelevancias['normal']);
            $this->getTemplate()->set_var("iRelevanciaBajaId", $aRelevancias['baja']);

            $this->getTemplate()->set_var("dEstimacion", $dEstimacion);

            //dropdown ejes con subejes
            $aEjes = SeguimientosController::getInstance()->getEjesPersonalizados();
            foreach($aEjes as $oEje){
                $this->getTemplate()->set_var("sEjePpal", $oEje->getDescripcion());
                foreach($oEje->getSubejes() as $oSubEje){
                    $this->getTemplate()->set_var("sSubEje", $oSubEje->getDescripcion());
                    $this->getTemplate()->set_var("iSubEjeValue", $oSubEje->getId());
                    if($iEjeId == $oSubEje->getId()){
                        $this->getTemplate()->set_var("sSelectedSubEje", "selected='selected'");
                    }
                    $this->getTemplate()->parse("OptionSelectEje", true);
                    $this->getTemplate()->set_var("sSelectedSubEje", "");
                }
                $this->getTemplate()->parse("OptGrpSelectEje", true);
                $this->getTemplate()->delete_parsed_blocks("OptionSelectEje");
            }
                          
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            throw $e;
        }
    }

    private function mostrarFormularioObjetivoAprendizajePopUp($iSeguimientoId)
    {
        try
        {
            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "popUpContent", "FormularioObjetivoBlock");

            $this->getTemplate()->set_var("EjePersonalizadoBlock", "");

            $sCheckedRelevanciaAlta = "";
            $sCheckedRelevanciaNormal = "";
            $sCheckedRelevanciaBaja = "";

            //FORMULARIO CREAR
            if(!$this->getRequest()->has('iObjetivoId')){
                $this->getTemplate()->set_var("SubmitModificarObjetivoBlock", "");
                $this->getTemplate()->set_var("ReadOnlyObjetivoAprendizaje", "");

                $sTituloForm = "Asociar objetivo de aprendizaje a seguimiento";

                $sCheckedRelevanciaNormal = "checked='checked'";
                $dEstimacion =  "";
                $disabled = "disabled"; //para desactivar los selects en la creacion

                //combo niveles
                $iRecordsNiveles = 0;
                $aNiveles = SeguimientosController::getInstance()->getNiveles($filtro = array(), $iRecordsNiveles, null, null, null, null);
                foreach ($aNiveles as $oNivel){
                    $this->getTemplate()->set_var("iNivelId", $oNivel->getId());
                    $this->getTemplate()->set_var("sNivelDescripcion", $oNivel->getDescripcion());
                    $this->getTemplate()->parse("NivelesListBlock", true);
                }

                $iObjetivoId = "";
                
            //FORMULARIO EDITAR
            }else{
                //tiene permiso para editar este objetivo?
                $iObjetivoId = $this->getRequest()->get('iObjetivoId');

                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }

                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);

                if(!$oObjetivo->isEditable()){
                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeEdicionExpirada", "MsgFichaInfoBlock");
                    $this->getTemplate()->set_var("sTituloMsgFicha", "Plazo de edición expirado");
                    $this->getTemplate()->set_var("sMsgFicha", "El plazo para edición de Seguimientos y entidades relacionadas en el sistema es de "
                                                                .SeguimientosController::getInstance()->getCantidadDiasExpiracionSeguimiento()." días.
                                                                Puede desactivar el objetivo para las entradas posteriores, sin embargo la referencia permanecerá en el historial.");
                    $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse("sMensajeEdicionExpirada"));
                    return;
                }
                
                $sTituloForm = "Editar Objetivo de aprendizaje en Seguimiento";
                $this->getTemplate()->set_var("SubmitCrearObjetivoBlock", "");
                $this->getTemplate()->set_var("SelectsObjetivoAprendizajeBlock", "");

                switch($oObjetivo->getRelevancia()->getDescripcion()){
                    case "alta": $sCheckedRelevanciaAlta = "checked='checked'"; break;
                    case "normal": $sCheckedRelevanciaNormal = "checked='checked'"; break;
                    case "baja": $sCheckedRelevanciaBaja = "checked='checked'"; break;
                }
                
                $dEstimacion =  $oObjetivo->getEstimacion();
                $disabled = "";

                //lleno los selects con los valores actuales.
                $oEjeTematico = $oObjetivo->getEje();

                $sNivelDescripcion = $oEjeTematico->getArea()->getCiclo()->getNivel()->getDescripcion();
                $sCicloDescripcion = $oEjeTematico->getArea()->getCiclo()->getDescripcion();
                $sAreaDescripcion = $oEjeTematico->getArea()->getDescripcion();
                $sEjeDescripcion = $oEjeTematico->getDescripcion();
                $sObjetivoDescripcion = $oObjetivo->getDescripcion();
                $this->getTemplate()->set_var("sNivelDescripcion", $sNivelDescripcion);
                $this->getTemplate()->set_var("sCicloDescripcion", $sCicloDescripcion);
                $this->getTemplate()->set_var("sAreaDescripcion", $sAreaDescripcion);
                $this->getTemplate()->set_var("sEjeDescripcion", $sEjeDescripcion);
                $sObjetivoDescripcion = Utils::tokenTruncate($sObjetivoDescripcion, 300);
                $this->getTemplate()->set_var("sObjetivoDescripcion", $sObjetivoDescripcion);
                $this->getTemplate()->set_var("iObjetivoAprendizajeId", $oObjetivo->getId());
            }

            $iSeguimientoId = $this->getRequest()->getPost('iSeguimientoId');
            $this->getTemplate()->set_var("iSeguimientoIdForm", $iSeguimientoId);
            $this->getTemplate()->set_var("iObjetivoIdForm", $iObjetivoId);

            $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
            $this->getTemplate()->set_var("disabled", $disabled);

            $this->getTemplate()->set_var("checkedRelevanciaAlta", $sCheckedRelevanciaAlta);
            $this->getTemplate()->set_var("checkedRelevanciaNormal", $sCheckedRelevanciaNormal);
            $this->getTemplate()->set_var("checkedRelevanciaBaja", $sCheckedRelevanciaBaja);
            $aRelevancias = SeguimientosController::getInstance()->obtenerArrayRelevancias();
            $this->getTemplate()->set_var("iRelevanciaAltaId", $aRelevancias['alta']);
            $this->getTemplate()->set_var("iRelevanciaNormalId", $aRelevancias['normal']);
            $this->getTemplate()->set_var("iRelevanciaBajaId", $aRelevancias['baja']);

            $this->getTemplate()->set_var("dEstimacion", $dEstimacion);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarObjetivo()
    {
        try{
            if(!$this->getAjaxHelper()->isAjaxContext()){
                throw new Exception("", 404);
            }

            $iSeguimientoId = $this->getRequest()->getPost('seguimientoIdForm');
            if(empty($iSeguimientoId)){
                throw new Exception("no puede ejecutar la acción", 401);
            }

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();

            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para administrar objetivos en este seguimiento", 401);
            }

            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->guardarObjetivoPersonalizado($iSeguimientoId);
            }else{
                $this->guardarObjetivoAprendizaje($iSeguimientoId);
            }
        }catch(Exception $e){
            throw $e;
        }                
    }

    private function guardarObjetivoPersonalizado($iSeguimientoId)
    {
        if($this->getRequest()->has('crearObjetivo')){
            $this->crearObjetivoPersonalizado($iSeguimientoId);
            return;
        }

        if($this->getRequest()->has('modificarObjetivo')){
            $this->modificarObjetivoPersonalizado($iSeguimientoId);
            return;
        }  
    }

    private function crearObjetivoPersonalizado($iSeguimientoId)
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oObjetivoPersonalizado = new stdClass();

            $oEje = SeguimientosController::getInstance()->getEjePersonalizadoById($this->getRequest()->getPost("eje"));
            $oRelevancia = SeguimientosController::getInstance()->getRelevanciaById($this->getRequest()->getPost("relevancia"));

            $oObjetivoPersonalizado->sDescripcion = $this->getRequest()->getPost("descripcion");
            $oObjetivoPersonalizado->oEje = $oEje;
            $oObjetivoPersonalizado->oRelevancia = $oRelevancia;
            $dEstimacion = Utils::fechaAFormatoSQL($this->getRequest()->getPost("estimacion"));
            $oObjetivoPersonalizado->dEstimacion = $dEstimacion;

            $oObjetivoPersonalizado = Factory::getObjetivoPersonalizadoInstance($oObjetivoPersonalizado);
            
            SeguimientosController::getInstance()->guardarObjetivoPersonalizado($oObjetivoPersonalizado, $iSeguimientoId);

            $this->getJsonHelper()->setValor("agregarObjetivo", "1");
            $this->getJsonHelper()->setMessage("El objetivo se ha creado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarObjetivoPersonalizado($iSeguimientoId)
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iObjetivoId = $this->getRequest()->getPost('objetivoIdForm');
           
            if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                throw new Exception("No tiene permiso para administrar objetivos en este seguimiento", 401);
            }
            
            $oObjetivoPersonalizado = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
                        
            if(!$oObjetivoPersonalizado->isEditable()){
                $this->getJsonHelper()->setMessage("El plazo para editar el objetivo ha expirado.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oEje = SeguimientosController::getInstance()->getEjePersonalizadoById($this->getRequest()->getPost("eje"));
            $oRelevancia = SeguimientosController::getInstance()->getRelevanciaById($this->getRequest()->getPost("relevancia"));

            $oObjetivoPersonalizado->setRelevancia($oRelevancia);
            $oObjetivoPersonalizado->setEje($oEje);
            $oObjetivoPersonalizado->setDescripcion($this->getRequest()->getPost("descripcion"));
            $dEstimacion = Utils::fechaAFormatoSQL($this->getRequest()->getPost("estimacion"));
            $oObjetivoPersonalizado->setEstimacion($dEstimacion);

            SeguimientosController::getInstance()->guardarObjetivoPersonalizado($oObjetivoPersonalizado);

            $this->getJsonHelper()->setValor("modificarObjetivo", "1");
            $this->getJsonHelper()->setMessage("Los cambios se han guardado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){            
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function guardarObjetivoAprendizaje($iSeguimientoId)
    {
        if($this->getRequest()->has('crearObjetivo')){
            $this->asociarObjetivoAprendizaje($iSeguimientoId);
            return;
        }

        if($this->getRequest()->has('modificarObjetivo')){
            $this->modificarObjetivoAprendizaje($iSeguimientoId);
            return;
        }
    }

    private function asociarObjetivoAprendizaje($iSeguimientoId)
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $oObjetivoAprendizaje = SeguimientosController::getInstance()->getObjetivoAprendizajeById($this->getRequest()->getPost("objetivoAprendizaje"));

            //primero me fijo si el objetivo de aprendizaje ya esta asociado
            if(SeguimientosController::getInstance()->existeObjetivoAprendizajeAsociadoSeguimientoSCC($iSeguimientoId, $oObjetivoAprendizaje->getId())){
                $this->getJsonHelper()->setMessage("El objetivo ya esta asociado al seguimiento");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oRelevancia = SeguimientosController::getInstance()->getRelevanciaById($this->getRequest()->getPost("relevancia"));
            $oObjetivoAprendizaje->setRelevancia($oRelevancia);
            $dEstimacion = Utils::fechaAFormatoSQL($this->getRequest()->getPost("estimacion"));
            $oObjetivoAprendizaje->setEstimacion($dEstimacion);

            SeguimientosController::getInstance()->guardarObjetivoAprendizajeSeguimientoScc($oObjetivoAprendizaje, $iSeguimientoId);

            $this->getJsonHelper()->setValor("agregarObjetivo", "1");
            $this->getJsonHelper()->setMessage("El objetivo se ha asociado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){            
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    /**
     * El objetivo asociado no cambia, lo que se modifica es la estimacino y la relevancia.
     *
     * Descripcion esta precargada
     */
    private function modificarObjetivoAprendizaje($iSeguimientoId)
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iObjetivoId = $this->getRequest()->getPost('objetivoIdForm');

            if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                throw new Exception("No tiene permiso para editar el objetivo", 401);
            }

            $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);

            if(!$oObjetivo->isEditable()){
                $this->getJsonHelper()->setMessage("El plazo para editar el objetivo ha expirado.");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oRelevancia = SeguimientosController::getInstance()->getRelevanciaById($this->getRequest()->getPost("relevancia"));
            $oObjetivo->setRelevancia($oRelevancia);
            $dEstimacion = Utils::fechaAFormatoSQL($this->getRequest()->getPost("estimacion"));
            $oObjetivo->setEstimacion($dEstimacion);

            SeguimientosController::getInstance()->guardarObjetivoAprendizajeSeguimientoScc($oObjetivo, $iSeguimientoId);

            $this->getJsonHelper()->setValor("modificarObjetivo", "1");
            $this->getJsonHelper()->setMessage("Los cambios se han guardado con éxito");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function verObjetivo()
    {
        try{
            if(!$this->getAjaxHelper()->isAjaxContext()){
                throw new Exception("", 404);
            }

            $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
            $iObjetivoId = $this->getRequest()->getParam('iObjetivoId');
            $tipoObjetivoIdForm = $this->getRequest()->getParam('tipoObjetivo');

            if(empty($iSeguimientoId) || empty($iObjetivoId) || empty($tipoObjetivoIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
            }

            if($tipoObjetivoIdForm == "ObjetivoPersonalizado"){
                if(!SeguimientosController::getInstance()->isObjetivoPersonalizadoUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoPersonalizadoById($iObjetivoId);
            }else{
                if(!SeguimientosController::getInstance()->isObjetivoAprendizajeUsuario($iObjetivoId)){
                    throw new Exception("No tiene permiso para editar el objetivo", 401);
                }
                $oObjetivo = SeguimientosController::getInstance()->getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoId, $iObjetivoId);
            }

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "popUpContent", "VerObjetivoBlock");

            $this->getTemplate()->set_var("dEstimacion", $oObjetivo->getEstimacion(true));

            if($oObjetivo->isLogrado()){
                $this->getTemplate()->set_var("EstimacionBlock", "");
                $this->getTemplate()->set_var("EstimacionVencida", "");
                $this->getTemplate()->set_var("dFechaLogrado", $oObjetivo->getUltimaEvolucion()->getFecha(true));
            }else{
                $this->getTemplate()->set_var("FechaLogradoBlock", "");
                if(!$oObjetivo->isEstimacionVencida()){
                    $this->getTemplate()->set_var("EstimacionVencida", "");
                }else{
                    $this->getTemplate()->set_var("expiradaClass", "txt_cuidado");
                }
            }

            switch($oObjetivo->getRelevancia()->getDescripcion())
            {
                case "baja":{
                    $iconoRelevanciaBlock = "IconoRelevanciaBaja32Block";
                    break;
                }
                case "normal":{
                    $iconoRelevanciaBlock = "IconoRelevanciaNormal32Block";
                    break;
                }
                case "alta":{
                    $iconoRelevanciaBlock = "IconoRelevanciaAlta32Block";
                    break;
                }
            }
            
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/objetivos.gui.html", "iconoRelevancia", $iconoRelevanciaBlock);
            $this->getTemplate()->set_var("iconoRelevancia", $this->getTemplate()->pparse("iconoRelevancia"));
            $this->getTemplate()->set_var("sRelevancia", $oObjetivo->getRelevancia()->getDescripcion());

            $oEje = $oObjetivo->getEje();
            if($oObjetivo->isObjetivoPersonalizado()){
                $this->getTemplate()->set_var("sDescripcionEje", $oObjetivo->getEje()->getDescripcion());
                $this->getTemplate()->set_var("EjeTematicoBlock", "");
            }

            if($oObjetivo->isObjetivoAprendizaje()){                                                                
                $this->getTemplate()->set_var("sNivel", $oObjetivo->getEje()->getArea()->getCiclo()->getNivel()->getDescripcion());
                $this->getTemplate()->set_var("sCiclo", $oObjetivo->getEje()->getArea()->getCiclo()->getDescripcion());
                $this->getTemplate()->set_var("sArea", $oObjetivo->getEje()->getArea()->getDescripcion());
                $this->getTemplate()->set_var("sDescripcionEje", $oObjetivo->getEje()->getDescripcion());

                $this->getTemplate()->set_var("EjePersonalizadoBlock", "");
            }
                        
            $this->getTemplate()->set_var("sDescripcionObjetivo", $oObjetivo->getDescripcion(true));

            //iteracion con los elementos de la evolucion
            $aEvolucion = $oObjetivo->getEvolucion();
            if(count($aEvolucion) > 0){
                $this->getTemplate()->set_var("EvolucionNoRecords", "");
                foreach($aEvolucion as $oEvolucion){
                    $sFecha = $oEvolucion->getFecha(true);
                    $hrefEntradaSeguimiento = $this->getRequest()->getBaseUrl()."/seguimientos/entradas/".$iSeguimientoId."-".$sFecha;
                    $this->getTemplate()->set_var("hrefEntradaSeguimiento", $hrefEntradaSeguimiento);
                    $this->getTemplate()->set_var("sFecha", $sFecha);
                    $this->getTemplate()->set_var("iEvolucion", $oEvolucion->getProgreso());

                    if($oEvolucion->isObjetivoLogrado()){
                        $this->getTemplate()->set_var("sGoalClass", "goal");
                    }else{
                        $this->getTemplate()->set_var("sGoalClass", "");
                    }

                    $this->getTemplate()->parse("EvolucionFechaBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("EvolucionFechaBlock", "");                
            }
            $this->getTemplate()->parse("VerObjetivoBlock", true);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function editarPronostico()
    {
        $iSeguimientoId = $this->getRequest()->getParam('iSeguimientoId');
    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        try{
            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $aCurrentOptions[] = "currentOptionSeguimiento";
            $aCurrentOptions[] = "currentSubOptionEditarPronosticoSeguimiento";

            $this->setFrameTemplate()
                 ->setJSPronostico()
                 ->setHeadTag();

            self::setMenuDerechaVerSeguimiento($this->getTemplate(), $this, $aCurrentOptions);

            //para que pueda ser reutilizado en otras vistas
            SeguimientosControllerSeguimientos::setFichaPersonaSeguimiento($this->getTemplate(), $this->getUploadHelper(), $oSeguimiento->getDiscapacitado());

            IndexControllerSeguimientos::setCabecera($this->getTemplate());
            IndexControllerSeguimientos::setCenterHeader($this->getTemplate());
            $this->printMsgTop();

            //titulo seccion
            if($oSeguimiento->isSeguimientoPersonalizado()){
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_PERSONALIZADO_DESC);
            }else{
                $this->getTemplate()->set_var("tituloSeccion", self::TIPO_SEGUIMIENTO_SCC_DESC);
            }

            $this->getTemplate()->set_var("SubtituloSeccionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/pronostico.gui.html", "pageRightInnerMainCont", "FormularioBlock");

            $this->getTemplate()->set_var("iSeguimientoId", $iSeguimientoId);
            $this->getTemplate()->set_var("sPronostico", $oSeguimiento->getPronostico());

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
         }catch(Exception $e){
            print_r($e);
        }
    }

    public function procesarPronostico()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

    	if($this->getRequest()->has('guardarPronostico')){
            $this->guardarPronostico();
            return;
        }
    }

    private function guardarPronostico()
    {
        $iSeguimientoId = $this->getRequest()->getParam('idSeguimientoForm');

    	if(empty($iSeguimientoId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la accion", 401);
    	}

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oSeguimiento = SeguimientosController::getInstance()->getSeguimientoById($iSeguimientoId);

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if($oSeguimiento->getUsuarioId() != $iUsuarioId){
                throw new Exception("No tiene permiso para editar este seguimiento", 401);
            }

            $sPronostico = $this->getRequest()->getPost('pronostico');

            $oSeguimiento->setPronostico($sPronostico);
            $resultado = SeguimientosController::getInstance()->guardarSeguimiento($oSeguimiento);

            if($resultado){
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
           $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
}