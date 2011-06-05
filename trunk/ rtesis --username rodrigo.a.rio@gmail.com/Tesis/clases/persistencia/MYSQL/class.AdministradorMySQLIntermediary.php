<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classAdministradorMySQLIntermediary
 *
 * @author Andres
 */
class AdministradorMySQLIntermediary extends AdministradorIntermediary
{
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

     public function getAdministradorById ($id){

    try{
        //este es de ejemplo
			$db = $this->conn;
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
}
?>
