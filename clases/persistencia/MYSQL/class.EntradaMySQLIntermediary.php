<?php

class EntradaMySQLIntermediary extends EntradaIntermediary
{
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
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " INSERT INTO entradas SET ".
                    " seguimientos_id = ".$this->escInt($oEntrada->getSeguimientoId()).", ".
                    " fecha = ".$this->escDate($oEntrada->getFecha())." ";

            $db->execSQL($sSQL);
            $iLastId = $db->insert_id();

            $sSQL = "INSERT INTO entrada_x_contenido_variables (entradas_id, variables_id, valorTexto, valorNumerico) VALUES ";

            $aUnidades = $oEntrada->getUnidades();
            foreach($aUnidades as $oUnidad){

                $aVariables = $oUnidad->getVariables();
                
                foreach($aVariables as $oVariable){
                    $sSQL .= " (".$iLastId.", ".$this->escInt($oVariable->getId()).", ";

                    if($oVariable->isVariableTexto()){
                        $sSQL .= $this->escStr($oVariable->getValor()).", null),";
                    }
                    if($oVariable->isVariableNumerica()){
                        $sSQL .= "null, ".$this->escFlt($oVariable->getValor())."),";
                    }
                    if($oVariable->isVariableCualitativa()){
                        $sSQL .= "null, ".$this->escInt($oVariable->getValor()->getId())."),";
                    }
                }
            }
            $sSQL = substr($sSQL, 0, -1);

            $db->execSQL($sSQL);
            $db->commit();

            $oEntrada->setId($iLastId);            
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
    
    public function actualizar($oEntrada)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $guardada = $oEntrada->isGuardada()?"1":"0";
            $sSQL = " UPDATE entradas SET ".
                    " guardada = ".$guardada." WHERE id = ".$this->escInt($oEntrada->getId());

            $db->execSQL($sSQL);

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
                foreach($aVariables as $oVariable){
                    $sSQL .= " (".$this->escInt($oEntrada->getId()).", ".$this->escInt($oVariable->getId()).", ";

                    if($oVariable->isVariableTexto()){
                        $sSQL .= $this->escStr($oVariable->getValor()).", null),";
                    }else{
                        $sSQL .= "null, ".$this->escInt($oVariable->getValor())."),";
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
                        IF(scc.id IS NULL, 'SeguimientoPersonalizado', 'SeguimientoSCC') as sObjType 
                     FROM
                        entradas e
                     LEFT JOIN 
                        seguimientos_personalizados sp ON e.seguimientos_id = sp.id
                     LEFT JOIN
                        seguimientos_scc scc ON e.seguimientos_id = scc.id
                     ";

            $WHERE = array();

            if(isset($filtro['e.seguimientos_id']) && $filtro['e.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.seguimientos_id', $filtro['e.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.fecha']) && $filtro['e.fecha']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.fecha', $filtro['e.fecha'], MYSQL_TYPE_DATE);
            }
            if(isset($filtro['fechas']) && null !== $filtro['fechas']){
                if(is_array($filtro['fechas'])){
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
            
    public function borrar($iEntradaId)
    {

    }
	
    public function existe($filtro)
    {

    }
       
    public function actualizarCampoArray($objects, $cambios){}
}