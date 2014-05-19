<?php

class EntrevistaMySQLIntermediary extends EntrevistaIntermediary
{
    private static $instance = null;

    protected function __construct($conn) {
        parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return GroupMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public final function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "   SELECT DISTINCT SQL_CALC_FOUND_ROWS
                            e.id as iId, e.descripcion as sDescripcion, e.usuarios_id as iUsuarioId,
                            e.fechaHora as dFechaHora, e.fechaBorradoLogico as dFechaBorradoLogico
                        FROM
                            entrevistas e
                        LEFT JOIN
                            seguimiento_x_entrevista se ON e.id = se.entrevistas_id ";

            $WHERE = array();

            if(isset($filtro['e.id']) && $filtro['e.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.id', $filtro['e.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.usuarios_id']) && $filtro['e.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.usuarios_id', $filtro['e.usuarios_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.borradoLogico']) && $filtro['e.borradoLogico']!=""){
                $WHERE[] = $this->crearFiltroSimple('e.borradoLogico', $filtro['e.borradoLogico'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.descripcion']) && $filtro['e.descripcion'] != ""){
                $WHERE[] = $this->crearFiltroTexto('e.descripcion', $filtro['e.descripcion']);
            }
            if(isset($filtro['se.seguimientos_id']) && $filtro['se.seguimientos_id'] != ""){
                $WHERE[] = $this->crearFiltroSimple('se.seguimientos_id', $filtro['se.seguimientos_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['e.fechaHora']) && $filtro['e.fechaHora'] != ""){
                $WHERE[] = $this->crearFiltroFecha('e.fechaHora', null, $filtro['e.fechaHora'], false, true);
            }
            if(isset($filtro['e.fechaBorradoLogico']) && $filtro['e.fechaBorradoLogico'] != ""){
                $WHERE[] = $this->crearFiltroFecha('e.fechaBorradoLogico', null, $filtro['e.fechaBorradoLogico'], true, true);
            }
            if(isset($filtro['noAsociado']) && $filtro['noAsociado'] != ""){
                $WHERE[] = " e.id NOT IN (SELECT entrevistas_id FROM seguimiento_x_entrevista WHERE seguimientos_id = ".$this->escInt($filtro['noAsociado']).") ";
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                //por defecto ordeno unidades por fecha de creacion desc
                $sSQL .= " order by e.fechaHora desc ";
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aEntrevistas = array();
            while($oObj = $db->oNextRecord()){
                $oEntrevista = new stdClass();
                $oEntrevista->iId = $oObj->iId;
                $oEntrevista->sDescripcion = $oObj->sDescripcion;
                $oEntrevista->dFechaHora = $oObj->dFechaHora;
                $oEntrevista->dFechaBorradoLogico = $oObj->dFechaBorradoLogico;

                //puede no tener un usuario asociado si es precargada desde admin
                if($oObj->iUsuarioId !== null){
                    $oEntrevista->iUsuarioId = $oObj->iUsuarioId;
                }

                $aEntrevistas[] = Factory::getEntrevistaInstance($oEntrevista);
            }

            return $aEntrevistas;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}

    public function insertar($oEntrevista)
    {
        try{
            $db = $this->conn;

            $sSQL = " INSERT INTO entrevistas SET ".
                    "   usuarios_id = ".$this->escInt($oEntrevista->getUsuarioId()).", ".
                    "   descripcion = ".$this->escStr($oEntrevista->getDescripcion());

            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}

	public function actualizar($oEntrevista)
    {
        try{
            $db = $this->conn;

            $sSQL = " UPDATE entrevistas SET ".
                    "   usuarios_id = ".$this->escInt($oEntrevista->getUsuarioId()).", ".
                    "   descripcion = ".$this->escStr($oEntrevista->getDescripcion()).", ".
                    "   fechaBorradoLogico = ".$this->escDate($oEntrevista->getFechaBorradoLogico())." ".
                    " WHERE id = ".$this->escInt($oEntrevista->getId())." ";

            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
	}

    public function guardar($oEntrevista)
    {
        try{
			if($oEntrevista->getId() != null){
            	return $this->actualizar($oEntrevista);
            }else{
				return $this->insertar($oEntrevista);
            }
		}catch(Exception $e){
			throw new Exception($e->getMessage(), 0);
		}
    }

    public function obtenerMetadatosEntrevista($iEntrevistaId)
    {
        try{
            $iCantidadPreguntasAsociadas = $iCantidadSeguimientosAsociados = 0;

            $db = $this->conn;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            entrevistas e JOIN preguntas p ON p.entrevistas_id = e.id
                        WHERE
                            e.id = '".$iEntrevistaId."'");

            $iCantidadPreguntasAsociadas = $db->oNextRecord()->cantidad;

            $db->query("SELECT
                            COUNT(*) as cantidad
                        FROM
                            entrevistas e JOIN seguimiento_x_entrevista se ON e.id = se.entrevistas_id
                        WHERE
                            e.id = '".$iEntrevistaId."'");

            $iCantidadSeguimientosAsociados = $db->oNextRecord()->cantidad;

            return array($iCantidadPreguntasAsociadas, $iCantidadSeguimientosAsociados);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($oEntrevista) {
		try{
			$db = $this->conn;
			$db->execSQL("delete from entrevistas where id=".$db->escape($oEntrevista->getId(),false,MYSQL_TYPE_INT));
			$db->commit();

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
                        entrevistas e
					WHERE ".$this->crearCondicionSimple($filtro,"",false,"OR");

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
}
