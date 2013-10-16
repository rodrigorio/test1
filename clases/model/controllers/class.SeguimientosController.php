<?php

/**
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
            throw $e;
        }        
    }

    public function guardarSeguimiento($oSeguimiento){
    	try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->guardar($oSeguimiento);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function buscarSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["s.usuarios_id"] = $oUsuario->getId();
            
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->buscar($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oUsuario = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario();
            $filtro["s.usuarios_id"] = $oUsuario->getId();

            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount);

        }catch(Exception $e){
            throw $e;
        }
    }

    public function getSeguimientoById($iSeguimientoId){
        try{
            $filtro = array('s.id' => $iSeguimientoId);
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aSeguimiento = $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aSeguimiento){
                return $aSeguimiento[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
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
            throw $e;
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
    public function guardarDiscapacitado($oDiscapacitado)
    {
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
            throw $e;
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
            throw $e;
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
            throw $e;
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
            throw $e;
            return false;
        }
    }

    public function existeDiscapacitado($filtro){
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            return $oDiscapacitadoIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function getPracticaById($iPracticaId){
        try{
            $filtro = array('p.id' => $iPracticaId);
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aPractica = $oPracticaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
            if(null !== $aPractica){
                return $aPractica[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
            return false;
        }
    }

    public function obtenerPracticas($filtro = array(), &$iRecordsTotal=0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            return $oPracticaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
        }catch(Exception $e){
            throw $e;
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
            $iRecordsTotal = 0;
            return $oFotoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
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
            $iRecordsTotal = 0;
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
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
            $iRecordsTotal = 0;
            return $oArchivoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
            return false;
        }          
    }

    public function eliminarSeguimiento($oSeguimiento, $pathServidorFotos, $pathServidorArchivos){
        try{            
            $aFotos = $oSeguimiento->getFotos();
            $aArchivos = $oSeguimiento->getArchivos();
            
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            $result = $oSeguimientoIntermediary->borrar($oSeguimiento);
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
            
            throw $e;
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
            throw $e;
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
            throw $e;
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
            throw $e;
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
            $iRecordsTotal = 0;
            return $oEmbedVideoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);
        }catch(Exception $e){
            throw $e;
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
            throw $e;
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
            throw $e;
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
            throw $e;
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
            throw $e;
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
            throw $e;
        }
    }
    
    /**
     * Obtener diagnostico de un seguimiento
     *
     */
    public function getDiagnosticoBySeg($oSeguimiento)
    {
    	try{
    		$filtro = array('s.id' => $oSeguimiento->getId());
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);
            $iRecordsTotal = 0;
            if(get_class($oSeguimiento)=="SeguimientoPersonalizado"){
            	$aDiagnostico = $oDiagnosticoIntermediary->obtenerPersonalizado($filtro, $iRecordsTotal, null, null, null, null);
            }else{
            	$aDiagnostico = $oDiagnosticoIntermediary->obtenerSCC($filtro, $iRecordsTotal, null, null, null, null);
            }
            if(null !== $aDiagnostico){
                return $aDiagnostico[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getDiagnosticoById($iId)
    {
    	try{
            $filtro = array('d.id' => $iId);
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDiagnosticoIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getCicloById($iCicloId)
    {
    	try{
            $filtro = array('c.id' => $iCicloId);
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            $iRecordsTotal = 0;
            $aCiclo = $oCicloIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aCiclo){
                return $aCiclo[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
     public function getNivelById($iNivelId)
      {
    	try{
            $filtro = array('n.id' => $iNivelId);
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            $iRecordsTotal = 0;
            $aNivel = $oNivelIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aNivel){
                return $aNivel[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function existeNivelByDescripcion($sDescripcion)
    {
        try{
            $filtro = array('n.descripcion' => $sDescripcion);
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            return $oNivelIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }  
    }
    
    public function getAreaById($iAreaId)
      {
    	try{
            $filtro = array('a.id' => $iAreaId);
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aArea = $oAreaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aArea){
                return $aArea[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function guardarDiagnostico($oDiagnostico){
        try{          
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);                        
            return $oDiagnosticoIntermediary->guardar($oDiagnostico);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getNiveles($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oNivelIntermediary = PersistenceFactory::getNivelIntermediary($this->db);
            return $oNivelIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getCiclos($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            return $oCicloIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getAreas($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
    {
    	try{
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            return $oAreaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtener ciclo por id de nivel
     *
     */
    public function getCiclosByNivelId($iNivelId)
    {
    	try{
            $filtro = array('n.id' => $iNivelId);
            $oCicloIntermediary = PersistenceFactory::getCicloIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oCicloIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Obtener areas por id de ciclo
     *
     */
    public function getAreasByCicloId($iCicloId)
    {
    	try{
            $filtro = array('c.id' => $iCicloId);
            $oAreaIntermediary = PersistenceFactory::getAreaIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oAreaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    /**
     * Obtener areas por id de ciclo
     *
     */
    public function getEjesByAreaId($iAreaId)
    {
    	try{
            $filtro = array('a.id' => $iAreaId);
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            $iRecordsTotal = 0;
            return  $oEjeTematicoIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve un array con usuarios realizando seguimientos a una persona
     */
    public function obtenerUsuariosAsociadosPersona($iDiscapacitadoId)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->obtenerUsuariosAsociadosPersona($iDiscapacitadoId);
        }catch(Exception $e){
            throw $e;
        }         
    }

    /**
     * Obtener Unidades 
     *
     */
   public function getUnidades($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount )
      {
    	try{    		
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }

   public function getUnidadById($iUnidadId)
    {
    	try{
            $filtro = array('u.id' => $iUnidadId);
            $iRecordsTotal = 0;
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            $aUnidad = $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aUnidad){
                return $aUnidad[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve todas las unidades para una entrada.
     * Cada una de las unidades tiene el listado completo de variables con su respectivo valor.
     * Las unidades son todas de edicion Regular. (se modifican los valores en cada entrada).
     * Se muestran inclusive las de borrado logico porque no se pierde la informacion guardada
     *
     */
    public function getUnidadesByEntrada($oEntrada)
    {
    	try{
            //primero obtengo las unidades que se ASOCIARON hasta la fecha/hora de la entrada
            $iSeguimientoId = $oEntrada->getSeguimientoId();
            $dFechaHora = $oEntrada->getFechaHoraCreacion();

            $filtro = array('su.seguimientos_id' => $iSeguimientoId,
                            'su.fechaHora' => $dFechaHora,
                            'u.tipoEdicion' => 'regular',
                            // con esto se cuando si mostrar las unidades que se borraron logicamente
                            'u.fechaBorradoLogico' => $dFechaHora);
                       
            $iRecordsTotal = 0;
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            $aUnidades = $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, 'u.fechaHora', 'asc', null, null);

            //ahora asigno todas las variables a cada unidad filtrando por entrada
            if(count($aUnidades)>0){
                foreach($aUnidades as $oUnidad){
                    //agrego lista de variables con el valor correspondiente a la entrada                    
                    $aVariables = $this->getVariablesContenidoByUnidadId($oEntrada->getId(), $oUnidad->getId());
                    $oUnidad->setVariables($aVariables);
                }
            }

            return $aUnidades;            
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Se utiliza este metodo porque es muy costoso levantar todo el array de unidades y de seguimientos
     * para saber la cantidad a la cual la unidad esta asociada.
     *
     * @param int $iUnidadId
     *
     * @return array($iCantVariables, $iCantSeguimientos)
     */
    public function obtenerMetadatosUnidad($iUnidadId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->obtenerMetadatosUnidad($iUnidadId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }        
    }

    /**
     * Obtener Variables
     *
     */
    public function getVariables($filtro, &$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount)
    {
    	try{
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            return $oVariableIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Obtener variables por id de unidad
     *
     * @param boolean $bBorradoLogico si FALSE entonces no trae las variables que fueron borradas logicamente
     */
    public function getVariablesByUnidadId($iUnidadId, $bBorradoLogico = true)
    {
    	try{
            $filtro = array('v.unidad_id' => $iUnidadId);
            if(!$bBorradoLogico){
                $filtro['v.borradoLogico'] = "0";
            }
            
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oVariableIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve array de variables para una unidad con el valor actual para una fecha en formato SQL
     */
    public function getVariablesContenidoByUnidadId($iEntradaId, $iUnidadId, $bBorradoLogico = true)
    {
    	try{
            $filtro = array('e.id' => $iEntradaId, 'v.unidad_id' => $iUnidadId);
            if(!$bBorradoLogico){
                $filtro['v.borradoLogico'] = "0";
            }
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oVariableIntermediary->obtenerContenido($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getVariableById($iVariableId)
    {
    	try{
            $filtro = array('v.id' => $iVariableId);
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            $iRecordsTotal = 0;
            $aVariables = $oVariableIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aVariables){
                return $aVariables[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve true si el nombre ya esta siendo utilizado para una variable
     * dentro de la unidad
     */
    public function existeVariableUnidadIntegrante($sNombreVariable, $iUnidadId)
    {
    	try{
            $filtro = array('v.nombre' => $sNombreVariable, 'v.unidad_id' => $iUnidadId);
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            return $oVariableIntermediary->existe($filtro);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve array de objetos modalidad para una variable del tipo cualitativa.
     */
    public function getModalidadesByVariableId($iVariableCualitativaId)
    {
    	try{
            $filtro = array('vcm.variables_id' => $iVariableCualitativaId);
            $oModalidadIntermediary = PersistenceFactory::getModalidadIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oModalidadIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtiene todas las unidades administrables que el usuario logueado creo
     * para asociar a sus seguimientos personalizados
     *
     */
    public function obtenerUnidadesPersonalizadasUsuario($filtro, &$iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount)
    {
    	try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            
            $filtro["u.usuarios_id"] = $iUsuarioId;
            $filtro["u.preCargada"] = "0";
            $filtro["u.asociacionAutomatica"] = "0";
            $filtro["u.borradoLogico"] = "0";
            
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

     /**
     * Obtener unidades  por id de seguimiento
     *
     * Se utiliza para saber las unidades asociadas, las variables no tienen el valor correspondiente a una fecha
     *
     * @param boolean $bBorradoLogico Si FALSE entonces no devuelve las unidades eliminadas
     */
    public function getUnidadesBySeguimientoId($iSeguimientoId, $bBorradoLogico = true, $sTipoEdicion = null, $sOrderBy, $sOrder)
    {        
    	try{
            $filtro = array('su.seguimientos_id' => $iSeguimientoId);
            if(!$bBorradoLogico){
                $filtro['u.borradoLogico'] = "0";
            }
            if(null !== $sTipoEdicion){
                $filtro['u.tipoEdicion'] = $sTipoEdicion;
            }
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oUnidadIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Guardar Variables
     *
     * Si es insertar hace falta el id de la unidad
     *
     */
    public function guardarVariable($oVariable, $iUnidadId = ""){
        try{
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            return $oVariableIntermediary->guardar($oVariable, $iUnidadId);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Guardar Unidades
     *
     */
    public function guardarUnidad($oUnidad){
        try{
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->guardar($oUnidad);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Borrar Unidad
     *
     */
    public function borrarUnidad($oUnidad)
    {
    	try{
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);

            $success = true;
            if(null !== $oUnidad->getVariables()){
                //borro todas las variables de la unidad. si devuelve true no hubo errores.
                $success = $this->borrarVariables($oUnidad->getVariables());
            }

            if($success){
                //en este metodo se fija que si al menos una variable tiene borrado logico la unidad tmb
                //se borra logicamente.
                return $oUnidadIntermediary->borrar($oUnidad);
            }else{
                return false;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * recibe un array de 1 o N variables y las borra fisica o logicamente dependiendo el plazo dispuesto para la edicion de seguimientos.
     *
     * Toda variable que tenga asociado un valor a un seguimiento en una fecha que exceda la cantidad de dias del plazo
     * sera borrada logicamente en el sistema.
     *
     * El metodo esta pensado para que pueda ser utilizado tanto en la eliminacion individual de una variable
     * como en la eliminacion de una unidad con un conjunto N de variables. 
     */
    public function borrarVariables($aVariables)
    {
    	try{
            $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');            
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);

            //genero un string con los ids separados con ',' para que se realize la transaccion en el sql.
            $sIds = "";
            foreach($aVariables as $oVariable){
                $sIds .= $oVariable->getId().",";
                $sIds = substr($sIds, 0, -1);
            }
            
            return $oVariableIntermediary->borrarVariables($sIds, $cantDiasExpiracion);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function isModalidadVariableUsuario($iModalidadId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oModalidadIntermediary = PersistenceFactory::getModalidadIntermediary($this->db);
            return $oModalidadIntermediary->isModalidadVariableUsuario($iModalidadId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * En las modalidades no se tiene en cuenta el periodo de ventana en el cual se puede editar un seguimiento,
     * porque sino habria que reemplazar la modalidad utilizada por otra antes de ser eliminada.
     *
     * Simplemente siempre que esta asociada con al menos un seguimiento se borra logicamente.
     */
    public function borrarModalidadVariable($iModalidadId)
    {
    	try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oModalidadIntermediary = PersistenceFactory::getModalidadIntermediary($this->db);

            //si la modalidad se uso como valor de variable en seguimientos asociados el borrado es logico.
            if($oModalidadIntermediary->isUtilizadaEnSeguimientoUsuario($iModalidadId, $iUsuarioId)){                
                return $oModalidadIntermediary->borradoLogico($iModalidadId);
            }else{
                return $oModalidadIntermediary->borrar($iModalidadId);
            }
            
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Obtener Objetivos Personalizados
     *
     * Se utiliza para saber los objetivos asociados a un seguimiento personalizado.
     *
     */
    public function getObjetivosPersonalizadosBySeguimientoId($iSeguimientoId, $sOrderBy, $sOrder)
      {
    	try{    	    
            $filtro = array('op.seguimientos_personalizados_id' => $iSeguimientoId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oObjetivoIntermediary->obtenerObjetivosPersonalizados($filtro, $iRecordsTotal, $sOrderBy, $sOrder, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve un objeto personalizado completo (correponde a un seguimiento personalizado)
     */
    public function getObjetivoPersonalizadoById($iObjetivoId)
    {
    	try{
            $filtro = array('o.id' => $iObjetivoId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aObjetivo = $oObjetivoIntermediary->obtenerObjetivosPersonalizados($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aObjetivo){
                return $aObjetivo[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getObjetivosPersonalizadosByEntrada($iSeguimientoId, $dFechaHora)
    {        
    	try{
            $filtro = array(
                'op.seguimientos_personalizados_id' => $iSeguimientoId,
                'op.fechaCreacion' => $dFechaHora,
                'op.fechaDesactivado' => $dFechaHora
            );
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oObjetivoIntermediary->obtenerObjetivosPersonalizados($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){            
            throw $e;
        }
    }
    
   /**
    * Obtener Objetivos Aprendizaje
    *
    * Se utiliza para saber los objetivos asociados a un seguimiento SCC.
    */
    public function getObjetivosAprendizajeAsociadosSeguimientoScc($iSeguimientoSCCId, $sOrderBy, $sOrder)
      {
    	try{
            $filtro = array('sxo.seguimientos_scc_id' => $iSeguimientoSCCId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oObjetivoIntermediary->obtenerObjetivosAprendizajeAsociadosSeguimientoScc($filtro, $iRecordsTotal, $sOrderBy, $sOrder, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getObjetivosAprendizajeByEntrada($iSeguimientoId, $dFechaHora)
    {
    	try{
            $filtro = array(
                'sxo.seguimientos_scc_id' => $iSeguimientoId,
                'sxo.fechaCreacion' => $dFechaHora,
                'sxo.fechaDesactivado' => $dFechaHora
            );
            
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;            
            return $oObjetivoIntermediary->obtenerObjetivosAprendizajeAsociadosSeguimientoScc($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

   /**
    * obtener un objetivo de aprendizaje asociado a un seguimiento scc (completo, con relevancia, estimacion, etc)
    *
    */
    public function getObjetivoAprendizajeAsociadoSeguimientoSccById($iSeguimientoSCCId, $iObjetivoId)
      {
    	try{
            $filtro = array('sxo.seguimientos_scc_id' => $iSeguimientoSCCId, 
                            'sxo.objetivos_aprendizaje_id' => $iObjetivoId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aObjetivo = $oObjetivoIntermediary->obtenerObjetivosAprendizajeAsociadosSeguimientoScc($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aObjetivo){
                return $aObjetivo[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve objetivos aprendizaje pero TODA la lista, no tiene en cuenta el seguimiento scc
     * (se crean y se modifican solo desde el controlador de admin)
     */
    public function getObjetivosAprendizaje()
    {
    	try{
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oObjetivoIntermediary->obtenerObjetivosAprendizaje($filtro = array(), $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getObjetivoAprendizajeById($iObjetivoAprendizajeId)
    {
    	try{
            $filtro = array('o.id' => $iObjetivoAprendizajeId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aObjetivo = $oObjetivoIntermediary->obtenerObjetivosAprendizaje($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aObjetivo){
                return $aObjetivo[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * lo mismo que getObjetivosAprendizaje() solo que con el filtro de todos los objetivos dentro de un eje determinado
     * esto se usa para el select de creacion/modificacion de objetivo de aprendizaje
     */
    public function getObjetivosAprendizajeByEjeId($iEjeId)
    {
    	try{
            $filtro = array('oa.ejes_id' => $iEjeId);
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oObjetivoIntermediary->obtenerObjetivosAprendizaje($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *  Internamente se guarda la relacion entre seguimiento scc y objetivo aprendizaje
     */
    public function guardarObjetivoAprendizajeSeguimientoScc($oObjetivoAprendizaje, $iSeguimientoSCCId){
        try{        	      		
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->guardarObjetivoAprendizajeSeguimientoScc($oObjetivoAprendizaje, $iSeguimientoSCCId);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Guardar Objetivos Personalizados verifica que sea el usuario que creo el seguimiento
     */
    public function guardarObjetivoPersonalizado($oObjetivo, $iSeguimientoId = null)
    {
        try{            
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->guardarObjetivoPersonalizadoSeguimiento($oObjetivo, $iSeguimientoId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Metodo unificado para borrar objetivos asociados a un seguimiento, tanto scc como personalizado.
     *
     * Sirve para eliminar fisicamente el objetivo junto con todo el historial de evolucion.
     *
     * Fijarse que el borrado fisico solo en el periodo de edicion de seguimientos.
     *
     * Borrado logico seria el desactivar. que hace que, a partir de que un objetivo esta desactivado
     * no aparece en la vista de entradas por fecha*
     */
    public function borrarObjetivoSeguimiento($oObjetivo, $iSeguimientoId = null)
    {
        try{
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            $iObjetivoId = $oObjetivo->getId();
            if($oObjetivo->isObjetivoPersonalizado()){
                return $oObjetivoIntermediary->borrar($iObjetivoId);
            }
            if($oObjetivo->isObjetivoAprendizaje()){
                return $oObjetivoIntermediary->borrarObjetivoAprendizajeSeguimientoSCC($iSeguimientoId, $iObjetivoId);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve una lista de todos los ejes para objetivos personalizados
     * cada eje puede tener una lista de subejes distinta de null. (2 niveles)
     */
    public function getEjesPersonalizados()
    {
    	try{
            $oEjeIntermediary = PersistenceFactory::getEjeIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oEjeIntermediary->obtener($filtro = array(), $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getEjePersonalizadoById($iEjeId)
    {
    	try{
            $filtro = array('ope.id' => $iEjeId);
            $oEjeIntermediary = PersistenceFactory::getEjeIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEje = $oEjeIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aEje){
                return $aEje[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Guardar Ejes Tematicos con estado inicial en un seguimiento SCC
     *
     */
    public function guardarEstadoInicial($oDiagnosticoSCC){
        try{            
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->guardarEstadoInicial($oDiagnosticoSCC);
        }catch(Exception $e){
            throw $e;
        }
    }
            
    public function eliminarEstadoInicial($iEjeTematicoId, $iDiagnosticoSCCId){
        try{        	        	
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->eliminarEstadoInicial($iEjeTematicoId, $iDiagnosticoSCCId);
        }catch(Exception $e){
            throw $e;
        }
    }     
    
    public function getDiagnosticoSeguimientoSCCById($iSeguimientoId)
    {
    	try{
            $filtro = array('s.id' => $iSeguimientoId);
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDiagnosticoIntermediary->obtenerSCC($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getDiagnosticoSeguimientoPersonalizadoById($iSeguimientoId)
    {
    	try{
            $filtro = array('s.id' => $iSeguimientoId);
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oDiagnosticoIntermediary->obtenerPersonalizado($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     *  Obtiene el eje tematico By Id
     */
    public function getEjeTematicoById($iEjeTematicoId)
    {
    	try{
            $filtro = array('e.id' => $iEjeTematicoId);
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEjeTematico = $oEjeTematicoIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aEjeTematico){
                return $aEjeTematico[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    /**
     * Devuelve true si  pertenece a un seguimiento creado por el usuario que esta logueado.
     *
     * @return boolean true si pertenece al integrante logueado.
     */
    public function isDiagnosticoPersonalizadoUsuario($iDiagnosticoPersonalizadoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->isDiagnosticoPersonalizadoUsuario($iUsuarioId, $iDiagnosticoPersonalizadoId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     *
     * Devuelve true si  pertenece a un seguimiento creado por el usuario que esta logueado.
     *
     * @return boolean true si pertenece al integrante logueado.
     */
    public function isDiagnosticoSCCUsuario($iDiagnosticoSCCId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->isDiagnosticoSCCUsuario($iUsuarioId, $iDiagnosticoSCCId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve true si el diagn�stico pertenece a un seguimiento creado por el usuario que esta logueado.
     *
     * @return boolean true si la foto pertenece al integrante logueado.
     */
    public function isEjeTematicoDiagnosticoUsuario($iDiagnosticoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oEjeTematicoIntermediary = PersistenceFactory::getEjeTematicoIntermediary($this->db);
            return $oEjeTematicoIntermediary->isEjeTematicoDiagnosticoUsuario($iDiagnosticoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function isObjetivoPersonalizadoUsuario($iObjetivoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->isObjetivoPersonalizadoUsuario($iObjetivoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function isObjetivoAprendizajeUsuario($iObjetivoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->isObjetivoAprendizajeUsuario($iObjetivoId,$iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve true si un objetivo de aprendizaje ya esta asociado a un seguimiento SCC
     */
    public function existeObjetivoAprendizajeAsociadoSeguimientoSCC($iSeguimientoSCCId, $iObjetivoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oObjetivoIntermediary = PersistenceFactory::getObjetivoIntermediary($this->db);
            return $oObjetivoIntermediary->existeObjetivoAprendizajeSeguimientoSCC($iSeguimientoSCCId, $iObjetivoId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerArrayRelevancias(){
        try{
            $oRelevanciaIntermediary = PersistenceFactory::getRelevanciaIntermediary($this->db);
            return $oRelevanciaIntermediary->obtenerRelevancias();
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getRelevanciaById($iRelevanciaId)
    {
    	try{
            $filtro = array('objr.id' => $iRelevanciaId);
            $oRelevanciaIntermediary = PersistenceFactory::getRelevanciaIntermediary($this->db);
            $iRecordsTotal = 0;
            $aRelevancia = $oRelevanciaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aRelevancia){
                return $aRelevancia[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function isUnidadUsuario($iUnidadId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oUnidadIntermediary = PersistenceFactory::getUnidadIntermediary($this->db);
            return $oUnidadIntermediary->isUnidadUsuario($iUnidadId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function isVariableUsuario($iVariableId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oVariableIntermediary = PersistenceFactory::getVariableIntermediary($this->db);
            return $oVariableIntermediary->isVariableUsuario($iVariableId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    public function isDiagnosticoUsuario($iDiagnosticoId)
    {
        try{
            $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
            $oDiagnosticoIntermediary = PersistenceFactory::getDiagnosticoIntermediary($this->db);

            return $oDiagnosticoIntermediary->isDiagnosticoUsuario($iDiagnosticoId, $iUsuarioId);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Obtener opciones  por id de pregunta
     *
     */
    public function getOpcionesByPreguntaId($iPreguntaId)
    {
    	try{
            $filtro = array('p.id' => $iPreguntaId);
            $oOpcionIntermediary = PersistenceFactory::getOpcionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oOpcionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function getPreguntasByEntrevistaId($iEntevistaId)
    {
    	try{
            $filtro = array('e.id' => $iEntrevistaId);
            $oPreguntaIntermediary = PersistenceFactory::getPreguntaIntermediary($this->db);
            $iRecordsTotal = 0;
            return $opreguntasIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Se fija que un seguimiento tenga asociado las entidades necesarias previas a ingresar entradas por fecha
     *
     * al menos un objetivo ACTIVO.
     */
    public function checkEntradasOK($oSeguimiento)
    {
        try{
            if($oSeguimiento->getAntecedentes() === null){
                return false;
            }

            $oDiagnostico = $oSeguimiento->getDiagnostico();

            if($oDiagnostico->isDiagnosticoPersonalizado() && $oDiagnostico->getDescripcion() === null){
                return false;
            }
            if($oDiagnostico->isDiagnosticoSCC() && $oDiagnostico->getEjesTematicos() === null){
                return false;
            }

            //al menos un objetivo activo                        
            if($oSeguimiento->getObjetivos() === null){
                return false;
            }else{
                $bAux = false;
                foreach($oSeguimiento->getObjetivos() as $oObjetivo)
                {
                    if($oObjetivo->isActivo()){ $bAux = true; break;}
                }

                if(!$bAux){ return false; }
            }

            return true;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getEntradaById($iEntradaId)
    {
    	try{
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);
            $filtro = array('e.id' => $iEntradaId);
            $iRecordsTotal = 0;
            $aEntrada = $oEntradaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
            if(null !== $aEntrada){
                return $aEntrada[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve lista completa de entradas para un seguimiento, los objetos son livianos.
     * puede setearse un periodo de entre fechas como filtro.
     */
    public function getEntradasBySeguimientoId($iSeguimientoId, $dFechaDesde, $dFechaHasta)
    {
    	try{
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);

            $filtroFecha = array('fechaDesde' => $dFechaDesde, 'fechaHasta' => $dFechaHasta);
            $filtro = array('e.seguimientos_id' => $iSeguimientoId);
            $filtro['fechas'] = $filtroFecha;
            $iRecordsTotal = 0;

            return $oEntradaIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }            
    }

    public function getEntradaPorFechaBySeguimientoId($iSeguimientoId, $dFecha)
    {
    	try{
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);

            $sOrderBy = "e.fecha"; $sOrder = "desc";
            $filtro = array('e.seguimientos_id' => $iSeguimientoId, 'e.fecha' => $dFecha);
            $iRecordsTotal = 0;

            $aEntrada = $oEntradaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, 0, 1);

            if(null !== $aEntrada){
                return $aEntrada[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getUltimaEntradaBySeguimiento($iSeguimientoId)
    {
    	try{
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);
            
            $sOrderBy = "e.fecha"; $sOrder = "desc";
            $filtro = array('e.seguimientos_id' => $iSeguimientoId);
            $iRecordsTotal = 0;
            
            $aEntrada = $oEntradaIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy, $sOrder, 0, 1);

            if(null !== $aEntrada){
                return $aEntrada[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Por ahora la regla es que cada nueva entrada tenga todas las unidades asociadas al seguimiento
     * hasta la fecha actual y ademas que todas las unidades que ya tienen informacion de una entrada anterior y no se eliminaron logicamente
     * se copien a los valores de la nueva entrada si el tipo de variable no es de tipo texto.
     *
     * esto es porq en un futuro se generaran graficos a partir de las variaciones en las variables numericas.
     */
    public function crearEntrada($oSeguimiento, $sFechaNuevaEntrada)
    {
        try{
            //primero compruebo que la fecha de la entrada sea efectivamente posterior a la ultima entrada (si es que existe)
            $oUltimaEntrada = $oSeguimiento->getUltimaEntrada();
            if($oUltimaEntrada !== null){
                $dFechaUltimaEntrada = strtotime($oUltimaEntrada->getFecha());
                $dFechaNuevaEntrada = strtotime($sFechaNuevaEntrada);
                if($dFechaUltimaEntrada > $dFechaNuevaEntrada){
                    throw new Exception("La entrada tiene que ser posterior a la ultima creada.");
                }
            }

            //obtengo todas las unidades asociadas al seguimiento hasta el dia de la fecha que no tengan el flag de borrado logico prendido
            $aUnidades = $this->getUnidadesBySeguimientoId($oSeguimiento->getId(), false, "regular", "u.fechaHora", "ASC");
            foreach($aUnidades as $oUnidad){

                //si la unidad ya existe en la ultima entrada copio las variables con valor, sino levanto todas las variables sin valor
                if($oUltimaEntrada !== null){                    
                    $aUnidadesUltimaEntrada = $oUltimaEntrada->getUnidades(); // estas vienen con los valores.
                    $bExiste = false;
                    $iUnidadId = $oUnidad->getId();
                    foreach($aUnidadesUltimaEntrada as $oUnidadUltimaEntrada){
                        $iUltimaEntradaId = $oUnidadUltimaEntrada->getId();
                        if($iUltimaEntradaId == $iUnidadId){
                            $bExiste = true;
                            //copio todas las variables con los valores de la ultima entrada excepto las que son de tipo texto.
                            //SIN INCLUIR LAS QUE POSEEN BORRADO LOGICO
                            $aVariablesUltimaEntrada = $this->getVariablesContenidoByUnidadId($oUltimaEntrada->getId(), $oUnidadUltimaEntrada->getId(), false);
                            foreach($aVariablesUltimaEntrada as $oVariable){
                                if($oVariable->isVariableTexto()){
                                    $oVariable->setValor(null);
                                }
                                $oUnidad->addVariable($oVariable);
                            }
                            break;
                        }
                    }
                }

                if($oUltimaEntrada === null || !$bExiste){
                    //agrego todas las variables con valor == null
                    $aVariables = $this->getVariablesByUnidadId($oUnidad->getId(), false);
                    $oUnidad->setVariables($aVariables);
                }
            }

            //creo el objeto Entrada propiamente dicho
            $oEntrada = new stdClass();
            $oEntrada->iSeguimientoId = $oSeguimiento->getId();
            $oEntrada->dFecha = $sFechaNuevaEntrada;
            $oEntrada->aUnidades = $aUnidades;
            $oEntrada->bGuardada = false;

            if($oSeguimiento->isSeguimientoPersonalizado()){
                $oEntrada = Factory::getEntradaPersonalizadaInstance($oEntrada);
            }
            if($oSeguimiento->isSeguimientoSCC()){
                $oEntrada = Factory::getEntradaSCCInstance($oEntrada);
            }
            
            return $oEntrada;
        }catch(Exception $e){
            throw $e;
        }            
    }

    /**
     * El objeto tiene que venir COMPLETO, cuando entra aca ya tiene todas las unidades con todas las variables/valor
     * Recordar que los objetivos se guardan por separado porque no necesariamente se modifican en todas las fechas de entrada.
     */
    public function guardarEntrada($oEntrada)
    {
        try{            
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);
            $result = $oEntradaIntermediary->guardar($oEntrada);
            return $result;
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Si hay evolucion cargada para cualquiera de los objetivos en el dia de la entrada las borro.
     * No necesariamente se guarda una evolucion en cada nueva entrada.
     */
    public function borrarEntrada($oEntrada){
        try{
            $oEntradaIntermediary = PersistenceFactory::getEntradaIntermediary($this->db);
            return $oEntradaIntermediary->borrar($oEntrada);
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Este es un metodo generico util para determinar si una entidad sigue siendo
     * susceptible a modificaciones dependiendo el parametro de expiracion de sistema
     *
     * Se utiliza por ejemplo al crear los objetivos para setear la propiedad isEditable
     *
     * @param string $dFechaCreacion formato yyyy-mm-dd
     */
    public function isEntidadEditable($dFechaCreacion)
    {
        $iCantDias = Utils::dateDiffDays($dFechaCreacion, date('Y-m-d h:i:s', time()));
        $cantDiasExpiracion = FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
        
        if($iCantDias > $cantDiasExpiracion){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Interfaz para obtener cantidad de dias del plazo de edicion para un seguimiento
     * para ocultar bajo nivel a los page controllers.
     */
    public function getCantidadDiasExpiracionSeguimiento()
    {
        return FrontController::getInstance()->getPlugin('PluginParametros')->obtener('CANT_DIAS_EDICION_SEGUIMIENTOS');
    }

    public function obtenerEvolucionObjetivoScc($iObjetivoId, $iSeguimientoSCCId)
    {
    	try{
            $filtro = array('oe.seg_scc_x_obj_apr_obj_id' => $iObjetivoId,
                            'oe.seg_scc_x_obj_apr_seg_id' => $iSeguimientoSCCId);
            
            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }        
    }
    
    public function obtenerEvolucionObjetivoSccByDate($iObjetivoId, $iSeguimientoSCCId, $dFecha)
    {
    	try{
            $filtro = array('oe.seg_scc_x_obj_apr_obj_id' => $iObjetivoId,
                            'oe.seg_scc_x_obj_apr_seg_id' => $iSeguimientoSCCId,
                            'e.fecha' => $dFecha);
            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEvolucion = $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);

            if(null !== $aEvolucion){
                return $aEvolucion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function obtenerEvolucionObjetivoSccToDate($iObjetivoId, $iSeguimientoSCCId, $dFecha)
    {
    	try{
            $filtro = array('oe.seg_scc_x_obj_apr_obj_id' => $iObjetivoId,
                            'oe.seg_scc_x_obj_apr_seg_id' => $iSeguimientoSCCId,
                            'toDate' => $dFecha);

            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEvolucion = $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, "e.fecha", "desc", 0, 1);

            if(null !== $aEvolucion){
                return $aEvolucion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function obtenerEvolucionObjetivoPersonalizado($iObjetivoId)
    {
    	try{
            $filtro = array('oe.objetivos_personalizados_id' => $iObjetivoId);

            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            return $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);
        }catch(Exception $e){
            throw $e;
        }  
    }
    
    public function obtenerEvolucionObjetivoPersonalizadoByDate($iObjetivoId, $dFecha)
    {
    	try{
            $filtro = array('oe.objetivos_personalizados_id' => $iObjetivoId,
                            'e.fecha' => $dFecha);
            
            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEvolucion = $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, null, null, null, null);

            if(null !== $aEvolucion){
                return $aEvolucion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Devuelve el objeto evolucion correspondiente a la fecha mas cercana a la pasada por parametro
     */
    public function obtenerEvolucionObjetivoPersonalizadoToDate($iObjetivoId, $dFecha)
    {
    	try{
            $filtro = array('oe.objetivos_personalizados_id' => $iObjetivoId,
                            'toDate' => $dFecha);

            $oEvolucionIntermediary = PersistenceFactory::getEvolucionIntermediary($this->db);
            $iRecordsTotal = 0;
            $aEvolucion = $oEvolucionIntermediary->obtener($filtro, $iRecordsTotal, "e.fecha", "desc", 0, 1);

            if(null !== $aEvolucion){
                return $aEvolucion[0];
            }else{
                return null;
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Por ahora es simplemente la estimacion mas lejana de todos los objetivos activos que no esten logrados
     */
    public function obtenerDuracionEstimadaSeguimiento($oSeguimiento)
    {
        $dFecha = null;

        $aObjetivos = $oSeguimiento->getObjetivos();
        if(count($aObjetivos) > 0){
            //me fijo en todos los objetivos del seguimiento
            foreach($aObjetivos as $oObjetivo){

                //solo considero los activos, que no esten vencidos y que no esten logrados
                if($oObjetivo->isActivo() && !$oObjetivo->isEstimacionVencida() && !$oObjetivo->isLogrado()){

                    $nextDate = strtotime($oObjetivo->getEstimacion());

                    //si es la primer fecha no comparo nada
                    if($dFecha === null){
                        $dFecha = $nextDate;
                        continue;
                    }

                    //si la fecha del objetivo 2 es mayor a la del objetivo 1 entonces reemplazo
                    if($dFecha < $nextDate){
                        $dFecha = $nextDate;
                    }
                }
            }
        }

        //puedo devolver nulo o la fecha mas lejana de todos los objetivos. 
        return ($dFecha === null)?$dFecha:date("d/m/Y", $dFecha);
    }
}
