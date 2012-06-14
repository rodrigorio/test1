<?php

/**
 * Description of class
 *
 * @author Matias Velilla
 */
class SeguimientosController
{
    
    /**
     * @var Instancia de DB
     */
    private $db = null;

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
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }

    /**
     *
     * @return array Nombre de la clase => Descripcion
     */
    public function obtenerTiposSeguimiento()
    {
    	try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->obtenerTiposSeguimientos();
        }catch(Exception $e){
            throw new Exception($e);
        }        
    }

    public function guardarSeguimiento($oSeguimiento){
    	try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->guardar($oSeguimiento);
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    public function buscarSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro[] = array("s.usuarios_id" => $oUsuario->getId());

            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    public function obtenerSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["s.usuarios_id"] = $oUsuario->getId();

            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount);

        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    public function getSeguimientoById($iSeguimientoId){
        try{
            $filtro = array('s.id' => $iSeguimientoId);
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            $aSeguimiento = $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aSeguimiento){
                return $aSeguimiento[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    public function getDiscapacitadoById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('p.id' => $iId);
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            $aDiscapacitado = $oDiscapacitadoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
            if(null !== $aDiscapacitado){
                return $aDiscapacitado[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    /**
     * Este es complejo:
     *
     * Primero hay que comprobar que no haya moderacion pendiente, sino no se pueden hacer cambios.
     * 
     * Si el usuario que guarda es el que creo el discapacitado -> OK
     * Si el usuario que guarda no es el que creo el discapacitado -> agrega en tabla temporal.
     * (luego cuando se aprueban los cambios por el moderador se avisa al usuario que lo creo original).
     *
     * Si el usuario que guarda no es el que creo el discapacitado pero el usuario que lo creo
     * originalmente ya no existe en el sistema entonces el usuario pasa a tener el privilegio de modificar sin moderacion
     *
     * @return Array $result, $moderacion. Result es un boolean que dice si se guardo o no, $moderacion indica si se guardo con pendiente de moderacion
     *     
     */
    public function guardarDiscapacitado($oDiscapacitado){
        try{
            //ojo, extraigo el objeto usuario del objeto perfil.
            $oUsuarioSesion = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
                                   
            //si se modifica (la persona ya existe) y hay moderacion pendiente no se permiten hacer cambios
            if(null !== $oDiscapacitado->getId() && $this->existeModeracionPendiente($oDiscapacitado)){
                return array(false, false);
            }
            
            //si el usuario que guarda no es el que creo la persona
            if(null !== $oDiscapacitado->getUsuario() && $oUsuarioSesion->getId() != $oDiscapacitado->getUsuario()->getId()){
                //guarda en la tabla temporal con el usuario que modifica
                $oDiscapacitado->setUsuario($oUsuarioSesion);
                $result = $oDiscapacitadoIntermediary->guardarModeracion($oDiscapacitado);
                return array($result, true);
            }else{
                //si el usuario que creo la persona ya no existe mas
                if(null === $oDiscapacitado->getUsuario()){
                    //guardo el usuario actual con el privilegio de modificar sin moderacion
                    $oDiscapacitado->setUsuario($oUsuarioSesion);
                }
                $result = $oDiscapacitadoIntermediary->guardar($oDiscapacitado);
                return array($result, false);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Agrega la logica de moderacion pendiente al metodo ya existente en el controlador de comunidad para guardar foto de perfil.
     *
     * @return Array $result, $moderacion. Result es un boolean que dice si se guardo o no, $moderacion indica si se guardo con pendiente de moderacion*
     */
    public function guardarFotoPerfilDiscapacitado($aNombreArchivos, $pathServidor, $oDiscapacitado)
    {
        try{
            $oUsuarioSesion = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);

            if(null !== $oDiscapacitado->getId() && $this->existeModeracionPendiente($oDiscapacitado)){
                return array(false, false);
            }
            
            //si el usuario que cambia la foto no es el que creo la persona
            if(null !== $oDiscapacitado->getUsuario() && $oUsuarioSesion->getId() != $oDiscapacitado->getUsuario()->getId()){
                /*
                 * aca hay un detalle porque tengo que mantener los archivos por si se aprueba
                 * el cambio de foto de perfil y guardar los datos en la tabla temporal.
                 *
                 * Ademas tengo que mantener la foto vieja por si se cancelan los cambios.
                 * 
                 */
                $oFoto = new stdClass();
                $oFoto->sNombreBigSize = $aNombreArchivos['nombreFotoGrande'];
                $oFoto->sNombreMediumSize = $aNombreArchivos['nombreFotoMediana'];
                $oFoto->sNombreSmallSize = $aNombreArchivos['nombreFotoChica'];
                $oFotoPerfil = Factory::getFotoInstance($oFoto);
                
                $oDiscapacitado->setUsuario($oUsuarioSesion);
                $oDiscapacitado->setFotoPerfil($oFotoPerfil);

                $cambioFoto = true;
                $result = $oDiscapacitadoIntermediary->guardarModeracion($oDiscapacitado, $cambioFoto);
                return array($result, true);
            }else{
                //si el usuario que creo la persona ya no existe mas
                if(null === $oDiscapacitado->getUsuario()){                    
                    //guardo el usuario actual con el privilegio de modificar sin moderacion
                    $oDiscapacitado->setUsuario($oUsuarioSesion);
                    $oDiscapacitadoIntermediary->guardar($oDiscapacitado);
                }
                //piso la foto existente. y guardo sin moderacion de la forma tradicional
                $result = ComunidadController::getInstance()->guardarFotoPerfil($aNombreArchivos, $pathServidor, $oDiscapacitado);
                return array($result, false);
            }
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Si el discapacitado tiene una moderacion pendiente de modificacion de datos devuelve true
     */
    public function existeModeracionPendiente($oDiscapacitado)
    {
        try{
            $filtro = array('dm.id' => $oDiscapacitado->getId());
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->existeModeracion($filtro);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * Hay que comprobar que no tenga seguimiento de ningun usuario.
     * Si esta libre de seguimientos se borra desde el administrador.
     */
    public function borrarDiscapacitado($iDiscapacitadoId, $pathServidor){
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            $result = false;                       
            if(!$oDiscapacitadoIntermediary->tieneSeguimientos($iDiscapacitadoId)){

                $filtro = array('d.id' => $iDiscapacitadoId);
                $aDiscapacitado = $oDiscapacitadoIntermediary->obtener($filtro, $iRecordsTotal);
                $oDiscapacitado = $aDiscapacitado[0];

                $result = $oDiscapacitadoIntermediary->borrar($iDiscapacitadoId);
                
                if($result && null !== $oDiscapacitado->getFotoPerfil()){
                    
                    $aNombreArchivos = $oDiscapacitado->getFotoPerfil()->getArrayNombres();
                    
                    foreach($aNombreArchivos as $nombreServidorArchivo){
                        $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
                        if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                            unlink($pathServidorArchivo);
                        }
                    }
                }

                return $result;
            }else{
                return false;
            }
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * @return Array|Discapacitado
     */
    public function obtenerDiscapacitado($filtro, &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function existeDiscapacitado($filtro){
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->existe($filtro);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function getPracticaById($iPracticaId){
        try{
            $filtro = array('p.id' => $iPracticaId);
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            $aPractica = $oPracticaIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aPractica){
                return $aPractica[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    public function obtenerPracticas($filtro = array(), &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            return $oPracticaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw new Exception($e);
        }
    }
    
    /**
     * @return array|null
     */
    public function obtenerFotosSeguimiento($iSeguimientoId)
    {
        try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            $filtro = array('f.seguimientos_id' => $iSeguimientoId);
            return $oFotoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }        
    }

    /**
     * @return array|null
     */
    public function obtenerArchivosSeguimiento($iSeguimientoId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.seguimientos_id' => $iSeguimientoId);
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }          
    }
    
 /**
     * @return array|null
     */
    public function obtenerArchivoAntecedente($iSeguimientoId)
    {
        try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            $filtro = array('a.seguimientos_id' => $iSeguimientoId, 'a.tipo'=>"antecedentes");
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }          
    }

    public function eliminarSeguimiento($oSeguimiento, $pathServidorFotos, $pathServidorArchivos){
        try{            
            $aFotos = $oSeguimiento->getFotos();
            $aArchivos = $oSeguimiento->getArchivos();
            
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            $result = $oSeguimientoIntermediary->borrar($oSeguimiento->getId());
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
    
    public function guardarAntecedentesFile($seguimiento, $nombreArchivo, $tipoMimeArchivo, $tamanioArchivo, $nombreServidorArchivo, $pathServidor) {
    	try{           
            //creo el objeto archivo y lo guardo.
            $oArchivo = new stdClass();
            $oArchivo->sNombre 	= $nombreArchivo;
            $oArchivo->sNombreServidor = $nombreServidorArchivo;
            $oArchivo->sTipoMime= $tipoMimeArchivo;
            $oArchivo->iTamanio = $tamanioArchivo;
            $antecedentes = Factory::getArchivoInstance($oArchivo);

            $antecedentes->setTipoAntecedentes();
            $antecedentes->isModerado(false);
            $antecedentes->isActivo(true);
            $antecedentes->isPublico(false);
            $antecedentes->isActivoComentarios(false);
            
            //si ya tenia un archivo de antecedente el seguimiento borro el actual
            if(null !== $seguimiento->getArchivoAntecedentes()){
                $this->borrarAntecedentesFile($seguimiento, $pathServidor);
            }
            
            $seguimiento->setArchivoAntecedentes($antecedentes);

            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->guardarAntecedentesFile($seguimiento);
            
        }catch(Exception $e){

            $pathServidorArchivo = $pathServidor.$nombreServidorArchivo;
            if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                unlink($pathServidorArchivo);
            }
            $usuario->setArchivoAntecedentes(null);
            
            throw new Exception($e->getMessage());
        }
    }
    
    public function borrarAntecedentesFile($seguimiento, $pathServidor)
    {
    	try{
            if(null === $seguimiento->getArchivoAntecedentes()){
                throw new Exception("El seguimiento no posee archivo de antecedentes");
            }

            IndexController::getInstance()->borrarArchivo($seguimiento->getArchivoAntecedentes(), $pathServidor);

            $seguimiento->setArchivoAntecedentes(null);

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @return array($cantFotos, $cantVideos, $cantArchivos)
     */
    public function obtenerCantidadMultimediaSeguimiento($iSeguimientoId)
    {
        try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->obtenerCantidadElementosAdjuntos($iSeguimientoId);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Devuelve true si la foto pertenece a un seguimiento creado por el usuario que esta logueado.
     *
     * @return boolean true si la foto pertenece al integrante logueado.
     */
    public function isFotoSeguimientoUsuario($iFotoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->isFotoSeguimientoUsuario($iFotoId, $iUsuarioId);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * @return array|null
     */
    public function obtenerEmbedVideosSeguimiento($iSeguimientoId)
    {
        try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            $filtro = array('v.seguimientos_id' => $iSeguimientoId);
            return $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * similar a $this->isFotoSeguimientoUsuario
     */
    public function isEmbedVideoSeguimientoUsuario($iEmbedVideoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->isEmbedVideoSeguimientoUsuario($iEmbedVideoId, $iUsuarioId);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * similar a $this->isFotoSeguimientoUsuario
     */
    public function isArchivoSeguimientoUsuario($iArchivoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->isArchivoSeguimientoUsuario($iArchivoId, $iUsuarioId);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }

    /**
     * Guarda todas las fotos vinculadas a un seguimiento en tiempo de ejecucion.
     *
     * @param SeguimientoAbstract $oSeguimiento
     * @param string $pathServidor directorio donde estan guardados los archivos
     */
    public function guardarFotoSeguimiento($oSeguimiento, $pathServidor)
    {
    	try{
            $oFotoIntermediary = PersistenceFactory::getFotoIntermediary($this->db);
            return $oFotoIntermediary->guardarFotosSeguimiento($oSeguimiento);
        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            $aFotos = $oSeguimiento->getFotos();
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
                $oSeguimiento->setFotos(null);
            }
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Guarda todos los archivos vinculados a un seguimiento en tiempo de ejecucion.
     *
     * @param SeguimientoAbstract $oSeguimiento
     * @param string $pathServidor directorio donde estan guardados los archivos
     */
    public function guardarArchivoSeguimiento($oSeguimiento, $pathServidor)
    {
    	try{
            $oArchivoIntermediary = PersistenceFactory::getArchivoIntermediary($this->db);
            return $oArchivoIntermediary->guardarArchivosSeguimiento($oSeguimiento);
        }catch(Exception $e){
            //si hubo error borro los archivos en disco
            $aArchivos = $oSeguimiento->getArchivos();
            if(count($aArchivos) > 0){
                foreach($aArchivos as $oArchivo){
                    $pathServidorArchivo = $pathServidorArchivos.$oArchivo->getNombreServidor();
                    if(is_file($pathServidorArchivo) && file_exists($pathServidorArchivo)){
                        unlink($pathServidorArchivo);
                    }
                }
                $oSeguimiento->setArchivos(null);
            }
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Sirve para guardar todos los embedVideos asociados en tiempo de ejecucion a un objeto
     * que herede de SeguimientoAbstract.
     *
     * @param SeguimientoAbstract $oSeguimiento puede ser tanto una SeguimientoPersonalizado o un SeguimientoSCC
     */
    public function guardarEmbedVideosSeguimiento($oSeguimiento)
    {
    	try{
            $oEmbedVideoIntermediary = PersistenceFactory::getEmbedVideoIntermediary($this->db);
            return $oEmbedVideoIntermediary->guardarEmbedVideosSeguimiento($oSeguimiento);
        }catch(Exception $e){
            $oSeguimiento->setEmbedVideos(null);
            throw new Exception($e->getMessage());
        }
    }
}
