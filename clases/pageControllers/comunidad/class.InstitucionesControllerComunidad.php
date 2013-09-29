<?php
/**
 * @author Rodrigo A. Rio
 */
class InstitucionesControllerComunidad extends PageControllerAbstract
{
    private $filtrosFormConfig = array('filtroNombre' => 'i.nombre',
                                       'filtroTipoInstitucion' => 'i.tipoInstitucion_id',
                                       'filtroPais' => 'pa.id',
                                       'filtroProvincia' => 'pr.id',
                                       'filtroCiudad' => 'i.ciudades_id');

    private $orderByConfig = array('nombre' => array('variableTemplate' => 'orderByNombre',
                                                     'orderBy' => 'i.nombre',
                                                     'order' => 'desc'),
                                   'tipo' => array('variableTemplate' => 'orderByTipoInstitucion',
                                                   'orderBy' => 'it.nombre',
                                                   'order' => 'desc'));
       
    private function setFrameTemplate(){
        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        return $this;
    }
    
    /**
     * Setea el Head para las vistas de Instituciones
     */
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setHeadTagInstitucion($oInstitucion)
    {
        $front = FrontController::getInstance();
        $parametros = $front->getPlugin('PluginParametros');
        $nombreSitio = $parametros->obtener('NOMBRE_SITIO');

        $tituloVista = $nombreSitio.' | '.$oInstitucion->getNombre();

        $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                      $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                      $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

        $descriptionVista = "Institución de ".$sUbicacion." relacionada con ".$oInstitucion->getNombreTipoInstitucion().".
                             Contacto a la dirección de email ".$oInstitucion->getEmail();

        $keywordsVista = $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getNombre().", ".$oInstitucion->getNombreTipoInstitucion();

        $this->getTemplate()->set_var("pathUrlBase", $this->getRequest()->getBaseTagUrl());
        $this->getTemplate()->set_var("sTituloVista", $tituloVista);
        $this->getTemplate()->set_var("sMetaDescription", $descriptionVista);
        $this->getTemplate()->set_var("sMetaKeywords", $keywordsVista);

        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    private function setMenuDerecha()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
        
        $this->getTemplate()->set_var("hrefListadoInstituciones", $this->getUrlFromRoute("comunidadInstitucionesIndex", true));
        $this->getTemplate()->set_var("hrefMisInstituciones", $this->getUrlFromRoute("comunidadInstitucionesMisInstituciones", true));
        $this->getTemplate()->set_var("hrefNuevaInstitucion", $this->getUrlFromRoute("comunidadInstitucionesNueva", true));

        return $this;
    }

    /**
     * Establece descripcion de Instituciones y el menu con 2 opciones,
     * estado de Instituciones enviadas y formulario para enviar nueva Institucion
     */
    public function index()
    {
        $this->listadoInstituciones();
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('borrarInstitucion')){
            $this->borrarInstitucion();
            return;
        }
        
        if($this->getRequest()->has('solicitarInstitucionForm')){
            $this->solicitarInstitucionForm();
            return;
        }

