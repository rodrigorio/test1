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
                $iCiudadId = $this->escInt($oInstitucion->getCiudad()->getId());
            }else{
                $iCiudadId = 'NULL';
            }

            if(null !== $oInstitucion->getUsuario()){
                $iUsuarioId = $this->escInt($oInstitucion->getUsuario()->getId());
            }else{
                $iUsuarioId = 'NULL';
            }

            $sSQL = " INSERT INTO instituciones ".
                    " SET nombre = ".$this->escStr($oInstitucion->getNombre()).", ".
                    " ciudades_id = ".$iCiudadId.", ".
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
                    " usuario_id = ".$iUsuarioId." ";
			
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
                $iCiudadId = $this->escInt($oInstitucion->getCiudad()->getId());
            }else{
                $iCiudadId = 'NULL';
            }

            if(null !== $oInstitucion->getUsuario()){
                $iUsuarioId = $this->escInt($oInstitucion->getUsuario()->getId());
            }else{
                $iUsuarioId = 'NULL';
            }
       
            $sSQL = " UPDATE instituciones ".
                    " SET nombre = ".$this->escStr($oInstitucion->getNombre()).", ".
                    " ciudades_id = ".$iCiudadId.", ".
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
                    " usuario_id = ".$iUsuarioId." ".
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
                     LEFT JOIN
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
               
                if(null !== $oObj->iUsuarioId){
                    $oInstitucion->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                }else{
                    $oInstitucion->oUsuario = null;
                }

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
                     LEFT JOIN
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
            
            //lo hago asi porque es un and que no se puede ser por filtro automatico
            if(isset($filtro['latLng'])){
                $WHERE[] = " (i.latitud <> '' AND i.longitud <> '') ";
            }

            //con un minimo de denuncias. en el valor tengo la cantidad.
            if(isset($filtro['minDenuncias']) && $filtro['minDenuncias'] != ""){
                $WHERE[] = " i.id IN (SELECT d.instituciones_id FROM denuncias d WHERE d.instituciones_id IS NOT NULL
                                      GROUP BY d.instituciones_id
                                      HAVING COUNT(*) >= ".$filtro['minDenuncias'].") ";
            }

            //con un maximo de N de denuncias. en el valor tengo la cantidad.
            if(isset($filtro['maxDenuncias']) && $filtro['maxDenuncias'] != ""){
                $WHERE[] = " i.id NOT IN (SELECT d.instituciones_id FROM denuncias d WHERE d.instituciones_id IS NOT NULL
                                      GROUP BY d.instituciones_id
                                      HAVING COUNT(*) >= ".$filtro['maxDenuncias'].") ";
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

                if(null !== $oObj->iUsuarioId){
                    $oInstitucion->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                }else{
                    $oInstitucion->oUsuario = null;
                }

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
     * Devuelve todas las instituciones que tienen solicitud para administracion de contenido
     */
    public function obtenerInstitucionesSolicitud($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try
        {
            $db = $this->conn;
            
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
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
                     LEFT JOIN
                     	usuarios u ON u.id = i.usuario_id
                     JOIN
                     	instituciones_tipos it ON it.id = i.tipoInstitucion_id
                     JOIN
                        institucion_solicitudes iss ON iss.instituciones_id = i.id
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

                if(null !== $oObj->iUsuarioId){
                    $oInstitucion->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                }else{
                    $oInstitucion->oUsuario = null;
                }

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
     * Le pongo NULL a todos los usuarios que estan asignados a la institucion
     */
    public function borrar($iInstitucionId)
    {
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

    public function obtenerSolicitudes($filtro = array(), &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        iss.id as iId,
                        iss.fecha as dFecha,
                        iss.mensaje as sMensaje,
                        iss.usuarios_id as iUsuarioId
                    FROM
                        institucion_solicitudes iss ";

            $WHERE = array();

            if(isset($filtro['iss.id']) && $filtro['iss.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('iss.id', $filtro['iss.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['iss.usuarios_id']) && $filtro['iss.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('iss.usuarios_id', $filtro['iss.usuarios_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by fecha asc ";
            }

            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aSolicitudes = array();
            while($oObj = $db->oNextRecord()){
                $oSolicitud = new stdClass();
                $oSolicitud->iId = $oObj->iId;
                $oSolicitud->dFecha = $oObj->dFecha;
                $oSolicitud->sMensaje = $oObj->sMensaje;
                $oSolicitud->oUsuario = ComunidadController::getInstance()->getUsuarioById($oObj->iUsuarioId);

                $aSolicitudes[] = Factory::getSolicitudInstance($oSolicitud);
            }

            return $aSolicitudes;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }

    /**
     * @return boolean si el usuario ya tiene una solicitud de administracion de institucion pendiente.
     */
    public function existeSolicitud($iInstitucionId, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        institucion_solicitudes iss
                    WHERE iss.usuarios_id = ".$iUsuarioId."
                          AND iss.instituciones_id = ".$iInstitucionId;
            
            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            return true;

    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarSolicitudes($oInstitucion)
    {
        if(null !== $oInstitucion->getSolicitudes()){
            foreach($oInstitucion->getSolicitudes() as $oSolicitud){
                if(null !== $oSolicitud->getId()){
                    return $this->actualizarSolicitud($oSolicitud);
                }else{                    
                    return $this->insertarSolicitud($oSolicitud, $oInstitucion->getId());
                }
            }
        }
    }

    public function actualizarSolicitud($oSolicitud)
    {
        try{
            $db = $this->conn;

            $sSQL = "UPDATE institucion_solicitudes SET ".                    
                    "   mensaje = ".$this->escStr($oSolicitud->getMensaje())." ".
                    "WHERE id = ".$this->escInt($oSolicitud->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarSolicitud($oSolicitud, $iInstitucionId)
    {
        try{
            $db = $this->conn;

            $iUsuarioId = $oSolicitud->getUsuario()->getId();

            $sSQL = "INSERT INTO institucion_solicitudes SET ".
                    "   usuarios_id = ".$iUsuarioId.", ".
                    "   instituciones_id = ".$iInstitucionId.", ".
                    "   mensaje = ".$this->escStr($oSolicitud->getMensaje())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oSolicitud->setId($iLastId);
            $oSolicitud->setFecha(date("d/m/Y"));

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Limpia todas las solicitudes para una institucion dada.
     */
    public function limpiarSolicitudes($iInstitucionId)
    {
        try{
            $db = $this->conn;
           
            $db->execSQL("delete from institucion_solicitudes where instituciones_id = ".$iInstitucionId." ");
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }
}