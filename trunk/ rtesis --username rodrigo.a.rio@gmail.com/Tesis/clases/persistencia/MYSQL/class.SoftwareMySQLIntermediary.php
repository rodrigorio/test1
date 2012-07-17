<?php

class SoftwareMySQLIntermediary extends SoftwareIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return SoftwareMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public function existe($filtro)
    {
    	try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        fichas_abstractas fa
                    JOIN software s ON fa.id = s.id ";

            $WHERE = array();

            if(isset($filtro['s.id']) && $filtro['s.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('s.id', $filtro['s.id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }

            return true;
    	}catch(Exception $e){
            throw new Exception($e);
        }
    }
	
    public function guardar($oSoftware)
    {
        try{
            if($oSoftware->getId() !== null){
                return $this->actualizar($oSoftware);
            } else {
                return $this->insertar($oSoftware);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
        
    public function insertar($oSoftware)
    {
        try{
            $db = $this->conn;
            
            $db->begin_transaction();

            $activo = $oSoftware->isActivo()?"1":"0";

            $sSQL = " insert into fichas_abstractas set ".
                    " titulo = ".$db->escape($oSoftware->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oSoftware->getDescripcion(),true);

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $iUsuarioId = $oSoftware->getUsuario()->getId();
            $iCategoriaId = $oSoftware->getCategoria()->getId();

            $publico = $oSoftware->isPublico()?"1":"0";
            $activoComentarios = $oSoftware->isActivoComentarios()?"1":"0";

            $sSQL = " insert into software set ".
                    " id = ".$db->escape($iLastId, false, MYSQL_TYPE_INT).", " .
                    " usuarios_id = ".$db->escape($iUsuarioId, false, MYSQL_TYPE_INT).", ".
                    " categorias_id = ".$db->escape($iCategoriaId, false, MYSQL_TYPE_INT).", ".
                    " publico = ".$publico.", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oSoftware->getDescripcionBreve(), true).", ".
                    " enlaces = ".$db->escape($oSoftware->getEnlaces(), true);

            $db->execSQL($sSQL);

            $db->commit();
            
            $oSoftware->setId($iLastId);
            $oSoftware->setFecha(date("Y/m/d"));

            return true;            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oSoftware)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $iSoftwareId = $oSoftware->getId();
            $activo = $oSoftware->isActivo()?"1":"0";
		        
            $sSQL = " update fichas_abstractas set ".
                    " titulo = ".$db->escape($oSoftware->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oSoftware->getDescripcion(), true)." ".
                    " where id = ".$iSoftwareId;

            $db->execSQL($sSQL);

            $publico = $oSoftware->isPublico()?"1":"0";
            $activoComentarios = $oSoftware->isActivoComentarios()?"1":"0";
            $iCategoriaId = $oSoftware->getCategoria()->getId();
             
            $sSQL = " update software set ".
                    " publico = ".$publico.", ".
                    " categorias_id = ".$db->escape($iCategoriaId, false, MYSQL_TYPE_INT).", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oSoftware->getDescripcionBreve(), true).", ".
                    " enlaces = ".$db->escape($oSoftware->getEnlaces(), true)." ".
                    " where id = ".$iSoftwareId;
						 
             $db->execSQL($sSQL);
             $db->commit();

             return true;
             
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
        
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          f.id as iId, 
                          f.titulo as sTitulo,
                          f.fecha as dFecha,
                          f.activo as bActivo,
                          f.descripcion as sDescripcion,
                          
                          s.usuarios_id as iUsuarioId,
                          s.categorias_id as iCategoriaId,
                          s.publico as bPublico,
                          s.activoComentarios as bActivoComentarios,
                          s.descripcionBreve as sDescripcionBreve,
                          s.enlaces AS sEnlaces,

                          m.iModeracionId,
                          m.sModeracionEstado,
                          m.sModeracionMensaje,
                          m.dModeracionFecha
                    FROM
                        fichas_abstractas f
                    JOIN
                        software s ON s.id = f.id
                    LEFT JOIN
                        (SELECT
                            m.id AS iModeracionId, m.fichas_abstractas_id, m.estado AS sModeracionEstado, m.mensaje AS sModeracionMensaje, m.fecha AS dModeracionFecha
                         FROM
                            moderaciones m
                         JOIN
                            (SELECT MAX(m.id) AS idd FROM moderaciones m GROUP BY fichas_abstractas_id) AS filtro ON filtro.idd = m.id)
                        AS m ON m.fichas_abstractas_id = f.id ";
                    
            if(!empty($filtro)){
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
            }
            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT);
            }

            $db->query($sSQL);
            $iRecordsTotal = (int)$db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aSoftware = array();
            while($oObj = $db->oNextRecord()){
            	$oSoftware = new stdClass();
            	$oSoftware->iId = $oObj->iId;
            	$oSoftware->sTitulo  = $oObj->sTitulo;
            	$oSoftware->dFecha = $oObj->dFecha;
            	$oSoftware->bActivo = ($oObj->bActivo == "1")?true:false;
            	$oSoftware->sDescripcion = $oObj->sDescripcion;
            	$oSoftware->iUsuarioId = $oObj->iUsuarioId;
                $oSoftware->iCategoriaId = $oObj->iCategoriaId;
            	$oSoftware->bPublico = ($oObj->bPublico == "1") ? true:false;
            	$oSoftware->bActivoComentarios = ($oObj->bActivoComentarios == "1")?true:false;
            	$oSoftware->sDescripcionBreve = $oObj->sDescripcionBreve;
            	$oSoftware->sEnlaces = $oObj->sEnlaces;

                //objeto ultima moderacion
                if(null !== $oObj->iModeracionId){
                    $oModeracion                   = new stdClass();
                    $oModeracion->iId              = $oObj->iModeracionId;
                    $oModeracion->dFecha           = $oObj->dModeracionFecha;
                    $oModeracion->sMensaje         = $oObj->sModeracionMensaje;
                    $oModeracion->sEstado          = $oObj->sModeracionEstado;

                    $oSoftware->oModeracion = Factory::getModeracionInstance($oModeracion);
                }
  
            	$aSoftware[] = Factory::getSoftwareInstance($oSoftware);
            }

            return $aSoftware;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * En los listados de publicaciones se muestran tanto las publicaciones como los reviews.
     * Este metodo se utiliza para los filtros de los listados y devuelve un array con objetos de las dos clases.
     */
    public function buscar($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{            
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          f.id as iId,
                          f.titulo as sTitulo,
                          f.fecha as dFecha,
                          f.activo as bActivo,
                          f.descripcion as sDescripcion,

                          s.usuarios_id as iUsuarioId,
                          s.categorias_id as iCategoriaId,
                          s.publico as bPublico,
                          s.activoComentarios as bActivoComentarios,
                          s.descripcionBreve as sDescripcionBreve,
                          s.enlaces AS sEnlaces,

                          p.apellido,

                          m.iModeracionId,
                          m.sModeracionEstado,
                          m.sModeracionMensaje,
                          m.dModeracionFecha
                    FROM
                        fichas_abstractas f
                    JOIN
                        software s ON s.id = f.id
                    JOIN
                        personas p ON p.id = s.usuarios_id
                    LEFT JOIN
                        (SELECT
                            m.id AS iModeracionId, m.fichas_abstractas_id, m.estado AS sModeracionEstado, m.mensaje AS sModeracionMensaje, m.fecha AS dModeracionFecha
                         FROM
                            moderaciones m
                         JOIN
                            (SELECT MAX(m.id) AS idd FROM moderaciones m GROUP BY fichas_abstractas_id) AS filtro ON filtro.idd = m.id)
                        AS m ON m.fichas_abstractas_id = f.id ";

            $WHERE = array();

            if(isset($filtro['s.usuarios_id']) && $filtro['s.usuarios_id'] != ""){
                $WHERE[]= $this->crearFiltroSimple('s.usuarios_id', $filtro['s.usuarios_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['s.categorias_id']) && $filtro['s.categorias_id'] != ""){
                $WHERE[]= $this->crearFiltroSimple('s.categorias_id', $filtro['s.categorias_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['s.publico']) && $filtro['s.publico'] != ""){
                $WHERE[] = $this->crearFiltroSimple('s.publico', $filtro['s.publico']);
            }
            if(isset($filtro['p.apellido']) && $filtro['p.apellido'] != ""){
                $WHERE[] = $this->crearFiltroTexto('p.apellido', $filtro['p.apellido']);
            }
            if(isset($filtro['f.titulo']) && $filtro['f.titulo'] != ""){
                $WHERE[] = $this->crearFiltroTexto('f.titulo', $filtro['f.titulo']);
            }
            if(isset($filtro['f.activo']) && $filtro['f.activo'] != ""){
                $WHERE[] = $this->crearFiltroSimple('f.activo', $filtro['f.activo']);
            }
            if(isset($filtro['fecha']) && null !== $filtro['fecha']){
                if(is_array($filtro['fecha'])){
                    $WHERE[] = $this->crearFiltroFechaDesdeHasta('f.fecha', $filtro['fecha']);
                }
            }
            if(isset($filtro['m.sModeracionEstado']) && $filtro['m.sModeracionEstado'] != ""){
                $WHERE[] = $this->crearFiltroSimple('m.sModeracionEstado', $filtro['m.sModeracionEstado']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by f.fecha desc ";
            }
            
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aSoftware = array();
            while($oObj = $db->oNextRecord()){
            	$oSoftware = new stdClass();
            	$oSoftware->iId = $oObj->iId;
            	$oSoftware->sTitulo  = $oObj->sTitulo;
            	$oSoftware->dFecha = $oObj->dFecha;
            	$oSoftware->bActivo = ($oObj->bActivo == "1")?true:false;
            	$oSoftware->sDescripcion = $oObj->sDescripcion;
            	$oSoftware->iUsuarioId = $oObj->iUsuarioId;
                $oSoftware->iCategoriaId = $oObj->iCategoriaId;
            	$oSoftware->bPublico = ($oObj->bPublico == "1") ? true:false;
            	$oSoftware->bActivoComentarios = ($oObj->bActivoComentarios == "1")?true:false;
            	$oSoftware->sDescripcionBreve = $oObj->sDescripcionBreve;
            	$oSoftware->sEnlaces = $oObj->sEnlaces;

                //objeto ultima moderacion
                if(null !== $oObj->iModeracionId){
                    $oModeracion                   = new stdClass();
                    $oModeracion->iId              = $oObj->iModeracionId;
                    $oModeracion->dFecha           = $oObj->dModeracionFecha;
                    $oModeracion->sMensaje         = $oObj->sModeracionMensaje;
                    $oModeracion->sEstado          = $oObj->sModeracionEstado;

                    $oSoftware->oModeracion = Factory::getModeracionInstance($oModeracion);
                }

            	$aSoftware[] = Factory::getSoftwareInstance($oSoftware);
            }
            
            return $aSoftware;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }
                                                    
    public function borrar($iFichaAbstractaId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from fichas_abstractas where id = '".$iFichaAbstractaId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            return false;
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * La relacion con videos archivos y fotos es de la clase abstracta
     * asi que me alcanza con utilizar la tabla fichas_abstractas
     */
    public function obtenerCantidadElementosAdjuntos($iFichaId)
    {
        try{
            $cantFotos = $cantVideos = $cantArchivos = 0;

            $db = $this->conn;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            archivos where fichas_abstractas_id = '".$iFichaId."'");
            $cantArchivos = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            fotos where fichas_abstractas_id = '".$iFichaId."'");
            $cantFotos = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            embed_videos where fichas_abstractas_id = '".$iFichaId."'");
            $cantVideos = $db->oNextRecord()->cantidad;

            return array($cantFotos, $cantVideos, $cantArchivos);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
        
    public function actualizarCampoArray($objects, $cambios){}  
}