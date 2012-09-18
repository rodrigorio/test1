<?php
/**
 * Controlador que opera con las clases de modelo para realizar funciones de sistema.
 *
 * @author Matias Velilla
 */
class SysController
{
    /**
     * @var Instancia de DB
     */
    private $db = null;

    private static $instance = null;

    private function __construct(){ }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param DB $db
     */
    public function setDBDriver(DB $db){
        $this->db = $db;
    }

    /**
     * Devuelve un array con los parametros dinamicos asociados al sistema
     */
    public function obtenerParametrosSistema()
    {
    	$oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
    	return $oParametrosIntermediary->obtenerArrayParametrosSistema();
    }

    /**
     * Devuelve un array con los parametros dinamicos correspondientes a un controlador del sistema
     *
     * @param string $grupoControlador Compuesto por "nombreModulo_nombreControlador" es el nombre del controlador
     * @return array con parametros dinamicos correspondientes al controlador, por lo general el controlador actual del request que se este procesando.
     */
    public function obtenerParametrosControlador($grupoControlador)
    {
    	$oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
    	return $oParametrosIntermediary->obtenerArrayParametrosControlador($grupoControlador);
    }

    /**
     * Devuelve un array con los parametros dinamicos correspondientes a un usuario del sistema
     *
     * @param integer $iUsuarioId el id del Usuario
     * @return array con parametros dinamicos correspondientes al usuario, por lo general el usuario que esta logueado en session.
     */
    public function obtenerParametrosUsuario($iUsuarioId)
    {
    	$oParametrosIntermediary = PersistenceFactory::getParametrosIntermediary($this->db);
    	return $oParametrosIntermediary->obtenerArrayParametrosUsuario($iUsuarioId);
    }

    /**
     * Retorna un array con elmentos que tienen la estructura ['funcion'] = $activo
     * funcion es el key que se genera a partir de modulo_controlador_accion
     * $activo es boolean que indica si una funcion esta activada o no.
     *
     * @param integer $idPerfil - id del perfil del que tiene que obtener permisos.
     */
    public function cargarPermisosPerfil($idPerfil)
    {
    	$oPermisosIntermediary = PersistenceFactory::getPermisosIntermediary($this->db);
    	return $oPermisosIntermediary->permisosPorPerfil($idPerfil);
    }

    /**
     * Crea el perfil por defecto para navegar la pagina
     * @return Visitante|null
     */
    public function obtenerPerfilDefecto()
    {
        $oObj = new stdClass();
        $perfil = Factory::getVisitanteInstance($oObj);
        $perfil->iniciarPermisos();
        return $perfil;
    }

    /**
     * Obtiene un usuario desde nombre y contraseña, retorna un objeto perfil con el usuario asignado dependiendo el perfil del usuario.
     * @return array $errorDatos(boolean) $errorSuspendido(boolean) $exito(boolean)
     * @throws Exception si hubo error en la consulta
     */
    public function loginUsuario($tipoDocumento, $nroDocumento, $sContrasenia){
        try{
            $errorDatos = false;
            $errorSuspendido = false;
            $exito = false;

            $filtro = array('p.documento_tipos_id' => $tipoDocumento, 'p.numeroDocumento' => $nroDocumento, 'u.contrasenia' => $sContrasenia);
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $iRecordsTotal = 0;
            $aUsuario = $oUsuarioIntermediary->obtener($filtro,$iRecordsTotal,null,null,null,null);
            if(null !== $aUsuario){
                $oUsuario = $aUsuario[0];
                if(!$oUsuario->isActivo()){
                    $errorSuspendido = true;
                    return array($errorDatos, $errorSuspendido, $exito);
                }
                $exito = $this->iniciarPerfilSessionUsuario($oUsuario);                
                return array($errorDatos, $errorSuspendido, $exito);
            }else{
                $errorDatos = true;
                return array($errorDatos, $errorSuspendido, $exito);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    /**
     * Envuelve a un objeto Usuario en el objeto perfil que le corresponde segun base de datos.
     * Luego levanta los permisos y lo carga en session.
     */
    public function iniciarPerfilSessionUsuario($oUsuario)
    {
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            $oPerfil = $oUsuarioIntermediary->obtenerPerfil($oUsuario);
            $oPerfil->iniciarPermisos();
            SessionAutentificacion::getInstance()->cargarAutentificacion($oPerfil)
                                                 ->realizoLogin(true);
            return true;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function cerrarSesion(){
        try{
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            if(!$perfil->isVisitante()){
                PluginSession::destruirSesion();
            }
        }catch(Exception $e){
            throw $e;
        }
    }
   
    /**
     * Devuelve el estado de privacidad para un dato de un usuario
     * @return string en la DB es un enum puede ser uno de tres 'comunidad' 'privado' 'publico'
     */
    public function getPrivacidadCampo($usuarioId, $nombreCampo){
        $filtro = array('p.usuarios_id' => $usuarioId);
        $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
        return $oUsuarioIntermediary->obtenerPrivacidadCampo($filtro, $nombreCampo);
    }
    /**
     * Devuelve array de privacidad de un usuario en el sistema
     * @return array
     */
    public function getPrivacidad($usuarioId){
        $filtro = array('p.usuarios_id' => $usuarioId);
        $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
        return $oUsuarioIntermediary->obtenerPrivacidad($filtro);
    }

    public function setPrivacidadCampo($usuarioId, $nombreCampo, $valorPrivacidad){
        $filtro = array('p.usuarios_id' => $usuarioId);
        $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
        $oUsuarioIntermediary->updatePrivacidadCampo($filtro, $nombreCampo, $valorPrivacidad);
    }
}