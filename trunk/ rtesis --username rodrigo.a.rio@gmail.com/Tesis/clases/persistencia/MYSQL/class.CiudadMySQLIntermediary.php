<?php
/**
 * Description of class CiudadMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class CiudadMySQLIntermediary extends CiudadIntermediary
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
                        c.id as iId, c.nombre as sNombre,c.provincia_id as iProvinciaId
                    FROM
                       ciudades c ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
			$aCiudades = array();
            while($oObj = $db->oNextRecord()){
            	$oCiudad 		= new stdClass();
            	$oCiudad->iId 		= $oObj->iId;
            	$oCiudad->sNombre	= $oObj->sNombre;
            	$filtroProv = array("p.id"=>$oObj->iProvinciaId);
            	$oCiudad->oProvincia    = ComunidadController::getInstance()->getProvinciaById($filtroProv);
            	$aCiudades[]		= Factory::getCiudadInstance($oCiudad);
            }
            return $aCiudades;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oCiudad)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into ciudades ".
                    " set nombre =".$db->escape($oCiudad->getNombre(),true).", " .
                    " ciudad_id =".$db->escape($oCiudad->getProvincia()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oCiudad)
   {
		try{
			$db = $this->conn;
		if($oCiudad->getProvincia()!= null){
			$provinciaId = ($oCiudad->getProvincia()->getId());
			}else {
				$provinciaId = null;
			}
        
			$sSQL =	" update ciudades ".
                    " set nombre =".$db->escape($oCiudad->getNombre(),true).", " .
                    " provincia_id =".escape($provinciaId,false,MYSQL_TYPE_INT).
                    " where id =".$db->escape($oCiudad->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oCiudad)
    {
        try{
			if($oCiudad->getId() != null){
            	return $this->actualizar($oCiudad);
            }else{
				return $this->insertar($oCiudad);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oCiudad) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from ciudades where id=".$db->escape($oCiudad->getId(),false,MYSQL_TYPE_INT));
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
                        ciudades c 
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
?>