<?php
 /* Description of class UnidadMySQLIntermediary
 *
 * @author Andr�s
 */
class UnidadMySQLIntermediary extends UnidadIntermediary
{
    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return VariableMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
          self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);
            
            $sSQL = "   SELECT SQL_CALC_FOUND_ROWS
                            u.id as iId, u.nombre as sNombre, u.descripcion as sDescripcion, u.usuarios_id as iUsuarioId, 
                            u.preCargada as bPreCargada, u.fechaHora as dFechaHora, u.asociacionAutomatica as bAsociacionAutomatica
                        FROM
                            unidades u 
                        LEFT JOIN
                            seguimiento_x_unidad su ON u.id = su.unidad_id ";

            $WHERE = array();
            
            if(isset($filtro['u.id']) && $filtro['u.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.id', $filtro['u.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.usuarios_id']) && $filtro['u.usuarios_id']!=""){                
                $WHERE[] = $this->crearFiltroSimple('u.usuarios_id', $filtro['u.usuarios_id'], MYSQL_TYPE_INT);
            }           
            if(isset($filtro['u.preCargada']) && $filtro['u.preCargada']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.preCargada', $filtro['u.preCargada']);
            }
            if(isset($filtro['u.asociacionAutomatica']) && $filtro['u.asociacionAutomatica']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.asociacionAutomatica', $filtro['u.asociacionAutomatica']);
            }
            if(isset($filtro['u.nombre']) && $filtro['u.nombre'] != ""){
                $WHERE[] = $this->crearFiltroTexto('u.nombre', $filtro['u.nombre']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                //por defecto ordeno unidades por fecha de creacion desc
                $sSQL .= " order by u.fechaHora desc ";
            }
            
            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aUnidades = array();
            while($oObj = $db->oNextRecord()){
                $oUnidad = new stdClass();
                $oUnidad->iId = $oObj->iId;
                $oUnidad->sNombre = $oObj->sNombre;
                $oUnidad->sDescripcion = $oObj->sDescripcion;
                $oUnidad->dFechaHora = $oObj->dFechaHora;

                //puede no tener un usuario asociado
                if($oObj->iUsuarioId !== null){
                    $oUnidad->iUsuarioId = $oObj->iUsuarioId;
                }

                $oUnidad->bPreCargada = $oObj->bPreCargada ? true : false;
                $oUnidad->bAsociacionAutomatica = $oObj->bAsociacionAutomatica ? true : false;

                $aUnidades[] = Factory::getUnidadInstance($oUnidad);
            }

            return $aUnidades;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }        
    }

    public  function insertar($oUnidad)
    {
        try{
            $db = $this->conn;

            //puede no tener usuario integrante si se crea desde el administrador
            if($oUnidad->getUsuarioId() !== null){
                $usuarioId = $this->escInt($oUnidad->getUsuarioId());
            }else{
                $usuarioId = "NULL";
            }

            //se setean dependiendo si se inserta desde modulo de seguimientos o desde el administrador
            $preCargada = $oUnidad->isPreCargada() ? "1" : "0";
            $asociacionAutomatica = $oUnidad->isAsociacionAutomatica() ? "1" : "0";
            
            $sSQL = " INSERT INTO unidades SET ".
                    "   usuarios_id = ".$usuarioId.", ".
                    "   nombre = ".$this->escStr($oUnidad->getNombre())." , ".
                    "   descripcion = ".$this->escStr($oUnidad->getDescripcion()).", ".
                    "   preCargada = '".$preCargada."', ".
                    "   asociacionAutomatica = '".$asociacionAutomatica."' ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oUnidad)
    {
        try{
            $db = $this->conn;

            if($oUnidad->getUsuarioId() !== null){
                $usuarioId = $this->escInt($oUnidad->getUsuarioId());
            }else{
                $usuarioId = "NULL";
            }

            $preCargada = $oUnidad->isPreCargada() ? "1" : "0";
            $asociacionAutomatica = $oUnidad->isAsociacionAutomatica() ? "1" : "0";

            $sSQL = " UPDATE unidades SET ".
                    "   usuarios_id = ".$usuarioId.", ".
                    "   nombre = ".$this->escStr($oUnidad->getNombre()).", ".
                    "   descripcion = ".$this->escStr($oUnidad->getDescripcion()).", ".
                    "   preCargada = '".$preCargada."', ".
                    "   asociacionAutomatica = '".$asociacionAutomatica."' ".
                    " WHERE id = ".$this->escInt($oUnidad->getId())." ";
            
            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oUnidad)
    {
        try{
            if($oUnidad->getId() !== null){
                return $this->actualizar($oUnidad);
            }else{
                return $this->insertar($oUnidad);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Si la unidad tiene al menos una variable asociada que este borrada logicamente
     * entonces la unidad tambien se borra logicamente.
     *
     */
    public function borrar($iUnidadId)
    {
        try{
            $db = $this->conn;
            
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        unidades u
                    JOIN variables v ON u.id = v.unidad_id
                    WHERE v.borradoLogico = 1 AND u.id = ".$this->escInt($iUnidadId);

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
                //borra fisicamente la unidad
                $db->execSQL("delete from unidades where id = '".$iUnidadId."'");
                $db->commit();
                return true;
            }else{
            	//borra logicamente la unidad
                $db->execSQL("UPDATE unidades SET borradoLogico = 1 WHERE id = '".$iUnidadId."'");
                $db->commit();
                return true;
            }            
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
		
    public function actualizarCampoArray($objects, $cambios){
    }

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        unidades u 
                    WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){ 
            	return false; 
            }
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0); 
        }
    }
    
    public function isUnidadUsuario($iUnidadId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        unidades u
                      WHERE
                        u.id = ".$this->escInt($iUnidadId)." AND
                        u.usuarios_id = ".$this->escInt($iUsuarioId);

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($foundRows)){
            	return false;
            }
            
            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * El usuario es necesario porque si es una unidad pre cargada puede estar
     * asociada a seguimientos que no sean del integrante que solicita la informacion
     */
    public function obtenerMetadatosUnidad($iUnidadId, $iUsuarioId)
    {
        try{
            $iCantidadVariablesAsociadas = $iCantidadSeguimientosAsociados = 0;

            $db = $this->conn;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            unidades u JOIN variables v ON v.unidad_id = u.id
                        WHERE 
                            u.id = '".$iUnidadId."'");
            
            $iCantidadVariablesAsociadas = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            unidades u JOIN seguimiento_x_unidad su ON u.id = su.unidad_id 
                            JOIN seguimientos s ON su.seguimiento_id = s.id 
                        WHERE
                            u.id = '".$iUnidadId."' AND 
                            s.usuarios_id = '".$iUsuarioId."'");

            $iCantidadSeguimientosAsociados = $db->oNextRecord()->cantidad;

            return array($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
}