<?php
/** Description of class EspecialidadMySQLIntermediary
 *
 */
 
 
class EspecialidadMySQLIntermediary extends EspecialidadIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return EspecialidadMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
    public function existe($filtro){}

          
    private  function insertar(Especialidad $oEspecialidad)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into especialidades ".
                    " set nombre =".$db->escape($oEspecialidad->getNombre(),true).", ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
 private  function actualizar(Especialidad $oEspecialidad)
   {
		try{
			$db = $this->conn;
			$sSQL =	" update especialidades ".
                    " set nombre =".$db->escape($oEspecialidad->getNombre(),true).", " .
                    " where id =".$db->escape($oEspecialidad->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar(Especialidad $oEspecialidad)
    {
        try{
			if($oEspecialidad->getId() != null){
            	return actualizar($oEspecilaidad);
            }else{
				return insertar($oEspecialidad);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

public final function obtener($filtro, &$foundRows = 0){
	 	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        e.id as iId, e.nombre as sNombre
                        FROM
                       especialidades e ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aEspecialidades = array();
            while($oObj = $db->oNextRecord()){
            	$oEspecialidad 		= new stdClass();
            	$oEspecialidad->iId 	= $oObj->iId;
            	$oEspecialidad->sNombre= $oObj->sNombre;
            	$aEspecialidades[]		= Factory::getEspecilidadInstance($oEspecialidad);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aEspecialidads) == 1){
                return $aEspecialidades[0];
            }else{
                return $aEspecialidades;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
    
	//borra muchas especialidades
	//public function borrar($objects){}
    
    //borra una especialidad
    public function borrar(Especialidad $oEspecialidad) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from especialidades where id=".$db->escape($oEspecialidad->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}
?>	