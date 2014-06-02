<?php

class PreguntaMySQLIntermediary extends PreguntaIntermediary
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
                       p.id AS iId, p.tipo AS sTipoPregunta, p.descripcion AS sDescripcion, p.fechaHora as dFecha, p.orden as iOrden
                    FROM
                       preguntas p ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by orden asc ";
            }

            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aPreguntas = array();
            while($oObj = $db->oNextRecord()){
                $oPregunta = new stdClass();
                $oPregunta->iId = $oObj->iId;
                $oPregunta->sDescripcion = $oObj->sDescripcion;
                $oPregunta->dFecha = $oObj->dFecha;
                $oPregunta->iOrden = $oObj->iOrden;

                switch($oObj->sTipoPregunta){
                    case "PreguntaAbierta":{
                        $oPregunta = Factory::getPreguntaAbiertaInstance($oPregunta);
                        break;
                    }
                    case "PreguntaMC":{
                        $oPregunta = Factory::getPreguntaMCInstance($oPregunta);
                        $aOpciones = SeguimientosController::getInstance()->getOpcionesByPreguntaId($oObj->iId);
                        $oPregunta->setOpciones($aOpciones);
                        break;
                    }
                }

                $aPreguntas[] = $oPregunta;
            }

            return $aPreguntas;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * las preguntas abiertas ya estan asociadas al seguimiento y hay que guardar el valor.
     * las preguntas multiple choise se tratan a parte porque pueden tener mas de una opcion marcada como respuesta.
     */
    public final function guardarRespuestas($aPreguntas, $iSeguimientoId)
    {
        try{
            $db = $this->conn;

            if(count($aPreguntas)==0){
                return true;
            }

            //preparo los strings para agregar respuestas abiertas y MC
            $sSqlPreguntasAbiertasTemp = "";
            $sSqlPreguntasMC = "";
            $sIdsPreguntasMC = "";

            foreach($aPreguntas as $oPregunta){

                if($oPregunta->isPreguntaAbierta()){
                    $sSqlPreguntasAbiertasTemp .= " (".$this->escInt($oPregunta->getId()).", ".$this->escStr($oPregunta->getRespuesta()).", ".$this->escInt($iSeguimientoId)."),";
                }

                if($oPregunta->isPreguntaMC()){
                    $sIdsPreguntasMC .= " ".$oPregunta->getId().",";
                    $iValor = $oPregunta->getRespuesta() ? $oPregunta->getRespuesta()->getId() : "null";
                    $sSqlPreguntasMC .= " (".$this->escInt($oPregunta->getId()).", ".$iValor.", ".$this->escInt($iSeguimientoId)."),";
                }
            }

            $db->begin_transaction();

            //al menos 1 pregunta abierta
            if(!empty($sSqlPreguntasAbiertasTemp)){

                //tabla temporal para las respuestas a preguntas abiertas
                $sSQL = "CREATE TEMPORARY TABLE IF NOT EXISTS preguntasTemp(
                            `preguntas_id` INT(11) NOT NULL,
                            `respuesta` TEXT,
                            `seguimientos_id` INT(11) NOT NULL)";
                $db->execSQL($sSQL);

                $sSqlPreguntasAbiertasTemp = substr($sSqlPreguntasAbiertasTemp, 0, -1);
                $sSqlPreguntasAbiertasTemp = "INSERT INTO preguntasTemp (preguntas_id, respuesta, seguimientos_id) VALUES ".$sSqlPreguntasAbiertasTemp;
                $db->execSQL($sSqlPreguntasAbiertasTemp);

                //update desde tabla temporal
                $sSQL = "UPDATE pregunta_x_seguimiento ps
                        JOIN preguntasTemp pt ON ps.preguntas_id = pt.preguntas_id AND ps.seguimientos_id = pt.seguimientos_id
                        SET ps.respuesta = pt.respuesta";
                $db->execSQL($sSQL);

                //elimino la tabla temporal
                $sSQL = "DROP TABLE preguntasTemp";
                $db->execSQL($sSQL);
            }

            //al menos 1 pregunta MC
            if(!empty($sSqlPreguntasMC)){

                //elimino fisicamente todas las opciones para todas las preguntas, luego vuelvo a insertar las nuevas
                $sIdsPreguntasMC = substr($sIdsPreguntasMC, 0, -1);
                $sSqlPreguntasMC = substr($sSqlPreguntasMC, 0, -1);

                $sSQL = " DELETE FROM pregunta_x_opcion_x_seguimiento WHERE ".
                        " seguimientos_id = ".$this->escInt($iSeguimientoId)." ".
                        " AND preguntas_id IN (".$sIdsPreguntasMC.") ";

                $db->execSQL($sSQL);

                //inserto las opciones nuevas
                $sSQL = "INSERT INTO pregunta_x_opcion_x_seguimiento (preguntas_id, preguntas_opciones_id, seguimientos_id) VALUES ".$sSqlPreguntasMC;
                $db->execSQL($sSQL);
            }

            $db->commit();

            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Devuelve preguntas con respuestas.
     * Lo complicado es que las respuestas de multiple choise son en tabla aparte y hay que mantener el orden de las preguntas en la entrevista.
     * Estan en tabla aparte las respuestas MC porque esta todo preparado para q en el futuro soporte mas de una opcion como respuesta.
     *
     * Si o si tiene que venir el filtro de entrevista y de seguimiento
     *
     * @todo falta soportar mas de una opcion por pregunta MC. como esta ahora generaria 1 pregunta por cada opcion
     */
    public final function obtenerRespuestas($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            if(!isset($filtro['p.entrevistas_id']) || !isset($filtro['ps.seguimientos_id']) || !isset($filtro['pos.seguimientos_id'])){
                return null;
            }

            $sSQL = "SELECT
                        p.id AS iId, p.tipo AS sTipoPregunta, p.descripcion AS sDescripcion, p.fechaHora as dFecha, p.orden as iOrden,
                        ps.respuesta as sRespuesta, pos.preguntas_opciones_id as iOpcionId
                    FROM
                       preguntas p
                    LEFT JOIN
                       pregunta_x_seguimiento ps ON p.id = ps.preguntas_id
                    LEFT JOIN
                       pregunta_x_opcion_x_seguimiento pos ON p.id = pos.preguntas_id";

            $WHERE = array();

            if(isset($filtro['p.id']) && $filtro['p.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.id', $filtro['p.id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.entrevistas_id']) && $filtro['p.entrevistas_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.entrevistas_id', $filtro['p.entrevistas_id'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['p.borradoLogico']) && $filtro['p.borradoLogico']!=""){
                $WHERE[] = $this->crearFiltroSimple('p.borradoLogico', $filtro['p.borradoLogico'], MYSQL_TYPE_INT);
            }
            if(isset($filtro['ps.seguimientos_id']) && $filtro['ps.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('ps.seguimientos_id', $filtro['ps.seguimientos_id'], MYSQL_TYPE_INT, TRUE);
            }
            if(isset($filtro['pos.seguimientos_id']) && $filtro['pos.seguimientos_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('pos.seguimientos_id', $filtro['pos.seguimientos_id'], MYSQL_TYPE_INT, TRUE);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by orden asc ";
            }

            if ($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT);
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aPreguntas = array();
            while($oObj = $db->oNextRecord()){
                $oPregunta = new stdClass();
                $oPregunta->iId = $oObj->iId;
                $oPregunta->sDescripcion = $oObj->sDescripcion;
                $oPregunta->dFecha = $oObj->dFecha;
                $oPregunta->iOrden = $oObj->iOrden;

                switch($oObj->sTipoPregunta){
                    case "PreguntaAbierta":{
                        $oPregunta = Factory::getPreguntaAbiertaInstance($oPregunta);
                        $oPregunta->setRespuesta($oObj->sRespuesta);
                        break;
                    }
                    case "PreguntaMC":{
                        $oPregunta = Factory::getPreguntaMCInstance($oPregunta);
                        $aOpciones = SeguimientosController::getInstance()->getOpcionesByPreguntaId($oObj->iId);
                        $oPregunta->setOpciones($aOpciones);
                        foreach($aOpciones as $oOpcion){
                            if($oOpcion->getId() == $oObj->iOpcionId){
                                $oPregunta->setRespuesta($oOpcion);
                                break;
                            }
                        }
                        break;
                    }
                }

                $aPreguntas[] = $oPregunta;
            }

            return $aPreguntas;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardar($oPregunta, $iEntrevistaId = "")
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            if($oPregunta->getId() != null){
                $this->actualizar($oPregunta);
            }else{
                $this->insertar($oPregunta, $iEntrevistaId);
            }

            $db->commit();
            return true;
        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

   /**
    * Si es actualizar no es necesario el id de entrevista
    */
   public  function insertar($oPregunta, $iEntrevistaId = "")
   {
        try{
            $db = $this->conn;
            $sTipo = get_class($oPregunta);

            $sSQL = " insert into preguntas set ".
                    " descripcion = ".$this->escStr($oPregunta->getDescripcion()).", ".
                    " tipo = ".$this->escStr($sTipo).", ".
                    " orden = ".$this->escInt($oPregunta->getOrden()).", ".
                    " entrevistas_id = ".$this->escInt($iEntrevistaId)." ";

             $this->conn->execSQL($sSQL);
             $oPregunta->setId($this->conn->insert_id());

             if($oPregunta->isPreguntaMC()){
                 $oOpcionIntermediary = PersistenceFactory::getOpcionIntermediary($db);
                 $oOpcionIntermediary->guardarOpcionesPreguntaMC($oPregunta);
             }

             return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

   public function actualizar($oPregunta)
   {
        try{
            $sTipo = get_class($oPregunta);
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " update preguntas set ".
                    " orden = ".$this->escInt($oPregunta->getOrden()).", ".
                    " descripcion = ".$this->escStr($oPregunta->getDescripcion())." ".
                    " where id = ".$this->escInt($oPregunta->getId())." ";

             $this->conn->execSQL($sSQL);

             if($oPregunta->isPreguntaMC()){
                 $oOpcionIntermediary = PersistenceFactory::getOpcionIntermediary($db);
                 $oOpcionIntermediary->guardarOpcionesPreguntaMC($oPregunta);
             }

            $db->commit();
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     *  En una transaccion primero se hace update de borrado logico = 1 para todas las preguntas que no se pueden borrar fisicamente.
     *  Luego se utilizan los mismos ids para borrar fisicamente si y solo si borrado logico = 0.
     *  En el caso de las preguntas multiple choise las opciones se borran en cascada si el borrado es fisico.
     *
     *
     * @param string $iIds string separado por comas con todos los ids de las preguntas que hay que borrar (logica o fisicamente)
     * @param integer $cantDiasExpiracion cantidad de dias del periodo en el cual se puede editar un seguimiento.
     * Esto implica que si la pregunta esta contestada en algun seguimiento y a su vez expirada desde la fecha en la q se realizo la entrevisata,
     * entonces se borra si o si logicamente.
     */
    public function borrarPreguntas($sIds, $cantDiasExpiracion)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            //para hacerlo mas simple me fijo por separado las multiple choise y las abiertas porq son distintas tablas
            $sSQL = " UPDATE preguntas SET ".
                    " borradoLogico = '1' ".
                    " WHERE ".
                    " id in (SELECT DISTINCT pos.preguntas_id
                             FROM pregunta_x_opcion_x_seguimiento pos JOIN seguimiento_x_entrevista se ON se.seguimientos_id = pos.seguimientos_id
                             WHERE se.realizada = 1
                             AND preguntas_id IN (".$sIds.")
                             AND TO_DAYS(NOW()) - TO_DAYS(se.fechaRealizado) <= ".$cantDiasExpiracion.") ";

            $this->conn->execSQL($sSQL);

            $sSQL = " UPDATE preguntas SET ".
                    " borradoLogico = '1' ".
                    " WHERE ".
                    " id in (SELECT DISTINCT ps.preguntas_id
                             FROM pregunta_x_seguimiento ps JOIN seguimiento_x_entrevista se ON se.seguimientos_id = ps.seguimientos_id
                             WHERE se.realizada = 1
                             AND preguntas_id IN (".$sIds.")
                             AND TO_DAYS(NOW()) - TO_DAYS(se.fechaRealizado) <= ".$cantDiasExpiracion.") ";

            $this->conn->execSQL($sSQL);

            $sSQL = " DELETE FROM preguntas WHERE ".
                    " id IN (".$sIds.") AND borradoLogico = '0' ";

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
                        preguntas p
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

    public function isPreguntaUsuario($iPreguntaId, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        preguntas p JOIN entrevistas e ON p.entrevistas_id = e.id
                      WHERE
                        p.id = ".$this->escInt($iPreguntaId)." AND
                        e.usuarios_id = ".$this->escInt($iUsuarioId)." ";

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
