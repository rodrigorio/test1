<?php

/**
 * Description of class ProvinciaMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class ProvinciaMySQLIntermediaryMySQLIntermediary extends ProvinciaIntermediary
{
     static $singletonInstance = 0;


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
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
	
	public final function obtener($filtro, &$foundRows = 0){
	 	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        p.id as iId, p.nombre as sNombre,p.paises_id as iPaisId
                    FROM
                       provincias p ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "p");
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aProvincias = array();
            while($oObj = $db->oNextRecord()){
            	$oProvincia 		= new stdClass();
            	$oProvincia->iId 	= $oObj->iId;
            	$oProvincia->sNombre= $oObj->sNombre;
            	//$oProvincia->oPais= $oObj->iPaisId;
            	$aProvincias[]		= Factory::getProvinciaInstance($oProvincia);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aProvincias) == 1){
                return $aProvincias[0];
            }else{
                return $aProvincias;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
	private  function insertar(Provincia $oProvincia)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into provincias ".
                    " set nombre =".$db->escape($oInstitucion->getNombre(),true).", " .
                    " paises_id =".$db->escape($oProvincia->getPais()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
private  function actualizar(Provincia $oProvincia)
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
                    " paises_id =".escape($paisId,false,MYSQL_TYPE_INT).
                    " where id =".$db->escape($oProvincias->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar(Provincia $oProvincia)
    {
        try{
			if($oProvincia->getId() != null){
            	return actualizar($oProvincia);
            }else{
				return insertar($oProvincia);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
public function borrar(Provincia $oProvincia) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from provincias where id=".$db->escape($oProvincia->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
}
?>
