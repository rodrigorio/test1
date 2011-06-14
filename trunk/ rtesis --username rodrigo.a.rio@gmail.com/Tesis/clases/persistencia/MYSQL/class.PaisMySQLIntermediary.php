<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class PaisMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class PaisMySQLIntermediaryMySQLIntermediary extends PaisIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return PaisMySQLIntermediary
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
                        p.id as iId, p.nombre as sNombre, p.codigo as sCodigo
                    FROM
                       paises p ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "p");
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aPaises = array();
            while($oObj = $db->oNextRecord()){
            	$oPais 			= new stdClass();
            	$oPais->iId 	= $oObj->iId;
            	$oPais->sNombre	= $oObj->sNombre;
            	$oPais->sCodigo	= $oObj->sCodigo;
            	$aPaises[]		= Factory::getPaisInstance($oPais);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aPaises) == 1){
                return $aPaises[0];
            }else{
                return $aPaises;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
}
?>