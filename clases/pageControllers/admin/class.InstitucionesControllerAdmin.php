<?php
class InstitucionesControllerAdmin extends PageControllerAbstract
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
        $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "jsContent", "JsContent");

        return $this;
    }

    public function index(){
        $this->listarInstituciones();
    }

    public function listarInstituciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionInstituciones");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "widgetsContent", "HeaderBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "mainContent", "ListadoInstitucionesBlock");

            $iRecordsTotal = 0;
            $aInstituciones = ComunidadController::getInstance()->obtenerInstituciones($filtro = array(), $iRecordsTotal, null, null, null, null);

            if(count($aInstituciones) > 0){
                foreach($aInstituciones as $oInstitucion){
                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());
                    $this->getTemplate()->parse("InstitucionesBlock", true);                   
                }
                $this->getTemplate()->set_var("NoRecordsInstitucionesBlock", "");
            }else{
                $this->getTemplate()->set_var("InstitucionesBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsInstitucionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay instituciones cargadas en el sistema");
                $this->getTemplate()->parse("noRecords", false);
            }

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function form()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iInstitucionId = $this->getRequest()->getPost('iInstitucionId');

        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }
        
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "popUpContent", "FormularioInstitucionBlock");

        $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

        $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);

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
       
        $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));
    }

    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        if($this->getRequest()->has('eliminar')){
            $this->eliminarInstitucion();
            return;
        }

        if($this->getRequest()->has('masModeraciones')){
            $this->masModeraciones();
            return;
        }
        
        if($this->getRequest()->has('masSolicitudes')){
            $this->masSolicitudes();
            return;
        }

        if($this->getRequest()->has('modificarInstitucion')){
            $this->modificarInstitucion();
            return;
        }

        if($this->getRequest()->has('ampliarInstitucion')){
            $this->ampliar();
            return;
        }

        if($this->getRequest()->has('moderarInstitucion')){
            $this->moderarInstitucion();
            return;
        }

        if($this->getRequest()->has('toggleModeraciones')){
            $this->toggleModeraciones();
            return;
        }
        
        if($this->getRequest()->has('destituirIntegrante')){
            $this->destituirIntegrante();
            return;
        }

        if($this->getRequest()->has('solicitarAdministrarContenido')){
            $this->solicitarAdministrarContenido();
            return;
        }
        
        if($this->getRequest()->has('aprobarSolicitud')){
            $this->aprobarSolicitud();
            return;
        }       
    }

    /**
     * Modifica el valor booleano del parametro activar moderaciones
     * para el controlador software del modulo comunidad
     */
    private function toggleModeraciones()
    {
        $sValor = $this->getRequest()->getParam('sValor');

        //si o si tiene que ser boolean
        if($sValor != '1' && $sValor != '0'){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $oParametroControlador = AdminController::getInstance()->getParametroControladorByNombre('ACTIVAR_MODERACIONES', 'comunidad_instituciones');
        $oParametroControlador->setValor($sValor);
        AdminController::getInstance()->guardarParametroControlador($oParametroControlador);
    }

    /**
     * Este metodo es el que se ejecuta cuando se acepta la solicitud de administracino de contenido
     * desde el listado de solicitudes.
     */
    private function aprobarSolicitud()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        $iUsuarioId = $this->getRequest()->getParam('iUsuarioId');

        if(empty($iInstitucionId) || empty($iUsuarioId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            //esto es por si un moderador o administrador justo se asocio 
            if(null !== $oInstitucion->getUsuario()){
                $msg = "La institución ya posee un usuario administrador de contenido (vea en ficha ampliada)";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);

            }else{

                $oUsuario = ComunidadController::getInstance()->getUsuarioById($iUsuarioId);
                $oInstitucion->setUsuario($oUsuario);
                ComunidadController::getInstance()->guardarInstitucion($oInstitucion);

                $msg = "El usuario fue asignado correctamente a la institucion";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha procesado la solicitud para la institución";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->restartTemplate();
        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    /**
     * ESTE METODO ES PARA PISAR EL USUARIO ACTUAL DE UNA INSTITUCION Y NO ES EL QUE SE DISPARA
     * DESDE EL LISTADO DE MODERACION DE SOLICITUDES.
     * POR ESO NO SE COMPRUEBA QUE LA INSTITUCION TENGA EL USUARIO EN NULL.     
     */
    private function solicitarAdministrarContenido()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');

        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            $oInstitucion->setUsuario(SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario());
            ComunidadController::getInstance()->guardarInstitucion($oInstitucion);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function destituirIntegrante()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');

        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);
            $oInstitucion->setUsuario(null);
            ComunidadController::getInstance()->guardarInstitucion($oInstitucion);
            
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse(); 
    }

    private function ampliar()
    {
        if(!$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }

        $iInstitucionId = $this->getRequest()->getPost('iInstitucionId');

        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        try{
            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

            $sTituloUrlizedActual = $this->getInflectorHelper()->urlize($oInstitucion->getNombre());
            $sPermalink = 'comunidad/instituciones/'.$oInstitucion->getId()."-".$sTituloUrlizedActual;

            $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "popUpContent", "FichaInstitucionBlock");

            $sUbicacion = $oInstitucion->getCiudad()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getNombre()." ".
                          $oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre();

            $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

            //integrante administrador de contenido
            if(null !== $oInstitucion->getUsuario())
            {
                $this->getTemplate()->set_var("IntegranteAdministradorNoExisteBlock", "");

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oInstitucion->getUsuario()->getNombreAvatar();
                if(null != $oInstitucion->getUsuario()->getFotoPerfil()){
                    $oFoto = $oInstitucion->getUsuario()->getFotoPerfil();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("hrefFotoPerfilIntegranteAdministrador", $pathFotoServidorBigSize);
                }else{
                    $this->getTemplate()->set_var("hrefFotoPerfilIntegranteAdministrador", $scrAvatarAutor);
                }
                $this->getTemplate()->set_var("scrAvatarIntegranteAdministrador", $scrAvatarAutor);

                $sNombreUsuario = $oInstitucion->getUsuario()->getNombre()." ".$oInstitucion->getUsuario()->getApellido();
                $this->getTemplate()->set_var("sNombreUsuarioAdministrador", $sNombreUsuario);
                $this->getTemplate()->set_var("sEmailAdministrador", $oInstitucion->getUsuario()->getEmail());

                if(null !== $oInstitucion->getUsuario()->getTelefono()){
                    $this->getTemplate()->set_var("sTelefonoAdministrador", $oInstitucion->getUsuario()->getTelefono());
                }else{
                    $this->getTemplate()->set_var("sTelefonoAdministrador", " - ");
                }

                if(null !== $oInstitucion->getUsuario()->getCelular()){
                    $this->getTemplate()->set_var("sCelularAdministrador", $oInstitucion->getUsuario()->getCelular());
                }else{
                    $this->getTemplate()->set_var("sCelularAdministrador", " - ");
                }

                if(null !== $oInstitucion->getUsuario()->getFax()){
                    $this->getTemplate()->set_var("sFaxAdministrador", $oInstitucion->getUsuario()->getFax());
                }else{
                    $this->getTemplate()->set_var("sFaxAdministrador", " - ");
                }

                if(null !== $oInstitucion->getUsuario()->getCurriculumVitae()){
                    $hrefDescargarCv = "";
                    $oArchivo = $oInstitucion->getUsuario()->getCurriculumVitae();
                    $hrefDescargarCv = $this->getRequest()->getBaseUrl().'/comunidad/descargar?nombreServidor='.$oArchivo->getNombreServidor();
                    $this->getTemplate()->set_var("hrefDescargarCvAdministrador", $hrefDescargarCv);
                    $this->getTemplate()->parse("CvAdministradorBlock");
                }else{
                    $this->getTemplate()->set_var("CvAdministradorBlock", "");
                }

                $this->getTemplate()->parse("IntegranteAdministradorBlock", true);
            }else{
                $this->getTemplate()->set_var("IntegranteAdministradorBlock", "");
            }

            $this->getTemplate()->set_var("sPermalink", $sPermalink);
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
                        $this->getTemplate()->set_var("hrefFotoPerfilIntegrante", $pathFotoServidorBigSize);
                    }else{
                        $this->getTemplate()->set_var("hrefFotoPerfilIntegrante", $scrAvatarAutor);
                    }
                    $this->getTemplate()->set_var("scrAvatarIntegrante", $scrAvatarAutor);

                    $sNombreUsuario = $oUsuario->getNombre()." ".$oUsuario->getApellido();
                    $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                    $this->getTemplate()->set_var("sEmail", $oUsuario->getEmail());

                    if(null !== $oUsuario->getTelefono()){
                        $this->getTemplate()->set_var("sTelefono", $oUsuario->getTelefono());
                    }else{
                        $this->getTemplate()->set_var("sTelefono", " - ");
                    }

                    if(null !== $oUsuario->getCelular()){
                        $this->getTemplate()->set_var("sCelular", $oUsuario->getCelular());
                    }else{
                        $this->getTemplate()->set_var("sCelular", " - ");
                    }

                    if(null !== $oUsuario->getFax()){
                        $this->getTemplate()->set_var("sFax", $oUsuario->getFax());
                    }else{
                        $this->getTemplate()->set_var("sFax", " - ");
                    }

                    if(null !== $oUsuario->getCurriculumVitae()){
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

            //listado de discapacitados asociados a la institucion
            $aDiscapacitados = ComunidadController::getInstance()->obtenerDiscapacitadosAsociadosInstitucion($iInstitucionId);

            if(count($aDiscapacitados) > 0){
                $this->getTemplate()->set_var("DiscapacitadoNoRecords", "");

                foreach($aDiscapacitados as $oDiscapacitado){

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oDiscapacitado->getNombreAvatar();
                    if(null != $oDiscapacitado->getFotoPerfil()){
                        $oFoto = $oDiscapacitado->getFotoPerfil();
                        $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                        $this->getTemplate()->set_var("hrefFotoPerfilPersona", $pathFotoServidorBigSize);
                    }else{
                        $this->getTemplate()->set_var("hrefFotoPerfilPersona", $scrAvatarAutor);
                    }
                    $this->getTemplate()->set_var("scrAvatarPersona", $scrAvatarAutor);

                    $sNombrePersona = $oDiscapacitado->getNombre()." ".$oDiscapacitado->getApellido();
                    $sSexo = ($oDiscapacitado->getSexo() == 'm')?"Masculino":"Femenino";
                    $sFechaNacimiento = Utils::fechaFormateada($oDiscapacitado->getFechaNacimiento(), "d/m/Y");
                    $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
                    $sDocumento = $aTiposDocumentos[$oDiscapacitado->getTipoDocumento()]." ".$oDiscapacitado->getNumeroDocumento();
                    
                    $this->getTemplate()->set_var("sNombrePersona", $sNombrePersona);
                    $this->getTemplate()->set_var("sDocumento", $sDocumento);
                    $this->getTemplate()->set_var("sSexo", $sSexo);
                    $this->getTemplate()->set_var("sFechaNacimiento", $sFechaNacimiento);

                    $this->getTemplate()->parse("DiscapacitadoBlock", true);
                }
            }else{
                $this->getTemplate()->set_var("DiscapacitadoBlock", "");
            }
            
            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('frame', false));

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }            
    }

    private function moderarInstitucion()
    {
        $iModeracionId = $this->getRequest()->getParam('iModeracionId');
        $sEstado = $this->getRequest()->getParam('estado');
        $sMensaje = $this->getRequest()->getParam('mensaje');

        if(empty($iModeracionId) || empty($sEstado) || empty($sMensaje)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{

            $oModeracion = AdminController::getInstance()->getModeracionById($iModeracionId);

            switch($sEstado)
            {
                case "aprobado": $oModeracion->setEstadoAprobado(); break;
                case "rechazado": $oModeracion->setEstadoRechazado(); break;
            }

            $oModeracion->setMensaje($sMensaje);

            $result = AdminController::getInstance()->guardarModeracion($oModeracion);

            $this->restartTemplate();

            if($result){
                $msg = "La institución fue moderada";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha procesado la moderacion en la institución";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function modificarInstitucion()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iInstitucionId = $this->getRequest()->getPost('institucionIdForm');
            if(empty($iInstitucionId)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }

            $oInstitucion = ComunidadController::getInstance()->getInstitucionById($iInstitucionId);

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

            $this->getJsonHelper()->setMessage("La institucion se modifico exitosamente");
            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function eliminarInstitucion()
    {
        $iInstitucionId = $this->getRequest()->getParam('iInstitucionId');
        if(empty($iInstitucionId)){
            throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
        }

        $this->getJsonHelper()->initJsonAjaxResponse();
        try{
            $result = ComunidadController::getInstance()->borrarInstitucion($iInstitucionId);

            $this->restartTemplate();

            if($result){
                $msg = "La institucion fue eliminada del sistema";
                $bloque = 'MsgCorrectoBlockI32';
                $this->getJsonHelper()->setSuccess(true);
            }else{
                $msg = "Ocurrio un error, no se ha eliminado la institucion del sistema";
                $bloque = 'MsgErrorBlockI32';
                $this->getJsonHelper()->setSuccess(false);
            }

        }catch(Exception $e){
            $msg = "Ocurrio un error, no se ha eliminado la institucion del sistema";
            $bloque = 'MsgErrorBlockI32';
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "html", $bloque);
        $this->getTemplate()->set_var("sMensaje", $msg);
        $this->getJsonHelper()->setValor("html", $this->getTemplate()->pparse('html', false));

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    public function listarModeraciones()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "widgetsContent", "HeaderModeracionesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "mainContent", "ListadoModeracionBlock");

            //check activar/desactivar moderaciones
            $oParametroControlador = AdminController::getInstance()->getParametroControladorByNombre('ACTIVAR_MODERACIONES', 'comunidad_instituciones');
            if($oParametroControlador->getValor()){
                $this->getTemplate()->set_var("moderacionesChecked", "checked='checked'");
            }

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesInstitucion($oInstitucion->getId());
                    //al menos 1 porque es un listado de instituciones con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());

                        $this->getTemplate()->parse("ModeracionHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");

                    $this->getTemplate()->parse("InstitucionModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay instituciones pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-procesar", "listadoModeracionesResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    private function masModeraciones()
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "ajaxGrillaModeracionesBlock", "GrillaModeracionesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesModeracion($filtro = null, $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aModeracion = AdminController::getInstance()->obtenerHistorialModeracionesInstitucion($oInstitucion->getId());
                    //al menos 1 porque es un listado de instituciones con moderacion pendiente.
                    foreach($aModeracion as $oModeracion){
                        $this->getTemplate()->set_var("sFechaModeracion", $oModeracion->getFecha(true));
                        $this->getTemplate()->set_var("sEstadoModeracion", $oModeracion->getEstado());

                        $sMensajeModeracion = $oModeracion->getMensaje(true);
                        if(empty($sMensajeModeracion)){ $sMensajeModeracion = " - "; }
                        $this->getTemplate()->set_var("sMensaje", $sMensajeModeracion);
                        $this->getTemplate()->set_var("iModeracionId", $oModeracion->getId());

                        $this->getTemplate()->parse("ModeracionHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->set_var("sEstadoAprobarValue", "aprobado");
                    $this->getTemplate()->set_var("sEstadoRechazarValue", "rechazado");

                    $this->getTemplate()->parse("InstitucionModerarBlock", true);
                    $this->getTemplate()->set_var("ModeracionHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsModeracionesBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionModerarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsModeracionesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay instituciones pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masModeraciones=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-procesar", "listadoModeracionesResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaModeracionesBlock', false));
        }catch(Exception $e){
            print_r($e);
        }
    }

    public function listarSolicitudes()
    {
        try{
            $this->setFrameTemplate()
                 ->setHeadTag();

            IndexControllerAdmin::setCabecera($this->getTemplate());
            IndexControllerAdmin::setMenu($this->getTemplate(), "currentOptionModeracion");

            $this->printMsgTop();

            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "widgetsContent", "HeaderSolicitudesBlock");
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "mainContent", "ListadoSolicitudesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesSolicitud($filtro = array(), $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aSolicitud = $oInstitucion->getSolicitudes();
                                       
                    //al menos 1 porque es un listado de instituciones con solicitudes pendientes.
                    foreach($aSolicitud as $oSolicitud){
                        $this->getTemplate()->set_var("sFechaSolicitud", $oSolicitud->getFecha(true));
                        $this->getTemplate()->set_var("sMensaje", $oSolicitud->getMensaje(true));
                        $this->getTemplate()->set_var("iSolicitudId", $oSolicitud->getId());

                        //usuario que realizo la solicitud
                        $oUsuario = $oSolicitud->getUsuario();
                        $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                        $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();
                        $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                        $this->getTemplate()->set_var("scrAvatarUsuario", $scrAvatarAutor);
                        $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);
                        
                        $this->getTemplate()->parse("SolicitudHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->parse("InstitucionSolicitarBlock", true);
                    $this->getTemplate()->set_var("SolicitudHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsSolicitudesBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionSolicitarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsSolicitudesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay solicitudes pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $params[] = "masSolicitudes=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-procesar", "listadoSolicitudesResult", $params);

            $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
        }catch(Exception $e){
            print_r($e);
        }        
    }

    private function masSolicitudes()
    {
        try{
            $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "ajaxGrillaSolicitudesBlock", "GrillaSolicitudesBlock");

            list($iItemsForPage, $iPage, $iMinLimit, $sOrderBy, $sOrder) = $this->initPaginator();

            $iRecordsTotal = 0;
            $aInstituciones = AdminController::getInstance()->buscarInstitucionesSolicitud($filtro = array(), $iRecordsTotal, $sOrderBy, $sOrder, $iMinLimit, $iItemsForPage);

            $this->getTemplate()->set_var("iRecordsTotal", $iRecordsTotal);

            if(count($aInstituciones) > 0){

                foreach($aInstituciones as $oInstitucion){

                    $this->getTemplate()->set_var("iInstitucionId", $oInstitucion->getId());

                    $this->getTemplate()->set_var("sNombre", $oInstitucion->getNombre());
                    $this->getTemplate()->set_var("sTipo", $oInstitucion->getNombreTipoInstitucion());
                    $this->getTemplate()->set_var("sUbicacion", $oInstitucion->getCiudad()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getNombre().", ".$oInstitucion->getCiudad()->getProvincia()->getPais()->getNombre());
                    $this->getTemplate()->set_var("sEmail", $oInstitucion->getEmail());

                    $aSolicitud = $oInstitucion->getSolicitudes();
                    
                    //al menos 1 porque es un listado de instituciones con solicitudes pendientes.
                    foreach($aSolicitud as $oSolicitud){
                        $this->getTemplate()->set_var("sFechaSolicitud", $oSolicitud->getFecha(true));
                        $this->getTemplate()->set_var("sMensaje", $oSolicitud->getMensaje(true));
                        $this->getTemplate()->set_var("iSolicitudId", $oSolicitud->getId());

                        //usuario que realizo la solicitud
                        $oUsuario = $oSolicitud->getUsuario();
                        $scrAvatarAutor = $this->getUploadHelper()->getDirectorioUploadFotos().$oUsuario->getNombreAvatar();
                        $sNombreUsuario = $oUsuario->getApellido().", ".$oUsuario->getNombre();
                        $this->getTemplate()->set_var("iUsuarioId", $oUsuario->getId());
                        $this->getTemplate()->set_var("scrAvatarUsuario", $scrAvatarAutor);
                        $this->getTemplate()->set_var("sNombreUsuario", $sNombreUsuario);

                        $this->getTemplate()->parse("SolicitudHistorialInstitucionBlock", true);
                    }

                    $this->getTemplate()->parse("InstitucionSolicitarBlock", true);
                    $this->getTemplate()->set_var("SolicitudHistorialInstitucionBlock", "");
                }

                $this->getTemplate()->set_var("NoRecordsSolicitudesBlock", "");

            }else{
                $this->getTemplate()->set_var("InstitucionSolicitarBlock", "");
                $this->getTemplate()->load_file_section("gui/vistas/admin/instituciones.gui.html", "noRecords", "NoRecordsSolicitudesBlock");
                $this->getTemplate()->set_var("sNoRecords", "No hay solicitudes pendientes de moderación");
                $this->getTemplate()->parse("noRecords", false);
            }

            $paramsPaginador[] = "masSolicitudes=1";
            $this->calcularPaginas($iItemsForPage, $iPage, $iRecordsTotal, "admin/instituciones-procesar", "listadoSolicitudesResult", $paramsPaginador);

            $this->getAjaxHelper()->sendHtmlAjaxResponse($this->getTemplate()->pparse('ajaxGrillaSolicitudesBlock', false));
        }catch(Exception $e){
            print_r($e);
        }
    }
}