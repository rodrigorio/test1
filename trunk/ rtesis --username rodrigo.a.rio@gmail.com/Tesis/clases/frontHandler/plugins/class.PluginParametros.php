<?php
/**
 * Esta clase es un plugin que provee parametros segun la accion en el sistema
 * para cualquier metodo que asi lo requiera
 *
 * COLABORACIONES
 * Los parametros son unicos, pero los valores pueden ser determinados dependiendo el lugar donde se utilizen.
 * Los valores pueden estar asociados a:
 *      - Ciertas entidades como un Usuario (en ese caso los valores dependen del usuario instanciado)
 *      - Un controlador de un modulo
 *      - Al sistema en su totalidad
 *
 * Para todo Parametro entonces se genera una matriz [grupo][key] = valor
 * Donde grupo puede ser 'sistema', 'admin_publicaciones', 'user-123', 'sitio-12323', etc. y key es el nombre del parametro
 * Cuando se utilize el parametro solo se utilizara el nombre del parametro.
 * El valor segun el grupo lo soluciona este plugin.
 *
 * Los valores se buscaran de lo mas especifico a lo mas general. Es decir, cuando se solicite un parametro primero se buscaran
 * valores asociados a las entidades que posean parametros (Usuarios, Sitios, etc)
 * Luego se buscara el valor en los parametros para el controlador utilizado (se extrae desde $request)
 * Si ninguno de los casos anteriores cumple se buscaran valores del parametro generales a todo el sistema.
 *
 * TIPOS
 * Existiran parametros estaticos declarados en archivos y parametros dinamicos almacenados en la DB.
 * De estos ultimos se guardaran copias en $_SESSION (se cargan en el fron controller).
 * En todos los casos se revisaran primeros los valores estaticos, luego los valores de $_SESSION.
 *
 * VALORES
 * Por cuestiones de como se almacenan los valores todos los valores seran strings.
 * Si un parametro es booleano se guarda 1 ó 0. Despues PHP automaticamente toma el 0 como false y el 1 como true.
 *
 * TIP
 * 1) Lo ideal es programar creando parametros estaticos, cuando se termina de programar una funcionalidad
 * se analiza luego que parametro conviene guardarlo en la base de datos para que pueda ser modificado de ser necesario.
 * 2) Los paramatros de entidad siempre son dinamicos, los parametros estaticos unicamente son los de controlador y de sistema.
 *
 *
    'DATABASE_HOST' => '181.168.228.197',
    'DATABASE_DRIVER' => 'IMYSQL',
    'DATABASE_USER' => 'usuariodetest',
    'DATABASE_PASSWORD' => 'usuariodetest1234',
    'DATABASE_NAME' => 'tesis',
    'DATABASE_PORT' => '3306',
    'DATABASE_AUTOCOMMIT' => '0',
 * 
 * @author Matias Velilla
 */
class PluginParametros extends PluginAbstract
{   
    /**
     * Nombre del controlador general para todo el sistema (corresponde con el contenido de los registros de la DB en tabla 'controladores_pagina')
     */
    const GRUPO_SISTEMA = 'sistema';

    private $parametrosEstaticos = array();

    /**
     * @var PluginParametrosDinamicosStrategy
     */
    private $parametrosDinamicosStrategy;

    /**
     * Fueron cargados los parametros dinamicos en el request actual?
     */
    private $parametrosDinamicosCargados = false;

    /**
     * Por defecto construyo el objeto con la estrategia de parametros dinamicos que utilizan Session.
     * El Plugin se instancia en index.php
     */
    public function __construct()
    {        
        $this->setParametrosDinamicosStrategy( new PluginParametrosDinamicosSession() );
    }

    public function routeStartup(HttpRequest $request)
    {
        //Si la session se destruyo entonces cambio la estrategia de como manejar los parametros dinamicos.
        if(Session::isDestroyed()){
            $this->setParametrosDinamicosStrategy( new PluginParametrosDinamicosDB() );
        }
        $this->parametrosDinamicosStrategy->setRequest($request);
              
        $this->setRequest($request) //se setea porque el $request se va a usar en metodos privados de la clase
             ->agregarParametrosEstaticos();
    }

    public function preDispatch(HttpRequest $request)
    {
        //carga parametros dinamicos solo si el plugin de base de datos pudo establecer conexion
        if(PluginConexionDataBase::isConnected()){
            $this->setRequest($request)
                 ->parametrosDinamicosStrategy->cargarParametrosDinamicos();
            $this->parametrosDinamicosCargados(true);
        }
    }

