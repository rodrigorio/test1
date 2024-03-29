<?php

class EntradaMySQLIntermediary extends EntradaIntermediary
{
    const EDICION_REGULAR = "regular";
    const EDICION_ESPORADICA = "esporadica";

    private static $instance = null;

    protected function __construct( $conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return EntradaMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public function insertar($oEntrada)
    {
        $db = $this->conn;
        try{
            $db->begin_transaction();

            $sSQL = " INSERT INTO entradas SET ".
                    " seguimientos_id = ".$this->escInt($oEntrada->getSeguimientoId()).", ".
                    " tipoEdicion = ".$this->escStr($oEntrada->getTipoEdicion()).", ".
                    " fecha = ".$this->escDate($oEntrada->getFecha())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $aUnidades = $oEntrada->getUnidades();

            $sSQL1 = "INSERT INTO entrada_x_unidad (unidades_id, entradas_id) VALUES ";
            $sSQL2 = "INSERT INTO entrada_x_contenido_variables (entradas_id, variables_id, valorTexto, valorNumerico) VALUES ";

            foreach($aUnidades as $oUnidad){

                $sSQL1 .= "(".$this->escInt($oUnidad->getId()).", ".$iLastId."),";

                $aVariables = $oUnidad->getVariables(); //puede que la unidad este vacia
                if(count($aVariables)>0){
                    foreach($aVariables as $oVariable){
                        $sSQL2 .= " (".$iLastId.", ".$this->escInt($oVariable->getId()).", ";

                        if($oVariable->isVariableTexto()){
                            $sSQL2 .= $this->escStr($oVariable->getValor()).", null),";
                        }
                        if($oVariable->isVariableNumerica()){
                            $sSQL2 .= "null, ".$this->escFlt($oVariable->getValor())."),";
                        }
                        if($oVariable->isVariableCualitativa()){
                            $iValor = $oVariable->getValor() ? $oVariable->getValor()->getId() : "null";
                            $sSQL2 .= "null, ".$this->escInt($iValor)."),";
                        }
                    }
                }
            }
            $sSQL1 = substr($sSQL1, 0, -1);
            $sSQL2 = substr($sSQL2, 0, -1);

            $db->execSQL($sSQL1);
            $db->execSQL($sSQL2);

            $db->commit();

            $oEntrada->setId($iLastId);
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oEntrada)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            if(!$oEntrada->isGuardada()){
                $sSQL = " UPDATE entradas SET ".
                        " guardada = '1' WHERE id = ".$this->escInt($oEntrada->getId());
                $db->execSQL($sSQL);

                $oEntrada->isGuardada(true);
            }

            //son muchas variables las que hay que actualizar asi que genero una tabla temporal y updateo con join
            $sSQL = "CREATE TEMPORARY TABLE IF NOT EXISTS variablesTemp(
                        `entradas_id` INT(11) NOT NULL,
                        `variables_id` INT(11) NOT NULL,
                        `valorTexto` TEXT,
                        `valorNumerico` FLOAT DEFAULT NULL)";
            $db->execSQL($sSQL);

            //agrego todas las filas a actualizar en la tabla temporal
            $sSQL = "INSERT INTO variablesTemp (entradas_id, variables_id, valorTexto, valorNumerico) VALUES ";

            $aUnidades = $oEntrada->getUnidades();
            foreach($aUnidades as $oUnidad){
                $aVariables = $oUnidad->getVariables();
                if(count($aVariables)>0){
                    foreach($aVariables as $oVariable){
                        $sSQL .= " (".$this->escInt($oEntrada->getId()).", ".$this->escInt($oVariable->getId()).", ";

                        if($oVariable->isVariableTexto()){
                            $sSQL .= $this->escStr($oVariable->getValor()).", null),";
                        }
                        if($oVariable->isVariableNumerica()){
                            $sSQL .= "null, ".$this->escFlt($oVariable->getValor())."),";
                        }
                        if($oVariable->isVariableCualitativa()){
                            $iValor = $oVariable->getValor() ? $oVariable->getValor()->getId() : "null";
                            $sSQL .= "null, ".$this->escInt($iValor)."),";
                        }
                    }
                }
            }
            $sSQL = substr($sSQL, 0, -1);

            $db->execSQL($sSQL);

            //update desde tabla temporal
            $sSQL = "UPDATE entrada_x_contenido_variables ecv
                    JOIN variablesTemp vt ON ecv.entradas_id = vt.entradas_id AND ecv.variables_id = vt.variables_id
                    SET ecv.valorTexto = vt.valorTexto, ecv.valorNumerico = vt.valorNumerico";

            $db->execSQL($sSQL);

            //elimino la tabla temporal
            $sSQL = "DROP TABLE variablesTemp";
            $db->execSQL($sSQL);

            $db->commit();

            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oEntrada)
    {
        try{
            if($oEntrada->getId() != null){
                return $this->actualizar($oEntrada);
            }else{
                return $this->insertar($oEntrada);
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT DISTINCT
                        e.id as iId, e.fechaHoraCreacion as dFechaHoraCreacion, e.fecha as dFecha, e.seguimientos_id as iSeguimientoId, e.guardada as bGuardada,
                        IF(scc.id IS NULL, 'SeguimientoPersonalizado', 'SeguimientoSCC') as sObjType,
                        e.tipoEdicion as eTipoEdicion
                     FROM
                        entradas e
                     LEFT JOIN
                        seguimientos_personalizados sp ON e.seguimientos_id = sp.id
                     LEFT JOIN
                        seguimientos_scc scc ON e.seguimientos_id = scc.id
                     ";

            $WHERE = array();

            if(isset($filtro['e.id']) && $filtro['e.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.id', $filtro['e.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.seguimientos_id']) && $filtro['e.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.seguimientos_id', $filtro['e.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.tipoEdicion']) && $filtro['e.tipoEdicion']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.tipoEdicion', $filtro['e.tipoEdicion']);
            }
            if(isset($filtro['e.fecha']) && $filtro['e.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.fecha', $filtro['e.fecha'], MYSQL_TYPE_DATE);
            }
            if(isset($filtro['fechas']) && null !== $filtro['fechas']){
                if(is_array($filtro['fechas']) && (count($filtro['fechas']) > 0)){
                    $WHERE[] = $this->crearFiltroFechaDesdeHasta('e.fecha', $filtro['fechas'], false);
                }
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by e.fechaHoraCreacion desc ";
            }

            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEntradas = array();
            while($oObj = $db->oNextRecord()){
                $oEntrada = new stdClass();
                $oEntrada->iId = $oObj->iId;
                $oEntrada->eTipoEdicion = $oObj->eTipoEdicion;
                $oEntrada->dFechaHoraCreacion = $oObj->dFechaHoraCreacion;
                $oEntrada->dFecha = $oObj->dFecha;
                $oEntrada->iSeguimientoId = $oObj->iSeguimientoId;
                $oEntrada->bGuardada = ($oObj->bGuardada == "1") ? true:false;

                if($oObj->sObjType == 'SeguimientoPersonalizado')
                {
                    $oEntrada = Factory::getEntradaPersonalizadaInstance($oEntrada);
                }

                if($oObj->sObjType == 'SeguimientoSCC')
                {
                    $oEntrada = Factory::getEntradaSCCInstance($oEntrada);
                }

            	$aEntradas[] = $oEntrada;
            }

            return $aEntradas;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public final function obtenerRelUnidades($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT DISTINCT
                        e.id as iId, e.fechaHoraCreacion as dFechaHoraCreacion, e.fecha as dFecha, e.seguimientos_id as iSeguimientoId, e.guardada as bGuardada,
                        IF(scc.id IS NULL, 'SeguimientoPersonalizado', 'SeguimientoSCC') as sObjType,
                        e.tipoEdicion as eTipoEdicion
                     FROM
                        entradas e
                     JOIN
                        entrada_x_unidad eu ON eu.entradas_id = e.id
                     LEFT JOIN
                        seguimientos_personalizados sp ON e.seguimientos_id = sp.id
                     LEFT JOIN
                        seguimientos_scc scc ON e.seguimientos_id = scc.id
                     ";

            $WHERE = array();

            if(isset($filtro['e.id']) && $filtro['e.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.id', $filtro['e.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.seguimientos_id']) && $filtro['e.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.seguimientos_id', $filtro['e.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.tipoEdicion']) && $filtro['e.tipoEdicion']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.tipoEdicion', $filtro['e.tipoEdicion']);
            }
            if(isset($filtro['e.fecha']) && $filtro['e.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.fecha', $filtro['e.fecha'], MYSQL_TYPE_DATE);
            }
            if(isset($filtro['fechas']) && null !== $filtro['fechas']){
                if(is_array($filtro['fechas'])){
                    $WHERE[] = $this->crearFiltroFechaDesdeHasta('e.fecha', $filtro['fechas'], false);
                }
            }
            if(isset($filtro['eu.unidades_id']) && $filtro['eu.unidades_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('eu.unidades_id', $filtro['eu.unidades_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by e.fechaHoraCreacion desc ";
            }

            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEntradas = array();
            while($oObj = $db->oNextRecord()){
                $oEntrada = new stdClass();
                $oEntrada->iId = $oObj->iId;
                $oEntrada->eTipoEdicion = $oObj->eTipoEdicion;
                $oEntrada->dFechaHoraCreacion = $oObj->dFechaHoraCreacion;
                $oEntrada->dFecha = $oObj->dFecha;
                $oEntrada->iSeguimientoId = $oObj->iSeguimientoId;
                $oEntrada->bGuardada = ($oObj->bGuardada == "1") ? true:false;

                if($oObj->sObjType == 'SeguimientoPersonalizado')
                {
                    $oEntrada = Factory::getEntradaPersonalizadaInstance($oEntrada);
                }

                if($oObj->sObjType == 'SeguimientoSCC')
                {
                    $oEntrada = Factory::getEntradaSCCInstance($oEntrada);
                }

            	$aEntradas[] = $oEntrada;
            }

            return $aEntradas;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Este metodo devuelve cantidad de entradas por mes en un array de objetos stdClass
     * uno corresponde al año y el otro al mes con un atributo de la cantidad de entradas.
     *
     */
    public function obtenerCantidadEntradasYearMonth($iSeguimientoId)
    {
        $aMeses = array('1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo',
                        '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'septiembre', '10' => 'octubre',
                        '11' => 'noviembre', '12' => 'diciembre');

        try{
            $db = clone($this->conn);

            $sSQL = " SELECT YEAR(fecha) AS year, MONTH(fecha) AS month, COUNT(*) AS cantEntradas
                      FROM entradas e WHERE e.seguimientos_id = ".$this->escInt($iSeguimientoId)."
                      AND e.tipoEdicion = ".$this->escStr(self::EDICION_REGULAR)."
                      GROUP BY YEAR(fecha), MONTH(fecha)
                      ORDER BY year DESC, month ASC ";

            $db->query($sSQL);

            $iRecordsTotal = (int)$db->getDBValue("select FOUND_ROWS() as list_count");
            if(empty($iRecordsTotal)){ return null;}

            $aYears = array();
            $oLastRow = null;
            while($oRow = $db->oNextRecord()){
                if($oLastRow === null || $oLastRow->year != $oRow->year){
                    $oYear = new stdClass();
                    $oYear->year = $oRow->year;
                    $aYears[] = $oYear; //esto se puede hacer porq se guarda solo apuntador
                    $oLastRow = $oRow;
                }

                $oMonth = new stdClass();
                $oMonth->month = $aMeses[$oRow->month];
                $oMonth->monthNumber = $oRow->month;
                $oMonth->cantidad = $oRow->cantEntradas;
                $oYear->months[] = $oMonth;
            }

            return $aYears;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oEntrada)
    {
        try{
            $db = $this->conn;
            $db->execSQL("DELETE FROM entradas WHERE id = ".$this->escInt($oEntrada->getId()));
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro)
    {

    }

    public function actualizarCampoArray($objects, $cambios){}
}
