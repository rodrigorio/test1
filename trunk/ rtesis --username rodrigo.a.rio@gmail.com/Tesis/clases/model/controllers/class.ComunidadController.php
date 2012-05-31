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
	

    public function obtenerUltimaPublicacion()
    {

    }

    public function enviarInvitacion($oUsuario, $oInvitado, $sDescripcion){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->enviarInvitacion($oUsuario,Factory::getInvitadoInstance($oInvitado), $sDescripcion);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function listaPaises($array, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oPaisIntermediary = PersistenceFactory::getPaisIntermediary($this->db);
            return $oPaisIntermediary->obtener($array, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    /**
     *
     */
    public function getPaisById($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oPaisIntermediary = PersistenceFactory::getPaisIntermediary($this->db);
            $aPais = $oPaisIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
            if(null !== $aPais){
                return $aPais[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function listaProvinciasByPais($iPaisId,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $filtro = array("p.paises_id"=>$iPaisId);
            $oProvinciaIntermediary = PersistenceFactory::getProvinciaIntermediary($this->db);
            return $oProvinciaIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     */
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
            throw new Exception($e->getMessage());
        }
    }
    
    public function listaCiudadByProvincia($iProvinciaId,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $filtro = array("c.provincia_id" => $iProvinciaId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            return $oCiudadIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     *
     */
    public function getCiudadById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('c.id' => $iId);
            $oCiudadIntermediary = PersistenceFactory::getCiudadIntermediary($this->db);
            $aCiudad = $oCiudadIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
            if(null !== $aCiudad){
                return $aCiudad[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    ///tipea andres
    public function guardarInstitucion($oInstitucion){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->guardar($oInstitucion);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function borrarInstitucion($oInstitucion){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->borrar($oInstitucion);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
    
    public function obtenerInstitucion($filtro,&$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
        }
    }

    public function obtenerInstituciones($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->obtenerInstituciones($filtro,$iRecordsTotal,$sOrderBy,$sOrder,$iIniLimit,$iRecordCount);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function existeInstitucion($filtro){
        try{
            $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
            return $oInstitucionIntermediary->existe($filtro);
        }catch(Exception $e){
           throw new Exception($e->getMessage());
        }
    }

    public function listaTiposDeInstitucion($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    try{
        $oInstitucionIntermediary = PersistenceFactory::getInstitucionIntermediary($this->db);
        return $oInstitucionIntermediary->listaTiposDeInstitucion($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
        }            
    }

    public function existeNombreUsuarioDb($nombreUsuario)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existeNombreUsuarioDb($nombreUsuario);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
        }
    }

    public function buscarUsuarios($filtro,$iRecordsTotal = 0,$sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
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
            $curriculumVitae->isModerado(false);
            $curriculumVitae->isActivo(true);
            $curriculumVitae->isPublico(false);
            $curriculumVitae->isActivoComentarios(false);
            
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
            
            throw new Exception($e->getMessage());
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

            $pathServidorArchivo = $pathServidor.$usuario->getCurriculumVitae()->getNombreServidor();

            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $oArchivoIntermediary->borrar($usuario->getCurriculumVitae());

            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                unlink($pathServidorArchivo);
            }

            $usuario->setCurriculumVitae(null);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
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
            
            throw new Exception($e->getMessage());
        }        
    }

    public function borrarFotoPerfil($oPersona, $pathServidor)
    {
    	try{
            if(null === $oPersona->getFotoPerfil()){
                throw new Exception("El usuario no posee foto de perfil");
            }

            $aNombreArchivos = $oPersona->getFotoPerfil()->getArrayNombres();

            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $oFotoIntermediary->borrar($oPersona->getFotoPerfil());

            foreach($aNombreArchivos as $nombreServidorArchivo){
                $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                    unlink($pathServidorArchivo);
                }
            }

            $oPersona->setFotoPerfil(null);
            
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            null !== $oUsuario->getCurriculumVitae() &&
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
            throw new Exception($e->getMessage());
        }
    }

    public function existePublicacion($filtro){
        try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->existe($filtro);
        }catch(Exception $e){
           throw new Exception($e->getMessage());
        }
    }

     /**
     * @param stdClass $obj
     */
    public function guardarPublicacion($oPublicacion){
    	try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->guardar($oPublicacion);
          }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function guardarReview($oReview){
    	try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->guardarReview($oReview);
          }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function obtenerPublicacion($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
        }        
    }

    /**
     * @return array|null
     */
    public function obtenerFotosPublicacion($iPublicacionId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.publicacion_id' => $iPublicacionId);
            return $oFotoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }        
    }
   /**
     * @return array|null
     */
    
    public function obtenerVideosPublicacion($iPublicacionId)
    {
        try{
            $oVideoIntermediary = PersistenceFactory::getVideoIntermediary($this->db);
            $filtro = array('v.publicacion_id' => $iPublicacionId);
            return $oVideoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }        
    }
   /**
     * @return array|null
     */
    public function obtenerArchivosPublicacion($iPublicacionId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.publicacion_id' => $iPublicacionId);
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }          
    }
/**
     * @return array|null
     */
    public function obtenerArchivosCategoria($iCategoriaId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.categoria_id' => $iCategoriaId);
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }          
    }
    /**
     *
     * @param array $aNombreArchivos 3 celdas con los nombres de los archivos ['nombreFotoGrande'] ['nombreFotoMediana'] ['nombreFotoChica']
     * @param string $pathServidor directorio donde estan guardadas las fotos
     * @param FichaAbstract puede ser tanto un discapacitado, un usuario o cualquiera que herede de persona
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
            throw new Exception($e->getMessage());
        }        
    }
    
    public function existeDocumentoUsuario($numeroDocumento)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->existeDocumentoUsuario($numeroDocumento);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }        
    }

    /**
     * Se diferencia de buscar publicaciones visitantes porque no arregla los filtros de moderacion y de publico
     */
    public function buscarPublicacionesComunidad($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oPublicacionIntermediary = PersistenceFactory::getPublicacionIntermediary($this->db);
            return $oPublicacionIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch (Exception $e){
            throw new Exception($e->getMessage());
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
            throw new Exception($e->getMessage());
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
            throw new Exception($e);
            return false;
        }            
    }
}
