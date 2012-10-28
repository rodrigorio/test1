<?php

class EjeTematicoMySQLIntermediary extends EjeTematicoIntermediary
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
                        e.id as iId, e.descripcion as sDescripcion, e.contenidos as sContenidos, e.areas_id as iAreaId
                    FROM
                        ejes e 
                    JOIN 
                    	areas a ON e.areas_id = e.id ";
            
                    if(!empty($filtro)){     
                    	$sSQL .=" WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
			$aEjesTematicos = array();
            while($oObj = $db->oNextRecord()){
            	$oEjeTematico 		= new stdClass();
            	$oEjeTematico->iId 		= $oObj->iId;
            	$oEjeTematico->sDescripcion	= $oObj->sDescripcion;
            	$oEjeTematico->sContenidos	= $oObj->sContenidos;
            	$oEjeTematico->oArea    = SeguimientosController::getInstance()->getAreaById($oObj->iAreaId);
            	$aEjesTematicos[]		= Factory::getEjeTematicoInstance($oEjeTematico);
            }
            return $aEjesTematicos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oEjeTematico)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into ejes ".
                    " set descripcion =".$db->escape($oEjeTematico->getDescripcion(),true).", " .
		        	" set contenidos =".$db->escape($oEjeTematico->getContenidos($n12br),true).", " .
                    " areas_id =".$db->escape($oEjeTematico->getArea()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oEjeTematico)
   {
		try{
			$db = $this->conn;
		if($oEjeTematico->getArea()!= null){
			$areaId = ($oEjeTematico->getArea()->getId());
			}else {
				$areaId = null;
			}
        
			$sSQL =	" update ejes ".
                    " set descripcion =".$db->escape($oEjeTematico->getDescripcion(),true).", " .
			        " set contenidos =".$db->escape($oEjeTematico->getContenidos($n12br),true).", " .
                    " areas_id =".escape($areaId,false,MYSQL_TYPE_INT).
                    " where id =".$db->escape($oEjeTematico->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oEjeTematico)
    {
        try{
			if($oEjeTematico->getId() != null){
            	return $this->actualizar($oEjeTematico);
            }else{
				return $this->insertar($oEjeTematico);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oEjeTematico) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from ejes where id=".$db->escape($oEjeTematico->getId(),false,MYSQL_TYPE_INT));
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
                        ejes e
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