<?php
class SeguimientoSCCMySQLIntermediary extends SeguimientoSCCIntermediary
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
    
	public function actualizar($oSeguimientoSCC)
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

    public function guardar($oSeguimientoSCC)
    {
        try{
			if($oSeguimientoSCC->getId() != null){
            	return $this->actualizar($oSeguimientoSCC);
            }else{
				return $this->insertar($oSeguimientoSCC);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    public function insertar($oSeguimientoSCC)
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

   //el borrado debe ser logico sino vamos a tener problemas, mirar diagramas de base de datos
    public function borrar($oSeguimientoSCC) {
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
	
	public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = $this->conn;
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
                    FROM
                        seguimientos s 
                        
                    JOIN usuarios u ON u.id = s.usuarios_id";
                        

            if(!empty($filtro)){
                $sSQL .="WHERE".$this->crearCondicionSimple($filtro);
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
            	$oSeguimiento->oDiscapacitado = SeguimientoController::getInstance()->getDiscapacitadoById($Obj->iDispacitadoId);
            	$oSeguimiento->sFrecuenciaEncuentros = $oObj->sFrecuenciaEncuentros;
            	$oSeguimiento->sDiaHorario = $oObj->sDiaHorario;
            	$oSeguimiento->oPractica = SeguimientoController::getInstance()->getPracticaById($Obj->iPracticaId);
            	$oSeguimiento->oUsuario = SysController::getInstance()->getUsuarioById($Obj->iUsuarioId);
            	$oSeguimiento->sAntecedentes = $oObj->sAntecedentes;
            	$oSeguimiento->sPronostico = $oObj->sPronostico;
            	   	
            	$aSeguimientos[] = Factory::getSeguimientoInstance($oSeguimiento);
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

	public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
	{
		 try{
            $db = clone($this->conn);
            //$filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        u.id as iId, p.nombre as sNombre, p.apellido as sApellido
                        s.id AS iDId, s.discapacitados_id AS iDiscapacitadoId                    
                        FROM               
                        personas p JOIN usuarios u ON p.id = u.id
                        JOIN seguimientos s ON u.id = s.usuarios_id
                        JOIN discapacitados d ON s.discapacitados_id = d.id
                        JOIN personas pp ON d.id = pp.id";
                        
                        
              
			$WHERE = array();
            if(isset($filtro['p.nombre']) && $filtro['p.nombre']!=""){
                $WHERE[]= $this->crearFiltroTexto('p.nombre', $filtro['p.nombre']);
            }
            if(isset($filtro['p.apellido']) && $filtro['p.apellido']!=""){
                $WHERE[]= $this->crearFiltroSimple('p.apellido', $filtro['p.apellido'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.id']) && $filtro['u.id']!=""){
                $WHERE[]= $this->crearFiltroSimple('u.id', $filtro['u.id'], MYSQL_TYPE_INT);
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

            $aSeguimiento = array();
            while($oObj = $db->oNextRecord()){
                $oSeguimiento 				= new stdClass();
                $oSeguimiento->iId 			= $oObj->iId;
                $oSeguimiento->iDiscapacitadoId = null;
                $oSeguimiento->iUsuarioId = null;
                    
                //creo el seguimiento
                $oSeguimiento = Factory::getSeguimientoSCCInstance($oSeguimiento);

                //creo el objeto discapacitado o lo creo despues
                
                if(null !== $oObj->iDiscapacitadoId){
                
                    $oDiscapacitado = new stdClass();
                    $oDiscapacitado->iId             = $oObj->iDiscapacitadoId;
                    $oSeguimiento->oDiscapacitado = Factory::getDiscapacitadoInstance($oDiscapacitado);
                }
                
               /* else  try {}
                
                catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        	}*/
                           

                if(null !== $oObj->iUsuarioId){
                    $oUsuario = new stdClass();
                    $oUsuario->iId = $oObj->iId;
                    $oSeguimiento->oUsuario = Factory::getUsuarioInstance($oUsuario);
                }
                
                $aSeguimiento[] = $oSeguimiento;
            }

           return $aSeguimiento;

			}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }

	}
	  
} 
