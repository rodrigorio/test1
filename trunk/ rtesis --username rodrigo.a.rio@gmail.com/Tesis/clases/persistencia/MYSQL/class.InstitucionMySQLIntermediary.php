<?php
/**
 * Description of class InstitucionMySQLIntermediary
 *
 *
 */
class InstitucionMySQLIntermediaryMySQLIntermediary extends InstitucionIntermediary
{
     static $singletonInstance = 0;


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
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}
    public function existe($filtro){}

    public function actualizar(stdClass $object){}

    public function actualizarCampoArray($objects, $cambios){}

    
    private  function insertar(Institucion $oInstitucion)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into instituciones ".
                    " set nombre =".$db->escape($oInstitucion->getNombre(),true).", " .
                    " id =".$db->escape($oInstitucion->getId(),false,MYSQL_TYPE_INT).", ".
                    " ciudades_id =".$db->escape($oInstitucion->getCiudadId(),false,MYSQL_TYPE_INT)." ";
			 
			 $db->execSQL($sSQL);
			 $db->commit();

             
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
    

    public function guardar(stdClass $object){}

    public function borrar($objects){}

    public function buscar($args, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
}
?>	