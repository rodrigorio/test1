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

    public function guardarSeguimiento($oSeguimiento){
    	try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->guardar($oSeguimiento);
        }catch(Exception $e){
                echo $e->getMessage();
        }
    }

    public function listarSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
          	$listaSeg = $oSeguimientoIntermediary->obtenerSeguimientos($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount );
          	return $listaSeg;
        }catch(Exception $e){
                echo $e->getMessage();
        }
    }

    public function getSeguimientoById($iId, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('s.id' => $iId);
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            $aSeguimiento = $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount );
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
            $filtro = array('d.id' => $iId);
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
                
                $result = $oDiscapacitadoIntermediary->guardarModeracion($oDiscapacitado);
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
    public function borrarDiscapacitado($oDiscapacitado){
        try{
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            if(!$oDiscapacitadoIntermediary->tieneSeguimientos($oDiscapacitado->getId())){
                return $oDiscapacitadoIntermediary->borrar($oDiscapacitado);
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

    public function getPracticaById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('c.id' => $iId);
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            $aPractica = $oPracticaIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
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

    public function eliminarSeguimiento($oSeguimiento){
        try{
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoIntermediary($this->db);
            return $oSeguimientoIntermediary->borrar($oSeguimiento);
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }    
    }
}
