<?php

class VariableMySQLIntermediary extends VariableIntermediary
{
    private static $instance = null;

    protected function __construct( $conn){
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return VariableMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT
                       v.id AS iId, v.nombre AS sNombre, v.tipo AS sTipoVariable, v.descripcion AS sDescripcion, v.fechaHora as dFecha
                    FROM
                       variables v ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }

            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aVariables = array();
            while($oObj = $db->oNextRecord()){
            	$oVariable = new stdClass();
            	$oVariable->iId	= $oObj->iId;
            	$oVariable->sNombre = $oObj->sNombre;
            	$oVariable->sDescripcion = $oObj->sDescripcion;
                $oVariable->dFecha = $oObj->dFecha;

                switch($oObj->sTipoVariable){
                    case "VariableTexto":{
                        $oVariable = Factory::getVariableTextoInstance($oVariable);
                        break;
                    }
                    case "VariableNumerica":{
                        $oVariable = Factory::getVariableNumericaInstance($oVariable);
                        break;
                    }
                    case "VariableCualitativa":{
                        $oVariable = Factory::getVariableCualitativaInstance($oVariable);
                        $aModalidades = SeguimientosController::getInstance()->getModalidadesByVariableId($oObj->iId);
                        $oVariable->setModalidades($aModalidades);
                        break;
                    }
                }

            	$aVariables[] = $oVariable;
            }

            return $aVariables;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * devuelve variables pero con el contenido por fecha
     */
    public final function obtenerContenido($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);

            $sSQL = "SELECT
                       v.id AS iId, v.nombre AS sNombre, v.tipo AS sTipoVariable, v.descripcion AS sDescripcion, v.fechaHora as dFecha,
                       ecv.valorTexto as sValorTexto, ecv.valorNumerico as sValorNumerico
                    FROM
                       variables v
                    JOIN
                       entrada_x_contenido_variables ecv ON v.id = ecv.variables_id
                    JOIN
                       entradas e ON ecv.entradas_id = e.id";

            $WHERE = array();

