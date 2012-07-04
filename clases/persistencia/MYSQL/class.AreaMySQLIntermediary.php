<?php
/**
 * Description of class AreaMySQLIntermediary
 *
 * @author Andres
 */
class AreaMySQLIntermediary extends AreaIntermediary
{
	private static $instance = null;

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
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        a.id as iId, a.descripcion as sDescripcion, a.ciclos_id as iCicloId
                    FROM
                        areas a 
                    JOIN 
                    	ciclos c ON a.ciclos_id = c.id ";
            
                    if(!empty($filtro)){     
                    	$sSQL .=" WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
			$aAreas = array();
            while($oObj = $db->oNextRecord()){
            	$oArea 		= new stdClass();
            	$oArea->iId 		= $oObj->iId;
            	$oArea->sDescripcion	= $oObj->sDescripcion;
            	$oArea->oCiclo    = SeguimientosController::getInstance()->getCicloById($oObj->iCicloId);
            	$aAreas[]		= Factory::getAreaInstance($oArea);
            }
            return $aAreas;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oArea)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into areas ".
                    " set descripcion =".$db->escape($oArea->getDescripcion(),true).", " .
                    " ciclos_id =".$db->escape($oArea->getCiclo()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oArea)
   {
		try{
			$db = $this->conn;
		if($oArea->getCiclo()!= null){
			$cicloId = ($oArea->getCiclo()->getId());
			}else {
				$cicloId = null;
			}
        
			$sSQL =	" update areas ".
                    " set descripcion =".$db->escape($oArea->getDescripcion(),true).", " .
                    " ciclos_id =".escape($cicloId,false,MYSQL_TYPE_INT).
                    " where id =".$db->escape($oArea->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oArea)
    {
        try{
			if($oArea->getId() != null){
            	return $this->actualizar($oArea);
            }else{
				return $this->insertar($oArea);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oArea) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from areas where id=".$db->escape($oArea->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
	public function actualizarCampoArray($objects, $cambios){
		
	}
 	
	public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        areas a 
					WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ 
            	return false; 
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
           	return false; 
        }
    }
}