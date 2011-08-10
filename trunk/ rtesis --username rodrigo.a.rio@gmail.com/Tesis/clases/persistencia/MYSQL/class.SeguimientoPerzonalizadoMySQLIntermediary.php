<?php
class SeguimientoPerzonalizadoMySQLIntermediary extends SeguimientoPersonalizadoIntermediary
{
static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return CategoriaMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
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

    public function guardar($oSeguimientoPersonalizado)
    {
        try{
			if($oSeguimientoPersonalizado->getId() != null){
            	return $this->actualizar($oSeguimientoPersonalizado);
            }else{
				return $this->insertar($oSeguimientoPersonalizado);
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
			
			//ver esto!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			
			$diagnosticoPersonalizadoId = null;
			
			$sSQL =" insert into seguimientos_personalizados set ".
                    " id=".$db->escape($iLastId,false).", " .
                    " diagnostico_personalizado_id=".$db->escape($diagnosticoPersonalizadoId,false,MYSQL_TYPE_INT)." " ;	
		
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
    public function borrar($oSeguimientoPersonalizado) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from seguimientos where id=".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT));
            $db->execSQL("delete from seguimientos_personalizados where id=".$db->escape($oSeguimientoPersonalizado->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    } 

?>