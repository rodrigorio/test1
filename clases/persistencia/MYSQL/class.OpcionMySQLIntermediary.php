<?php

class OpcionMySQLIntermediary extends OpcionIntermediary
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

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        po.id as iId,
                        po.descripcion as sDescripcion,
                        po.orden as iOrden
                    FROM
                       pregunta_opciones po
                    WHERE
                       po.borradoLogico = 0 ";

            if(!empty($filtro)){
                $sSQL .= "AND ".$this->crearCondicionSimple($filtro);
            }

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by orden asc ";
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aOpciones = array();
            while($oObj = $db->oNextRecord()){
                $oOpcion = new stdClass();
                $oOpcion->iId = $oObj->iId;
                $oOpcion->sDescripcion = $oObj->sDescripcion;
                $oOpcion->iOrden = $oObj->iOrden;

                $aOpciones[] = Factory::getOpcionInstance($oOpcion);
            }

            return $aOpciones;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarOpcionesPreguntaMC(PreguntaMC $oPregunta)
    {
        try
        {
            if(null !== $oPregunta->getOpciones()){
                $db = $this->conn;
                $db->begin_transaction();
                foreach($oPregunta->getOpciones() as $oOpcion){
                    if(null !== $oOpcion->getId()){
                        $this->actualizar($oOpcion);
                    }else{
                        $iPreguntaId = $oPregunta->getId();
                        $this->insertarAsociado($oOpcion, $iPreguntaId);
                    }
                }
                $db->commit();
            }
            return true;
        }catch(Exception $e){
            echo $e->getMessage(); exit();
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Inserta una opcion asociada a la pregunta a la cual pertenece.
     *
     */
    public function insertarAsociado($oOpcion, $iPreguntaId)
    {
        try{
            $iVariableId = $this->escInt($iPreguntaId);

            $sSQL = " INSERT INTO pregunta_opciones SET ".
                     " preguntas_id = '".$iPreguntaId."', ".
                     " descripcion = ".$this->escStr($oOpcion->getDescripcion()).", ".
                     " orden = ".$this->escInt($oOpcion->getOrden())." ";

            $this->conn->execSQL($sSQL);
            $oOpcion->setId($this->conn->insert_id());

            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function actualizar($oOpcion)
    {
        try{
            $sSQL = " UPDATE pregunta_opciones SET ".
                    " descripcion = ".$this->escStr($oOpcion->getDescripcion()).", ".
                    " orden = ".$this->escInt($oOpcion->getOrden())." ".
                    " where id = ".$this->escInt($oOpcion->getId())." ";

            $this->conn->execSQL($sSQL);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borradoLogico($iOpcionId)
    {
        try{
            $sSQL = " UPDATE pregunta_opciones SET ".
                    " borradoLogico = 1 ".
                    " where id = ".$this->escInt($iOpcionId)." ";
            $db->execSQL($sSQL);
            $this->conn->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function borrar($iOpcionId)
    {
        try{
            $db = $this->conn;
            $db->execSQL("delete from pregunta_opciones where id = ".$this->escInt($iOpcionId));
            $db->commit();
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro)
    {
        try{
            $db = $this->conn;
            $filtro = $this->escapeStringArray($filtro);

            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                    FROM
                        pregunta_opciones po
                    WHERE ".$this->crearCondicionSimple($filtro);

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

    public function isOpcionPreguntaUsuario($iOpcionId, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        pregunta_opciones po
                        JOIN preguntas p ON po.preguntas_id = p.id
                        JOIN entrevistas e ON p.entrevistas_id = e.id
                      WHERE
                        op.id = ".$this->escInt($iOpcionId)." AND
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

    /**
     * Devuelve true si la opcion se selecciono como valor de una pregunta multiple choise
     * asociada a un seguimiento de un usuario.
     */
    public function isUtilizadaEnSeguimientoUsuario($iOpcionId, $iUsuarioId)
    {
        try{
            $db = $this->conn;

            $sSQL = " SELECT SQL_CALC_FOUND_ROWS
                        1 as existe
                      FROM
                        pregunta_x_opcion_x_seguimiento pos
                        JOIN seguimientos s ON pos.seguimientos_id = s.id
                      WHERE
                        pos.preguntas_opciones_id = ".$this->escInt($iOpcionId)." AND
                        s.usuarios_id = ".$this->escInt($iUsuarioId)." ";

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

    public function guardar($object){}
    public function insertar($objects){}
    public function actualizarCampoArray($objects, $cambios){}
}