        if($this->getRequest()->has('solicitarInstitucionProcesar')){
            $this->solicitarInstitucionProcesar();
            return;
        }
    }

    private function solicitarInstitucionProcesar()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionIdForm');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            
            if(ComunidadController::getInstance()->existeSolicitudInstitucion($iInstitucionId, $iUsuarioId))
            {
                $this->getJsonHelper()->setMessage("Ya hay una solicitud pendiente de moderación");
                $this->getJsonHelper()->setSuccess(false);
                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            $oSolicitud = new stdClass();
            $oSolicitud->oUsuario = $perfil->getUsuario();
            $oSolicitud->sMensaje = $this->getRequest()->getPost('mensaje');
            $oSolicitud = Factory::getSolicitudInstance($oSolicitud);

            $oInstitucion->addSolicitud($oSolicitud);
            ComunidadController::getInstance()->guardarSolicitudesInstitucion($oInstitucion);
            $this->getJsonHelper()->setSuccess(true);
            $this->getJsonHelper()->sendJsonAjaxResponse();

        }catch(Exception $e){
            $this->getJsonHelper()->setMessage("Hubo un error al tratar de procesar la solicitud");
            $this->getJsonHelper()->setSuccess(false);
            $this->getJsonHelper()->sendJsonAjaxResponse();
        }       
    }

    private function solicitarInstitucionForm()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "popUpContent", "FormularioSolicitarInstitucionBlock");

        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function borrarInstitucion()
    {
        $iInstitucionId = $this->getRequest()->getPost('iInstitucionId');

        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            //si la institucion no la creo el usuario entonces no muestro la vista.
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if(null === $oInstitucion->getUsuario() || $oInstitucion->getUsuario()->getId() != $iUsuarioId){
                throw new Exception("No tiene permiso para borrar esta institucion", 401);
            }

            $result = ComunidadController::getInstance()->borrarInstitucion($iInstitucionId);

            $this->restartTemplate();

            if($result){
                $msg = "La institución fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la institución del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la institución del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    /**
     * Procesa el envio desde un formulario de Institucion.
     */
    public function guardar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404);}

        if($this->getRequest()->has('crearInstitucion')){
            $this->crearInstitucionProcesar();
            return;
        }

        if($this->getRequest()->has('modificarInstitucion')){
            $this->modificarInstitucionProcesar();
            return;
        }
    }

    private function crearInstitucionProcesar()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $oUsuario = $perfil->getUsuario();

            $oInstitucion = new stdClass();
            
            $oInstitucion->sNombre = $this->getRequest()->getPost('nombre');
            $oInstitucion->sDescripcion	= $this->getRequest()->getPost('descripcion');
            $oInstitucion->iTipoInstitucion = $this->getRequest()->getPost('tipo');
            $oInstitucion->sCargo = $this->getRequest()->getPost('cargo');
            $oInstitucion->sPersoneriaJuridica = $this->getRequest()->getPost('personeriaJuridica');
            $oInstitucion->sDireccion = $this->getRequest()->getPost('direccion');
            $oCiudad = ComunidadController::getInstance()->getCiudadById($this->getRequest()->getPost('ciudad'));
            $oInstitucion->oCiudad = $oCiudad;
            $oInstitucion->iCiudadId = $this->getRequest()->getPost('ciudad');
            $oInstitucion->sTelefono = $this->getRequest()->getPost('telefono');
            $oInstitucion->sSitioWeb = $this->getRequest()->getPost('sitioWeb');
            $oInstitucion->sEmail = $this->getRequest()->getPost('email');
            $oInstitucion->sHorariosAtencion = $this->getRequest()->getPost('horariosAtencion');
            $oInstitucion->sSedes = $this->getRequest()->getPost('sedes');
            $oInstitucion->sAutoridades	= $this->getRequest()->getPost('autoridades');
            $oInstitucion->sActividadesMes = $this->getRequest()->getPost('actividadesMes');
            $oInstitucion->sLatitud = $this->getRequest()->getPost('latitud');
            $oInstitucion->sLongitud = $this->getRequest()->getPost('longitud');
            $oInstitucion->oUsuario = $oUsuario;
            $oInstitucion = Factory::getInstitucionInstance($oInstitucion);

            ComunidadController::getInstance()->guardarInstitucion($oInstitucion);

            $this->getJsonHelper()->setValor("agregarInstitucion", "1");
            $this->getJsonHelper()->setMessage("La institucion se creo exitosamente en el sistema");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarInstitucionProcesar()
    {
        try{                        
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iInstitucionId = $this->getRequest()->getPost('institucionId');
            if(empty($iInstitucionId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            //si la institucion no la creo el usuario entonces no muestro la vista.
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if(null === $oInstitucion->getUsuario() || $oInstitucion->getUsuario()->getId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta institucion", 401);
            }

            $oInstitucion->setNombre($this->getRequest()->getPost('nombre'));
            $oInstitucion->setDescripcion($this->getRequest()->getPost('descripcion'));
            $oInstitucion->setTipoInstitucion( $this->getRequest()->getPost('tipo'));
            $oInstitucion->setCargo($this->getRequest()->getPost('cargo'));
            $oInstitucion->setPersoneriaJuridica($this->getRequest()->getPost('personeriaJuridica'));
            $oInstitucion->setDireccion($this->getRequest()->getPost('direccion'));
            $oCiudad = ComunidadController::getInstance()->getCiudadById($this->getRequest()->getPost('ciudad'));
            $oInstitucion->setCiudad($oCiudad);
            $oInstitucion->setTelefono($this->getRequest()->getPost('telefono'));
            $oInstitucion->setSitioWeb($this->getRequest()->getPost('sitioWeb'));
            $oInstitucion->setAutoridades($this->getRequest()->getPost('autoridades'));
            $oInstitucion->setEmail($this->getRequest()->getPost('email'));
            $oInstitucion->setSedes($this->getRequest()->getPost('sedes'));
            $oInstitucion->setHorariosAtencion($this->getRequest()->getPost('horariosAtencion'));
            $oInstitucion->setActividadesMes($this->getRequest()->getPost('actividadesMes'));
            $oInstitucion->setLatitud($this->getRequest()->getPost('latitud'));
            $oInstitucion->setLongitud( $this->getRequest()->getPost('longitud'));
                       
            ComunidadController::getInstance()->guardarInstitucion($oInstitucion);

            $this->getJsonHelper()->setValor("modificarInstitucion", "1");
            $this->getJsonHelper()->setMessage("La institucion se modifico exitosamente");
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }
    
    public function nuevaInstitucion()
    {
        $this->formularioInstitucion();
    }

    public function editarInstitucion()
    {
        $this->formularioInstitucion();
    }

    private function formularioInstitucion()
    {
        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();
        
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FormularioBlock");
        $this->getTemplate()->set_var("tituloSeccion", "Instituciones Comunidad");

        //AGREGAR INSTITUCION
        if($this->getRequest()->getActionName() == "nuevaInstitucion"){

            $this->getTemplate()->unset_blocks("SubmitModificarInstitucionBlock");

            $sTituloForm = "Agregar una nueva institución";

            //valores por defecto en el agregar
            $oInstitucion = null;
            $sNombre = "";
            $sDescripcion = "";
            $iTipoInstitucion = "";
            $sCargo = "";
            $sPersoneriaJuridica = "";
            $sDireccion = "";
            $iPaisId = "";
            $iProvinciaId = "";
            $iCiudadId = "";
            $sEmail = "";
            $sTelefono = "";
            $sSitioWeb = "";
            $sHorariosAtencion = "";
            $sSedes = "";
            $sAutoridades = "";
            $sActividadesMes = "";
            $sLatitud = "";
            $sLongitud = "";

        //MODIFICAR INSTITUCION
        }else{

            $iInstitucionId = $this->getRequest()->getParam('institucionId');
            if(empty($iInstitucionId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            //si la institucion no la creo el usuario entonces no muestro la vista.
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $iUsuarioId = $perfil->getUsuario()->getId();
            if(null === $oInstitucion->getUsuario() || $oInstitucion->getUsuario()->getId() != $iUsuarioId){
                throw new Exception("No tiene permiso para modificar esta institucion", 401);
            }
             
            $this->getTemplate()->unset_blocks("SubmitCrearInstitucionBlock");

            $sTituloForm = "Modificar Institución";

            $sNombre = $oInstitucion->getNombre();
            $sDescripcion = $oInstitucion->getDescripcion();
            $iTipoInstitucion = $oInstitucion->getTipoInstitucionId();
            $sCargo = $oInstitucion->getCargo();
            $sPersoneriaJuridica = $oInstitucion->getPersoneriaJuridica();
            $sDireccion = $oInstitucion->getDireccion();
            $sEmail = $oInstitucion->getEmail();
            $sTelefono = $oInstitucion->getTelefono();
            $sSitioWeb = $oInstitucion->getSitioWeb();
            $sHorariosAtencion = $oInstitucion->getHorariosAtencion();
            $sSedes = $oInstitucion->getSedes();
            $sAutoridades = $oInstitucion->getAutoridades();
            $sActividadesMes = $oInstitucion->getActividadesMes();
            $sLatitud = $oInstitucion->getLatitud();
            $sLongitud = $oInstitucion->getLongitud();

            $iPaisId = "";
            $iProvinciaId = "";
            $iCiudadId = "";
            if(null != $oInstitucion->getCiudad()){
                $iCiudadId = $oInstitucion->getCiudad()->getId();
                if(null != $oInstitucion->getCiudad()->getProvincia()){
                $iProvinciaId = $oInstitucion->getCiudad()->getProvincia()->getId();
                    if(null != $oInstitucion->getCiudad()->getProvincia()->getPais()){
                        $iPaisId = $oInstitucion->getCiudad()->getProvincia()->getPais()->getId();
                    }
                }
            }

            $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);
        }
        
        $arrayPaises = array();
        $iRecordsTotalPais = 0;
        $listaPaises = ComunidadController::getInstance()->listaPaises($arrayPaises, $iRecordsTotalPais, null,  null,  null,  null);
        foreach ($listaPaises as $oPais){
            if($iPaisId == $oPais->getId()){
                $this->getTemplate()->set_var("sPaisSelect", "selected='selected'");
            }else{
                $this->getTemplate()->set_var("sPaisSelect", "");
            }
            $this->getTemplate()->set_var("iPaisId", $oPais->getId());
            $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
            $this->getTemplate()->parse("ListaPaisesBlock", true);
        }

        if(!empty($iPaisId)){
            $listaProvincias = ComunidadController::getInstance()->listaProvinciasByPais($iPaisId);
            foreach ($listaProvincias as $oProvincia){
                if($iProvinciaId == $oProvincia->getId()){
                    $this->getTemplate()->set_var("sProvinciaSelect", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sProvinciaSelect", "");
                }
                $this->getTemplate()->set_var("iProvinciaId", $oProvincia->getId());
                $this->getTemplate()->set_var("sProvinciaNombre", $oProvincia->getNombre());
                $this->getTemplate()->parse("ListaProvinciasBlock", true);
            }
        }
        
        if(!empty($iProvinciaId)){
            $listaCiudades = ComunidadController::getInstance()->listaCiudadByProvincia($iProvinciaId);            
            foreach($listaCiudades as $oCiudad){
                if($iCiudadId == $oCiudad->getId()){
                    $this->getTemplate()->set_var("sCiudadSelect", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sCiudadSelect", "");
                }
                $this->getTemplate()->set_var("iCiudadId", $oCiudad->getId());
                $this->getTemplate()->set_var("sCiudadNombre", $oCiudad->getNombre());
                $this->getTemplate()->parse("ListaCiudadesBlock", true);
            }
        }

        $iRecordsTotal = 0;
        $aTipoInstitucion = ComunidadController::getInstance()->listaTiposDeInstitucion($filtro = array(), $iRecordsTotal, null, null, null, null);
        foreach ($aTipoInstitucion as $oTipoInstitucion){
            if($oTipoInstitucion->iId == $iTipoInstitucion){
                $this->getTemplate()->set_var("sSelectedTipoInstitucion", "selected='selected'");
            }else{
                $this->getTemplate()->set_var("sSelectedTipoInstitucion", "");
            }
            $this->getTemplate()->set_var("iTipoInstitucionValue", $oTipoInstitucion->iId);
            $this->getTemplate()->set_var("sTipoInstitucion", $oTipoInstitucion->sNombre);
            $this->getTemplate()->parse("OptionSelectTipoInstitucion", true);
        }

        $this->getTemplate()->set_var("sTituloForm", $sTituloForm);
        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sDescripcion", $sDescripcion);
        $this->getTemplate()->set_var("sCargo", $sCargo);
        $this->getTemplate()->set_var("sPersoneriaJuridica", $sPersoneriaJuridica);
        $this->getTemplate()->set_var("sDireccion", $sDireccion);
        $this->getTemplate()->set_var("sEmail", $sEmail);
        $this->getTemplate()->set_var("sTelefono", $sTelefono);
        $this->getTemplate()->set_var("sSitioWeb", $sSitioWeb);
        $this->getTemplate()->set_var("sHorariosAtencion", $sHorariosAtencion);
        $this->getTemplate()->set_var("sSedes", $sSedes);
        $this->getTemplate()->set_var("sAutoridades", $sAutoridades);
        $this->getTemplate()->set_var("sActividadesMes", $sActividadesMes);
        $this->getTemplate()->set_var("sLatitud", $sLatitud);
        $this->getTemplate()->set_var("sLongitud", $sLongitud);
        	
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    public function listadoInstituciones()
    {
        $this->setFrameTemplate()
             ->setHeadTag()
             ->setMenuDerecha();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        $this->printMsgTop();

        $this->getTemplate()->set_var("tituloSeccion", "Instituciones Comunidad");
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "ListadoInstitucionesBlock");

        ///////////// ARMO LOS SELECTS DEL FORMULARIO DEL FILTRO

        $iRecordsTotalPais = 0;
        $listaPaises = ComunidadController::getInstance()->listaPaises($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($listaPaises as $oPais){
            $this->getTemplate()->set_var("iPaisId", $oPais->getId());
            $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
            $this->getTemplate()->parse("ListaPaisesBlock", true);
        }

        $vListaInstitucionTipos	= ComunidadController::getInstance()->listaTiposDeInstitucion($filtro = array(), $iRecordsTotalPais, null,  null,  null,  null);
        foreach($vListaInstitucionTipos as $oInstitucionTipo){
            $this->getTemplate()->set_var("iInstitucionTiposId", $oInstitucionTipo->iId);
            $this->getTemplate()->set_var("sInstitucionTiposNombre", $oInstitucionTipo->sNombre);
            $this->getTemplate()->parse("ListaTipoDeInstitucionesBlock", true);
        }

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesComunidad($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);                                
            }

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            $params = array();
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/masInstituciones", "listadoInstitucionesResult", $params);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
            $this->getTemplate()->set_var("sNoRecords", "No hay instituciones cargadas en la comunidad");
            $this->getTemplate()->parse("noRecords", false);
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    public function masInstituciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "ajaxFichasInstitucionesBlock", "FichasInstitucionesBlock");

        $this->initFiltrosForm($filtroSql, $paramsPaginador, $this->filtrosFormConfig);

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesComunidad($filtroSql, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            foreach($aInstituciones as $oInstitucion){

                $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                              $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

                $this->getTemplate()->set_var("sTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sUbicacion", $sUbicacion);
                $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->parse("InstitucionBlock", true);
            }

            $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");

            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/masInstituciones", "listadoInstitucionesResult", $paramsPaginador);
        }else{
            $this->getTemplate()->set_var("InstitucionBlock", "");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron instituciones");
            $this->getTemplate()->parse("noRecords", false);
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxFichasInstitucionesBlock', false));
    }

    public function ampliarInstitucion()
    {
    	try{
            $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
            $sTituloUrlized = $this->getRequest()->getParam('sTituloUrlized');

            //validacion 1.
            if(empty($iInstitucionId))
            {
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            //validacion 2.
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            if(null === $oInstitucion)
            {
                throw new Exception("", 404);
            }

            //validacion 3.
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
            if(count($oInstitucion->getDenuncias()) >= $iCantMaxDenuncias){
                $this->getRedirectorHelper()->setCode(307);
                $url = $this->getUrlFromRoute("comunidadSoftwareIndex");
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            //validacion 4.
            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
            if($sTituloUrlized != $sTituloUrlizedActual){
                $this->getRedirectorHelper()->setCode(301);
                $url = 'comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrlizedActual;
                $this->getRedirectorHelper()->gotoUrl($url);
            }

            $this->setFrameTemplate()
                 ->setHeadTagInstitucion($oInstitucion)
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            //titulo seccion
            $this->getTemplate()->set_var("tituloSeccion", "Instituciones Comunidad");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "FichaInstitucionBlock");

            $oUsuarioSesion = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            if(null !== $oInstitucion->getUsuario())
            {
                $this->getTemplate()->set_var("SolicitarInstitucionBlock", "");
                $this->getTemplate()->set_var("SolicitudEnviadaInstitucionBlock", "");
            }else{
                if(ComunidadController::getInstance()->existeSolicitudInstitucion($iInstitucionId, $oUsuarioSesion->getId()))
                {
                    $this->getTemplate()->set_var("SolicitarInstitucionBlock", "");
                }else{
                    $this->getTemplate()->set_var("SolicitudEnviadaInstitucionBlock", "");
                }
            }

            $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();


            $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
            $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());            
            $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
            $this->getTemplate()->set_var("sUbicacion", $sUbicacion);                       
            $this->getTemplate()->set_var("sDescripcion", $oInstitucion->getDescripcion(true));

            $sActividadesMes = (null !== $oInstitucion->getActividadesMes(true))? $oInstitucion->getActividadesMes(true) : " - ";
            $sAutoridades = (null !== $oInstitucion->getAutoridades(true))? $oInstitucion->getAutoridades(true) : " - ";
            $sSedes = (null !== $oInstitucion->getSedes(true))? $oInstitucion->getSedes(true) : " - ";
            $sHorariosAtencion = (null !== $oInstitucion->getHorariosAtencion())? $oInstitucion->getHorariosAtencion() : " - ";

            $this->getTemplate()->set_var("sActividadesMes", $sActividadesMes);
            $this->getTemplate()->set_var("sAutoridades", $sAutoridades);
            $this->getTemplate()->set_var("sSedes", $sSedes);
            $this->getTemplate()->set_var("sHorariosAtencion", $sHorariosAtencion);
            $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());
            $this->getTemplate()->set_var("sTelefono", $oInstitucion->getTelefono());            
            $this->getTemplate()->set_var("sDireccion", $oInstitucion->getDireccion());
            
            if(null !== $oInstitucion->getSitioWeb()){
                $this->getTemplate()->set_var("sSitioWeb", $oInstitucion->getSitioWeb());
            }else{
                $this->getTemplate()->set_var("SitioWebBlock", "");                                
            }

            if(null === $oInstitucion->getLatitud() && null === $oInstitucion->getLongitud()){
                $this->getTemplate()->set_var("MapaInstitucionBlock", "");                
            }else{
                $this->getTemplate()->set_var("sLatitud", $oInstitucion->getLatitud());
                $this->getTemplate()->set_var("sLongitud", $oInstitucion->getLongitud());
            }

            //listado de integrantes asociados a la institucion
            $aUsuarios = ComunidadController::getInstance()->obtenerUsuariosAsociadosInstitucion($iInstitucionId);
            
            if(count($aUsuarios) > 0){
                $this->getTemplate()->set_var("IntegranteNoRecords", "");
                
                foreach($aUsuarios as $oUsuario){

                    //foto de perfil actual
                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                    if(null != $oUsuario->getFotoPerfil()){
                        $oFoto = $oUsuario->getFotoPerfil();
                        $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                        $this->getTemplate()->set_var("hrefFotoPerfil", $pathFotoServidorBigSize);
                    }else{
                        $this->getTemplate()->set_var("hrefFotoPerfil", $scrAvatarAutor);
                    }                                        
                    $this->getTemplate()->set_var("scrAvatarAutor", $scrAvatarAutor);

                    $sNombreUsuario = $oUsuario->getNombre()." ".$oUsuario->getApellido();
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("sEmail", $oUsuario->getEmail());

                    $aPrivacidad = $oUsuario->obtenerPrivacidad();

                    if($aPrivacidad['telefono'] == 'comunidad' && null !== $oUsuario->getTelefono()){
                        $this->getTemplate()->set_var("sTelefono", $oUsuario->getTelefono());
                        $this->getTemplate()->parse("TelefonoBlock");
                    }else{
                        $this->getTemplate()->set_var("TelefonoBlock", "");
                    }

                    if($aPrivacidad['celular'] == 'comunidad' && null !== $oUsuario->getCelular()){
                        $this->getTemplate()->set_var("sCelular", $oUsuario->getCelular());
                        $this->getTemplate()->parse("CelularBlock");                        
                    }else{
                        $this->getTemplate()->set_var("CelularBlock", "");
                    }

                    if($aPrivacidad['fax'] == 'comunidad' && null !== $oUsuario->getFax()){
                        $this->getTemplate()->set_var("sFax", $oUsuario->getFax());
                        $this->getTemplate()->parse("FaxBlock");                        
                    }else{
                        $this->getTemplate()->set_var("FaxBlock", "");
                    }

                    if($aPrivacidad['curriculum'] == 'comunidad' && null !== $oUsuario->getCurriculumVitae()){
                        $hrefDescargarCv = "";
                        $oArchivo = $oUsuario->getCurriculumVitae();
                        $hrefDescargarCv = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
                        $this->getTemplate()->set_var("hrefDescargarCv", $hrefDescargarCv);
                        $this->getTemplate()->parse("CvBlock");
                    }else{
                        $this->getTemplate()->set_var("CvBlock", "");
                    }
                    
                    $this->getTemplate()->parse("IntegranteBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("IntegranteBlock", "");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw $e;
        }
     }
     
    /**
     * Devuelve las instituciones para el autocomplete de la busqueda de instituciones
     */
    public function buscarInstituciones()
    {
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }

        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            $iRecordsTotal = 0;
            $sOrderBy=$sOrder=$iIniLimit=$iRecordCount=null;
            $filtro = array("i.nombre" => $this->getRequest()->get('str'));
            $vInstituciones = ComunidadController::getInstance()->obtenerInstituciones($filtro, $iRecordsTotal,$sOrderBy,$sOrder,$iIniLimit,$iRecordCount);
            $vResult = array();
            if(count($vInstituciones)>0){
                foreach($vInstituciones as $oInstitucion){
                    $obj = new stdClass();
                    $obj->id = $oInstitucion->getId();
                    $obj->nombre = $oInstitucion->getNombre();
                    $vResult[] = $obj;
                }
            }
            //agrega una url para que el js redireccione
            $this->getJsonHelper()->setSuccess(true)->setValor("instituciones", $vResult);
         }catch(Exception $e){
            print_r($e);
        }

        //setea headers y body en el response con los valores codificados
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function misInstituciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag()
                 ->setMenuDerecha();

            IndexControllerComunidad::setCabecera($this->getTemplate());
            IndexControllerComunidad::setCenterHeader($this->getTemplate());

            $this->printMsgTop();

            $this->getTemplate()->set_var("tituloSeccion", "Mis Instituciones");
            $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "pageRightInnerMainCont", "ListadoMisInstitucionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
            $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

            $iRecordsTotal = 0;
            $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            if(count($aInstituciones) > 0){

                $this->getTemplate()->set_var("NoRecordsMisInstitucionesBlock", "");

                foreach($aInstituciones as $oInstitucion){

                    /*
                     * la url de publicacion ampliada es diferente segun el tipo
                     *
                     * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                     */
                    $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                    $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                    $this->getTemplate()->set_var("hrefEditarInstitucion", $this->getUrlFromRoute("comunidadInstitucionesEditarInstitucion", true)."?institucionId=".$oInstitucion->getId());

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sNombreTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sCargo", $oInstitucion->getCargo());

                    //mensajes adjuntos. por moderacion y por acumulacion de denuncias.
                    $sMensajesInstitucion = "";

                    if($oInstitucion->getModeracion()->isPendiente()){
                        $cartelModeracion = "MsgFichaInfoBlock";
                        $tituloModeracion = "Moderación Pendiente";
                        $mensajeModeracion = "La institución solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                    }

                    if($oInstitucion->getModeracion()->isRechazado()){
                        $cartelModeracion = "MsgFichaErrorBlock";
                        $tituloModeracion = "Institución Rechazada";
                        $mensajeModeracion = "La Institución no sera visible fuera de la comunidad de profesionales. Causa: ".$oInstitucion->getModeracion()->getMensaje(true);
                    }

                    if($oInstitucion->getModeracion()->isAprobado()){
                        $cartelModeracion = "MsgFichaCorrectoBlock";
                        $tituloModeracion = "Institución Aprobada";
                        $mensajeModeracion = "La institución es visible para cualquier visitante fuera de la comunidad de profesionales, su contenido fue aprobado.";
                    }

                    $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeInstitucionModeracion", $cartelModeracion);
                    $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                    $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                    $this->getTemplate()->set_var("class", "mabo");
                    $sMensajesInstitucion = $this->getTemplate()->pparse("sMensajeInstitucionModeracion");

                    //puede agregarse un mensaje por acumulacion de denuncias.
                    $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
                    $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');
                    if(count($oInstitucion->getDenuncias()) >= $iCantMaxDenuncias){
                        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeInstitucionDenuncias", "MsgFichaErrorBlock");
                        $this->getTemplate()->set_var("sTituloMsgFicha", "Acumulación de denuncias");
                        $this->getTemplate()->set_var("sMsgFicha", "La institución se ha quitado de los listados temporalmente por acumulación de denuncias.");
                        $sMensajesInstitucion .= $this->getTemplate()->pparse("sMensajeInstitucionDenuncias");
                    }

                    $this->getTemplate()->set_var("sMensajeInstitucion", $sMensajesInstitucion);
                    
                    $this->getTemplate()->parse("MiInstitucionBlock", true);

                    $this->getTemplate()->set_var("sMensajeInstitucion","");
                }

                $params = array();
                $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/instituciones/mas-mis-instituciones", "listadoMisInstitucionesResult", $params);

            }else{
                $this->getTemplate()->set_var("MiInstitucionBlock", "");
                $this->getTemplate()->set_var("sNoRecords", "Todavía no hay instituciones creadas.");
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            echo $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }

    public function masMisInstituciones()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/instituciones.gui.html", "ajaxGrillaInstitucionesBlock", "GrillaMisInstitucionesBlock");

        list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();
        $this->initOrderBy($sOrderBy, $sOrder, $this->orderByConfig);

        $iRecordsTotal = 0;
        $aInstituciones = ComunidadController::getInstance()->buscarInstitucionesUsuario($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

        if(count($aInstituciones) > 0){

            $this->getTemplate()->set_var("NoRecordsMisInstitucionesBlock", "");

            foreach($aInstituciones as $oInstitucion){

                /*
                 * la url de publicacion ampliada es diferente segun el tipo
                 *
                 * http://domain.com/comunidad/instituciones/32-Nombre de la institucion
                 */
                $sTituloUrl = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
                $this->getTemplate()->set_var("hrefAmpliarInstitucion", $this->getRequest()->getBaseUrl().'/comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrl);

                $this->getTemplate()->set_var("hrefEditarInstitucion", $this->getUrlFromRoute("comunidadInstitucionesEditarInstitucion", true)."?institucionId=".$oInstitucion->getId());

                $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                $this->getTemplate()->set_var("sNombreTipoInstitucion", $oInstitucion->getNombreTipoInstitucion());
                $this->getTemplate()->set_var("sCargo", $oInstitucion->getCargo());

                if($oInstitucion->getModeracion()->isPendiente()){
                    $cartelModeracion = "MsgFichaInfoBlock";
                    $tituloModeracion = "Moderación Pendiente";
                    $mensajeModeracion = "La institución solo será visible por usuarios del sistema mientras se encuentre pendiente de moderación.";
                }

                if($oInstitucion->getModeracion()->isRechazado()){
                    $cartelModeracion = "MsgFichaErrorBlock";
                    $tituloModeracion = "Institución Rechazada";
                    $mensajeModeracion = "La Institución no sera visible fuera de la comunidad de profesionales. Causa: ".$oInstitucion->getModeracion()->getMensaje(true);
                }

                if($oInstitucion->getModeracion()->isAprobado()){
                    $cartelModeracion = "MsgFichaCorrectoBlock";
                    $tituloModeracion = "Institución Aprobada";
                    $mensajeModeracion = "La institución es visible para cualquier visitante fuera de la comunidad de profesionales, su contenido fue aprobado.";
                }

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "sMensajeInstitucion", $cartelModeracion);
                $this->getTemplate()->set_var("sTituloMsgFicha", $tituloModeracion);
                $this->getTemplate()->set_var("sMsgFicha", $mensajeModeracion);
                $this->getTemplate()->set_var("class", "mabo");
                $this->getTemplate()->parse("sMensajeInstitucion", false);

                $this->getTemplate()->parse("MiInstitucionBlock", true);

                $this->getTemplate()->set_var("sMensajeInstitucion","");
            }

            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "comunidad/instituciones/mas-mis-instituciones", "listadoMisInstitucionesResult", $paramsPaginador = array());
        }else{
            $this->getTemplate()->set_var("MiInstitucionBlock", "");
            $this->getTemplate()->set_var("sNoRecords", "No se encontraron instituciones.");
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaInstitucionesBlock', false));
    }

    public function denunciar()
    {                
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }
        
        if($this->getRequest()->has('enviarDenuncia')){
            $this->procesarDenuncia();
            return;
        }
        
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
                
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/componentes/formularios.gui.html", "popUpContent", "FormularioDenunciarBlock");

        //select razones denuncias
        $aRazones = ComunidadController::getInstance()->obtenerRazonesDenuncia();
        while($sRazon = current($aRazones)){
            $this->getTemplate()->set_var("sRazonValue", key($aRazones));
            $this->getTemplate()->set_var("sRazon", $sRazon);
            $this->getTemplate()->parse("OptionRazonBlock", true);
            next($aRazones);
        }

        $this->getTemplate()->set_var("iItemId", $iInstitucionId);
        $this->getTemplate()->set_var("sTipoItem", "Institucion");

        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    private function procesarDenuncia()
    {
        $iInstitucionId = $this->getRequest()->getParam('iItemIdFormDenuncia');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            //no se puede denunciar 2 veces la misma institucion
            if(ComunidadController::getInstance()->usuarioEnvioDenunciaInstitucion($iInstitucionId)){
                $msg = "Su denuncia ya fue enviada. No puede denunciar dos veces la misma institución.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
                $this->getTemplate()->set_var("sMensaje", $msg);
                $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

                $this->getJsonHelper()->sendJsonAjaxResponse();
                return;
            }

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $oUsuario = $perfil->getUsuario();
            
            $oDenuncia = new stdClass();

            $oDenuncia->sMensaje = $this->getRequest()->getPost('mensaje');
            $oDenuncia->sRazon = $this->getRequest()->getPost('razon');
            $oDenuncia->oUsuario = $oUsuario;

            $oDenuncia = Factory::getDenunciaInstance($oDenuncia);
            
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            $oInstitucion->addDenuncia($oDenuncia);           
            $result = ComunidadController::getInstance()->guardarDenuncias($oInstitucion);

            $this->restartTemplate();

            if($result){
                $msg = "Su denuncia fue enviada con éxito.";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha podido enviar su denuncia.";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha podido enviar su denuncia.";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();            
    }
}