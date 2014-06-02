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

            $bSeguimiento = false; //si se obtienen las asociadas al seguimiento levanto campos de relacion
            if(isset($filtro['se.seguimientos_id']) && $filtro['se.seguimientos_id'] != ""){
                $bSeguimiento = true;
                $iSeguimientoId = $filtro['se.seguimientos_id'];
            }

            $sSQL = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
                        e.id as iId, e.descripcion as sDescripcion, e.usuarios_id as iUsuarioId,
                        e.fechaHora as dFechaHora, e.fechaBorradoLogico as dFechaBorradoLogico ";

            if($bSeguimiento){
                $sSQL .= ", se.fechaRealizado as dFechaRealizado, se.realizada as bRealizada ";
            }

            $sSQL .= "FROM
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
            if($bSeguimiento){
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

                if($bSeguimiento){
                    $oEntrevista->iSeguimientoId = $iSeguimientoId;
                    $oEntrevista->bRealizada = ($oObj->bRealizada == '1')?true:false;
                    $oEntrevista->dFechaRealizado = $oObj->dFechaRealizado;
                }

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

    public function guardarRespuestas($oEntrevista)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            //si se guarda por primera vez actualizo fecha realizado
            if(!$oEntrevista->isRealizada()){

                $oEntrevista->setFechaRealizadoHoy();

                $sSQL = " UPDATE seguimiento_x_entrevista SET ".
                        " realizada = '1', fechaRealizado = ".$this->escDate($oEntrevista->getFechaRealizado())." ".
                        " WHERE entrevistas_id = ".$this->escInt($oEntrevista->getId())." ".
                        " AND seguimientos_id = ".$this->escInt($oEntrevista->getSeguimientoId())." ";
                $db->execSQL($sSQL);

                $oEntrevista->isRealizada(TRUE);
            }

            $aPreguntas = $oEntrevista->getPreguntasRespuestas();

            SeguimientosController::getInstance()->guardarRespuestasPreguntas($aPreguntas, $oEntrevista->getSeguimientoId());

            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
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

    public function isEntrevistaUsuario($iEntrevistaId, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        entrevistas e
                      WHERE
                        e.id = ".$this->escInt($iEntrevistaId)." AND
                        e.usuarios_id = ".$this->escInt($iUsuarioId);

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

    public function isEntrevistaSeguimiento($iEntrevistaId, $iSeguimientoId)
    {
        try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        seguimiento_x_entrevista se
                      WHERE
                        se.entrevistas_id = ".$this->escInt($iEntrevistaId)." AND
                        se.seguimientos_id = ".$this->escInt($iSeguimientoId);

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

    public function borrar($oEntrevista)
    {
        try{
            $db = $this->conn;

            $iEntrevistaId = $oEntrevista->getId();

            //si al menos una pregunta fue borrada logicamente en la entrevista, entonces la entrevista tmb se borra logicamente
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        entrevistas e
                    JOIN preguntas p ON e.id = p.entrevistas_id
                    WHERE p.borradoLogico = 1 AND e.id = ".$this->escInt($iEntrevistaId);

            $db->query($sSQL);

            $foundRows = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            $db->begin_transaction();

            if(empty($foundRows)){
                //borra fisicamente la entrevista, la asociacion se va con borrado en cascada
                $db->execSQL("delete from entrevistas where id = ".$this->escInt($iEntrevistaId));
            }else{
                //borra logicamente la entrevista
                $dFechaBorradoLogico = $oEntrevista->getFechaBorradoLogico();
                $db->execSQL("UPDATE entrevistas SET borradoLogico = 1, fechaBorradoLogico = ".$this->escDate($dFechaBorradoLogico)." WHERE id = ".$this->escInt($iEntrevistaId));

                //Si el borrado es logico, entonces borro las relaciones entre entrevistas y seguimientos
                //para los seguimientos que tienen la entrevista asociada pero que todavia no fue realizada.
                $db->execSQL("DELETE FROM seguimiento_x_entrevista WHERE entrevistas_id = ".$this->escInt($iEntrevistaId)." AND realizada = 0");
            }

            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

	public function actualizarCampoArray($objects, $cambios){}

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

    /**
     * Las respuestas multiple choise estan preparadas para que se pueda elegir mas de una opcion.
     * Por eso el insert se hace solo cuando se guarda la entrevista con las respuestas.
     */
    public function asociarSeguimiento($iSeguimientoId, $oEntrevista)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            //creo la relacion entre entrevista y seguimiento
            $sSQL = " INSERT INTO seguimiento_x_entrevista SET ".
                    "   entrevistas_id = ".$this->escInt($oEntrevista->getId()).", ".
                    "   seguimientos_id = ".$this->escInt($iSeguimientoId);

            $db->execSQL($sSQL);

            //asocio las preguntas abiertas al seguimiento (sin respuestas)
            $aPreguntas = $oEntrevista->getPreguntas();
            $sSQL = "INSERT INTO pregunta_x_seguimiento (preguntas_id, respuesta, seguimientos_id) VALUES ";

            $bEntro = false;
            if(count($aPreguntas) > 0){
                foreach($aPreguntas as $oPregunta){
                    if($oPregunta->isPreguntaAbierta()){
                        $bEntro = true;
                        $sSQL .= " (".$this->escInt($oPregunta->getId()).", null, ".$this->escInt($iSeguimientoId)."),";
                    }
                }
            }

            if($bEntro){
                $sSQL = substr($sSQL, 0, -1);
                $db->execSQL($sSQL);
            }

            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
            return false;
        }
    }

    /**
     * borro siempre fisicamente asociacion entre entrevista y seguimiento y
     * se eliminan fisicamente las respuestas para las preguntas de la entrevista
     *
     * si esta realizada y expirada -> no se puede desasociar, SE DETERMINA EN EL CONTROLLER
     *
     * es mucho mas facil que unidad por entrada porque solo se puede realizar 1 vez, no hay multiples fechas
     * por eso se determina en controller si se puede desasociar, aca siempre es fisico
     *
     */
    public function desasociarSeguimiento($iSeguimientoId, $iEntrevistaId)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $db->execSQL("delete from seguimiento_x_entrevista where entrevistas_id = ".$this->escInt($iEntrevistaId)." and seguimientos_id = ".$this->escInt($iSeguimientoId));

            $sSQL = " DELETE pregunta_x_seguimiento FROM pregunta_x_seguimiento
                        JOIN preguntas ON preguntas.id = pregunta_x_seguimiento.preguntas_id
                      WHERE
                        seguimientos_id = ".$this->escInt($iSeguimientoId)."
                      AND preguntas.entrevistas_id = ".$this->escInt($iEntrevistaId);

            $db->execSQL($sSQL);

            $sSQL = " DELETE pregunta_x_opcion_x_seguimiento FROM pregunta_x_opcion_x_seguimiento
                        JOIN preguntas ON preguntas.id = pregunta_x_opcion_x_seguimiento.preguntas_id
                      WHERE
                        seguimientos_id = ".$this->escInt($iSeguimientoId)."
                      AND preguntas.entrevistas_id = ".$this->escInt($iEntrevistaId);

            $db->execSQL($sSQL);
            $db->commit();

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }
}
