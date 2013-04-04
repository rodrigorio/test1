<?php
 /* Description of class VariableMySQLIntermediary
 *
 * @author Andr�s
 */
class VariableMySQLIntermediary extends VariableIntermediary
{
	private static $instance = null;

	protected function __construct( $conn) {
		parent::__construct($conn);
	}
	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return VariableMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}
	
	
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                        v.id as iId, v.nombre as sNombre, v.tipo as iTipo , v.descripcion as sDescripcion, v.unidad_id as iUnidadId, scv.valor as sValor, v.fechaHora as dFechaHora
                    FROM
                       variables v 
                    JOIN 
                       seguimiento_x_contenido_variables scv
                    ON
                       v.id = scv.variable_id ";
            
            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            $db->query($sSQL);
                                              
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
            
            if(empty($iRecordsTotal)){ return null; }
            
            $aVariables = array();
            while($oObj = $db->oNextRecord()){
            	$oVariable 		= new stdClass();
            	$oVariable->iId 		= $oObj->iId;
            	$oVariable->sNombre	= $oObj->sNombre;
            	$oVariable->iTipo   = $oObj->iTipo;
            	$oVariable->sDescripcion	= $oObj->sDescripcion;
            	$oVariable->sValor = $oObj->sValor;
            	//$oVariable->oUnidad    = SeguimientoController::getInstance()->getUnidadById($oObj->iUnidadId);
            	$oVariable->dFechaHora	= $oObj->dFechaHora;
            	$aVariables[]		= Factory::getVariableInstance($oVariable);
            }
            return $aVariables;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
   public  function insertar($oVariable)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into variables ".
                    " set nombre =".$db->escape($oVariable->getNombre(),true).", " .
			        " tipo =".$db->escape($oVariable->getTipo(),false,MYSQL_TYPE_INT).", ".
			        " descripcion =".$db->escape($oVariable->getDescripcion(),true).", ".
			        " unidad_id =".$db->escape($oVariable->getUnidad()->getId(),false,MYSQL_TYPE_INT).", ".
			        " fechaHora =".$db->escape($oVariable->getFechaHora(),true)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    
	public function actualizar($oVariable)
   {
		try{
			$db = $this->conn;
		
			$sSQL =	" update variables ".
                    " set nombre =".$db->escape($oVariable->getNombre(),true).", " .
			        " tipo =".$db->escape($oVariable->getTipo(),false,MYSQL_TYPE_INT).", ".
			        " descripcion =".$db->escape($oVariable->getDescripcion(),true).", ".
			        " unidad_id =".$db->escape($oVariable->getUnidad()->getId(),false,MYSQL_TYPE_INT).",".
			        " fechaHora =".$db->escape($oVariable->getFechaHora(),true)." ".
					" where id =".$db->escape($oVariable->getId(),false,MYSQL_TYPE_INT)." ";	 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oVariable)
    {
        try{
			if($oVariable->getId() != null){
            	return $this->actualizar($oVariable);
            }else{
				return $this->insertar($oVariable);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }
    
	public function borrar($iVariableId) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from variables where id = '".$iVariableId."'");
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
                        variables v
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
    public function isVariableUsuario($iVariableId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimientos s
                      JOIN 
                      	seguimiento_x_unidad su 
                      ON 	
                      	su.seguimiento_id = s.id
                      JOIN
                         unidades u
                      ON
                         su.unidad_id = u.id
                      JOIN
                      	variables v
                      ON
                         u.id = v.unidad_id                     	
                      WHERE
                        v.id = ".$this->escInt($iVariableId)." AND
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
}