<?php
/**
 * Generic Intermediary
 * @author rodrigo Rio <rodrigorio@netpowermdp.com>
 */

include_once(P_PERSISTENCE_CLASSPATH . "class.GenericIntermediary.php");

/**
 * @package Persistence
 * @see GroupIntermediary
 */
class GenericMySQLIntermediary extends GenericIntermediary{
	static $singletonInstance = 0;

	
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

	/**
	 * @param P_Generic $generic
	 * @param string $table
	 * @return mixed
	 */
	public function _insert(P_Generic $generic,$table) {
		try{
			$db = $this->conn;
			$sSQL =	" insert into $table ". 
					" set idGrupo=".$db->escape($generic->get(0),false,MYSQL_TYPE_INT).", ".
					" nombre=".$db->escape($generic->getCountry()->get(1),true).", " .
					" idUsuarioAdmin=".$db->escape($$generic->get(2),true).", " .
			        " fechaRegistro= ".$db->escape($generic->get(3), true);

			 $db->execSQL($sSQL);
			 $db->commit();
			
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

	/**
	 * @param Group $group
	 * @return mixed
	 */
	public function _update(P_Generic $group) {
		try{
			$db = $this->conn;
			
			//No se modifican los siguientes campos: licence
			$sSQL =	" update groups set " .
					" set idGrupo=".$db->escape($group->getId(),false,MYSQL_TYPE_INT).", ".
					" nombre=".$db->escape($group->getCountry()->getNombre(),true).", " .
					" idUsuarioAdmin=".$db->escape($group->getUsuario(),false,MYSQL_TYPE_INT).", " .
			        " fechaRegistro= ".$db->escape($group->getNombre(), true);
				    " where idGrupo=".$db->escape($group->getId(),false,MYSQL_TYPE_INT);
	
			$db->execSQL($sSQL);
			$db->commit();
			
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

	/**
	 * @param P_Generic $generic
	 * @param string $table
	 * @return mixed
	 */
	public function _delete(P_Generic $generic,$table){
		try{
			$db = $this->conn;
			$db->execSQL("delete from $table where idGrupo=".$db->escape($generic->get(0),false,MYSQL_TYPE_INT));
			$db->commit();
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
	}

	public function _getList($table){
		try{
			$db = $this->conn;
			$sSQL = "select * from $table " ;
			$db->query($sSQL);
			
			while( ($oObject = $db->oNextRecord() ) ){
				$vResult[] = Factory::getGenericInstance($oObject);
			}
			$iRecordsTotal = (int) $db->getDBValue(" select FOUND_ROWS() as list_count ");
			
			$db->commit();
			return $vResult;
		
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}	
	}
}
?>
