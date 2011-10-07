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
	 * @return PaisMySQLIntermediary
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

        public  function insertar($oEspecialidad)
       {
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

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}

    public function existe($filtro){}

    public function actualizarCampoArray($objects, $cambios){}

}
?>	