    public function postDispatch(HttpRequest $request)
    {
        //Carga parametros dinamicos si se pudo establecer conexion y si el request todavia no fue despachado.
        if(PluginConexionDataBase::isConnected() && !$request->isDispatched())
        {
            $this->setRequest($request)
                 ->parametrosDinamicosStrategy->cargarParametrosDinamicos();
            $this->parametrosDinamicosCargados(true);
        }
    }

    private function agregarParametrosEstaticos()
    {
        $sistema = array(
                       
                        'DATABASE_HOST' => '24.232.188.102',
                        'DATABASE_DRIVER' => 'IMYSQL',
                        'DATABASE_USER' => 'usuariodetest',
                        'DATABASE_PASSWORD' => 'usuariodetest1234',
                        'DATABASE_NAME' => 'tesis',
                        'DATABASE_PORT' => '3306',
                        'DATABASE_AUTOCOMMIT' => '0',
                        'MULTI_IDIOMA' => '1',
                        'SESSION_NAME' => 'Tesis',
                        'HOME_SITIO_MODULO' => 'index',
                        'HOME_SITIO_CONTROLADOR' => 'index',
                        'HOME_SITIO_ACCION' => 'index',
                        'HOME_SITIO_PATH' => '/',

                        //ojo que los nombres corresponden con el nombre de los archivos y carpetas fisicos
                        'ACTIVO_MODULO_ADMIN' => '1', //este no deberia desactivarse nunca
                        'ACTIVO_MODULO_COMUNIDAD' => '1',
                        'ACTIVO_MODULO_SEGUIMIENTOS' => '1',
                        'ACTIVO_MODULO_INDEX' => '1',

                        'ERROR_DB_REDIRECICON_MODULO' => 'index',
                        'ERROR_DB_REDIRECICON_CONTROLADOR' => 'index',
                        'ERROR_DB_REDIRECICON_ACCION' => 'sitioEnConstruccion',

                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCION_MODULO' => 'comunidad',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCION_CONTROLADOR' => 'datosPersonales',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCION_ACCION' => 'index',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCION_PATH' => '/comunidad/datos-personales',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_MODULO' => 'comunidad',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_ACCION' => 'index',
                        'PERFIL_INTEGRANTEINACTIVO_REDIRECCIONLOGIN_PATH' => '/comunidad/home',

                        'PERFIL_INTEGRANTEACTIVO_REDIRECCION_MODULO' => 'comunidad',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCION_CONTROLADOR' => 'index',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCION_ACCION' => 'index',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCION_PATH' => '/comunidad',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCIONLOGIN_MODULO' => 'comunidad',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCIONLOGIN_ACCION' => 'index',
                        'PERFIL_INTEGRANTEACTIVO_REDIRECCIONLOGIN_PATH' => '/comunidad/home',

                        'PERFIL_MODERADOR_REDIRECCION_MODULO' => 'admin',
                        'PERFIL_MODERADOR_REDIRECCION_CONTROLADOR' => 'index',
                        'PERFIL_MODERADOR_REDIRECCION_ACCION' => 'index',
                        'PERFIL_MODERADOR_REDIRECCION_PATH' => '/admin',
                        'PERFIL_MODERADOR_REDIRECCIONLOGIN_MODULO' => 'admin',
                        'PERFIL_MODERADOR_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                        'PERFIL_MODERADOR_REDIRECCIONLOGIN_ACCION' => 'index',
                        'PERFIL_MODERADOR_REDIRECCIONLOGIN_PATH' => '/admin/home',

                        'PERFIL_ADMINISTRADOR_REDIRECCION_MODULO' => 'admin',
                        'PERFIL_ADMINISTRADOR_REDIRECCION_CONTROLADOR' => 'index',
                        'PERFIL_ADMINISTRADOR_REDIRECCION_ACCION' => 'index',
                        'PERFIL_ADMINISTRADOR_REDIRECCION_PATH' => '/admin',
                        'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_MODULO' => 'admin',
                        'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                        'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_ACCION' => 'index',
                        'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_PATH' => '/admin/home');

        $indexPublicaciones = array('MAXCANT_PUBLICIDADES_COL_IZQ' => '3');

        $this->adjuntarArray(self::GRUPO_SISTEMA, $sistema)
             ->adjuntarArray('index_publicaciones', $indexPublicaciones);
    }

    /**
     * El parametro esta tipeado para reforzar la idea del strategy. Solo objetos de la interfaz, no cualquier objeto.
     * 
     * @param PluginParametrosDinamicosStrategy $strategy
     */
    public function setParametrosDinamicosStrategy($strategy)
    {
        //en lugar de tipear el parametro por php hago la comprobacion yo para que sea una excepcion controlada por el sistema.
        if (!($strategy instanceof PluginParametrosDinamicosStrategy)){
            throw new Exception('Objeto "' . $strategy . '" no es una instancia de PluginParametrosDinamicosStrategy');
        }

        //quito la estrategia anterior si es que habia alguna y seteo la nueva
        $this->parametrosDinamicosStrategy = null;
        $this->parametrosDinamicosStrategy = $strategy;
        return $this;
    }

