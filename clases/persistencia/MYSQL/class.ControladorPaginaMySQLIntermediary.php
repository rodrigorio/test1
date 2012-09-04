<?php

class ControladorPaginaMySQLIntermediary extends ControladorPaginaIntermediary
{    
    private static $instance = null;

    protected function __construct( $conn) {
            parent::__construct($conn);
    }

    /**
     * Singleton
     *
     * @param mixed $conn
     * @return ControladorPaginaMySQLIntermediary
     */
    public static function &getInstance(IMYSQL $conn) {
            if (null === self::$instance){
        self::$instance = new self($conn);
    }
    return self::$instance;
    }

    public function obtener($filtro,  &$iRecordsTotal, $sOrderBy = null, $sOrder = null, $iIniLimit = null, $iRecordCount = null)
    {
        try{
            $db = $this->conn;

            $sSQL = "SELECT
                        cp.id AS iId,
                        cp.controlador AS sKey
                    FROM
                        controladores_pagina cp ";

            if(!empty($filtro)){
                $sSQL .= "WHERE".$this->crearCondicionSimple($filtro);
            }

            if(isset($sOrderBy) && isset($sOrder)){
                $sSQL .= " order by $sOrderBy $sOrder ";
            }else{
                $sSQL .= " order by cp.controlador ";
            }

            $db->query($sSQL);

            $iRecordsTotal = (int) $db->getDBValue("select FOUND_ROWS() as list_count");

            if(empty($iRecordsTotal)){ return null; }

            $aControladoresPagina = array();
            while($oObj = $db->oNextRecord()){

                $oControladorPagina = new stdClass();
                $oControladorPagina->iId = $oObj->iId;
                $oControladorPagina->sKey = $oObj->sKey;

                $oControladorPagina = Factory::getControladorPaginaInstance($oControladorPagina);

                $aControladoresPagina[] = $oControladorPagina;
            }
            
            return $aControladoresPagina;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }                      
    }

    public function guardar($oControladorPagina){}
    public  function insertar($oControladorPagina){}
    public  function actualizar($oControladorPagina){}
    public function borrar($iControladorPaginaId){}
    public function existe($filtro){}
    public function actualizarCampoArray($objects, $cambios){}
}