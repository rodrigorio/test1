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
	
	
    public function insertar($oCategoria)
    {

    }
    
    public  function actualizar($oCategoria)
    {

    }
    
    public function guardar($oCategoria)
    {

    }

    public final function obtener($filtro, &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{            
            $db = clone($this->conn);

            $sSQL = "SELECT DISTINCT 
                        scv.fechaHora as dFechaHora, s.id as iSeguimientoId,
                        IF(scc.id IS NULL, 'SeguimientoPersonalizado', 'SeguimientoSCC') as sObjType 
                     FROM
                        seguimientos s 
                     LEFT JOIN 
                        seguimientos_personalizados sp ON s.id = sp.id 
                     LEFT JOIN
                        seguimientos_scc scc ON s.id = scc.id 
                     JOIN
                        seguimiento_x_contenido_variables scv ON s.id = scv.seguimiento_id 
                     ";

            $WHERE = array();

            if(isset($filtro['s.id']) && $filtro['s.id']!=""){
                $WHERE[] = $this->crearFiltroSimple('s.id', $filtro['s.id'], MYSQL_TYPE_INT);
            }                        
            if(isset($filtro['fechas']) && null !== $filtro['fechas']){
                if(is_array($filtro['fechas'])){
                    $WHERE[] = $this->crearFiltroFechaDesdeHasta('scv.fechaHora', $filtro['fechas'], false);
                }
            }

            $sSQL = $this->agregarFiltrosConsulta($sSQL, $WHERE);

            //porque hay multiples entradas por cada N variable de cada N unidad. A mi solo me importa la entrada
            $sSQL .= " GROUP BY DATE(scv.fechaHora) ";

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by scv.fechaHora desc ";
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
                $oEntrada->dFechaHora = $oObj->dFechaHora;
                $oEntrada->iSeguimientoId = $oObj->iSeguimientoId;

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
            
    public function borrar($iCategoriaId)
    {

    }
	
    public function existe($filtro)
    {

    }
       
    public function actualizarCampoArray($objects, $cambios){}
}