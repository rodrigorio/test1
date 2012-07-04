<?php
/**
 * Description of class CicloMySQLIntermediary
 *
 * @author Andres
 */
class CicloMySQLIntermediary extends CicloIntermediary
{
    private static $instance = null;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return MySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        c.id as iId, c.descripcion as sDescripcion, c.niveles_id as iNivelesId
                    FROM
                       ciclos c 
                    JOIN niveles n ON c.niveles_id = n.id ";
                    if(!empty($filtro)){     
                    	$sSQL .=" WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aCiclos = array();
            while($oObj = $db->oNextRecord()){
            	$oCiclo 		= new stdClass();
            	$oCiclo->iId 	= $oObj->iId;
            	$oCiclo->sDescripcion = $oObj->sDescripcion;
            	$oCiclo->oNivel= SeguimientosController::getInstance()->getNivelById($oObj->iNivelesId);
            	$aCiclos[] = Factory::getCicloInstance($oCiclo);
            }

            return $aCiclos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oCiclo)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into ciclos ".
                    " set descripcion =".$db->escape($oCiclo->getDescripcion(),true).", " .
                    " niveles_id =".$db->escape($oCiclo->getNivel()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public  function actualizar($oCiclo)
    {
		try{
			$db = $this->conn;
		if($oCiclo->getNivel()!= null){
			$nivelId = ($oCiclo->getNivel()->getId());
			}else {
				$nivelId = null;
			}
        
			$sSQL =	" update ciclos ".
                    " set descripcion =".$db->escape($oCiclo->getDescripcion(),true).", " .
                    " niveles_id =".escape($nivelId,false,MYSQL_TYPE_INT)." ".
                    " where id =".$db->escape($oCiclo->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oCiclo)
    {
        try{
			if($oCiclo->getId() != null){
            	return $this->actualizar($oCiclo);
            }else{
				return $this->insertar($oCiclo);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oCiclo) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from ciclos where id=".$db->escape($oCiclo->getId(),false,MYSQL_TYPE_INT));
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
                        ciclos c 
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