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
	
	public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
	
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
	
	public function existe($filtro){}

	public function actualizarCampoArray($objects, $cambios){}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
	  
} 
?>