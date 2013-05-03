<?php
/**
 * Description of class OpcionMySQLIntermediary
 *
 * @author Andres
 */
class OpcionMySQLIntermediary extends OpcionIntermediary
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
     public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        po.id as iId, po.descripcion as sDescripcion
                    FROM
                       preguntas_opciones po";
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aOpciones = array();
            while($oObj = $db->oNextRecord()){
            	$oOpcion 		= new stdClass();
            	$oOpcion->iId 		= $oObj->iId;
            	$oOpcion->sDescripcion	= $oObj->sDescripcion;
            	$aOpciones[]		= Factory::getOpcionInstance($oOpcion);
            }
            return $aCiudades;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
	public  function insertar($oOpcion, $iPreguntaId)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into preguntas_opciones ".
                    " set descripcion =".$db->escape($oOpcion->getDescripcion(),true)." , " .
                    " preguntas_id =".escape($iPreguntaId,false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oOpcion, $iPreguntaId)
   {
		try{
			$db = $this->conn;
	        
			$sSQL =	" update preguntas_opciones ".
                    " set descripcion =".$db->escape($oOpcion->getDescripcion(),true).", " .
                     " preguntas_id =".escape($iPreguntaId,false,MYSQL_TYPE_INT)." ".
                    " where id =".$db->escape($oOpcion->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oOpcion)
    {
        try{
			if($oOpcion->getId() != null){
            	return $this->actualizar($oOpcion);
            }else{
				return $this->insertar($oOpcion);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oOpcion) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from preguntas_opciones where id=".$db->escape($oOpcion->getId(),false,MYSQL_TYPE_INT));
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
                        preguntas_opciones 
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
}