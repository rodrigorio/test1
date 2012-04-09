<?php

/**
 * @author Matias Velilla
 * 
 */
class PersonasControllerSeguimientos extends PageControllerAbstract
{
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
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "jsContent", "JsContent");

        return $this;
    }
    
    public function index()
    {
        $this->listar();
    }

    public function agregar()
    {
        $this->mostrarFormularioPopUp();
    }

    public function modificar()
    {
        $this->mostrarFormularioPopUp();
    }

    private function mostrarFormularioPopUp()
    {
        $this->getTemplate()->load_file("gui/templates/index/framePopUp01-02.gui.html", "frame");
        
        //AGREGAR PERSONA
        if($this->getRequest()->getActionName() == "agregar"){
            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/personas.gui.html", "popUpContent", "FormularioBlock");
            $this->getTemplate()->unset_blocks("SubmitModificarPersonaBlock");
            $this->getTemplate()->unset_blocks("FotoPerfilActualBlock");

            //valores por defecto en el agregar
            $oDiscapacitado = null;
            $iPersonaIdForm = "";

            $iTipoDocumentoId = "";
            $sNumeroDocumento = "Numero";
            $sSexo = "";
            $sNombre = "";
            $sApellido = "";
            $iPaisId = "";
            $iProvinciaId = "";
            $iCiudadId = "";
            $sDomicilio = "";
            $sTelefono = "";
            $sNombreApellidoPadre = "";
            $sNombreApellidoMadre = "";
            $sOcupacionPadre = "";
            $sOcupacionMadre = "";
            $sNombreHermanos = "";
            $iInstitucionId = "";
            $sInstitucion = "";
            $nacimientoDia = ""; $nacimientoPadreDia = ""; $nacimientoMadreDia = "";
            $nacimientoMes = ""; $nacimientoPadreMes = ""; $nacimientoMadreMes = "";
            $nacimientoAnio = ""; $nacimientoPadreAnio = ""; $nacimientoMadreAnio = "";

        //MODIFICAR PERSONA
        }else{
            $iPersonaIdForm = $this->getRequest()->getParam('personaId');
            if(empty($iPersonaIdForm)){
                throw new Exception("La url esta incompleta, no puede ejecutar la acción", 401);
            }
           
            $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitado($iPersonaIdForm);

            $moderacionPendiente = SeguimientosController::getInstance()->existeModeracionPendiente($oDiscapacitado);
            if($moderacionPendiente){
                //si existe moderacion pendiente no muestra los formularios
                $tituloMensajeError = "Datos con moderación pendiente";
                $ficha = "MsgFichaInfoBlock";
                $mensajeInfoError = "La información de la persona ha sido modificada recientemente.<br> La modificación esta pendiente de ser moderada, no se pueden realizar nuevos cambios por el momento.";

                $this->getTemplate()->load_file_section("gui/componentes/carteles.gui.html", "popUpContent", $ficha);
                $this->getTemplate()->set_var("sTituloMsgFicha", $tituloMensajeError);
                $this->getTemplate()->set_var("sMsgFicha", $mensajeInfoError);
                return;
            }

            $this->getTemplate()->load_file_section("gui/vistas/seguimientos/personas.gui.html", "popUpContent", "FormularioBlock");
            $this->getTemplate()->unset_blocks("SubmitCrearPersonaBlock");

            //esto es para que cuando se haga el submit se sepa que persona se estaba modificando
            $this->getTemplate()->set_var("iPersonaIdForm", $iPersonaIdForm);
            $this->getTemplate()->set_var("iPersonaIdFoto", $iPersonaIdForm);

            $iTipoDocumentoId = $oDiscapacitado->getTipoDocumento();
            $sNumeroDocumento = $oDiscapacitado->getNumeroDocumento();
            $sSexo = $oDiscapacitado->getSexo();
            $sNombre = $oDiscapacitado->getNombre();
            $sApellido = $oDiscapacitado->getApellido();

            $iPaisId = "";
            $iProvinciaId = "";
            $iCiudadId = "";
            if(null != $oDiscapacitado->getCiudad()){
                $iCiudadId = $oDiscapacitado->getCiudad()->getId();
                if(null != $oDiscapacitado->getCiudad()->getProvincia()){
                $iProvinciaId = $oDiscapacitado->getCiudad()->getProvincia()->getId();
                    if(null != $oDiscapacitado->getCiudad()->getProvincia()->getPais()){
                        $iPaisId = $oDiscapacitado->getCiudad()->getProvincia()->getPais()->getId();
                    }
                }
            }

            $sDomicilio = $oDiscapacitado->getDomicilio();
            $sTelefono = $oDiscapacitado->getTelefono();
            $sNombreApellidoPadre = $oDiscapacitado->getNombreApellidoPadre();
            $sNombreApellidoMadre = $oDiscapacitado->getNombreApellidoMadre();
            $sOcupacionPadre = $oDiscapacitado->getOcupacionPadre();
            $sOcupacionMadre = $oDiscapacitado->getOcupacionMadre();
            $sNombreHermanos = $oDiscapacitado->getNombreHermanos();

            $iInstitucionId = "";
            $sInstitucion = "";
            if(null != $oDiscapacitado->getInstitucion()){
                $iInstitucionId = $oDiscapacitado->getInstitucion()->getId();
                $sInstitucion = $oDiscapacitado->getInstitucion()->getNombre();
            }
            
            $nacimientoDia = ""; $nacimientoPadreDia = ""; $nacimientoMadreDia = "";
            $nacimientoMes = ""; $nacimientoPadreMes = ""; $nacimientoMadreMes = "";
            $nacimientoAnio = ""; $nacimientoPadreAnio = ""; $nacimientoMadreAnio = "";
            list($nacimientoAnio, $nacimientoMes, $nacimientoDia) = explode("-", $oDiscapacitado->getFechaNacimiento());
            list($nacimientoPadreAnio, $nacimientoPadreMes, $nacimientoPadreDia) = explode("-", $oDiscapacitado->getFechaNacimientoPadre());
            list($nacimientoMadreAnio, $nacimientoMadreMes, $nacimientoMadreDia) = explode("-", $oDiscapacitado->getFechaNacimientoMadre());

            //foto de perfil actual
            if(null != $oDiscapacitado->getFotoPerfil()){
                $oFoto = $oDiscapacitado->getFotoPerfil();

                $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();

                $this->getTemplate()->set_var("scrFotoPerfilActual", $pathFotoServidorMediumSize);
                $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);

                $this->getTemplate()->parse("FotoPerfilActualBlock");
            }else{
                $this->getTemplate()->unset_blocks("FotoPerfilActualBlock");
            }
        }

        //armo el select con los tipos de documentos cargados en db
        $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
        foreach ($aTiposDocumentos as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($iTipoDocumentoId == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectDocumento", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //select sexo
        if(empty($sSexo) || $sSexo == 'm'){
            $this->getTemplate()->set_var("sSelectedMasculino", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedFemenino", "selected='selected'");
        }

        //dia de nacimiento
        for($i = 1; $i <= 31; $i++){
            $value = (string)$i;
            if($i<10){ $value = "0".$value; }

            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoDia == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectDia", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //dia de nacimiento Padre
        for($i = 1; $i <= 31; $i++){
            $value = (string)$i;
            if($i<10){ $value = "0".$value; }

            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoPadreDia == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectPadreDia", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //dia de nacimiento Madre
        for($i = 1; $i <= 31; $i++){
            $value = (string)$i;
            if($i<10){ $value = "0".$value; }

            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoMadreDia == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectMadreDia", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        $aMeses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
                        '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre',
                        '11' => 'noviembre', '12' => 'diciembre');

        //mes de nacimiento
        foreach ($aMeses as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($nacimientoMes == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectMes", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //mes de nacimiento Padre
        foreach ($aMeses as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($nacimientoPadreMes == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectPadreMes", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //mes de nacimiento Madre
        foreach ($aMeses as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($nacimientoMadreMes == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectMadreMes", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //year nacimiento
        $anioActual = date("Y");
        for($i = $anioActual; $i >= 1905; $i--){
            $value = (string)$i;
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoAnio == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectAnio", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //year nacimiento Padre
        $anioActual = date("Y");
        for($i = $anioActual; $i >= 1905; $i--){
            $value = (string)$i;
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoPadreAnio == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectPadreAnio", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //year nacimiento Madre
        $anioActual = date("Y");
        for($i = $anioActual; $i >= 1905; $i--){
            $value = (string)$i;
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $value);
            if($nacimientoMadreAnio == $i){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectMadreAnio", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        //combo nacionalidad
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
                if($usuario->getCiudad()->getId() == $oCiudad->getId()){
                    $this->getTemplate()->set_var("sDatosPersonalesCiudadSelect", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sDatosPersonalesCiudadSelect", "");
                }
                $this->getTemplate()->set_var("iCiudadId", $oCiudad->getId());
                $this->getTemplate()->set_var("sCiudadNombre", $oCiudad->getNombre());
                $this->getTemplate()->parse("ListaCiudadesBlock", true);
            }
        }

        $this->getTemplate()->set_var("sNombre", $sNombre);
        $this->getTemplate()->set_var("sApellido", $sApellido);
        $this->getTemplate()->set_var("sDireccion", $sDomicilio);
        $this->getTemplate()->set_var("sTelefono", $sTelefono);
        $this->getTemplate()->set_var("sNumeroDocumento", $sNumeroDocumento);
        $this->getTemplate()->set_var("sNombreApellidoPadre", $sNombreApellidoPadre);
        $this->getTemplate()->set_var("sNombreApellidoMadre", $sNombreApellidoMadre);
        $this->getTemplate()->set_var("sOcupacionPadre", $sOcupacionPadre);
        $this->getTemplate()->set_var("sOcupacionMadre", $sOcupacionMadre);
        $this->getTemplate()->set_var("sNombreHermanos", $sNombreHermanos);
        $this->getTemplate()->set_var("iInstitucionId", $iInstitucionId);
        $this->getTemplate()->set_var("sInstitucion", $sInstitucion);

        //Foto perfil
        $nombreInputFile = 'fotoPerfil';
        $this->getUploadHelper()->setTiposValidosFotos();
        $this->getTemplate()->set_var("sTiposPermitidosFoto", $this->getUploadHelper()->getStringTiposValidos());
        $this->getTemplate()->set_var("iTamanioMaximo", $this->getUploadHelper()->getTamanioMaximo());
        $this->getTemplate()->set_var("iMaxFileSizeForm", $this->getUploadHelper()->getMaxFileSize());
        
        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));
    }

    /**
     * Con un post se determina si es edicion o alta.
     * Se puede hacer asi porque los permisos de edicion y alta serian los mismos.
     * Todo integrante activo podria crear y tambien modificar una persona.
     *
     * En el procesar tambien se procesa la foto de perfil.
     * La foto de perfil se puede asociar unicamente luego de que se crea la persona
     */
    public function procesar(){

        //si accedio a traves de la url muestra pagina 404, excepto si es upload de archivo
        if(!$this->getRequest()->has('fotoUpload') && !$this->getAjaxHelper()->isAjaxContext()){
            throw new Exception("", 404);
        }        

        if($this->getRequest()->has('crearPersona')){
            $this->crearPersona();
            return;
        }

        if($this->getRequest()->has('modificarPersona')){
            $this->modificarPersona();
            return;
        }

        if($this->getRequest()->has('fotoUpload')){
            $this->fotoPerfilUpload();
            return;
        }

        if($this->getRequest()->has('checkNumeroDocumento')){
            $this->checkNumeroDocumento();
            return;
        }
    }

    private function crearPersona()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();
            
            //$this->getRequest()->imprimirPost(true);
            $oDiscapacitado = new stdClass();

            $oDiscapacitado->iTipoDocumentoId = $this->getRequest()->getPost("tipoDocumento");
            $oDiscapacitado->sNumeroDocumento = $this->getRequest()->getPost("nroDocumento");
            $oDiscapacitado->sSexo = $this->getRequest()->getPost("sexo");
            $oDiscapacitado->sNombre = $this->getRequest()->getPost("nombre");
            $oDiscapacitado->sApellido = $this->getRequest()->getPost("apellido");

            $fechaNacimientoDia = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $dFechaNacimiento = implode('-', $aFechaNacimiento);
            $oDiscapacitado->dFechaNacimiento = $dFechaNacimiento;

            $oDiscapacitado->iCiudadId = $this->getRequest()->getPost("ciudad");
            $oDiscapacitado->sTelefono = $this->getRequest()->getPost("telefono");
            $oDiscapacitado->sDomicilio = $this->getRequest()->getPost("direccion");
            $oDiscapacitado->sNombreApellidoPadre = $this->getRequest()->getPost("nombreApellidoPadre");

            $fechaNacimientoPadreDia = $this->getRequest()->getPost("fechaNacimientoPadreDia");
            $fechaNacimientoPadreMes = $this->getRequest()->getPost("fechaNacimientoPadreMes");
            $fechaNacimientoPadreAnio = $this->getRequest()->getPost("fechaNacimientoPadreAnio");
            $aFechaNacimientoPadre = array($fechaNacimientoPadreAnio, $fechaNacimientoPadreMes, $fechaNacimientoPadreDia);
            $dFechaNacimientoPadre = implode('-', $aFechaNacimientoPadre);
            $oDiscapacitado->dFechaNacimientoPadre = $dFechaNacimientoPadre;

            $oDiscapacitado->sOcupacionPadre = $this->getRequest()->getPost("ocupacionPadre");
            $oDiscapacitado->sNombreApellidoMadre = $this->getRequest()->getPost("nombreApellidoMadre");

            $fechaNacimientoMadreDia = $this->getRequest()->getPost("fechaNacimientoMadreDia");
            $fechaNacimientoMadreMes = $this->getRequest()->getPost("fechaNacimientoMadreMes");
            $fechaNacimientoMadreAnio = $this->getRequest()->getPost("fechaNacimientoMadreAnio");
            $aFechaNacimientoMadre = array($fechaNacimientoMadreAnio, $fechaNacimientoMadreMes, $fechaNacimientoMadreDia);
            $dFechaNacimientoMadre = implode('-', $aFechaNacimientoMadre);
            $oDiscapacitado->dFechaNacimientoMadre = $dFechaNacimientoMadre;

            $oDiscapacitado->sOcupacionMadre = $this->getRequest()->getPost("ocupacionMadre");
            $oDiscapacitado->sNombreHermanos = $this->getRequest()->getPost("nombreHermanos");
            $oDiscapacitado->iInstitucionId = $this->getRequest()->getPost("institucionId");

            $oDiscapacitado->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();

            $oDiscapacitado = Factory::getDiscapacitadoInstance($oDiscapacitado);
            SeguimientosController::getInstance()->guardarDiscapacitado($oDiscapacitado);

            $this->getJsonHelper()->setValor("agregarPersona", "1");
            $this->getJsonHelper()->setValor("personaId", $oDiscapacitado->getId());
            $this->getJsonHelper()->setSuccess(true);

        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }
    
    private function modificarPersona()
    {
        try{
            $this->getJsonHelper()->initJsonAjaxResponse();

            $iId = $this->getRequest()->getPost('personaIdForm');
            $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($iId);
            
            $oDiscapacitado->setTipoDocumento($this->getRequest()->getPost("tipoDocumento"));
            $oDiscapacitado->setNumeroDocumento($this->getRequest()->getPost("nroDocumento"));
            $oDiscapacitado->setSexo($this->getRequest()->getPost("sexo"));
            $oDiscapacitado->setNombre($this->getRequest()->getPost("nombre"));
            $oDiscapacitado->setApellido($this->getRequest()->getPost("apellido"));

            $fechaNacimientoDia = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $dFechaNacimiento = implode('-', $aFechaNacimiento);
            $oDiscapacitado->setFechaNacimiento($dFechaNacimiento);

            $oDiscapacitado->setCiudad($this->getRequest()->getPost("ciudad"));
            $oDiscapacitado->setTelefono($this->getRequest()->getPost("telefono"));
            $oDiscapacitado->setDomicilio($this->getRequest()->getPost("direccion"));
            $oDiscapacitado->setNombreApellidoPadre($this->getRequest()->getPost("nombreApellidoPadre"));

            $fechaNacimientoPadreDia = $this->getRequest()->getPost("fechaNacimientoPadreDia");
            $fechaNacimientoPadreMes = $this->getRequest()->getPost("fechaNacimientoPadreMes");
            $fechaNacimientoPadreAnio = $this->getRequest()->getPost("fechaNacimientoPadreAnio");
            $aFechaNacimientoPadre = array($fechaNacimientoPadreAnio, $fechaNacimientoPadreMes, $fechaNacimientoPadreDia);
            $dFechaNacimientoPadre = implode('-', $aFechaNacimientoPadre);
            $oDiscapacitado->setFechaNacimientoPadre($dFechaNacimientoPadre);

            $oDiscapacitado->setOcupacionPadre($this->getRequest()->getPost("ocupacionPadre"));
            $oDiscapacitado->setNombreApellidoMadre($this->getRequest()->getPost("nombreApellidoMadre"));

            $fechaNacimientoMadreDia = $this->getRequest()->getPost("fechaNacimientoMadreDia");
            $fechaNacimientoMadreMes = $this->getRequest()->getPost("fechaNacimientoMadreMes");
            $fechaNacimientoMadreAnio = $this->getRequest()->getPost("fechaNacimientoMadreAnio");
            $aFechaNacimientoMadre = array($fechaNacimientoMadreAnio, $fechaNacimientoMadreMes, $fechaNacimientoMadreDia);
            $dFechaNacimientoMadre = implode('-', $aFechaNacimientoMadre);
            $oDiscapacitado->setFechaNacimientoMadre($dFechaNacimientoMadre);

            $oDiscapacitado->setOcupacionMadre($this->getRequest()->getPost("ocupacionMadre"));
            $oDiscapacitado->setNombreHermanos($this->getRequest()->getPost("nombreHermanos"));
                        
            $oDiscapacitado->setInstitucionId($this->getRequest()->getPost("institucionId"));

            /*
             * El controlador verifica, si el usuario en sesion que esta modificando los datos
             * no es el usuario que creo la persona entonces solicita al moderador aprobar los cambios.
             *
             * Luego en una proxima instancia, si los datos son aprobados se informa al usuario original
             * que creo la persona en una primera instancia los cambios realizados en los datos.
             */
            list($result, $moderacion) = SeguimientosController::getInstance()->guardarDiscapacitado($oDiscapacitado);

            if(!$result){
                $mensaje = "Existe una moderacion pendiente, no se guardaron los cambios";
                $success = false;
            }else{
                if($moderacion){
                    $mensaje = "La información se proceso con exito, los cambios seran aplicados luego de que un moderador los apruebe.";
                }else{
                    $mensaje = "Los cambios fueron aplicados con exito.";                    
                }
                $success = true;
            }

            $this->getJsonHelper()->setValor("modificarPersona", "1");
            $this->getJsonHelper()->setSuccess($success);
            $this->getJsonHelper()->setMessage($mensaje);

        }catch(Exception $e){

            $this->getJsonHelper()->setSuccess(false);
        }

        $this->getJsonHelper()->sendJsonAjaxResponse();
    }

    private function fotoPerfilUpload()
    {
        try{            
            $nombreInputFile = 'fotoPerfil'; //el nombre del input file (se setea por javascript con el ajax uploader)

            $this->getUploadHelper()->setTiposValidosFotos();
            
            if($this->getUploadHelper()->verificarUpload($nombreInputFile)){

                $iId = $this->getRequest()->getPost('personaIdFoto');
                               
                $oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($iId);
                                
                //un array con los datos de las fotos
                $aNombreArchivos = $this->getUploadHelper()->generarFotosSistema($iId, 'fotoPerfil');
                $pathServidor = $this->getUploadHelper()->getDirectorioUploadFotos(true);
                              
                try{
                    /*
                     * primero va al controler de seguimientos en lugar
                     * de llamar directamente al guardarFotoPerfil de ComunidadController
                     * porque se tiene que agregar la logica de que no haya moderacion pendiente.
                     */                    
                    list($result, $moderacion) = SeguimientosController::getInstance()->guardarFotoPerfilDiscapacitado($aNombreArchivos, $pathServidor, $oDiscapacitado);

                    //si no hay moderacion, se muestra la foto, sino un cartel que indica que la foto falta ser moderada.
                    if(!$result){
                        $respuesta = "0; Existe una moderacion pendiente, no se guardaron los cambios";
                        $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                        return;
                    }

                    if($moderacion){
                        $respuesta = "2; La información se proceso con exito, los cambios seran aplicados luego de que un moderador los apruebe";
                        $this->getAjaxHelper()->sendHtmlAjaxResponse($respuesta);
                        return;
                    }

                    //devuelvo la foto nueva
                    $oFoto = $oDiscapacitado->getFotoPerfil();

                    $this->restartTemplate();
                    /*
                     * contFotoPerfilActual en realidad no esta como {contFotoPerfilActual} en el template,
                     * es solo un nombre para el bloque despues cuando se parsea se manda por javascript el html.
                     * Despues el javascript asigna con $("#contenedor").html()
                     */
                    $this->getTemplate()->load_file_section("gui/vistas/seguimientos/personas.gui.html", "contFotoPerfilActual", "FotoPerfilActualBlock");

                    $this->getUploadHelper()->utilizarDirectorioUploadUsuarios();
                    $pathFotoServidorMediumSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreMediumSize();
                    $pathFotoServidorBigSize = $this->getUploadHelper()->getDirectorioUploadFotos().$oFoto->getNombreBigSize();
                    $this->getTemplate()->set_var("scrFotoPerfilActual", $pathFotoServidorMediumSize);
                    $this->getTemplate()->set_var("hrefFotoPerfilActualAmpliada", $pathFotoServidorBigSize);

                    $respuesta = "1; ".$this->getTemplate()->pparse('contFotoPerfilActual', false);
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

    /**
     * (AJAX) devuelve 1 si el numero de documento ya existe para alguna perona en el sistema
     */
    private function checkNumeroDocumento()
    {
        $dataResult = '0';
        $filtro = array("p.numeroDocumento" => $this->getRequest()->getPost('numeroDocumento'));
        if(SeguimientosController::getInstance()->existeDiscapacitado($filtro)){
            $dataResult = '1';
        }
        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);        
    }

    public function listar(){}
}