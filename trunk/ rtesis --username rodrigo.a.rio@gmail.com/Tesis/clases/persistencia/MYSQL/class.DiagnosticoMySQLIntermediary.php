<?php
class DiagnosticoMySQLIntermediary extends DiagnosticoIntermediary
{
 	const TIPO_DIAGNOSTICO_SCC = "DiagnosticoSCC";
    const TIPO_DIAGNOSTICO_PERSONALIZADO = "DiagnosticoPersonalizado";
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return DiagnosticoMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public final function buscar($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                    	d.id as iId,
                    	d.descripcion as sDescripcion,
                    	dp.codigo as sCodigo,
                    	dscc.area_id as iAreaId,
                    	IF(dp.id IS NULL, '".self::TIPO_DIAGNOSTICO_SCC."', '".self::TIPO_DIAGNOSTICO_PERSONALIZADO."') as tipo
                    FROM
                        diagnosticos d
                    LEFT JOIN
                        diagnosticos_personalizados dp ON dp.id = d.id
                    LEFT JOIN
                        diagnosticos_scc dscc ON d.id = dscc.id ";
              

            $WHERE = array();
			if(isset($filtro['d.id']) && $filtro['u.id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('u.id', $filtro['u.id']);
            }
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aDiagnosticos = array();
            while($oObj = $db->oNextRecord()){

              
            }

            return $aDiagnosticos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        diagnosicos d 
                    JOIN 
                    	diagnosticos u ON s.id = d.id
                    WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

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
    
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                    	d.id as iId,
                    	d.descripcion as sDescripcion,
                    	dp.codigo as sCodigo,
                    	dscc.area_id as iAreaId
                    FROM
                        diagnosticos d
                    LEFT JOIN
                        diagnosticos_personalizados dp ON dp.id = d.id
                    LEFT JOIN
                        diagnosticos_scc dscc ON d.id = dscc.id ";

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oDiagnostico)
    {        
        try{
        	$sDiagnosticoClass = get_class($oDiagnostico);
            if($oDiagnostico->getId() !== null){
                if($sDiagnosticoClass == self::TIPO_DIAGNOSTICO_PERSONALIZADO){
                    return $this->actualizar($oDiagnostico);
                }else{
                    return $this->actualizarSCC($oDiagnostico);
                }
            }else{
                if($sDiagnosticoClass == self::TIPO_DIAGNOSTICO_SCC){
                    return $this->insertar($oDiagnostico);
                }else{
                    return $this->insertarSCC($oDiagnostico);
                }
            }
            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

     public function actualizar($oDiagnosticoPersonalizado) {
        try{
			$db = $this->conn;
					
			if($oDiagnosticoPersonalizado->getUsuario()!= null){
				$usuarioId = $oDiagnosticoPersonalizado->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oDiagnosticoPersonalizado->getDiscapacitado()!= null){
				$discapacitadoId = $oDiagnosticoPersonalizado->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oDiagnosticoPersonalizado->getPractica()!= null){
				$practicaId = $oDiagnosticoPersonalizado->getPractica()->getId();
			}else {
				$practicaId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update diagnosticos " .
                    " set frecuenciaEncuentros =".$db->escape($oDiagnosticoPersonalizado->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oDiagnosticoPersonalizado->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oDiagnosticoPersonalizado->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oDiagnosticoPersonalizado->getPronostico(), true) .", ".
                    " estado= ".$db->escape($oDiagnosticoPersonalizado->getEstado(), true) ." ".
                    " WHERE id = ".$db->escape($oDiagnosticoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);

			
			 $diagnosticoPersonalizadoId = null;
			 
             $sSQL =" update diagnosticos_personalizados ".
                    " set diagnostico_personalizado_id=".$db->escape($diagnosticoPersonalizadoId,false,MYSQL_TYPE_INT)." ".
					" WHERE id = ".$db->escape($oDiagnosticoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();
			 return true;
	
		}catch(Exception $e){
			echo $e->getMessage();
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function actualizarSCC($oDiagnosticoSCC)
    {
        try{
			$db = $this->conn;
					
			if($oDiagnosticoSCC->getUsuario()!= null){
				$usuarioId = $oDiagnosticoSCC->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oDiagnosticoSCC->getDiscapacitado()!= null){
				$discapacitadoId = $oDiagnosticoSCC->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oDiagnosticoSCC->getPractica()!= null){
				$practicaId = $oDiagnosticoSCC->getPractica()->getId();
			}else {
				$practicaId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update diagnosticos " .
                    " set frecuenciaEncuentros =".$db->escape($oDiagnosticoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oDiagnosticoSCC->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oDiagnosticoSCC->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oDiagnosticoSCC->getPronostico(), true) .", ".
            		" estado= ".$db->escape($oDiagnosticoSCC->getEstado(), true) ." ".
                    " WHERE id = ".$db->escape($oDiagnosticoSCC->getId(),false,MYSQL_TYPE_INT)." ";

			 $db->execSQL($sSQL);

			 
			 $diagnosticoSCCId = null;
			 
             $sSQL =" update diagnosticos_scc ".
                    " set diagnostico_scc_id=".$db->escape($diagnosticoSCCId,false,MYSQL_TYPE_INT)." ".
					" WHERE id = ".$db->escape($oDiagnosticoSCC->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();

                         return true;


		}catch(Exception $e){
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
    
    public function insertar($oDiagnosticoPersonalizado)
   {
		try{
            $db = $this->conn;
					
			if($oDiagnosticoPersonalizado->getUsuario()!= null){
				$usuarioId = $oDiagnosticoPersonalizado->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
                        if($oDiagnosticoPersonalizado->getDiscapacitado()!= null){
				$discapacitadoId = $oDiagnosticoPersonalizado->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
                        if($oDiagnosticoPersonalizado->getPractica()!= null){
				$practicaId = $oDiagnosticoPersonalizado->getPractica()->getId();
			}else {
				$practicaId = null;
			}
			
			
			$db->begin_transaction();
			$sSQL =	" insert into diagnosticos ".
                        " set frecuenciaEncuentros =".$db->escape($oDiagnosticoPersonalizado->getFrecuenciaEncuentros(),true).", " .
                        " diaHorario =".$db->escape($oDiagnosticoPersonalizado->getDiaHorario(),true).", " .
						" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                        " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                        " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                        " antecedentes =".$db->escape($oDiagnosticoPersonalizado->getAntecedentes(),true).", " .
                        " pronostico= ".$db->escape($oDiagnosticoPersonalizado->getPronostico(), true) ." ";
			
			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			
			$diagnosticoPersonalizadoId = null;
			
			$sSQL =" insert into diagnosticos_personalizados set ".
                        " id=".$db->escape($iLastId,false).", " .
                        " diagnostico_personalizado_id=".$db->escape($diagnosticoPersonalizadoId,false,MYSQL_TYPE_INT)." " ;
			$db->execSQL($sSQL);

			$sSQL = "SELECT u.id as iId FROM unidades u WHERE u.porDefecto = 1 ";
            $db->query($sSQL);
            while($oObj = $db->oNextRecord()){
            	$iUnidadId = $oObj->iId;
            }

            $sSQL =" insert into diagnostico_x_unidades set ".
            " unidad_id = ".$db->escape($iUnidadId,false).", " .
            " diagnostico_id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT)." " ;

			$db->execSQL($sSQL);
			$db->commit();
			return true;

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
   }

   
	public function insertarSCC($oDiagnosticoSCC)
    {
		try{
		    $db = $this->conn;
					
			if($oDiagnosticoSCC->getUsuario()!= null){
				$usuarioId = $oDiagnosticoSCC->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oDiagnosticoSCC->getDiscapacitado()!= null){
				$discapacitadoId = $oDiagnosticoSCC->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oDiagnosticoSCC->getPractica()!= null){
				$practicaId = $oDiagnosticoSCC->getpractica()->getId();
			}else {
				$practicaId = null;
			}
			
			
			$db->begin_transaction();
			$sSQL =	" insert into diagnosticos ".
                    " set frecuenciaEncuentros =".$db->escape($oDiagnosticoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oDiagnosticoSCC->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oDiagnosticoSCC->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oDiagnosticoSCC->getPronostico(), true) ." ";
			
			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			
			//ver esto!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			
			$diagnosticoSCCId = null;
			
			$sSQL =" insert into diagnosticos_scc set ".
                    " id=".$db->escape($iLastId,false).", " .
                    " diagnostico_scc_id=".$db->escape($diagnosticoSCCId,false,MYSQL_TYPE_INT)." " ;	
		
			$db->execSQL($sSQL);
			 $db->commit();
			 return true;

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
   }
    
   public function borrar($iDiagnosticoId)
   {
        try{
            $db = $this->conn;
            $db->execSQL("delete from diagnosticos where id = '".$iDiagnosticoId."'");
            $db->commit();
            return true;
        }catch(Exception $e){
            return false;
            throw new Exception($e->getMessage(), 0);
        }
    }

 
   	 
    public function actualizarCampoArray($objects, $cambios){} 
}  