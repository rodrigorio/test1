<?php
/** Description of class CategoriaMySQLIntermediary
 *
 */
 
 
class CategoriaMySQLIntermediary extends CategoriaIntermediary
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
    public function existe($filtro){}

    public function actualizar(stdClass $object){}

    public function actualizarCampoArray($objects, $cambios){}

    
    private  function insertar(Categoria $oCategoria)
   {
		try{
			$db = $this->conn;
			$sSQL =	" insert into Categorias ".
                    " set nombre =".$db->escape($oCategoria->getNombre(),true).", " .
                    " id =".$db->escape($oCategoria->getId(),false,MYSQL_TYPE_INT)." ";
                    			 
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