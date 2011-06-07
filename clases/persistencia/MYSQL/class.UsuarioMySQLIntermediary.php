<?php


/**
 * Description of classUsuarioMySQLIntermediary
 *
 * @author Andres
 */
class classUsuarioMySQLIntermediary extends UsuarioIntermediary
{ static $singletonInstance = 0;


	protected function __construct( $conn) {
		parent::__construct($conn);
	}


	/**
	 * Singleton
	 *
	 * @param mixed $conn
	 * @return GroupMySQLIntermediary
	 */
	public static function &getInstance(IMYSQL $conn) {
		if (!self::$singletonInstance){
			$sClassName = __CLASS__;
			self::$singletonInstance = new $sClassName($conn);
		}
		return(self::$singletonInstance);
	}

   public function insert($sitioWeb,$especialidades_id,$perfiles_id,$contraseña,$fechaAlta) {
		try{
			$db = $this->conn;
			$sSQL =	" insert into usuarios ".
                    " sitioWeb=".$db->escape($nombre,true).", " .
					" especialidades_id =".$db->escape($especialidades_id,false,MYSQL_TYPE_INT).", ".
                    " perfiles_id =".$db->escape($especialidades_id,false,MYSQL_TYPE_INT).", ".
					" contrasenia=".$db->escape($contrasenia,true).", " .
			        " fechaAlta= ".$db->escape($fechaAlta, false,MYSQL_TYPE_DATE);

			 $db->execSQL($sSQL);
			 $db->commit();

             //aca ahy que hacer otra insecion en la tabla personas

		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}
}
?>
