<?php
/** Description of class EspecialidadMySQLIntermediary
 *
 */
 
 
class EspecialidadMySQLIntermediary extends EspecialidadIntermediary
{
        private static $instance = null;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return EspecialidadMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
	}

    public final function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
            try{
                $db = $this->conn;
                $filtro = $this->escapeStringArray($filtro);

                $sSQL = "SELECT
                            e.id as iId, e.nombre as sNombre, e.descripcion as sDescripcion
                            FROM
                           especialidades e ";
                        if(!empty($filtro)){
                            $sSQL .="WHERE".$this->crearCondicionSimple($filtro);
                        }

                $db->query($sSQL);

                $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

                if(empty($iRecordsTotal)){ return null; }

                $aEspecialidades = array();
                while($oObj = $db->oNextRecord()){
                    $oEspecialidad 		= new stdClass();
                    $oEspecialidad->iId 	= $oObj->iId;
                    $oEspecialidad->sNombre= $oObj->sNombre;
                    $oEspecialidad->sDescripcion= $oObj->sDescripcion;
                    $aEspecialidades[]		= Factory::getEspecialidadInstance($oEspecialidad);
                }

                return $aEspecialidades;
            }catch(Exception $e){
                throw new Exception($e->getMessage(), 0);
            }
	}

    public  function insertar($oEspecialidad){
		try{
			$db = $this->conn;
			$sSQL =	" insert into especialidades ".
                    " set nombre =".$db->escape($oEspecialidad->getNombre(),true).", ".
                    " descripcion =".$db->escape($oEspecialidad->getDescripcion(),true)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}


 public  function actualizar($oEspecialidad)
   {
		try{
			$db = $this->conn;
			$sSQL =	" update especialidades ".
                    " set nombre =".$db->escape($oEspecialidad->getNombre(),true).", " .
                    " descripcion =".$db->escape($oEspecialidad->getDescripcion(),true)." " .
                    " where id =".$db->escape($oEspecialidad->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
			 $db->execSQL($sSQL);
			 $db->commit();
             
		}catch(Exception $e){
			echo $e->getMessage();
			throw new Exception($e->getMessage(), 0);
		}
	}
    public function guardar($oEspecialidad)
    {
        try{
			if($oEspecialidad->getId() != null){
            	return $this->actualizar($oEspecialidad);
            }else{
				return $this->insertar($oEspecialidad);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

    //borra una especialidad
    public function borrar($oEspecialidad) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from especialidades where id=".$db->escape($oEspecialidad->getId(),false,MYSQL_TYPE_INT));
			$db->commit();
			return true;
		}catch(Exception $e){
			echo $e->getMessage();
			throw new Exception($e->getMessage(), 0);
			return false;
		}
	}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}

    public function existe($filtro){}

    public function actualizarCampoArray($objects, $cambios){}

    public function especialidadUsadaPorUsuario($oEspecialidad){
    	try{
	    	$db = $this->conn;
	    	
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
            			u.id as iId
                	FROM
                		usuarios u
            		WHERE u.especialidades_id=".$db->escape($oEspecialidad->getId(),false,MYSQL_TYPE_INT)."";
            $db->query($sSQL);
            
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

			if(empty($iRecordsTotal)){ 
				return false; 
			}
			return true;
	    }catch(Exception $e){
        	throw new Exception($e->getMessage(), 0);
        }	
    }
    public function search($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
	 	try{
            $db = $this->conn;

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                            e.id as iId, e.nombre as sNombre, e.descripcion as sDescripcion
                            FROM
                           especialidades e ";
            $WHERE = array();
           	if(isset($filtro['e.nombre']) && $filtro['e.nombre']!=""){
	           	$WHERE[]= $this->crearFiltroTexto('e.nombre', $filtro['e.nombre']);
           	}
           	if(isset($filtro['e.descripcion']) && $filtro['e.descripcion']!=""){
	           	$WHERE[]= $this->crearFiltroTexto('e.descripcion', $filtro['e.descripcion']);
           	}
           	if(isset($filtro['e.id']) && $filtro['e.id']!=""){
	           	$WHERE[]= $this->crearFiltroSimple('e.id', $filtro['e.id'], MYSQL_TYPE_INT);
           	}
           	
            $sSQL 	= $this->agregarFiltrosConsulta($sSQL, $WHERE);
            
                    
	 		if (isset($sOrderBy) && isset($sOrder)){
				$sSQL .= " order by $sOrderBy $sOrder ";
			}
			if ($iIniLimit!==null && $iRecordCount!==null){
				$sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
			}
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");
			if(empty($iRecordsTotal)){ return null; }

			$aEspecialidades = array();
			while($oObj = $db->oNextRecord()){
            	$oEspecialidad 		= new stdClass();
                $oEspecialidad->iId 	= $oObj->iId;
                $oEspecialidad->sNombre= $oObj->sNombre;
                $oEspecialidad->sDescripcion= $oObj->sDescripcion;
                $aEspecialidades[]		= Factory::getEspecialidadInstance($oEspecialidad);
            }

			return $aEspecialidades;
        }catch(Exception $e){
           return null;
            throw new Exception($e->getMessage(), 0);
        }
    }
}
?>	