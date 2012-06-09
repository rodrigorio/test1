<?php
class PracticaMySQLIntermediary extends PracticaIntermediary
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
    
    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){
        try{
            $db = clone ($this->conn);
            
            $sSQL = "SELECT SQL_CALC_FOUND_ROWS
                        id as iId,
                        nombre as sNombre
                     FROM 
                        practicas p ";

            if(!empty($filtro)){
                $filtro = $this->escapeStringArray($filtro);
                $sSQL .= " WHERE ".$this->crearCondicionSimple($filtro);
            }

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }
            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }

            $db->query($sSQL);
            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }
            
            $aPracticas = array();

            while($oObj = $db->oNextRecord()){
                $oPractica = new stdClass();
                $oPractica->iId = $oObj->iId;
                $oPractica->sNombre = $oObj->sNombre;
                $aPracticas[] = Factory::getPracticaInstance($oPractica);
            }
            
            return $aPracticas;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
    public function insertar($objects){}
    public function guardar($object){}
    public function borrar($oPractica) {}
    public function actualizar($oPractica){}
}