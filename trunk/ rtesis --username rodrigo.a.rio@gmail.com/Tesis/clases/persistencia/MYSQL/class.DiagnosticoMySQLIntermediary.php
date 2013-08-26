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
    *
    */
   public final function obtenerSCC($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount){
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS			
                    	d.id AS iId,
                    	d.descripcion AS sDescripcion                  	
                    FROM
                        diagnosticos d
                    JOIN
                        diagnosticos_scc dscc ON d.id = dscc.id 
                    JOIN
                        seguimientos_scc s ON s.diagnosticos_scc_id = dscc.id
                     ";
            
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
            $iRecordsTotal = (int)$db->getDBValue("select FOUND_ROWS() as list_count");
            if(empty($iRecordsTotal)){ return null; }
            
            $aEjesTematicos = array();
            $oDiagnostico = null;
            while($oObj = $db->oNextRecord()){
            	$oDiagnostico = new stdClass();
            	$oDiagnostico->iId = $oObj->iId;
                $oDiagnostico->sDescripcion = $oObj->sDescripcion;
                $oDiagnostico = Factory::getDiagnosticoSCCInstance($oDiagnostico);
            }

            if(null !== $oDiagnostico){
            	$filtro = array('d.id' => $oDiagnostico->getId());
            	$oDiagnostico->setEjesTematicos($this->obtenerEjesXDiagnostico($filtro,null,null,null,null,null));
            }

            return $oDiagnostico;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
	
    public final function obtenerPersonalizado($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount)
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
                        seguimientos_personalizados s ON s.diagnosticos_personalizado_id = dp.id ";
            
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
            $db->begin_transaction();

            $sSQL = " update diagnosticos " .
                    " set descripcion = ".$this->escStr($oDiagnosticoSCC->getDescripcion())." ".
                    " WHERE id = ".$this->escInt($oDiagnosticoSCC->getId())." ";                        
            $db->execSQL($sSQL);

            SeguimientosController::getInstance()->guardarEstadoInicial($oDiagnosticoSCC);
            
            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw $e;
        }
    }
    
    public function insertar($oDiagnosticoPersonalizado)
    {
        try{
            $db = $this->conn;
					
            $db->begin_transaction();

            $sSQL = " insert into diagnosticos ".
                    " set descripcion = ".$this->escStr($oDiagnosticoPersonalizado->getDescripcion())." ";
			
            $db->execSQL($sSQL);

            $iLastId = $db->insert_id();
			
            $sSQL = " insert into diagnosticos_personalizado set ".
                    " id = ".$this->escInt($iLastId).", ".
                    " codigo = ".$this->escStr($oDiagnosticoPersonalizado->getCodigo())." ";

            $db->execSQL($sSQL);						
            $db->commit();

            $oDiagnosticoPersonalizado->setId($iLastId);

            return $iLastId;
        }catch(Exception $e){
            $db->rollback_transaction();
            $oDiagnosticoPersonalizado->setId(null);
            throw new Exception($e->getMessage(), 0);
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
                    " id = ".$this->escInt($iLastId)." ";

            $db->execSQL($sSQL);
            $db->commit();

            return $iLastId;

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
        
    private function obtenerEjesXDiagnostico($filtro, $iRecordsTotal, $sOrderBy, $sOrder , $iIniLimit , $iRecordCount)
    {
   		try{
            $db = clone($this->conn);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                    	e.id AS iId,
                    	e.descripcion as sDescripcion,
                    	e.contenidos as sContenidos,
                    	e.areas_id as iAreaId,              
                    	dxe.ejes_id AS iId, 
                    	dxe.estadoInicial AS sEstadoInicial                    	
                    FROM
                        diagnosticos d
                    JOIN
                        diagnosticos_scc dscc ON d.id = dscc.id 
                    JOIN
                        diagnosticos_scc_x_ejes	dxe ON dscc.id = diagnosticos_scc_id 
                    JOIN 
                    	ejes e ON e.id = dxe.ejes_id ";
            
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
			$vEjesTematicos = array();
            $oEjeTematico = null;
            while($oObj = $db->oNextRecord()){
               	$oEjeTematico = new stdClass();
                $oEjeTematico->iId = $oObj->iId;
                $oEjeTematico->sDescripcion = $oObj->sDescripcion;
                $oEjeTematico->oArea = SeguimientosController::getInstance()->getAreaById($oObj->iAreaId);
                $oEjeTematico->sContenidos = $oObj->sContenidos;
                $oEjeTematico = Factory::getEjeTematicoInstance($oEjeTematico);
                $oEjeTematico->setEstadoInicial($oObj->sEstadoInicial);            	
                $vEjesTematicos[] = $oEjeTematico; 
            }
            
            return $vEjesTematicos;
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function isDiagnosticoUsuario($iDiagnosticoId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        diagnosticos d
                        LEFT JOIN seguimientos_personalizados sp ON d.id = sp.diagnosticos_personalizado_id
                        LEFT JOIN seguimientos_scc sc ON d.id = sc.diagnosticos_scc_id
                        JOIN seguimientos s
                            ON s.id = CASE
                                WHEN ISNULL(sc.id) THEN sp.id
                                WHEN ISNULL(sp.id) THEN sc.id
                            END
                        WHERE
                            d.id = ".$this->escInt($iDiagnosticoId)." AND
                            s.usuarios_id = ".$this->escInt($iUsuarioId)." ";

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
    
    public function actualizarCampoArray($objects, $cambios){}
    public function existe($filtro){}
    
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
    	$oDiagnostico = $this->obtenerSCC($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
    	if ($oDiagnostico == null) {
    		$oDiagnostico = $this->obtenerPersonalizado($filtro, $iRecordsTotal, $sOrderBy, $sOrder, $iIniLimit, $iRecordCount);
    	}
    	return $oDiagnostico;
    }
}  