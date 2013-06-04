<?php
 /* Description of class EntrevistaMySQLIntermediary
 *
 * @author Andres
 */
class EntrevistaMySQLIntermediary extends EntrevistaIntermediary
{
    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }
    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
    public final function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        e.id as iId, e.descripcion as sDescripcio
                    FROM
                       entrevistas e ";
             if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEntrevistas = array();
            while($oObj = $db->oNextRecord()){
            	$oEntrevista 		= new stdClass();
            	$oEntrevista->iId 	= $oObj->iId;
            	$oEntrevista->sDescripcion= $oObj->sDescripcion;
            	$aEntrevistas[] = Factory::getEntrevistaInstance($oEntrevista);
            }

            return $aEntrevistas;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
    public  function insertar($oEntrevista)
    {
		try{
			$db = $this->conn;
			$sSQL =	" insert into entrevistas ".
                    " set descripcion =".$db->escape($oEntrevista->getDescripcion(),true).", ";
                   
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public  function actualizar($oEntrevista)
    {
		try{
			$db = $this->conn;
		        
			$sSQL =	" update entrevistas ".
                    " set descripcion =".$db->escape($oEntrevista->getDescripcion(),true)." " .
                    " where id =".$db->escape($oEntrevista->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oEntrevista)
    {
        try{
			if($oEntrevista->getId() != null){
            	return $this->actualizar($oEntrevista);
            }else{
				return $this->insertar($oEntrevista);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
   public function borrar($oEntrevista) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from entrevistas where id=".$db->escape($oEntrevista->getId(),false,MYSQL_TYPE_INT));
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
                        entrevistas e 
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