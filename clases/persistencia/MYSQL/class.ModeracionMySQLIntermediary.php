<?php

class ModeracionMySQLIntermediary extends ModeracionIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return ComentarioMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    /**
     * polimorfico para todas las entidades del sistema que son moderadas
     * con la clase Moderacion
     */
    public function guardarModeracionEntidad($oObj)
    {
        if(null !== $oObj->getModeracion()){
            $oModeracion = $oObj->getModeracion();
            if(null !== $oModeracion->getId()){
                return $this->actualizar($oModeracion);
            }else{
                $iId = $oObj->getId();
                return $this->insertarAsociado($oModeracion, $iId, get_class($oObj));
            }
        }
    }

    public function insertarAsociado($oModeracion, $iIdItem, $sObjetoAsociado)
    {
        try{
            $db = $this->conn;
            $iIdItem = $this->escInt($iIdItem);

            $sSQL = " INSERT INTO moderaciones SET ";

            switch($sObjetoAsociado){
                case "Publicacion": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Review": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Software": $sSQL .= "fichas_abstractas_id = ".$iIdItem.", "; break;
                case "Institucion": $sSQL .= "instituciones_id = ".$iIdItem.", "; break;
            }

            $sSQL .= " estado = ".$this->escStr($oModeracion->getEstado()).", ".
                     " mensaje = ".$this->escStr($oModeracion->getMensaje())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();
            $db->commit();

            $oModeracion->setId($iLastId);
            $oModeracion->setFecha(date("Y/m/d"));

            return true;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oModeracion)
    {
        try{
            $db = $this->conn;
            
            $sSQL = "UPDATE moderaciones SET ".
            " estado = ".$this->escStr($oModeracion->getEstado()).", " .
            " mensaje = ".$this->escStr($oModeracion->getMensaje())." " .
            " WHERE id = ".$this->escInt($oModeracion->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oModeracion)
    {
        try{
            if($oModeracion->getId() != null){
                return $this->actualizar($oModeracion);
            }else{
                return $this->insertar($oModeracion);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iModeracionId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from moderaciones where id = '".$iModeracionId."'");
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null) {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        m.id as iId,
                        m.fecha as dFecha,
                        m.mensaje as sMensaje,
                        m.estado as sEstado
                    FROM
                        moderaciones m ";

            $WHERE = array();

            if(isset($filtro['m.id']) && $filtro['m.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('m.id', $filtro['m.id'], MYSQL_TYPE_INT);
            }

            if(isset($filtro['m.fichas_abstractas_id']) && $filtro['m.fichas_abstractas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('m.fichas_abstractas_id', $filtro['m.fichas_abstractas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['m.instituciones_id']) && $filtro['m.instituciones_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('m.instituciones_id', $filtro['m.instituciones_id'], MYSQL_TYPE_INT);
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

            $aModeraciones = array();
            while($oObj = $db->oNextRecord()){
                $oModeracion                   = new stdClass();
                $oModeracion->iId              = $oObj->iId;
                $oModeracion->dFecha           = $oObj->dFecha;
                $oModeracion->sMensaje         = $oObj->sMensaje;
                $oModeracion->sEstado          = $oObj->sEstado;

                $aModeraciones[] = Factory::getModeracionInstance($oModeracion);
            }

            return $aModeraciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($oComentario){}
    public function existe($filtro){}
}