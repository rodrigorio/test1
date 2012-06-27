<?php

/**
 * Description of class ProvinciaMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class ProvinciaMySQLIntermediary extends ProvinciaIntermediary
{
    private static $instance = null;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return ProvinciaMySQLIntermediary
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
                        p.id as iId, p.nombre as sNombre,p.paises_id as iPaisId
                    FROM
                       provincias p ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aProvincias = array();
            while($oObj = $db->oNextRecord()){
            	$oProvincia 		= new stdClass();
            	$oProvincia->iId 	= $oObj->iId;
            	$oProvincia->sNombre= $oObj->sNombre;
            	$filtroPais = array("p.id"=>$oObj->iPaisId);
            	$oProvincia->oPais= ComunidadController::getInstance()->getPaisById($filtroPais);
            	$aProvincias[] = Factory::getProvinciaInstance($oProvincia);
            }

            return $aProvincias;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	public  function insertar($oProvincia)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into provincias ".
                    " set nombre =".$db->escape($oProvincia->getNombre(),true).", " .
                    " paises_id =".$db->escape($oProvincia->getPais()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public  function actualizar($oProvincia)
    {
		try{
			$db = $this->conn;
		if($oProvincia->getPais()!= null){
			$paisId = ($oProvincia->getPais()->getId());
			}else {
				$paisId = null;
			}
        
			$sSQL =	" update provincias ".
                    " set nombre =".$db->escape($oProvincia->getNombre(),true).", " .
                    " paises_id =".escape($paisId,false,MYSQL_TYPE_INT)." ".
                    " where id =".$db->escape($oProvincias->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oProvincia)
    {
        try{
			if($oProvincia->getId() != null){
            	return $this->actualizar($oProvincia);
            }else{
				return $this->insertar($oProvincia);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oProvincia) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from provincias where id=".$db->escape($oProvincia->getId(),false,MYSQL_TYPE_INT));
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
                        provincias p 
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