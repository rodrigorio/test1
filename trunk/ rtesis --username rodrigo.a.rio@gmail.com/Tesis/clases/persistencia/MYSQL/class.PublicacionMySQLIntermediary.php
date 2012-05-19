<?php

/**
 * ATENCION
 *
 * En este persistencia se establecen los metodos tanto para objetos Publicacion como para
 * objetos Review.
 *
 * Es asi porque los dos objetos aparecen intercalados en un mismo listado.
 * Ademas heredan de la misma clase y los review a pesar de guardarse en otra tabla comparten muchos campos.
 * 
 */
class PublicacionMySQLIntermediary extends PublicacionIntermediary
{
 private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}

	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return PublicacionMySQLIntermediary
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
                        f.id as iId, f.titulo as sTitulo
                        FROM
                       fichas_abstractas f ";
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
	
    public function guardar($oPublicacion) {
        try {
            if($oPublicacion->getId() !== null) {
                return $this->actualizar($oPublicacion);
            } else {
                return $this->insertar($oPublicacion);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
        
    public function insertar($oPublicacion)
    {
        try{
            $db = $this->conn;
            
            $db->begin_transaction();

            $activo = $oPublicacion->isActivo()?"1":"0";

            $sSQL = " insert into fichas_abstractas set ".
                    " titulo = ".$db->escape($oPublicacion->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oPublicacion->getDescripcion(),true);

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $iUsuarioId = $oPublicacion->getUsuario()->getId();

            $publico = $oPublicacion->isPublico()?"1":"0";
            $activoComentarios = $oPublicacion->isActivoComentarios()?"1":"0";

            $sSQL = " insert into publicaciones set ".
                    " id = ".$db->escape($iLastId, false, MYSQL_TYPE_INT).", " .
                    " usuarios_id = ".$db->escape($iUsuarioId, false, MYSQL_TYPE_INT).", ".
                    " publico = ".$publico.", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oPublicacion->getDescripcionBreve(), true).", ".
                    " keywords = ".$db->escape($oPublicacion->getKeywords(), true);

            $db->execSQL($sSQL);

            $db->commit();
            
            $oPublicacion->setId($iLastId);

            return true;            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oPublicacion)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $iPublicacionId = $oPublicacion->getId();
            $activo = $oPublicacion->isActivo()?"1":"0";
		        
            $sSQL = " update fichas_abstractas set ".
                    " titulo = ".$db->escape($oPublicacion->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oPublicacion->getDescripcion(), true)." ".
                    " where id = ".$iPublicacionId;

            $db->execSQL($sSQL);

            $moderado = $oPublicacion->isModerado()?"1":"0";
            $publico = $oPublicacion->isPublico()?"1":"0";
            $activoComentarios = $oPublicacion->isActivoComentarios()?"1":"0";
             
            $sSQL = " update publicaciones set ".
                    " moderado = ".$moderado.", ".
                    " publico = ".$publico.", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oPublicacion->getDescripcionBreve(), true).", ".
                    " keywords = ".$db->escape($oPublicacion->getKeywords(), true)." ".
                    " where id = ".$iPublicacionId;
						 
             $db->execSQL($sSQL);
             $db->commit();

             return true;
             
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarReview($oReview) {
        try {
            if($oReview->getId() !== null) {
                return $this->actualizarReview($oReview);
            } else {
                return $this->insertarReview($oReview);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarReview($oReview)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $activo = $oReview->isActivo()?"1":"0";

            $sSQL = " insert into fichas_abstractas set ".
                    " titulo = ".$db->escape($oReview->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oReview->getDescripcion(),true);

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $iUsuarioId = $oReview->getUsuario()->getId();

            $publico = $oReview->isPublico()?"1":"0";
            $activoComentarios = $oReview->isActivoComentarios()?"1":"0";

            //lo hago a mano porque sino el scape int te transforma el 0 en null tmb
            $fRating = $oReview->getRating();
            if($fRating === null){
                $fRating = "null";
            }

            $sSQL = " insert into reviews set ".
                    " id = ".$db->escape($iLastId, false, MYSQL_TYPE_INT).", " .
                    " usuarios_id = ".$db->escape($iUsuarioId, false, MYSQL_TYPE_INT).", ".
                    " publico = ".$publico.", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oReview->getDescripcionBreve(), true).", ".
                    " keywords = ".$db->escape($oReview->getKeywords(), true).", ".
                    " itemType = ".$db->escape($oReview->getItemType(), true).", ".
                    " itemName = ".$db->escape($oReview->getItemName(), true).", ".
                    " itemEventSummary = ".$db->escape($oReview->getItemEventSummary(), true).", ".
                    " itemUrl = ".$db->escape($oReview->getItemUrl(), true).", ".
                    " rating = ".$fRating.", ".
                    " fuenteOriginal = ".$db->escape($oReview->getFuenteOriginal(), true);

            $db->execSQL($sSQL);

            $db->commit();

            $oReview->setId($iLastId);

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizarReview($oReview)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $iPublicacionId = $oPublicacion->getId();
            $activo = $oPublicacion->isActivo()?"1":"0";

            $sSQL = " update fichas_abstractas set ".
                    " titulo = ".$db->escape($oPublicacion->getTitulo(), true).", ".
                    " activo = ".$activo.", ".
                    " descripcion = ".$db->escape($oPublicacion->getDescripcion(), true)." ".
                    " where id = ".$iPublicacionId;

            $db->execSQL($sSQL);

            $moderado = $oPublicacion->isModerado()?"1":"0";
            $publico = $oPublicacion->isPublico()?"1":"0";
            $activoComentarios = $oPublicacion->isActivoComentarios()?"1":"0";

            $sSQL = " update publicaciones set ".
                    " moderado = ".$moderado.", ".
                    " publico = ".$publico.", ".
                    " activoComentarios = ".$activoComentarios.", ".
                    " descripcionBreve = ".$db->escape($oPublicacion->getDescripcionBreve(), true).", ".
                    " keywords = ".$db->escape($oPublicacion->getKeywords(), true)." ".
                    " where id = ".$iPublicacionId;

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
                          
                          p.usuarios_id as iUsuarioId,
                          p.moderado as bModerado,
                          p.publico as bPublico,
                          p.activoComentarios as bActivoComentarios,
                          p.descripcionBreve as sDescripcionBreve,
                          p.keywords as sKeywords
                    FROM
                        fichas_abstractas f
                    JOIN
                        publicaciones p ON p.id = f.id";
                    
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

            $aPublicaciones = array();
            while($oObj = $db->oNextRecord()){
            	$oPublicacion = new stdClass();
            	$oPublicacion->iId = $oObj->iId;
            	$oPublicacion->sTitulo  = $oObj->sTitulo;
            	$oPublicacion->dFecha = $oObj->dFecha;
            	$oPublicacion->bActivo = ($oObj->bActivo == "1") ? true : false;
            	$oPublicacion->sDescripcion = $oObj->sDescripcion;
            	$oPublicacion->iUsuarioId = $oObj->iUsuarioId;
            	$oPublicacion->bModerado = ($oObj->bModerado == "1") ? true:false;
            	$oPublicacion->bPublico = ($oObj->bPublico == "1") ? true:false;
            	$oPublicacion->bActivoComentarios = ($oObj->bActivoComentarios == "1")?true:false;
            	$oPublicacion->sDescripcionBreve = $oObj->sDescripcionBreve;
            	$oPublicacion->sKeywords = $oObj->sKeywords;
  
            	$aPublicaciones[] = Factory::getPublicacionInstance($oPublicacion);
            }

            return $aPublicaciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public final function obtenerReview($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                          f.id as iId,
                          f.titulo as sTitulo,
                          f.fecha as dFecha,
                          f.activo as bActivo,
                          f.descripcion as sDescripcion,

                          r.usuarios_id as iUsuarioId,
                          r.moderado as bModerado,
                          r.publico as bPublico,
                          r.activoComentarios as bActivoComentarios,
                          r.descripcionBreve as sDescripcionBreve,
                          r.keywords as sKeywords,
                          r.itemType as sItemType,
                          r.itemName as sItemName,
                          r.itemEventSummary as sItemEventSummary,
                          r.itemUrl as sItemUrl,
                          r.rating as fRating,
                          r.fuenteOriginal as sFuenteOriginal
                    FROM
                        fichas_abstractas f
                    JOIN
                        reviews r ON r.id = f.id";

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

            $aReviews = array();
            while($oObj = $db->oNextRecord()){
            	$oReview = new stdClass();
            	$oReview->iId = $oObj->iId;
            	$oReview->sTitulo  = $oObj->sTitulo;
            	$oReview->dFecha = $oObj->dFecha;
            	$oReview->bActivo = ($oObj->bActivo == "1") ? true : false;
            	$oReview->sDescripcion = $oObj->sDescripcion;
            	$oReview->iUsuarioId = $oObj->iUsuarioId;
            	$oReview->bModerado = ($oObj->bModerado == "1") ? true:false;
            	$oReview->bPublico = ($oObj->bPublico == "1") ? true:false;
            	$oReview->bActivoComentarios = ($oObj->bActivoComentarios == "1")?true:false;
            	$oReview->sDescripcionBreve = $oObj->sDescripcionBreve;
            	$oReview->sKeywords = $oObj->sKeywords;                
                $oReview->sItemType = $oObj->sItemType;
                $oReview->sItemName = $oObj->sItemName;
                $oReview->sItemEventSummary = $oObj->sItemEventSummary;
                $oReview->sItemUrl = $oObj->sItemUrl;
                $oReview->fRating = $oObj->fRating;
                $oReview->sFuenteOriginal = $oObj->sFuenteOriginal;

            	$aReviews[] = Factory::getReviewInstance($oReview);
            }

            return $aReviews;

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

                          r.usuarios_id as iUsuarioId,
                          r.moderado as bModerado,
                          r.publico as bPublico,
                          r.activoComentarios as bActivoComentarios,
                          r.descripcionBreve as sDescripcionBreve,
                          r.keywords as sKeywords,
                          r.itemType as sItemType,
                          r.itemName as sItemName,
                          r.itemEventSummary as sItemEventSummary,
                          r.itemUrl as sItemUrl,
                          r.rating as fRating,
                          r.fuenteOriginal as sFuenteOriginal
                    FROM
                        fichas_abstractas f
                    JOIN
                        reviews r ON r.id = f.id";

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

            $aReviews = array();
            while($oObj = $db->oNextRecord()){
            	$oReview = new stdClass();
            	$oReview->iId = $oObj->iId;
            	$oReview->sTitulo  = $oObj->sTitulo;
            	$oReview->dFecha = $oObj->dFecha;
            	$oReview->bActivo = ($oObj->bActivo == "1") ? true : false;
            	$oReview->sDescripcion = $oObj->sDescripcion;
            	$oReview->iUsuarioId = $oObj->iUsuarioId;
            	$oReview->bModerado = ($oObj->bModerado == "1") ? true:false;
            	$oReview->bPublico = ($oObj->bPublico == "1") ? true:false;
            	$oReview->bActivoComentarios = ($oObj->bActivoComentarios == "1")?true:false;
            	$oReview->sDescripcionBreve = $oObj->sDescripcionBreve;
            	$oReview->sKeywords = $oObj->sKeywords;
                $oReview->sItemType = $oObj->sItemType;
                $oReview->sItemName = $oObj->sItemName;
                $oReview->sItemEventSummary = $oObj->sItemEventSummary;
                $oReview->sItemUrl = $oObj->sItemUrl;
                $oReview->fRating = $oObj->fRating;
                $oReview->sFuenteOriginal = $oObj->sFuenteOriginal;

            	$aReviews[] = Factory::getReviewInstance($oReview);
            }

            return $aReviews;

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
   	 
    public function actualizarCampoArray($objects, $cambios){}  
}