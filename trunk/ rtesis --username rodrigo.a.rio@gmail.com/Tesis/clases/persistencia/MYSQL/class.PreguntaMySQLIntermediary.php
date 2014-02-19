<?php
/**
 * Description of class CiudadMySQLIntermediary
 */
class PreguntaMySQLIntermediary extends PreguntaIntermediary
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
    /*
     * Este obtener sirve para obtener tambien  los valores de las respuestas multiples choices cuando la entrevista este asociada a un seguimiento, para eso hay que agregar un filtro con el id de seguimiento
     * 
     */
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        p.id as iId, p.descripcion as sDescripcion, p.tipo as sTipoPregunta, psr.respuesta as sRespuesta
                    FROM
                       preguntas p 
                    JOIN
                       preguntas_simples_respuestas psr
                    ON 
                       p.id = psr.preguntas_id";
                       
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }
            /*
             * 
             */

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aPreguntas = array();
            while($oObj = $db->oNextRecord()){
            	$oPregunta 		= new stdClass();
            	$oPregunta->iId 		= $oObj->iId;
            	$oPregunta->sDescripcion	= $oObj->sDescripcion;
            	
            	switch($oObj->sTipoPregunta){
                    case "PreguntaSimple":{
                        $oPregunta = Factory::getPreguntaInstance($oPregunta);
                        $oPregunta->setRespuesta($oObj->sRespuesta);
                        break;
                    }
            	    case "PreguntaMC":{
                        $oPregunta = Factory::getPreguntaMCInstance($oPregunta);
                        $aOpciones = SeguimientosController::getInstance()->getOpcionesByPreguntaId($oObj->iId);
                        $oPregunta->setOpciones($aOpciones);
                        break;
                    }
            	} 
            	$aPreguntas[]		= $oPregunta;
            }
            return $aPreguntas;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
       
    
	public  function insertar($oPregunta, $iEntrevistaId)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into preguntas ".
                    " set descripcion =".$db->escape($oPregunta->getDescripcion(),true).", " .
                    " set tipo =".$db->escape($oPregunta->getTipo(),true)." , ".
			        " entrevistas_id =".$db->escape($iEntrevistaId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oPregunta, $iEntrevistaId)
   {
		try{
			$db = $this->conn;
		        
			$sSQL =	" update preguntas ".
                    " set descripcion =".$db->escape($oPregunta->getDescripcion(),true).", " .
			        " set tipo =".$db->escape($oPregunta->getTipo(),true).", " .
                    " entrevistas_id =".escape($iEntrevistaId(),false,MYSQL_TYPE_INT)." ".
                    " where id =".$db->escape($oPregunta->getId(),false,MYSQL_TYPE_INT)." " ;			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oPregunta)
    {
        try{
			if($oPregunta->getId() != null){
            	return $this->actualizar($oPregunta);
            }else{
				return $this->insertar($oPregunta);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
	public function borrar($oPregunta) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from preguntas where id=".$db->escape($oPregunta->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
	
	public function actualizarCampoArray($objects, $cambios){
		
	}
 	
	public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        preguntas p 
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
}