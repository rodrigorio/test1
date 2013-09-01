<?php

/**
 * Controlador principal de la 'logica de negocio'. 
 *
 */
class IndexController
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
	
    /**
     * registra un invitado en el sistema para que pase a ser un usuario.
     * este metodo no crea el objeto perfil ni actualiza los permisos de sesion.
     *
     * recibe como parametro un stdClass
     * y devuelve un objeto Usuario si se pudo registrar con exito.
     */
    public function registrarInvitado($oObj){
    	try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->registrarInvitado($oObj);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existePasswordTemporal($iUsuarioId)
    {
    	try{
            //tengo en cuenta que no este expirada.
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_REC_PASS');

            $filtro = array('upt.usuarios_id' => $iUsuarioId,
                            'expiracion' => $cantDiasExpiracion);
            
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existePasswordTemporal($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existePasswordTemporalToken($sToken)
    {
    	try{
            //tengo en cuenta que no este expirada.
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_REC_PASS');

            $filtro = array('upt.token' => $sToken,
                            'expiracion' => $cantDiasExpiracion);

            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existePasswordTemporal($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function borrarPasswordTemporalExpiradaUsuario($iUsuarioId)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_REC_PASS');

            $oUsuarioIntermediary->borrarPasswordTemporalExpiradaUsuario($iUsuarioId, $iDiasExpiracion);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Crea un password temporal y lo asocia al usuario
     */
    public function crearPasswordTemporal($oUsuario)
    {
    	try{
            $sPassword = Utils::generarPassword();
            $sPasswordMd5 = md5($sPassword);
            $dTime = time();
            $sToken = md5($sPassword.$dTime);
            $sEmail = $oUsuario->getEmail();

            $oPasswordTemporal = new stdClass();
            $oPasswordTemporal->sPassword = $sPassword;
            $oPasswordTemporal->sPasswordMd5 = $sPasswordMd5;
            $oPasswordTemporal->sToken = $sToken;
            $oPasswordTemporal->sEmail = $sEmail;
            $oPasswordTemporal = Factory::getPasswordTemporalInstance($oPasswordTemporal);

            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $result = $oUsuarioIntermediary->insertarPasswordTemporal($oPasswordTemporal, $oUsuario->getId());

            if($result){
                $oUsuario->setPasswordTemporal($oPasswordTemporal);
            }

            return $result;
            
        }catch(Exception $e){
            throw $e;
        }
    }

    public function confirmarPasswordTemporal($sToken)
    {
    	try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EXPIRACION_REC_PASS');
            return $oUsuarioIntermediary->confirmarPasswordTemporal($sToken, $iDiasExpiracion);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * devuelve array con id de registro y descripcion de documento
     * para generar el select de tipo documento
     */
    public function obtenerTiposDocumentos()
    {
        $oDocumentoTiposIntermediary = PersistenceFactory::getDocumentoTiposIntermediary($this->db);
        return $oDocumentoTiposIntermediary->obtenerTiposDocumentos();
    }

    ///*** METODOS BASICOS DE ADJUNTOS ***///
    
    public function borrarFoto($oFoto, $pathServidor)
    {
    	try{
            $aNombreArchivos = $oFoto->getArrayNombres();

            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $oFotoIntermediary->borrar($oFoto);

            foreach($aNombreArchivos as $nombreServidorArchivo){
                $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                    unlink($pathServidorArchivo);
                }
            }

        }catch(Exception $e){
            throw $e;
        }
    }

    public function borrarEmbedVideo($oEmbedVideo)
    {
    	try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->borrar($oEmbedVideo);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function borrarArchivo($oArchivo, $pathServidor)
    {
    	try{
            $pathServidorArchivo = $pathServidor.$oArchivo->getNombreServidor();

            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $oArchivoIntermediary->borrar($oArchivo);

            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                unlink($pathServidorArchivo);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve una foto suelta sin asociarse a ningun objeto.
     * Esto se necesita para el formulario en el que se modifica orden, titulo, etc.
     * Tambien para obtener el objeto cuando se tiene que borrar.
     */
    public function getFotoById($iFotoId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.id' => $iFotoId);
            $iRecordsTotal = 0;
            $aFotos = $oFotoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aFotos){
                return $aFotos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function getEmbedVideoById($iEmbedVideoId)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.id' => $iEmbedVideoId);
            $iRecordsTotal = 0;
            $aEmbedVideos = $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aEmbedVideos){
                return $aEmbedVideos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }
    
    public function getFotoUrlKey($iFotoId, $sUrlKey)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.urlKey' => $sUrlKey, 'f.id' => $iFotoId);
            $iRecordsTotal = 0;
            $aFotos = $oFotoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aFotos){
                return $aFotos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function getEmbedVideoUrlKey($iEmbedVideoId, $sUrlKey)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.urlKey' => $sUrlKey, 'v.id' => $iEmbedVideoId);
            $iRecordsTotal = 0;
            $aEmbedVideos = $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aEmbedVideos){
                return $aEmbedVideos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function getArchivoById($iArchivoId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.id' => $iArchivoId);
            $iRecordsTotal = 0;
            $aArchivos = $oArchivoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aArchivos){
                return $aArchivos[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de foto.
     * Titulo, descripcion, etc.
     *
     * No sirve para asociar la foto a ninguna entidad
     */
    public function guardarFoto($oFoto)
    {
    	try{
            if(null === $oFoto->getId()){
                throw new Exception("La foto no posee Id");
            }
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->actualizar($oFoto);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de archivo.
     * Titulo, descripcion, orden, etc.
     *
     * No sirve para asociar el archivo a ninguna entidad
     */
    public function guardarArchivo($oArchivo)
    {
    	try{
            if(null === $oArchivo->getId()){
                throw new Exception("El archivo no posee Id");
            }
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->actualizar($oArchivo);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este metodo se debe usar solo para guardar la informacion del formulario de edicion de foto.
     * Titulo, descripcion, etc.
     *
     * No sirve para asociar la foto a ninguna entidad
     */
    public function guardarEmbedVideo($oEmbedVideo)
    {
    	try{
            if(null === $oEmbedVideo->getId()){
                throw new Exception("El video no posee Id");
            }
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->actualizar($oEmbedVideo);
        }catch(Exception $e){
            throw $e;
        }
    }
}