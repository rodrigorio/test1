<?php
class SeguimientoMySQLIntermediary extends SeguimientoIntermediary
{
        private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}

	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return InstitucionMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}


        public final function obtenerSeguimientos($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
            try{
                $db = clone($this->conn);
                $filtro = $this->escapeStringArray($filtro);

                $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                              s.id as iId,
                              s.discapacitados_id as iDiscapacitadoId,
                              s.frecuenciaEncuentros as sFrecuenciaEncuentros,
                              s.diaHorario as sDiaHorario,
                              s.practicas_id as iPracticaId,
                              s.usuarios_id as iUsuarioId,
                              s.antecedentes as sAntecedentes,
                              s.pronostico as sPronostico,
                              s.fechaCreacion as dFechaCreacion,
                              IF(sp.id IS NULL,'scc','pers') AS tipo
                        FROM
                            seguimientos s
                        LEFT JOIN
                            seguimientos_personalizados sp ON sp.id = s.id
                        LEFT JOIN
                            seguimientos_scc sscc ON s.id = sscc.id
                        JOIN
                            usuarios u ON u.id = s.usuarios_id ";

                if(!empty($filtro)){
                    $sSQL .=" WHERE".$this->crearCondicionSimple($filtro);
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

                $aSeguimientos = array();
                while($oObj = $db->oNextRecord()){
                    $oSeguimiento 			= new stdClass();
                    $oSeguimiento->iId 		= $oObj->iId;
                    $oSeguimiento->oDiscapacitado   = SeguimientosController::getInstance()->getDiscapacitadoById($oObj->iDiscapacitadoId);
                    $oSeguimiento->sFrecuenciaEncuentros = $oObj->sFrecuenciaEncuentros;
                    $oSeguimiento->sDiaHorario      = $oObj->sDiaHorario;
                    //$oSeguimiento->oPractica        = SeguimientosController::getInstance()->getPracticaById($oObj->iPracticaId);
                    $oSeguimiento->oUsuario         = SysController::getInstance()->getUsuarioById($oObj->iUsuarioId);
                    $oSeguimiento->sAntecedentes    = $oObj->sAntecedentes;
                    $oSeguimiento->sPronostico      = $oObj->sPronostico;
                    $oSeguimiento->dFechaCreacion   = $oObj->dFechaCreacion;
                    if($oObj->tipo=='scc'){
                        $aSeguimientos[] = Factory::getSeguimientoSCCInstance($oSeguimiento);
                    }else{
                        $aSeguimientos[] = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
                    }
                }

                 return $aSeguimientos;

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
                        seguimientos s 
                    JOIN 
                    	usuarios u ON s.usuarios_id = u.id
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
                          s.id as iId, 
                          s.discapacitados_id as iDiscapacitadoId, 
                          s.frecuenciaEncuentros as sFrecuenciaEncuentros,
                          s.diaHorario as sDiaHorario,
                          s.practicas_id as iPracticaId,
                          s.usuarios_id as iUsuarioId,
                          s.antecedentes as sAntecedentes,
                          s.pronostico as sPronostico,
                          s.fechaCreacion as dFechaCreacion ,
                          s.estado as sEstado
                    FROM
                        seguimientos s 
                        
                    JOIN usuarios u ON u.id = s.usuarios_id
                    JOIN personas p ON p.id = s.discapacitados_id ";
                        

            if(!empty($filtro)){
                $sSQL .=" WHERE ".$this->crearCondicionSimple($filtro);
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

            $aSeguimientos = array();
            while($oObj = $db->oNextRecord()){
            	$oSeguimiento 			= new stdClass();
            	$oSeguimiento->iId 		= $oObj->iId;
            	$oSeguimiento->oDiscapacitado = SeguimientosController::getInstance()->getDiscapacitadoById($oObj->iDiscapacitadoId);
            	$oSeguimiento->sFrecuenciaEncuentros = $oObj->sFrecuenciaEncuentros;
            	$oSeguimiento->sDiaHorario = $oObj->sDiaHorario;
            	$oSeguimiento->oPractica = SeguimientosController::getInstance()->getPracticaById($oObj->iPracticaId);
            	$oSeguimiento->oUsuario = SysController::getInstance()->getUsuarioById($oObj->iUsuarioId);
            	$oSeguimiento->sAntecedentes = $oObj->sAntecedentes;
            	$oSeguimiento->sPronostico = $oObj->sPronostico;
            	$oSeguimiento->dFechaCreacion = $oObj->dFechaCreacion;
            	$oSeguimiento->sEstado = $oObj->sEstado;
            	   	
            	$aSeguimientos[] = Factory::getSeguimientoPersonalizadoInstance($oSeguimiento);
            }

            //si es solo un elemento devuelvo el objeto si hay mas de un elemento o 0 devuelvo el array.
            if(count($aSeguimientos) == 1){
                return $aSeguimientos[0];
            }else{
                return $aSeguimientos;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
     public function actualizar($oSeguimientoPersonalizado)
    {
        try{
			$db = $this->conn;
					
			if($oSeguimientoPersonalizado->getUsuario()!= null){
				$usuarioId = $oSeguimientoPersonalizado->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oSeguimientoPersonalizado->getDiscapacitado()!= null){
				$discapacitadoId = $oSeguimientoPersonalizado->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oSeguimientoPersonalizado->getPractica()!= null){
				$practicaId = $oSeguimientoPersonalizado->getpractica()->getId();
			}else {
				$practicaId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update seguimientos " .
                    " set frecuenciaEncuentros =".$db->escape($oSeguimientoPersonalizado->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oSeguimientoPersonalizado->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oSeguimientoPersonalizado->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oSeguimientoPersonalizado->getPronostico(), true) ." ".
                    " WHERE id = ".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";

			 $db->execSQL($sSQL);

			 // ver esto!!!!!!!!!!!!!!!!!!!!!!!!!!!
			 $diagnosticoPersonalizadoId = null;
			 
             $sSQL =" update seguimiento_personalizados ".
                    " set diagnostico_personalizado_id=".$db->escape($diagnosticoPersonalizadoId,false,MYSQL_TYPE_INT).", ".
					" WHERE id = ".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();


		}catch(Exception $e){
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function actualizarSCC($oSeguimientoSCC)
    {
        try{
			$db = $this->conn;
					
			if($oSeguimientoSCC->getUsuario()!= null){
				$usuarioId = $oSeguimientoSCC->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oSeguimientoSCC->getDiscapacitado()!= null){
				$discapacitadoId = $oSeguimientoSCC->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oSeguimientoSCC->getPractica()!= null){
				$practicaId = $oSeguimientoSCC->getpractica()->getId();
			}else {
				$practicaId = null;
			}
			
            $db->begin_transaction();
            $sSQL = " update seguimientos " .
                    " set frecuenciaEncuentros =".$db->escape($oSeguimientoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oSeguimientoSCC->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oSeguimientoSCC->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oSeguimientoSCC->getPronostico(), true) ." ".
                    " WHERE id = ".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT)." ";

			 $db->execSQL($sSQL);

			 // ver esto!!!!!!!!!!!!!!!!!!!!!!!!!!!
			 $diagnosticoSCCId = null;
			 
             $sSQL =" update seguimiento_scc ".
                    " set diagnostico_scc_id=".$db->escape($diagnosticoSCCId,false,MYSQL_TYPE_INT)." ".
					" WHERE id = ".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT)." ";
			 $db->execSQL($sSQL);
			 $db->commit();


		}catch(Exception $e){
            $db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
    }
    
    public function guardar($oSeguimiento)
    {
        try{
            if($oSeguimiento->getId() != null){
            	if($oSeguimiento->getTipoSeguimiento() == "PERSONALIZADO"){
                    return $this->insertar($oSeguimiento);
                 }else{
            	return $this->actualizarSCC($oSeguimiento);
                 }
            }else{
                 if($oSeguimiento->getTipoSeguimiento() == "PERSONALIZADO"){
                    return $this->insertar($oSeguimiento);
                 }else{
                    return $this->insertarSCC($oSeguimiento);
                 }
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    public function insertar($oSeguimientoPersonalizado)
   {
		try{
                        $db = $this->conn;
					
			if($oSeguimientoPersonalizado->getUsuario()!= null){
				$usuarioId = $oSeguimientoPersonalizado->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
                        if($oSeguimientoPersonalizado->getDiscapacitado()!= null){
				$discapacitadoId = $oSeguimientoPersonalizado->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
                        if($oSeguimientoPersonalizado->getPractica()!= null){
				$practicaId = $oSeguimientoPersonalizado->getpractica()->getId();
			}else {
				$practicaId = null;
			}
			
			
			$db->begin_transaction();
			$sSQL =	" insert into seguimientos ".
                        " set frecuenciaEncuentros =".$db->escape($oSeguimientoPersonalizado->getFrecuenciaEncuentros(),true).", " .
                        " diaHorario =".$db->escape($oSeguimientoPersonalizado->getDiaHorario(),true).", " .
						" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                        " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                        " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                        " antecedentes =".$db->escape($oSeguimientoPersonalizado->getAntecedentes(),true).", " .
                        " pronostico= ".$db->escape($oSeguimientoPersonalizado->getPronostico(), true) ." ";
			
			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			
			$diagnosticoPersonalizadoId = null;
			
			$sSQL =" insert into seguimientos_personalizados set ".
                        " id=".$db->escape($iLastId,false).", " .
                        " diagnostico_personalizado_id=".$db->escape($diagnosticoPersonalizadoId,false,MYSQL_TYPE_INT)." " ;
			$db->execSQL($sSQL);

			$sSQL = "SELECT u.id as iId FROM unidades u WHERE u.porDefecto = 1 ";
            $db->query($sSQL);
            while($oObj = $db->oNextRecord()){
            	$iUnidadId = $oObj->iId;
            }

            $sSQL =" insert into seguimiento_x_unidades set ".
            " unidad_id = ".$db->escape($iUnidadId,false).", " .
            " seguimiento_id = ".$db->escape($iLastId,false,MYSQL_TYPE_INT)." " ;

			$db->execSQL($sSQL);
			$db->commit();
			return true;

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
   }

   
public function insertarSCC($oSeguimientoSCC)
   {
		try{
		    $db = $this->conn;
					
			if($oSeguimientoSCC->getUsuario()!= null){
				$usuarioId = $oSeguimientoSCC->getUsuario()->getId();
			}else {
				$usuarioId = null;
			}
        	if($oSeguimientoSCC->getDiscapacitado()!= null){
				$discapacitadoId = $oSeguimientoSCC->getDiscapacitado()->getId();
			}else {
				$discapacitadoId = null;
			}
            if($oSeguimientoSCC->getPractica()!= null){
				$practicaId = $oSeguimientoSCC->getpractica()->getId();
			}else {
				$practicaId = null;
			}
			
			
			$db->begin_transaction();
			$sSQL =	" insert into seguimientos ".
                    " set frecuenciaEncuentros =".$db->escape($oSeguimientoSCC->getFrecuenciaEncuentros(),true).", " .
                    " diaHorario =".$db->escape($oSeguimientoSCC->getDiaHorario(),true).", " .
					" discapacitados_id =".$db->escape($discapacitadoId,false,MYSQL_TYPE_INT).", ".
                    " usuarios_id =".$db->escape($usuarioId,false,MYSQL_TYPE_INT).", ".
                    " practicas_id =".$db->escape($practicaId,false,MYSQL_TYPE_INT).", ".
                    " antecedentes =".$db->escape($oSeguimientoSCC->getAntecedentes(),true).", " .
                    " pronostico= ".$db->escape($oSeguimientoSCC->getPronostico(), true) ." ";
			
			$db->execSQL($sSQL);
			$iLastId = $db->insert_id();
			
			//ver esto!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			
			$diagnosticoSCCId = null;
			
			$sSQL =" insert into seguimientos_scc set ".
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
    
   public function borrar($oSeguimiento) {
		try{
   			if($oSeguimiento->getTipoSeguimiento() == "PERSONALIZADO"){
                    $this->borrarPersonalizado($oSeguimiento);
                 }else{
            	    $this->borrarSCC($oSeguimiento);
                 }
                 	}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
		}
   	
   
    public function borrarPersonalizado($oSeguimientoPersonalizado) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from seguimientos where id=".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from seguimientos_personalizados where id=".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
public function borrarSCC($oSeguimientoSCC) {
		try{
			
			$db = $this->conn;
			$db->begin_transaction();
			$db->execSQL("delete from seguimientos where id=".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from seguimientos_scc where id=".$db->escape($oSeguimientoSCC->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			$db->rollback_transaction();
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function actualizarCampoArray($objects, $cambios){}
    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
	
}

	
	