    private function parametrosDinamicosCargados($flag)
    {
        $this->parametrosDinamicosCargados = (boolean) $flag;
        return $this;
    }

    private function getGrupoControladorParametro()
    {
        $request = $this->getRequest();
        $modulo = $request->getModuleName();
        $controlador = $request->getControllerName();
        if(empty($modulo)||empty($controlador)){
            return "";
        }else{
            return $modulo.'_'.$controlador;
        }
    }

    private function getGrupoUsuarioParametro()
    {
        $grupoUsuario = "";

        if(!Session::isDestroyed()){
            if(null !== SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()){
                $iUsuarioId = SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()->getId();
                if(!empty($iUsuarioId)){
                    $grupoUsuario = 'user-'.$iUsuarioId;
                }
            }
        }

        return $grupoUsuario;
    }

    private function adjuntarArray($grupo, $arrayParams)
    {
        if(!isset($this->parametrosEstaticos[$grupo])){
            $this->parametrosEstaticos[$grupo] = $arrayParams;
        }else{
            //si algun parametro estatico se repite no pisa el valor anterior.
            $this->parametrosEstaticos[$grupo] = $this->parametrosEstaticos[$grupo] + $arrayParams;
        }
        return $this;
    }

    public function imprimirParametrosEstaticos()
    {
        echo "<pre>".print_r($this->parametrosEstaticos)."</pre>"; exit();
    }

    /**
     * Le hice un agregado por una cuestion de practicidad a la hora de usar parametros en los plugins
     * Si se utiliza el parametro soloSistema se buscaran parametros solamente en array de sistema.
     *
     * @param string $key Es el namespace, el 'nombre' del parametro que puede estar asociado al sistema, usuario, etc.
     * @param boolean $soloSistema Poner en true si se necesitan parametros unicamente de sistema.
     */
    public function obtener($key, $soloSistema = false)
    {
        //primero entidades, despues controladores, por ultimo parametros de sistema (controladores y entidades solo si dispatched)
        if(!$this->request->isDispatched() || $soloSistema){
            return $this->obtenerParametroSistema($key);
        }
        
        //solo busca la key en el grupo de parametros de usuario si hay un usuario logueado
        if(!Session::isDestroyed() && null !== SessionAutentificacion::getInstance()->obtenerIdentificacion()->getUsuario()){
            $valor = $this->obtenerParametroUsuario($key); // existe ? -> return valor
            if(null !== $valor){ return $valor; }
        }
        
        $valor = $this->obtenerParametroControlador($key); // existe ? -> return valor
        if(null !== $valor){ return $valor; }

        //no lo encontro en parametros dinamicos devuelvo por defecto el que haya en sistema o null si no lo encontro
        return $this->obtenerParametroSistema($key);
    }

    private function obtenerParametroSistema($key)
    {
        $valor = $this->obtenerParametroEstatico(self::GRUPO_SISTEMA, $key);
        if (null === $valor && $this->parametrosDinamicosCargados)
        {
            $valor = $this->parametrosDinamicosStrategy->obtenerParametroDinamico(self::GRUPO_SISTEMA, $key);
        }
        return $valor;
    }

    /**
     * Los parametros de entidad como un usuario son siempre dinamicos.
     * No tiene sentido guardar copias estaticas si hay Altas, Bajas y Modificaciones de la entidad
     */
    private function obtenerParametroUsuario($key)
    {
        $grupoUsuario = $this->getGrupoUsuarioParametro();
        $valor = null;
        if($this->parametrosDinamicosCargados)
        {
            $valor = $this->parametrosDinamicosStrategy->obtenerParametroDinamico($grupoUsuario, $key);
        }
        return $valor;         
    }
    
    private function obtenerParametroControlador($key)
    {
        $grupoControlador = $this->getGrupoControladorParametro();        
        $valor = $this->obtenerParametroEstatico($grupoControlador, $key);        
        if (null === $valor && $this->parametrosDinamicosCargados)
        {
            $valor = $this->parametrosDinamicosStrategy->obtenerParametroDinamico($grupoControlador, $key);
        }
        return $valor;                
    }
    
    private function obtenerParametroEstatico($grupo, $key)
    {
        if(isset($this->parametrosEstaticos[$grupo][$key])){
            return $this->parametrosEstaticos[$grupo][$key];
        }else{
            return null;
        }
    }  
}
