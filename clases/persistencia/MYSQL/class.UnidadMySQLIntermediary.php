<?php
 /* Description of class UnidadMySQLIntermediary
 *
 * @author Andrés
 */
class UnidadMySQLIntermediary extends UnidadIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return VariableMySQLIntermediary
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

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        u.id as iId, u.nombre as sNombre, u.descripcion as sDescripcion
                    FROM
                       unidades u ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aUnidades = array();
            while($oObj = $db->oNextRecord()){
            	$oUnidad 			= new stdClass();
            	$oUnidad->iId 	= $oObj->iId;
            	$oUnidad->sNombre	= $oObj->sNombre;
            	$oUnidad->sDescripcion	= $oObj->sDescripcion;
            	$aUnidades[]		= Factory::getUnidadInstance($oUnidad);
            }
            
            return $aUnidades;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oUnidad)
   		{
		try{
			$db = $this->conn;
			$sSQL =	" insert into unidades ".
                    " set nombre =".$db->escape($oUnidad->getNombre(),true)." , ".
			 	    " descripcion =".$db->escape($oUnidad->getDescripcion(),true)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	public  function actualizar($oUnidad)
   	{
		try{
			$db = $this->conn;
			$sSQL =	" update unidades ".
                    " set nombre =".$db->escape($oUnidad->getNombre(),true)." , " .
			        " descripcion =".$db->escape($oUnidad->getDescripcion(),true)." ".
                    " where id =".$db->escape($oUnidad->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oUnidad)
    {
        try{
			if($oUnidad->getId() != null){
            	return $this->actualizar($oUnidad);
            }else{
				return $this->insertar($oUnidad);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
 	public function borrar($oUnidad) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from unidades where id=".$db->escape($oUnidad->getId(),false,MYSQL_TYPE_INT));
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
                        unidades u 
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