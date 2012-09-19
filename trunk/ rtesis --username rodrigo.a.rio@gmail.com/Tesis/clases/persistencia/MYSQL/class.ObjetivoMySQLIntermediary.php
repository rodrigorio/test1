<?php
/* Description of class ObjetivoMySQLIntermediary
 *
 * @author Andrés
 */
class ObjetivoMySQLIntermediary extends ObjetivoIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return ObjetivoMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
     public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        objetivos o 
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
    
      public final function obtenerObjetivoPersonalizado($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion, op.objetivo_ejes_id as iObjetivoEjeId, sp.seguimientos_personalizados_id as iSeguimientoId
                    FROM
                       objetivos o 
                        JOIN 
                    	objetivos_personalizados op ON o.id = op.id 
                    	JOIN 
                    	seguimiento_personalizado_x_objetivo_personalizado sp ON op.id = sp.objetivos_personalizados_id";
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){
            	$oObjetivo		= new stdClass();
            	$oObjetivo->iId 		= $oObj->iId;
            	$oObjetivo->sDescripcion	= $oObj->sDescripcion;
            	$oObjetivo->oObjetivoEje 		= ComunidadController::getInstance()->getObjetivoEjeById($oObj->iObjetivoEjeid);            
            	
            	$aObjetivos[]		= Factory::getObjetivoInstance($oObjetivo);

            }
            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
     public final function obtenerObjetivoCurricular($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion, oc.areas_id as iAreaId
                    FROM
                       objetivos o 
                        JOIN 
                    	objetivos_curriculares oc ON o.id = oc.id ";
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){
            	$oObjetivo		= new stdClass();
            	$oObjetivo->iId 		= $oObj->iId;
            	$oObjetivo->sDescripcion	= $oObj->sDescripcion;
            	$oObjetivo->oArea		= ComunidadController::getInstance()->getAreaById($oObj->iAreaId); 
            	$aObjetivos[]		= Factory::getObjetivoInstance($oObjetivo);
            }
            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function guardarObjetivoCurricular($oOjetivo)
    {
        try{
			if($oObjetivo->getId() != null){
            	return $this->actualizarObjetivoCurricular($oObjetivo);
            }else{
				return $this->insertarObjetivoCurricular($oOjetivo);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    public function guardarObjetivoPersonalizado($oOjetivo)
    {
        try{
			if($oObjetivo->getId() != null){
            	return $this->actualizarObjetivoPersonalizado($oObjetivo);
            }else{
				return $this->insertarObjetivoPersonalizado($oOjetivo);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
   public  function insertarObjetivoCurricular($oObjetivo)
   {
		try{
			$db = $this->conn;
			$db->begin_transaction();
			$sSQL =	" insert into objetivos ".
                    " set descripcion =".$db->escape($oObjetivo->getDescripcion(),true)." " ;
			        			 
			 $db->execSQL($sSQL);
			 $iLastId = $db->insert_id();
			 
			 $sSQL =	" insert into objetivos_curriculares ".
                    " set  id=".$db->escape($iLastId,false).", " .
                    " areas_id =".$db->escape($oObjetivo->getAreaId(),false,MYSQL_TYPE_INT)." " ;
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

             
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
   public  function insertarObjetivoPersonalizado($oObjetivo)
   {
		try{
			$db = $this->conn;
			$db->begin_transaction();
			$sSQL =	" insert into objetivos ".
                    " set descripcion =".$db->escape($oObjetivo->getDescripcion(),true)." " ;
			        			 
			 $db->execSQL($sSQL);
			 $iLastId = $db->insert_id();
			 
			 $sSQL =	" insert into objetivos_personalizados ".
                    " set  id=".$db->escape($iLastId,false).", " .
                    " objetivo_ejes_id =".$db->escape($oObjetivo->getObjetivoEjeId(),false,MYSQL_TYPE_INT)." " ;
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

             
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
    public function actualizarObjetivoCurricular($oObjetivo)
      {
		try{
			$db = $this->conn;
		
			$sSQL =	" update objetivos ".
		            " set descripcion =".$db->escape($oObjetivo->getDescripcion(),true)." ".
		            " where id =".$db->escape($oObjetivo->getId(),false,MYSQL_TYPE_INT)." ";
			        			 
			 $db->execSQL($sSQL);
			 $iLastId = $db->insert_id();
			 
			 $sSQL = " update objetivos_curriculares ".
                     " set areas_id =".$db->escape($oObjetivo->getAreaId(),false,MYSQL_TYPE_INT)." ".
			         " where id =".$db->escape($oObjetivo->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

             
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
     public function actualizarObjetivoPersonalizado($oObjetivo)
      {
		try{
			$db = $this->conn;
		
			$sSQL =	" update objetivos ".
		            " set descripcion =".$db->escape($oObjetivo->getDescripcion(),true)." ".
		            " where id =".$db->escape($oObjetivo->getId(),false,MYSQL_TYPE_INT)." ";
			        			 
			 $db->execSQL($sSQL);
			 $iLastId = $db->insert_id();
			 
			 $sSQL = " update objetivos_personalizados".
                     " set areas_id =".$db->escape($oObjetivo->getAreaId(),false,MYSQL_TYPE_INT)." ".
			         " where id =".$db->escape($oObjetivo->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;

             
		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}
	
	public function borrar($iObjetivoId)
   {
        try{
            $db = $this->conn;
            $db->execSQL("delete from objetivos where id = '".$iObjetivoId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            return false;
            throw new Exception($e->getMessage(), 0);
        }
    }
	 public function actualizarCampoArray($objects, $cambios){} 
}
	