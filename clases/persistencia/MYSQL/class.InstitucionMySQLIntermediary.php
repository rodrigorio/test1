<?php
/**
 * Description of class InstitucionMySQLIntermediary
 *
 *
 */
class InstitucionMySQLIntermediary extends InstitucionIntermediary
{
	private static $instance = null;

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
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	public  function existe($filtro){
		try{
			$db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);
   
            $sSQL = "SELECT
                        i.id as iId, i.nombre as sNombre
                        FROM
                       instituciones i ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
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

    
    public  function insertar($oInstitucion)
    {
		try{
			$db = $this->conn;
			if($oInstitucion->getCiudad()!= null){
				$ciudadId = ($oInstitucion->getCiudad()->getId());
			}else {
				$ciudadId = null;
			}
			$sSQL =	" insert into instituciones ".
                    " set nombre =".$db->escape($oInstitucion->getNombre(),true).", ".
                    " ciudades_id =".$db->escape($ciudadId,false,MYSQL_TYPE_INT).", ".
					" moderado =".$db->escape($oInstitucion->getModerado(),false,MYSQL_TYPE_INT).", ".
					" descripcion =".$db->escape($oInstitucion->getDescripcion(),true).", ".
					" tipoInstitucion_id =".$db->escape($oInstitucion->getTipoInstitucion(),false,MYSQL_TYPE_INT).", ".
					" direccion =".$db->escape($oInstitucion->getDireccion(),true).", ".
					" email =".$db->escape($oInstitucion->getEmail(),true).", ".
					" telefono =".$db->escape($oInstitucion->getTelefono(),true).", ".
					" sitioWeb =".$db->escape($oInstitucion->getSitioWeb(),true).", ".
					" horariosAtencion =".$db->escape($oInstitucion->getHorariosAtencion(),true).", ".
					" autoridades =".$db->escape($oInstitucion->getAutoridades(),true).", ".
					" cargo =".$db->escape($oInstitucion->getCargo(),true).", ".
					" personeriaJuridica =".$db->escape($oInstitucion->getPersoneriaJuridica(),true).", ".
					" sedes =".$db->escape($oInstitucion->getSedes(),true).", ".
					" actividadesMes =".$db->escape($oInstitucion->getActividadesMes(),true)." ";
						 
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;
		}catch(Exception $e){
			return false;
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oInstitucion)
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
                    " ciudades_id =".escape($ciudadId,false,MYSQL_TYPE_INT)." ".
					" moderado_id =".$db->escape($oInstitucion->getModerado(),false,MYSQL_TYPE_INT).", ".
					" descripcion =".$db->escape($oInstitucion->getDescripcion(),true).", ".
					" tipoInstitucion_id =".$db->escape($oInstitucion->getTipoInstitucion(),false,MYSQL_TYPE_INT).", ".
					" direccion =".$db->escape($oInstitucion->getDireccion(),true).", ".
					" email =".$db->escape($oInstitucion->getEmail(),true).", ".
					" telefono =".$db->escape($oInstitucion->getTelefono(),true).", ".
					" sitioWeb =".$db->escape($oInstitucion->getSitioWeb(),true).", ".
					" horariosAtencion =".$db->escape($oInstitucion->getHorariosAtencion(),true).", ".
					" autoridades =".$db->escape($oInstitucion->getAutoridades(),true).", ".
					" cargo =".$db->escape($oInstitucion->getCargo(),true).", ".
					" personeriaJuridica =".$db->escape($oInstitucion->getPersoneriaJuridica(),true).", ".
					" sedes =".$db->escape($oInstitucion->getSedes(),true).", ".
					" actividadesMes =".$db->escape($oInstitucion->getActividadesMes(),true)." ".
                    " where id =".$db->escape($oInstitucion->getId(),false,MYSQL_TYPE_INT)." ";
						 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oInstitucion)
    {
        try{
			if($oInstitucion->getId() != null){
            	return $this->actualizar($oInstitucion);
            }else{
				return $this->insertar($oInstitucion);
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
                        i.id as iId, i.nombre as sNombre
                        FROM
                       instituciones i ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ return null; }

			$aInstituciones = array();
            while($oObj = $db->oNextRecord()){
            	$oInstitucion 		= new stdClass();
            	$oInstitucion->iId 	= $oObj->iId;
            	$oInstitucion->sNombre= $oObj->sNombre;
            	$oUsuario->oCiudades 	= null;
            	$oInstitucion->iModerado 	= $oObj->iModerado;
            	$oInstitucion->sDescripcion	= $oObj->sDescripcion;
            	$oInstitucion->iTipoInstitucion 	= $oObj->iTipoInstitucion;
            	$oInstitucion->sDireccion 	= $oObj->sDireccion;
            	$oInstitucion->sEmail 	= $oObj->sEmail;
            	$oInstitucion->sTelefono 	= $oObj->sTelefono;
            	$oInstitucion->sSitioWeb 	= $oObj->sSitioWeb;
            	$oInstitucion->sHorariosAtencion 	= $oObj->sHorariosAtencion;
            	$oInstitucion->sAutoridades 	= $oObj->sAutoridades;
            	$oInstitucion->sCargo 	= $oObj->sCargo;
            	$oInstitucion->sPersoneriaJuridica 	= $oObj->sPersoneriaJuridica;
            	$oInstitucion->sSedes 	= $oObj->sSedes;
            	$oInstitucion->sActividadesMes 	= $oObj->sActividadesMes;
  
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
    
	//borra muchas instituciones
	//public function borrar($objects){}
    
    //borra una Institucion
    public function borrar($oInstitucion) {
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