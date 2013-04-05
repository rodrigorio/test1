<?php

/**
 * Define la interfaz general para todo los objetos MySQLi y establece metodos de ayuda para trabajar en el desarrollo de consultas
 *
 * @abstract
 * @package Persistence
 */
abstract class Intermediary
{
    /**
     * @var DB
     */
    protected $conn;

    /**
     * @param object $conn
     */
    protected function __construct(IMYSQL $conn){
        $this->conn = $conn;
    }

    /**
     * @param DB $conn;
     */
    protected static function &getInstance(IMYSQL $conn){}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////// METODOS PRIVADOS DE AYUDA PARA UTILIZAR CUANDO SE ESRIBAN LOS METODOS QUE REALIZAN CONSULTAS  ///////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Genera cadenas "seguras" con caracteres de escape en los distintos valores de un array
     * para que se puedan concatenar a una consulta y no se generen roturas ni agujeros de seguridad.
     *
     * Por lo general se 'escapan' valores individuales pero estas funciones son utiles cuando hay que escapar un array entero de valores
     * que se saben de antemano que van a ser de un mismo tipo.
     *
     * Por ejemplo si en un $_POST me llega un array dentro de $_POST['valores'] y yo se que son todos enteros puedo escapar todos los valores
     * al mismo tiempo: $_POST['valores'] = escapeIntegerArray($_POST['valores'])
     *
     * OJO QUE SI RECIBEN UN NULL O CADENA VACIA DE PHP LO CONVIERTE A UN STRING 'NULL' PARA QUE SE PUEDA CONCATENAR EN LA CONSULTA
     *
     * @param array
     * @return array El filtro procesado para que no rompa el string de la consulta
     * @throws Exception
     */
    protected final function escapeIntegerArray($arr){
        return array_map(array($this, 'escInt'), $arr);
    }
    protected final function escapeStringArray($arr){
        return array_map(array($this, 'escStr'), $arr);
    }
    protected final function escapeDateArray($arr){
        return array_map(array($this, 'escDate'), $arr);
    }
    protected final function escapeFloatArray($arr){
        return array_map(array($this, 'escFlt'), $arr);
    }
    protected final function escInt($val) { return $this->conn->escape($val, false, MYSQL_TYPE_INT); }
    protected final function escStr($val) { return $this->conn->escape($val, true, MYSQL_TYPE_STRING); }
    protected final function escDate($val){ return $this->conn->escape($val, true, MYSQL_TYPE_DATE); }
    protected final function escFlt($val) { return $this->conn->escape($val, false, MYSQL_TYPE_FLOAT); }


    /**
     * Este es un metodo util para casos simples.
     * Crea un string para agregar luego de la sentencia "WHERE" con la informacion de un Filtro.
     * Se puede especificar un $aliasTabla para adjuntar a los campos
     *
     * LOS VALORES YA DEBEN HABER SIDO FILTRADOS POR scape_string
     *
     * Ejemplo:
     *
     * $filtro = array('nombreUsuario' => 'unUsuario', 'contrasenia', 'unMD5cualquiera');
     * $usuarioMySQLIntermediary->obtener($filtro)
     *
     * public function obtener($filtro){
     *      ...
     *      echo "WHERE ".crearCondicionSimple($filtro);
     *      ...
     * }
     *
     * Devuelve:  WHERE nombreUsuario = 'unUsuario' AND contrasenia = 'unMD5cualquiera'
     *
     * si el alias de la tabla usuario fuera 'u' entonces se llama de la forma crearCondicionSimple($filtro, 'u')
     * y genera una cadena de la forma: WHERE u.nombreUsuario = 'unUsuario' AND u.contrasenia = 'unMD5cualquiera'
     *
     *
     * @param array $filtro Puede ser directamente el $_POST de un formulario de filtro extraido de HttpRequest Si es que este no es muy complejo.
     * @param string $aliasTabla Si el campo tiene un alias se agrega "$alias." al campo
     * @param boolean $quotes por defecto no agrega comillas. Si los filtros ya se encuentran con scape_string no se necesitan.
     * @return string del tipo [$alias.]$filtro[nombreCampo] = '$valor' AND [$alias.]$filtro[nombreCampo2] = '$valor2' AND ...
     */
    protected final function crearCondicionSimple($filtro, $aliasTabla = "", $quotes = false,$concatenador = "AND"){
        if(empty($filtro)){ return ""; }

        foreach ($filtro as $campo => $valor){
            $campo = (!empty($aliasTabla)) ? $aliasTabla.".".$campo : $campo;
            $condicion[] = ($quotes) ? " ".$campo." = '".$valor."' " : " ".$campo." = ".$valor." ";
        }
        return implode($concatenador, $condicion);
    }

