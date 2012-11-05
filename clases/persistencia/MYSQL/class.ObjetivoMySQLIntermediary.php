<?php

class ObjetivoMySQLIntermediary extends ObjetivoIntermediary
{
    private static $instance = null;

    protected function __construct( $conn){
        parent::__construct($conn);
    }

    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
	
    public function existeObjetivoAprendizaje($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        objetivos o
                    JOIN
                        objetivos_aprendizaje oa 
                    WHERE ".$this->crearCondicionSimple($filtro);

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
    
    public final function obtenerObjetivoPersonalizado($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion,
                        op.objetivo_personalizado_ejes_id as iObjetivoEjeId, op.objetivo_relevancias_id as iObjetivoRelevanciaId, op.evolucion as fEvolucion, op.estimacion as dEstimacion,
                        ope.descripcion as sDescripcionEje, orr.descripcion as sDescripcionRelevancia
                    FROM
                        objetivos o
                    JOIN
                        objetivos_personalizados op ON o.id = op.id
                    JOIN
                        objetivo_personalizado_ejes ope ON ope.id = op.objetivo_personalizado_ejes_id
                    JOIN
                        objetivo_relevancias orr ON orr.id = op.objetivo_relevancias_id ";
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){

                $oObjetivoPersonalizadoEje = new stdClass();
            	$oObjetivoPersonalizadoEje->iId = $oObj->iObjetivoEjeId;
            	$oObjetivoPersonalizadoEje->sDescripcion = $oObj->sDescripcionEje;
                $oObjetivoPersonalizadoEje = Factory::getObjetivoPersonalizadoEjeInstance($oObjetivoPersonalizadoEje);

                $oObjetivoRelevancia = new stdClass();
            	$oObjetivoRelevancia->iId = $oObj->iObjetivoRelevanciaId;
            	$oObjetivoRelevancia->sDescripcion = $oObj->sDescripcionRelevancia;
                $oObjetivoRelevancia = Factory::getObjetivoRelevanciaInstance($oObjetivoRelevancia);

            	$oObjetivo = new stdClass();
            	$oObjetivo->iId = $oObj->iId;
            	$oObjetivo->sDescripcion = $oObj->sDescripcion;
            	$oObjetivo->dEstimacion = $oObj->dEstimacion;
                $oObjetivo->fEvolucion = $oObj->fEvolucion;
                $oObjetivo->oObjetivoRelevancia = $oObjetivoRelevancia;
                $oObjetivo->oObjetivoPersonalizadoEje = $oObjetivoPersonalizadoEje;
            	
            	$aObjetivos[] = Factory::getObjetivoPersonalizadoInstance($oObjetivo);
            }

            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public final function obtenerObjetivoAprendizaje($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion,
                        oa.ejes_id as iEjeTematicoId,
                        sxo.evolucion as fEvolucion, sxo.estimacion as dEstimacion,
                        sxo.objetivo_relevancias_id as iObjetivoRelevanciaId, orr.descripcion as sDescripcionRelevancia 
                    FROM
                       objetivos o 
                    JOIN
                       objetivos_aprendizaje oa ON o.id = oa.id
                    JOIN
                       seguimiento_scc_x_objetivo_aprendizaje sxo ON oa.id = sxo.objetivos_aprendizaje_id
                    JOIN
                       objetivo_relevancias orr ON orr.id = sxo.objetivo_relevancias_id ";
                                             
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){

                $oObjetivoRelevancia = new stdClass();
            	$oObjetivoRelevancia->iId = $oObj->iObjetivoRelevanciaId;
            	$oObjetivoRelevancia->sDescripcion = $oObj->sDescripcionRelevancia;
                $oObjetivoRelevancia = Factory::getObjetivoRelevanciaInstance($oObjetivoRelevancia);
                
            	$oObjetivo = new stdClass();
            	$oObjetivo->iId = $oObj->iId;
            	$oObjetivo->sDescripcion = $oObj->sDescripcion;
            	$oObjetivo->dEstimacion = $oObj->dEstimacion;
                $oObjetivo->fEvolucion = $oObj->fEvolucion;
                $oObjetivo->oObjetivoRelevancia = $oObjetivoRelevancia;
                $oObjetivo->oEjeTematico = SeguimientosController::getInstance()->getEjeTematicoById($oObj->iEjeTematicoId);
            	$aObjetivos[] = Factory::getObjetivoPersonalizadoInstance($oObjetivo);
            }

            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardarObjetivoAprendizaje($oOjetivo)
    {
        if($oObjetivo->getEjeTematico() === null){
            throw new Exception("El objetivo no tiene eje tematico");
        }
        
        try{
            if($oObjetivo->getId() !== null){
                return $this->actualizarObjetivoAprendizaje($oObjetivo);
            }else{
                return $this->insertarObjetivoAprendizaje($oOjetivo);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertarObjetivoAprendizaje($oObjetivo)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " insert into objetivos ".
                    " set descripcion = ".$this->escStr($oObjetivo->getDescripcion())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $sSQL = " insert into objetivos_aprendizaje ".
                    " set id = ".$this->escInt($iLastId).", ".
                    " ejes_id =".$this->escInt($oObjetivo->getEjeTematico()->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            $oObjetivo->setId($iLastId);

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizarObjetivoAprendizaje($oObjetivo)
    {        
        try{
            $db = $this->conn;
            $db->begin_transaction();
            
            $sSQL = " update objetivos ".
                    " set descripcion = ".$this->escStr($oObjetivo->getDescripcion())." ".
                    " where id = ".$this->escInt($oObjetivo->getId())." ";

            $db->execSQL($sSQL);

            $sSQL = " update objetivos_aprendizaje ".
                    " set ejes_id =".$this->escInt($oObjetivo->getEjeTematico()->getId())." ".
                    " where id = ".$this->escInt($oObjetivo->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Objetivo personalizado siempre esta asociado a un seguimiento personalizado.
     * No es que se crean desde el administrador y despues se asocian en una relacion NxN
     * como pasa en los objetivos de aprendizaje.
     */
    public function guardarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oOjetivo)
    {
        if($oObjetivo->getObjetivoPersonalizadoEje() === null){
            throw new Exception("El objetivo no tiene eje");
        }

        if($oObjetivo->getObjetivoRelevancia() === null){
            throw new Exception("El objetivo no tiene relevancia");
        }
        
        try{
            if ($oObjetivo->getId() !== null) {
                return $this->actualizarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo);
            } else {
                return $this->asociarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oOjetivo);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Determina si un objetivo de aprendizaje esta asociado a un seguimiento SCC
     */
    public function existeObjetivoAprendizajeSeguimientoSCC($iSeguimientoSCCId, $iObjetivoId)
    {
    	try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        seguimiento_scc_x_objetivo_aprendizaje sxo
                    WHERE
                        sxo.objetivos_aprendizaje_id = ".$this->escInt($iObjetivoId)."
                    AND
                        sxo.seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId);

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

    /**
     * Asociar un objetivo de aprendizaje a un seguimiento SCC con sus valores de relacion
     * estimacion, evolucion, etc.
     */
    public function guardarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo)
    {
        if($oObjetivo->getObjetivoRelevancia() === null){
            throw new Exception("El objetivo no tiene relevancia");
        }
        
        try{
            if($this->existeObjetivoAprendizajeSeguimientoSCC($iSeguimientoSCCId, $oObjetivo->getId())){
                return $this->actualizarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo);
            } else {
                return $this->asociarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oOjetivo);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function asociarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oOjetivo)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " insert into objetivos ".
                    " set descripcion = ".$this->escStr($oObjetivo->getDescripcion())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $sSQL = " insert into objetivos_personalizados ".
                    " set id = ".$this->escInt($iLastId).", ".
                    " seguimientos_personalizados_id = ".$this->escInt($iSeguimientoPersonalizadoId).", ".
                    " objetivo_personalizado_ejes_id = ".$this->escInt($oOjetivo->getObjetivoPersonalizadoEje()->getId()).", ".
                    " objetivo_relevancias_id = ".$this->escInt($oOjetivo->getObjetivoRelevancia()->getId()).", ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion())." ";

            $db->execSQL($sSQL);
            $db->commit();

            $oOjetivo->setId($iLastId);
                        
            return true;
            
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Antes de llegar a este metodo el controlador deberia confirmar que es un objetivo
     * de un seguimiento que pertenece al usuario que inicio sesion.
     */
    public function actualizarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo)
    {
        try{
            $db = $this->conn;

            $sSQL = " update objetivos ".
                    " set descripcion = ".$this->escStr($oObjetivo->getDescripcion())." ".
                    " where id = ".$this->escInt($oObjetivo->getId())." ";

            $db->execSQL($sSQL);

            $sSQL = " update objetivos_personalizados set ".
                    " objetivo_personalizado_ejes_id = ".$this->escInt($oOjetivo->getObjetivoPersonalizadoEje()->getId()).", ".
                    " objetivo_relevancias_id = ".$this->escInt($oOjetivo->getObjetivoRelevancia()->getId()).", ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion())." ".
                    " where id = ".$this->escInt($oObjetivo->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();
            
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function asociarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oOjetivo)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into seguimiento_scc_x_objetivo_aprendizaje ".
                    " set seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId).", ".
                    " objetivos_aprendizaje_id = ".$this->escInt($oOjetivo->getId()).", ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion()).", ".
                    " objetivo_relevancias_id ".$this->escInt($oOjetivo->getObjetivoRelevancia()->getId());
            
            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo)
    {
        try{
            $db = $this->conn;
            $sSQL = " update seguimiento_scc_x_objetivo_aprendizaje sxo set ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion()).", ".
                    " objetivo_relevancias_id ".$this->escInt($oOjetivo->getObjetivoRelevancia()->getId())." ".
                    " WHERE
                        sxo.seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId)."
                      AND
                        sxo.objetivos_aprendizaje_id = ".$this->escInt($oOjetivo->getId());

            $db->execSQL($sSQL);
            $db->commit();

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
    public function borrar($iObjetivoId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from objetivos where id = '".$iObjetivoId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrarObjetivoAprendizajeDiagnosticoSCC($iSeguimientoSCCId, $iObjetivoId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from seguimiento_scc_x_objetivo_aprendizaje
                          where seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId)."
                          and objetivos_aprendizaje_id = ".$this->escInt($iObjetivoId));
            $db->commit();
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
   
    public function actualizarCampoArray($objects, $cambios){}
    public function existe($objects){}
    public function insertar($objects){}
    public function guardar($objects){}
    public function borrar($objects){}
    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}
	