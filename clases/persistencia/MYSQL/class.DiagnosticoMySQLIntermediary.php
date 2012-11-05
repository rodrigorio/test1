<?php
class DiagnosticoMySQLIntermediary extends DiagnosticoIntermediary
{
    const TIPO_DIAGNOSTICO_SCC = "DiagnosticoSCC";
    const TIPO_DIAGNOSTICO_PERSONALIZADO = "DiagnosticoPersonalizado";

    private static $instance = null;

    protected function __construct( $conn){
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return DiagnosticoMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }
    
   /**
    * Siempre que se obtiene, se devuelve como maximo un diagnostico porque
    * en las vistas no hay listado de diagnostico, es solo uno por seguimiento.
    * 
    * Este obtener por lo tanto no devuelve un array sino un unico objeto
    */
   public final function obtenerSCC($iSeguimientoId){
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS			
                    	d.id AS iId,
                    	d.descripcion AS sDescripcion, dxe.ejes_id AS iEjeId, dxe.estadoInicial AS sEstadoInicial                    	
                    FROM
                        diagnosticos d
                    JOIN
                        diagnosticos_scc dscc ON d.id = dscc.id 
                    JOIN
                        seguimientos_scc s ON s.diagnosticos_scc_id = dscc.id
                    JOIN
                        diagnosticos_scc_x_ejes	dxe ON dscc.id = dxe.ejes_id
                    WHERE s.id = ".$this->escInt($iSeguimientoId)." limit 1 ";
            
            $db->query($sSQL);
            $iRecordsTotal = (int)$db->getDBValue("select FOUND_ROWS() as list_count");
            if(empty($iRecordsTotal)){ return null; }
            
            $aEjesTematicos = array();
            $oDiagnostico = null;
            while($oObj = $db->oNextRecord()){
            	if($oDiagnostico === nul){
                    $oDiagnostico = new stdClass();
                    $oDiagnostico->iId = $oObj->iId;
                    $oDiagnostico->sDescripcion = $oObj->sDescripcion;
                    $oDiagnostico = Factory::getDiagnosticoSCCInstance($oDiagnostico);
            	}            	
            	
            	$oEjeTematico = SeguimientosController::getInstance()->getEjeTematicoById($oObj->iEjeId);
                $oEjeTematico->setEstadoInicial($oObj->sEstadoInicial);            	
                $aEjesTematicos[] = $oEjeTematico;               
            }

            if(null !== $oDiagnostico){
                $oDiagnostico->setEjesTematicos($aEjesTematicos);
            }
                                   
            return $oDiagnostico;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
    public final function obtenerPersonalizado($iSeguimientoId)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                    	d.id as iId,
                    	d.descripcion as sDescripcion,
                    	dp.codigo as sCodigo
                    FROM
                        diagnosticos d
                    JOIN
                        diagnosticos_personalizado dp ON d.id = dp.id 
                    JOIN
                        seguimientos_personalizados s ON s.diagnosticos_personalizado_id = dp.id
                    WHERE
                        s.id = ".$this->escInt($iSeguimientoId)." limit 1 ";
            
            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $oDiagnostico = null;
            while($oObj = $db->oNextRecord()){
            	$oDiagnostico = new stdClass();
                $oDiagnostico->iId = $oObj->iId;
                $oDiagnostico->sDescripcion = $oObj->sDescripcion;
                $oDiagnostico->sCodigo = $oObj->sCodigo;
                $oDiagnostico = Factory::getDiagnosticoPersonalizadoInstance($oDiagnostico);
            }
            
            return $oDiagnostico;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function guardar($oDiagnostico)
    {        
        try{
            if($oDiagnostico->getId() !== null){
                if($oDiagnostico->isDiagnosticoPersonalizado()){
                    return $this->actualizar($oDiagnostico);
                }else{
                    return $this->actualizarSCC($oDiagnostico);
                }
            }else{
                if($oDiagnostico->isDiagnosticoPersonalizado()){
                    return $this->insertar($oDiagnostico);
                }else{
                    return $this->insertarSCC($oDiagnostico);
                }
            }            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oDiagnosticoPersonalizado)
    {
        try{
            $db = $this->conn;
											
            $db->begin_transaction();

            $sSQL = " update diagnosticos " .
                    " set descripcion = ".$this->escStr($oDiagnosticoPersonalizado->getDescripcion())." ".
                    " WHERE id = ".$this->escInt($oDiagnosticoPersonalizado->getId())." ";

            $db->execSQL($sSQL);
				 
            $sSQL = " update diagnosticos_personalizado ".
                    " set codigo = ".$this->escStr($oDiagnosticoPersonalizado->getCodigo())." ".
                    " WHERE id = ".$this->escInt($oDiagnosticoPersonalizado->getId())." ";
             
            $db->execSQL($sSQL);
            $db->commit();
            return true;
	
        }catch(Exception $e){
            $db->rollback_transaction();
            throw $e;
        }
    }

    /**
     * Los ejes se guardan en el mysql de ejes.
     */
    public function actualizarSCC($oDiagnosticoSCC)
    {
        try{
            $db = $this->conn;

            $sSQL = " update diagnosticos " .
                    " set descripcion = ".$this->escStr($oDiagnosticoSCC->getDescripcion())." ".
                    " WHERE id = ".$db->escInt($oDiagnosticoSCC->getId())." ";
             
            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw $e;
        }
    }
    
    public function insertar($oDiagnosticoPersonalizado)
    {
        try{
            $db = $this->conn;
					
            $db->begin_transaction();

            $sSQL = " insert into diagnosticos ".
                    " set descripcion = ".$db->escStr($oDiagnosticoPersonalizado->getDescripcion())." ";
			
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
			
            $sSQL = " insert into diagnosticos_personalizado set ".
                    " id = ".$this->escInt($iLastId).", ".
                    " codigo = ".$this->escStr($oDiagnosticoPersonalizado->getCodigo())." ";

            $db->execSQL($sSQL);
						
            $db->commit();

            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw $e;
        }
    }
   
    public function insertarSCC($oDiagnosticoSCC)
    {
        try{
            $db = $this->conn;

            $db->begin_transaction();

            $sSQL = " insert into diagnosticos ".
                    " set descripcion = ".$this->escStr($oDiagnosticoSCC->getDescripcion())." ";

            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();

            $sSQL = " insert into diagnosticos_scc set ".
                    " id = ".$db->escInt($iLastId)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
   }

   /**
    * Ojo con este porque en realidad todo seguimiento debe tener asociado los registros en db
    * correspondientes al diagnostico.
    *
    * El diagnostico se borra cuando se borra el seguimiento.
    *
    * No puede haber borrado en cascada entre las tablas de seguimiento y diagnostico
    */
   public function borrar($iDiagnosticoId)
   {
        try{
            $db = $this->conn;
            $db->execSQL("delete from diagnosticos where id = '".$iDiagnosticoId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    	 
    public function actualizarCampoArray($objects, $cambios){}
    public function existe($filtro){}
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}  