            if(isset($filtro['e.id']) && $filtro['e.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.id', $filtro['e.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['v.unidad_id']) && $filtro['v.unidad_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('v.unidad_id', $filtro['v.unidad_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.fechaHoraCreacion']) && $filtro['e.fechaHoraCreacion'] != ""){
                $WHERE[] = $this->crearFiltroSimple('e.fechaHoraCreacion', $filtro['e.fechaHoraCreacion'], MYSQL_TYPE_DATE);
            }
            if(isset($filtro['v.borradoLogico']) && $filtro['v.borradoLogico']!=""){
                $WHERE[] = $this->crearFiltroSimple('v.borradoLogico', $filtro['v.borradoLogico']);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aVariables = array();
            while($oObj = $db->oNextRecord()){
            	$oVariable = new stdClass();
            	$oVariable->iId	= $oObj->iId;
            	$oVariable->sNombre = $oObj->sNombre;
            	$oVariable->sDescripcion = $oObj->sDescripcion;
                $oVariable->dFecha = $oObj->dFecha;

                switch($oObj->sTipoVariable){
                    case "VariableTexto":{
                        $oVariable = Factory::getVariableTextoInstance($oVariable);
                        $oVariable->setValor($oObj->sValorTexto);
                        break;
                    }
                    case "VariableNumerica":{
                        $oVariable = Factory::getVariableNumericaInstance($oVariable);
                        $oVariable->setValor($oObj->sValorNumerico);
                        break;
                    }
                    case "VariableCualitativa":{
                        $oVariable = Factory::getVariableCualitativaInstance($oVariable);
                        $aModalidades = SeguimientosController::getInstance()->getModalidadesByVariableId($oObj->iId);
                        $oVariable->setModalidades($aModalidades);
                        foreach($aModalidades as $oModalidad){
                            if($oModalidad->getId() == $oObj->sValorNumerico){
                                $oVariable->setValor($oModalidad);
                                break;
                            }
                        }
                        break;
                    }
                }

            	$aVariables[] = $oVariable;
            }

            return $aVariables;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oVariable, $iUnidadId = "")
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            if($oVariable->getId() != null){
                $this->actualizar($oVariable);
            }else{
                $this->insertar($oVariable, $iUnidadId);
            }

            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

   /**
    * Si es actualizar no es necesario el id de unidad
    */
   public  function insertar($oVariable, $iUnidadId = "")
   {
        try{
            $db = $this->conn;
            $sTipo = get_class($oVariable);

            $sSQL = " insert into variables set ".
                    " nombre = ".$this->escStr($oVariable->getNombre()).", ".
                    " tipo = ".$this->escStr($sTipo).", ".
                    " descripcion = ".$this->escStr($oVariable->getDescripcion()).", ".
                    " unidad_id = ".$this->escInt($iUnidadId)." ";

             $this->conn->execSQL($sSQL);
             $oVariable->setId($this->conn->insert_id());

             if($oVariable->isVariableCualitativa()){
                 $oModalidadIntermediary = PersistenceFactory::getModalidadIntermediary($db);
                 $oModalidadIntermediary->guardarModalidadesVariableCualitativa($oVariable);
             }

             return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

   public function actualizar($oVariable)
   {
        try{
            $sTipo = get_class($oVariable);
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " update variables set ".
                    " nombre = ".$this->escStr($oVariable->getNombre()).", ".
                    " descripcion = ".$this->escStr($oVariable->getDescripcion())." ".
                    " where id = ".$this->escInt($oVariable->getId())." ";

             $this->conn->execSQL($sSQL);

             if($oVariable->isVariableCualitativa()){
                 $oModalidadIntermediary = PersistenceFactory::getModalidadIntermediary($this->conn);
                 $oModalidadIntermediary->guardarModalidadesVariableCualitativa($oVariable);
             }

            $db->commit();
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     *  En una transaccion primero se hace update de borrado logico = 1 para todas las variables que no se pueden borrar fisicamente.
     *  Luego se utilizan los mismos ids para borrar fisicamente si y solo si borrado logico = 0.
     *  En el caso de las variables cualitativas las modalidades se borran en cascada si el borrado es fisico.
     *
     *
     * @param string $iIds string separado por comas con todos los ids de las variables que hay que borrar (logica o fisicamente)
     * @param integer $cantDiasExpiracion cantidad de dias del periodo en el cual se puede editar un seguimiento.
     * Esto implica que si la variable tiene al menos una entrada por fecha con valor mayor al periodo se borra si o si logicamente.
     */
    public function borrarVariables($iIds, $cantDiasExpiracion)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " UPDATE variables SET ".
                    " borradoLogico = '1' ".
                    " WHERE ".
                    " id in (SELECT DISTINCT ecv.variables_id
                             FROM entrada_x_contenido_variables ecv JOIN entradas e ON e.id = ecv.entradas_id
                             WHERE variables_id IN (".$iIds.")
                             AND TO_DAYS(NOW()) - TO_DAYS(e.fechaHoraCreacion) <= ".$cantDiasExpiracion.") ";

            $this->conn->execSQL($sSQL);

            $sSQL = " DELETE FROM variables WHERE ".
                    " id IN (".$iIds.") AND borradoLogico = '0' ";

            $this->conn->execSQL($sSQL);

            $db->commit();
            return true;

    	}catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iVariableId){}

    public function actualizarCampoArray($objects, $cambios){}

    public function existe($filtro){
    	try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        variables v
                    WHERE ".$this->crearCondicionSimple($filtro);

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

    public function isVariableUsuario($iVariableId, $iUsuarioId)
    {
    	try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        variables v JOIN unidades u ON v.unidad_id = u.id
                      WHERE
                        v.id = ".$this->escInt($iVariableId)." AND
                        u.usuarios_id = ".$this->escInt($iUsuarioId)." ";

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
}
