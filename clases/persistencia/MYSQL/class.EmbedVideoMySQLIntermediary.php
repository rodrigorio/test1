<?php

class EmbedVideoMySQLIntermediary extends EmbedVideoIntermediary
{
    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * En el peor de los casos se guarda 1 foto sola, esto es por si se usa alguna libreria javascript
     * en la que se pueden subir muchas fotos al mismo tiempo
     */
    public function guardarEmbedVideosFicha(FichaAbstract $oFicha)
    {
        foreach($oFicha->getEmbedVideos() as $oEmbedVideo){
            if(null !== $oEmbedVideo->getId()){
                return $this->actualizar($oEmbedVideo);
            }else{
                $iId = $oFicha->getId();
                return $this->insertarAsociado($oEmbedVideo, $iId, get_class($oFicha));
            }
        }
    }
       
    public function borrar($aEmbedVideos)
    {
        try{
            $db = $this->conn;

            if(is_array($aEmbedVideos)){
                $db->begin_transaction();
                foreach($aEmbedVideos as $oEmbedVideo){
                    $db->execSQL("DELETE FROM embed_videos WHERE id = ".$this->escInt($oEmbedVideo->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM embed_videos WHERE id = ".$this->escInt($aEmbedVideos->getId()));
                $db->commit();
                return true;
            }

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }  
    }
    
    public function actualizar($oEmbedVideo){
        try{
            $db = $this->conn;

            $iOrden = ($oEmbedVideo->getOrden() == "" || $oEmbedVideo->getOrden() == '0') ? "null" : $oEmbedVideo->getOrden();

            $sSQL = " UPDATE embed_videos SET" .
                    " codigo = ".$this->escStr($oEmbedVideo->getCodigo()).", " .
                    " orden = ".$iOrden.", " .
                    " titulo = ".$this->escStr($oEmbedVideo->getTitulo()).", " .
                    " descripcion = ".$this->escStr($oEmbedVideo->getDescripcion()).", " .
                    " origen = ".$this->escStr($oEmbedVideo->getOrigen())." " .
                    " WHERE id = ".$this->escInt($oEmbedVideo->getId());

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){            
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function insertarAsociado($oEmbedVideo, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $sSQL = " INSERT INTO embed_videos SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "SeguimientoSCC": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
                case "SeguimientoPersonalizado": $sSQL .= "seguimientos_id = ".$iIdItem.", "; break;
            }

            $sSQL .= " codigo = ".$this->escStr($oEmbedVideo->getCodigo()).", " .
                     " titulo = ".$this->escStr($oEmbedVideo->getTitulo()).", " .
                     " descripcion = ".$this->escStr($oEmbedVideo->getDescripcion()).", " .
                     " origen = ".$this->escStr($oEmbedVideo->getOrigen());

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oEmbedVideo->setId($iLastId);

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }  
    
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        v.id as iId, 
                        v.codigo as sCodigo,
                        v.orden as iOrden,
                        v.titulo as sTitulo,
                        v.descripcion as sDescripcion,
                        v.origen as sOrigen
                    FROM
                        embed_videos v ";

            $WHERE = array();

            if(isset($filtro['v.id']) && $filtro['v.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('v.id', $filtro['v.id'], MYSQL_TYPE_INT);
            }            
            if(isset($filtro['v.seguimientos_id']) && $filtro['v.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('v.seguimientos_id', $filtro['v.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['v.fichas_abstractas_id']) && $filtro['v.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('v.fichas_abstractas_id', $filtro['v.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEmbedVideos = array();
            while($oObj = $db->oNextRecord()){

                $oEmbedVideo = new stdClass();
                $oEmbedVideo->iId = $oObj->iId;
                $oEmbedVideo->sCodigo = $oObj->sCodigo;
                $oEmbedVideo->iOrden = $oObj->iOrden;
                $oEmbedVideo->sTitulo = $oObj->sTitulo;
                $oEmbedVideo->sDescripcion = $oObj->sDescripcion;
                $oEmbedVideo->sOrigen = $oObj->sOrigen;

                $aEmbedVideos[] = Factory::getEmbedVideoInstance($oEmbedVideo);
           }

           return $aEmbedVideos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function isEmbedVideoPublicacionUsuario($iEmbedVideoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        embed_videos v
                        JOIN fichas_abstractas fa ON v.fichas_abstractas_id = fa.id
                        LEFT JOIN publicaciones p ON fa.id = p.id
                        LEFT JOIN reviews r ON fa.id = r.id
                      WHERE
                        v.id = ".$this->escInt($iEmbedVideoId)." AND
                        (p.usuarios_id = ".$this->escInt($iUsuarioId)." OR r.usuarios_id = ".$this->escInt($iUsuarioId).")";

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
    
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}