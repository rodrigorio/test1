<?php

/**
 * Controlador principal de la 'logica de negocio'. 
 *
 */
class ComunidadController
{
    /**
     * @var Instancia de DB
     */
    private $db = null;

    /**
     * @var Instancia de clase que maneja session de usuario
     */
    private $auth = null;
    
    private static $instance = null;

    private function __construct(){ }	
	
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Auth $session
     */
    public function setAuth(Auth $auth){
        $this->auth = $auth;
    }

    /**
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }
	
    public function enviarInvitacion($oInvitacion)
    {
        try{
            $oInvitacionIntermediary = PersistenceFactory::getInvitacionIntermediary($this->db);                        
            return $oInvitacionIntermediary->insertar($oInvitacion);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve verdadero si el usuario que esta logueado ya envio una invitacion al
     * correo de una persona y la invitacion todavia no expiro.
     */
    public function existeInvitacionUsuario($sEmail)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_INVITACION');
            
            $filtroExiste = array("ui.usuarios_id" => $iUsuarioId,
                                  "p.email" => $sEmail,
                                  "expiracion" => $cantDiasExpiracion);

            $oInvitacionIntermediary = PersistenceFactory::getInvitacionIntermediary($this->db);
            return $oInvitacionIntermediary->existe($filtroExiste);
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerInvitadoByEmail($sEmail)
    {
        try{
            $filtro = array('p.email' => $sEmail);
            $oInvitacionIntermediary = PersistenceFactory::getInvitacionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aInvitados = $oInvitacionIntermediary->obtenerInvitados($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aInvitados){
                return $aInvitados[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function borrarInvitacionesExpiradasUsuario()
    {
        try{
            $oInvitacionIntermediary = PersistenceFactory::getInvitacionIntermediary($this->db);
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $iDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_INVITACION');
            $oInvitacionIntermediary->borrarInvitacionesExpiradasUsuario($iUsuarioId, $iDiasExpiracion);            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getInvitacionByToken($sToken)
    {
        try{
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_INVITACION');
            $filtro = array('ui.token' => $sToken, 'expiracion' => $cantDiasExpiracion);
            $oInvitacionIntermediary = PersistenceFactory::getInvitacionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aInvitacion = $oInvitacionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aInvitacion){
                return $aInvitacion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        } 
    }
    
    public function listaPaises($array, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oPaisIntermediary = PersistenceFactory::getPaisIntermediary($this->db);
            return $oPaisIntermediary->obtener($array, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getPaisById($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oPaisIntermediary = PersistenceFactory::getPaisIntermediary($this->db);
            $aPais = $oPaisIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount);
            if(null !== $aPais){
                return $aPais[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    public function listaProvinciasByPais($iPaisId,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $filtro = array("p.paises_id"=>$iPaisId);
            $oProvinciaIntermediary = PersistenceFactory::getProvinciaIntermediary($this->db);
            return $oProvinciaIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getProvinciaById($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oProvinciaIntermediary = PersistenceFactory::getProvinciaIntermediary($this->db);
            $aProvincia = $oProvinciaIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
            if(null !== $aProvincia){
                return $aProvincia[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function listaCiudadByProvincia($iProvinciaId,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $filtro = array("c.provincia_id" => $iProvinciaId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            return $oCiudadIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getCiudadById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('c.id' => $iId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            $aCiudad = $oCiudadIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount);
            if(null !== $aCiudad){
                return $aCiudad[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarInstitucion($oInstitucion)
    {
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            $oInstitucionIntermediary->guardar($oInstitucion);

            //si se guarda con objeto usuario, limpio las solicitudes de administracion de contenido pendientes.
            if(null !== $oInstitucion->getUsuario()){
                $oInstitucionIntermediary->limpiarSolicitudes($oInstitucion->getId());
            }

            //si la institucion la guarda (desde el form de comunidad) un moderador o administrador se aprueba automaticamente.
            $classPerfil = SessionAutentificacion::getInstance()->getClassPerfilAutentificado();
            if($classPerfil == "Administrador" || $classPerfil == "Moderador"){
                if((null === $oInstitucion->getModeracion()) || ($oInstitucion->getModeracion()->isRechazado())){
                    $oModeracion = new stdClass();
                    $oModeracion = Factory::getModeracionInstance($oModeracion);
                    $oModeracion->setEstadoAprobado();
                    $oModeracion->setMensaje("Moderacion automatica por perfil Administrador o Moderador.");
                    $oInstitucion->setModeracion($oModeracion);

                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->guardarModeracionEntidad($oInstitucion);
                }
                return;
            }

            //si las moderaciones estan desactivadas para el controlador de pagina
            //entonces no se crean nuevas entradas de moderacion pendiente.
            $activarModeraciones = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('ACTIVAR_MODERACIONES');
            if(!$activarModeraciones){
                if((null === $oInstitucion->getModeracion()) || ($oInstitucion->getModeracion()->isRechazado())){
                    $oModeracion = new stdClass();
                    $oModeracion = Factory::getModeracionInstance($oModeracion);
                    $oModeracion->setEstadoAprobado();
                    $oModeracion->setMensaje("Aprobado automaticamente por moderaciones desactivadas.");
                    $oInstitucion->setModeracion($oModeracion);

                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->guardarModeracionEntidad($oInstitucion);
                }
                return;
            }

            if((null === $oInstitucion->getModeracion()) || (!$oInstitucion->getModeracion()->isPendiente()))
            {
                //fecha se genera sola, mensaje vacio, estado pendiente por defecto.
                $oModeracion = new stdClass();
                $oModeracion = Factory::getModeracionInstance($oModeracion);
                $oInstitucion->setModeracion($oModeracion);

                $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                $oModeracionIntermediary->guardarModeracionEntidad($oInstitucion);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function borrarInstitucion($iInstitucionId){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->borrar($iInstitucionId);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    
    public function obtenerInstitucion($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function buscarInstitucionesComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve un array de objetos usuario con todos los integrantes asociados a una institucion.
     */
    public function obtenerUsuariosAsociadosInstitucion($iInstitucionId)
    {
        try{
            $filtro = array('p.instituciones_id' => $iInstitucionId);
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oUsuarioIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }                
    }

    public function obtenerDiscapacitadosAsociadosInstitucion($iInstitucionId)
    {
        try{
            $filtro = array('p.instituciones_id' => $iInstitucionId);
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDiscapacitadoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getInstitucionById($iInstitucionId){
    	try{
            $filtro = array('i.id' => $iInstitucionId);
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aInstitucion = $oInstitucionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aInstitucion){
                return $aInstitucion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerInstituciones($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existeInstitucion($filtro){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->existe($filtro);
        }catch(Exception $e){
           throw $e;
        }
    }

    public function listaTiposDeInstitucion($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->listaTiposDeInstitucion($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve true si el usuario ya envio una solicitud para administrar la institucion
     */
    public function existeSolicitudInstitucion($iInstitucionId, $iUsuarioId)
    {
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->existeSolicitud($iInstitucionId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     * Guarda las solicitudes de una institucion en DB
     */
    public function guardarSolicitudesInstitucion($oInstitucion)
    {
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->guardarSolicitudes($oInstitucion);
        }catch(Exception $e){
            $oInstitucion->setSolicitudes(null);
            throw $e;
        }        
    }
       
    /**
     * Sirve para determinar si un mail ya existe asociado a alguna cuenta de la db, independientemente del estado, perfil de usuario, etc.
     * Se puede pasar el id de usuario (se usa para no tener en cuenta el mail de la cuenta activa)
     */
    public function existeMailDb($email, $userId = "")
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existeMailDb($email, $userId);
        }catch(Exception $e){
            throw $e;
        }            
    }

    public function existeNombreUsuarioDb($nombreUsuario)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existeNombreUsuarioDb($nombreUsuario);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * @param stdClass $obj
     */
    public function guardarUsuario($oUsuario){
    	try{            
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $oUsuarioIntermediary->guardar($oUsuario);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Tiene que devolver null si el usuario no existe.
     */
    public function getUsuarioById($iUsuarioId){
    	try{
            $filtro = array('p.id' => $iUsuarioId);
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iRecordsTotal = 0;
            $aUsuario = $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aUsuario){
                return $aUsuario[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Retorna un objeto usuario a partir del numero de documento y el email.
     * si no existe retorna null
     */
    public function getUsuarioByEmailDni($sEmail, $iTipoDocumentoId, $sNumeroDocumento){
    	try{
            $filtro = array('p.documento_tipos_id' => $iTipoDocumentoId,
                            'p.numeroDocumento' => $sNumeroDocumento,
                            'p.email' => $sEmail);
            
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iRecordsTotal = 0;
            $aUsuario = $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aUsuario){
                return $aUsuario[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function buscarUsuarios($filtro, $iRecordsTotal = 0,$sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    public function guardarCurriculumUsuario($usuario, $nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor)
    {
    	try{            
            //creo el objeto archivo y lo guardo.
            $oArchivo = new stdClass();
            $oArchivo->sNombre = $nombreArchivo;
            $oArchivo->sNombreServidor = $nombreServidorArchivo;
            $oArchivo->sTipoMime = $tipoMimeArchivo;
            $oArchivo->iTamanio = $tamanioArchivo;
            $curriculumVitae = Factory::getArchivoInstance($oArchivo);

            $curriculumVitae->setTipoCurriculum();
            
            //si ya tenia cv el usuario borro el actual
            if(null !== $usuario->getCurriculumVitae())
            {
                $this->borrarCurriculumUsuario($usuario, $pathServidor);
            }
            
            //asociarlo al usuario           
            $usuario->setCurriculumVitae($curriculumVitae);

            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->guardarCurriculumVitae($usuario);
            
        }catch(Exception $e){            
            $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                unlink($pathServidorArchivo);
            }
            $usuario->setCurriculumVitae(null);
            
            throw $e;
        }
    }

    /**
     *
     * @param Usuario $usuario el usuario al que se le va a eliminar el CV
     * @param string $pathServidor el path al directorio donde esta el archivo que se va a borrar
     */
    public function borrarCurriculumUsuario($usuario, $pathServidor)
    {
    	try{
            if(null === $usuario->getCurriculumVitae()){
                throw new Exception("El usuario no posee Curriculum");
            }

            IndexController::getInstance()->borrarArchivo($usuario->getCurriculumVitae(), $pathServidor);

            $usuario->setCurriculumVitae(null);
            
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este devuelve un unico archivo a partir de un Id o del nombreServidor
     * Si se necesita obtener un array de objetos hay que hacer otro metodo con el algoritmo de la busqueda.
     *
     * este metodo se usa en el descargarArchivo del page controller index de los modulos 
     */
    public function obtenerArchivo($aParams)
    {
    	try{            
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);

            $filtro = array();
            if(array_key_exists('id', $aParams)){ 
                $filtro['a.id'] = $aParams['id'];
            }else{
                if(array_key_exists('nombreServidor', $aParams)){ 
                    $filtro['a.nombreServidor'] = $aParams['nombreServidor'];
                }
            }

            if(empty($filtro)){
                throw new Exception("se llamo a la funcion sin filtro");
                return;
            }
            
            $aArchivo = $oArchivoIntermediary->obtener($filtro, $iRecordsTotal);
            if(null !== $aArchivo){
                return $aArchivo[0];
            }else{
                return false;
            }
            
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     *
     * @param array $aNombreArchivos 3 celdas con los nombres de los archivos ['nombreFotoGrande'] ['nombreFotoMediana'] ['nombreFotoChica']
     * @param string $pathServidor directorio donde estan guardadas las fotos
     * @param PersonaAbstract puede ser tanto un discapacitado, un usuario o cualquiera que herede de persona
     */
    public function guardarFotoPerfil($aNombreArchivos, $pathServidor, $oPersona)
    {
    	try{
            //creo el objeto Foto y lo guardo.
            $oFoto = new stdClass();
            $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
            $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
            $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];

            $oFotoPerfil = Factory::getFotoInstance($oFoto);

            $oFotoPerfil->setOrden(0);
            $oFotoPerfil->setTitulo('Foto de perfil');
            $oFotoPerfil->setDescripcion('');
            $oFotoPerfil->setTipoPerfil();

            //si ya tenia foto de perfil borro la actual
            if(null !== $oPersona->getFotoPerfil())
            {
                $this->borrarFotoPerfil($oPersona, $pathServidor);
            }

            //asociarlo al objeto
            $oPersona->setFotoPerfil($oFotoPerfil);

            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->guardarFotoPerfil($oPersona);

        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            foreach($aNombreArchivos as $nombreServidorArchivo){
                $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                    unlink($pathServidorArchivo);
                }
            }
            $oPersona->setFotoPerfil(null);
            
            throw $e;
        }        
    }

    public function borrarFotoPerfil($oPersona, $pathServidor)
    {
    	try{
            if(null === $oPersona->getFotoPerfil()){
                throw new Exception("El usuario no posee foto de perfil");
            }

            IndexController::getInstance()->borrarFoto($oPersona->getFotoPerfil(), $pathServidor);

            $oPersona->setFotoPerfil(null);
            
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     * Devuelve verdadero si el usuario tiene los datos minimos
     * requeridos para el perfil Integrante Activo
     *
     * @todo hay que probarlo y ademas el javascript que toma el resultado de esto
     */
    public function cumpleIntegranteActivo($oUsuario)
    {
        //serian los campos obligatorios para pasar de perfil
        if(
            null !== $oUsuario->getNombre() &&
            null !== $oUsuario->getApellido() &&
            null !== $oUsuario->getEmail() &&
            null !== $oUsuario->getSexo() &&
            null !== $oUsuario->getFechaNacimiento() &&

            null !== $oUsuario->getCiudad() &&
            null !== $oUsuario->getCodigoPostal() &&
            null !== $oUsuario->getDomicilio() &&
            null !== $oUsuario->getTelefono() &&

            null !== $oUsuario->getSecundaria() &&
            null !== $oUsuario->getEspecialidad()                     
        ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Este metodo toma un usuario cargado en sesion en perfil Integrante Inactivo
     * y le cambia el perfil a Integrante Activo, tambien actualiza los permisos.
     * 
     */
    public function cambiarIntegranteActivoUsuarioSesion()
    {
    	try{
            if("IntegranteInactivo" == SessionAutentificacion::getInstance()->getClassPerfilAutentificado())
            {                
                $oPerfil = new stdClass();
                $oPerfil->oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
                $oIntegranteActivo = Factory::getIntegranteActivoInstance($oPerfil);
                $oIntegranteActivo->iniciarPermisos();
                SessionAutentificacion::getInstance()->cargarAutentificacion($oIntegranteActivo);

                //guardo la info en la DB
                $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
                $oUsuarioIntermediary->guardarPerfil($oIntegranteActivo, false);
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existePublicacion($filtro){
        try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->existe($filtro);
        }catch(Exception $e){
           throw $e;
        }
    }

     /**
     * @param stdClass $obj
     */
    public function guardarPublicacion($oPublicacion){
    	try{            
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            $oPublicacionIntermediary->guardar($oPublicacion);
            $this->procesarModeracionFicha($oPublicacion);           
        }catch(Exception $e){
            throw $e;
        }
    }
    public function guardarReview($oReview){
    	try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            $oPublicacionIntermediary->guardarReview($oReview);
            $this->procesarModeracionFicha($oReview);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Si esta marcada como publica:
     *
     * - Si la ultima moderacion no existe o existe y el estado es diferente de 'pendiente'
     * entonces creo una nueva entrada en el historial de moderaciones para la ficha en estado pendiente.
     * Esto ultimo SI y SOLO SI estan las moderaciones activadas para el controlador.
     * 
     * - Si la ultima moderacion esta en estado pendiente no hago nada.
     * 
     * Si la ficha no esta marcada como publica:
     * 
     * - Si la ultima moderacion existe y el estado es 'pendiente' entonces la elimino.
     *
     *
     * Si el usuario que realiza la modificacion es admin o moderador se aprueba automaticamente
     * y ese registro queda fijo, nunca mas se vuelve a cambiar se mantiene asi.
     */
    private function procesarModeracionFicha($oFicha)
    {        
        try{
            $classPerfil = SessionAutentificacion::getInstance()->getClassPerfilAutentificado();

            if($classPerfil == "Administrador" || $classPerfil == "Moderador"){
                if((null === $oFicha->getModeracion()) || ($oFicha->getModeracion()->isRechazado())){
                    $oModeracion = new stdClass();
                    $oModeracion = Factory::getModeracionInstance($oModeracion);
                    $oModeracion->setEstadoAprobado();
                    $oModeracion->setMensaje("Moderacion automatica por perfil Administrador o Moderador.");
                    $oFicha->setModeracion($oModeracion);
                    
                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->guardarModeracionEntidad($oFicha);
                }
                return;
            }

            $activarModeraciones = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('ACTIVAR_MODERACIONES');
            if($oFicha->isPublico() && !$activarModeraciones){
                if((null === $oFicha->getModeracion()) || ($oFicha->getModeracion()->isRechazado())){                
                    $oModeracion = new stdClass();
                    $oModeracion = Factory::getModeracionInstance($oModeracion);
                    $oModeracion->setEstadoAprobado();
                    $oModeracion->setMensaje("Aprobada automaticamente por moderaciones desactivadas.");
                    $oFicha->setModeracion($oModeracion);

                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->guardarModeracionEntidad($oFicha);
                }
                return;
            }
            
            if($oFicha->isPublico()){
                if((null === $oFicha->getModeracion()) ||
                   (!$oFicha->getModeracion()->isPendiente()))
                {
                    //fecha se genera sola, mensaje vacio, estado pendiente por defecto.
                    $oModeracion = new stdClass();
                    $oModeracion = Factory::getModeracionInstance($oModeracion);
                    $oFicha->setModeracion($oModeracion);

                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->guardarModeracionEntidad($oFicha);
                }
            }else{
                if((null !== $oFicha->getModeracion()) ||
                   ($oFicha->getModeracion()->isPendiente()))
                {
                    $oModeracionIntermediary = PersistenceFactory::getModeracionIntermediary($this->db);
                    $oModeracionIntermediary->borrar($oFicha->getModeracion()->getId());
                    $oFicha->setModeracion(null);
                }
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerPublicacion($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getPublicacionById($iPublicacionId)
    {
    	try{
            $filtro = array('f.id' => $iPublicacionId);
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aPublicaciones = $oPublicacionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aPublicaciones){
                return $aPublicaciones[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function getReviewById($iReviewId)
    {
    	try{
            $filtro = array('f.id' => $iReviewId);
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aReviews = $oPublicacionIntermediary->obtenerReview($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aReviews){
                return $aReviews[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function getSoftwareById($iSoftwareId)
    {
    	try{
            $filtro = array('f.id' => $iSoftwareId);
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            $iRecordsTotal = 0;
            $aSoftware = $oSoftwareIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aSoftware){
                return $aSoftware[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarSoftware($oSoftware){
    	try{
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            $oSoftwareIntermediary->guardar($oSoftware);
            $this->procesarModeracionFicha($oSoftware);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve true si el usuario ya emitio una valoracino para la aplicacion
     */
    public function usuarioEmitioValoracionSoftware($iSoftwareId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->usuarioEmitioValoracion($iSoftwareId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
            return false;
        }        
    }

    public function getFotoDestacadaFicha($iFichaId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.fichas_abstractas_id' => $iFichaId);
            return $oFotoIntermediary->obtenerFotoDestacada($filtro);
        }catch(Exception $e){
            throw $e;
            return false;
        }                
    }

    public function getEmbedVideoDestacadoFicha($iFichaId)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.fichas_abstractas_id' => $iFichaId);
            return $oEmbedVideoIntermediary->obtenerEmbedVideoDestacado($filtro);
        }catch(Exception $e){
            throw $e;
            return false;
        }           
    }

    /**
     * @return array|null
     */
    public function obtenerFotosFicha($iFichaId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.fichas_abstractas_id' => $iFichaId);
            $iRecordsTotal = 0;
            return $oFotoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }        
    }
          
    /**
     * Devuelve true si la foto es de una publicacion creada por el usuario que esta logueado.
     * Cree el metodo porque levantar la publicacion, para despues levantar todas las fotos,
     * para despues fijarse si existe la foto en el array es muy costoso.
     *
     * Este metodo ademas es util porque yo no quiero que se modifique una foto o se elimine si
     * el usuario que esta logueado en el sistema no fue el que la creo.
     * Con esto me aseguro que nadie pueda hacer cosas raras con el javascript.
     *
     * @return boolean true si la foto pertenece al integrante logueado.
     */
    public function isFotoPublicacionUsuario($iFotoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->isFotoPublicacionUsuario($iFotoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
            return false;
        }  
    }
   
    /**
     * @return array|null
     */    
    public function obtenerEmbedVideosFicha($iFichaId)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.fichas_abstractas_id' => $iFichaId);
            $iRecordsTotal = 0;
            return $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }        
    }

    /**
     * similar a $this->isFotoPublicacionUsuario
     */
    public function isEmbedVideoPublicacionUsuario($iEmbedVideoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->isEmbedVideoPublicacionUsuario($iEmbedVideoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
            return false;
        }          
    }
    
   /**
     * @return array|null
     */
    public function obtenerArchivosFicha($iFichaId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.fichas_abstractas_id' => $iFichaId);
            $iRecordsTotal = 0;
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }          
    }

    /**
     * similar a $this->isFotoPublicacionUsuario
     */
    public function isArchivoPublicacionUsuario($iArchivoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->isArchivoPublicacionUsuario($iArchivoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }
      
    /**
     * @return array|null
     */
    public function obtenerSoftwareCategoria($iCategoriaId)
    {
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            $filtro = array('s.categorias_id' => $iCategoriaId);
            $filtro["maxDenuncias"] = $iCantMaxDenuncias;            
            $iRecordsTotal = 0;
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }          
    }

    /**
     * Solo software marcado como publico y moderado
     */
    public function obtenerSoftwareCategoriaVisitantes($iCategoriaId)
    {
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            $filtro = array('s.categorias_id' => $iCategoriaId);
            $filtro["f.activo"] = "1";
            $filtro["s.publico"] = "1";
            $filtro["m.sModeracionEstado"] = "aprobado";
            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $iRecordsTotal = 0;
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerCategoria($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            return $oCategoriaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerCategoriaById($iCategoriaId)
    {
        try{
            $filtro = array('c.id' => $iCategoriaId);
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aCategorias = $oCategoriaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aCategorias){
                return $aCategorias[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerCategoriaByUrlToken($sUrlToken)
    {
        try{
            $filtro = array('c.urlToken' => $sUrlToken);
            $oCategoriaIntermediary = PersistenceFactory::getCategoriaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aCategorias = $oCategoriaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aCategorias){
                return $aCategorias[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }        
    }
    
    /**
     * Guarda todas las fotos vinculadas a una ficha en tiempo de ejecucion.
     * 
     * @param FichaAbstract $oFicha puede ser tanto una publicacion o un review
     * @param string $pathServidor directorio donde estan guardadas las fotos
     */
    public function guardarFotoFicha($oFicha, $pathServidor)
    {
    	try{            
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->guardarFotosFicha($oFicha);
        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            $aFotos = $oFicha->getFotos();
            if(count($aFotos) > 0){
                foreach($aFotos as $oFoto){
                    $aNombreArchivos = $oFoto->getArrayNombres();
                    foreach($aNombreArchivos as $nombreServidorArchivo)
                    {
                        $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }
                $oFicha->setFotos(null);
            }                                             
            throw $e;
        }        
    }

    /**
     * Guarda todos los archivos vinculados a una ficha en tiempo de ejecucion.
     *
     * @param FichaAbstract $oFicha puede ser tanto una publicacion o un review o software
     * @param string $pathServidor directorio donde estan guardados los archivos
     */
    public function guardarArchivoFicha($oFicha, $pathServidor)
    {
    	try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->guardarArchivosFicha($oFicha);
        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            $aArchivos = $oFicha->getArchivos();
            if(count($aArchivos) > 0){
                foreach($aArchivos as $oArchivo){
                    $pathServidorArchivo = $pathServidor.$oArchivo->getNombreServidor();
                    if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                        unlink($pathServidorArchivo);
                    }
                }
                $oFicha->setArchivos(null);
            }
            throw $e;
        }
    }

    /**
     * Sirve para guardar todos los embedVideos asociados en tiempo de ejecucion a un objeto
     * que herede de FichaAbstract.
     *     
     * @param FichaAbstract $oFicha puede ser tanto una publicacion o un review
     */
    public function guardarEmbedVideosFicha($oFicha)
    {
    	try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->guardarEmbedVideosFicha($oFicha);
        }catch(Exception $e){
            $oFicha->setEmbedVideos(null);
            throw $e;
        }
    }
    
    public function existeUsuarioUrlKey($iUsuarioId, $sUrlTokenKey)
    {
        try{
            $filtro = array('u.id' => $iUsuarioId, 'u.urlTokenKey' => $sUrlTokenKey);
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existeDocumentoUsuario($numeroDocumento)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existeDocumentoUsuario($numeroDocumento);
        }catch(Exception $e){
            throw $e;
        }        
    }
    
    /**
     * Se diferencia de buscar publicaciones visitantes porque no arregla los filtros de moderacion y de publico
     */
    public function buscarPublicacionesComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{            
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $filtro["f.activo"] = "1";
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * Agrega el filtro del usuario que esta logueado
     */
    public function buscarPublicacionesUsuario($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["usuario"] = $oUsuario->getId();
            
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }
    
    public function buscarPublicacionesVisitantes($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $filtro["f.activo"] = "1";
            $filtro["publico"] = "1";
            $filtro["m.sModeracionEstado"] = "aprobado";
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * Se diferencia de buscar publicaciones visitantes porque no arregla los filtros de moderacion y de publico
     */
    public function buscarSoftwareComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $filtro["f.activo"] = "1";
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * Agrega el filtro del usuario que esta logueado
     */
    public function buscarSoftwareUsuario($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["s.usuarios_id"] = $oUsuario->getId();

            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }
    
    public function buscarSoftwareVisitantes($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $filtro["f.activo"] = "1";
            $filtro["s.publico"] = "1";
            $filtro["m.sModeracionEstado"] = "aprobado";
            $oSoftwareIntermediary = PersistenceFactory::getSoftwareIntermediary($this->db);
            return $oSoftwareIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }
    
    /**
     * Agrega el filtro del usuario que esta logueado
     */
    public function buscarInstitucionesUsuario($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["i.usuario_id"] = $oUsuario->getId();

            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }
    
    public function buscarInstitucionesVisitantes($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $parametros = FrontController::getInstance()->getPlugin('PluginParametros');
            $iCantMaxDenuncias = (int)$parametros->obtener('CANT_MAX_DENUNCIAS');

            $filtro["maxDenuncias"] = $iCantMaxDenuncias;
            $filtro["m.sModeracionEstado"] = "aprobado";
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw $e;
        }
    }

    /**
     * @return array($cantFotos, $cantVideos, $cantArchivos)
     */
    public function obtenerCantidadMultimediaFicha($iFichaId)
    {
        try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->obtenerCantidadElementosAdjuntos($iFichaId);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function obtenerComentariosPublicacion($iPublicacionId)
    {
        try{
            $oComentariosIntermediary = PersistenceFactory::getComentarioIntermediary($this->db);
            $filtro = array('c.publicaciones_id' => $iPublicacionId);
            $iRecordsTotal = 0;
            return $oComentariosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }        
    }

    public function obtenerComentariosReview($iReviewId)
    {
        try{
            $oComentariosIntermediary = PersistenceFactory::getComentarioIntermediary($this->db);
            $filtro = array('c.reviews_id' => $iReviewId);
            $iRecordsTotal = 0;
            return $oComentariosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function obtenerComentariosSoftware($iSoftwareId)
    {
        try{
            $oComentariosIntermediary = PersistenceFactory::getComentarioIntermediary($this->db);
            $filtro = array('c.software_id' => $iSoftwareId);
            $iRecordsTotal = 0;
            return $oComentariosIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function guardarComentariosFicha($oFicha)
    {
    	try{
            $oComentariosIntermediary = PersistenceFactory::getComentarioIntermediary($this->db);
            return $oComentariosIntermediary->guardarComentariosFicha($oFicha);
        }catch(Exception $e){
            $oFicha->setComentarios(null);
            throw $e;
        }        
    }

    /**
     *  no hace falta distinguir tipo review/publicacion
     *  porque se borra desde ficha abstracta en cascada
     */
    public function borrarPublicacion($oFicha, $pathServidorFotos, $pathServidorArchivos)
    {
        try{
            $aFotos = $oFicha->getFotos();
            $aArchivos = $oFicha->getArchivos();
            //los videos no van porque estamos usando los embed que no se guardan en el servidor

            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            $result = $oPublicacionIntermediary->borrar($oFicha->getId());
            if($result){
                //borro archivos de fotos y adjuntos en el servidor, los registros en db volaron en cascada
                if(null != $aFotos){
                    foreach($aFotos as $oFoto){
                        $aNombreArchivos = $oFoto->getArrayNombres();

                        foreach($aNombreArchivos as $nombreServidorArchivo){
                            $pathServidorArchivo = $pathServidorFotos.$nombreServidorArchivo;
                            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                                unlink($pathServidorArchivo);
                            }
                        }
                    }
                }
                if(null != $aArchivos){
                    foreach($aArchivos as $oArchivo){
                        $pathServidorArchivo = $pathServidorArchivos.$oArchivo->getNombreServidor();
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }
            }

            return $result;
        }catch(Exception $e){
            throw $e;
            return false;
        }            
    }

    public function borrarComentario($iComentarioId)
    {
    	try{
            $oComentarioIntermediary = PersistenceFactory::getComentarioIntermediary($this->db);
            return $oComentarioIntermediary->borrar($iComentarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerRazonesDenuncia()
    {
    	try{
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            return $oDenunciaIntermediary->obtenerRazonesDenuncia();
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * @param $oObj tiene que soportar la interfaz de metodos de denuncias y los metodos basicos (getId, etc)
     */
    public function guardarDenuncias($oObj)
    {
    	try{
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            return $oDenunciaIntermediary->guardarDenunciasEntidad($oObj);
        }catch(Exception $e){
            throw $e;
        }        
    }

    public function usuarioEnvioDenunciaInstitucion($iInstitucionId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            $filtro = array('d.instituciones_id' => $iInstitucionId, 'usuarios_id' => $iUsuarioId);
            return $oDenunciaIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function usuarioEnvioDenunciaFicha($iFichaId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oDenunciaIntermediary = PersistenceFactory::getDenunciaIntermediary($this->db);
            $filtro = array('d.fichas_abstractas_id' => $iFichaId, 'usuarios_id' => $iUsuarioId);
            return $oDenunciaIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }
}