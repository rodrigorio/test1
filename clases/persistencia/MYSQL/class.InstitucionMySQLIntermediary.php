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
                                        " latitud =".$db->escape($oInstitucion->getLatitud(),true).", ".
					" longitud =".$db->escape($oInstitucion->getLongitud(),true).", ".
					" actividadesMes =".$db->escape($oInstitucion->getActividadesMes(),true).", ".
					" usuario_id =".$db->escape($oInstitucion->getUsuario()->getId(),true)." ";

			
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
					" latitud =".$db->escape($oInstitucion->getLatitud(),true).", ".
					" longitud =".$db->escape($oInstitucion->getLongitud(),true).", ".
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

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          i.id as iId, 
                          i.nombre as sNombre,
                          i.`ciudades_id` as iCiudad,
                          i.`moderado` as iModerado,
                          i.`descripcion` as sDescripcion,
                          i.`tipoInstitucion_id` as iTipoInstitucion,
                          it.`nombre` as sNombreTipoInstitucion,
                          i.`direccion` as sDireccion,
                          i.`email` as sEmail,
                          i.`telefono` as sTelefono,
                          i.`sitioWeb` as sSitioWeb,
                          i.`horariosAtencion` as sHorariosAtencion,
                          i.`autoridades` as sAutoridades,
                          i.`cargo` as sCargo,
                          i.`personeriaJuridica` as sPersoneriaJuridica,
                          i.`sedes` as sSedes,
                          i.`actividadesMes` as sActividadesMes,
                          i.`usuario_id` as iUsuarioId,
                          i.`latitud` as sLatitud,
                          i.`longitud` as sLongitud,
                          prov.`id` as provinciaId,
                          pais.id as paisId
                    FROM
                        instituciones i
                    JOIN
                        usuarios u ON u.id = i.usuario_id
                    JOIN
                        instituciones_tipos it ON it.id = i.tipoInstitucion_id
                    LEFT JOIN `ciudades` c on c.`id` = i.`ciudades_id`
                    LEFT JOIN `provincias` prov on prov.`id` = c.`provincia_id`
                    LEFT JOIN `paises` pais on pais.`id` = prov.`paises_id` ";

            if(!empty($filtro)){
                $sSQL .="WHERE".$this->crearCondicionSimple($filtro);
            }
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aInstituciones = array();
            while($oObj = $db->oNextRecord()){
            	$oInstitucion 			= new stdClass();
            	$oInstitucion->iId 		= $oObj->iId;
            	$oInstitucion->sNombre  = $oObj->sNombre;
            	$oUsuario->oCiudades 	= null;
            	$oInstitucion->iModerado= $oObj->iModerado;
            	$oInstitucion->sDescripcion	= $oObj->sDescripcion;
            	$oInstitucion->iTipoInstitucion 	= $oObj->iTipoInstitucion;
            	$oInstitucion->sDireccion 	= $oObj->sDireccion;
            	$oInstitucion->sEmail 	= $oObj->sEmail;
            	$oInstitucion->sTelefono= $oObj->sTelefono;
            	$oInstitucion->sSitioWeb	= $oObj->sSitioWeb;
            	$oInstitucion->sHorariosAtencion 	= $oObj->sHorariosAtencion;
            	$oInstitucion->sAutoridades	= $oObj->sAutoridades;
            	$oInstitucion->sCargo 	= $oObj->sCargo;
            	$oInstitucion->sPersoneriaJuridica 	= $oObj->sPersoneriaJuridica;
            	$oInstitucion->sSedes 	= $oObj->sSedes;
            	$oInstitucion->sActividadesMes 	= $oObj->sActividadesMes;
  
            	$aInstituciones[] = Factory::getInstitucionInstance($oInstitucion);
            }

            return $aInstituciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

	public final function obtenerInstituciones($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
	 	try{
            $db = $this->conn;
            //$filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          i.id as iId, 
                          i.nombre as sNombre,
						  i.`ciudades_id` as iCiudad,
						  i.`moderado` as iModerado,
						  i.`descripcion` as sDescripcion,
						  i.`tipoInstitucion_id` as iTipoInstitucion,
						  it.`nombre` as sNombreTipoInstitucion,
						  i.`direccion` as sDireccion,
						  i.`email` as sEmail,
						  i.`telefono` as sTelefono,
						  i.`sitioWeb` as sSitioWeb,
						  i.`horariosAtencion` as sHorariosAtencion,
						  i.`autoridades` as sAutoridades,
						  i.`cargo` as sCargo,
						  i.`personeriaJuridica` as sPersoneriaJuridica,
						  i.`sedes` as sSedes,
						  i.`actividadesMes` as sActividadesMes,
						  i.`usuario_id` as iUsuarioId,
						  i.`latitud` as sLatitud,
						  i.`longitud` as sLongitud,
						  prov.`id` as provinciaId, 
						  pais.id as paisId
                     FROM
                       	instituciones i 
                     JOIN 
                     	usuarios u ON u.id = i.usuario_id 
                     JOIN
                     	instituciones_tipos it ON it.id = i.tipoInstitucion_id
 					LEFT JOIN `ciudades` c on c.`id` = i.`ciudades_id`
 					LEFT JOIN `provincias` prov on prov.`id` = c.`provincia_id`
 					LEFT JOIN `paises` pais on pais.`id` = prov.`paises_id` ";
            $WHERE = array();
           	if(isset($filtro['i.nombre']) && $filtro['i.nombre']!=""){
	           	$WHERE[]= $this->crearFiltroTexto('i.nombre', $filtro['i.nombre']);
           	}
           	if(isset($filtro['i.id']) && $filtro['i.id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('i.id', $filtro['i.id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['i.tipoInstitucion_id']) && $filtro['i.tipoInstitucion_id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('i.tipoInstitucion_id', $filtro['i.tipoInstitucion_id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['pais.id']) && $filtro['pais.id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('pais.id', $filtro['pais.id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['prov.id']) && $filtro['prov.id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('prov.id', $filtro['prov.id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['i.ciudades_id']) && $filtro['i.ciudades_id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('i.ciudades_id', $filtro['i.ciudades_id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['i.usuario_id']) && $filtro['i.usuario_id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('i.usuario_id', $filtro['i.usuario_id'], MYSQL_TYPE_INT);
           	}
           	if(isset($filtro['i.tipoInstitucion_id']) && $filtro['i.tipoInstitucion_id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('i.tipoInstitucion_id', $filtro['i.tipoInstitucion_id'], MYSQL_TYPE_INT);
           	}
            $sSQL 	= $this->agregarFiltrosConsulta($sSQL, $WHERE);
            
			/*if(!empty($filtro)){     
            	$sSQL .=" AND ".$this->crearCondicionSimple($filtro);
            }*/
                    
	 		if (isset($sOrderBy) && isset($sOrder)){
				$sSQL .= " order by $sOrderBy $sOrder ";
			}
			if ($iIniLimit!==null && $iRecordCount!==null){
				$sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
			}
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

			$aInstituciones = array();
            while($oObj = $db->oNextRecord()){
            	$oInstitucion 			= new stdClass();
            	$oInstitucion->iId 		= $oObj->iId;
            	$oInstitucion->sNombre  = $oObj->sNombre;
            	$oInstitucion->iModerado= $oObj->iModerado;
            	$oInstitucion->sDescripcion	= $oObj->sDescripcion;
            	$oInstitucion->iTipoInstitucion = $oObj->iTipoInstitucion;
            	$oInstitucion->sNombreTipoInstitucion = $oObj->sNombreTipoInstitucion;
            	$oInstitucion->sDireccion 	= $oObj->sDireccion;
            	$oInstitucion->sEmail   = $oObj->sEmail;
            	$oInstitucion->sTelefono= $oObj->sTelefono;
            	$oInstitucion->sSitioWeb= $oObj->sSitioWeb;
            	$oInstitucion->sHorariosAtencion= $oObj->sHorariosAtencion;
            	$oInstitucion->sAutoridades	= $oObj->sAutoridades;
            	$oInstitucion->sCargo   = $oObj->sCargo;
            	$oInstitucion->sPersoneriaJuridica 	= $oObj->sPersoneriaJuridica;
            	$oInstitucion->sSedes   = $oObj->sSedes;
            	$oInstitucion->sActividadesMes 	= $oObj->sActividadesMes;
            	$oInstitucion->iCiudadId= $oObj->iCiudad;
            	$oInstitucion->sLatitud= $oObj->sLatitud;
            	$oInstitucion->sLongitud= $oObj->sLongitud;
            	$oInstitucion->oCiudad  = ComunidadController::getInstance()->getCiudadById($oObj->iCiudad);
            	$oInstitucion->oUsuario  = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
            	$aInstituciones[] = Factory::getInstitucionInstance($oInstitucion);
            }

          	return $aInstituciones;
        }catch(Exception $e){
           return null;
            throw new Exception($e->getMessage(), 0);
        }
	}
    
    
    /**
     * Le pongo NULL a todos los usuarios que estan asignados a la institucion
     */
    public function borrar($iInstitucionId) {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("update personas set instituciones_id = null where instituciones_id = '".$iInstitucionId."'");            
            $db->execSQL("delete from instituciones where id = '".$iInstitucionId."'");

            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }
   
    public function listaTiposDeInstitucion($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	try{
   	        $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        it.id as iId, it.nombre as sNombre
                        FROM
                       instituciones_tipos it ";
                    if(!empty($filtro)){     
                    	$sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                    }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

			$vInstitucionesTipos = array();
            while($oObj = $db->oNextRecord()){
            	$vInstitucionesTipos[]	= $oObj;
            }

           return $vInstitucionesTipos;
        }catch(Exception $e){
        	return null;
            throw new Exception($e->getMessage(), 0);
        }
    }
}