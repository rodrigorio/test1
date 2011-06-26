<?php
/**
 * Description of class InstitucionMySQLIntermediary
 *
 *
 */
class InstitucionMySQLIntermediaryMySQLIntermediary extends InstitucionIntermediary
{
     static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return InstitucionMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
public  function existe($filtro){
		try{
			$db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        i.id as iId, i.nombre as sNombre, i.ciudades_id as iCiudadId
                        FROM
                       instituciones i ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "i");
                    }
            			
			$db->query($sSQL);
			if($db->num_rows() > 0){
				return true;
			}else{
				return false;
			}
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}	
	}

    
    public function actualizarCampoArray($objects, $cambios){}

    
    private  function insertar(Institucion $oInstitucion)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into instituciones ".
                    " set nombre =".$db->escape($oInstitucion->getNombre(),true).", " .
                    " ciudades_id =".$db->escape($oInstitucion->getCiudad()->getId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
private  function actualizar(Intitucion $oInstitucion)
   {
		try{
			$db = $this->conn;
		if($oInstitucion->getCiudad()!= null){
			$ciudadId = ($oInstitucion->getCiudad()->getId());
			}else {
				$ciudadId = null;
			}
        
			$sSQL =	" update instituciones ".
                    " set nombre =".$db->escape($oInstitucion->getNombre(),true).", " .
                    " ciudades_id =".escape($ciudadId,false,MYSQL_TYPE_INT).
                    " where id =".$db->escape($oInstitucion->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar(Institucion $oInstitucion)
    {
        try{
			if($oInstitucion->getId() != null){
            	return actualizar($oInstitucion);
            }else{
				return insertar($oInstitucion);
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
                        i.id as iId, i.nombre as sNombre, i.ciudades_id as iCiudadId
                        FROM
                       instituciones i ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro, "i");
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aInstituciones = array();
            while($oObj = $db->oNextRecord()){
            	$oInstitucion 		= new stdClass();
            	$oInstitucion->iId 	= $oObj->iId;
            	$oInstitucion->sNombre= $oObj->sNombre;
  //falta un campo de tipo objeto-->
            	$aInstituciones[]		= Factory::getInstitucionInstance($oinstitucion);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aInstituciones) == 1){
                return $aInstituciones[0];
            }else{
                return $aInstituciones;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}
    
	//borra muchas especialidades
	//public function borrar($objects){}
    
    //borra una Institucion
    public function borrar(Institucion $oInstitucion) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from instituciones where id=".$db->escape($oInstitucion->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
   
    
}
?>	