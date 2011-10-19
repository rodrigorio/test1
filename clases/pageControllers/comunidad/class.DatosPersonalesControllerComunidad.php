<?php
/**
 * @author Matias Velilla
 */
class DatosPersonalesControllerComunidad extends PageControllerAbstract
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
        $this->formulario();
    }

    /**
     * Procesa el envio desde un formulario de modificacion de datos personales
     */
    public function procesar()
    {
        //si accedio a traves de la url muestra pagina 404
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        //segun que seccion del formulario proceso de manera diferente
        $seccion = $this->getRequest()->getPost('seccion');
        switch($seccion){
            case 'basica': $this->procesarFormInfoBasica(); break;
            case 'contacto': $this->procesarFormInfoContacto();  break;
            case 'profesional': $this->procesarFormInfoProfesional(); break;
            case 'foto': $this->procesarFormFotoPerfil();  break;
            //ya esta el mail registrado?
            case 'check-mail-existe': $this->mailDb();  break;
            //es la contrasenia actual del usuario?
            case 'check-contrasenia-actual': $this->contraseniaActual(); break;
        }
    }
    private function procesarFormInfoBasica(){
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $usuario = $perfil->getUsuario();

            $tipoDocumento  = $this->getRequest()->getPost("tipoDocumento");
            $nroDocumento   = $this->getRequest()->getPost("nroDocumento");
            $nombre         = $this->getRequest()->getPost("nombre");
            $apellido       = $this->getRequest()->getPost("apellido");
            $email          = $this->getRequest()->getPost("email");
            $contraseniaNuevaMD5    = $this->getRequest()->getPost("contraseniaNuevaMD5");
            $sexo                   = $this->getRequest()->getPost("sexo");
            $fechaNacimientoDia     = $this->getRequest()->getPost("fechaNacimientoDia");
            $fechaNacimientoMes     = $this->getRequest()->getPost("fechaNacimientoMes");
            $fechaNacimientoAnio    = $this->getRequest()->getPost("fechaNacimientoAnio");
            $aFechaNacimiento = array($fechaNacimientoAnio, $fechaNacimientoMes, $fechaNacimientoDia);
            $fechaNacimiento = implode('-', $aFechaNacimiento);

            $usuario->setTipoDocumento($tipoDocumento);
            $usuario->setNumeroDocumento($nroDocumento);
            $usuario->setNombre($nombre);
            $usuario->setApellido($apellido);
            $usuario->setEmail($email);
            //sino se borra la que ya estaba
            if(!empty($contraseniaNuevaMD5)){
                $usuario->setContrasenia($contraseniaNuevaMD5);
            }
            $usuario->setSexo($sexo);
            $usuario->setFechaNacimiento($fechaNacimiento);

            ComunidadController::getInstance()->guardarUsuario($usuario);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function procesarFormInfoContacto(){
        try{
            //se fija si existe callback de jQuery y lo guarda, tmb inicializa el array que se va a codificar
            $this->getJsonHelper()->initJsonAjaxResponse();

            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            $usuario = $perfil->getUsuario();

            $iCiudadId      = $this->getRequest()->getPost("ciudad");            
            $sCiudadOrigen  = $this->getRequest()->getPost("ciudadOrigen");
            $sCodigoPostal  = $this->getRequest()->getPost("codigoPostal");
            $sDomicilio     = $this->getRequest()->getPost("direccion");
            $sTelefono      = $this->getRequest()->getPost("telefono");
            $sCelular       = $this->getRequest()->getPost("celular");
            $sFax           = $this->getRequest()->getPost("fax");

            //internamente se fija de levantar de nuevo el objeto ciudad si es != null
            $usuario->setCiudadId($iCiudadId);
            $usuario->setCiudadOrigen($sCiudadOrigen);
            $usuario->setCodigoPostal($sCodigoPostal);
            $usuario->setDomicilio($sDomicilio);
            $usuario->setTelefono($sTelefono);
            //estos dos no son obligatorios, si se envian con cadena vacia elimina los valores viejos
            $usuario->setCelular($sCelular);
            $usuario->setFax($sFax);
            
            ComunidadController::getInstance()->guardarUsuario($usuario);

            $this->getJsonHelper()->setSuccess(true);
        }catch(Exception $e){
            $this->getJsonHelper()->setSuccess(false);
        }
        $this->getJsonHelper()->sendJsonAjaxResponse();        
    }

    private function procesarFormInfoProfesional(){

    }
    
    private function procesarFormFotoPerfil(){

    }
    
    /**
     * (AJAX) devuelve 1 si el mail ya existe en la DB asociado a una cuenta que no es la del usuario que modifica sus datos.
     */
    private function mailDb(){
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $dataResult = '0';

        if(ComunidadController::getInstance()->existeMailDb($this->getRequest()->getPost('email'), $usuario->getId())){
            $dataResult = '1';
        }

        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);        
    }

    /**
     * (AJAX) devuelve 1 si es contrasenia actual, 0 si no es la contrasenia actual
     */
    private function contraseniaActual(){
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $dataResult = '0';

        if($usuario->getContrasenia() == $this->getRequest()->getPost('contraseniaActual')){
            $dataResult = '1';
        }
        
        $this->getAjaxHelper()->sendHtmlAjaxResponse($dataResult);
    }
    
    /**
     * Vista con el formulario de modificacion de datos personales
     *
     */
    public function formulario()
    {
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();

        $this->getTemplate()->load_file("gui/templates/comunidad/frame01-01.gui.html", "frame");
        $this->setHeadTag();

        $this->printMsgTop();

        IndexControllerComunidad::setCabecera($this->getTemplate());
        IndexControllerComunidad::setCenterHeader($this->getTemplate());

        //titulo seccion
        $this->getTemplate()->set_var("tituloSeccion", "Modificar datos personales");

        //privacidad (columna)
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerCont", "PageRightInnerContBlock");
        //seteo los valores actuales para los campos
        $aPrivacidad = $usuario->obtenerPrivacidad();
        $this->getTemplate()->set_var($aPrivacidad['email']."EmailSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['telefono']."TelefonoSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['celular']."CelularSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['fax']."FaxSelected", "selected = 'selected' ");
        $this->getTemplate()->set_var($aPrivacidad['curriculum']."CurriculumSelected", "selected = 'selected' ");

        //menu con los distintos formularios (info basica, info contacto, etc)
        $this->getTemplate()->load_file_section("gui/componentes/menues.gui.html", "pageRightInnerMainCont", "MenuHorizontal02Block");
        $this->getTemplate()->set_var("idOpcion", 'optFormInfoBasica');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/comunidad/datos-personales?seccion=basica');
        $this->getTemplate()->set_var("sNombreOpcion", "Información básica");
        $this->getTemplate()->parse("OpcionesMenu", true);

        $this->getTemplate()->set_var("idOpcion", 'optFormInfoContacto');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/comunidad/datos-personales?seccion=contacto');
        $this->getTemplate()->set_var("sNombreOpcion", "Información Contacto");
        $this->getTemplate()->parse("OpcionesMenu", true);

        $this->getTemplate()->set_var("idOpcion", 'optFormPerfilProfesional');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/comunidad/datos-personales?seccion=profesional');
        $this->getTemplate()->set_var("sNombreOpcion", "Perfil Profesional");
        $this->getTemplate()->parse("OpcionesMenu", true);

        $this->getTemplate()->set_var("idOpcion", 'optFormFotoPerfil');
        $this->getTemplate()->set_var("hrefOpcion", $this->getRequest()->getBaseUrl().'/comunidad/datos-personales?seccion=foto');
        $this->getTemplate()->set_var("sNombreOpcion", "Foto de Perfil");
        $this->getTemplate()->parse("OpcionMenuLastOpt");
        
        //contenido ppal, carga formulario dependiendo una variable por get
        $seccion = $this->getRequest()->get('seccion');
        switch($seccion){
            case 'basica': $this->formInfoBasica(); break;
            case 'contacto': $this->formInfoContacto();  break;
            case 'profesional': $this->formInfoProfesional(); break;
            case 'foto': $this->formFotoPerfil();  break;
            default: $this->formInfoBasica(); break;
        }

        $this->getResponse()->setBody($this->getTemplate()->pparse('frame', false));        
    }

    private function formInfoBasica()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerMainCont", "FormularioInfoBasicaBlock", true);

        //obtengo los valores iniciales desde el usuario
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $tipoDocumentoId = $usuario->getTipoDocumento();
        $numeroDocumento = $usuario->getNumeroDocumento();
        $nombre = $usuario->getNombre();
        $apellido = $usuario->getApellido();
        $email = $usuario->getEmail();
        $sexo = $usuario->getSexo();
        $fechaNacimiento = $usuario->getFechaNacimiento();
        list($nacimientoAnio, $nacimientoMes, $nacimientoDia) = explode("-", $fechaNacimiento);
        
        //armo el select con los tipos de documentos cargados en db
        $aTiposDocumentos = IndexController::getInstance()->obtenerTiposDocumentos();
        foreach ($aTiposDocumentos as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($tipoDocumentoId == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectDocumento", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

        $this->getTemplate()->set_var("sNumeroDocumento", $numeroDocumento);
        $this->getTemplate()->set_var("sNombre", $nombre);
        $this->getTemplate()->set_var("sApellido", $apellido);
        $this->getTemplate()->set_var("sEmail", $email);
        
        if($sexo == 'm'){
            $this->getTemplate()->set_var("sSelectedMasculino", "selected='selected'");
        }else{
            $this->getTemplate()->set_var("sSelectedFemenino", "selected='selected'");
        }

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

        $aMeses = array('01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril', '05' => 'mayo',
                        '06' => 'junio', '07' => 'julio', '08' => 'agosto', '09' => 'septiembre', '10' => 'octubre',
                        '11' => 'noviembre', '12' => 'diciembre');
        
        foreach ($aMeses as $value => $text){
            $this->getTemplate()->set_var("iValue", $value);
            $this->getTemplate()->set_var("sDescripcion", $text);
            if($nacimientoMes == $value){
                $this->getTemplate()->set_var("sSelected", "selected='selected'");
            }
            $this->getTemplate()->parse("OptionSelectMes", true);
            $this->getTemplate()->set_var("sSelected", "");
        }

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
    }

    private function formInfoContacto()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerMainCont", "FormularioInfoContactoBlock", true);

        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();

        $arrayPaises = array();
        $iRecordsTotalPais = 0;
        $listaPaises = ComunidadController::getInstance()->listaPaises($arrayPaises, $iRecordsTotalPais, null,  null,  null,  null);
        foreach ($listaPaises as $oPais){
            if(null !== $usuario->getCiudadId() && $usuario->getCiudad()->getProvincia()->getPais()->getId() == $oPais->getId()){
                $this->getTemplate()->set_var("sDatosPersonalesPaisSelect", "selected='selected'");
            }else{
                $this->getTemplate()->set_var("sDatosPersonalesPaisSelect", "");
            }
            $this->getTemplate()->set_var("iPaisId", $oPais->getId());
            $this->getTemplate()->set_var("sPaisNombre", $oPais->getNombre());
            $this->getTemplate()->parse("ListaPaisesBlock", true);
        }

        if(null !== $usuario->getCiudadId()){
            $listaProvincias = ComunidadController::getInstance()->listaProvinciasByPais($usuario->getCiudad()->getProvincia()->getPais()->getId());
            foreach ($listaProvincias as $oProvincia){
                if($usuario->getCiudad()->getProvincia()->getId() == $oProvincia->getId()){
                    $this->getTemplate()->set_var("sDatosPersonalesProvinciaSelect", "selected='selected'");
                }else{
                    $this->getTemplate()->set_var("sDatosPersonalesProvinciaSelect", "");
                }
                $this->getTemplate()->set_var("iProvinciaId", $oProvincia->getId());
                $this->getTemplate()->set_var("sProvinciaNombre", $oProvincia->getNombre());
                $this->getTemplate()->parse("ListaProvinciasBlock", true);
            }

            $listaCiudades = ComunidadController::getInstance()->listaCiudadByProvincia($usuario->getCiudad()->getProvincia()->getId());
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

        $sCiudadOrigen   = $usuario->getCiudadOrigen();
        $sCodigoPostal   = $usuario->getCodigoPostal();
        $sDomicilio      = $usuario->getDomicilio();
        $sTelefono       = $usuario->getTelefono();
        $sCelular        = $usuario->getCelular();
        $sFax            = $usuario->getFax();

        $this->getTemplate()->set_var("sCiudadOrigen", $sCiudadOrigen);
        $this->getTemplate()->set_var("sCodigoPostal", $sCodigoPostal);
        $this->getTemplate()->set_var("sDireccion", $sDomicilio);
        $this->getTemplate()->set_var("sTelefono", $sTelefono);
        $this->getTemplate()->set_var("sCelular", $sCelular);
        $this->getTemplate()->set_var("sFax", $sFax);
    }

    private function formInfoProfesional()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerMainCont", "FormularioInfoProfesionalBlock", true);
    }

    private function formFotoPerfil()
    {
        $this->getTemplate()->load_file_section("gui/vistas/comunidad/datosPersonales.gui.html", "pageRightInnerMainCont", "FormularioFotoPerfilBlock", true);
    }

    public function modificarPrivacidadCampo()
    {
        //si accedio a traves de la url muestra pagina 404 porq es ajax
        if(!$this->getAjaxHelper()->isAjaxContext()){ throw new Exception("", 404); }
        
        $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
        $usuario = $perfil->getUsuario();
        $usuario->guardarPrivacidadCampo($this->getRequest()->getPost('nombreCampo'), $this->getRequest()->getPost('valorPrivacidad'));
    }
}