    /**
     * Crea un string para agregar luego de la sentencia WHERE del tipo "nombreCampo in (array[0], array[1], ... array[n])"
     *
     * @param string $nombreCampo al que "IN" afecta
     * @param array $filtro valores que seran incluidos dentro de IN. NO INTERESA EL KEY DE LAS CELDAS DEL ARRAY, solo el valor.
     * @param string $aliasTabla Si el campo tiene un alias se agrega "$alias." al campo
     * @return " [$aliasTabla.]$nombreCampo in ($filtro[0], $filtro[1], ... $filtro[n]) "
     */
    protected final function crearCondicionIn($filtro, $nombreCampo, $aliasTabla = ""){}

    /**
     * Crea un string como el del metodo CrearCondicionIn pero recibe como parametro un array de $_POST (para usar con checkbox por ej)
     *
     * @param array $arrayPost Recibe un array desde $_POST extraido con HttpRequest->getPost('nombreArray')
     * @param string $aliasTabla El alias de la tabla del campo si es que existe
     * @return string para aplicar luego de sentencia WHERE del tipo "[$aliasTabla.]$nombreCampo in ($filtro[0], $filtro[1], ... $filtro[n])"
     */
    protected final function crearCondicionInArrayPost($arrayPost, $aliasTabla){
        //extraigo key y paso el array de la variable $_POST
        return crearCondicionIn($filtro, $nombreCampo, $aliasTabla);
    }

    // FUNCIONES PARA EL METODO BUSCAR //

   /**
     * Devuelve un string para agregar a la clausula WHERE de una consulta, se genera desde el nombre del campo y el valor de un argumento
     *
     * @param string $campo El campo de la tabla
     * @param mixed $valor Valor contra el que se va a comparar el campo en una condicion simple  A = B ?
     * @param int $tipo Constante correspondiente al tipo del contenido del campo
     * @return string
     */
    protected final function crearFiltroSimple($campo, $valor, $tipo = MYSQL_TYPE_STRING){        
        $filtro = "";
        if($valor != ""){
            switch($tipo){
                case MYSQL_TYPE_STRING: $valor = $this->escStr($valor); break;
                case MYSQL_TYPE_INT:                   
                    $valor = $this->escInt($valor);
                    break;
                case MYSQL_TYPE_FLOAT: 
                    $valor = $this->escFloat($valor);
                    break;
                case MYSQL_TYPE_DATE: $valor = $this->escDate($valor); break;
                default: $valor = $this->escStr($valor);
            }
            $filtro = " ".$campo." = ".$valor." ";
        }
        return $filtro;
    }

    /**
     * Devuelve un string para agregar a la clausula WHERE de una consulta, se genera desde el nombre del campo y el valor de un argumento
     * Si se pasa $bRigthLeft con true hace un like con 'valor%'
     * Si se pasa $bRigthLeft con false hace un like con '%valor'
     * Si no se pasa $bRigthLeft hace un like con '%valor%'
     *
     * @param string $campo El campo de la tabla
     * @param string $valor el texto que quiero filtrar en un campo
     * @param boolean $bRigthLeft que tipo de like hace
     * @return string
     */
    protected final function crearFiltroTexto($campo, $valor, $bRigthLeft = null){
    	$return = "";
	if($bRigthLeft === true){
            $return = " $campo like \"$valor%\" ";
    	}elseif($bRigthLeft === false){
            $return = " $campo like \"%$valor\" ";
    	}else{
            $return = " $campo like \"%$valor%\" ";
    	}
    	return $return;
    }

