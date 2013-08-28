<?php

class EjeMySQLIntermediary extends EjeTematicoIntermediary
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

    /**
     * Devuelve un array principal con todos los ejes que no tienen padre y sus respectivas
     * sub listas si es que tiene sub ejes.
     *
     * soporta filtros por id, de la misma manera devuelve con sublista si es que el eje la posee
     */
    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = clone ($this->conn);

            $sSQL = "SELECT ope.id as iId, ope.descripcion as sDescripcion, opeHijo.id iSubEjeId, opeHijo.descripcion as sSubEjeDescripcion 
                     FROM objetivo_personalizado_ejes ope LEFT JOIN objetivo_personalizado_ejes opeHijo ON ope.id = opeHijo.ejePadre ";
            
            $WHERE = array();

            //si no hay filtro por id (devuelvo todos los ejes) asocio un filtro auxiliar para devolver solo los ejes padre con sus hijos
            if(isset($filtro['ope.id']) && $filtro['ope.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('ope.id', $filtro['ope.id'], MYSQL_TYPE_INT);
            }else{
                $WHERE[] = $this->crearFiltroSimple('ope.ejePadre', "0", MYSQL_TYPE_INT);
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            if (isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by ope.descripcion asc ";
            }

            if ($iIniLimit!==null && $iRecordCount!==null){
                $sSQL .= " limit  ".$db->escape($iIniLimit,false,MYSQL_TYPE_INT).",".$db->escape($iRecordCount,false,MYSQL_TYPE_INT) ;
            }
            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }
            
            $aEjes = array();
            $oEjeAnterior = null;
            $oEje = null;
            while($oObj = $db->oNextRecord()){

                if($oEjeAnterior === null || $oEjeAnterior->getId() != $oObj->iId){
                    $oEje = new stdClass();
                    $oEje->iId = $oObj->iId;
                    $oEje->sDescripcion = $oObj->sDescripcion;
                    $oEje = Factory::getEjeInstance($oEje);
                    $aEjes[] = $oEje; //esto se puede hacer porq se guarda solo apuntador
                    $oEjeAnterior = $oEje;
                }

                if(null !== $oObj->iSubEjeId){
                    $oSubEje = new stdClass();
                    $oSubEje->iId = $oObj->iSubEjeId;
                    $oSubEje->sDescripcion = $oObj->sSubEjeDescripcion;
                    $oSubEje = Factory::getEjeInstance($oSubEje);
                    $oEje->addSubEje($oSubEje); //se actualiza la instancia, el array apunta a este objeto tmb
                }                               
            }

            return $aEjes;
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    public function insertar($oEjeTematico){}    
    public function actualizar($oEjeTematico){}    
    public function guardar($oEjeTematico){}
    public function borrar($oEjeTematico){}	
    public function actualizarCampoArray($objects, $cambios){} 	
    public function existe($filtro){}
}