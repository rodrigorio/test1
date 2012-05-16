<?php
class VideoMySQLIntermediary extends VideoIntermediary
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
    
    public function guardarVideosFicha(FichaAbstract $oFicha)
    {
        if(null !== $oVideo->getVideos()){
        	foreach($oFicha->getVideos() as $oVideo){
        		if(null !== $oVideo->getId()){
        	       	return $this->actualizar($oVideo);
		        }else{
		            $iId = $oFicha->getId();
		            return $this->insertarAsociado($oVideo, $iId, get_class($oFicha));
		        }        		        		
        	}
        }                    
    }  
    
	public function borrar($aVideos)
    {
        try{
            $db = $this->conn;

            if(is_array($aVideos)){
                $db->begin_transaction();
                foreach($aVideos as $oVideo){
                    $db->execSQL("DELETE FROM embed_videos WHERE id = ".$this->escInt($oVideo->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM embed_videos WHERE id = ".$this->escInt($aVideos->getId()));
                $db->commit();
                return true;
            }

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }  
    }
    
    public function actualizar($oVideo){
        try{
            $db = $this->conn;
            
            $sSQL = " UPDATE embed_videos SET" .
                    " codigo = ".$this->escInt($oVideo>getCodigo()).", " .
                	" orden = ".$this->escInt($oVideo->getOrden()).", " .
                    " titulo = ".$this->escStr($oVideo->getTitulo()).", " .
                    " descripcion = ".$this->escStr($oVideo->getDescripcion()).", " .
                    " origen = ".$this->escStr($oVideo->getOrigen()).", " .
                    " WHERE id = ".$this->escInt($oVideo->getId());

            $db->execSQL($sSQL);

            return true;

        }catch(Exception $e){            
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function insertarAsociado($oVideo, $iIdItem, $sObjetoAsociado)
    {
    	
    }  
    
 public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        v.id as iVideoId, v.codigo as sVideoCodigo,
                        v.orden as iVideoOrden, v.titulo as sVideoTitulo,
                        v.descripcion as sVideoDescripcion, v.origen as sVideoOrigen
                    FROM
                        embed_videos v ";

            $WHERE = array();
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

            $aVideos = array();
            while($oObj = $db->oNextRecord()){
                $oVideo = new stdClass();
                $oVideo->iId = $oObj->iVideoId;
               $oVideo->sCodigo = $oObj->sVideoCodigo;
               $oVideo->iOrden = $oObj->iVideoOrden;
                $oVideo->sTitulo = $oObj->sVideoTitulo;
                $oVideo->sDescripcion = $oObj->sVideoDescripcion;
                $oVideo->sOrigen = $oObj->sVideoOrigen;

                $aVideos[] = Factory::getVideoInstance($oVideo);
           }

           return $aVideos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}