    /**
     * Devuelve un string para agregar en el WHERE de una consulta
     * si son pasados los 2 valores hace un between entre las 2,
     * si es pasado valor1, hace campo >= fechaDesde
     * si es pasado valor2, hace campo <= fechaHasta
     *
     * El valor debe ser una fecha Sql.
     * El campo ya debe poseer alias de la tabla. * DE
     * @param string $campo
     * @param date $fechaDesde
     * @param date $fechaHasta
     */
    protected final function crearFiltroFecha($campo, $fechaDesde = null, $fechaHasta = null){
    	$filtro = "";
    	if($fechaDesde != null && $fechaHasta != null){
            $filtro = " date($campo) BETWEEN '".$fechaDesde."' AND '".$fechaHasta."' " ;
    	}elseif($fechaDesde != null){
            $filtro = " date($campo) >= '".$fechaDesde."' ";
    	}elseif($fechaHasta != null){
            $filtro = " date($campo) <= '".$fechaHasta."' ";
    	}
        return $filtro;
    }

    /**
     * Recibe un array que puede tener los valores fecha desde y fecha hasta.
     * utiliza la funcion crearFiltroFecha de esta clase para crear el filtro.
     */
    protected final function crearFiltroFechaDesdeHasta($campo, $aFechas)
    {
        $aFechas = array_map('trim', $aFechas);

        if(array_key_exists("fechaDesde", $aFechas) && null != $aFechas['fechaDesde']){
            $aFechas['fechaDesde'] = Utils::fechaAFormatoSQL($aFechas['fechaDesde']);
        }

        if(array_key_exists("fechaHasta", $aFechas) && null != $aFechas['fechaHasta']){
            $aFechas['fechaHasta'] = Utils::fechaAFormatoSQL($aFechas['fechaHasta']);
        }
               
        if(
            array_key_exists("fechaDesde", $aFechas) && null != $aFechas['fechaDesde'] &&
            array_key_exists("fechaHasta", $aFechas) && null != $aFechas['fechaHasta'])
        {            
            return $this->crearFiltroFecha($campo, $aFechas['fechaDesde'], $aFechas['fechaHasta']);
        }
        
        if(array_key_exists("fechaDesde", $aFechas) && null != $aFechas['fechaDesde'])
        {
            return $this->crearFiltroFecha($campo, $aFechas['fechaDesde']);            
        }

        if(array_key_exists("fechaHasta", $aFechas) && null != $aFechas['fechaHasta'])
        {
            return $this->crearFiltroFecha($campo, null, $aFechas['fechaHasta']);
        }
    }

    /**
     * @param string $consulta String con la consulta que se va a ejecutar
     * @return boolean dependiendo si ya existe la clausula WHERE en la query
     */
    protected final function existeWhere($consulta){
        return (strpos(strtolower($consulta), "where") === true);
    }

