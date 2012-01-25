<?php
/**
 * Persistence Factory
 * @author Rodrigo A. Rio <rodrigorio@netpowermdp.com.ar>
 * @package Persistence
 */


class PersistenceFactory {

    public function __construct() {}

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
    public static function getDocumentoTiposIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (DocumentoTiposMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getCiudadIntermediary(IMYSQL $conn)
    {
  		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (CiudadMySQLIntermediary::getInstance($conn));
        }          
    }
    public static function getProvinciaIntermediary(IMYSQL $conn)
    {
  		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (ProvinciaMySQLIntermediary::getInstance($conn));
        }          
    }
    public static function getPaisIntermediary(IMYSQL $conn)
    {
  		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (PaisMySQLIntermediary::getInstance($conn));
        }          
    }
    public static function getInstitucionIntermediary(IMYSQL $conn)
    {
  		if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
			return (InstitucionMySQLIntermediary::getInstance($conn));
        }          
    }
    public static function getEspecialidadIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (EspecialidadMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getCategoriaIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (CategoriaMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getSeguimientoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (SeguimientoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getArchivoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (ArchivoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getFotoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (FotoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getPracticaIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (PracticaMySQLIntermediary::getInstance($conn));
        }
    }
}