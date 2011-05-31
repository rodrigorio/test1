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
 * @author Matias Velilla
 */
class PluginParametros extends PluginAbstract{

    /**
     * Cantidad de segundos antes de que expiren los parametros dinamicos
     */
    const SEGUNDOS_EXPIRACION_CONTROLADORES = 600;
    const SEGUNDOS_EXPIRACION_SISTEMA = 600;
    
    private $parametrosEstaticos = array();
    /**
     * los parametros dinamicos utilizan "espacio en session" (SessionNamespace)
     */
    private $parametrosDinamicos;

    public function __construct()
    {
        //inicio el namespace de parametros dinamicos en session
        $this->parametrosDinamicos = new SessionNamespace('parametrosDinamicos');
    }

    public function routeStartup(HttpRequest $request)
    {
        //se setea porque el $request se va a usar en metodos privados de la clase
        $this->setRequest($request);

        //primero se extraen los parametros estaticos seteados en /sitio/.
        $this->cargarParametrosEstaticosSitio()
             ->agregarParametrosEstaticos();
    }

    public function preDispatch(HttpRequest $request)
    {
        $this->setRequest($request);
        $this->cargarParametrosDinamicos();
    }

    public function postDispatch(HttpRequest $request)
    {
        if(!$request->isDispatched())
        {
            $this->setRequest($request);
            $this->cargarParametrosDinamicos();
        }
    }

    private function getGrupoParametro()
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

    private function cargarParametrosDinamicos()
    {
        //En sistemas donde el usuario pueda setear sus parametros, aca hay que cargar tambien los valores de los parametros de usuario.
        $controladorId = $this->getGrupoParametro();

        if(!isset($this->parametrosDinamicos->$controladorId))
        {
            $this->parametrosDinamicos->$controladorId = SysController::getInstance()->obtenerParametrosControlador($controladorId);
            if(!empty($this->parametrosDinamicos->$controladorId)){
                //luego de $seconds segundos se borran los parametros forzando a que se vuelvan a buscar en DB.
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_CONTROLADORES, $controladorId);
            }
        }
        if(!isset($this->parametrosDinamicos->sistema))
        {
            $this->parametrosDinamicos->sistema = SysController::getInstance()->obtenerParametrosControlador('sistema');
            if(!empty($this->parametrosDinamicos->sistema)){
                $this->parametrosDinamicos->setExpirationSeconds(self::SEGUNDOS_EXPIRACION_SISTEMA, 'sistema');
            }
        }
    }

    private function cargarParametrosEstaticosSitio()
    {
        $parametrosSitio = new ParametrosSitio();
        $this->parametrosEstaticos = $parametrosSitio->getParametrosSitio();
        return $this;
    }

    private function agregarParametrosEstaticos()
    {
        $sistema = array('DATABASE_HOST' => 'localhost',
                         'DATABASE_DRIVER' => 'IMYSQL',
                         'DATABASE_USER' => 'root',
                         'DATABASE_PASSWORD' => '',
                         'DATABASE_NAME' => 'tesis',
                         'DATABASE_PORT' => '3306',
                         'DATABASE_AUTOCOMMIT' => '0',

                         'MULTI_IDIOMA' => '1',
                         'SESSION_NAME' => 'tesis',
                         'HOME_SITIO_MODULO' => 'index',
                         'HOME_SITIO_CONTROLADOR' => 'index',
                         'HOME_SITIO_ACCION' => 'index',

                         'ACTIVO_MODULO_INDEX' => '1',
                         'ACTIVO_MODULO_ADMIN' => '1', //este no deberia desactivarse nunca

                         'ERROR_DB_REDIRECICON_MODULO' => 'index',
                         'ERROR_DB_REDIRECICON_CONTROLADOR' => 'index',
                         'ERROR_DB_REDIRECICON_ACCION' => 'sitioEnConstruccion',

                         'PERFIL_BLOGGER_REDIRECCION401_MODULO' => 'index',
                         'PERFIL_BLOGGER_REDIRECCION401_CONTROLADOR' => 'publicaciones',
                         'PERFIL_BLOGGER_REDIRECCION401_ACCION' => 'index',
                         'PERFIL_BLOGGER_REDIRECCIONLOGIN_MODULO' => 'admin',
                         'PERFIL_BLOGGER_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                         'PERFIL_BLOGGER_REDIRECCIONLOGIN_ACCION' => 'index',

                         'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_MODULO' => 'admin',
                         'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_CONTROLADOR' => 'index',
                         'PERFIL_ADMINISTRADOR_REDIRECCIONLOGIN_ACCION' => 'index');

        $indexPublicaciones = array('MAXCANT_PUBLICIDADES_COL_IZQ' => '3');

        $this->adjuntarArray('sistema', $sistema);
        $this->adjuntarArray('index_publicaciones', $indexPublicaciones);      
    }

    private function adjuntarArray($grupo, $arrayParams)
    {
        if(!isset($this->parametrosEstaticos[$grupo])){
            $this->parametrosEstaticos[$grupo] = $arrayParams;
        }else{
            //si algun parametro estatico se repite no pisa el valor anterior.
            $this->parametrosEstaticos[$grupo] = $this->parametrosEstaticos[$grupo] + $arrayParams;
        }
    }

    public function imprimirParametrosEstaticos()
    {
        echo "<pre>"; print_r($this->parametrosEstaticos); echo "</pre>"; exit();
    }

    /**
     * Le hice un agregado por una cuestion de practicidad a la hora de usar parametros en los plugins
     * Si se utiliza el parametro soloSistema se buscaran parametros solamente en array de sistema.
     *
     * @param boolean $soloSistema Poner en true si se necesitan parametros unicamente de sistema.
     */
    public function obtener($key, $soloSistema = false)
    {
        //primero entidades, despues controladores, por ultimo parametros de sistema (controladores y entidades solo si dispatched)
        if(!$this->request->isDispatched() || $soloSistema){
            return $this->obtenerParametroSistema($key);
        }

        //si hay que buscar tambien en parametros dinamicos...
        $valor = $this->obtenerParametroControlador($key);
        if(null !== $valor){ return $valor; }

        //no lo encontro en parametros dinamicos devuelvo por defecto el que haya en sistema o null si no lo encontro
        return $this->obtenerParametroSistema($key);
    }

    private function obtenerParametroSistema($key)
    {
        $valor = $this->obtenerParametroEstatico('sistema', $key);
        if (null === $valor)
        {
            $valor = $this->obtenerParametroDinamico('sistema', $key);
        }
        return $valor;
    }

    private function obtenerParametroControlador($key)
    {
        $grupo = $this->getGrupoParametro();
        $valor = $this->obtenerParametroEstatico($grupo, $key);
        if (null === $valor)
        {
            $valor = $this->obtenerParametroDinamico($grupo, $key);
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

    private function obtenerParametroDinamico($grupo, $key)
    {
        if(isset($this->parametrosDinamicos->$grupo[$key])){
            return $this->parametrosDinamicos->$grupo[$key];
        }else{
            return null;
        }
    }
}