    /**
     * Esta funcion se utiliza para concatenar los filtros de busqueda en una consulta de los metodos buscar() de las clases MySQL concretas
     * Un caso de ejemplo seria:
     *
     * public function buscar($args, $recordsTotal){
     *
     *      ...
     *
     *      $sqlQuery = "SELECT u.nombre, u.edad, u.fechaAlta FROM usuarios u";
     *
     *      $WHERE[] = $this->crearFiltroTexto('u.nombre', $args['nombre']);
     *      $WHERE[] = $this->crearFiltroSimple('u.edad', $args['edad'], MYSQL_TYPE_INT);
     *      $WHERE[] = $this->crearFiltroFechaDesde('u.fechaAlta', $args['fecha']);
     *
     *      $sqlQuery = $this->agregarFiltrosConsulta($sqlQuery, $WHERE);
     *
     *      ...
     *
     * }
     *
     * @param string $consulta Query String que se va a ejecutar en la DB
     * @param array $condiciones tiene condiciones para ser asignadas a la clausula WHERE generadas por las funciones desde los argumentos de buscar()
     * @return string Si el array no tiene condiciones devuelve el string intacto
     */
    protected final function agregarFiltrosConsulta($consulta, $filtrosBuscar){
        if(is_array($filtrosBuscar) && count($filtrosBuscar) > 0){
            if(!$this->existeWhere($consulta)){
                $consulta .= " WHERE ";
            }else{
                $consulta .= " AND ";
            }
            $consulta .= implode("AND", $filtrosBuscar);
        }
        return $consulta;
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////// METODOS INTERFACE (DEBEN SER IMPLEMENTADOS EN LOS INTERMEDIARY CONCRETOS) ///////////
    /////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param  array|null $filtro Tiene todos los campos por los que hay que filtrar, puede tener subarray si el filtro es complejo.
     *         $filtro['nombreCampo'] = valorFiltro. Si se pasa null por parametro el obtener() devuelve todos los objetos guardados en persistencia
     * @param  int $foundRows Si existe se pasa por referencia y asigna a la variable la cantidad de filas resultantes de la consulta
     * @return array|null El tipo de objeto que devuelve depende de la clase de model con la que trabaje el Intermediary.
     * @throws Exception si hubo error en el metodo
     */
    abstract public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null);

    /**
     * @param  array $filtro Tiene todos los campos por los que hay que filtrar. $filtro['nombreCampo'] = valorFiltro
     * @return boolean
     * @throws Exception si hubo error en la consulta
     */
    abstract public function existe($filtro);

    /**
     * @param  StdClass|array $objects objeto o array de objetos de modelo que tiene que ser actualizado en persistencia.
     * @return StdClass|array retorna el objeto que se actualizo con posibles alteraciones en sus atributos dependiendo la actualizacion
     * @throws Exception si hubo error en la consulta
     */
    abstract public function actualizar($object);

    /**
     * @param array $objects Objetos de la clase que manipule el intermediary concreto
     * @param array $cambios Array de la estructura $['nombreCampo'] = $valor.
     *        Actualiza 1..N campos con sus respectivos valores en un conjunto de objetos de la clase que manipula el intermediary concreto
     */
    abstract public function actualizarCampoArray($objects, $cambios);

    /**
     * @param  StdClass|array $objects objeto o array de objetos de modelo que tiene que ser insertado en persistencia.
     * @return StdClass|array retorna el objeto o el array de objetos que se actualizo
     *         con posibles alteraciones en sus atributos dependiendo la actualizacion (por ejemplo, inserta el id en el objeto si no lo tenia)
     * @throws Exception si hubo error en la consulta
     */
     abstract public function insertar($objects);

    /**
     * Este es un metodo de conveniencia, recibe un objeto y se fija si posee clave primaria.
     * Dependiendo el resultado de esta comparacion decide si actualizar o insertar el objeto.
     *
     * @param  StdClass $object objeto de modelo que tiene que ser guardado en persistencia.
     * @return StdClass retorna el objeto que se guardo con posibles alteraciones en sus atributos dependiendo la consulta ejecutada
     * @throws Exception si hubo error en la consulta
     */
    abstract public function guardar($object);

    /**
     * Internamente utiliza la clave primaria de los objetos para borrarlos de la persistencia.
     *
     * @param  StdClass|array $objects objeto o array de objetos de modelo que tiene que ser eliminado de la persistencia.
     * @throws Exception si hubo error en la consulta
     */
    abstract public function borrar($objects);
}
