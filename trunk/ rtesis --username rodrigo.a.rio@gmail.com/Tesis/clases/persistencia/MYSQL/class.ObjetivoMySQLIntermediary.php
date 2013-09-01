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
	    
    public final function obtenerObjetivosPersonalizados($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion,
                        op.objetivo_personalizado_ejes_id as iEjeId, op.objetivo_relevancias_id as iRelevanciaId, op.estimacion as dEstimacion, op.activo as bActivo, 
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

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by bActivo desc, $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by bActivo desc, iRelevanciaId desc ";
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){

                //esto esta bien asi porque los ejes de los objetivos no tienen sublista de ejes, se asocian solo los ejes hoja
                $oEje = new stdClass();
            	$oEje->iId = $oObj->iEjeId;
            	$oEje->sDescripcion = $oObj->sDescripcionEje;
                $oEje = Factory::getEjeInstance($oEje);

                $oRelevancia = new stdClass();
            	$oRelevancia->iId = $oObj->iRelevanciaId;
            	$oRelevancia->sDescripcion = $oObj->sDescripcionRelevancia;
                $oRelevancia = Factory::getRelevanciaInstance($oRelevancia);

            	$oObjetivo = new stdClass();
            	$oObjetivo->iId = $oObj->iId;
            	$oObjetivo->sDescripcion = $oObj->sDescripcion;
            	$oObjetivo->dEstimacion = $oObj->dEstimacion;
                $oObjetivo->oRelevancia = $oRelevancia;
                $oObjetivo->bActivo = ($oObj->bActivo == "1")?true:false;
                $oObjetivo->oEje = $oEje;
            	
            	$aObjetivos[] = Factory::getObjetivoPersonalizadoInstance($oObjetivo);
            }

            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Solo devuelve objetivos aprendizaje, no tiene en cuenta las asociaciones con seguimientos scc
     */
    public final function obtenerObjetivosAprendizaje($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion,
                        oa.ejes_id as iEjeTematicoId
                    FROM
                       objetivos o 
                    JOIN
                       objetivos_aprendizaje oa ON o.id = oa.id ";
                                             
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){

                $oObjetivo = new stdClass();
                $oObjetivo->iId = $oObj->iId;
                $oObjetivo->sDescripcion = $oObj->sDescripcion;
                $oObjetivo->oEjeTematico = SeguimientosController::getInstance()->getEjeTematicoById($oObj->iEjeTematicoId);
                             
            	$aObjetivos[] = Factory::getObjetivoAprendizajeInstance($oObjetivo);
            }
            
            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Utilizado para obtener los objetivos de aprendizaje asociados a un seguimiento scc
     *
     * seguramente siempre venga el filtro para todos los objetivos de un seguimiento. no hay una vista global en el sistema
     */
    public final function obtenerObjetivosAprendizajeAsociadosSeguimientoScc($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        o.id as iId, o.descripcion as sDescripcion,
                        oa.ejes_id as iEjeTematicoId,
                        sxo.seguimientos_scc_id as iSeguimientoSCCId, sxo.estimacion as dEstimacion, sxo.activo as bActivo,
                        sxo.objetivo_relevancias_id as iRelevanciaId, orr.descripcion as sDescripcionRelevancia
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

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by bActivo desc, $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by bActivo desc, iRelevanciaId desc ";
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aObjetivos = array();
            while($oObj = $db->oNextRecord()){

                $oObjetivo = new stdClass();

                $oObjetivo->oRelevancia = null;
                if(null !== $oObj->iRelevanciaId){
                    $oRelevancia = new stdClass();
                    $oRelevancia->iId = $oObj->iRelevanciaId;
                    $oRelevancia->sDescripcion = $oObj->sDescripcionRelevancia;
                    $oRelevancia = Factory::getRelevanciaInstance($oRelevancia);
                    $oObjetivo->oRelevancia = $oRelevancia;
                }

                $oObjetivo->iId = $oObj->iId;
                $oObjetivo->iSeguimientoSCCId = $oObj->iSeguimientoSCCId;
                $oObjetivo->sDescripcion = $oObj->sDescripcion;
            	$oObjetivo->dEstimacion = $oObj->dEstimacion;
                $oObjetivo->bActivo = ($oObj->bActivo == "1")?true:false;
                $oObjetivo->oEjeTematico = SeguimientosController::getInstance()->getEjeTematicoById($oObj->iEjeTematicoId);

            	$aObjetivos[] = Factory::getObjetivoAprendizajeInstance($oObjetivo);
            }

            return $aObjetivos;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardarObjetivoAprendizaje($oObjetivo)
    {       
        if($oObjetivo->getEje() === null){
            throw new Exception("El objetivo no tiene eje tematico", 0);
        }
        
        try{
            if($oObjetivo->getId() !== null){
                return $this->actualizarObjetivoAprendizaje($oObjetivo);
            }else{
                return $this->insertarObjetivoAprendizaje($oObjetivo);
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
                    " ejes_id =".$this->escInt($oObjetivo->getEje()->getId())." ";

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
                    " set ejes_id =".$this->escInt($oObjetivo->getEje()->getId())." ".
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
    public function guardarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo)
    {
        if($oObjetivo->getObjetivoPersonalizadoEje() === null){
            throw new Exception("El objetivo no tiene eje", 0);
        }

        if($oObjetivo->getRelevancia() === null){
            throw new Exception("El objetivo no tiene relevancia", 0);
        }
        
        try{
            if ($oObjetivo->getId() !== null) {
                return $this->actualizarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo);
            } else {
                return $this->asociarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
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
        if($oObjetivo->getRelevancia() === null){
            throw new Exception("El objetivo no tiene relevancia", 0);
        }
        
        try{
            if($this->existeObjetivoAprendizajeSeguimientoSCC($iSeguimientoSCCId, $oObjetivo->getId())){
                return $this->actualizarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo);
            } else {
                return $this->asociarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function asociarObjetivoPersonalizadoSeguimiento($iSeguimientoPersonalizadoId, $oObjetivo)
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
                    " objetivo_personalizado_ejes_id = ".$this->escInt($oObjetivo->getObjetivoPersonalizadoEje()->getId()).", ".
                    " objetivo_relevancias_id = ".$this->escInt($oObjetivo->getRelevancia()->getId()).", ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion())." ";

            $db->execSQL($sSQL);
            $db->commit();

            $oObjetivo->setId($iLastId);
                        
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
                    " objetivo_personalizado_ejes_id = ".$this->escInt($oObjetivo->getObjetivoPersonalizadoEje()->getId()).", ".
                    " objetivo_relevancias_id = ".$this->escInt($oObjetivo->getRelevancia()->getId()).", ".
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

    public function asociarObjetivoAprendizajeSeguimiento($iSeguimientoSCCId, $oObjetivo)
    {
        try{
            $db = $this->conn;
            $sSQL = " insert into seguimiento_scc_x_objetivo_aprendizaje ".
                    " set seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId).", ".
                    " objetivos_aprendizaje_id = ".$this->escInt($oObjetivo->getId()).", ".
                    " evolucion = ".$this->escFlt($oObjetivo->getEvolucion()).", ".
                    " estimacion = ".$this->escDate($oObjetivo->getEstimacion()).", ".
                    " objetivo_relevancias_id ".$this->escInt($oObjetivo->getRelevancia()->getId());
            
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
                    " objetivo_relevancias_id ".$this->escInt($oObjetivo->getRelevancia()->getId())." ".
                    " WHERE
                        sxo.seguimientos_scc_id = ".$this->escInt($iSeguimientoSCCId)."
                      AND
                        sxo.objetivos_aprendizaje_id = ".$this->escInt($oObjetivo->getId());

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

    public function isObjetivoPersonalizadoUsuario($iObjetivoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                        JOIN seguimientos_personalizados sp ON sp.id = s.id
                        JOIN objetivos_personalizados op ON sp.id = op.seguimientos_personalizados_id
                      WHERE
                        op.id = ".$this->escInt($iObjetivoId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId);

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
    public function isObjetivoAprendizajeUsuario($iObjetivoId,$iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                        JOIN 
                        seguimientos_scc sscc 
                        ON 
                        sscc.id = s.id
                        JOIN
                        seguimiento_scc_x_objetivo_aprendizaje soa
                        ON
                        sscc.id = soa.seguimientos_scc_id
                      WHERE
                        soa.objetivos_aprendizaje_id = ".$this->escInt($iObjetivoId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId);

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
   
    public function actualizarCampoArray($objects, $cambios){}
    public function existe($objects){}
    public function insertar($objects){}
    public function actualizar($objects){}
    public function guardar($objects){}
    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}
	