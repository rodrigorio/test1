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
            
            $sSQL = "   SELECT DISTINCT SQL_CALC_FOUND_ROWS
                            u.id as iId, u.nombre as sNombre, u.descripcion as sDescripcion, u.usuarios_id as iUsuarioId, 
                            u.preCargada as bPreCargada, u.fechaHora as dFechaHora, u.asociacionAutomatica as bAsociacionAutomatica,
                            u.tipoEdicion as eTipoEdicion, u.fechaBorradoLogico as dFechaBorradoLogico
                        FROM
                            unidades u 
                        LEFT JOIN
                            seguimiento_x_unidad su ON u.id = su.unidades_id ";

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
            if(isset($filtro['u.borradoLogico']) && $filtro['u.borradoLogico']!=""){
                $WHERE[] = $this->crearFiltroSimple('u.borradoLogico', $filtro['u.borradoLogico'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.nombre']) && $filtro['u.nombre'] != ""){
                $WHERE[] = $this->crearFiltroTexto('u.nombre', $filtro['u.nombre']);
            }
            if(isset($filtro['u.tipoEdicion']) && $filtro['u.tipoEdicion'] != ""){
                $WHERE[] = $this->crearFiltroSimple('u.tipoEdicion', $filtro['u.tipoEdicion']);
            }
            if(isset($filtro['su.seguimientos_id']) && $filtro['su.seguimientos_id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('su.seguimientos_id', $filtro['su.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['u.fechaHora']) && $filtro['u.fechaHora'] != ""){
                $WHERE[] = $this->crearFiltroFecha('u.fechaHora', null, $filtro['u.fechaHora'], false, true);
            }
            if(isset($filtro['u.fechaBorradoLogico']) && $filtro['u.fechaBorradoLogico'] != ""){
                $WHERE[] = $this->crearFiltroFecha('u.fechaBorradoLogico', null, $filtro['u.fechaBorradoLogico'], true, true);
            }
            if(isset($filtro['su.fechaBorradoLogico']) && $filtro['su.fechaBorradoLogico'] != ""){
                $WHERE[] = $this->crearFiltroFecha('su.fechaBorradoLogico', null, $filtro['su.fechaBorradoLogico'], true, true);
            }
            if(isset($filtro['noAsociado']) && $filtro['noAsociado'] != ""){
                $WHERE[] = " u.id NOT IN (SELECT unidades_id FROM seguimiento_x_unidad WHERE seguimientos_id = ".$this->escInt($filtro['noAsociado']).") ";
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
                $oUnidad->dFechaBorradoLogico = $oObj->dFechaBorradoLogico;
                $oUnidad->eTipoEdicion = $oObj->eTipoEdicion;

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

    /**
     * Es uno de los principales metodos de la clase, con esto puedo lograr optimizar y sumar en performance.
     */
    public function obtenerUnidadesByEntrada($iEntradaId, $eTipoEdicion = 'regular')
    {
        try{
            $db = clone($this->conn);

            $sSQL = "   SELECT DISTINCT SQL_CALC_FOUND_ROWS
                            u.id as iId, u.nombre as sNombre, u.descripcion as sDescripcion, u.usuarios_id as iUsuarioId,
                            u.preCargada as bPreCargada, u.fechaHora as dFechaHora, u.asociacionAutomatica as bAsociacionAutomatica,
                            u.tipoEdicion as eTipoEdicion, u.fechaBorradoLogico as dFechaBorradoLogico
                        FROM
                            entrada_x_unidad eu
                        JOIN
                            unidades u ON eu.unidades_id = u.id
                        LEFT JOIN
                            seguimiento_x_unidad su ON u.id = su.unidades_id ";

            $WHERE = array();

            $WHERE[] = $this->crearFiltroSimple('eu.entradas_id', $iEntradaId, MYSQL_TYPE_INT);
            $WHERE[] = $this->crearFiltroSimple('u.borradoLogico', "0", MYSQL_TYPE_INT);
            $WHERE[] = $this->crearFiltroSimple('u.tipoEdicion', $eTipoEdicion);
            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            $sSQL .= " order by u.fechaHora asc ";

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
                $oUnidad->dFechaBorradoLogico = $oObj->dFechaBorradoLogico;
                $oUnidad->eTipoEdicion = $oObj->eTipoEdicion;

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
                $usuarioId = null;
            }

            //se setean dependiendo si se inserta desde modulo de seguimientos o desde el administrador
            $preCargada = $oUnidad->isPreCargada() ? "1" : "0";
            $asociacionAutomatica = $oUnidad->isAsociacionAutomatica() ? "1" : "0";
            
            $sSQL = " INSERT INTO unidades SET ".
                    "   usuarios_id = ".$this->escInt($usuarioId).", ".
                    "   nombre = ".$this->escStr($oUnidad->getNombre())." , ".
                    "   descripcion = ".$this->escStr($oUnidad->getDescripcion()).", ".
                    "   preCargada = '".$preCargada."', ".
                    "   asociacionAutomatica = '".$asociacionAutomatica."', ". 
                    "   tipoEdicion = ".$this->escStr($oUnidad->getTipoEdicion());

            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Tipo de edicion se determina solo en crear unidad.
     */
    public function actualizar($oUnidad)
    {
        try{
            $db = $this->conn;

            if($oUnidad->getUsuarioId() !== null){
                $usuarioId = $this->escInt($oUnidad->getUsuarioId());
            }else{
                $usuarioId = null;
            }

            $preCargada = $oUnidad->isPreCargada() ? "1" : "0";
            $asociacionAutomatica = $oUnidad->isAsociacionAutomatica() ? "1" : "0";

            $sSQL = " UPDATE unidades SET ".
                    "   usuarios_id = ".$this->escInt($usuarioId).", ".
                    "   nombre = ".$this->escStr($oUnidad->getNombre()).", ".
                    "   descripcion = ".$this->escStr($oUnidad->getDescripcion()).", ".
                    "   preCargada = '".$preCargada."', ".
                    "   asociacionAutomatica = '".$asociacionAutomatica."', ".
                    "   fechaBorradoLogico = ".$this->escDate($oUnidad->getFechaBorradoLogico())." ".
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
    public function borrar($oUnidadId)
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

            $db->begin_transaction();
            
            if(empty($foundRows)){
                //borra fisicamente la unidad
                $db->execSQL("delete from unidades where id = ".$this->escInt($iUnidadId));
            }else{
            	//borra logicamente la unidad
                $dFechaBorradoLogico = $oUnidadId->getFechaBorradoLogico();
                $db->execSQL("UPDATE unidades SET borradoLogico = 1, fechaBorradoLogico = ".$this->escDate($dFechaBorradoLogico)." WHERE id = ".$this->escInt($iUnidadId));

                //Si el borrado es logico, entonces borro las relaciones entre unidades y seguimientos
                //para los seguimientos que tienen la unidad asociada pero todavia no guardaron valor en ninguna variable.
                $sSQL = " SELECT su.seguimientos_id
                            FROM seguimiento_x_unidad su LEFT JOIN
                            (SELECT e.seguimientos_id, variables_id FROM entrada_x_contenido_variables ecv JOIN entradas e ON e.id = ecv.entradas_id
                             JOIN variables v ON v.id = ecv.variables_id JOIN unidades u ON v.unidad_id = u.id WHERE u.id = ".$this->escInt($iUnidadId).")
                          AS aux ON aux.seguimientos_id = su.seguimientos_id
                          WHERE su.unidades_id = ".$this->escInt($iUnidadId)." AND ISNULL(aux.seguimientos_id) ";

                //creo una lista con los ids de los seguimientos:
                // los ids de los seguimientos q estan asociados a una unidad 'X'
                // pero q no tienen ningun valor en ninguna de las variables en ninguna fecha (sin entradas con esa unidad)
                $db->query($sSQL);
                $seguimientos = "";
                while($oObj = $db->oNextRecord()){
                    $seguimientos .= $oObj->seguimientos_id.",";
                }

                if($seguimientos != ""){
                    $seguimientos = substr($seguimientos, 0, -1);
                    $db->execSQL("DELETE FROM seguimiento_x_unidad WHERE seguimientos_id IN (".$seguimientos.")");
                }
            }

            $db->commit();
            return true;            
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
                            unidades u JOIN seguimiento_x_unidad su ON u.id = su.unidades_id
                            JOIN seguimientos s ON su.seguimientos_id = s.id
                        WHERE
                            u.id = '".$iUnidadId."' AND 
                            s.usuarios_id = '".$iUsuarioId."'");

            $iCantidadSeguimientosAsociados = $db->oNextRecord()->cantidad;

            return array($iCantidadVariablesAsociadas, $iCantidadSeguimientosAsociados);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function asociarSeguimiento($iSeguimientoId, $iUnidadId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " INSERT INTO seguimiento_x_unidad SET ".
                    "   unidades_id = ".$this->escInt($iUnidadId).", ".
                    "   seguimientos_id = ".$this->escInt($iSeguimientoId);

            $db->execSQL($sSQL);
            $db->commit();

            return true;
    	}catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * borro siempre fisicamente asociacion entre unidad y seguimiento
     *
     * tambien borro fisicamente la unidad con sus respectivas variables para entradas
     * que esten dentro del periodo de edicion    
     * (asociacion de entrada con variables de la unidad)
     * (asociacion de entrada con unidad)
     *
     * tambien borro fisicamente la unidad con sus respectivas variables (se repite el caso)
     * en entradas posteriores al periodo de edicion pero que no se guardaron nunca. (solo se crearon)
     *
     */
    public function desasociarSeguimiento($iSeguimientoId, $iUnidadId, $iCantDiasEdicion)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();
            
            $db->execSQL("delete from seguimiento_x_unidad where unidades_id = ".$this->escInt($iUnidadId)." and seguimientos_id = ".$this->escInt($iSeguimientoId));

            $sSQL = " DELETE FROM entrada_x_contenido_variables
                      WHERE entradas_id
                        IN (SELECT e.id FROM entradas e
                            WHERE e.seguimientos_id = ".$this->escInt($iSeguimientoId)." 
                            AND (TO_DAYS(NOW()) - TO_DAYS(e.fechaHoraCreacion) <= ".$this->escInt($iCantDiasEdicion).") 
                            OR ((TO_DAYS(NOW()) - TO_DAYS(e.fechaHoraCreacion) > ".$this->escInt($iCantDiasEdicion).") AND guardada = 0))
                      AND variables_id
                        IN (SELECT v.id FROM VARIABLES v WHERE v.unidad_id = ".$this->escInt($iUnidadId).")";

            $db->execSQL($sSQL);

            $sSQL = " DELETE FROM entrada_x_unidad
                      WHERE unidades_id = ".$this->escInt($iUnidadId)."
                      AND entradas_id IN (SELECT e.id FROM entradas e
                                          WHERE e.seguimientos_id = ".$this->escInt($iSeguimientoId)."
                                          AND (TO_DAYS(NOW()) - TO_DAYS(e.fechaHoraCreacion) <= ".$this->escInt($iCantDiasEdicion).")
                                          OR ((TO_DAYS(NOW()) - TO_DAYS(e.fechaHoraCreacion) > ".$this->escInt($iCantDiasEdicion).") AND guardada = 0))";
            $db->execSQL($sSQL);
            $db->commit();
            
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
}