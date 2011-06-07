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
    
    public static function getUsuarioIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (UsuarioMySQLIntermediary::getInstance($conn));
            }
    }
        public static function getVisitanteIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (VisitanteMySQLIntermediary::getInstance($conn));
            }
    }

     public static function getUsuarioIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (UsuarioMySQLIntermediary::getInstance($conn));
            }
    }
    public static function getDiscapacitadoIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (DiscapacitadoMySQLIntermediary::getInstance($conn));
            }
    }
    public static function getModeradorIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (ModeradorMySQLIntermediary::getInstance($conn));
            }
    }
    public static function getIntegranteActivoIntermediary(IMYSQL $conn)
    {

            if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (IntegranteActivoMySQLIntermediary::getInstance($conn));
            }
    }
}
?>
