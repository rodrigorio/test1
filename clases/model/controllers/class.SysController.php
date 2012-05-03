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
     * Devuelve un array con los parametros dinamicos correspondientes al controlador que posee el HttpRequest actual que se solicito.
     *
     * @param string $controladorId Compuesto por "nombreModulo_nombreControlador"
     * @return array con parametros dinamicos correspondientes al controlador actual del request que se este procesando.
     * @TODO llamar a la factoria del sistema que construya un array con el nombre y los valores de los parametros correspondientes a controladorId
     */
    public function obtenerParametrosControlador($controladorId)
    {
        //123probando...
        return array("NOMBRE_SITIO" => "Tesis",
                     "SUBTITULO_SITIO" => "sistema de ....",
                     "METATAG_TITLE" => "Titulo depende de Modulo-Controlador-Accion",
                     "METATAG_DESCRIPTION" => "Ble ble bl eb lebleblebisadj aslid",
                     "METATAG_KEYWORDS" => "una palabra, otra palabra, otra palabra mas",
                     "FILE_NAME_LOGO_SITIO" => "logo.jpg");
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
    	$array = $oPermisosIntermediary->permisosPorPerfil($idPerfil);
        
        if($array == null){
            echo "AGREGAR PERMISOS EN LA BASE DE DATOS, :D => RODRIGO!!!";
        }else{
            return $array;
        }
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
                $oPerfil = $oUsuarioIntermediary->obtenerPerfil($oUsuario);
                $oPerfil->iniciarPermisos();
                SessionAutentificacion::getInstance()->cargarAutentificacion($oPerfil)
                                                     ->realizoLogin(true);
                $exito = true;
                return array($errorDatos, $errorSuspendido, $exito);
            }else{
                $errorDatos = true;
                return array($errorDatos, $errorSuspendido, $exito);
            }
        }catch(Exception $e){
            throw $e;
        }
    }

    public function cerrarSesion(){
        try{
            $perfil = SessionAutentificacion::getInstance()->obtenerIdentificacion();
            if(get_class($perfil) != 'Visitante'){
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