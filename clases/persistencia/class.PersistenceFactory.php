<?php
/**
 * Persistence Factory
 * @author Rodrigo A. Rio <rodrigorio@netpowermdp.com.ar>
 * @package Persistence
 */


class PersistenceFactory {

	public function __construct() {}

	/**
	 * @param mixed $conn
	 * @return GroupIntermediary
     *
     *

	 */

    public static function getAdministradorIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (AdministradorMySQLIntermediary::getInstance($conn));
            }
    }
	public static function getGroupIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.GroupMySQLIntermediary.php");
			return (GroupMySQLIntermediary::getInstance($conn));
		}

	}
	/**
	 * @param mixed $conn
	 * @return P_Generic Intermediary
	 */
	public static function getGenericIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.GenericMySQLIntermediary.php");
			return (GenericMySQLIntermediary::getInstance($conn));
		}

	}

	public static function getUsuariosIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.UsuariosMySQLIntermediary.php");
			return (UsuariosMySQLIntermediary::getInstance($conn));
		}
	}	
	
	public static function getEquiposIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.EquiposMySQLIntermediary.php");
			return (EquiposMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getTipoTorneosIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.TipoTorneosMySQLIntermediary.php");
			return (TipoTorneosMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getTipoDeportesIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.TipoDeportesMySQLIntermediary.php");
			return (TipoDeportesMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getTorneosIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.TorneosMySQLIntermediary.php");
			return (TorneosMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getFixtureIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.FixtureMySQLIntermediary.php");
			return (FixtureMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getFechasIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.FechasMySQLIntermediary.php");
			return (FechasMySQLIntermediary::getInstance($conn));
		}
	}	
	public static function getBoletaIntermediary(IMYSQL  $conn) {
		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			include_once(P_PERSISTENCE_CLASSPATH . "MYSQL/class.BoletaMySQLIntermediary.php");
			return (BoletaMySQLIntermediary::getInstance($conn));
		}
	}	

}
?>
