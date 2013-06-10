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
    public static function getPermisosIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(PermisosMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getPublicacionIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(PublicacionMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getEmbedVideoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(EmbedVideoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getComentarioIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (ComentarioMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getDiagnosticoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (DiagnosticoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getModeracionIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (ModeracionMySQLIntermediary::getInstance($conn));
        }
    }
    
    public static function getNivelIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (NivelMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getAreaIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (AreaMySQLIntermediary::getInstance($conn));
        }
    }    
    public static function getCicloIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (CicloMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getEjeTematicoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(EjeTematicoMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getObjetivoIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (ObjetivoMySQLIntermediary::getInstance($conn));
        }
    }

    public static function getSoftwareIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (SoftwareMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getUnidadIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (UnidadMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getVariableIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (VariableMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getModalidadIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return (ModalidadMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getParametrosIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(ParametrosMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getControladorPaginaIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(ControladorPaginaMySQLIntermediary::getInstance($conn));
        }
    }    
    public static function getDenunciaIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(DenunciaMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getInvitacionIntermediary(IMYSQL $conn)
    {
        if( ($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(InvitacionMySQLIntermediary::getInstance($conn));
        }
    }
    public static function getEntradaIntermediary(IMYSQL $conn)
    {
        if(($conn instanceof MySQL) || ($conn instanceof IMySQL)){
            return(EntradaMySQLIntermediary::getInstance($conn));
        }
    }    
}