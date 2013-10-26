<?php

class EvolucionMySQLIntermediary extends EvolucionIntermediary
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

    public function guardarEvolucionObjetivo(ObjetivoAbstract $oObjetivo)
    {
        if(null !== $oObjetivo->getEvolucion()){
            foreach($oObjetivo->getEvolucion() as $oEvolucion){
                if(null !== $oEvolucion->getId()){
                    return $this->actualizar($oEvolucion);
                }else{
                    return $this->insertarAsociado($oEvolucion, $oObjetivo);
                }
            }
        }                    
    } 
    
    public function borrar($aEvolucion)
    {
        try{
            $db = $this->conn;

            if(is_array($aEvolucion)){
                $db->begin_transaction();
                foreach($aEvolucion as $oEvolucion){
                    $db->execSQL("DELETE FROM objetivo_evolucion WHERE id = ".$this->escInt($oEvolucion->getId()));
                }
                $db->commit();
                return true;
            }else{
                $db->execSQL("DELETE FROM objetivo_evolucion WHERE id = ".$this->escInt($aEvolucion->getId()));
                $db->commit();
                return true;
            }

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }  
    }

    public function actualizar($oEvolucion){
        try{
            $db = $this->conn;
                        
            $sSQL = " UPDATE objetivo_evolucion SET ".
                    " progreso = ".$this->escInt($oEvolucion->getProgreso()).", ".
                    " comentarios = ".$this->escStr($oEvolucion->getComentarios())." ".
                    " WHERE id = ".$this->escInt($oEvolucion->getId());
                    
            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){            
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarAsociado($oEvolucion, $oObjetivo)
    {
        try{
            $db = $this->conn;
            
            $sSQL = " INSERT INTO objetivo_evolucion SET ";

            if($oObjetivo->isObjetivoPersonalizado()){
                $sSQL .= "objetivos_personalizados_id = ".$this->escInt($oObjetivo->getId()).", ";
            }

            if($oObjetivo->isObjetivoAprendizaje()){
                $sSQL .= "seg_scc_x_obj_apr_obj_id = ".$this->escInt($oObjetivo->getId()).", ";
                $sSQL .= "seg_scc_x_obj_apr_seg_id = ".$this->escInt($oObjetivo->getSeguimientoSCCId()).", ";
            }

            $sSQL .= " progreso = ".$this->escInt($oEvolucion->getProgreso()).", ".
                     " entradas_id = ".$this->escInt($oEvolucion->getEntrada()->getId()).", ".
                     " comentarios = ".$this->escStr($oEvolucion->getComentarios());

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();            
            $db->commit();
                        
            $oEvolucion->setId($iLastId);

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
                        oe.id as iId, oe.progreso as iProgreso, 
                        oe.comentarios as sComentarios, 
                        oe.entradas_id as iEntradaId 
                    FROM
                        objetivo_evolucion oe
                    JOIN
                        entradas e ON oe.entradas_id = e.id ";

            $WHERE = array();
            
            if(isset($filtro['oe.id']) && $filtro['oe.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('oe.id', $filtro['oe.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['oe.objetivos_personalizados_id']) && $filtro['oe.objetivos_personalizados_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('oe.objetivos_personalizados_id', $filtro['oe.objetivos_personalizados_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['oe.seg_scc_x_obj_apr_obj_id']) && $filtro['oe.seg_scc_x_obj_apr_obj_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('oe.seg_scc_x_obj_apr_obj_id', $filtro['oe.seg_scc_x_obj_apr_obj_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['oe.seg_scc_x_obj_apr_seg_id']) && $filtro['oe.seg_scc_x_obj_apr_seg_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('oe.seg_scc_x_obj_apr_seg_id', $filtro['oe.seg_scc_x_obj_apr_seg_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.fecha']) && $filtro['e.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.fecha', $filtro['e.fecha'], MYSQL_TYPE_DATE);
            }
            if(isset($filtro['oe.entradas_id']) && $filtro['oe.entradas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('oe.entradas_id', $filtro['oe.entradas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['toDate']) && $filtro['toDate']!=""){
                $WHERE[] = $this->crearFiltroFecha('e.fecha', null, $filtro['toDate']);
            }
            
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by e.fecha desc ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).", ".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEvolucion = array();
            while($oObj = $db->oNextRecord()){
                $oEvolucion = new stdClass();
                $oEvolucion->iId = $oObj->iId;
                $oEvolucion->iProgreso = $oObj->iProgreso;
                $oEvolucion->iEntradaId = $oObj->iEntradaId;
                $oEvolucion->sComentarios = $oObj->sComentarios;

                $aEvolucion[] = Factory::getEvolucionInstance($oEvolucion);
           }

           return $aEvolucion;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
}
