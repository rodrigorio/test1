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
        return array("NOMBRE_SITIO" => "Urbis Blog - Plantilla",
                     "SUBTITULO_SITIO" => "Este es un ejemplo de la plantilla de blogs de Urbis.",
                     "METATAG_TITLE" => "Titulo depende de Modulo-Controlador-Accion",
                     "METATAG_DESCRIPTION" => "Ble ble bl eb lebleblebisadj aslid",
                     "METATAG_KEYWORDS" => "una palabra, otra palabra, otra palabra mas",
                     "FILE_NAME_LOGO_SITIO" => "logo.jpg");
    }

    /**
     * Retorna un array con elmentos que tienen la estructura ['funcion']=$activo
     * funcion es el key que se genera a partir de modulo_controlador_accion
     * $activo es boolean que indica si una funcion esta activada o no.
     *
     * @param integer $idPerfil - id del perfil del que tiene que obtener permisos.
     *
     * @TODO llamar a la factoria de sistema que construya este array dependiendo el perfil.
     * Reemplazar en el campo 'activo' el 0 y el 1 que esta en la DB por false o true cuando se guarden valores en dicho array.
     */
    public function cargarPermisosPerfil($idPerfil)
    {
        //123probando...
        return array("index_publicaciones_index" => true,
                     "index_publicaciones_redireccion404" => true,
                     "index_publicaciones_sitioOffline" => true,
                     "index_login_index" => true,
                     "index_login_procesar" => true,
                     "admin_index_redireccion404" => true,
                     "index_login_procesar" => true,
                     "index_login_redireccion404" => true,
                     "admin_index_index" => true);
    }

    /**
     * Crea el perfil por defecto para navegar la pagina
     * @return Visitante|null
     */
    public function obtenerPerfilDefecto()
    {
        $oObj = new stdClass();
        return Factory::getVisitanteInstance($oObj);
    }

    /**
     * Obtiene un usuario desde nombre y contraseña, retorna un objeto perfil con el usuario asignado dependiendo el perfil del usuario.
     * @return PerfilAbstract|null
     * @throws Exception si hubo error en la consulta
     */
    public function obtenerUsuarioLogin($sNombreUsuario, $sContrasenia){
        try{
            $oUsuarioIntermediary = PersistenceFactory::getUsuarioIntermediary($this->db);
            return $oUsuarioIntermediary->obtener($sNombreUsuario, $sContrasenia);
        }catch(Exception $e){
            throw $e;
        }
    } 
}