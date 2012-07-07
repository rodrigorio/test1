<?php

/**
 * Description of class InstitucionMySQLIntermediary
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

            if(null !== $oInstitucion->getCiudad()){
                $iCiudadId = $oInstitucion->getCiudad()->getId();
            }else{
                $iCiudadId = 'null';
            }

            if(null !== $oInstitucion->getUsuario()){
                $iUsuarioId = $oInstitucion->getUsuario()->getId();
            }else{
                $iUsuarioId = 'null';
            }

            $sSQL = " INSERT INTO instituciones ".
                    " SET nombre = ".$this->escStr($oInstitucion->getNombre()).", ".
                    " ciudades_id = '".$iCiudadId."', ".
                    " descripcion = ".$this->escStr($oInstitucion->getDescripcion()).", ".
                    " tipoInstitucion_id = ".$this->escInt($oInstitucion->getTipoInstitucionId()).", ".
                    " direccion = ".$this->escStr($oInstitucion->getDireccion()).", ".
                    " email = ".$this->escStr($oInstitucion->getEmail()).", ".
                    " telefono = ".$this->escStr($oInstitucion->getTelefono()).", ".
                    " sitioWeb = ".$this->escStr($oInstitucion->getSitioWeb()).", ".
                    " horariosAtencion = ".$this->escStr($oInstitucion->getHorariosAtencion()).", ".
                    " autoridades = ".$this->escStr($oInstitucion->getAutoridades()).", ".
                    " cargo = ".$this->escStr($oInstitucion->getCargo()).", ".
                    " personeriaJuridica = ".$this->escStr($oInstitucion->getPersoneriaJuridica()).", ".
                    " sedes = ".$this->escStr($oInstitucion->getSedes()).", ".
                    " latitud = ".$this->escStr($oInstitucion->getLatitud()).", ".
                    " longitud = ".$this->escStr($oInstitucion->getLongitud()).", ".
                    " actividadesMes = ".$this->escStr($oInstitucion->getActividadesMes()).", ".
                    " usuario_id = '".$iUsuarioId."' ";
			
            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            
            $db->commit();
            $oInstitucion->setId($iLastId);
            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oInstitucion)
    {
        try{
            $db = $this->conn;
            
            if(null !== $oInstitucion->getCiudad()){
                $iCiudadId = $oInstitucion->getCiudad()->getId();
            }else{
                $iCiudadId = 'null';
            }

            if(null !== $oInstitucion->getUsuario()){
                $iUsuarioId = $oInstitucion->getUsuario()->getId();
            }else{
                $iUsuarioId = 'null';
            }
       
            $sSQL = " UPDATE instituciones ".
                    " SET nombre = ".$this->escStr($oInstitucion->getNombre()).", ".
                    " ciudades_id = '".$iCiudadId."', ".
                    " descripcion = ".$this->escStr($oInstitucion->getDescripcion()).", ".
                    " tipoInstitucion_id = ".$this->escInt($oInstitucion->getTipoInstitucionId()).", ".
                    " direccion = ".$this->escStr($oInstitucion->getDireccion()).", ".
                    " email = ".$this->escStr($oInstitucion->getEmail()).", ".
                    " telefono = ".$this->escStr($oInstitucion->getTelefono()).", ".
                    " sitioWeb = ".$this->escStr($oInstitucion->getSitioWeb()).", ".
                    " horariosAtencion = ".$this->escStr($oInstitucion->getHorariosAtencion()).", ".
                    " autoridades = ".$this->escStr($oInstitucion->getAutoridades()).", ".
                    " cargo = ".$this->escStr($oInstitucion->getCargo()).", ".
                    " personeriaJuridica = ".$this->escStr($oInstitucion->getPersoneriaJuridica()).", ".
                    " sedes = ".$this->escStr($oInstitucion->getSedes()).", ".
                    " latitud = ".$this->escStr($oInstitucion->getLatitud()).", ".
                    " longitud = ".$this->escStr($oInstitucion->getLongitud()).", ".
                    " actividadesMes = ".$this->escStr($oInstitucion->getActividadesMes()).", ".
                    " usuario_id = '".$iUsuarioId."' ".
                    " where id = '".$oInstitucion->getId()."' ";
						 
            $db->execSQL($sSQL);
            $db->commit();

            return true;
             
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

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          i.id as iId,
                          i.nombre as sNombre,
                          i.ciudades_id as iCiudadId,
                          i.descripcion as sDescripcion,
                          i.tipoInstitucion_id as iTipoInstitucionId,
                          i.direccion as sDireccion,
                          i.email as sEmail,
                          i.telefono as sTelefono,
                          i.sitioWeb as sSitioWeb,
                          i.horariosAtencion as sHorariosAtencion,
                          i.autoridades as sAutoridades,
                          i.cargo as sCargo,
                          i.personeriaJuridica as sPersoneriaJuridica,
                          i.sedes as sSedes,
                          i.actividadesMes as sActividadesMes,
                          i.usuario_id as iUsuarioId,
                          i.latitud as sLatitud,
                          i.longitud as sLongitud,

                          it.nombre as sNombreTipoInstitucion,

                          m.iModeracionId,
                          m.sModeracionEstado,
                          m.sModeracionMensaje,
                          m.dModeracionFecha
                     FROM
                       	instituciones i
                     JOIN
                     	usuarios u ON u.id = i.usuario_id
                     JOIN
                     	instituciones_tipos it ON it.id = i.tipoInstitucion_id
                     LEFT JOIN
                        (SELECT
                            m.id AS iModeracionId, m.instituciones_id, m.estado AS sModeracionEstado, m.mensaje AS sModeracionMensaje, m.fecha AS dModeracionFecha
                         FROM
                            moderaciones m
                         JOIN
                            (SELECT MAX(m.id) AS idd FROM moderaciones m GROUP BY instituciones_id) AS filtro ON filtro.idd = m.id)
                         AS m ON m.instituciones_id = i.id
                     LEFT JOIN ciudades c on c.id = i.ciudades_id ";

            if(!empty($filtro)){
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
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
            	$oInstitucion = new stdClass();
            	$oInstitucion->iId = $oObj->iId;
            	$oInstitucion->sNombre = $oObj->sNombre;
                $oInstitucion->iCiudadId = $oObj->iCiudadId;
                $oInstitucion->oCiudad = ComunidadController::getInstance()->getCiudadById($oObj->iCiudadId);
            	$oInstitucion->sDescripcion = $oObj->sDescripcion;
            	$oInstitucion->iTipoInstitucion = $oObj->iTipoInstitucionId;
            	$oInstitucion->sNombreTipoInstitucion = $oObj->sNombreTipoInstitucion;
            	$oInstitucion->sDireccion = $oObj->sDireccion;
            	$oInstitucion->sEmail = $oObj->sEmail;
            	$oInstitucion->sTelefono = $oObj->sTelefono;
            	$oInstitucion->sSitioWeb = $oObj->sSitioWeb;
            	$oInstitucion->sHorariosAtencion = $oObj->sHorariosAtencion;
            	$oInstitucion->sAutoridades = $oObj->sAutoridades;
            	$oInstitucion->sCargo = $oObj->sCargo;
            	$oInstitucion->sPersoneriaJuridica = $oObj->sPersoneriaJuridica;
            	$oInstitucion->sSedes = $oObj->sSedes;
            	$oInstitucion->sActividadesMes = $oObj->sActividadesMes;
            	$oInstitucion->sLatitud= $oObj->sLatitud;
            	$oInstitucion->sLongitud= $oObj->sLongitud;
            	$oInstitucion->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);

                //objeto ultima moderacion
                if(null !== $oObj->iModeracionId){
                    $oModeracion                   = new stdClass();
                    $oModeracion->iId              = $oObj->iModeracionId;
                    $oModeracion->dFecha           = $oObj->dModeracionFecha;
                    $oModeracion->sMensaje         = $oObj->sModeracionMensaje;
                    $oModeracion->sEstado          = $oObj->sModeracionEstado;

                    $oInstitucion->oModeracion = Factory::getModeracionInstance($oModeracion);
                }

            	$aInstituciones[] = Factory::getInstitucionInstance($oInstitucion);
            }

            return $aInstituciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Join con otras tablas para realizar el filtro.
     */
    public function buscar($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          i.id as iId, 
                          i.nombre as sNombre,
                          i.ciudades_id as iCiudadId,
                          i.descripcion as sDescripcion,
                          i.tipoInstitucion_id as iTipoInstitucionId,
                          i.direccion as sDireccion,
                          i.email as sEmail,
                          i.telefono as sTelefono,
                          i.sitioWeb as sSitioWeb,
                          i.horariosAtencion as sHorariosAtencion,
                          i.autoridades as sAutoridades,
                          i.cargo as sCargo,
                          i.personeriaJuridica as sPersoneriaJuridica,
                          i.sedes as sSedes,
                          i.actividadesMes as sActividadesMes,
                          i.usuario_id as iUsuarioId,
                          i.latitud as sLatitud,
                          i.longitud as sLongitud,

                          it.nombre as sNombreTipoInstitucion,
                          
                          pa.id as iPaisId,
                          pr.id as iProvinciaId,

                          m.iModeracionId,
                          m.sModeracionEstado,
                          m.sModeracionMensaje,
                          m.dModeracionFecha
                     FROM
                       	instituciones i 
                     JOIN 
                     	usuarios u ON u.id = i.usuario_id 
                     JOIN
                     	instituciones_tipos it ON it.id = i.tipoInstitucion_id
                     LEFT JOIN
                        (SELECT
                            m.id AS iModeracionId, m.instituciones_id, m.estado AS sModeracionEstado, m.mensaje AS sModeracionMensaje, m.fecha AS dModeracionFecha
                         FROM
                            moderaciones m
                         JOIN
                            (SELECT MAX(m.id) AS idd FROM moderaciones m GROUP BY instituciones_id) AS filtro ON filtro.idd = m.id)
                         AS m ON m.instituciones_id = i.id 
                     LEFT JOIN ciudades c on c.id = i.ciudades_id
                     LEFT JOIN provincias pr on pr.id = c.provincia_id 
                     LEFT JOIN paises pa on pa.id = pr.paises_id ";
            
            $WHERE = array();
            
            if(isset($filtro['i.nombre']) && $filtro['i.nombre']!=""){
                $WHERE[]= $this->crearFiltroTexto('i.nombre', $filtro['i.nombre']);
            }
            if(isset($filtro['i.tipoInstitucion_id']) && $filtro['i.tipoInstitucion_id']!=""){
                $WHERE[]= $this->crearFiltroSimple('i.tipoInstitucion_id', $filtro['i.tipoInstitucion_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['pa.id']) && $filtro['pa.id']!=""){
                $WHERE[]= $this->crearFiltroSimple('pa.id', $filtro['pa.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['pr.id']) && $filtro['pr.id']!=""){
                $WHERE[]= $this->crearFiltroSimple('pr.id', $filtro['pr.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['i.ciudades_id']) && $filtro['i.ciudades_id']!=""){
                $WHERE[]= $this->crearFiltroSimple('i.ciudades_id', $filtro['i.ciudades_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['i.usuario_id']) && $filtro['i.usuario_id']!=""){
                $WHERE[]= $this->crearFiltroSimple('i.usuario_id', $filtro['i.usuario_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['m.sModeracionEstado']) && $filtro['m.sModeracionEstado'] != ""){
                $WHERE[] = $this->crearFiltroSimple('m.sModeracionEstado', $filtro['m.sModeracionEstado']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by i.nombre asc ";
            }

            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
                   
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aInstituciones = array();
            while($oObj = $db->oNextRecord()){

            	$oInstitucion = new stdClass();
            	$oInstitucion->iId = $oObj->iId;
            	$oInstitucion->sNombre = $oObj->sNombre;                
                $oInstitucion->iCiudadId = $oObj->iCiudadId;
                $oInstitucion->oCiudad = ComunidadController::getInstance()->getCiudadById($oObj->iCiudadId);
            	$oInstitucion->sDescripcion = $oObj->sDescripcion;
            	$oInstitucion->iTipoInstitucion = $oObj->iTipoInstitucionId;
            	$oInstitucion->sNombreTipoInstitucion = $oObj->sNombreTipoInstitucion;
            	$oInstitucion->sDireccion = $oObj->sDireccion;
            	$oInstitucion->sEmail = $oObj->sEmail;
            	$oInstitucion->sTelefono = $oObj->sTelefono;
            	$oInstitucion->sSitioWeb = $oObj->sSitioWeb;                
            	$oInstitucion->sHorariosAtencion = $oObj->sHorariosAtencion;
            	$oInstitucion->sAutoridades = $oObj->sAutoridades;
            	$oInstitucion->sCargo = $oObj->sCargo;
            	$oInstitucion->sPersoneriaJuridica = $oObj->sPersoneriaJuridica;
            	$oInstitucion->sSedes = $oObj->sSedes;
            	$oInstitucion->sActividadesMes = $oObj->sActividadesMes;
            	$oInstitucion->sLatitud= $oObj->sLatitud;
            	$oInstitucion->sLongitud= $oObj->sLongitud;

                //objeto ultima moderacion
                if(null !== $oObj->iModeracionId){
                    $oModeracion                   = new stdClass();
                    $oModeracion->iId              = $oObj->iModeracionId;
                    $oModeracion->dFecha           = $oObj->dModeracionFecha;
                    $oModeracion->sMensaje         = $oObj->sModeracionMensaje;
                    $oModeracion->sEstado          = $oObj->sModeracionEstado;

                    $oInstitucion->oModeracion = Factory::getModeracionInstance($oModeracion);
                }

            	$oInstitucion->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);

            	$aInstituciones[] = Factory::getInstitucionInstance($oInstitucion);
            }

            return $aInstituciones;
            
        }catch(Exception $e){
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
        }
    }
   
    public function listaTiposDeInstitucion($filtro = array(), &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
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
            	$vInstitucionesTipos[] = $oObj;
            }

           return $vInstitucionesTipos;
        }catch(Exception $e){
        	return null;
            throw new Exception($e->getMessage(), 0);
        }
    }
}