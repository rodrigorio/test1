<?php

/**
 * Description of class ProvinciaMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class ProvinciaMySQLIntermediaryMySQLIntermediary extends ProvinciaIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return ProvinciaMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
	
	public final function obtener($filtro, &$foundRows = 0){
	 	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        p.id as iId, p.nombre as sNombre,p.paises_id as iPaisId
                    FROM
                       provincias p ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "p");
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aProvincias = array();
            while($oObj = $db->oNextRecord()){
            	$oProvincia 		= new stdClass();
            	$oProvincia->iId 	= $oObj->iId;
            	$oProvincia->sNombre= $oObj->sNombre;
            	//$oProvincia->oPais= $oObj->iPaisId;
            	$aProvincias[]		= Factory::getProvinciaInstance($oProvincia);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aProvincias) == 1){
                return $aProvincias[0];
            }else{
                return $aProvincias;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
}
?>
