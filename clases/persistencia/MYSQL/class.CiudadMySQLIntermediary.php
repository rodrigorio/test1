<?php
/**
 * Description of class CiudadMySQLIntermediary
 *
 * @author Rodrigo A. Rio
 */
class CiudadMySQLIntermediaryMySQLIntermediary extends CiudadIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return CiudadMySQLIntermediary
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
                        c.id as iId, c.nombre as sNombre,c.provincia_id as iProvinciaId
                    FROM
                       ciudades c ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);
            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($foundRows)){ return null; }
            
			$aCiudades = array();
            while($oObj = $db->oNextRecord()){
            	$oCiudad 			= new stdClass();
            	$oCiudad->iId 		= $oObj->iId;
            	$oCiudad->sNombre	= $oObj->sNombre;
            	$oCiudad->oProvincia= null;
            	$aProvincias[]		= Factory::getCiudadInstance($oCiudad);
            }
            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aCiudades) == 1){
                return $aCiudades[0];
            }else{
                return $aCiudades;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
private  function insertar(Ciudad $oCiudad)
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
    
private  function actualizar(Ciudad $oCiudad)
   {
		try{
			$db = $this->conn;
		if($oCiudad->getProvincia()!= null){
			$provinciaId = ($oCiudad->getProvincia()->getId());
			}else {
				$provinciaIdId = null;
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
    public function guardar(Ciudad $oCiudad)
    {
        try{
			if($oCiudad->getId() != null){
            	return actualizar($oCiudad);
            }else{
				return insertar($oCiudad);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
public function borrar(Ciudad $oCiudad) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from ciudades where id=".$db->escape($oCiudad->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
}
?>
