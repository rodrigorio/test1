<?php

class InformeMySQLIntermediary extends InformeIntermediary
{
    private static $instance = null;

    protected function __construct( $conn){
        parent::__construct($conn);
    }

    public static function &getInstance(IMYSQL $conn){
        if (null === self::$instance){
            self::$instance = new self($conn);
        }
        return self::$instance;
    }

    public final function obtenerConfiguracion($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone($this->conn);

            $sSQL = "SELECT
                        usuarios_id as iUsuarioId, titulo as sTitulo, subtitulo as sSubtitulo, pie as sPie
                     FROM
                        configuraciones_informes ci ";

            $WHERE = array();

            if(isset($filtro['ci.usuarios_id']) && $filtro['ci.usuarios_id']!=""){
                $WHERE[] = $this->crearFiltroSimple('ci.usuarios_id', $filtro['ci.usuarios_id'], MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }

            if($iIniLimit !== null && $iRecordCount !== null){
                $sSQL .= " limit ".$this->escInt($iIniLimit).", ".$this->escInt($iRecordCount);
            }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aInformeConfiguracion = array();
            while($oObj = $db->oNextRecord()){
                $oInformeConfiguracion = new stdClass();
                $oInformeConfiguracion->iUsuarioId = $oObj->iUsuarioId;

                $oInformeConfiguracion->sTitulo = $oObj->sTitulo;
                $oInformeConfiguracion->sSubtitulo = $oObj->sSubtitulo;
                $oInformeConfiguracion->sPie = $oObj->sPie;

                $oInformeConfiguracion = Factory::getInformeConfiguracionInstance($oInformeConfiguracion);
                $aInformeConfiguracion[] = $oInformeConfiguracion;
            }

            return $aInformeConfiguracion;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function guardarConfiguracionInforme($oInformeConfiguracion)
    {
        try{
            $db = $this->conn;
            $db->begin_transaction();

            $sSQL = " update configuraciones_informes set ".
                    " titulo = ".$this->escStr($oInformeConfiguracion->getTitulo()).", ".
                    " subtitulo = ".$this->escStr($oInformeConfiguracion->getSubtitulo()).", ".
                    " pie = ".$this->escStr($oInformeConfiguracion->getPie())." ".
                    " where usuarios_id = ".$this->escInt($oInformeConfiguracion->getUsuarioId())." ";

            $this->conn->execSQL($sSQL);

            $db->commit();
            return true;

        }catch(Exception $e){
            $db->rollback_transaction();
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null){}
    public function insertar($oObj){}
    public function actualizar($oObj){}
    public function guardar($oObj){}
    public function borrar($oObj){}
    public function actualizarCampoArray($oObjs, $cambios){}
    public function existe($filtro){}
}
