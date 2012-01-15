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
            if($oSeguimiento->getTipoSeguimiento() == "PERSONALIZADO"){
                 $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoPersonalizadoIntermediary($this->db);
            }else{
                 $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoSCCIntermediary($this->db);
            }
            return $oSeguimientoIntermediary->guardar($oSeguimiento);
        }catch(Exception $e){
                echo $e->getMessage();
        }
    }
    public function listarSeguimiento($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
        	$oSeguimientoIntermediary = PersistenceFactory::getSeguimientoPersonalizadoIntermediary($this->db);
     	    return $listaSegPers = $oSeguimientoIntermediary->obtener($filtro, $iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount );
          	
            $oSeguimientoIntermediary = PersistenceFactory::getSeguimientoSCCIntermediary($this->db);
          	$listaSegSCC = $oSeguimientoIntermediary->obtener($filtro, &$iRecordsTotal, $sOrderBy , $sOrder, $iIniLimit, $iRecordCount );
          	$res = array();
          	array_push($res,$listaSegPers);
          	array_push($res,$listaSegSCC);
          	return $res;
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
    
    
}