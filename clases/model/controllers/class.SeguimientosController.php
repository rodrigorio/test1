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
/**
     *
     */
    public function getDiscapacitadoById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('d.id' => $iId);
            $oDiscapacitadoIntermediary = PersistenceFactory::getDiscapacitadoIntermediary($this->db);
            $r =  $oDiscapacitadoIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
                return $r;
        }catch(Exception $e){
            throw new Exception($e);
            return false;
        }
    }
	/**
     *
     */
    public function getPracticaById($iId, &$iRecordsTotal = 0, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $filtro = array('c.id' => $iId);
            $oPracticaIntermediary = PersistenceFactory::getPracticaIntermediary($this->db);
            $r =  $oPracticaIntermediary ->obtener($filtro,$iRecordsTotal, $sOrderBy , $sOrder , $iIniLimit , $iRecordCount );
        	if(count($r) == 1){
                return $r[0];
            }else{
                return $r;
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
            echo $e->getMessage();
        }
    
    }
    
    
}